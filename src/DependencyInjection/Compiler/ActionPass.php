<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use VXM\Hasura\Action\ResolverInterface;

final class ActionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $actions = [];

        foreach ($container->findTaggedServiceIds('vxm.hasura.action.resolver') as $id => $tags) {
            if (!is_a($container->getDefinition($id)->getClass(), ResolverInterface::class, true)) {
                throw new InvalidConfigurationException(
                    sprintf('Action resolver should be implement `%s`', ResolverInterface::class)
                );
            }

            foreach ($tags as $attributes) {
                $actionName = $attributes['actionName'];
                $action = sprintf('vxm.hasura.action.action_%s', $actionName);
                $metadata = sprintf('vxm.hasura.action.metadata_%s', $actionName);
                $resolver = sprintf('vxm.hasura.action.resolver_%s', $actionName);

                $metadataDef = new ChildDefinition('vxm.hasura.action.metadata');
                $metadataDef->replaceArgument(0, $actionName);
                $metadataDef->replaceArgument(1, $attributes['inputClass']);
                $metadataDef->replaceArgument(2, $attributes['denormalizeContext']);
                $metadataDef->replaceArgument(3, $attributes['validate']);
                $metadataDef->replaceArgument(4, $attributes['normalizeContext']);

                $actionDef = new ChildDefinition('vxm.hasura.action.action');
                $actionDef->replaceArgument(0, new Reference($metadata));
                $actionDef->replaceArgument(1, new Reference($resolver));

                $container->setDefinition($metadata, $metadataDef);
                $container->setAlias($resolver, $id);
                $container->setDefinition($action, $actionDef);

                $actions[$actionName] = new Reference($action);
            }
        }

        $container
            ->getDefinition('vxm.hasura.action.manager')
            ->replaceArgument(0, $actions);
    }
}