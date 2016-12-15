<?php
require_once(__DIR__ . '/HolidaySchedule.php');

class DeliveryDateCalculator {
	private $time;

	function __construct() {
		$this->time = new DateTimeImmutable();
		$this->holiday_schedule = new HolidaySchedule();
	}

	function setTime(DateTimeImmutable $time) {
		$this->time = $time;
	}
	function getTime() {
		return $this->time;
	}

	function calculate($business_days) {
		$time = $this->time;

		if ($this->is_business_day($time) && $time->format('G') < 9) {
			$business_days--;
		}

		while ($business_days > 0) {
			$time = $time->add(new DateInterval('P1D'));

			if ($this->is_business_day($time)) {
				$business_days--;
			}
		}

		return $time;
	}

	function is_business_day($time) {
		return !$this->is_weekend($time) && !$this->holiday_schedule->is_holiday($time);
	}

	function is_weekend($time) {
		$weekday = $time->format('D');

		return in_array($weekday, ['Sat', 'Sun']);
	}
}