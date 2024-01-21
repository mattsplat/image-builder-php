<?php

namespace Matt\Vips\Contracts;

class Column
{
    public array $rows = [];
    function __construct(public string $type, array $rows = [])
    {
        foreach ($rows as $row) {
            $this->rows[] = new Row(
                path: $row['path'],
                yOffset: $row['yOffset'] ?? 0,
                xOffset: $roow['xOffset'] ?? 0,
            );
        }
    }

    public function getUrlSet() : array
    {
        $urlSet = [];
        foreach ($this->rows as $row) {
            $urlSet[] = $row->path;
        }
        return array_unique($urlSet);
    }
}