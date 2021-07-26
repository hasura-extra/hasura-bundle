<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Handler;

final class HandlerDescriptor
{
    public function __construct(
        private ActionHandlerInterface | EventHandlerInterface $handler,
        private array $attributes
    ) {
    }

    public function getHandle(): ActionHandlerInterface | EventHandlerInterface
    {
        return $this->handler;
    }

    public function getAttribute(string $attribute): mixed
    {
        return $this->attributes[$attribute] ?? null;
    }
}
