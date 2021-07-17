<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Handler;

interface EventHandlerInterface
{
    public function handle(
        string $name,
        string $id,
        array $table,
        array $event,
        string $createdAt,
        array $deliveryInfo
    ): void;
}
