<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use VXM\Hasura\HasuraBundle;
use VXM\Hasura\Tests\Fixture\ActionHandler;
use VXM\Hasura\Tests\Fixture\ActionHandlerWithoutInputClass;
use VXM\Hasura\Tests\Fixture\EventHandler;

final class TestKernel extends Kernel implements CompilerPassInterface
{
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new MakerBundle(),
            new HasuraBundle()
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

                $this->registerHandlerFixtures($container);
            }
        );
    }

    private function registerHandlerFixtures(ContainerBuilder $container): void
    {
        $fixtures = [
            ActionHandler::class,
            ActionHandlerWithoutInputClass::class,
            EventHandler::class
        ];

        foreach ($fixtures as $fixture) {
            $container
                ->register($fixture, $fixture)
                ->setAutoconfigured(true);
        }
    }

    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('vxm.hasura.handler.locator')->setPublic(true);
        $container->getDefinition('vxm.hasura.handler.descriptor')->setPublic(true);
        $container->getDefinition('vxm.hasura.event_listener.resolve_request')->setPublic(true);
        $container->getDefinition('vxm.hasura.event_listener.action_input')->setPublic(true);
        $container->getDefinition('vxm.hasura.event_listener.handler')->setPublic(true);
        $container->getDefinition('vxm.hasura.event_listener.action_output')->setPublic(true);
        $container->getDefinition('vxm.hasura.event_listener.respond')->setPublic(true);
        $container->getDefinition('vxm.hasura.event_listener.exception')->setPublic(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return sprintf('%s/tests/.kernel/cache', $this->getProjectDir());
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return sprintf('%s/tests/.kernel/logs', $this->getProjectDir());
    }

    public function getTempDir()
    {
        return sprintf('%s/tests/.kernel/temp', $this->getProjectDir());
    }
}