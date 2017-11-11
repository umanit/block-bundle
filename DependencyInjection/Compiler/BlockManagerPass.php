<?php

namespace Umanit\BlockBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Umanit\BlockBundle\Resolver\BlockManagerResolver;

/**
 * @author Arthur Guigand <aguigand@umanit.fr>
 */
class BlockManagerPass implements CompilerPassInterface
{
    /**
     * Adds every BlockManagers to the BlockManagerResolver.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('umanit_block.resolver.block_manager_resolver')) {
            return;
        }

        $definition = $container->findDefinition('umanit_block.resolver.block_manager_resolver');

        $taggedServices = $container->findTaggedServiceIds('umanit_block.manager');

        foreach ($taggedServices as $id => $tags) {
            // Inject Template Engine
            $managerDefinition = $container->findDefinition($id);
            $managerDefinition->addMethodCall('setEngine', [new Reference('templating')]);

            $definition->addMethodCall('addBlockManager', [new Reference($id)]);
        }
    }
}
