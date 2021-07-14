<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Action;

use PHPUnit\Framework\TestCase;
use VXM\Hasura\Action\Action;
use VXM\Hasura\Action\ActionManager;
use VXM\Hasura\Action\Metadata;
use VXM\Hasura\Action\ResolverInterface;

class ActionManagerTest extends TestCase
{
    public function testHasAction(): void
    {
        $manager = $this->createActionManager();

        $this->assertTrue($manager->hasAction('a'));
        $this->assertTrue($manager->hasAction('b'));
        $this->assertFalse($manager->hasAction('c'));
    }

    public function testGetAction(): void
    {
        $manager = $this->createActionManager();

        $this->assertSame('a', $manager->getAction('a')->getMetadata()->getName());
        $this->assertSame('b', $manager->getAction('b')->getMetadata()->getName());
        $this->assertInstanceOf(ResolverInterface::class, $manager->getAction('a')->getResolver());
        $this->assertInstanceOf(ResolverInterface::class, $manager->getAction('b')->getResolver());

        $this->expectException(\InvalidArgumentException::class);
        $manager->getAction('c');
    }

    private function createActionManager(): ActionManager
    {
        $actions = [
            'a' => new Action(new Metadata('a'), $this->createMock(ResolverInterface::class)),
            'b' => new Action(new Metadata('b'), $this->createMock(ResolverInterface::class)),
        ];

        return new ActionManager($actions);
    }
}