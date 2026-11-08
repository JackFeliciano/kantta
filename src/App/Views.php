<?php

declare(strict_types=1);

namespace Kantta\App;

use Kantta\Home\Company;
use Kantta\Home\Engagement;
use Kantta\Home\Sample;
use Kantta\Home\Seat;

/**
 * The HTML. Every string reaching the page goes through h(), so a company name a
 * recruiter typed can carry an angle bracket without breaking the page.
 */
final class Views
{
    public function __construct(private readonly string $base = '/')
    {
    }

    private function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    private function money(int $cents, string $currency = 'EUR'): string
    {
        return $currency . ' ' . number_format($cents / 100, 2, '.', ',');
    }

    public function header(string $title): string
    {
        $base = $this->h($this->base);
        $t = $this->h($title);

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$t}</title>
<base href="{$base}">
<link rel="stylesheet" href="assets/site.css">
</head>
<body>
<main>
HTML;
    }

    public function footer(): string
    {
        return "</main>\n</body>\n</html>\n";
    }

    private function filterForm(Sample $sample, array $filter): string
    {
        $out = '<form class="filters" method="get" action="">' . "\n";
        $out .= '<label>Role <select name="role"><option value="">any</option>';
        foreach (Seat::ROLES as $role) {
            $picked = ($filter['role'] ?? '') === $role ? ' selected' : '';
            $out .= '<option value="' . $this->h($role) . '"' . $picked . '>' . $this->h(strtoupper($role)) . '</option>';
        }
        $out .= '</select></label>' . "\n";
        $out .= '<label>Hours a day up to <select name="hours"><option value="">any</option>';
        foreach ([2, 3, 4, 6, 8] as $hours) {
            $picked = (string) ($filter['hours'] ?? '') === (string) $hours ? ' selected' : '';
            $out .= '<option value="' . $hours . '"' . $picked . '>' . $hours . '</option>';
        }
        $out .= '</select></label>' . "\n";
        $out .= '<label>Term at least <select name="months"><option value="">any</option>';
        foreach ([6, 9, 12, 18] as $months) {
            $picked = (string) ($filter['months'] ?? '') === (string) $months ? ' selected' : '';
            $out .= '<option value="' . $months . '"' . $picked . '>' . $months . ' months</option>';
        }
        $out .= '</select></label>' . "\n";
        $out .= '<label>Sector <select name="sector"><option value="">any</option>';
        foreach ($sample->wall()->sectors() as $sector) {
            $picked = ($filter['sector'] ?? '') === $sector ? ' selected' : '';
            $out .= '<option value="' . $this->h($sector) . '"' . $picked . '>' . $this->h($sector) . '</option>';
        }
        $out .= '</select></label>' . "\n";
        $out .= '<label class="check"><input type="checkbox" name="open" value="1"'
            . (!empty($filter['open']) ? ' checked' : '') . '> Open seats only</label>' . "\n";
        $out .= '<button type="submit">Filter the wall</button>' . "\n";
        $out .= '</form>' . "\n";

        return $out;
    }

    public function wall(Sample $sample, array $filter): string
    {
        $TITLE = 'Kantta, the wall of fractional executive seats';
        $LEDE = 'Kantta lists the seats companies open to executives who give a share of their week.';
        $WHAT = 'A company posts a seat with a role, a day rate, the hours a day it takes and the term it lasts. A term runs six months at the shortest and a seat asks for two hours a day at the lowest. An executive gives a fixed number of hours a day across every seat they hold, so the schedule manager counts the engagements of one executive against that ceiling. The wall filters on role, on hours a day, on term length and on sector.';

        $seats = $sample->wall()->find($filter, Sample::TODAY);
        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $out .= $this->filterForm($sample, $filter);
        $out .= '<p class="stage">Seats on the wall: ' . $sample->seats() . '. Open today: ' . $sample->openSeats()
            . '. Matching this filter: ' . count($seats) . '.</p>' . "\n";

        $out .= '<ul class="wall">' . "\n";
        foreach ($seats as $seat) {
            $company = $sample->wall()->company($seat->company);
            $open = $seat->openOn(Sample::TODAY);
            $out .= '<li class="seat' . ($open ? '' : ' closed') . '">' . "\n";
            $out .= '<p class="seat-role">' . $this->h(strtoupper($seat->role)) . ' <span class="seat-open">'
                . ($open ? 'open' : 'closed ' . $this->h($seat->lastDay)) . '</span></p>' . "\n";
            $out .= '<h2>' . $this->h($seat->title) . ' at ' . $this->h($company->name) . '</h2>' . "\n";
            $out .= '<p class="seat-where">' . $this->h($company->town) . ' · ' . $this->h($company->sector) . '</p>' . "\n";
            $out .= '<p class="seat-terms">' . $seat->hoursADay . ' hours a day · ' . $seat->months() . ' months · '
                . $this->h($seat->firstDay) . ' to ' . $this->h($seat->lastDay) . ' · '
                . $this->money($seat->rateCents) . ' a day</p>' . "\n";
            $out .= '</li>' . "\n";
        }
        if ($seats === []) {
            $out .= '<li class="seat empty">No seat answers this filter. Widen one box and ask again.</li>' . "\n";
        }
        $out .= '</ul>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    public function schedule(Sample $sample, string $monday): string
    {
        $schedule = $sample->schedule();
        $executive = $schedule->executive;
        $TITLE = 'The schedule manager';
        $out = $this->header($TITLE) . '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($executive->name . ' holds ' . count($schedule->engagements())
            . ' engagements and gives ' . $executive->hoursADay . ' hours a day.') . '</p>' . "\n";
        $out .= '<p class="stage">Week from ' . $this->h($monday) . '. Engagements: ' . count($schedule->engagements())
            . '. Days past the ceiling this week: ' . $schedule->overDays($monday, (new \DateTimeImmutable($monday))->modify('+6 day')->format('Y-m-d')) . '.</p>' . "\n";

        $out .= '<table class="week"><thead><tr><th>Day</th><th>Hours owed</th><th>State</th></tr></thead><tbody>' . "\n";
        foreach ($schedule->week($monday) as $day) {
            $state = $day['hours'] === 0 ? 'free' : ($day['over'] ? 'past the ceiling' : 'within the ceiling');
            $out .= '<tr' . ($day['over'] ? ' class="over"' : '') . '><td>' . $this->h($day['day']) . '</td><td>'
                . $day['hours'] . '</td><td>' . $this->h($state) . '</td></tr>' . "\n";
        }
        $out .= '</tbody></table>' . "\n";

        $out .= '<h2>Engagements</h2>' . "\n";
        $out .= '<table class="engagements"><thead><tr><th>Seat</th><th>Company</th><th>Hours a day</th><th>Term</th><th>Day rate</th></tr></thead><tbody>' . "\n";
        foreach ($schedule->engagements() as $engagement) {
            $company = $sample->wall()->company($engagement->company);
            $out .= '<tr><td>' . $this->h($engagement->seat->title) . '</td><td>' . $this->h($company->name)
                . '</td><td>' . $engagement->hoursADay() . '</td><td>' . $this->h($engagement->firstDay())
                . ' to ' . $this->h($engagement->lastDay()) . '</td><td>' . $this->money($engagement->seat->rateCents)
                . '</td></tr>' . "\n";
        }
        $out .= '</tbody></table>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That path does not exist here.</p>\n" . $this->footer();
    }
}
