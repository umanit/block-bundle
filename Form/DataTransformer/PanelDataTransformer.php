<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Umanit\BlockBundle\Entity\Panel;

/**
 * Class PanelDataTransformer
 */
class PanelDataTransformer implements DataTransformerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * PanelDataTransformer constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public function transform($value): ?array
    {
        if (!$value instanceof Panel) {
            return null;
        }

        $rawData = [
            'id'     => $value->getId(),
            'blocks' => [],
        ];

        foreach ($value->getBlocks() as $block) {
            $blockName = (new \ReflectionClass($block))->getShortName();
            $rawData['blocks'][$blockName][] = $block;
        }

        return $rawData;
    }

    public function reverseTransform($value)
    {
        if (empty($value['id'])) {
            $panel = new Panel();
        } else {
            $panel = $this->em->getRepository(Panel::class)->find($value['id']);
        }

        $blocks = new ArrayCollection();

        foreach ($value['blocks'] as $blockType) {
            foreach ($blockType as $block) {
                $block->setPanel($panel);
                $blocks->add($block);
            }
        }

        // Sort $blockInstances by position
        $blocks = $blocks->matching(Criteria::create()->orderBy(['position' => Criteria::ASC]));

        $panel->setBlocks($blocks);

        return $panel;
    }
}
