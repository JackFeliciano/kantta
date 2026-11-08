# Kantta

Kantta is a wall of fractional executive seats where companies post roles and executives take defined daily commitments. Its wall helps executives find seats, while its schedule manager counts daily hours across engagements and identifies overlapping commitments.

## What a seat carries

Every seat carries a handle, the company, a role, a title, a day rate in cents, hours a day, a first day and a last day.

The allowed roles are ceo, cfo, coo, cto, cmo, chro, clo and cso. A seat runs for at least six months. Its daily commitment ranges from two to eight hours.

Each company carries a handle, a name, a town and a sector. Each executive carries a handle, a name, a title and the hours a day they can give across all their seats.

An executive can take one seat at a company. Kantta refuses a second engagement with the same company. This keeps each executive's company commitments distinct.

A seat closes after its last day. The wall keeps closed seats visible and marks them as closed, rather than treating them as available.

## The wall and its filters

The wall lives at /. Companies post seats there, and executives can browse the available roles and commitments.

Five filters help narrow the wall:

Role selects one of the allowed executive roles.
Hours a day sets a ceiling for the daily commitment.
Term in months sets a minimum term.
Sector narrows the companies by sector.
Open seats only hides seats that have closed.

The hours filter acts as a ceiling, while the term filter acts as a floor. This lets an executive narrow the wall by the amount of time they can give and the minimum commitment they want to take.

## The schedule manager

The schedule manager lives at /schedule. It takes the engagements of one executive, counts the hours owed on each day and identifies days that exceed the executive's available hours.

The manager makes overlapping commitments visible at a daily level. It compares the total hours owed with the hours the executive can give across every seat. When a day exceeds that limit, the schedule names it so the executive can see where commitments conflict.

## Open in version 1

A seat holds one executive at a time. The schedule counts every day the same way. It does not apply different daily weights or distinguish between weekdays and weekends.

These boundaries describe the current model, rather than features that Kantta already supports.

## Running it here

Kantta uses PHP 8.3. It has no dependencies and does not require Composer.

Run the local development server from the project directory:

```
php -S 127.0.0.1:8109 -t showroom showroom/index.php
```

The application uses port 8109. Set KANTTA_BASE to the path prefix where the site sits under a larger URL. Use the root path when the application sits at the domain root.

The health route answers JSON for a probe:

```
{
  "ok": true,
  "seats": 5,
  "open": 4,
  "engagements": 3,
  "hours_monday": 3
}
```

The application exposes three routes. The wall and its filters sit at /, the schedule manager at /schedule, and the JSON status at /health.

## The demo

The sample contains four companies, five seats and one executive with three engagements. One seat has already closed as of today, 2026-11-09. The wall still displays that seat with its closed status.

The schedule starts with the week of 2026-11-09. In the sample, the executive owes three hours on Monday and five hours from the following Sunday. These commitments give the schedule manager concrete overlapping engagements to count and display.

The sample provides a small dataset for exploring the wall and schedule without setting up additional records. Its dates and commitments make the closed-seat and daily-hours behaviour visible.

## Layout

The source code separates the domain model from the application entry points and views.

src/Home/ contains Seat, Company, Executive, Engagement, Schedule, Wall and Sample. These classes hold the project model, the sample data, the wall behaviour and the schedule calculations.

src/App/ contains Front, Router and Views. This directory handles the application entry point, route selection and rendered views.

## Checks

Kantta includes 45 hand-written checks in a small runner. The checks exercise the project logic without a testing framework or any dependency.

## Licence

Kantta uses the Mozilla Public License 2.0.
