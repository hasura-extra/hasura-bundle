<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventTrigger;

final class EventTriggerManager
{
    public function __construct(private array $eventTriggers)
    {
    }

    public function hasEventTrigger(string $name): bool
    {
        return array_key_exists($name, $this->eventTriggers);
    }

    public function getEventTrigger(string $name): EventTrigger
    {
        if (!$this->hasEventTrigger($name)) {
            throw new \InvalidArgumentException(sprintf('Not found event trigger: `%s`', $name));
        }

        return $this->eventTriggers[$name];
    }
}