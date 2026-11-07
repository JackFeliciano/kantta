# Kantta

## What the project is

Kantta is a PHP 8.3 program that keeps jobs in a queue and gives workers jobs in a fixed order. Each job carries a handle, title, priority, filed day and due day.

Jobs have 3 priority levels: high, normal and low. The queue sorts jobs by higher priority first, then by earlier filed day, then by handle. This gives every reader the same order.

A job can enter the queue only once. A second job with the same handle is refused. A job can run once, and a completed job cannot run again. The due day cannot come before the filed day. A done day before the filed day is also refused.

A job becomes late when its due day has passed and no worker has finished it.

The demo contains 4 jobs. Three remain waiting and 1 is late. The demo measures lateness against the fixed day 2026-10-12.

The program uses plain PHP 8.3. It requires no Composer and no packages.

## How to run it

Run the test suite from the repository root:

```bash
php tests/run.php
```

Start the demo with PHP's built-in server:

```bash
php -S 127.0.0.1:8109 -t showroom showroom/index.php
```

The application uses `/` as its default URL prefix. If a proxy passes a different prefix, set `KANTTA_BASE` to that value.

## What the routes answer

The demo exposes 2 routes.

`/` renders the queue in its defined order and shows the done day for each job.

`/health` answers with the total job count, waiting count and late count.

The home route displays all 4 sample jobs in queue order. Visitors can see which job has finished and which jobs remain waiting.

The health route provides counts for the current queue. Its late count uses the fixed day 2026-10-12 in the demonstration.

## How the layout is arranged

The repository separates job and queue logic from request handling, presentation, tests and deployment files.

src/Home/ holds Job, Queue and Sample.
src/App/ holds the router, front controller and HTML.
showroom/ holds the entry file and stylesheet.
tests/ holds the test suite.
deploy/ holds the systemd unit and nginx location block.
SPEC.md describes the queue rules.

The specification states the project vocabulary, the ordering rule, what the queue refuses and what version 1 leaves open.

The src/Home/ directory contains the job and queue logic. The src/App/ directory handles requests and page output. The remaining directories support the demo, testing and deployment.

## The state of the suite

The repository includes a hand-written test runner with 19 checks.

Run all checks with:

```bash
php tests/run.php
```

The runner needs no package installation and no test framework. It checks the program from the command line.

## Licence

The repository uses the Mozilla Public License 2.0 (MPL 2.0). You may use, modify and distribute the code. You must publish changes to files covered by the licence.

