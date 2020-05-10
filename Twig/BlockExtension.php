<?php

namespace Umanit\BlockBundle\Twig;

use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;

/**
 * Class BlockExtension
 */
class BlockExtension extends AbstractExtension
{
    /** @var BlockManagerResolver */
    private $blockManagerResolver;

    /** @var bool */
    private $debugIsEnabled;

    /** @var LoggerInterface */
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
        $this->logger = $logger;
        $this->debugIsEnabled = $debugIsEnabled;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('umanit_block_render', [$this, 'renderBlock'], ['is_safe' => ['html']]),
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
        $html = '';

        try {
            $html = $blockManager->render($block);
        } catch (\Exception $e) {
            if ($this->debugIsEnabled) {
                throw $e;
            }

            // If debug is not enabled (prod), silent the exception and log
            $this->logger->critical($e->getMessage());
        }

        return $html;
    }
}
