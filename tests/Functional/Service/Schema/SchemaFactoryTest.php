<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional\Service\Schema;

use Hasura\Service\Schema\SchemaFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SchemaFactoryTest extends KernelTestCase
{
    public function testFactoryFromIntrospection(): void
    {
        /** @var SchemaFactory $factory */
        $factory = self::bootKernel()->getContainer()->get('hasura.service.schema.schema_factory');
        $schema = $factory->fromIntrospection();

        $this->assertTrue($schema->hasType('products'));
    }
}
