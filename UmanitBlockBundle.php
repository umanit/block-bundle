<?php

namespace Umanit\BlockBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Umanit\BlockBundle\DependencyInjection\Compiler\BlockManagerPass;

/**
 * Class UmanitBlockBundle
 */
class UmanitBlockBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BlockManagerPass());
    }
}
