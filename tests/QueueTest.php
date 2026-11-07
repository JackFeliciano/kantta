<?php

declare(strict_types=1);

use Kantta\Home\Job;
use Kantta\Home\Queue;
use Kantta\Tests\Assert;

function kan_queue(): Queue
{
    $queue = new Queue();
    $queue->file(new Job('job-3', 'Rebuild the index', 'normal', '2026-10-06', '2026-10-20'));
    $queue->file(new Job('job-1', 'Post the invoices', 'high', '2026-10-09', '2026-10-10'));
    $queue->file(new Job('job-4', 'Answer the survey', 'low', '2026-10-02', '2026-10-30'));
    $queue->file(new Job('job-2', 'Check the backup', 'high', '2026-10-01', '2026-10-02'));

    return $queue;
}

return [
    ['the queue files a job once', static function (): void {
        $queue = kan_queue();
        Assert::same(4, $queue->size());
        Assert::throws(static fn () => $queue->file(new Job('job-1', 'Again', 'high', '2026-10-09', '2026-10-10')), 'already holds job-1');
    }],
    ['the queue puts the higher priority first', static function (): void {
        $order = array_map(static fn (Job $j): string => $j->handle, kan_queue()->waiting());
        Assert::same(['job-2', 'job-1', 'job-3', 'job-4'], $order);
    }],
    ['the earlier filing goes first among one priority', static function (): void {
        $queue = new Queue();
        $queue->file(new Job('b', 'B', 'normal', '2026-10-03', '2026-10-20'));
        $queue->file(new Job('a', 'A', 'normal', '2026-10-01', '2026-10-20'));
        Assert::same(['a', 'b'], array_map(static fn (Job $j): string => $j->handle, $queue->waiting()));
    }],
    ['the handle decides on one day at one priority', static function (): void {
        $queue = new Queue();
        $queue->file(new Job('b', 'B', 'normal', '2026-10-01', '2026-10-20'));
        $queue->file(new Job('a', 'A', 'normal', '2026-10-01', '2026-10-20'));
        Assert::same(['a', 'b'], array_map(static fn (Job $j): string => $j->handle, $queue->waiting()));
    }],
    ['the next job is the head of the queue', static function (): void {
        Assert::same('job-2', kan_queue()->next()->handle);
    }],
    ['an empty queue answers no next job', static function (): void {
        Assert::same(null, (new Queue())->next());
    }],
    ['a done job leaves the waiting list', static function (): void {
        $queue = kan_queue();
        $queue->run('job-2', '2026-10-02');
        Assert::same('job-1', $queue->next()->handle);
        Assert::same(3, count($queue->waiting()));
        Assert::same('2026-10-02', $queue->doneOn('job-2'));
    }],
    ['a job runs once', static function (): void {
        $queue = kan_queue();
        $queue->run('job-2', '2026-10-02');
        Assert::throws(static fn () => $queue->run('job-2', '2026-10-03'), 'was done on 2026-10-02');
    }],
    ['a job cannot be done before it was filed', static function (): void {
        Assert::throws(static fn () => kan_queue()->run('job-1', '2026-10-08'), 'before it was filed');
    }],
    ['an unknown job answers nothing', static function (): void {
        Assert::throws(static fn () => kan_queue()->run('job-9', '2026-10-10'), 'no job called job-9');
    }],
    ['the queue marks the jobs past their due day', static function (): void {
        $late = array_map(static fn (Job $j): string => $j->handle, kan_queue()->late('2026-10-12'));
        Assert::same(['job-2', 'job-1'], $late);
    }],
];
