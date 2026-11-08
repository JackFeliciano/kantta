<?php

declare(strict_types=1);

namespace Kantta\Home;

/** One executive inside one seat, from the seat's first day to its last. */
final class Engagement
{
    public function __construct(
        public readonly string $executive,
        public readonly Seat $seat,
        public readonly string $company,
    ) {
    }

    public function firstDay(): string
    {
        return $this->seat->firstDay;
    }

    public function lastDay(): string
    {
        return $this->seat->lastDay;
    }

    public function hoursADay(): int
    {
        return $this->seat->hoursADay;
    }

    public function overlaps(Engagement $other): bool
    {
        return $this->firstDay() <= $other->lastDay() && $other->firstDay() <= $this->lastDay();
    }
}
