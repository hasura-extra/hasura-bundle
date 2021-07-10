<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use VXM\Hasura\Attribute\AsHasuraActionResolver;
use VXM\Hasura\Attribute\AsHasuraEventHandler;

final class HasuraExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('action.php');
        $loader->load('base.php');
        $loader->load('controller.php');
        $loader->load('event_listener.php');
        $loader->load('event_trigger.php');
        $loader->load('validation.php');

        $this->registerActionAttribute($container);
        $this->registerEventTriggerAttribute($container);
    }

    private function registerActionAttribute(ContainerBuilder $container)
    {
        $container->registerAttributeForAutoconfiguration(
            AsHasuraActionResolver::class,
            function (ChildDefinition $definition, AsHasuraActionResolver $attribute) use ($container) {
                $metadata = sprintf('vxm.hasura.action.metadata_%s', $attribute->actionName);
                $metadataRef = new ChildDefinition('vxm.hasura.action.metadata');
                $metadataRef->replaceArgument(0, $attribute->actionName);
                $metadataRef->replaceArgument(1, $attribute->inputClass);
                $metadataRef->replaceArgument(2, $attribute->denormalizeContext);
                $metadataRef->replaceArgument(3, $attribute->validate);
                $metadataRef->replaceArgument(4, $attribute->normalizeContext);
                $container->setDefinition($metadata, $metadataRef);

                $definition->addTag(
                    'vxm.hasura.action.resolver',
                    [
                        'actionName' => $attribute->actionName,
                        'metadata' => $metadata,
                    ]
                );
            }
        );
    }

    private function registerEventTriggerAttribute(ContainerBuilder $container)
    {
        $container->registerAttributeForAutoconfiguration(
            AsHasuraEventHandler::class,
            function (ChildDefinition $definition, AsHasuraEventHandler $attribute) use ($container) {
                $metadata = sprintf('vxm.hasura.event_trigger.metadata_%s', $attribute->triggerName);
                $metadataDef = new ChildDefinition('vxm.hasura.event_trigger.metadata');
                $metadataDef->replaceArgument(0, $attribute->triggerName);
                $container->setDefinition($metadata, $metadataDef);

                $definition->addTag(
                    'vxm.hasura.event_trigger.handler',
                    [
                        'triggerName' => $attribute->triggerName,
                        'metadata' => $metadata,
                    ]
                );
            }
        );
    }
}