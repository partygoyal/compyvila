<?php

namespace App\Support;

final class ToolCatalog
{
    public static function all(): array
    {
        return json_decode(file_get_contents(resource_path('data/tools.json')), true, flags: JSON_THROW_ON_ERROR);
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $tool) {
            if ($tool['slug'] === $slug) return $tool;
        }
        return null;
    }
}
