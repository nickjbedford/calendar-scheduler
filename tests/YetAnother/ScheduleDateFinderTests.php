<?php
	namespace YetAnother;
	
	use Exception;
	use PHPUnit\Framework\TestCase;
	
	class ScheduleDateFinderTests extends TestCase
	{
		protected function setUp(): void
		{
			parent::setUp();
			ScheduleDateFinder::$defaultHolidays = [];
		}
		
		/**
		 * @throws Exception
		 */
		function testEveryCalendarDayAndWeekdaysFindsDate()
		{
			/** @var DayOfMonthScheduleMethod $method */
			foreach([ DayOfMonthScheduleMethod::NextPreferredDate, DayOfMonthScheduleMethod::NextWorkday ] as $method)
			{
				$schedule = new ScheduleDateFinder(
					Weekday::Weekdays,
					dayOfMonthScheduleMethod: $method);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-02',
					'2024-07-03' => '2024-07-03',
					'2024-07-04' => '2024-07-04',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $method->name);
				
				$schedule = new ScheduleDateFinder(
					Weekday::Weekdays,
					holidays: [ '2024-07-03' ],
					dayOfMonthScheduleMethod: $method);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-02',
					'2024-07-03' => '2024-07-04',
					'2024-07-04' => '2024-07-04',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $method->name);
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testEveryCalendarDayAndSelectWeekdaysFindsDate()
		{
			foreach([ DayOfMonthScheduleMethod::NextPreferredDate, DayOfMonthScheduleMethod::NextWorkday ] as $method)
			{
				$schedule = new ScheduleDateFinder(
					Weekday::MondayWednesdayFriday,
					dayOfMonthScheduleMethod: $method);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-03',
					'2024-07-03' => '2024-07-03',
					'2024-07-04' => '2024-07-05',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $method->name);
				
				$schedule = new ScheduleDateFinder(
					Weekday::MondayWednesdayFriday,
					holidays: [
						'2024-07-03',
						'2024-07-08',
					],
					dayOfMonthScheduleMethod: $method);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-05',
					'2024-07-03' => '2024-07-05',
					'2024-07-04' => '2024-07-05',
					'2024-07-05' => '2024-07-05',
					'2024-07-06' => '2024-07-10',
					'2024-07-07' => '2024-07-10',
					'2024-07-08' => '2024-07-10',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $method->name);
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testEveryCalendarDayAndMondayFindsDate()
		{
			foreach([ DayOfMonthScheduleMethod::NextPreferredDate, DayOfMonthScheduleMethod::NextWorkday ] as $method)
			{
				$schedule = new ScheduleDateFinder(
					[ Weekday::Monday ],
					dayOfMonthScheduleMethod: $method);
				
				foreach([
					'2024-07-01' => '2024-07-01',
					'2024-07-02' => '2024-07-08',
					'2024-07-03' => '2024-07-08',
					'2024-07-04' => '2024-07-08',
					'2024-07-05' => '2024-07-08',
					'2024-07-06' => '2024-07-08',
					'2024-07-07' => '2024-07-08',
					'2024-07-08' => '2024-07-08',
					'2024-07-09' => '2024-07-15',
				] as $from=>$expected)
					$this->assertEquals($expected, $schedule->nextAsString($from), $method->name);
			}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysFindsNextDate()
		{
			$schedule = new ScheduleDateFinder(
				availabilityCalendar: ScheduleDateFinder::createAvailabilityCalendar([ 5, 15, 25 ]));
			
				foreach([
					'2024-07-05' => [
						'2024-06-26',
						'2024-06-27',
						'2024-06-28',
						'2024-06-29',
						'2024-06-30',
						'2024-07-01',
						'2024-07-02',
						'2024-07-03',
						'2024-07-04',
						'2024-07-05',
					],
					'2024-07-15' => [
						'2024-07-06',
						'2024-07-07',
						'2024-07-08',
						'2024-07-09',
						'2024-07-10',
						'2024-07-11',
						'2024-07-12',
						'2024-07-13',
						'2024-07-14',
						'2024-07-15',
					],
					'2024-07-25' => [
						'2024-07-16',
						'2024-07-17',
						'2024-07-18',
						'2024-07-19',
						'2024-07-20',
						'2024-07-21',
						'2024-07-22',
						'2024-07-23',
						'2024-07-24',
						'2024-07-25',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from));
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysInAugustFindsNextDate()
		{
			$schedule = new ScheduleDateFinder(
				availabilityCalendar: ScheduleDateFinder::createAvailabilityCalendar([ 5, 15, 25 ]));
			
				foreach([
					'2024-08-05' => [
						'2024-07-26',
						'2024-07-27',
						'2024-07-28',
						'2024-07-29',
						'2024-07-30',
						'2024-08-01',
						'2024-08-02',
						'2024-08-03',
						'2024-08-04',
						'2024-08-05',
					],
					'2024-08-15' => [
						'2024-08-06',
						'2024-08-07',
						'2024-08-08',
						'2024-08-09',
						'2024-08-10',
						'2024-08-11',
						'2024-08-12',
						'2024-08-13',
						'2024-08-14',
						'2024-08-15',
					],
					'2024-08-26' => [
						'2024-08-16',
						'2024-08-17',
						'2024-08-18',
						'2024-08-19',
						'2024-08-20',
						'2024-08-21',
						'2024-08-22',
						'2024-08-23',
						'2024-08-24',
						'2024-08-25',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from));
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysInAugustFindsPreviousDate()
		{
			$schedule = new ScheduleDateFinder(
				                          [ Weekday::Monday, Weekday::Tuesday, Weekday::Wednesday ],
				availabilityCalendar:     ScheduleDateFinder::createAvailabilityCalendar([ 7, 15, 25 ]),
				dayOfMonthScheduleMethod: DayOfMonthScheduleMethod::ClosestWorkday);
			
				foreach([
					'2024-07-03' => [
						'2024-06-26',
						'2024-06-27',
						'2024-06-28',
						'2024-06-29',
						'2024-06-30',
						'2024-07-01',
						'2024-07-02',
						'2024-07-03',
					],
					'2024-07-08' => [
						'2024-07-04',
						'2024-07-05',
						'2024-07-06',
						'2024-07-07',
					],
					'2024-07-15' => [
						'2024-07-08',
						'2024-07-09',
						'2024-07-10',
						'2024-07-11',
						'2024-07-12',
						'2024-07-13',
						'2024-07-14',
						'2024-07-15',
					],
					'2024-07-24' => [
						'2024-07-16',
						'2024-07-17',
						'2024-07-18',
						'2024-07-19',
						'2024-07-20',
						'2024-07-21',
						'2024-07-22',
						'2024-07-23',
						'2024-07-24',
					],
					'2024-07-29' => [
						'2024-07-25',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from), $from);
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificCalendarDaysInAugustFindsPreviousDateWithEarlierNotBefore()
		{
			$schedule = new ScheduleDateFinder(
				                          [ Weekday::Monday, Weekday::Wednesday ],
				availabilityCalendar:     ScheduleDateFinder::createAvailabilityCalendar([ 7, 15, 25 ]),
				dayOfMonthScheduleMethod: DayOfMonthScheduleMethod::ClosestWorkday);
			
				foreach([
					'2024-07-03' => [
						'2024-07-04',
						'2024-07-05',
						'2024-07-06',
						'2024-07-07',
					],
					'2024-07-15' => [
						'2024-07-08',
						'2024-07-09',
						'2024-07-10',
						'2024-07-11',
						'2024-07-12',
						'2024-07-13',
						'2024-07-14',
						'2024-07-15',
					],
				] as $expected=>$froms)
				{
					foreach($froms as $from)
						$this->assertEquals($expected, $schedule->nextAsString($from, '2024-07-03'), $from);
				}
		}
		
		/**
		 * @throws Exception
		 */
		function testSpecificClosestWorkdayScheduleWorks()
		{
			$schedule = new ScheduleDateFinder(
				workdays:                 [Weekday::Monday, Weekday::Wednesday, Weekday::Friday],
				availabilityCalendar:     ScheduleDateFinder::createAvailabilityCalendar([5, 15, 25]),
				holidays:                 ['2024-06-17'],
				dayOfMonthScheduleMethod: DayOfMonthScheduleMethod::ClosestWorkday);
			
			/**
			 * '2024-06-05' Tuesday because 5th is a Wednesday so the next
			 * available calendar date.
			 */
			$this->assertEquals('2024-06-05', $schedule->nextAsString(from: '2024-06-04'));
			
			/**
			 * '2024-06-14' Friday because 15th is a Saturday so the "closest workday"
			 * (not before 6th June) is the Friday before the 15th.
			 */
			$this->assertEquals('2024-06-14', $schedule->nextAsString(from: '2024-06-06'));
			
			/**
			 * '2024-06-19' Wednesday because the 17th (Monday) is a holiday. The 15th is
			 * a Saturday so the next available workday is the Wednesday after the 17th.
			 */
			$this->assertEquals('2024-06-19', $schedule->nextAsString(from: '2024-06-15'));
			
			/**
			 * '2024-06-24' Monday because the 25th (Tuesday) is not a workday so the closest
			 * workday (not before 17th June) is the Monday before the 25th.
			 */
			$this->assertEquals('2024-06-24', $schedule->nextAsString(from: '2024-06-17'));
			
			/**
			 * '2024-06-26' Wednesday because the 25th (Tuesday) is not a workday
			 * so the closest workday (not before 25th June) is the Wednesday after the 25th.
			 */
			$this->assertEquals('2024-06-26', $schedule->nextAsString(from: '2024-06-25'));
		}
	}
