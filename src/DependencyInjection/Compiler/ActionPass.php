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
use VXM\Hasura\Attribute\AsHasuraActionResolver;

final class ActionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $actions = [];

        foreach ($container->findTaggedServiceIds('vxm.hasura.action.resolver') as $id => $tags) {
            $resolverClass = $container->getDefinition($id)->getClass();

            if (!is_a($resolverClass, ResolverInterface::class, true)) {
                throw new InvalidConfigurationException(
                    sprintf(
                        'Action resolver `%s` should be implement `%s`, are you forget it?',
                        $resolverClass,
                        ResolverInterface::class
                    )
                );
            }

            foreach ($tags as ['attribute' => $attribute]) {
                $attribute = \unserialize($attribute);
                /** @var AsHasuraActionResolver $attribute */
                $resolver = sprintf('vxm.hasura.action.resolver_%s', $attribute->actionName);
                $action = sprintf('vxm.hasura.action.action_%s', $attribute->actionName);
                $metadata = sprintf('vxm.hasura.action.metadata_%s', $attribute->actionName);

                $metadataDef = new ChildDefinition('vxm.hasura.action.metadata');
                $metadataDef->replaceArgument(0, $attribute->actionName);
                $metadataDef->replaceArgument(1, $attribute->inputClass);
                $metadataDef->replaceArgument(2, $attribute->denormalizeContext);
                $metadataDef->replaceArgument(3, $attribute->validate);
                $metadataDef->replaceArgument(4, $attribute->normalizeContext);

                $actionDef = new ChildDefinition('vxm.hasura.action.action');
                $actionDef->replaceArgument(0, new Reference($metadata));
                $actionDef->replaceArgument(1, new Reference($resolver));

                $container->setAlias($resolver, $id);
                $container->setDefinition($metadata, $metadataDef);
                $container->setDefinition($action, $actionDef);

                $actions[$attribute->actionName] = new Reference($action);
            }
        }

        $container
            ->getDefinition('vxm.hasura.action.manager')
            ->replaceArgument(0, $actions);
    }
}