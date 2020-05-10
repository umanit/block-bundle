<?php

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
        $this->message = sprintf(
            'A BlockManager could not be found for block of type "%s".',
            \get_class($blockEntity)
        );
    }
}
