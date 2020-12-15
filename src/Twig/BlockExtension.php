<?php

declare(strict_types=1);

namespace Umanit\BlockBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class BlockExtension
 */
class BlockExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('umanit_block_render', [BlockRuntime::class, 'renderBlock'], ['is_safe' => ['html']]),
        ];
    }
}
