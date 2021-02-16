<?php
	namespace YetAnother;
	
	use Exception;
	use PHPUnit\Framework\TestCase;
	
	class DaySchedulerTests extends TestCase
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
		public function testIsScheduledDailyDate()
		{
			$schedule = new DayCalendarScheduler('2020-01-05', 5);
			$dates = [
				'2019-12-01',
				'2019-12-26',
				'2020-01-05',
				'2020-01-15',
				'2020-02-04',
				'2020-02-24',
			];
			
			foreach($dates as $date)
				$this->assertTrue($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testIsNotScheduledDailyDate()
		{
			$schedule = new DayCalendarScheduler('2020-01-05', 5);
			$dates = [
				'2019-12-02',
				'2019-12-23',
				'2020-01-06',
				'2020-01-12',
				'2020-03-03',
			];
			
			foreach($dates as $date)
				$this->assertFalse($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testDailyDateScheduleFromSameDate()
		{
			$this->runScheduleTest([
					'2020-02-15' => 0,
					'2020-02-20' => 1,
					'2020-02-25' => 2,
					'2020-02-10' => -1
				],
				new DayCalendarScheduler('2020-02-15', 5),
				'2020-02-15');
		}
		
		/**
		 * @throws Exception
		 */
		public function testDailyDateScheduleFromDifferentScheduleDate()
		{
			$this->runScheduleTest([
					'2020-02-20' => 0,
					'2020-02-25' => 1,
					'2020-03-01' => 2,
					'2020-02-15' => -1
				],
				new DayCalendarScheduler('2020-02-15', 5),
				'2020-02-20');
		}
		
		/**
		 * @throws Exception
		 */
		public function testDailyDateScheduleFromIntermediateDate()
		{
			$this->runScheduleTest([
					'2020-02-20' => 0,
					'2020-02-25' => 1,
					'2020-03-01' => 2,
					'2020-02-15' => -1
				],
				new DayCalendarScheduler('2020-02-15', 5),
				'2020-02-17');
		}
		
		/**
		 * @throws Exception
		 */
		public function testDailyDateScheduleFromPastIntermediateDate()
		{
			$this->runScheduleTest([
					'2020-02-05' => 0,
					'2020-02-10' => 1,
					'2020-02-15' => 2,
					'2020-01-31' => -1,
					'2020-01-26' => -2
				],
				new DayCalendarScheduler('2020-02-25', 5),
				'2020-02-01');
		}
	}
