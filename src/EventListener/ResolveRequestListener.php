<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\EventListener;

use Hasura\Handler\HandlersLocator;
use Hasura\Validation\RequestValidatorInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ResolveRequestListener
{
    public function __construct(
        private RequestValidatorInterface $requestValidator,
        private HandlersLocator $handlersLocator
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

        $data = json_decode($request->getContent(), true);
        $name = $data['action']['name'] ?? $data['trigger']['name'];

        try {
            $descriptor = match ($type) {
                'action' => $this->handlersLocator->getActionHandler($name),
                'event' => $this->handlersLocator->getEventHandler($name)
            };
        } catch (\InvalidArgumentException) {
            throw new NotFoundHttpException(sprintf('Not found handler for %s: `%s`', $type, $name));
        }

        $attributes->set('_hasura_handler_descriptor', $descriptor);
        $attributes->set('_hasura_request_data', $data);
    }
}
