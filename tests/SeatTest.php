<?php

declare(strict_types=1);

use Kantta\Home\Seat;
use Kantta\Tests\Assert;

return [
    ['a seat runs six months at the shortest', static function (): void {
        Assert::same(6, (new Seat('a', 'kestrel', 'cfo', 'Fractional CFO', 95000, 3, '2026-11-01', '2027-04-30'))->months());
        Assert::throws(static fn () => new Seat('b', 'kestrel', 'cfo', 'Short', 95000, 3, '2026-11-01', '2027-03-31'), 'six months');
    }],
    ['a seat takes between two and eight hours a day', static function (): void {
        Assert::throws(static fn () => new Seat('c', 'kestrel', 'cfo', 'Light', 95000, 1, '2026-11-01', '2027-10-31'), 'between 2 and 8');
        Assert::throws(static fn () => new Seat('d', 'kestrel', 'cfo', 'Heavy', 95000, 9, '2026-11-01', '2027-10-31'), 'between 2 and 8');
    }],
    ['a role comes from the list', static function (): void {
        Assert::throws(static fn () => new Seat('e', 'kestrel', 'chair', 'Chair', 95000, 3, '2026-11-01', '2027-10-31'), 'a role is one of');
    }],
    ['a seat ends after it starts and carries a rate', static function (): void {
        Assert::throws(static fn () => new Seat('f', 'kestrel', 'cfo', 'Backwards', 95000, 3, '2027-10-31', '2026-11-01'), 'ends after it starts');
        Assert::throws(static fn () => new Seat('g', 'kestrel', 'cfo', 'Free', 0, 3, '2026-11-01', '2027-10-31'), 'rate in cents');
    }],
    ['a seat answers the hours a day it takes', static function (): void {
        $seat = new Seat('i', 'kestrel', 'cfo', 'Fractional CFO', 95000, 3, '2026-11-01', '2027-04-30');
        Assert::same(3, $seat->hoursADay);
    }],
    ['a seat is open until its last day and closed after', static function (): void {
        $seat = new Seat('h', 'kestrel', 'cfo', 'Fractional CFO', 95000, 3, '2026-11-01', '2027-04-30');
        Assert::same(true, $seat->openOn('2027-04-30'));
        Assert::same(false, $seat->openOn('2027-05-01'));
    }],
];
