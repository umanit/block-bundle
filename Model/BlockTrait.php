<?php

namespace Umanit\BlockBundle\Model;

use Umanit\BlockBundle\Entity\Panel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
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
     * @var Panel
     *
     * @ORM\ManyToOne(targetEntity="Umanit\BlockBundle\Entity\Panel")
     * @ORM\JoinColumn(name="panel_id", referencedColumnName="id")
     */
    protected $panel;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * @return Panel
     */
    public function getPanel()
    {
        return $this->panel;
    }

    /**
     * @param Panel|null $panel
     *
     * @return $this
     */
    public function setPanel(Panel $panel = null)
    {
        $this->panel = $panel;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Set id
     *
     * @param int|null $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

}
