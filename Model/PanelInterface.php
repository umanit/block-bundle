<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface PanelInterface
{
    public function preUpdate(): void;

    public function getId(): ?int;

    public function getBlocks(): Collection;

    public function setBlocks(ArrayCollection $blocks = null): PanelInterface;

    public function getCreatedAt(): \DateTime;

    public function getUpdatedAt(): \DateTime;
}
