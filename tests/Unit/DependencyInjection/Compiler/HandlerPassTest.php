<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use VXM\Hasura\DependencyInjection\Compiler\HandlerPass;
use VXM\Hasura\DependencyInjection\HasuraExtension;
use VXM\Hasura\Tests\Fixture\ActionHandler;
use VXM\Hasura\Tests\Fixture\EventHandler;
use VXM\Hasura\Tests\Fixture\InvalidActionHandler;
use VXM\Hasura\Tests\Fixture\InvalidEventHandler;

class HandlerPassTest extends TestCase
{
    /**
     * @dataProvider definitions
     */
    public function testProcess(Definition $definition, string $type)
    {
        $pass = new HandlerPass();
        $container = new ContainerBuilder();
        $container->setDefinition('test', $definition);
        $definition->addTag('vxm.hasura.handler', ['attributes' => \serialize(['name' => 'test']), 'type' => $type]);

        $extension = new HasuraExtension();
        $extension->load([], $container);
        $pass->process($container);

        $handler = sprintf('vxm.hasura.handler.handler_%s_test', $type);
        $this->assertTrue($container->hasAlias($handler));
    }

    /**
     * @dataProvider invalidDefinitions
     */
    public function testProcessWithInvalidHandler(Definition $definition, string $type): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $pass = new HandlerPass();
        $container = new ContainerBuilder();
        $container->setDefinition('test', $definition);
        $definition->addTag('vxm.hasura.handler', ['attributes' => \serialize(['name' => 'test']), 'type' => $type]);

        $extension = new HasuraExtension();
        $extension->load([], $container);
        $pass->process($container);
    }

    public function definitions(): array
    {
        return [
            [
                new Definition(ActionHandler::class), 'action',
            ],
            [
                new Definition(EventHandler::class), 'event',
            ],
        ];
    }

    public function invalidDefinitions(): array
    {
        return [
            [
                new Definition(InvalidActionHandler::class), 'action',
            ],
            [
                new Definition(InvalidEventHandler::class), 'event',
            ],
        ];
    }
}
