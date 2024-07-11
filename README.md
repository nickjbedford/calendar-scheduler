# CalendarTimer (Yet Another)

Provides day, month and year-based calendar schedule
calculation. For each type of timer, a reference
date along with an interval is used to calculate past
and future dates, from any date including now.

For example, to calculate a fortnightly schedule of dates
from Wednesday 6th January 2021, you can use the following
code:

```php
use \YetAnother\DayTimer;

$scheduler = new DayTimer('2021-01-04', 14);

// get next 3 dates from 1st February 2021
$dates = $scheduler->getDates(3, '2021-02-01'); 

// $dates = [
//   '2021-02-03'
//   '2021-02-17'
//   '2021-03-03'
// ]
```

Alternatively you can use the provided helper methods to create 
calendar timer. These create instances of the `DayTimer`, `MonthTimer`
or `YearTimer` subclasses depending on the helper.

```php
use \YetAnother\CalendarTimer;

$date = '2020-01-04';

$weekly = CalendarTimer::weekly($date);
$fortnightly = CalendarTimer::fortnightly($date);
$monthly = CalendarTimer::monthly($date);
$biannual = CalendarTimer::sixMonthly($date);
$yearly = CalendarTimer::yearly($date);
```

## ScheduleDateFinder

The `ScheduleDateFinder` class can be used to find the next or previous
date in a configuration for "available" workdays across a calendar year
while also specifying holiday dates to exclude.

For example, a business may only want to ship orders on Mondays, Wednesdays
and Fridays but also prefer to only ship on the 5th, 15th and 25th days of
each month. This class helps you discover the next most appropriate shipping
date based on a reference date, allowing for an optional "not-before" date
when using the `YetAnother\DayOfMonthSchedule::ClosestWorkday` option.

### Examples

```php
use YetAnother\ScheduleDateFinder;
use YetAnother\Weekday;
use YetAnother\DayOfMonthScheduleMethod;

$schedule = new ScheduleDateFinder(
    workdays: [ Weekday::Monday, Weekday::Wednesday, Weekday::Friday ],
    holidays: [ '2024-06-17' ],
    availabilityCalendar: ScheduleDateFinder::createAvailabilityCalendar([ 5, 15, 25 ]),
    dayOfMonthScheduleMethod: DayOfMonthScheduleMethod::ClosestWorkday);

/**
 * '2024-06-05' Tuesday because 5th is a Wednesday so the next
 * available calendar date.
 */
$schedule->nextAsString(from: '2024-06-04');

/**
 * '2024-06-14' Friday because 15th is a Saturday so the "closest workday"
 * (not before 6th June) is the Friday before the 15th.
 */
$schedule->nextAsString(from: '2024-06-06');

/**
 * '2024-06-19' Wednesday because the 17th (Monday) is a holiday. The 15th is
 * a Saturday so the next available workday is the Wednesday after the 17th. 
 */
$schedule->nextAsString(from: '2024-06-15');

/**
 * '2024-06-24' Monday because the 25th (Tuesday) is not a workday so the closest
 * workday (not before 17th June) is the Monday before the 25th.
 */
$schedule->nextAsString(from: '2024-06-17');

/**
 * '2024-06-26' Wednesday because the 25th (Tuesday) is not a workday
 * so the closest workday (not before 25th June) is the Wednesday after the 25th.
 */
$schedule->nextAsString(from: '2024-06-25');
```
