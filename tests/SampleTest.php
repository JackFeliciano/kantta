<?php

declare(strict_types=1);

use Kantta\Home\Sample;
use Kantta\Tests\Assert;

return [
    ['the sample holds four jobs and three waiting', static function (): void {
        $sample = new Sample();
        Assert::same(4, $sample->size());
        Assert::same(3, $sample->waiting());
    }],
    ['the sample answers one late job', static function (): void {
        Assert::same(1, (new Sample())->late());
    }],
    ['the sample runs the backup job first on the fixed day', static function (): void {
        Assert::same('job-1', (new Sample())->queue()->next()->handle);
    }],
    ['the sample holds the done day of the backup job', static function (): void {
        Assert::same('2026-10-02', (new Sample())->queue()->doneOn('job-2'));
    }],
];
