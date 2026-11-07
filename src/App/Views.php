<?php

declare(strict_types=1);

namespace Kantta\App;

use Kantta\Home\Job;
use Kantta\Home\Sample;

/**
 * The HTML. Every string reaching the page goes through h(), so a job title a
 * worker typed can carry an angle bracket without breaking the page.
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

    public function front(Sample $sample): string
    {
        $TITLE = 'Kantta, one queue that answers in order';
        $LEDE = 'Kantta keeps jobs in a priority queue and gives workers the next job in a fixed order.';
        $WHAT = 'Each job has a handle, title, priority, filed day and due day. The queue sorts jobs by priority, then by the earliest filed day, then by handle. This gives every reader the same order. A job can enter the queue and run only once. The due day cannot come before the filed day, and a worker cannot finish a job before it was filed. A job becomes late when its due day passes without completion.';

        $queue = $sample->queue();
        $out = $this->header($TITLE);
        $out .= '<h1>' . $this->h($TITLE) . '</h1>' . "\n";
        $out .= '<p class="lede">' . $this->h($LEDE) . '</p>' . "\n";
        $out .= '<p>' . $this->h($WHAT) . '</p>' . "\n";
        $next = $queue->next();
        $out .= '<p class="stage">Jobs: ' . $sample->size() . '. Waiting: ' . $sample->waiting() . '. Late: ' . $sample->late()
            . '. Next: ' . ($next === null ? 'nothing waiting' : $this->h($next->handle)) . '.</p>' . "\n";

        $out .= '<h2>Queue order</h2>' . "\n";
        $out .= '<table class="queue"><thead><tr><th>Job</th><th>Priority</th><th>Filed</th><th>Due</th><th>Done</th></tr></thead><tbody>' . "\n";
        foreach ($queue->waiting() as $job) {
            $out .= '<tr' . ($job->late(Sample::TODAY) ? ' class="late"' : '') . '><td>' . $this->h($job->handle) . ' ' . $this->h($job->title)
                . '</td><td>' . $this->h($job->priority) . '</td><td>' . $this->h($job->filedDay)
                . '</td><td>' . $this->h($job->dueDay) . '</td><td>waiting</td></tr>' . "\n";
        }
        foreach ($queue->done() as $handle => $day) {
            $job = $queue->job($handle);
            $out .= '<tr class="done"><td>' . $this->h($job->handle) . ' ' . $this->h($job->title)
                . '</td><td>' . $this->h($job->priority) . '</td><td>' . $this->h($job->filedDay)
                . '</td><td>' . $this->h($job->dueDay) . '</td><td>' . $this->h($day) . '</td></tr>' . "\n";
        }
        $out .= '</tbody></table>' . "\n";

        $out .= '<h2>What the queue does</h2>' . "\n";
        $out .= '<ol class="steps">' . "\n";
        foreach ([
            ['Job is filed', 'A job enters the queue with its details and due day.'],
            ['Queue sorts jobs', 'Higher priority comes first, followed by filed day and handle.'],
            ['Queue answers next', 'The queue identifies the first waiting job in order.'],
            ['Worker runs job', 'A worker finishes a job once and records its done day.'],
            ['Queue marks late ones', 'The queue marks unfinished jobs whose due day has passed.'],
        ] as [$step, $line]) {
            $out .= '<li><strong>' . $this->h($step) . '</strong> ' . $this->h($line) . '</li>' . "\n";
        }
        $out .= '</ol>' . "\n";
        $out .= $this->footer();

        return $out;
    }

    public function missing(): string
    {
        return $this->header('Not found') . "<h1>Not found</h1>\n<p>That path does not exist here.</p>\n" . $this->footer();
    }
}
