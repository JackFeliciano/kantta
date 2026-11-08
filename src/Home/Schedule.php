<?php

declare(strict_types=1);

namespace Kantta\Home;

/**
 * The schedule manager. It holds one executive's engagements and answers what the
 * executive owes on a week and which days run past the hours they can give.
 */
final class Schedule
{
    /** @var list<Engagement> */
    private array $engagements = [];

    public function __construct(public readonly Executive $executive)
    {
    }

    public function take(Seat $seat, string $company): Engagement
    {
        foreach ($this->engagements as $held) {
            if ($held->company === $company) {
                throw new \RuntimeException('one executive takes one seat at ' . $company);
            }
            $clash = $held->overlaps(new Engagement($this->executive->handle, $seat, $company));
            if ($clash && $held->hoursADay() + $seat->hoursADay > $this->executive->hoursADay) {
                throw new \RuntimeException('the hours run past ' . $this->executive->hoursADay
                    . ' a day against ' . $held->seat->handle);
            }
        }
        $engagement = new Engagement($this->executive->handle, $seat, $company);
        $this->engagements[] = $engagement;

        return $engagement;
    }

    /** @return list<Engagement> */
    public function engagements(): array
    {
        return $this->engagements;
    }

    /** The hours the executive owes on a day, over every engagement that holds that day. */
    public function hoursOn(string $day): int
    {
        $hours = 0;
        foreach ($this->engagements as $engagement) {
            if ($engagement->firstDay() <= $day && $day <= $engagement->lastDay()) {
                $hours += $engagement->hoursADay();
            }
        }

        return $hours;
    }

    public function overOn(string $day): bool
    {
        return $this->hoursOn($day) > $this->executive->hoursADay;
    }

    /**
     * The seven days from a Monday, each with the hours owed and whether the day runs
     * past the ceiling.
     *
     * @return list<array{day: string, hours: int, over: bool}>
     */
    public function week(string $monday): array
    {
        $start = new \DateTimeImmutable($monday);
        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->modify('+' . $i . ' day')->format('Y-m-d');
            $week[] = ['day' => $day, 'hours' => $this->hoursOn($day), 'over' => $this->overOn($day)];
        }

        return $week;
    }

    public function overDays(string $from, string $to): int
    {
        $start = new \DateTimeImmutable($from);
        $end = new \DateTimeImmutable($to);
        $count = 0;
        for ($day = $start; $day <= $end; $day = $day->modify('+1 day')) {
            if ($this->overOn($day->format('Y-m-d'))) {
                $count++;
            }
        }

        return $count;
    }
}
