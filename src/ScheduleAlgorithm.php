<?php
	namespace YetAnother;
	
	/**
	 * Specifies the algorithm to use when finding the next date in a schedule.
	 * The next preferred calendar date is first found, then the algorithm
	 * determines which method to use from that date. If no match is found,
	 * the algorithm moves to the next preferred calendar date.
	 */
	enum ScheduleAlgorithm
	{
		/**
		 * If the calendar date falls on an unavailable date (non-workday or holiday),
		 * the previous available preferred workday is chosen, otherwise
		 * the previous standard workday will be chosen, but not before the
		 * specified 'not-before' date. If there are no options, the next
		 * preferred or normal workday will be chosen. See {@see ScheduleAlgorithm::NextPreferredThenStandardWorkday}.
		 */
		case ClosestPreferredThenClosestStandardWorkday;
		
		/**
		 * If the calendar date falls on an unavailable date (non-workday or holiday),
		 * the previous available workday is chosen, but not before the
		 * specified 'not-before' date. Otherwise the next available workday
		 * will be chosen. See {@see ScheduleAlgorithm::NextStandardWorkday}.
		 */
		case ClosestStandardWorkday;
		
		/**
		 * If the calendar date falls on an unavailable date (non-workday or holiday),
		 * the previous preferred workday is chosen, but not before the
		 * specified 'not-before' date. Otherwise the next preferred workday
		 * will be chosen. See {@see ScheduleAlgorithm::NextPreferredWorkday}.
		 */
		case ClosestPreferredWorkday;
		
		/**
		 * If the calendar date falls on an unavailable date (non-workday or holiday),
		 * the next available preferred workday is chosen. If this date is not available,
		 * the next available workday will be chosen.
		 */
		case NextPreferredThenClosestStandardWorkday;
		
		/**
		 * If the next calendar date falls on an unavailable date, the next
		 * available work day will be chosen.
		 */
		case NextStandardWorkday;
		
		/**
		 * If the next calendar date falls on an unavailable date, the next
		 * available preferred date will be chosen.
		 */
		case NextPreferredWorkday;
		
		/**
		 * If the next calendar date falls on an unavailable date, the next
		 * preferred date (preferred calendar, preferred workday or standard workday) will be chosen.
		 * This algorithm will only choose preferred dates and not fallback to another method.
		 */
		case OnlyPreferredDates;
		
		/**
		 * The default algorithm is {@see ScheduleAlgorithm::ClosestPreferredThenClosestStandardWorkday},
		 * which is the most resourceful method of finding the next schedule date.
		 */
		const ScheduleAlgorithm Default = self::ClosestPreferredThenClosestStandardWorkday;
	}
