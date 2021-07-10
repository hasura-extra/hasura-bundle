<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;
use VXM\Hasura\Action\ActionManager;
use VXM\Hasura\EventTrigger\EventTriggerManager;
use VXM\Hasura\Validation\RequestValidatorInterface;

final class NormalizeRequestListener
{
    public function __construct(
        private RequestValidatorInterface $requestValidator,
        private ActionManager $actionManager,
        private EventTriggerManager $eventTriggerManager
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;
        $type = $attributes->get('_hasura');

        if (null === $type) {
            return;
        }

        $this->requestValidator->validate($request);

        $message = json_decode($request->getContent(), true);

        $this->enrichAttributes($type, $attributes, $message);
    }


    private function enrichAttributes(string $type, ParameterBag $attributes, array $message): void
    {
        if ('action' === $type) {
            $this->enrichActionAttributes($attributes, $message);
        } else {
            $this->enrichEventAttributes($attributes, $message);
        }
    }

    private function enrichActionAttributes(ParameterBag $attribute, array $message): void
    {
        $actionName = $message['action']['name'];

        if (!$this->actionManager->hasAction($actionName)) {
            throw new NotFoundHttpException(sprintf('Not found Hasura action: `%s`', $actionName));
        }

        $attribute->set('_hasura_action', $this->actionManager->getAction($actionName));
        $attribute->set('_hasura_action_input', $message['input']);
        $attribute->set('_hasura_action_session_variables', $message['session_variables']);
    }

    private function enrichEventAttributes(ParameterBag $attribute, array $message): void
    {
        $triggerName = $message['trigger']['name'];

        if (!$this->eventTriggerManager->hasEventTrigger($triggerName)) {
            throw new NotFoundHttpException(sprintf('Not found Hasura event trigger: `%s`', $triggerName));
        }

        $attribute->set('_hasura_event_trigger', $this->eventTriggerManager->getEventTrigger($triggerName));
        $attribute->set('_hasura_event_id', Uuid::fromString($message['id']));
        $attribute->set('_hasura_event_created_at', new \DateTimeImmutable($message['created_at']));
        $attribute->set('_hasura_event_table', $message['table']);
        $attribute->set('_hasura_event', $message['event']);
    }
}
