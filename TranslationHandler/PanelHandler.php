<?php

namespace Umanit\BlockBundle\TranslationHandler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Umanit\BlockBundle\Entity\Panel;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\TranslationBundle\Translation\Args\TranslationArgs;
use Umanit\TranslationBundle\Translation\EntityTranslator;
use Umanit\TranslationBundle\Translation\Handlers\TranslationHandlerInterface;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class PanelHandler implements TranslationHandlerInterface
{
    /**
     * @var EntityTranslator
     */
    private $translator;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PanelHandler constructor.
     *
     * @param EntityTranslator       $translator
     * @param PropertyAccessor       $accessor
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityTranslator $translator, PropertyAccessor $accessor, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->accessor   = $accessor;
        $this->em         = $em;
    }

    public function supports(TranslationArgs $args): bool
    {
        return $args->getDataToBeTranslated() instanceof Panel;
    }

    public function handleSharedAmongstTranslations(TranslationArgs $args)
    {
        // @fixme: should return something else.
        return new Panel();
    }

    public function handleEmptyOnTranslate(TranslationArgs $args)
    {
        return new Panel();
    }

    public function translate(TranslationArgs $args)
    {
        /** @var Panel $source */
        $source      = $args->getDataToBeTranslated();
        $translation = clone $source;

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
            $this->em->persist($translatedBlock);
        }

        return $translation;
    }

}
