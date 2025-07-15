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
    public function __construct(
        private readonly BlockManagerResolver $blockManagerResolver,
        private readonly LoggerInterface $logger,
        private readonly bool $debugIsEnabled = false
    ) {
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
