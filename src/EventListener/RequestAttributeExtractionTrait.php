<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\EventListener;

use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
trait RequestAttributeExtractionTrait
{
    private function extractAttributes(Request $request, string $expectedType = null): ?array
    {
        $type = $request->attributes->get('_hasura');
        $descriptor = $request->attributes->get('_hasura_handler_descriptor');
        $data = $request->attributes->get('_hasura_request_data');

        if (null === $type || null === $data || null === $descriptor) {
            return null;
        }

        if (null !== $expectedType && $expectedType !== $type) {
            return null;
        }

        return [$descriptor, $data];
    }
}
