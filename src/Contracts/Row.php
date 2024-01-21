<?php

namespace Matt\Vips\Contracts;

class Row
{
    function __construct(public string $path, public float $yOffset = 0, public float $xOffset = 0)
    {
    }
}