<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

final class EventPriorities
{
    // kernel.request
    public const PRE_RESOLVE_REQUEST = 17;
    public const POST_RESOLVE_REQUEST = 15;
    public const PRE_DENORMALIZE_ACTION_INPUT = 7;
    public const POST_DENORMALIZE_ACTION_INPUT = 5;
    public const PRE_ACTION_INPUT_VALIDATE = 5;
    public const POST_ACTION_INPUT_VALIDATE = 3;
    public const PRE_HANDLER = 3;
    public const POST_HANDLER = 1;

    // kernel.view
    public const PRE_NORMALIZE_ACTION_OUTPUT = 17;
    public const POST_NORMALIZE_ACTION_OUTPUT = 15;
    public const PRE_RESPOND = 9;

    // kernel.respond
    public const POST_RESPOND = 0;
}