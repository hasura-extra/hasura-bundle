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
use Symfony\Component\Console\Tester\CommandTester;

trait PutInconsistentTableTrait
{
    private function putInconsistentTable(): void
    {
        $kernel = self::bootKernel();
        $applier = new CommandTester((new Application($kernel))->find('hasura:metadata:apply'));
        $metadataPath = $kernel->getContainer()->getParameter('hasura.metadata_path');

        file_put_contents(
            $metadataPath . '/sources/default/tables/inconsistent_table.yaml',
            <<<'IN_CONSISTENT'
table:
    schema: public
    name: inconsistent_table
IN_CONSISTENT
        );

        file_put_contents(
            $metadataPath . '/sources/default/tables.yaml',
            sprintf(
                "%s\n%s",
                file_get_contents($metadataPath . '/sources/default/tables.yaml'),
                '- !include tables/inconsistent_table.yaml'
            )
        );

        $applier->execute(['--allow-inconsistency' => true]);
    }
}
