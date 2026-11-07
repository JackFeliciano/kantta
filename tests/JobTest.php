<?php

declare(strict_types=1);

use Kantta\Home\Job;
use Kantta\Tests\Assert;

return [
    ['a job carries a handle, a title, a priority and two days', static function (): void {
        Assert::throws(static fn () => new Job(' ', 'A title', 'high', '2026-10-01', '2026-10-02'), 'carries a handle');
        Assert::throws(static fn () => new Job('j', ' ', 'high', '2026-10-01', '2026-10-02'), 'carries a title');
        Assert::throws(static fn () => new Job('j', 'A title', 'urgent', '2026-10-01', '2026-10-02'), 'high, normal or low');
        Assert::throws(static fn () => new Job('j', 'A title', 'high', '1 October', '2026-10-02'), 'filed day');
        Assert::throws(static fn () => new Job('j', 'A title', 'high', '2026-10-01', '2 October'), 'due day');
    }],
    ['a job cannot fall due before it was filed', static function (): void {
        Assert::throws(static fn () => new Job('j', 'A title', 'high', '2026-10-05', '2026-10-04'), 'cannot fall due before');
    }],
    ['a priority weighs high above normal above low', static function (): void {
        $high = new Job('a', 'A', 'high', '2026-10-01', '2026-10-02');
        $normal = new Job('b', 'B', 'normal', '2026-10-01', '2026-10-02');
        $low = new Job('c', 'C', 'low', '2026-10-01', '2026-10-02');
        Assert::same(true, $high->weight() > $normal->weight());
        Assert::same(true, $normal->weight() > $low->weight());
    }],
    ['a job is late after its due day and never on the day itself', static function (): void {
        $job = new Job('j', 'A title', 'normal', '2026-10-01', '2026-10-05');
        Assert::same(false, $job->late('2026-10-05'));
        Assert::same(true, $job->late('2026-10-06'));
        Assert::same(false, $job->late(null));
    }],
];
