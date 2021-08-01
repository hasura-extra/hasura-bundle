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
        $value = Yaml::parseFile($file, Yaml::PARSE_CUSTOM_TAGS | Yaml::PARSE_OBJECT_FOR_MAP);
        $inPath = dirname($file);

        return $this->parseValue($value, $inPath);
    }

    private function parseValue(mixed $value, string $inPath): mixed
    {
        if ($value instanceof \stdClass && ($arrayValue = get_object_vars($value))) {
            $value = $arrayValue;
        }

        if ($value instanceof TaggedValue) {
            $value = $this->parseTaggedValue($value, $inPath);
        }

        if (is_array($value)) {
            foreach ($value as &$item) {
                $item = $this->parseValue($item, $inPath);
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
