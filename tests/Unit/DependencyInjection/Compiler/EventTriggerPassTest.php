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
use VXM\Hasura\Attribute\AsHasuraEventHandler;
use VXM\Hasura\DependencyInjection\Compiler\EventTriggerPass;
use VXM\Hasura\DependencyInjection\HasuraExtension;
use VXM\Hasura\Tests\Fixture\Handler;
use VXM\Hasura\Tests\Fixture\InvalidHandler;

class EventTriggerPassTest extends TestCase
{
    public function testProcess()
    {
        $pass = new EventTriggerPass();
        $container = new ContainerBuilder();
        $this->setHandlerDefinition($container, true);

        $extension = new HasuraExtension();
        $extension->load([], $container);
        $pass->process($container);

        $this->assertTrue($container->hasDefinition('vxm.hasura.event_trigger.trigger_test'));
    }

    public function testProcessWithInvalidHandler(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $pass = new EventTriggerPass();
        $container = new ContainerBuilder();
        $this->setHandlerDefinition($container, false);

        $extension = new HasuraExtension();
        $extension->load([], $container);
        $pass->process($container);
    }

    private function setHandlerDefinition(ContainerBuilder $container, bool $isValid = true): void
    {
        if ($isValid) {
            $def = new Definition(Handler::class);
        } else {
            $def = new Definition(InvalidHandler::class);
        }

        $def->addTag(
            'vxm.hasura.event_trigger.handler',
            ['attribute' => \serialize(new AsHasuraEventHandler('test'))]
        );
        $container->setDefinition('handler', $def);
    }
}