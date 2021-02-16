<?php
	/** @noinspection PhpUnused */
	
	namespace YetAnother;
	
	use DateInterval;
	use Exception;
	
	/**
	 * Represents a calendar scheduler based on an interval measured
	 * in years. This can be used to calculate future or past schedule
	 * dates based on a reference date.
	 * @package YetAnother
	 */
	class YearTimer extends CalendarTimer
	{
		/**
		 * Initialises a new year-based scheduler.
		 * @param string $referenceDate A reference date to calculate schedule dates.
		 * @param int $interval The number of days for the interval.
		 * @throws Exception
		 */
		public function __construct(string $referenceDate, int $interval = 1)
		{
			parent::__construct($referenceDate, $interval, 'years');
		}
		
		/**
		 * @inheritDoc
		 */
		protected function calculateIntervalsUntilNext(DateInterval $difference): int
		{
			$years = $difference->y;
			[$intervals, $remainder] = self::modulus($years, $this->interval);
			
			if ($difference->invert === 0)
			{
				if ($remainder === 0 && ($difference->d > 0 || $difference->m > 0))
					$intervals++;
				return $intervals + ($remainder > 0 ? 1 : 0);
			}
			
			return -$intervals;
		}
	}
