<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional\Command;

trait BackupAndRestoreMetadataTrait
{
    private ?array $backupMetadata = null;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $client = $kernel->getContainer()->get('hasura.api_client.client');
        $this->backupMetadata = $client->metadata()->export()['metadata'];
    }

    protected function tearDown(): void
    {
        $kernel = self::bootKernel();
        $client = $kernel->getContainer()->get('hasura.api_client.client');
        $client->metadata()->replace($this->backupMetadata);

        parent::tearDown();
    }
}
