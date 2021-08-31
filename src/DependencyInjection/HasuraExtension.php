<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\DependencyInjection;

use Hasura\Attribute\AsActionHandler;
use Hasura\Attribute\AsEventHandler;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class HasuraExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('hasura.base_uri', $config['base_uri']);
        $container->setParameter('hasura.metadata_path', $config['metadata_path']);

        $this->loadServices($container, $config);
        $this->registerAttributesForAutoconfiguration($container);
    }

    private function loadServices(ContainerBuilder $container, array $config): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('api_client.php');
        $loader->load('base.php');
        $loader->load('command.php');
        $loader->load('handler.php');
        $loader->load('controller.php');
        $loader->load('event_listener.php');
        $loader->load('service.php');
        $loader->load('validation.php');

        if (class_exists(MakerBundle::class)) {
            $loader->load('maker.php');
        }

        $container->getDefinition('hasura.api_client.client')->setArgument(1, $config['admin_secret']);
    }

    private function registerAttributesForAutoconfiguration(ContainerBuilder $container): void
    {
        $callable = static function (ChildDefinition $definition, AsActionHandler|AsEventHandler $attribute): void {
            $definition->addTag(
                'hasura.handler',
                [
                    // Tag attribute not allow array so we need to serialize attribute.
                    // When this PR https://github.com/symfony/symfony/pull/38540 merge, remove it.
                    'attributes' => \serialize((array) $attribute),
                    'type' => $attribute instanceof AsActionHandler ? 'action' : 'event',
                ]
            );
        };

        $container->registerAttributeForAutoconfiguration(AsActionHandler::class, $callable);
        $container->registerAttributeForAutoconfiguration(AsEventHandler::class, $callable);
    }
}
