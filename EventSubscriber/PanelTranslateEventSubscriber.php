<?php

namespace Umanit\BlockBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Umanit\BlockBundle\Entity\Panel;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\TranslationBundle\Event\TranslateEvent;
use Umanit\TranslationBundle\Translator\EntityTranslator;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class PanelTranslateEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityTranslator
     */
    private $entityTranslator;

    /**
     * @inheritdoc
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [TranslateEvent::POST_TRANSLATE => 'postTranslate'];
    }

    /**
     * PanelTranslateEventSubscriber constructor.
     *
     * @param EntityManagerInterface $em
     * @param EntityTranslator       $entityTranslator
     */
    public function __construct(EntityManagerInterface $em, EntityTranslator $entityTranslator)
    {
        $this->em               = $em;
        $this->entityTranslator = $entityTranslator;
    }

    /**
     * Translates all blocks from source to translated Panel.
     *
     * @param TranslateEvent $event
     */
    public function postTranslate(TranslateEvent $event)
    {
        $source      = $event->getSourceEntity();
        $translation = $event->getTranslatedEntity();

        if ($source instanceof Panel && $translation instanceof Panel) {
            // Clone all blocks into new entity
            foreach ($source->getBlocks() as $sourceBlock) {
                /** @var BlockInterface $translatedBlock */
                // Temporary sets the panel as null to avoid infinite loop
                $sourceBlock->setPanel(null);
                $translatedBlock = $this->entityTranslator->getEntityTranslation($sourceBlock, $event->getLocale());
                $translatedBlock->setPanel($translation);
                // Re-set the panel of the source block
                $sourceBlock->setPanel($source);
                $this->em->persist($translatedBlock);
            }
            $this->em->flush();
        }
    }
}
