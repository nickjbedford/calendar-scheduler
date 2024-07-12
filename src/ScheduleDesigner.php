<?php
	
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
		private array $calendar = [];
		private DayOfMonthScheduleMethod $dayOfMonthScheduleMethod = DayOfMonthScheduleMethod::NextWorkday;
		
		/**
		 * Creates the schedule date finder.
		 * @throws Exception
		 */
		function create(): ScheduleDateFinder
		{
			return new ScheduleDateFinder(
				$this->workdays,
				!empty($this->preferredWorkdays) ? $this->preferredWorkdays : null,
				$this->calendar,
				$this->holidays,
				$this->dayOfMonthScheduleMethod);
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
		 * Adds monday to friday as available workdays.
		 * @return $this
		 */
		function availableMondayToFriday(): self
		{
			$this->workdays = Weekday::Weekdays;
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
		function prefer(array $weekdays): self
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
			foreach(Weekday::Weekdays as $day)
				$this->preferredWorkdays[] = $day;
			return $this;
		}
		
		/**
		 * Adds weekends as preferred workdays.
		 * @return $this
		 */
		function preferWeekends(): self
		{
			foreach(Weekday::Weekends as $day)
				$this->preferredWorkdays[] = $day;
			return $this;
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
		function addHoliday(string|Carbon|int $date): self
		{
			$this->holidays[] = Carbon::parse($date)->format('Y-m-d');
			return $this;
		}
		
		/**
		 * Adds multiple dates as holidays to exclude from availability.
		 * @param array $dates The dates to add as holidays to exclude.
		 * @return $this
		 */
		function addHolidays(array $dates): self
		{
			foreach($dates as $date)
				$this->addHoliday($date);
			return $this;
		}
		
		/**
		 * Sets the availability for the specified months and days.
		 * @param int[] $months The months available.
		 * @param int[] $days The days available.
		 * @return $this
		 */
		function setAvailability(array $months = ScheduleDateFinder::EveryMonth,
		                         array $days = ScheduleDateFinder::EveryDay): self
		{
			foreach($months as $month)
				$this->setAvailabilityForMonth($month, $days);
			return $this;
		}
		
		/**
		 * Sets the days available during a particular month.
		 * @param int $month The month number (1-12).
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setAvailabilityForMonth(int $month, array $days = ScheduleDateFinder::EveryDay): self
		{
			$this->calendar[$month] = $days;
			return $this;
		}
		
		/**
		 * Sets the days available during January.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setJanuaryAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(1, $days);
		}
		
		/**
		 * Sets the days available during February.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setFebruaryAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(2, $days);
		}
		
		/**
		 * Sets the days available during March.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setMarchAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(3, $days);
		}
		
		/**
		 * Sets the days available during April.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setAprilAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(4, $days);
		}
		
		/**
		 * Sets the days available during May.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setMayAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(5, $days);
		}
		
		/**
		 * Sets the days available during June.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setJuneAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(6, $days);
		}
		
		/**
		 * Sets the days available during July.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setJulyAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(7, $days);
		}
		
		/**
		 * Sets the days available during August.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setAugustAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(8, $days);
		}
		
		/**
		 * Sets the days available during September.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setSeptemberAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(9, $days);
		}
		
		/**
		 * Sets the days available during October.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setOctoberAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(10, $days);
		}
		
		/**
		 * Sets the days available during November.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setNovemberAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(11, $days);
		}
		
		/**
		 * Sets the days available during December.
		 * @param int[] $days The days of the month available.
		 * @return $this
		 */
		function setDecemberAvailability(array $days = ScheduleDateFinder::EveryDay): self
		{
			return $this->setAvailabilityForMonth(12, $days);
		}
	}
