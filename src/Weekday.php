<?php
	
	namespace YetAnother;
	
	/**
	 * Represents the ordinals of the days of the week.
	 */
	enum Weekday: int
	{
		case Sunday = 0;
		case Monday = 1;
		case Tuesday = 2;
		case Wednesday = 3;
		case Thursday = 4;
		case Friday = 5;
		case Saturday = 6;
		
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
