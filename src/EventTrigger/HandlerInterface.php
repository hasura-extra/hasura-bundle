<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventTrigger;

use Symfony\Component\Uid\Uuid;

interface HandlerInterface
{
    public function handle(
        Uuid $id,
        string $triggerName,
        string $op,
        array $tables,
        array $data,
        array $sessionVariables,
        \DateTimeImmutable $createdAt,
    ): void;
}