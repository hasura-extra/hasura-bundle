<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class RespondListener
{
    use RequestAttributeExtractionTrait;

    public function onKernelView(ViewEvent $event)
    {
        $attributes = $this->extractAttributes($event->getRequest()->attributes);

        if (null === $attributes) {
            return;
        }

        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof Response) {
            $event->setResponse($controllerResult);

            return;
        }

        if (is_array($controllerResult)) {
            $event->setResponse(new JsonResponse($controllerResult, 200));

            return;
        }

        $event->setResponse(new Response($controllerResult, 200));
    }
}
