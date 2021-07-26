<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\EventListener;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @internal
 */
trait RequestAttributeExtractionTrait
{
    private function extractAttributes(ParameterBag $attributes, string $expectedType = null): ?array
    {
        $type = $attributes->get('_hasura');
        $descriptor = $attributes->get('_hasura_handler_descriptor');
        $data = $attributes->get('_hasura_request_data');

        if (null === $type || null === $data || null === $descriptor) {
            return null;
        }

        if (null !== $expectedType && $expectedType !== $type) {
            return null;
        }

        return [$descriptor, $data];
    }
}
