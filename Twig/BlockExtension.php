<?php

namespace Umanit\BlockBundle\Twig;

use Psr\Log\LoggerInterface;
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
     * @var bool
     */
    private $debugIsEnabled;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BlockExtension constructor.
     *
     * @param BlockManagerResolver $blockManagerResolver
     * @param bool                 $debugIsEnabled
     * @param LoggerInterface      $logger
     */
    public function __construct(
        BlockManagerResolver $blockManagerResolver,
        LoggerInterface $logger,
        $debugIsEnabled = false
    ) {
        $this->blockManagerResolver = $blockManagerResolver;
        $this->logger               = $logger;
        $this->debugIsEnabled       = $debugIsEnabled;
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
     * @throws \Exception
     */
    public function renderBlock(BlockInterface $block)
    {
        $blockManager = $this->blockManagerResolver->resolveManager($block);
        $html         = '';

        try {
            $html = $blockManager->render($block);
        } catch (\Exception $e) {
            if (true === $this->debugIsEnabled) {
                throw $e;
            }
            // If debug is not enabled (prod), silent the exception and log
            $this->logger->critical($e->getMessage());
        }

        return $html;
    }
}
