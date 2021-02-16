<?php
	namespace YetAnother;
	
	use DateInterval;
	use DateTime;
	use Exception;
	
	/**
	 * Represents a calendar scheduler based on a type of interval
	 * and a frequency, such as days, months or years. This can be
	 * used to calculate future or past schedule dates based on a
	 * reference date.
	 * @package YetAnother
	 */
	abstract class CalendarScheduler
	{
		private string $intervalName;
		private DateTime $referenceDate;
		protected int $interval;
		
	    protected static function modulus(int $value, int $divisor): array
	    {
	        return [ intval($value / $divisor), $value % $divisor ];
	    }
		
		/**
		 * Initialises a new date schedule.
		 * @param string $referenceDate A reference date to calculate schedule dates.
		 * @param int $interval The number of periods for each scheduled recurrence.
		 * @param string $intervalName The strotime compatible interval name, such as "days".
		 * @throws Exception
		 */
		protected function __construct(string $referenceDate, int $interval, string $intervalName)
		{
			$this->referenceDate = (new DateTime($referenceDate))->setTime(0, 0);
			$this->interval = max(1, $interval);
			$this->intervalName = $intervalName;
		}
		
		/**
		 * Gets the reference date used to calculate scheduled dates.
		 * @return DateTime
		 */
		public function getReferenceDate(): DateTime
		{
			return $this->referenceDate;
		}
		
		/**
		 * Gets the interval frequency used to calculate scheduled dates.
		 * @return int
		 */
		public function getInterval(): int
		{
			return $this->interval;
		}
		
		/**
		 * Gets the next date for the schedule.
		 * @param int $intervalOffset The number of intervals ahead or behind to calculate.
		 * @param string|null $from The date to calculate from, otherwise today's date.
		 * @return string
		 * @throws Exception
		 */
		public function getNextDate(int $intervalOffset = 0, ?string $from = null): string
		{
			$date = (new DateTime($from ?: date('Y-m-d')))->setTime(0, 0);
			$difference = $this->referenceDate->diff($date);
			$intervals = $this->calculateIntervalsUntilNext($difference);
			
			$offset = ($intervals + $intervalOffset) * $this->interval;
			if ($offset >= 0)
				$offset = "+$offset";
			
			$offsetDescription = "$offset $this->intervalName";
			$timestamp = strtotime($offsetDescription, $this->referenceDate->getTimestamp());
			return date('Y-m-d', $timestamp);
		}
		
		/**
		 * This should calculate the number of days, months or years to add or subtract from the reference date
		 * based on a current date.
		 * @param DateInterval $difference The difference to the current date.
		 * @return int
		 */
		protected abstract function calculateIntervalsUntilNext(DateInterval $difference): int;
		
		/**
		 * @param int $count The number of dates to calculate.
		 * @param string|null $from The date to calculate from, otherwise today's date.
		 * @return array
		 * @throws Exception
		 */
		public function getDates(int $count = 1, ?string $from = null): array
		{
			return array_map(fn(int $offset) => $this->getNextDate($offset, $from),
				range(0, max(1, $count - 1)));
		}
		
		/**
		 * Determines if a date is on the a schedule date.
		 * @param string $date
		 * @return bool
		 * @throws Exception
		 */
		public function isScheduleDate(string $date): bool
		{
			return $this->getNextDate(0, $date) == $date;
		}
	}
