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
	}
