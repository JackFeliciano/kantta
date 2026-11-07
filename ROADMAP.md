# Kantta roadmap

Kantta keeps a queue of jobs and answers them in a fixed order. Version 1 holds one
queue in one process and runs one worker, and the list below is what a shop floor
and a second worker need.

## Open in the model

- A priority is fixed at filing. A job that turns urgent keeps its place.
- A job cannot be cancelled, so a job filed by mistake runs or sits late.
- Lateness is measured against a day the caller passes in. Nothing in the model
  knows a time zone, so two readers in two places can disagree about a late job.

## Owed to a worker

- The queue holds no claim, so two workers polling `next()` take the same job.
- A job carries no owner and no note, so a worker has to find the work elsewhere.
- A failed job leaves no mark. It stays waiting with no record of the attempt.

## Owed to a reader

- The page shows every job and holds no filters. A reader cannot ask for one
  priority or one due day.
- The done jobs sit in a map, so their page order depends on the map and not on
  the day they finished.
- The queue holds no history, so a job that ran twice under two workers shows one
  done day and nothing else.

## Checks owed

- Two workers asking for the next job at the same moment.
- A queue where every job carries the same handle in different case.
- A queue of ten thousand jobs, to see whether the sort holds.
