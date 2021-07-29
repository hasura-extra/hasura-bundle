<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests;

use Hasura\HasuraBundle;
use Hasura\Tests\Fixture\ActionHandler;
use Hasura\Tests\Fixture\ActionHandlerWithoutInputClass;
use Hasura\Tests\Fixture\EventHandler;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel implements CompilerPassInterface
{
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new MakerBundle(),
            new HasuraBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(
            function (ContainerBuilder $container) {
                $container->loadFromExtension(
                    'framework',
                    [
                        'secret' => '',
                        'test' => true,
                        'router' => [
                            'resource' => __DIR__ . '/../src/Resources/config/routing/routes.php',
                            'type' => 'php',
                            'utf8' => true,
                        ],
                    ]
                );

                $container->loadFromExtension(
                    'hasura',
                    [
                        'base_uri' => 'http://localhost:8080',
                        'admin_secret' => 'test',
                    ]
                );

                $this->registerHandlerFixtures($container);
            }
        );
    }

    private function registerHandlerFixtures(ContainerBuilder $container): void
    {
        $fixtures = [
            ActionHandler::class,
            ActionHandlerWithoutInputClass::class,
            EventHandler::class,
        ];

        foreach ($fixtures as $fixture) {
            $container
                ->register($fixture, $fixture)
                ->setAutoconfigured(true);
        }
    }

    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('hasura.api_client.client')->setPublic(true);
        $container->getDefinition('hasura.command.apply_metadata')->setPublic(true);
        $container->getDefinition('hasura.command.export_metadata')->setPublic(true);
        $container->getDefinition('hasura.service.metadata.manager')->setPublic(true);
        $container->getDefinition('hasura.handler.descriptor')->setPublic(true);
        $container->getDefinition('hasura.event_listener.resolve_request')->setPublic(true);
        $container->getDefinition('hasura.event_listener.action_input')->setPublic(true);
        $container->getDefinition('hasura.event_listener.handler')->setPublic(true);
        $container->getDefinition('hasura.event_listener.action_output')->setPublic(true);
        $container->getDefinition('hasura.event_listener.respond')->setPublic(true);
        $container->getDefinition('hasura.event_listener.exception')->setPublic(true);
    }

    public function getProjectDir()
    {
        return sprintf('%s/.kernel', __DIR__);
    }

    public function getTempDir()
    {
        return sprintf('%s/temp', $this->getProjectDir());
    }
}
