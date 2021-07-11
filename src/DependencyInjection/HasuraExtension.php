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
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use VXM\Hasura\Attribute\AsHasuraActionResolver;
use VXM\Hasura\Attribute\AsHasuraEventHandler;

final class HasuraExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig(
            'framework',
            [
                'serializer' => [
                    'enabled' => true,
                ],
                'property_info' => [
                    'enabled' => true,
                ],
            ]
        );
    }

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
            function (ChildDefinition $definition, AsHasuraActionResolver $attribute) {
                $definition->addTag(
                    'vxm.hasura.action.resolver',
                    [
                        'attribute' => \serialize($attribute),
                    ]
                );
            }
        );
    }

    private function registerEventTriggerAttribute(ContainerBuilder $container)
    {
        $container->registerAttributeForAutoconfiguration(
            AsHasuraEventHandler::class,
            function (ChildDefinition $definition, AsHasuraEventHandler $attribute) {
                $definition->addTag(
                    'vxm.hasura.event_trigger.handler',
                    [
                        'attribute' => \serialize($attribute),
                    ]
                );
            }
        );
    }
}