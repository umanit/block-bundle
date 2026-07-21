<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Twig;

use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;

class BlockExtension extends AbstractExtension
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
        bool $debugIsEnabled = false
    ) {
        $this->blockManagerResolver = $blockManagerResolver;
        $this->logger = $logger;
        $this->debugIsEnabled = $debugIsEnabled;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('umanit_block_render', [BlockRuntime::class, 'renderBlock'], ['is_safe' => ['html']]),
        ];
    }
}
