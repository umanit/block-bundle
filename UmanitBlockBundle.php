<?php

namespace Umanit\BlockBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Umanit\BlockBundle\DependencyInjection\Compiler\BlockManagerPass;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class UmanitBlockBundle extends Bundle
{
    /**
     * @inheritdoc
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BlockManagerPass());
    }
}
