<?php

namespace Umanit\BlockBundle\Model;

use Umanit\BlockBundle\Entity\Panel;

/**
 * Interface BlockInterface
 */
interface BlockInterface
{
    public function getId(): ?int;

    public function getPanel(): ?Panel;

    public function setPanel(Panel $panel = null): void;

    public function getPosition(): ?int;

    public function setPosition(int $position): void;

    public function __toString();
}
