<?php

declare(strict_types=1);

use Kantta\Home\Sample;
use Kantta\Tests\Assert;

function kan_wall(): Sample
{
    return new Sample();
}

return [
    ['the wall holds five seats from four companies', static function (): void {
        $sample = kan_wall();
        Assert::same(5, $sample->seats());
        Assert::same(['health', 'logistics', 'manufacturing', 'software'], $sample->wall()->sectors());
    }],
    ['the wall refuses a second seat with one handle', static function (): void {
        Assert::throws(static fn () => kan_wall()->wall()->post(
            new \Kantta\Home\Seat('ks-cfo', 'kestrel', 'cfo', 'Again', 95000, 3, '2026-11-01', '2027-10-31')
        ), 'already holds ks-cfo');
    }],
    ['the wall refuses a seat from a company it does not hold', static function (): void {
        Assert::throws(static fn () => kan_wall()->wall()->post(
            new \Kantta\Home\Seat('zz-cfo', 'nobody', 'cfo', 'Orphan', 95000, 3, '2026-11-01', '2027-10-31')
        ), 'no company called nobody');
    }],
    ['an open filter takes every seat', static function (): void {
        Assert::same(5, count(kan_wall()->wall()->find()));
    }],
    ['the role filter answers one role', static function (): void {
        $found = kan_wall()->wall()->find(['role' => 'cto']);
        Assert::same(1, count($found));
        Assert::same('nw-cto', $found[0]->handle);
    }],
    ['the hours filter holds the ceiling', static function (): void {
        Assert::same(3, count(kan_wall()->wall()->find(['hours' => 2])));
        Assert::same(5, count(kan_wall()->wall()->find(['hours' => 4])));
    }],
    ['the term filter holds the floor', static function (): void {
        Assert::same(3, count(kan_wall()->wall()->find(['months' => 12])));
    }],
    ['the sector filter reads the company', static function (): void {
        $found = kan_wall()->wall()->find(['sector' => 'software']);
        Assert::same(2, count($found));
    }],
    ['the open filter drops a seat closed today', static function (): void {
        Assert::same(4, count(kan_wall()->wall()->find(['open' => true], Sample::TODAY)));
    }],
    ['two filters narrow together', static function (): void {
        Assert::same(1, count(kan_wall()->wall()->find(['sector' => 'software', 'role' => 'coo'])));
        Assert::same(0, count(kan_wall()->wall()->find(['sector' => 'software', 'role' => 'cfo'])));
    }],
    ['the wall puts the heavier seats first', static function (): void {
        $found = kan_wall()->wall()->find();
        Assert::same('nw-cto', $found[0]->handle);
        Assert::same(4, $found[0]->hoursADay);
    }],
];
