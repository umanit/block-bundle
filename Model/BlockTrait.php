<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Entity\Panel;

trait BlockTrait
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var PanelInterface
     *
     * @ORM\ManyToOne(targetEntity="Umanit\BlockBundle\Entity\Panel")
     * @ORM\JoinColumn(name="panel_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $panel;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

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
