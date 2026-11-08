<?php

declare(strict_types=1);

namespace Kantta\Home;

/**
 * The job wall. It holds every seat and answers the ones a reader asks for, filtered
 * by role, by hours a day at the ceiling, by term in months at the floor and by sector.
 */
final class Wall
{
    /** @var array<string, Seat> */
    private array $seats = [];

    /** @var array<string, Company> */
    private array $companies = [];

    public function addCompany(Company $company): void
    {
        if (isset($this->companies[$company->handle])) {
            throw new \RuntimeException('the wall holds ' . $company->handle);
        }
        $this->companies[$company->handle] = $company;
    }

    public function company(string $handle): Company
    {
        return $this->companies[$handle] ?? throw new \RuntimeException('the wall holds no company called ' . $handle);
    }

    public function post(Seat $seat): void
    {
        $this->company($seat->company);
        if (isset($this->seats[$seat->handle])) {
            throw new \RuntimeException('the wall already holds ' . $seat->handle);
        }
        $this->seats[$seat->handle] = $seat;
    }

    public function seat(string $handle): Seat
    {
        return $this->seats[$handle] ?? throw new \RuntimeException('the wall holds no seat called ' . $handle);
    }

    /**
     * The seats a reader asks for. A filter left open takes every seat, so a reader who
     * names nothing sees the whole wall.
     *
     * @param array{role?: string, hours?: int, months?: int, sector?: string, open?: bool} $filter
     * @return list<Seat>
     */
    public function find(array $filter = [], ?string $today = null): array
    {
        $found = [];
        foreach ($this->seats as $seat) {
            if (isset($filter['role']) && $filter['role'] !== '' && $seat->role !== $filter['role']) {
                continue;
            }
            if (isset($filter['hours']) && $filter['hours'] > 0 && $seat->hoursADay > $filter['hours']) {
                continue;
            }
            if (isset($filter['months']) && $filter['months'] > 0 && $seat->months() < $filter['months']) {
                continue;
            }
            if (isset($filter['sector']) && $filter['sector'] !== ''
                && $this->company($seat->company)->sector !== $filter['sector']) {
                continue;
            }
            if (!empty($filter['open']) && $today !== null && !$seat->openOn($today)) {
                continue;
            }
            $found[] = $seat;
        }
        usort($found, static fn (Seat $a, Seat $b): int => [$b->hoursADay, $a->handle] <=> [$a->hoursADay, $b->handle]);

        return $found;
    }

    /** @return list<string> */
    public function sectors(): array
    {
        $sectors = [];
        foreach ($this->companies as $company) {
            $sectors[$company->sector] = true;
        }
        $list = array_keys($sectors);
        sort($list);

        return $list;
    }

    public function size(): int
    {
        return count($this->seats);
    }
}
