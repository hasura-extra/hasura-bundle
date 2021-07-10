<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace VXM\Hasura\Action;


final class Action
{
    public function __construct(private Metadata $metadata, private ResolverInterface $resolver)
    {
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function getResolver(): ResolverInterface
    {
        return $this->resolver;
    }
}