<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Block;

use Umanit\BlockBundle\Model\BlockInterface;

abstract class AbstractBlockManager
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
     * @param array          $parameters
     *
     * @return string
     */
    abstract public function render(BlockInterface $block, array $parameters = []): string;

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
