<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Action;

final class Metadata
{
    public function __construct(
        private string $name,
        private ?string $inputClass = null,
        private array $denormalizeContext = [],
        private bool $validate = true,
        private array $normalizeContext = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInputClass(): ?string
    {
        return $this->inputClass;
    }

    public function getDenormalizeContext(): array
    {
        return $this->denormalizeContext;
    }

    public function shouldValidate(): bool
    {
        return $this->validate;
    }

    public function getNormalizeContext(): array
    {
        return $this->normalizeContext;
    }
}