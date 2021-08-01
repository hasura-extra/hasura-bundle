<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DropInconsistentMetadataTest extends KernelTestCase
{
    use BackupAndRestoreMetadataTrait;
    use PutInconsistentTableTrait;

    public function testDropInconsistentMetadata(): void
    {
        $kernel = self::bootKernel();
        $tester = new CommandTester((new Application($kernel))->find('hasura:metadata:drop-inconsistent'));
        $client = $kernel->getContainer()->get('hasura.api_client.client');

        $this->putInconsistentTable();

        $metadata = $client->metadata()->export()['metadata'];
        $tableNames = array_column(
            array_column($metadata['sources'][0]['tables'], 'table'),
            'name'
        );

        $this->assertTrue(in_array('inconsistent_table', $tableNames, true));

        $tester->execute([]);
        $this->assertStringContainsString('Dropping...', $tester->getDisplay());
        $this->assertStringContainsString('Done!', $tester->getDisplay());

        $metadataAfterDrop = $client->metadata()->export()['metadata'];
        $tableNamesAfterDrop = array_column(
            array_column($metadataAfterDrop['sources'][0]['tables'], 'table'),
            'name'
        );

        $this->assertFalse(in_array('inconsistent_table', $tableNamesAfterDrop, true));
    }
}
