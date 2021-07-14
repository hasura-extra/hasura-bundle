<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener\Action;

use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Serializer\SerializerInterface;
use VXM\Hasura\Action\Action;

final class NormalizeActionOutputListener
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();
        /** @var Action $action */
        $action = $request->attributes->get('_hasura_action');

        if (null === $action) {
            return;
        }

        $metadata = $action->getMetadata();
        $controllerResult = $event->getControllerResult();

        if (!is_array($controllerResult)) {
            $controllerResult = $this->serializer->normalize(
                $controllerResult,
                'json',
                $metadata->getNormalizeContext()
            );

            $event->setControllerResult($controllerResult);
        }
    }
}
