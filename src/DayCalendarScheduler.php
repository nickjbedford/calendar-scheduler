<?php
	namespace YetAnother;
	
	use DateInterval;
	use Exception;
	
	/**
	 * Represents a calendar scheduler based on an interval measured
	 * in days. This can be used to calculate future or past schedule
	 * dates based on a reference date.
	 * @package YetAnother
	 */
	class DayCalendarScheduler extends CalendarScheduler
	{
		/**
		 * Initialises a new day-based scheduler.
		 * @param string $referenceDate A reference date to calculate schedule dates.
		 * @param int $interval The number of days for the interval.
		 * @throws Exception
		 */
		public function __construct(string $referenceDate, int $interval = 7)
		{
			parent::__construct($referenceDate, $interval, 'days');
		}
		
		/**
		 * Creates a weekly scheduler.
		 * @param string $fromDate
		 * @return static
		 * @throws Exception
		 */
		public static function weekly(string $fromDate): self
		{
			return new self($fromDate, 7);
		}
		
		/**
		 * Creates a fortnightly scheduler.
		 * @param string $fromDate
		 * @return static
		 * @throws Exception
		 */
		public static function fortnightly(string $fromDate): self
		{
			return new self($fromDate, 14);
		}
		
		/**
		 * @inheritDoc
		 */
		protected function calculateIntervalsUntilNext(DateInterval $difference): int
		{
			$days = $difference->days;
			[$intervals, $remainder] = self::modulus($days, $this->interval);
			
			if ($difference->invert === 0)
				return $intervals + ($remainder > 0 ? 1 : 0);
			
			return -$intervals;
		}
	}
