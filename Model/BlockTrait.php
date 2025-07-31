<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Entity\Panel;

trait BlockTrait
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Panel::class)]
    #[ORM\JoinColumn(name: 'panel_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected ?PanelInterface $panel;

    #[ORM\Column(name: 'position', type: 'integer')]
    protected ?int $position;

    public function getPanel(): PanelInterface
    {
        return $this->panel;
    }

    public function setPanel(PanelInterface $panel = null): void
    {
        $this->panel = $panel;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
