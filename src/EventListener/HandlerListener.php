<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Uid\Uuid;
use VXM\Hasura\Handler\ActionHandlerInterface;
use VXM\Hasura\Handler\HandlerDescriptor;

final class HandlerListener
{
    use RequestAttributeExtractionTrait;

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = $this->extractAttributes($request);

        if (null === $attributes) {
            return;
        }

        /** @var HandlerDescriptor $descriptor */
        [$descriptor, $data] = $attributes;
        $handler = $descriptor->getHandle();
        $name = $descriptor->getAttribute('name');

        if ($handler instanceof ActionHandlerInterface) {
            $result = $handler->handle(
                $name,
                $data['input'],
                $data['session_variables']
            );
        } else {
            $result = [];
            $handler->handle(
                Uuid::fromString($data['id']),
                $name,
                $data['event']['op'],
                $data['table'],
                $data['event']['data'],
                $data['event']['session_variables'],
                new \DateTimeImmutable($data['created_at'])
            );
        }

        $request->attributes->set('data', $result);
    }
}
