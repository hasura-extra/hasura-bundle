<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AsActionHandler extends AbstractAsHandler
{
    public function __construct(
        string $name,
        public ?string $inputClass = null,
        public array $denormalizeContext = [],
        public bool $validate = true,
        public array $normalizeContext = [],
    ) {
        parent::__construct($name);
    }
}
