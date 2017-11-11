<?php

namespace Umanit\BlockBundle\Exception;

use Umanit\BlockBundle\Model\BlockInterface;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class BlockManagerNotFoundException extends \Exception
{
    public function __construct(BlockInterface $blockEntity)
    {
        $this->message = sprintf("A BlockManager could not be found for block of type '%s'.", get_class($blockEntity));
    }
}
