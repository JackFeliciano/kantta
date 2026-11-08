<?php

declare(strict_types=1);

namespace Kantta\Home;

/**
 * One open fractional seat. It carries the term it lasts and the hours a day it takes,
 * which are the two numbers the wall filters on and the schedule counts with.
 */
final class Seat
{
    public const ROLES = ['ceo', 'cfo', 'coo', 'cto', 'cmo', 'chro', 'clo', 'cso'];

    public function __construct(
        public readonly string $handle,
        public readonly string $company,
        public readonly string $role,
        public readonly string $title,
        public readonly int $rateCents,
        public readonly int $hoursADay,
        public readonly string $firstDay,
        public readonly string $lastDay,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a seat carries a handle');
        }
        if (!in_array($role, self::ROLES, true)) {
            throw new \RuntimeException('a role is one of ' . implode(', ', self::ROLES));
        }
        if ($rateCents <= 0) {
            throw new \RuntimeException('a seat carries a rate in cents');
        }
        if ($hoursADay < 2 || $hoursADay > 8) {
            throw new \RuntimeException('a seat takes between 2 and 8 hours a day');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $firstDay) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $lastDay)) {
            throw new \RuntimeException('a seat carries its days as 2026-01-31');
        }
        if ($lastDay <= $firstDay) {
            throw new \RuntimeException('a seat ends after it starts');
        }
        if ($this->months() < 6) {
            throw new \RuntimeException('a seat runs at least six months');
        }
    }

    /** The days the term holds, both ends counted. */
    public function days(): int
    {
        $first = new \DateTimeImmutable($this->firstDay);
        $last = new \DateTimeImmutable($this->lastDay);

        return (int) $first->diff($last)->days + 1;
    }

    /** The months the term holds, taken as whole months of thirty days rounded down. */
    public function months(): int
    {
        return intdiv($this->days(), 30);
    }

    public function openOn(string $today): bool
    {
        return $today <= $this->lastDay;
    }
}
