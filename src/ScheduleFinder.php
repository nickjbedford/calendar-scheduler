<?php
	namespace YetAnother;
	
	use Carbon\Carbon;
	use Carbon\CarbonInterface;
	use DateTimeInterface;
	use Exception;
	
	/**
	 * Finds the next most appropriate date based on a reference date and a set of
	 * public holidays and working days of the week and/or calendar days of the month.
	 * For example, a business is open 4 days a week and wants to ship orders every 5th, 15th and
	 * 25th day of the month while preferring certain weekdays if possible.
	 * This class is capable of finding the most appropriate shipping date.
	 * @property Weekday[] $standardWorkdays The standard working days of the week to fallback to.
	 * @property Weekday[]|null $preferredWorkdays Optional. The preferred working days of the week to choose.
	 * @property int[][] $preferredCalendar The preferred dates in the calendar year to choose first.
	 * @property string[]|null $excludedDates The dates to exclude from the schedule such as public holidays.
	 * These must be in the format "YYYY-MM-DD". If null, ScheduleDateFinder::$defaultExcludedDates is used.
	 * @property ScheduleAlgorithm $algorithm The algorithm to use when finding the next date.
	 */
	class ScheduleFinder
	{
		/** @var string[] $defaultExcludedHolidays The default dates to exclude from the schedule. */
		static array $defaultExcludedHolidays = [];
		
		/** @var int The maximum number of iterations to prevent infinite loops. */
		static int $iterationLimit = 10000;
		
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
		
		/** @var string[] The public holidays to exclude from the schedule (formatted 'YYYY-MM-DD'). */
		public readonly array $excludedDates;
		
		/** @var Weekday[]|null The preferred working days every week. */
		public readonly ?array $preferredWorkdays;
		
		/**
		 * Creates a new schedule finder.
		 * @param Weekday[] $standardWorkdays The standard work days every week in the schedule.
		 * @param Weekday[]|null $preferredWorkdays The preferred working days every week.
		 * @param array|null $preferredCalendar The calendar days of each month of the year to prefer.
		 * @param string[]|null $excludedDates The public holidays to exclude from the schedule (these must be in the format "YYYY-MM-DD").
		 * @param ScheduleAlgorithm $algorithm The algorithm for determining the most appropriate date.
		 * @throws Exception Thrown when no workdays are specified.
		 */
		public function __construct(
			public readonly array    $standardWorkdays = Weekday::MondayToFriday,
			?array                   $preferredWorkdays = null,
			public readonly ?array   $preferredCalendar = null,
			?array                   $excludedDates = null,
			public ScheduleAlgorithm $algorithm = ScheduleAlgorithm::Default)
		{
			$this->excludedDates = $excludedDates ?? self::$defaultExcludedHolidays;
			$preferredWorkdays = !empty($preferredWorkdays) ? array_intersect($preferredWorkdays, $this->standardWorkdays) : null;
			$this->preferredWorkdays = !empty($preferredWorkdays) ? $preferredWorkdays : null;
			
			if (empty($this->standardWorkdays))
				throw new Exception('At least one standard workday must be specified.');
			
			if ($this->preferredCalendar)
			{
				$any = false;
				foreach($this->preferredCalendar as $month=>$days)
				{
					if (!empty($days) && $month >= 1 && $month <= 12)
					{
						$any = true;
						break;
					}
				}
				
				if (!$any)
					throw new Exception('At least one preferred calendar date must be specified, otherwise pass null.');
			}
		}
		
		
		/**
		 * Creates a preferred calendar for each month of the year with the same day-of-month dates.
		 * @param int[] $daysOfMonth The days of the month to prefer.
		 * @param int[] $monthsOfYear The months of the year to fill with prefered dates.
		 * @return array
		 */
		public static function createPreferredCalendar(array $daysOfMonth = self::EveryDay,
		                                               array $monthsOfYear = self::EveryMonth): array
		{
			$table = [];
			foreach($monthsOfYear as $month)
				$table[$month] = $daysOfMonth;
			return $table;
		}
		
		/**
		 * Finds the closest preferred workday based on the reference date without
		 * considering the standard/available workdays and public holidays.
		 * This date may be before the reference date if there are elligible preferred workdays,
		 * but not before the $earliestDate date. This can be used to find the most appropriate
		 * preferred date to pass into {@see ScheduleFinder::next()} (inclusive).
		 * @param string|int|Carbon|DateTimeInterface|null $date
		 * @param string|int|Carbon|DateTimeInterface|null $earliestDate
		 * @return Carbon
		 * @throws Exception
		 */
		public function findClosestPreferredDate(
			string|int|Carbon|DateTimeInterface|null $date = null,
			string|int|Carbon|DateTimeInterface|null $earliestDate = null): Carbon
		{
			$date = Carbon::parse($date ?? 'today');
			$date->setTime(0, 0);
			
			$earliestDate = Carbon::parse($earliestDate ?? $date);
			$earliestDate->setTime(0, 0);
			$notBefore = $earliestDate->timestamp;
			$iterations = self::$iterationLimit;
			
			while ($iterations--)
			{
				if ($this->isPreferredDate($date))
					return $date;
				
				if ($this->preferredCalendar !== null)
				{
					$copy = $date->copy();
					$isPreferred = false;
					while($iterations-- && ($notBefore <= $copy->timestamp) && !($isPreferred = $this->isPreferredCalendarDate($copy)))
						$copy->subDay();
					
					if ($isPreferred)
						return $copy;
					
					$isPreferred = false;
					while($iterations-- && !($isPreferred = $this->isPreferredCalendarDate($date)))
						$date->addDay();
					
					if ($isPreferred)
						return $date;
				}
				
				if ($this->findNextMostAppropriateDate($date,
				                                       $notBefore,
				                                       ScheduleAlgorithm::ClosestPreferredWorkday,
				                                       $iterations,
				                                       false))
					return $date;
				
				$this->moveToNextPreferredDate($date, $iterations);
			}
			
			if ($iterations <= 0)
				throw new Exception('The iteration limit was reached while finding the next scheduled date. Please check the schedule configuration.');
			
			return $date;
		}
		
		/**
		 * Finds the closest schedule date to the reference date (preferring earlier dates).
		 * This may return to an earlier preferred calendar date if the reference date is not
		 * a preferred date, then it will attempt to return to a preferred workday earlier
		 * than the reference date but not before the $earliestDate. Otherwise, it will
		 * move to the next preferred calendar or workday.
		 *
		 * @param string|int|Carbon|DateTimeInterface|null $date The reference date to calculate from (inclusive).
		 * @param string|int|Carbon|DateTimeInterface|null $earliestDate Optional. The earliest date to allow to be selected.
		 * This may be before the reference date, allowing {@see ScheduleAlgorithm::ClosestPreferredThenClosestStandardWorkday}
		 * to function.
		 * @param string|int|Carbon|DateTimeInterface|null $latestDate Optional. The latest date to allow to be selected.
		 *   If the date is after this date, null is returned.
		 * @return Carbon|null
		 * @throws Exception
		 */
		public function closest(
			string|int|Carbon|DateTimeInterface|null $date = null,
			string|int|Carbon|DateTimeInterface|null $earliestDate = null,
			string|int|Carbon|DateTimeInterface|null $latestDate = null): ?Carbon
		{
			$date = Carbon::parse($date ?? 'today');
			$earliestDate = Carbon::parse($earliestDate ?? $date);
			$preferred = $this->findClosestPreferredDate($date, $earliestDate);
			
			$latestDateValue = $latestDate ? Carbon::parse($latestDate)->timestamp : PHP_INT_MAX;
			
			if (($isAvailable = $this->isAvailableDate($preferred)) && $preferred->timestamp <= $latestDateValue)
				return $preferred;
			
			$preferredEarliest = $earliestDate->timestamp;
			
			if (!$isAvailable &&
			    $preferred->isBefore($date) &&
			    $this->findNextMostAppropriateDate($preferred,
			                                       $preferredEarliest,
			                                       ScheduleAlgorithm::ClosestStandardWorkday,
			                                       self::$iterationLimit))
			{
				if ($preferred->timestamp <= $latestDateValue)
					return $preferred;
			}
			return $this->next($preferred, $earliestDate, $latestDate);
		}
		
		/**
		 * Finds the next scheduled date based on the reference date, returning the date
		 * as a string ("Y-m-d" format). This will not return to an earlier preferred
		 * calendar date if the reference date is not a preferred date, however it may
		 * return to a preferred workday earlier than the reference date but not before the $earliestDate.
		 *
		 * @param string|int|Carbon|DateTimeInterface|null $from The reference date to calculate from (inclusive).
		 * @param string|int|Carbon|DateTimeInterface|null $earliestDate Optional. The earliest date to allow to be selected.
		 * This may be before the reference date, allowing {@see ScheduleAlgorithm::ClosestPreferredThenClosestStandardWorkday}
		 * to function.
		 * @param string|int|Carbon|DateTimeInterface|null $latestDate Optional. The latest date to allow to be selected.
		 *  If the date is after this date, null is returned.
		 * @return string|null
		 * @throws Exception
		 */
		public function nextAsString(
			string|int|Carbon|DateTimeInterface|null $from = null,
			string|int|Carbon|DateTimeInterface|null $earliestDate = null,
			string|int|Carbon|DateTimeInterface|null $latestDate = null): ?string
		{
			return $this->next($from, $earliestDate, $latestDate)?->toDateString();
		}
		
		/**
		 * Finds the next scheduled date based on the reference date. This will not return to an earlier preferred
		 * calendar date if the reference date is not a preferred date, however it may return to a preferred workday
		 * earlier than the reference date but not before the $earliestDate.
		 *
		 * @param string|int|Carbon|DateTimeInterface|null $from The reference date to calculate from (inclusive).
		 * @param string|int|Carbon|DateTimeInterface|null $earliestDate Optional. The earliest date to allow to be selected.
		 *  This may be before the reference date, allowing {@see ScheduleAlgorithm::ClosestPreferredThenClosestStandardWorkday}
		 *  to function.
		 * @param string|int|Carbon|DateTimeInterface|null $latestDate Optional. The latest date to allow to be selected.
		 * If the date is after this date, null is returned.
		 * @return Carbon|null
		 * @throws Exception
		 */
		public function next(
			string|int|Carbon|DateTimeInterface|null $from = null,
			string|int|Carbon|DateTimeInterface|null $earliestDate = null,
			string|int|Carbon|DateTimeInterface|null $latestDate = null): ?Carbon
		{
			$date = Carbon::parse($from ?? 'today');
			$date->setTime(0, 0);
			
			$latestDate = $latestDate !== null ? Carbon::parse($latestDate->timestamp) : PHP_INT_MAX;
			$earliestDate = Carbon::parse($earliestDate ?? $date);
			$earliestDate->setTime(0, 0);
			$notBefore = $earliestDate->timestamp;
			$iterations = self::$iterationLimit;
			
			if (!$this->isPreferredDate($date))
				$this->moveToNextPreferredDate($date, $iterations);
			
			while ($iterations--)
			{
				$isAvailable = $this->isAvailableDate($date);
				$isPreferred = $this->isPreferredDate($date);
				
				if ($isAvailable && $isPreferred)
					return $date->timestamp <= $latestDate ? $date : null;
				
				if ($this->findNextMostAppropriateDate($date,
				                                       $notBefore,
				                                       $this->algorithm,
				                                       $iterations))
					return $date->timestamp <= $latestDate ? $date : null;
				
				$this->moveToNextPreferredDate($date, $iterations);
			}
			
			if ($iterations <= 0)
				throw new Exception('The iteration limit was reached while finding the next scheduled date. Please check the schedule configuration.');
			
			return $date->timestamp <= $latestDate ? $date : null;
		}
		
		/**
		 * @param Carbon $date
		 * @param int $earliestDate
		 * @param ScheduleAlgorithm $algorithm
		 * @param int $iterations
		 * @param bool $mustBeAvailable
		 * @return bool Return true if a date was found, otherwise false to continue to the next preferred date.
		 */
		private function findNextMostAppropriateDate(Carbon $date,
		                                             int &$earliestDate,
		                                             ScheduleAlgorithm $algorithm,
		                                             int &$iterations,
		                                             bool $mustBeAvailable = true): bool
		{
			if (!$iterations)
				return false;
			
			switch ($algorithm)
			{
				case ScheduleAlgorithm::ClosestPreferredThenClosestStandardWorkday:
					$isPreferred = false;
					$originalTimestamp = $date->timestamp;
					
					while ($iterations-- && !($isPreferred = $this->isPreferredWorkday($date)) && ($date->timestamp >= $earliestDate))
						$date->subDay();
					
					if ($isPreferred && $this->isAvailableDate($date, !$mustBeAvailable) && ($date->timestamp >= $earliestDate))
						return true;
					
					$date->setTimestamp($originalTimestamp);
					
					$isAvailable = false;
					while ($iterations-- && !($isAvailable = $this->isAvailableDate($date, !$mustBeAvailable)) && ($date->timestamp >= $earliestDate))
						$date->subDay();
					
					if ($date->timestamp >= $earliestDate && $isAvailable)
						return true;
					
					$date->setTimestamp($originalTimestamp);
					$date->addDay();
					$earliestDate = $date->timestamp;
					return $this->findNextMostAppropriateDate($date, $earliestDate, ScheduleAlgorithm::NextPreferredThenClosestStandardWorkday, $iterations);
				
				case ScheduleAlgorithm::ClosestPreferredWorkday:
					$originalTimestamp = $date->timestamp;
					$isPreferred = false;
					
					while ($iterations-- && !($isPreferred = $this->isPreferredWorkday($date)) && ($date->timestamp >= $earliestDate))
						$date->subDay();
					
					if ($isPreferred && ($date->timestamp >= $earliestDate) && $this->isAvailableDate($date, !$mustBeAvailable))
						return true;

					$date->setTimestamp($originalTimestamp);
					$isAvailable = false;
					while ($iterations-- && !($isAvailable = $this->isAvailableDate($date, !$mustBeAvailable)) && ($date->timestamp >= $earliestDate))
						$date->subDay();
					
					if ($isAvailable)
						return true;
					
					$date->setTimestamp($originalTimestamp);
					$date->addDay();
					$earliestDate = $date->timestamp;
					
					return $this->findNextMostAppropriateDate($date, $earliestDate, ScheduleAlgorithm::NextPreferredWorkday, $iterations);
				
				case ScheduleAlgorithm::ClosestStandardWorkday:
					$isAvailable = false;
					$originalTimestamp = $date->timestamp;
					while ($iterations-- && !($isAvailable = $this->isAvailableDate($date, !$mustBeAvailable)) && ($date->timestamp >= $earliestDate))
						$date->subDay();
					
					if ($date->timestamp < $earliestDate)
					{
						$date->setTimestamp($originalTimestamp);
						$date->addDay();
						$earliestDate = $date->timestamp;
						
						if ($this->findNextMostAppropriateDate($date, $earliestDate, ScheduleAlgorithm::NextStandardWorkday, $iterations))
							return true;
					}
					
					return $isAvailable;
					
				case ScheduleAlgorithm::NextPreferredThenClosestStandardWorkday:
					while ($iterations-- && !$this->isPreferredWorkday($date))
						$date->addDay();
					
					if ($this->isAvailableDate($date, !$mustBeAvailable))
						return true;
					
					if (!$iterations)
						return false;
					
					return $this->findNextMostAppropriateDate($date,
					                                          $earliestDate,
					                                          ScheduleAlgorithm::ClosestStandardWorkday,
					                                          $iterations,
					                                          $mustBeAvailable);
					
				case ScheduleAlgorithm::NextPreferredWorkday:
					$isAvailable = false;
					while ($iterations-- && !($isAvailable = $this->isAvailableDate($date, !$mustBeAvailable)) && !$this->isPreferredWorkday($date))
						$date->addDay();
					return $isAvailable;
				
				case ScheduleAlgorithm::NextStandardWorkday:
					$isAvailable = false;
					while ($iterations-- && !($isAvailable =$this->isAvailableDate($date, !$mustBeAvailable)))
						$date->addDay();
					return $isAvailable;
					
				case ScheduleAlgorithm::OnlyPreferredDates:
					$isAvailable = false;
					if ($this->preferredCalendar !== null)
					{
						while ($iterations-- && !($isAvailable = $this->isAvailableDate($date, !$mustBeAvailable)) && !$this->isPreferredCalendarDate($date))
							$date->addDay();
						if ($isAvailable)
							return true;
					}
					else if ($this->preferredWorkdays !== null)
					{
						while ($iterations-- && !($isAvailable = $this->isAvailableDate($date, !$mustBeAvailable)) && !$this->isPreferredWorkday($date))
							$date->addDay();
						if ($isAvailable)
							return true;
					}
					else
						return $this->findNextMostAppropriateDate($date,
						                                          $earliestDate,
						                                          ScheduleAlgorithm::NextStandardWorkday,
						                                          $iterations,
						                                          $mustBeAvailable);
					return false;
			}
			
			return false;
		}
		
		/**
		 * Determines if the date is a standard workday and is not a public holiday.
		 *
		 * @param Carbon $date
		 * @param bool $skip
		 * @return bool
		 */
		private function isAvailableDate(Carbon $date, bool $skip = false): bool
		{
			if ($skip)
				return true;
			return !in_array($date->toDateString(), $this->excludedDates, true) &&
			       in_array($date->dayOfWeek, $this->standardWorkdays);
		}
		
		private function isPreferredDate(Carbon $date): bool
		{
			if ($this->preferredCalendar !== null)
				return $this->isPreferredCalendarDate($date);
			
			if ($this->preferredWorkdays !== null)
				return $this->isPreferredWorkday($date);
			
			return in_array($date->dayOfWeek, $this->standardWorkdays);
		}
		
		private function isPreferredWorkday(Carbon $date): bool
		{
			return in_array($date->dayOfWeek, $this->preferredWorkdays ?? $this->standardWorkdays);
		}
		
		private function isPreferredCalendarDate(Carbon $date): bool
		{
			return in_array($date->day, $this->preferredCalendar[$date->month] ?? []);
		}
		
		private function moveToNextPreferredDate(Carbon $from, int &$iterations): void
		{
			while ($iterations--)
			{
				if ($this->preferredCalendar && empty($this->preferredCalendar[$from->month] ?? null))
				{
					$from->setDay(1);
					$from->addMonthNoOverflow();
				}
				else
					$from->addDay();
				
				if ($this->isPreferredDate($from))
					break;
			}
		}
	}
