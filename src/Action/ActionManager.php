<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Action;

final class ActionManager
{
    public function __construct(private array $actions)
    {
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->actions);
    }

    public function getAction(string $name): Action
    {
        if (!$this->hasAction($name)) {
            throw new \InvalidArgumentException(sprintf('Not found resolver for action: `%s`', $name));
        }

        return $this->actions[$name];
    }
}
