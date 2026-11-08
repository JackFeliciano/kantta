<?php

declare(strict_types=1);

namespace Kantta\App;

use Kantta\Home\Sample;

/**
 * The front controller. Routes, then renders. The health route answers from the same
 * sample the page renders, so a probe and the page cannot disagree about the wall.
 */
final class Front
{
    public function __construct(private readonly string $base)
    {
    }

    /** @return array{role: string, hours: int, months: int, sector: string, open: bool} */
    private function filter(): array
    {
        $query = $_GET ?? [];

        return [
            'role' => is_string($query['role'] ?? null) ? $query['role'] : '',
            'hours' => (int) ($query['hours'] ?? 0),
            'months' => (int) ($query['months'] ?? 0),
            'sector' => is_string($query['sector'] ?? null) ? $query['sector'] : '',
            'open' => ($query['open'] ?? '') === '1',
        ];
    }

    public function run(): void
    {
        $router = new Router($this->base);
        $views = new Views($this->base);
        $sample = new Sample();

        $router->get('/', function () use ($views, $sample): array {
            return [200, ['html' => $views->wall($sample, $this->filter())]];
        });
        $router->get('/schedule', static fn (): array => [200, [
            'html' => $views->schedule($sample, Sample::MONDAY),
        ]]);
        $router->get('/health', static fn (): array => [200, ['json' => [
            'ok' => true,
            'seats' => $sample->seats(),
            'open' => $sample->openSeats(),
            'engagements' => $sample->engagements(),
            'hours_monday' => $sample->hoursOnMonday(),
        ]]]);

        [$status, $body] = $router->handle(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
        );

        http_response_code($status);
        if (isset($body['json'])) {
            header('Content-Type: application/json');
            echo json_encode($body['json'], JSON_UNESCAPED_SLASHES), "\n";

            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        if (isset($body['html'])) {
            echo $body['html'];

            return;
        }
        echo $views->missing();
    }
}
