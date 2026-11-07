<?php

declare(strict_types=1);

namespace Kantta\Home;

/**
 * Four jobs on one server, one of them done. The queue answers what runs next and
 * what has fallen late against a fixed day, so the page never moves under a reader.
 */
final class Sample
{
    public const TODAY = '2026-10-12';

    private Queue $queue;

    public function __construct()
    {
        $this->queue = new Queue();
        $this->queue->file(new Job('job-3', 'Rebuild the index', 'normal', '2026-10-06', '2026-10-20'));
        $this->queue->file(new Job('job-1', 'Post the invoices', 'high', '2026-10-09', '2026-10-10'));
        $this->queue->file(new Job('job-4', 'Answer the survey', 'low', '2026-10-02', '2026-10-30'));
        $this->queue->file(new Job('job-2', 'Check the backup', 'high', '2026-10-01', '2026-10-02'));
        $this->queue->run('job-2', '2026-10-02');
    }

    public function queue(): Queue
    {
        return $this->queue;
    }

    public function size(): int
    {
        return $this->queue->size();
    }

    public function waiting(): int
    {
        return count($this->queue->waiting());
    }

    public function late(): int
    {
        return count($this->queue->late(self::TODAY));
    }
}
