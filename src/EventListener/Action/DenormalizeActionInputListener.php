<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener\Action;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\SerializerInterface;

final class DenormalizeActionInputListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;
        $action = $attributes->get('_hasura_action');

        if (null !== $action) {
            return;
        }

        $metadata = $action->getMetadata();

        if (null !== $metadata->getInputClass()) {
            $input = $this->serializer->denormalize(
                $attributes->get('_hasura_action_input'),
                $metadata->getInputClass(),
                context: $metadata->getDenormalizeContext()
            );
            $request->attributes->set('_hasura_action_input', $input);
        }
    }

}