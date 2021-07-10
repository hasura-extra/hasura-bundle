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

        foreach ($container->findTaggedServiceIds('vxm.hasura.event_handler') as $id => $tags) {
            if (!is_a($container->getDefinition($id)->getClass(), HandlerInterface::class, true)) {
                throw new InvalidConfigurationException(
                    sprintf('Action resolver should be implement `%s`', HandlerInterface::class)
                );
            }

            foreach ($tags as $attributes) {
                $triggerName = $attributes['triggerName'];
                $eventTrigger = sprintf('vxm.hasura.event_trigger_%s', $triggerName);
                $metadata = sprintf('vxm.hasura.event_trigger_metadata_%s', $triggerName);
                $handler = sprintf('vxm.hasura.event_handler_%s', $triggerName);

                $metadataDef = new ChildDefinition('vxm.hasura.event_trigger_metadata');
                $metadataDef->replaceArgument(0, $triggerName);

                $eventTriggerDef = new ChildDefinition('vxm.hasura.event_trigger');
                $eventTriggerDef->replaceArgument(0, new Reference($metadata));
                $eventTriggerDef->replaceArgument(1, new Reference($handler));

                $container->setDefinition($metadata, $metadataDef);
                $container->setAlias($handler, $id);
                $container->setDefinition($eventTrigger, $eventTriggerDef);

                $eventTriggers[$triggerName] = new Reference($eventTrigger);
            }
        }

        $container
            ->getDefinition('vxm.hasura.event_trigger_manager')
            ->replaceArgument(0, $eventTriggers);
    }
}