<?php

namespace Umanit\BlockBundle\Model;

use Umanit\BlockBundle\Entity\Panel;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
interface BlockInterface
{
    /**
     * @return Panel|null
     */
    public function getPanel();

    /**
     * @param Panel|null $panel
     *
     * @return BlockInterface
     */
    public function setPanel(Panel $panel = null);

    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @param int $position
     *
     * @return BlockInterface
     */
    public function setPosition($position);

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string
     */
    public function __toString();
}
