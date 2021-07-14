<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Tests\Fixture;

use Symfony\Component\Uid\Uuid;
use VXM\Hasura\Attribute\AsHasuraEventHandler;
use VXM\Hasura\EventTrigger\HandlerInterface;

#[AsHasuraEventHandler(triggerName: "test")]
final class Handler implements HandlerInterface
{
    public function handle(
        Uuid $id,
        string $triggerName,
        string $op,
        array $tables,
        array $data,
        array $sessionVariables,
        \DateTimeImmutable $createdAt,
    ): void {
    }
}