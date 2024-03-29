<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\DependencyInjection\Compiler;

use Hasura\Handler\ActionHandlerInterface;
use Hasura\Handler\EventHandlerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $handlers = [];

        foreach ($container->findTaggedServiceIds('hasura.handler') as $id => $tags) {
            $handlerClass = $container->getDefinition($id)->getClass();

            foreach ($tags as ['attributes' => $attributes, 'type' => $type]) {
                $attributes = \unserialize($attributes);
                $name = $attributes['name'];

                if (isset($handlers[$type][$name])) {
                    throw new InvalidConfigurationException(sprintf('%s handler: `%s` duplicated', ucfirst($type), $name));
                }

                $handlerShouldImplement = 'action' === $type ? ActionHandlerInterface::class : EventHandlerInterface::class;

                if (!is_a($handlerClass, $handlerShouldImplement, true)) {
                    throw new InvalidConfigurationException(sprintf('%s handler `%s` should be implement `%s`, are you forget it?', ucfirst($type), $handlerClass, $handlerShouldImplement));
                }

                $handler = sprintf('hasura.handler.handler_%s_%s', $type, $name);
                $descriptor = sprintf('hasura.handler.descriptor_%s_%s', $type, $name);

                $descriptorDefinition = new ChildDefinition('hasura.handler.descriptor');
                $descriptorDefinition->replaceArgument(0, new Reference($handler));
                $descriptorDefinition->replaceArgument(1, $attributes);

                $container->setAlias($handler, $id);
                $container->setDefinition($descriptor, $descriptorDefinition);

                $handlers[$type][$name] = new Reference($descriptor);
            }
        }

        $container
            ->getDefinition('hasura.handler.locator')
            ->replaceArgument(0, $handlers['action'] ?? [])
            ->replaceArgument(1, $handlers['event'] ?? []);
    }
}
