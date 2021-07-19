<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\DependencyInjection;

use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use VXM\Hasura\Attribute\AsActionHandler;
use VXM\Hasura\Attribute\AsEventHandler;

final class HasuraExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('base.php');
        $loader->load('handler.php');
        $loader->load('controller.php');
        $loader->load('event_listener.php');
        $loader->load('validation.php');

        if (class_exists(MakerBundle::class)) {
            $loader->load('maker.php');
        }

        $this->registerAttributesForAutoconfiguration($container);
    }

    private function registerAttributesForAutoconfiguration(ContainerBuilder $container)
    {
        $callable = static function (ChildDefinition $definition, AsActionHandler | AsEventHandler $attribute): void {
            $definition->addTag(
                'vxm.hasura.handler',
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
