<?php
/*
 * (c) Minh Vuong <vuongxuongminh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare(strict_types=1);

namespace Hasura\Service\Metadata;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml;

final class FileExportedParser
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    public function parse(string $metadataPath): array
    {
        $metadata = [];

        foreach (FileExport::FIELDS_MAPPING as $field => $file) {
            $file = sprintf('%s/%s', $metadataPath, $file);

            if (!$this->filesystem->exists($file)) {
                continue;
            }

            $metadata[$field] = $this->parseYamlFile($file);
        }

        return $metadata;
    }

    private function parseYamlFile(string $file): mixed
    {
        $data = Yaml::parseFile($file, Yaml::PARSE_CUSTOM_TAGS);
        $inPath = dirname($file);

        if ($data instanceof TaggedValue) {
            $data = $this->parseTaggedValue($data, $inPath);
        }

        if (is_array($data)) {
            $data = $this->parseArrayValue($data, $inPath);
        }

        return $data;
    }

    private function parseArrayValue(array $value, string $inPath): array
    {
        foreach ($value as &$item) {
            if (is_array($item)) {
                $item = $this->parseArrayValue($item, $inPath);
                continue;
            }

            if ($item instanceof TaggedValue) {
                $item = $this->parseTaggedValue($item, $inPath);
            }
        }

        return $value;
    }

    private function parseTaggedValue(TaggedValue $value, string $inPath): mixed
    {
        if ('include' !== $value->getTag()) {
            throw new \RuntimeException('Only support include tag, tag: `%s` is not support', $value->getTag());
        }

        $file = sprintf('%s/%s', $inPath, $value->getValue());

        return $this->parseYamlFile($file);
    }
}