<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Twig;

use Psr\Log\LoggerInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Umanit\BlockBundle\Exception\BlockManagerNotFoundException;
use Umanit\BlockBundle\Model\BlockInterface;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;

class BlockRuntime implements RuntimeExtensionInterface
{
    /** @var BlockManagerResolver */
    private $blockManagerResolver;

    /** @var bool */
    private $debugIsEnabled;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        BlockManagerResolver $blockManagerResolver,
        LoggerInterface $logger,
        $debugIsEnabled = false
    ) {
        $this->blockManagerResolver = $blockManagerResolver;
        $this->logger = $logger;
        $this->debugIsEnabled = $debugIsEnabled;
    }

    /**
     * Renders a block.
     *
     * @param BlockInterface $block
     * @param array          $parameters
     *
     * @return string
     * @throws BlockManagerNotFoundException
     */
    public function renderBlock(BlockInterface $block, array $parameters = []): string
    {
        $blockManager = $this->blockManagerResolver->resolveManager($block);
        $html = '';

        try {
            $html = $blockManager->render($block, $parameters);
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
