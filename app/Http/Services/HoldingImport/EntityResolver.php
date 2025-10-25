<?php

namespace App\Http\Services\HoldingImport;

class EntityResolver
{
    private array $cache = [];

    public function resolve(string $modelClass, string $name): int
    {
        if (! isset($this->cache[$modelClass])) {
            $this->cache[$modelClass] = [];
        }

        if (isset($this->cache[$modelClass][$name])) {
            return $this->cache[$modelClass][$name];
        }

        $entity = $modelClass::firstOrCreate(['name' => $name]);
        $this->cache[$modelClass][$name] = $entity->id;

        return $entity->id;
    }

    public function preload(string $modelClass, array $entities): void
    {
        $this->cache[$modelClass] = $entities;
    }
}
