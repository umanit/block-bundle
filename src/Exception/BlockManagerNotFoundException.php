<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Exception;

use Umanit\BlockBundle\Model\BlockInterface;

/**
 * Class BlockManagerNotFoundException
 */
class BlockManagerNotFoundException extends \Exception
{
    /**
     * BlockManagerNotFoundException constructor.
     *
     * @param BlockInterface $blockEntity
     */
    public function __construct(BlockInterface $blockEntity)
    {
        parent::__construct(sprintf(
            'A BlockManager could not be found for block of type "%s".',
            \get_class($blockEntity)
        ));
    }
}
