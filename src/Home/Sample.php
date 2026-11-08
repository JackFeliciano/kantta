<?php

declare(strict_types=1);

namespace Kantta\Home;

/**
 * Four companies, five seats and one executive holding three of them. The sample fixes
 * its own today, so the wall and the schedule read the same on any day a reader opens it.
 */
final class Sample
{
    public const TODAY = '2026-11-09';
    public const MONDAY = '2026-11-09';

    private Wall $wall;

    private Schedule $schedule;

    public function __construct()
    {
        $this->wall = new Wall();
        $this->wall->addCompany(new Company('northlin', 'Northlin Freight', 'Porto', 'logistics'));
        $this->wall->addCompany(new Company('arven', 'Arven Labs', 'Lisbon', 'software'));
        $this->wall->addCompany(new Company('marlowe', 'Marlowe Health', 'Coimbra', 'health'));
        $this->wall->addCompany(new Company('kestrel', 'Kestrel Works', 'Braga', 'manufacturing'));

        $this->wall->post(new Seat('ks-cfo', 'kestrel', 'cfo', 'Fractional CFO', 95000, 3, '2026-11-01', '2027-10-31'));
        $this->wall->post(new Seat('nw-cto', 'northlin', 'cto', 'Fractional CTO', 110000, 4, '2026-12-01', '2027-11-30'));
        $this->wall->post(new Seat('ar-coo', 'arven', 'coo', 'Fractional COO', 87000, 2, '2026-11-15', '2027-05-14'));
        $this->wall->post(new Seat('mh-cmo', 'marlowe', 'cmo', 'Fractional CMO', 78000, 2, '2026-05-01', '2026-10-31'));
        $this->wall->post(new Seat('ar-clo', 'arven', 'clo', 'Fractional CLO', 60000, 2, '2027-01-01', '2027-12-31'));

        $executive = new Executive('ana-reis', 'Ana Reis', 'Operations executive', 8);
        $this->schedule = new Schedule($executive);
        $this->schedule->take($this->wall->seat('ks-cfo'), 'kestrel');
        $this->schedule->take($this->wall->seat('ar-coo'), 'arven');
        $this->schedule->take($this->wall->seat('nw-cto'), 'northlin');
    }

    public function wall(): Wall
    {
        return $this->wall;
    }

    public function schedule(): Schedule
    {
        return $this->schedule;
    }

    public function seats(): int
    {
        return $this->wall->size();
    }

    public function openSeats(): int
    {
        return count($this->wall->find(['open' => true], self::TODAY));
    }

    public function engagements(): int
    {
        return count($this->schedule->engagements());
    }

    public function hoursOnMonday(): int
    {
        return $this->schedule->hoursOn(self::MONDAY);
    }
}
