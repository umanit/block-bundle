<?php

namespace Umanit\BlockBundle\Resolver;

use Umanit\BlockBundle\Block\AbstractBlockManager;
use Umanit\BlockBundle\Exception\BlockManagerNotFoundException;
use Umanit\BlockBundle\Model\BlockInterface;

/**
 * Used to resolve which BlockManager is associated to which Block entity.
 *
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class BlockManagerResolver
{
    /**
     * @var AbstractBlockManager[]
     */
    protected $blockManagers = [];

    /**
     * @param AbstractBlockManager $blockManager
     */
    public function addBlockManager(AbstractBlockManager $blockManager)
    {
        $this->blockManagers[$blockManager->getManagedBlockType()] = $blockManager;
    }

    /**
     * Resolves and return a BlockManager for a given BlockInterface.
     *
     * @param BlockInterface $blockEntity
     *
     * @return AbstractBlockManager
     * @throws BlockManagerNotFoundException
     */
    public function resolveManager(BlockInterface $blockEntity)
    {
        if (isset($this->blockManagers[get_class($blockEntity)])) {
            return $this->blockManagers[get_class($blockEntity)];
        }

        throw new BlockManagerNotFoundException($blockEntity);
    }

    /**
     * Returns all block managers.
     *
     * @return AbstractBlockManager[]
     */
    public function getAll()
    {
        return $this->blockManagers;
    }
}
