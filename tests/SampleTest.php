<?php

declare(strict_types=1);

use Kantta\Home\Sample;
use Kantta\Tests\Assert;

return [
    ['the sample holds five seats and four open today', static function (): void {
        $sample = new Sample();
        Assert::same(5, $sample->seats());
        Assert::same(4, $sample->openSeats());
    }],
    ['the sample holds three engagements for one executive', static function (): void {
        Assert::same(3, (new Sample())->engagements());
    }],
    ['the sample owes nine hours on the first Monday', static function (): void {
        Assert::same(3, (new Sample())->hoursOnMonday());
    }],
];
