<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Umanit\BlockBundle\Model\PanelInterface;

/**
 * Panel
 *
 * @ORM\Table(name="umanit_block_panel")
 * @ORM\Entity(repositoryClass="Umanit\BlockBundle\Repository\PanelRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Panel implements PanelInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection|null
     *
     * @ORM\ManyToMany(targetEntity="Umanit\BlockBundle\Entity\Block", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="umanit_block_panel_blocks",
     *      joinColumns={@ORM\JoinColumn(name="panel_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="block_id_id", referencedColumnName="id", unique=true)}
     * )
     * @ORM\OrderBy({"position": "ASC"})
     */
    protected $blocks;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    protected $updatedAt;

    /**
     * Panel constructor.
     */
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

    public function setBlocks(ArrayCollection $blocks = null): Panel
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
