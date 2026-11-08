# Kantta

Kantta is a wall of fractional executive seats. A company posts a seat with the term it
lasts and the hours a day it takes, an executive takes seats, and the schedule manager
tells the executive where two seats run at once and where a day runs out of hours.

## Words

- **company**: the party hiring. It carries a handle, a name, a town and a sector.
- **seat**: one open position. It carries a handle, the company, a role, a day rate in
  cents, the hours a day it takes, the first day and the last day.
- **term**: the days from the first day to the last day, both of them counted. A seat
  runs six months at the shortest.
- **hours a day**: what the seat takes from the executive, between two and eight.
- **executive**: the party taking seats. It carries a handle, a name, a title and the
  hours a day it can give across every seat.
- **engagement**: one executive inside one seat, from its start day to its end day.

## What the wall enforces

- A seat carries a role, a rate, a first day and a last day, and none of them is empty.
- A seat runs six months at the shortest, so a shorter term lands nowhere.
- A seat takes two hours a day at the lowest and eight at the highest.
- A seat closes at its last day. The wall shows it closed and a company cannot extend it.
- One executive takes one seat at a company. A second engagement at the same company
  lands nowhere.
- A role is one of `ceo`, `cfo`, `coo`, `cto`, `cmo`, `chro`, `clo`, or `cso`. An unknown
  role lands nowhere.

## What the schedule answers

An engagement takes its hours a day across every day of its term. Two engagements of one
executive clash when their terms overlap and the hours together pass what the executive
can give. The manager answers, for each week, the hours the executive owes, and names
the days where the engagements run past the ceiling.

## Filters on the wall

- role
- hours a day, taken as a ceiling
- term in months, taken as a floor
- sector

## Open in version 1

- A seat holds one executive at a time.
- Nothing in the model moves an engagement's start day once an executive takes it.
- The schedule counts days, so a week holds seven and every day takes the same hours.
