<?php
	namespace YetAnother;
	
	use Exception;
	use PHPUnit\Framework\TestCase;
	
	class MonthSchedulerTests extends TestCase
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
		public function testIsScheduledMonthlyDate()
		{
			$schedule = new MonthCalendarScheduler('2020-01-05', 2);
			$dates = [
				'2019-01-05',
				'2020-01-05',
				'2020-03-05',
				'2020-05-05',
				'2020-07-05',
				'2021-07-05'
			];
			
			foreach($dates as $date)
				$this->assertTrue($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testIsScheduledFirstOfMonthlyDate()
		{
			$schedule = new MonthCalendarScheduler('2020-01-01', 1);
			$dates = [
				'2019-01-01',
				'2020-01-01',
				'2020-03-01',
				'2020-05-01',
				'2020-07-01',
				'2021-07-01'
			];
			
			foreach($dates as $date)
				$this->assertTrue($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testIsNotScheduledMonthlyDate()
		{
			$schedule = new MonthCalendarScheduler('2020-01-05', 2);
			$dates = [
				'2019-02-05',
				'2020-04-05',
				'2020-06-05',
				'2020-08-05',
				'2020-10-05',
				'2020-10-06',
				'2021-02-05'
			];
			
			foreach($dates as $date)
				$this->assertFalse($schedule->isScheduleDate($date));
		}
		
		/**
		 * @throws Exception
		 */
		public function testMonthlyDateScheduleFromSameDate()
		{
			$this->runScheduleTest([
					'2020-02-15' => 0,
					'2020-04-15' => 1,
					'2020-06-15' => 2,
					'2019-12-15' => -1
				],
				new MonthCalendarScheduler('2020-02-15', 2),
				'2020-02-15');
		}
		
		/**
		 * @throws Exception
		 */
		public function testMonthlyDateScheduleFromDifferentScheduleDate()
		{
			$this->runScheduleTest([
					'2020-04-15' => 0,
					'2020-06-15' => 1,
					'2020-08-15' => 2,
					'2020-02-15' => -1
				],
				new MonthCalendarScheduler('2020-02-15', 2),
				'2020-04-15');
		}
		
		/**
		 * @throws Exception
		 */
		public function testMonthlyDateScheduleFromIntermediateDate()
		{
			$this->runScheduleTest([
					'2020-04-15' => 0,
					'2020-06-15' => 1,
					'2020-08-15' => 2,
					'2020-02-15' => -1,
					'2019-12-15' => -2
				],
				new MonthCalendarScheduler('2020-02-15', 2),
				'2020-04-10');
		}
		
		/**
		 * @throws Exception
		 */
		public function testMonthlyDateScheduleFromFarIntermediateDate()
		{
			$this->runScheduleTest([
					'2021-04-15' => 0,
					'2021-06-15' => 1,
					'2021-08-15' => 2,
					'2021-02-15' => -1
				],
				new MonthCalendarScheduler('2020-02-15', 2),
				'2021-03-10');
		}
		
		/**
		 * @throws Exception
		 */
		public function testMonthlyDateScheduleFromPastIntermediateDate()
		{
			$this->runScheduleTest([
					'2019-04-15' => 0,
					'2019-07-15' => 1,
					'2019-10-15' => 2,
					'2019-01-15' => -1
				],
				new MonthCalendarScheduler('2020-01-15', 3),
				'2019-01-16');
		}
	}
