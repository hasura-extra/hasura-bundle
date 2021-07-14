<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventTrigger;

final class EventTrigger
{
    public function __construct(private Metadata $metadata, private HandlerInterface $handler)
    {
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function getHandler(): HandlerInterface
    {
        return $this->handler;
    }
}
