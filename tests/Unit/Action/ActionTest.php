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
use VXM\Hasura\Action\Metadata;
use VXM\Hasura\Action\ResolverInterface;

class ActionTest extends TestCase
{
    public function testGetters()
    {
        $metadata = new Metadata('test');
        $resolver = $this->createMock(ResolverInterface::class);
        $action = new Action($metadata, $resolver);

        $this->assertSame($resolver, $action->getResolver());
        $this->assertSame($metadata, $action->getMetadata());
    }
}