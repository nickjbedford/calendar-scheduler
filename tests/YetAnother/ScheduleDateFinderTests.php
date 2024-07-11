<?php
	namespace YetAnother;
	
	use PHPUnit\Framework\TestCase;
	
	class ScheduleDateFinderTests extends TestCase
	{
		protected function setUp(): void
		{
			parent::setUp();
			ScheduleDateFinder::$defaultHolidays = [];
		}
		
		function testEveryCalendarDayAndWeekdaysFindsDate()
		{
			/** @var DayOfMonthScheduleMethod $method */
			foreach([ DayOfMonthScheduleMethod::NextAvailableDate, DayOfMonthScheduleMethod::NextWorkday ] as $method)
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
					[ '2024-07-03' ],
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
		
		function testEveryCalendarDayAndSelectWeekdaysFindsDate()
		{
			foreach([ DayOfMonthScheduleMethod::NextAvailableDate, DayOfMonthScheduleMethod::NextWorkday ] as $method)
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
					[
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
		
		function testEveryCalendarDayAndMondayFindsDate()
		{
			foreach([ DayOfMonthScheduleMethod::NextAvailableDate, DayOfMonthScheduleMethod::NextWorkday ] as $method)
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
		
		function testSpecificCalendarDaysFindsNextDate()
		{
			$schedule = new ScheduleDateFinder(
				calendarAvailability: ScheduleDateFinder::createAvailabilityCalendar([ 5, 15, 25 ]));
			
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
		
		function testSpecificCalendarDaysInAugustFindsNextDate()
		{
			$schedule = new ScheduleDateFinder(
				calendarAvailability: ScheduleDateFinder::createAvailabilityCalendar([ 5, 15, 25 ]));
			
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
		
		function testSpecificCalendarDaysInAugustFindsPreviousDate()
		{
			$schedule = new ScheduleDateFinder(
				[ Weekday::Monday, Weekday::Tuesday, Weekday::Wednesday ],
				calendarAvailability: ScheduleDateFinder::createAvailabilityCalendar([ 7, 15, 25 ]),
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
	}
