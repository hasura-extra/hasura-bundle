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
use VXM\Hasura\Attribute\AsHasuraActionResolver;
use VXM\Hasura\Attribute\AsHasuraEventHandler;
use VXM\Hasura\DependencyInjection\HasuraExtension;

class HasuraExtensionTest extends TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();
        $extension = new HasuraExtension();
        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('vxm.hasura.action.manager'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.action.metadata'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.action.action'));

        $this->assertTrue($container->hasDefinition('vxm.hasura.event_trigger.manager'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_trigger.metadata'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_trigger.trigger'));

        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.action_input_validate'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.action_input_validation_exception'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.handle_event'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.denormalize_action_input'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.normalize_action_output'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.resolve_action'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.respond'));
        $this->assertTrue($container->hasDefinition('vxm.hasura.event_listener.normalize_request'));

        $this->assertTrue(isset($container->getAutoconfiguredAttributes()[AsHasuraActionResolver::class]));
        $this->assertTrue(isset($container->getAutoconfiguredAttributes()[AsHasuraEventHandler::class]));
    }

}