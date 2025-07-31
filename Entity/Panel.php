<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Model\PanelInterface;
use Umanit\BlockBundle\Repository\PanelRepository;

#[ORM\Table(name: 'umanit_block_panel')]
#[ORM\Entity(repositoryClass: PanelRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Panel implements PanelInterface
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Block::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: 'umanit_block_panel_blocks')]
    #[ORM\JoinColumn(name: 'panel_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'block_id_id', referencedColumnName: 'id', unique: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    protected Collection $blocks;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    protected ?\DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    protected ?\DateTime $updatedAt;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function setBlocks(Collection $blocks = null): PanelInterface
    {
        $this->blocks = $blocks;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
