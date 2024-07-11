<?php
	
	namespace YetAnother;
	
	use Carbon\CarbonInterface;
	
	/**
	 * Represents the ordinals of the days of the week.
	 */
	enum Weekday: int
	{
		case Sunday = CarbonInterface::SUNDAY;
		case Monday = CarbonInterface::MONDAY;
		case Tuesday = CarbonInterface::TUESDAY;
		case Wednesday = CarbonInterface::WEDNESDAY;
		case Thursday = CarbonInterface::THURSDAY;
		case Friday = CarbonInterface::FRIDAY;
		case Saturday = CarbonInterface::SATURDAY;
		
		const array Weekdays = [
			Weekday::Monday,
			Weekday::Tuesday,
			Weekday::Wednesday,
			Weekday::Thursday,
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
