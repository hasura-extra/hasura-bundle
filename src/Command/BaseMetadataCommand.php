<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Command;

use Hasura\Service\ManagerInterface;

abstract class BaseMetadataCommand extends BaseCommand
{
    public function __construct(protected ManagerInterface $metadataManager)
    {
        parent::__construct();
    }
}
