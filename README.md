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
