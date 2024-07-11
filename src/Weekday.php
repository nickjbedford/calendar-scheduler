<?php
	
	namespace YetAnother;
	
	use Carbon\CarbonInterface;
	
	/**
	 * Represents the ordinals of the days of the week.
	 */
	class Weekday
	{
		const int Sunday = CarbonInterface::SUNDAY;
		const int Monday = CarbonInterface::MONDAY;
		const int Tuesday = CarbonInterface::TUESDAY;
		const int Wednesday = CarbonInterface::WEDNESDAY;
		const int Thursday = CarbonInterface::THURSDAY;
		const int Friday = CarbonInterface::FRIDAY;
		const int Saturday = CarbonInterface::SATURDAY;
		
		const array Weekdays = [
			Weekday::Monday,
			Weekday::Tuesday,
			Weekday::Wednesday,
			Weekday::Thursday,
			Weekday::Friday
		];
		
		const array MondayWednesdayFriday = [
			Weekday::Monday,
			Weekday::Wednesday,
			Weekday::Friday
		];
		
		const array All = [
			Weekday::Sunday,
			Weekday::Monday,
			Weekday::Tuesday,
			Weekday::Wednesday,
			Weekday::Thursday,
			Weekday::Friday,
			Weekday::Saturday
		];
		
		const array Weekends = [
			Weekday::Saturday,
			Weekday::Sunday
		];
	}
