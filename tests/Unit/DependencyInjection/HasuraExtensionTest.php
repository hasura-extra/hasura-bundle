<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VXM\Hasura\Attribute\AsActionHandler;
use VXM\Hasura\Attribute\AsEventHandler;
use VXM\Hasura\DependencyInjection\HasuraExtension;

class HasuraExtensionTest extends TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();
        $extension = new HasuraExtension();
        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('vxm.hasura.handler.locator'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.handler.descriptor'));

        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.resolve_request'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.denormalize_action_input'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.action_input_validate'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.handler'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.normalize_action_output'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.respond'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.exception'));

        $this->assertTrue(isset($container->getAutoconfiguredAttributes()[AsEventHandler::class]));
        $this->assertTrue(isset($container->getAutoconfiguredAttributes()[AsActionHandler::class]));
    }
}