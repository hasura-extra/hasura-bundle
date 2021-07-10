<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

abstract class AbstractEventPriorities
{
    // kernel.request
    public const PRE_NORMALIZE_REQUEST = 17;
    public const POST_NORMALIZE_REQUEST = 15;

    // kernel.view
    public const PRE_RESPOND = 9;

    // kernel.respond
    public const POST_RESPOND = 0;
}