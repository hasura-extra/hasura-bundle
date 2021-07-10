<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use VXM\Hasura\Validation\ActionInputValidationException;

final class ActionInputValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ActionInputValidationException) {
            return;
        }

        $response = new JsonResponse(
            [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ],
            422
        );

        $event->setResponse($response);
    }
}