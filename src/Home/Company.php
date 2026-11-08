<?php

declare(strict_types=1);

namespace Kantta\Home;

/** The party hiring, with the sector the wall filters on. */
final class Company
{
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly string $town,
        public readonly string $sector,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a company carries a handle');
        }
        if (trim($name) === '') {
            throw new \RuntimeException('a company carries a name');
        }
        if (trim($sector) === '') {
            throw new \RuntimeException('a company carries a sector');
        }
    }
}
