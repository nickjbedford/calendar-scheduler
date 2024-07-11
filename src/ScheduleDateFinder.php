<?php
	namespace YetAnother;
	
	use Carbon\Carbon;
	use DateTimeInterface;
	
	/**
	 * Finds the next scheduled date based on a reference date and a set of
	 * public holidays and working days of the week and/or calendar days of the month.
	 * The week days and days of the month will be intersected to find the next
	 * available date from the reference date.
	 * For example, a business is open 4 days a week and wants to ship orders every 5th, 15th and
	 * 25th day of the month. This class is capable of finding the next available shipping date.
	 * @property Weekday[] $workdays The working days of the week to include in the schedule.
	 * @property int[] $daysOfMonth The calendar days of the month to include in the schedule.
	 * @property int[]|null $holidays The public holidays to exclude from the schedule (these must be in the format "YYYY-MM-DD").
	 * If null, ScheduleDateFinder::$defaultHolidays is used.
	 * @property DayOfMonthScheduleMethod $dayOfMonthScheduleMethod The method to use when finding
	 * the next or previous most appropriate date in the schedule from a day-of-month schedule.
	 */
	class ScheduleDateFinder
	{
		/**
		 * @var string[] The default public holidays to exclude from the schedule.
		 */
		static array $defaultHolidays = [];
		
		const array AllMonths = [
			1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12
		];
		
		const array AllDaysOfMonth = [
			1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
			11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
			21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
		];
		
		/**
		 * @param Weekday[] $workdays The working days of the week to include in the schedule.
		 * @param string[]|null $holidays The public holidays to exclude from the schedule (these must be in the format "YYYY-MM-DD").
		 * @param array $daysOfMonth The calendar days of the month to include in the schedule.
		 * @param array $monthsOfYear The months of the year to include in the schedule.
		 * @param DayOfMonthScheduleMethod $dayOfMonthScheduleMethod
		 */
		public function __construct(public array                    $workdays = Weekday::Weekdays,
		                            public ?array                   $holidays = null,
		                            public array                    $daysOfMonth = self::AllDaysOfMonth,
									public array                    $monthsOfYear = self::AllMonths,
		                            public DayOfMonthScheduleMethod $dayOfMonthScheduleMethod = DayOfMonthScheduleMethod::NextAvailableWorkday)
		{
			$this->holidays ??= self::$defaultHolidays;
		}
		
		public function next(string|int|Carbon|DateTimeInterface|null $from = null): Carbon
		{
			$from = Carbon::parse($from);
		}
	}
