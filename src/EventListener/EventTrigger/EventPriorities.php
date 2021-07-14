<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener\EventTrigger;

use VXM\Hasura\EventListener\AbstractEventPriorities;

final class EventPriorities extends AbstractEventPriorities
{
    // kernel.request
    public const PRE_HANDLE_EVENT = 3;
    public const POST_HANDLE_EVENT = 1;
}
