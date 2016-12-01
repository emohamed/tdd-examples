<?php
interface HolidaysDataProvider {
	function getHolidays();
}


class DummyHolidaysProvider implements HolidaysDataProvider {
	function getHolidays() {
		return ['test'];
	}
}