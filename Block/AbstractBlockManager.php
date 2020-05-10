<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Block;

use Symfony\Component\Form\AbstractType;
use Umanit\BlockBundle\Model\BlockInterface;

/**
 * Class AbstractBlockManager
 */
abstract class AbstractBlockManager extends AbstractType
{
    /**
     * This method must return the block entity managed by this block manager.
     *
     * @return string
     */
    abstract public function getManagedBlockType(): string;

    /**
     * This method must return the form typemanaged by this block manager.
     *
     * @return string
     */
    abstract public function getManagedFormType(): string;

    /**
     * This method will be called to render a block entity.
     *
     * @param BlockInterface $block
     *
     * @return string
     */
    abstract public function render(BlockInterface $block): string;

    /**
     * Returns the name to use in the Panel form.
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getPublicName(): string
    {
        $elements = preg_split(
            "/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/",
            (new \ReflectionClass($this->getManagedBlockType()))->getShortName()
        );

        return trim(implode(' ', $elements));
    }
}
