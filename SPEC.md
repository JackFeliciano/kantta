# Kantta

Kantta keeps a queue of jobs and runs them in the order they were filed, one at a
time. Each job carries the work it needs and the day it has to be done by, and the
queue tells you what is next, what is late and what is done.

## Words

- **job**: one piece of work. It carries a handle, a title, a priority, a filed day
  and a due day.
- **priority**: `high`, `normal` or `low`. A higher priority goes before a lower one
  filed on the same day.
- **filed day**: the day the job entered the queue. It never changes.
- **due day**: the last day the job may be done.
- **queue**: the jobs waiting, in the order they will run.
- **done**: a job that a worker finished, with the day it finished.

## What the queue enforces

- A job carries a handle, a title and two days, and none of them is empty.
- A job joins the queue once. A second job with the same handle lands nowhere.
- The queue runs a job once. A job already done cannot be run again.
- A job may not be filed with a due day before its filed day.
- A done day before the filed day lands nowhere.

## Order

1. Higher priority before lower.
2. Among jobs of one priority, the earlier filed day first.
3. Among jobs filed on the same day at the same priority, the handle decides, so
   the order never depends on the map.

## What the queue answers

The answer names the jobs in queue order, the job done next, the jobs still
waiting, the jobs done and the jobs late. A job is late when its due day has passed
and no worker has finished it.

## Open in version 1

- Whether a priority can change after a job is filed. Version 1 says no.
- Whether a job can be cancelled. Version 1 says no.
- Whether the queue holds a worker claim, so two workers never take one job. The
  demo runs one worker.
