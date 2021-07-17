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
use VXM\Hasura\Attribute\AsEventHandler;
use VXM\Hasura\Handler\EventHandlerInterface;

#[AsEventHandler(name: 'test')]
final class EventHandler implements EventHandlerInterface
{
    public function handle(
        Uuid $id,
        string $triggerName,
        string $op,
        array $table,
        array $data,
        array $sessionVariables,
        \DateTimeImmutable $createdAt,
    ): void {
    }
}
