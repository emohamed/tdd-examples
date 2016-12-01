<?php
class DeliveryDateCalculator {
	// Base DateTime used for calculations
	private $time;
	public $bank_holidays = ['01-01', '03-03', '07-04'];

	function __construct() {
		$this->time = new DateTimeImmutable();
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
		return !$this->is_weekend($time) && !$this->is_bank_holiday($time);
	}

	function is_bank_holiday($time) {
		return in_array($time->format('m-d'), $this->bank_holidays);
	}

	function is_weekend($time) {
		$weekday = $time->format('D');

		return in_array($weekday, ['Sat', 'Sun']);
	}
}