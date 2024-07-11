<?php
	
	namespace YetAnother;
	
	/**
	 * Specifies the method to use when finding the next or previous
	 * date in a schedule.
	 */
	enum DayOfMonthScheduleMethod
	{
		/**
		 * If the calendar date falls on an unavailable date,
		 * the previous availble workday on or after the reference date is chosen,
		 * otherwise the next available workday on or after the unavailable date.
		 */
		case PreviousWorkdayIfAvailable;
		
		/**
		 * If the next calendar date falls on an unavailable date,
		 * the next available work day will be chosen.
		 */
		case NextWorkday;
		case NextAvailableDate;
	}
