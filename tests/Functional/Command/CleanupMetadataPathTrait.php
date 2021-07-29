<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Tests\Functional\Command;

use Symfony\Component\Filesystem\Filesystem;

trait CleanupMetadataPathTrait
{
    protected function tearDown(): void
    {
//        $dir = sprintf('%s/hasura', self::bootKernel()->getProjectDir());
//
//        (new Filesystem())->remove($dir);
//        (new Filesystem())->mkdir($dir);

        parent::tearDown();
    }
}
