<?php
	
	namespace YetAnother;
	
	/**
	 * Specifies the method to use when finding the next or previous
	 * date in a schedule.
	 */
	enum DayOfMonthScheduleMethod
	{
		/**
		 * If the calendar date falls on an unavailable date, the previous
		 * availble workday (on or after the 'not-before' date) is chosen,
		 * otherwise the next available workday will be chosen.
		 */
		case ClosestWorkday;
		
		/**
		 * If the next calendar date falls on an unavailable date, the next
		 * available work day will be chosen.
		 */
		case NextWorkday;
		
		/**
		 * If the next calendar date falls on an unavailable date, the next
		 * available preferred date will be chosen.
		 */
		case NextPreferredDate;
	}
