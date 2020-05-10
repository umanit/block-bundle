<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\BlockBundle\Model\BlockTrait;

/**
 * @ORM\Table(name="umanit_block_block")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="block_type", type="string")
 */
abstract class Block implements BlockInterface
{
    use BlockTrait;
}
