<?php

namespace Umanit\BlockBundle\EventSubscriber;

use \Doctrine\ORM;
use \Doctrine\Common;
use Umanit\BlockBundle\Entity\Panel;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;
use Doctrine\Common\Collections\Criteria;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class PanelEventSubscriber implements Common\EventSubscriber
{
    /**
     * @var BlockManagerResolver
     */
    private $blockManagerResolver;

    /**
     * PanelEventSubscriber constructor.
     *
     * @param BlockManagerResolver $blockManagerResolver
     */
    public function __construct(BlockManagerResolver $blockManagerResolver)
    {
        $this->blockManagerResolver = $blockManagerResolver;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            ORM\Events::postLoad,
            ORM\Events::preUpdate,
            ORM\Events::postUpdate,
            ORM\Events::postUpdate,
            ORM\Events::postPersist,
        ];
    }

    /**
     * PostLoad to inject the blocks into the panel.
     *
     * @param ORM\Event\LifecycleEventArgs $args
     */
    public function postLoad(ORM\Event\LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Panel) {
            return;
        }
        $panelBlockInstances = new Common\Collections\ArrayCollection();
        // Fetch all blocks for each type
        foreach ($this->blockManagerResolver->getAll() as $blockManager) {

            $repo           = $args->getObjectManager()->getRepository($blockManager->getManagedBlockType());
            $blockInstances = $repo->findBy(['panel' => $entity]);

            if (empty($blockInstances)) {
                continue;
            }
            foreach ($blockInstances as $blockInstance) {
                $panelBlockInstances->add($blockInstance);
            }
        }

        // Sort $blockInstances by position
        $panelBlockInstances = $panelBlockInstances
            ->matching(Criteria::create()->orderBy(['position' => Criteria::ASC]));

        // Inject $blockInstances into the panel
        $entity->setBlocks($panelBlockInstances);
    }

    /**
     * PreUpdate for orphan blocks removal.
     *
     * @param ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(ORM\Event\LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof BlockInterface) {
            $entity = $entity->getPanel();
        }

        if (!$entity instanceof Panel) {
            return;
        }

        // Removes the orphans
        foreach ($this->blockManagerResolver->getAll() as $blockManager) {
            $repo           = $args->getObjectManager()->getRepository($blockManager->getManagedBlockType());
            $blockInstances = $repo->findBy(['panel' => $entity]);

            if (empty($blockInstances)) {
                continue;
            }

            foreach ($blockInstances as $blockInstance) {
                // Removes the instance if it's not in the entity (it's been removed)
                if (null === $entity->getBlocks() || !$entity->getBlocks()->contains($blockInstance)) {
                    $args->getObjectManager()->remove($blockInstance);
                }
            }
        }
    }

    /**
     * PostUpdate for persisting the new blocks.
     *
     * @param ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(ORM\Event\LifecycleEventArgs $args)
    {
        $this->associateBlocksToPanel($args);
    }

    /**
     * PostPersist for persisting the new blocks.
     *
     * @param ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(ORM\Event\LifecycleEventArgs $args)
    {
        $this->associateBlocksToPanel($args);
    }

    /**
     * Persists the association Panel-Blocks on persist/update.
     *
     * @param ORM\Event\LifecycleEventArgs $args
     */
    private function associateBlocksToPanel(ORM\Event\LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Panel) {
            return;
        }

        if (null === $entity->getBlocks()) {
            return;
        }

        // Adds the new blocks
        foreach ($entity->getBlocks() as $block) {
            $block->setPanel($entity);
            $args->getObjectManager()->persist($block);
        }

        $args->getObjectManager()->flush();
    }
}
