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
use VXM\Hasura\Action\Metadata;

class MetadataTest extends TestCase
{
    public function testGetters(): void
    {
        $metadata = new Metadata('a', 'b', ['c'], false, ['d']);

        $this->assertSame('a', $metadata->getName());
        $this->assertSame('b', $metadata->getInputClass());
        $this->assertSame(['c'], $metadata->getDenormalizeContext());
        $this->assertSame(false, $metadata->shouldValidate());
        $this->assertSame(['d'], $metadata->getNormalizeContext());
    }
}