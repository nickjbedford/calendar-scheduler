<?php
	namespace YetAnother;
	
	use Exception;
	use PHPUnit\Framework\TestCase;
	
	class YearSchedulerTests extends TestCase
	{
		/**
		 * @param array $expectedDatesForOffsets
		 * @param CalendarScheduler $schedule
		 * @param string $today
		 * @throws Exception
		 */
		private function runScheduleTest(
			array $expectedDatesForOffsets,
			CalendarScheduler $schedule,
			string $today)
		{
			foreach($expectedDatesForOffsets as $expected=>$offset)
			{
				$result = $schedule->getNextDate($offset, $today);
				$this->assertEquals($expected, $result);
			}
		}
		
		/**
		 * @throws Exception
		 */
		public function testIsScheduledYearlyDate()
		{
			$schedule = new YearCalendarScheduler('2020-01-05', 2);
			$dates = [
				'2018-01-05',
				'2020-01-05',
				'2022-01-05',
				'2024-01-05',
			];
			
			foreach($dates as $date)
				$this->assertTrue($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testIsNotScheduledYearlyDate()
		{
			$schedule = new YearCalendarScheduler('2020-01-05', 2);
			$dates = [
				'2019-01-05',
				'2021-01-05',
				'2023-01-05',
				'2025-01-05',
				'2020-01-04',
			];
			
			foreach($dates as $date)
				$this->assertFalse($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testYearlyDateScheduleFromSameDate()
		{
			$this->runScheduleTest([
					'2020-02-15' => 0,
					'2022-02-15' => 1,
					'2024-02-15' => 2,
					'2018-02-15' => -1
				],
				new YearCalendarScheduler('2020-02-15', 2),
				'2020-02-15');
		}
		
		/**
		 * @throws Exception
		 */
		public function testYearlyDateScheduleFromDifferentScheduleDate()
		{
			$this->runScheduleTest([
					'2022-02-15' => 0,
					'2024-02-15' => 1,
					'2026-02-15' => 2,
					'2020-02-15' => -1
				],
				new YearCalendarScheduler('2020-02-15', 2),
				'2020-04-15');
		}
		
		/**
		 * @throws Exception
		 */
		public function testYearlyDateScheduleFromIntermediateDate()
		{
			$this->runScheduleTest([
					'2022-02-15' => 0,
					'2024-02-15' => 1,
					'2026-02-15' => 2,
					'2020-02-15' => -1
				],
				new YearCalendarScheduler('2020-02-15', 2),
				'2020-04-10');
		}
		
		/**
		 * @throws Exception
		 */
		public function testYearlyDateScheduleFromPastIntermediateDate()
		{
			$this->runScheduleTest([
					'2018-02-15' => 0,
					'2020-02-15' => 1,
					'2022-02-15' => 2,
					'2016-02-15' => -1
				],
				new YearCalendarScheduler('2020-02-15', 2),
				'2016-04-10');
		}
	}
