<?php

declare(strict_types=1);

use Kantta\Home\Executive;
use Kantta\Home\Sample;
use Kantta\Home\Schedule;
use Kantta\Home\Seat;
use Kantta\Tests\Assert;

return [
    ['the schedule holds the engagements an executive took', static function (): void {
        $sample = new Sample();
        Assert::same(3, $sample->engagements());
        Assert::same('ana-reis', $sample->schedule()->executive->handle);
    }],
    ['the schedule refuses a second seat at one company', static function (): void {
        $sample = new Sample();
        Assert::throws(static fn () => $sample->schedule()->take($sample->wall()->seat('ar-clo'), 'arven'),
            'one seat at arven');
    }],
    ['the schedule refuses hours past the ceiling on an overlapping day', static function (): void {
        $schedule = new Schedule(new Executive('test', 'Test', 'Tester', 6));
        $schedule->take(new Seat('s1', 'a', 'cfo', 'CFO', 95000, 4, '2026-11-01', '2027-06-30'), 'a');
        Assert::throws(static fn () => $schedule->take(
            new Seat('s2', 'b', 'cto', 'CTO', 95000, 4, '2026-12-01', '2027-07-31'), 'b'
        ), 'run past 6 a day');
    }],
    ['the schedule takes hours that fit under the ceiling', static function (): void {
        $schedule = new Schedule(new Executive('test', 'Test', 'Tester', 6));
        $schedule->take(new Seat('s1', 'a', 'cfo', 'CFO', 95000, 4, '2026-11-01', '2027-06-30'), 'a');
        $schedule->take(new Seat('s2', 'b', 'cto', 'CTO', 95000, 2, '2026-12-01', '2027-07-31'), 'b');
        Assert::same(2, count($schedule->engagements()));
    }],
    ['the schedule takes two seats that never meet in time', static function (): void {
        $schedule = new Schedule(new Executive('test', 'Test', 'Tester', 4));
        $schedule->take(new Seat('s1', 'a', 'cfo', 'CFO', 95000, 4, '2026-11-01', '2027-06-30'), 'a');
        $schedule->take(new Seat('s2', 'b', 'cto', 'CTO', 95000, 4, '2027-07-01', '2028-06-30'), 'b');
        Assert::same(2, count($schedule->engagements()));
    }],
    ['the schedule counts the hours a day of each engagement', static function (): void {
        $schedule = (new Sample())->schedule();
        Assert::same(9, $schedule->hoursOn('2026-12-02'));
        Assert::same(3, $schedule->hoursOn('2026-11-01'));
    }],
    ['the schedule marks a day past the ceiling', static function (): void {
        $schedule = (new Sample())->schedule();
        Assert::same(true, $schedule->overOn('2026-12-02'));
        Assert::same(false, $schedule->overOn('2026-11-01'));
    }],
    ['a day takes the hours of every engagement that holds it', static function (): void {
        $schedule = (new Sample())->schedule();
        Assert::same(5, $schedule->hoursOn('2026-11-20'));
        Assert::same(false, $schedule->overOn('2026-11-20'));
    }],
    ['the schedule answers a week of seven days', static function (): void {
        $week = (new Sample())->schedule()->week(Sample::MONDAY);
        Assert::same(7, count($week));
        Assert::same('2026-11-09', $week[0]['day']);
        Assert::same('2026-11-15', $week[6]['day']);
        Assert::same('2026-11-15', $week[6]['day']);
        Assert::same(3, $week[0]['hours']);
    }],
    ['the schedule counts the days past the ceiling in a range', static function (): void {
        Assert::same(0, (new Sample())->schedule()->overDays(Sample::MONDAY, '2026-11-14'));
        Assert::same(1, (new Sample())->schedule()->overDays('2026-11-16', '2026-12-01'));
        Assert::same(30, (new Sample())->schedule()->overDays('2026-12-02', '2026-12-31'));
    }],
];
