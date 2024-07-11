<?php
	namespace YetAnother;
	
	use Carbon\Carbon;
	use Carbon\CarbonInterface;
	use Carbon\Exceptions\InvalidFormatException;
	use DateTimeInterface;
	
	/**
	 * Finds the next scheduled date based on a reference date and a set of
	 * public holidays and working days of the week and/or calendar days of the month.
	 * The week days and days of the month will be intersected to find the next
	 * available date from the reference date.
	 * For example, a business is open 4 days a week and wants to ship orders every 5th, 15th and
	 * 25th day of the month. This class is capable of finding the next available shipping date.
	 * @property Weekday[] $workdays The working days of the week to include in the schedule.
	 * @property string[]|null $holidays The public holidays to exclude from the schedule (these must be in the format "YYYY-MM-DD").
	 * If null, ScheduleDateFinder::$defaultHolidays is used.
	 * @property int[] $daysOfMonth The calendar days of the month to include in the schedule.
	 * @property int[] $monthsOfYear The months of the year to include in the schedule.
	 * @property DayOfMonthScheduleMethod $dayOfMonthScheduleMethod The method to use when finding
	 * the next or previous most appropriate date in the schedule from a day-of-month schedule.
	 */
	class ScheduleDateFinder
	{
		/**
		 * @var string[] The default public holidays to exclude from the schedule.
		 */
		static array $defaultHolidays = [];
		
		/**
		 * @var int The maximum number of iterations to prevent infinite loops.
		 */
		static int $loopLimit = 10000;
		
		const array EveryMonth = [
			CarbonInterface::JANUARY,
			CarbonInterface::FEBRUARY,
			CarbonInterface::MARCH,
			CarbonInterface::APRIL,
			CarbonInterface::MAY,
			CarbonInterface::JUNE,
			CarbonInterface::JULY,
			CarbonInterface::AUGUST,
			CarbonInterface::SEPTEMBER,
			CarbonInterface::OCTOBER,
			CarbonInterface::NOVEMBER,
			CarbonInterface::DECEMBER
		];
		
		const array EveryDay = [
			1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
			11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
			21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
		];
		
		const array AllYear = [
			CarbonInterface::JANUARY => self::EveryDay,
			CarbonInterface::FEBRUARY => self::EveryDay,
			CarbonInterface::MARCH => self::EveryDay,
			CarbonInterface::APRIL => self::EveryDay,
			CarbonInterface::MAY => self::EveryDay,
			CarbonInterface::JUNE => self::EveryDay,
			CarbonInterface::JULY => self::EveryDay,
			CarbonInterface::AUGUST => self::EveryDay,
			CarbonInterface::SEPTEMBER => self::EveryDay,
			CarbonInterface::OCTOBER => self::EveryDay,
			CarbonInterface::NOVEMBER => self::EveryDay,
			CarbonInterface::DECEMBER => self::EveryDay
		];
		
		public readonly array $holidays;
		
		/**
		 * Creates a calendar availability table for each month of the year with the same day-of-month dates.
		 * @param int[] $daysOfMonth The days of the month to make available.
		 * @param int[] $monthsOfYear The months of the year to make available.
		 * @return array
		 */
		public static function createAvailabilityCalendar(array $daysOfMonth = self::EveryDay, array $monthsOfYear = self::EveryMonth): array
		{
			$table = [];
			foreach($monthsOfYear as $month)
				$table[$month] = $daysOfMonth;
			return $table;
		}
		
		/**
		 * @param Weekday[] $workdays The working days of the week to include in the schedule.
		 * @param string[]|null $holidays The public holidays to exclude from the schedule (these must be in the format "YYYY-MM-DD").
		 * @param array $calendarAvailability The calendar days of each month of the year to make available in the schedule.
		 * @param DayOfMonthScheduleMethod $dayOfMonthScheduleMethod
		 */
		public function __construct(public readonly array           $workdays = Weekday::Weekdays,
		                            ?array                          $holidays = null,
		                            public readonly array           $calendarAvailability = self::AllYear,
		                            public DayOfMonthScheduleMethod $dayOfMonthScheduleMethod = DayOfMonthScheduleMethod::NextWorkday)
		{
			$this->holidays = $holidays ?? self::$defaultHolidays;
		}
		
		/**
		 * Finds the next scheduled date based on the reference date, returning a string representation.
		 * @param string|int|Carbon|DateTimeInterface|null $from The reference date to calculate from (inclusive).
		 * @return Carbon
		 * @throws InvalidFormatException
		 */
		public function nextAsString(string|int|Carbon|DateTimeInterface|null $from = null): string
		{
			return $this->next($from)->format('Y-m-d');
		}
		
		/**
		 * Finds the next scheduled date based on the reference date.
		 * @param string|int|Carbon|DateTimeInterface|null $from The reference date to calculate from (inclusive).
		 * @return Carbon
		 * @throws InvalidFormatException
		 */
		public function next(string|int|Carbon|DateTimeInterface|null $from = null): Carbon
		{
			$from = Carbon::parse($from);
			$from->setTime(0, 0);
			$notBefore = $from->timestamp;
			
			$i = self::$loopLimit;
			while ($i--)
			{
				$date = $from->format('Y-m-d');
				$isHoliday = in_array($date, $this->holidays, true);
				$isWorkday = in_array($from->dayOfWeek, $this->workdays);
				
				if (!$isHoliday && $isWorkday)
					return $from;
				
				switch($this->dayOfMonthScheduleMethod)
				{
					case DayOfMonthScheduleMethod::NextAvailableDate:
						$from->addDay();
						break;
						
					case DayOfMonthScheduleMethod::NextWorkday:
						do
						{
							$from->addDay();
						} while(!in_array($from->dayOfWeek, $this->workdays));
						continue 2;
						
					case DayOfMonthScheduleMethod::PreviousWorkdayIfAvailable:
						$before = $from->timestamp;
						do
						{
							$from->subDay();
							
							$isHoliday = in_array($from->format('Y-m-d'), $this->holidays, true);
							$isWorkday = in_array($from->dayOfWeek, $this->workdays);
						}
						while ($notBefore <= $from->timestamp && ($isHoliday || !$isWorkday));
						
						if ($from->timestamp < $notBefore)
						{
							$from->setTimestamp($before);
							$from->addDay();
							$notBefore = $from->timestamp;
							break;
						}
						
						return $from;
				}
				
				$this->moveToNextCalendarDate($from);
			}
			return $from;
		}
		
		private function moveToNextCalendarDate(Carbon $from): void
		{
			while (!in_array($from->dayOfMonth, $this->calendarAvailability[$from->month]))
				$from = $from->addDay();
		}
	}
