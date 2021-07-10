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
use VXM\Hasura\EventTrigger\HandlerInterface;

final class EventTriggerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $eventTriggers = [];

        foreach ($container->findTaggedServiceIds('vxm.hasura.event_trigger.handler') as $id => $tags) {
            if (!is_a($container->getDefinition($id)->getClass(), HandlerInterface::class, true)) {
                throw new InvalidConfigurationException(
                    sprintf('Action resolver should be implement `%s`', HandlerInterface::class)
                );
            }

            foreach ($tags as $attributes) {
                $triggerName = $attributes['triggerName'];
                $trigger = sprintf('vxm.hasura.event_trigger.trigger_%s', $triggerName);
                $metadata = sprintf('vxm.hasura.event_trigger.metadata_%s', $triggerName);
                $handler = sprintf('vxm.hasura.event_trigger.handler_%s', $triggerName);

                $metadataDef = new ChildDefinition('vxm.hasura.event_trigger.metadata');
                $metadataDef->replaceArgument(0, $triggerName);

                $triggerDef = new ChildDefinition('vxm.hasura.event_trigger.trigger');
                $triggerDef->replaceArgument(0, new Reference($metadata));
                $triggerDef->replaceArgument(1, new Reference($handler));

                $container->setDefinition($metadata, $metadataDef);
                $container->setAlias($handler, $id);
                $container->setDefinition($trigger, $triggerDef);

                $eventTriggers[$triggerName] = new Reference($trigger);
            }
        }

        $container
            ->getDefinition('vxm.hasura.event_trigger.manager')
            ->replaceArgument(0, $eventTriggers);
    }
}