<?php

namespace Umanit\BlockBundle\Twig;

use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var BlockManagerResolver
     */
    private $blockManagerResolver;

    /**
     * BlockExtension constructor.
     *
     * @param BlockManagerResolver $blockManagerResolver
     */
    public function __construct(BlockManagerResolver $blockManagerResolver)
    {
        $this->blockManagerResolver = $blockManagerResolver;
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('umanit_block_render', [$this, 'renderBlock'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Renders a block.
     *
     * @param BlockInterface $block
     *
     * @return mixed
     */
    public function renderBlock(BlockInterface $block)
    {
        $blockManager = $this->blockManagerResolver->resolveManager($block);

        return $blockManager->render($block);
    }
}
