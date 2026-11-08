<?php

declare(strict_types=1);

namespace Kantta\Home;

/** The party taking seats, and the hours a day it can give across all of them. */
final class Executive
{
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly string $title,
        public readonly int $hoursADay,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('an executive carries a handle');
        }
        if (trim($name) === '') {
            throw new \RuntimeException('an executive carries a name');
        }
        if ($hoursADay < 2 || $hoursADay > 12) {
            throw new \RuntimeException('an executive gives between 2 and 12 hours a day');
        }
    }
}
