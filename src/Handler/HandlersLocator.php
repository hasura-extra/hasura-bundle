<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Handler;

final class HandlersLocator
{
    /**
     * @param HandlerDescriptor[] $actionHandlers
     * @param HandlerDescriptor[] $eventHandlers
     */
    public function __construct(private array $actionHandlers, private array $eventHandlers)
    {
    }

    public function hasActionHandler(string $name): bool
    {
        return $this->hasHandler($name, true);
    }

    public function getActionHandler(string $name): HandlerDescriptor
    {
        return $this->getHandler($name, true);
    }

    public function hasEventHandler(string $name): bool
    {
        return $this->hasHandler($name, false);
    }

    public function getEventHandler(string $name): HandlerDescriptor
    {
        return $this->getHandler($name, false);
    }

    private function hasHandler(string $name, bool $isAction): bool
    {
        if ($isAction) {
            return array_key_exists($name, $this->actionHandlers);
        }

        return array_key_exists($name, $this->eventHandlers);
    }

    private function getHandler(string $name, bool $isAction): HandlerDescriptor
    {
        if (!$this->hasHandler($name, $isAction)) {
            throw new \InvalidArgumentException(sprintf('Not found resolver for action: `%s`', $name));
        }

        return $isAction ? $this->actionHandlers[$name] : $this->eventHandlers[$name];
    }
}
