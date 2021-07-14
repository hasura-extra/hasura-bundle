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
use VXM\Hasura\Attribute\AsHasuraActionResolver;
use VXM\Hasura\DependencyInjection\Compiler\ActionPass;
use VXM\Hasura\DependencyInjection\HasuraExtension;
use VXM\Hasura\Tests\Fixture\InvalidResolver;
use VXM\Hasura\Tests\Fixture\Resolver;

class ActionPassTest extends TestCase
{
    public function testProcess()
    {
        $pass = new ActionPass();
        $container = new ContainerBuilder();
        $this->setResolverDefinition($container, true);

        $extension = new HasuraExtension();
        $extension->load([], $container);
        $pass->process($container);

        $this->assertTrue($container->hasDefinition('vxm.hasura.action.action_test'));
    }

    public function testProcessWithInvalidResolver(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $pass = new ActionPass();
        $container = new ContainerBuilder();
        $this->setResolverDefinition($container, false);

        $extension = new HasuraExtension();
        $extension->load([], $container);
        $pass->process($container);
    }

    private function setResolverDefinition(ContainerBuilder $container, bool $isValid = true): void
    {
        if ($isValid) {
            $resolverDef = new Definition(Resolver::class);
        } else {
            $resolverDef = new Definition(InvalidResolver::class);
        }

        $resolverDef->addTag(
            'vxm.hasura.action.resolver',
            ['attribute' => \serialize(new AsHasuraActionResolver('test'))]
        );
        $container->setDefinition('vxm.hasura.tests.resolver', $resolverDef);
    }
}