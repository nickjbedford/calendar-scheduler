# CalendarScheduler (Yet Another)

Provides day, month and year-based calendar schedule
calculation. For each type of scheduler, a reference
date along with an interval is used to calculate past
and future dates, from any date including now.

For example, to calculate a fortnightly schedule of dates
from Wednesday 6th January 2021, you can use the following
code:

```php
use \YetAnother\DayCalendarScheduler;

$scheduler = new DayCalendarScheduler('2021-01-04', 14);

// get next 3 dates from 1st February 2021
$dates = $scheduler->getDates(3, '2021-02-01'); 

// $dates = [ '2021-02-03', '2021-02-17', '2021-03-03' ]
```

Alternatively you can use the provided helper methods to create 
schedulers:

```php
use \YetAnother\DayCalendarScheduler;

$weekly = DayCalendarScheduler::weekly('2020-01-04');
$fortnightly = DayCalendarScheduler::fortnightly('2020-01-04');
```
