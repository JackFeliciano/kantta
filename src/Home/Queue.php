<?php

declare(strict_types=1);

namespace Kantta\Home;

/**
 * The queue. It keeps jobs in filing order and answers them in queue order: the
 * higher priority first, the earlier filing next, the handle last.
 */
final class Queue
{
    /** @var array<string, Job> */
    private array $jobs = [];

    /** @var array<string, string> */
    private array $done = [];

    public function file(Job $job): void
    {
        if (isset($this->jobs[$job->handle])) {
            throw new \RuntimeException('the queue already holds ' . $job->handle);
        }
        $this->jobs[$job->handle] = $job;
    }

    public function job(string $handle): Job
    {
        return $this->jobs[$handle] ?? throw new \RuntimeException('the queue holds no job called ' . $handle);
    }

    public function run(string $handle, string $day): void
    {
        $job = $this->job($handle);
        if (isset($this->done[$handle])) {
            throw new \RuntimeException($handle . ' was done on ' . $this->done[$handle]);
        }
        if ($day < $job->filedDay) {
            throw new \RuntimeException('a job cannot be done before it was filed on ' . $job->filedDay);
        }
        $this->done[$handle] = $day;
    }

    public function doneOn(string $handle): ?string
    {
        return $this->done[$handle] ?? null;
    }

    /** @return list<Job> */
    public function waiting(): array
    {
        $waiting = [];
        foreach ($this->jobs as $job) {
            if (!isset($this->done[$job->handle])) {
                $waiting[] = $job;
            }
        }
        usort($waiting, static function (Job $a, Job $b): int {
            $byWeight = $b->weight() <=> $a->weight();
            if ($byWeight !== 0) {
                return $byWeight;
            }
            $byDay = $a->filedDay <=> $b->filedDay;
            if ($byDay !== 0) {
                return $byDay;
            }

            return strcmp($a->handle, $b->handle);
        });

        return $waiting;
    }

    public function next(): ?Job
    {
        return $this->waiting()[0] ?? null;
    }

    /** @return list<Job> */
    public function late(string $today): array
    {
        return array_values(array_filter(
            $this->waiting(),
            static fn (Job $job): bool => $job->late($today),
        ));
    }

    /** @return array<string, string> */
    public function done(): array
    {
        return $this->done;
    }

    public function size(): int
    {
        return count($this->jobs);
    }
}
