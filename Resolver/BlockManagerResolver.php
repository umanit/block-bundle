<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Resolver;

use Umanit\BlockBundle\Block\AbstractBlockManager;
use Umanit\BlockBundle\Exception\BlockManagerNotFoundException;
use Umanit\BlockBundle\Model\BlockInterface;

/**
 * Class BlockManagerResolver
 *
 * Used to resolve which BlockManager is associated to which Block entity.
 */
class BlockManagerResolver
{
    /** @var AbstractBlockManager[] */
    protected array $blockManagers = [];

    public function __construct(iterable $blockManagers)
    {
        foreach ($blockManagers as $blockManager) {
            $this->addBlockManager($blockManager);
        }
    }

    /**
     * Resolves and return a BlockManager for a given BlockInterface.
     *
     * @param BlockInterface $blockEntity
     *
     * @return AbstractBlockManager
     * @throws BlockManagerNotFoundException
     */
    public function resolveManager(BlockInterface $blockEntity): AbstractBlockManager
    {
        if (isset($this->blockManagers[\get_class($blockEntity)])) {
            return $this->blockManagers[\get_class($blockEntity)];
        }

        throw new BlockManagerNotFoundException($blockEntity);
    }

    /**
     * Returns all block managers.
     *
     * @return AbstractBlockManager[]
     */
    public function getAll(): array
    {
        return $this->blockManagers;
    }

    private function addBlockManager(AbstractBlockManager $blockManager): void
    {
        $this->blockManagers[$blockManager->getManagedBlockType()] = $blockManager;
    }
}
