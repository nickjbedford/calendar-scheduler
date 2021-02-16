<?php
	namespace YetAnother;
	
	use DateInterval;
	use Exception;
	
	/**
	 * Represents a calendar scheduler based on an interval measured
	 * in months. This can be used to calculate future or past schedule
	 * dates based on a reference date.
	 * @package YetAnother
	 */
	class MonthCalendarScheduler extends CalendarScheduler
	{
		/**
		 * Initialises a new month-based scheduler.
		 * @param string $referenceDate A reference date to calculate schedule dates.
		 * @param int $interval The number of days for the interval.
		 * @throws Exception
		 */
		public function __construct(string $referenceDate, int $interval = 1)
		{
			parent::__construct($referenceDate, $interval, 'months');
		}
		
		/**
		 * Creates a monthly scheduler.
		 * @param string $referenceDate
		 * @return static
		 * @throws Exception
		 */
		public static function monthly(string $referenceDate): self
		{
			return new self($referenceDate, 1);
		}
		
		/**
		 * Creates a six-monthly scheduler.
		 * @param string $referenceDate
		 * @return static
		 * @throws Exception
		 */
		public static function sixMonthly(string $referenceDate): self
		{
			return new self($referenceDate, 1);
		}
		
		/**
		 * Creates a six-monthly scheduler.
		 * @param string $referenceDate
		 * @return static
		 * @throws Exception
		 */
		public static function yearly(string $referenceDate): self
		{
			return new self($referenceDate, 12);
		}
		
		/**
		 * @inheritDoc
		 */
		protected function calculateIntervalsUntilNext(DateInterval $difference): int
		{
			$months = $difference->y * 12 + $difference->m;
			[$intervals, $remainder] = self::modulus($months, $this->interval);
			
			if ($difference->invert === 0)
			{
				if ($remainder === 0 && $difference->d > 0)
					$intervals++;
				return $intervals + ($remainder > 0 ? 1 : 0);
			}
			
			return -$intervals;
		}
	}
