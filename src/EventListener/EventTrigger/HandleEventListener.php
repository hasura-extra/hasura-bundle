<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener\EventTrigger;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use VXM\Hasura\EventTrigger\EventTrigger;

final class HandleEventListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        /** @var EventTrigger $eventTrigger */
        $eventTrigger = $attributes->get('_hasura_event_trigger');

        if (null === $eventTrigger) {
            return;
        }

        $metadata = $eventTrigger->getMetadata();
        $handler = $eventTrigger->getHandler();
        $event = $attributes->get('_hasura_event');

        $handler->handle(
            $attributes->get('_hasura_event_id'),
            $metadata->getTriggerName(),
            $event['op'],
            $attributes->get('_hasura_event_table'),
            $event['data'],
            $event['session_variables'],
            $attributes->get('_hasura_event_created_at')
        );

        $attributes->set('data', []);
    }
}
