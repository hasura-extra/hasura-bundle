<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\EventListener;

final class EventPriorities
{
    // kernel.request
    public const PRE_RESOLVE_REQUEST = 7;
    public const POST_RESOLVE_REQUEST = 5;
    public const PRE_ACTION_INPUT = 5;
    public const POST_ACTION_INPUT = 3;
    public const PRE_HANDLER = 3;
    public const POST_HANDLER = 1;

    // kernel.view
    public const PRE_ACTION_OUTPUT = 7;
    public const POST_ACTION_OUTPUT = 5;
    public const PRE_RESPOND = 5;

    // kernel.respond
    public const POST_RESPOND = 0;
}
