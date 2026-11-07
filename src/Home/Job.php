<?php

declare(strict_types=1);

namespace Kantta\Home;

/** One piece of work in the queue, with its priority, its two days and its done day. */
final class Job
{
    public const HIGH = 3;
    public const NORMAL = 2;
    public const LOW = 1;

    public function __construct(
        public readonly string $handle,
        public readonly string $title,
        public readonly string $priority,
        public readonly string $filedDay,
        public readonly string $dueDay,
    ) {
        if (trim($handle) === '') {
            throw new \RuntimeException('a job carries a handle');
        }
        if (trim($title) === '') {
            throw new \RuntimeException('a job carries a title');
        }
        if (!in_array($priority, ['high', 'normal', 'low'], true)) {
            throw new \RuntimeException('a priority is high, normal or low');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filedDay)) {
            throw new \RuntimeException('a job carries its filed day as 2026-01-31');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDay)) {
            throw new \RuntimeException('a job carries its due day as 2026-01-31');
        }
        if ($dueDay < $filedDay) {
            throw new \RuntimeException('a job cannot fall due before it was filed');
        }
    }

    public function weight(): int
    {
        return match ($this->priority) {
            'high' => self::HIGH,
            'normal' => self::NORMAL,
            default => self::LOW,
        };
    }

    public function late(?string $today): bool
    {
        return $today !== null && $today > $this->dueDay;
    }
}
