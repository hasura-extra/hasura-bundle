<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class RespondListener
{
    public function onKernelView(ViewEvent $event)
    {
        $type = $event->getRequest()->attributes->get('_hasura');

        if (null === $type) {
            return;
        }

        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof Response) {
            $response = $controllerResult;
        } elseif (is_array($controllerResult)) {
            $response = new JsonResponse($controllerResult, 200);
        } else {
            $response = new Response($controllerResult, 200);
        }

        $event->setResponse($response);
    }
}
