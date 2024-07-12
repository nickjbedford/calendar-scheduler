<?php
	/** @noinspection PhpUnused */
	
	namespace YetAnother;
	
	use Carbon\Carbon;
	use Exception;
	
	/**
	 * Provides a fluent interface for designing a schedule
	 * for finding the most appropriates for a particular action
	 * to be taken, such as specific product shipping dates,
	 * using preferred and available workdays, a calendar of availibility,
	 * and a list of holidays to exclude.
	 */
	class ScheduleDesigner
	{
		private array $workdays = [];
		private array $preferredWorkdays = [];
		private array $holidays = [];
		private array $preferredCalendar = [];
		private ScheduleAlgorithm $algorithm = ScheduleAlgorithm::Default;
		
		/**
		 * Creates the schedule date finder.
		 * @throws Exception
		 */
		function create(): ScheduleFinder
		{
			return new ScheduleFinder(
				$this->workdays,
				!empty($this->preferredWorkdays) ? $this->preferredWorkdays : null,
				!empty($this->preferredCalendar) ? $this->preferredCalendar : null,
				$this->holidays,
				$this->algorithm);
		}
		
		/**
		 * Sets the algorithm to use when finding the next date in a schedule.
		 * @param ScheduleAlgorithm $algorithm
		 * @return $this
		 */
		function useAlgorithm(ScheduleAlgorithm $algorithm): self
		{
			$this->algorithm = $algorithm;
			return $this;
		}
		
		function findingClosestPreferredThenClosestStandardWorkday(): self
		{
			return $this->useAlgorithm(ScheduleAlgorithm::ClosestPreferredThenClosestStandardWorkday);
		}
		
		function findingClosestPreferredWorkday(): self
		{
			return $this->useAlgorithm(ScheduleAlgorithm::ClosestPreferredWorkday);
		}
		
		function findingClosestStandardWorkday(): self
		{
			return $this->useAlgorithm(ScheduleAlgorithm::ClosestStandardWorkday);
		}
		
		function findingNextPreferredThenClosestStandardWorkday(): self
		{
			return $this->useAlgorithm(ScheduleAlgorithm::NextPreferredThenClosestStandardWorkday);
		}
		
		function findingNextPreferredWorkday(): self
		{
			return $this->useAlgorithm(ScheduleAlgorithm::NextPreferredWorkday);
		}
		
		function findingNextStandardWorkday(): self
		{
			return $this->useAlgorithm(ScheduleAlgorithm::NextStandardWorkday);
		}
		
		/**
		 * Adds the specified days of the week as available workdays.
		 * @param array $weekdays
		 * @return $this
		 */
		function availableOn(array $weekdays): self
		{
			foreach($weekdays as $day)
				$this->workdays[] = $day;
			return $this;
		}
		
		/**
		 * Adds all weekdays as available workdays.
		 * @return $this
		 */
		function availableAllWeek(): self
		{
			return $this->availableOn(Weekday::All);
		}
		
		/**
		 * Adds monday to friday as available workdays.
		 * @return $this
		 */
		function availableMondayToFriday(): self
		{
			$this->workdays = Weekday::MondayToFriday;
			return $this;
		}
		
		/**
		 * Adds weekends as available workdays.
		 * @return $this
		 */
		function availableOnWeekends(): self
		{
			$this->workdays = Weekday::Weekends;
			return $this;
		}
		
		/**
		 * Adds Mondays as available workdays.
		 * @return $this
		 */
		function availableOnMondays(): self
		{
			$this->workdays = [ Weekday::Monday ];
			return $this;
		}
		
		/**
		 * Adds Tuesdays as available workdays.
		 * @return $this
		 */
		function availableOnTuesdays(): self
		{
			$this->workdays = [ Weekday::Tuesday ];
			return $this;
		}
		
		/**
		 * Adds Wednesdays as available workdays.
		 * @return $this
		 */
		function availableOnWednesdays(): self
		{
			$this->workdays = [ Weekday::Wednesday ];
			return $this;
		}
		
		/**
		 * Adds Thursdays as available workdays.
		 * @return $this
		 */
		function availableOnThursdays(): self
		{
			$this->workdays = [ Weekday::Thursday ];
			return $this;
		}
		
		/**
		 * Adds Fridays as available workdays.
		 * @return $this
		 */
		function availableOnFridays(): self
		{
			$this->workdays = [ Weekday::Friday ];
			return $this;
		}
		
		/**
		 * Adds Saturdays as available workdays.
		 * @return $this
		 */
		function availableOnSaturdays(): self
		{
			$this->workdays = [ Weekday::Saturday ];
			return $this;
		}
		
		/**
		 * Adds Sundays as available workdays.
		 * @return $this
		 */
		function availableOnSundays(): self
		{
			$this->workdays = [ Weekday::Sunday ];
			return $this;
		}
		
		/**
		 * Adds the specified workdays as preferred workdays.
		 * @param array $weekdays
		 * @return $this
		 */
		function preferSpecificWeekdays(array $weekdays): self
		{
			foreach($weekdays as $day)
				$this->preferredWorkdays[] = $day;
			return $this;
		}
		
		/**
		 * Adds Monday to Friday as preferred workdays.
		 * @return $this
		 */
		function preferMondayToFriday(): self
		{
			return $this->preferSpecificWeekdays(Weekday::MondayToFriday);
		}
		
		/**
		 * Adds weekends as preferred workdays.
		 * @return $this
		 */
		function preferWeekends(): self
		{
			return $this->preferSpecificWeekdays(Weekday::Weekends);
		}
		
		/**
		 * Adds Mondays as preferred workdays.
		 * @return $this
		 */
		function preferMondays(): self
		{
			$this->preferredWorkdays = [ Weekday::Monday ];
			return $this;
		}
		
		/**
		 * Adds Tuesdays as preferred workdays.
		 * @return $this
		 */
		function preferTuesdays(): self
		{
			$this->preferredWorkdays = [ Weekday::Tuesday ];
			return $this;
		}
		
		/**
		 * Adds Wednesdays as preferred workdays.
		 * @return $this
		 */
		function preferWednesdays(): self
		{
			$this->preferredWorkdays = [ Weekday::Wednesday ];
			return $this;
		}
		
		/**
		 * Adds Thursdays as preferred workdays.
		 * @return $this
		 */
		function preferThursdays(): self
		{
			$this->preferredWorkdays = [ Weekday::Thursday ];
			return $this;
		}
		
		/**
		 * Adds Fridays as preferred workdays.
		 * @return $this
		 */
		function preferFridays(): self
		{
			$this->preferredWorkdays = [ Weekday::Friday ];
			return $this;
		}
		
		/**
		 * Adds Saturdays as preferred workdays.
		 * @return $this
		 */
		function preferSaturdays(): self
		{
			$this->preferredWorkdays = [ Weekday::Saturday ];
			return $this;
		}
		
		/**
		 * Adds Sundays as preferred workdays.
		 * @return $this
		 */
		function preferSundays(): self
		{
			$this->preferredWorkdays = [ Weekday::Sunday ];
			return $this;
		}
		
		/**
		 * Adds a date as a holiday to exclude from availability.
		 * @param string|Carbon|int $date The date to add as a holiday to exclude.
		 * @return $this
		 */
		function excludeDate(string|Carbon|int $date): self
		{
			$this->holidays[] = Carbon::parse($date)->format('Y-m-d');
			return $this;
		}
		
		/**
		 * Adds multiple dates as holidays to exclude from availability.
		 * @param array $dates The dates to add as holidays to exclude.
		 * @return $this
		 */
		function excludeDates(array $dates): self
		{
			foreach($dates as $date)
				$this->excludeDate($date);
			return $this;
		}
		
		/**
		 * Sets the availability for the specified months and days.
		 * @param int[] $days The days of the month preferred.
		 * @param int[] $months The months to fill these preferred days.
		 * @return $this
		 */
		function preferCalendarDaysInSpecificMonths(array $days = ScheduleFinder::EveryDay,
		                                            array $months = ScheduleFinder::EveryMonth): self
		{
			foreach($months as $month)
				$this->preferDaysInSpecificMonth($month, $days);
			return $this;
		}
		
		/**
		 * Adds the days preferred during a particular month.
		 * @param int $month The month number (1-12).
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInSpecificMonth(int $month, array $days): self
		{
			foreach($days as $day)
				$this->preferredCalendar[$month][] = $day;
			return $this;
		}
		
		/**
		 * Sets the days preferred during January.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInJanuary(array $days): self
		{
			return $this->preferDaysInSpecificMonth(1, $days);
		}
		
		/**
		 * Sets the days preferred during February.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInFebruary(array $days): self
		{
			return $this->preferDaysInSpecificMonth(2, $days);
		}
		
		/**
		 * Sets the days preferred during March.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInMarch(array $days): self
		{
			return $this->preferDaysInSpecificMonth(3, $days);
		}
		
		/**
		 * Sets the days preferred during April.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInApril(array $days): self
		{
			return $this->preferDaysInSpecificMonth(4, $days);
		}
		
		/**
		 * Sets the days preferred during May.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInMay(array $days): self
		{
			return $this->preferDaysInSpecificMonth(5, $days);
		}
		
		/**
		 * Sets the days preferred during June.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInJune(array $days): self
		{
			return $this->preferDaysInSpecificMonth(6, $days);
		}
		
		/**
		 * Sets the days preferred during July.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInJuly(array $days): self
		{
			return $this->preferDaysInSpecificMonth(7, $days);
		}
		
		/**
		 * Sets the days preferred during August.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInAugust(array $days): self
		{
			return $this->preferDaysInSpecificMonth(8, $days);
		}
		
		/**
		 * Sets the days preferred during September.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInSeptember(array $days): self
		{
			return $this->preferDaysInSpecificMonth(9, $days);
		}
		
		/**
		 * Sets the days preferred during October.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInOctober(array $days): self
		{
			return $this->preferDaysInSpecificMonth(10, $days);
		}
		
		/**
		 * Sets the days preferred during November.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInNovember(array $days): self
		{
			return $this->preferDaysInSpecificMonth(11, $days);
		}
		
		/**
		 * Sets the days preferred during December.
		 * @param int[] $days The days of the month preferred.
		 * @return $this
		 */
		function preferDaysInDecember(array $days): self
		{
			return $this->preferDaysInSpecificMonth(12, $days);
		}
	}
