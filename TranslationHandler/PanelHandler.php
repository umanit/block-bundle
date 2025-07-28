<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\TranslationHandler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Umanit\BlockBundle\Entity\Panel;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\TranslationBundle\Translation\Args\TranslationArgs;
use Umanit\TranslationBundle\Translation\EntityTranslator;
use Umanit\TranslationBundle\Translation\Handlers\TranslationHandlerInterface;

class PanelHandler implements TranslationHandlerInterface
{
    public function __construct(
        private readonly EntityTranslator $translator,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function supports(TranslationArgs $args): bool
    {
        return $args->getDataToBeTranslated() instanceof Panel;
    }

    public function handleSharedAmongstTranslations(TranslationArgs $args): Panel
    {
        // @fixme: should return something else.
        return new Panel();
    }

    public function handleEmptyOnTranslate(TranslationArgs $args): Panel
    {
        return new Panel();
    }

    public function translate(TranslationArgs $args): Panel
    {
        /** @var Panel $source */
        $source = $args->getDataToBeTranslated();
        $translation = clone $source;
        $newBlocks = new ArrayCollection();

        // Clone all blocks into new entity
        foreach ($source->getBlocks() as $sourceBlock) {
            /** @var BlockInterface $translatedBlock */
            // Temporary sets the panel as null to avoid infinite loop
            $sourceBlock->setPanel(null);

            $subTranslationArgs =
                (new TranslationArgs($sourceBlock, $args->getSourceLocale(), $args->getTargetLocale()))
                    ->setTranslatedParent($translation);

            $translatedBlock = $this->translator->processTranslation($subTranslationArgs);

            $translatedBlock->setPanel($translation);
            // Re-set the panel of the source block
            $sourceBlock->setPanel($source);
            $newBlocks->add($translatedBlock);
            $this->em->persist($translatedBlock);
        }

        $translation->setBlocks($newBlocks);

        return $translation;
    }
}
