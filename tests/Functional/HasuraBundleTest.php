<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HasuraBundleTest extends KernelTestCase
{
    public function testLoad()
    {
        $kernel = self::bootKernel();

        $container = $kernel->getContainer();

        $this->assertTrue($container->has('hasura.api_client.client'));
        $this->assertTrue($container->has('hasura.command.apply_metadata'));
        $this->assertTrue($container->has('hasura.command.export_metadata'));
        $this->assertTrue($container->has('hasura.service.metadata.manager'));
        $this->assertTrue($container->has('hasura.event_listener.resolve_request'));
        $this->assertTrue($container->has('hasura.event_listener.action_input'));
        $this->assertTrue($container->has('hasura.event_listener.handler'));
        $this->assertTrue($container->has('hasura.event_listener.action_output'));
        $this->assertTrue($container->has('hasura.event_listener.respond'));
        $this->assertTrue($container->has('hasura.event_listener.exception'));

        $this->assertTrue($container->has('hasura.handler.descriptor_action_test'));
        $this->assertTrue($container->has('hasura.handler.descriptor_event_test'));
    }
}
