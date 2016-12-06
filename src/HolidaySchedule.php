<?php
require(__DIR__ . '/../load.php');

class HolidaySchedule {
	public $http_client;

	const URL = "http://www.officeholidays.com/countries/usa/%s.php";
	function __construct() {
		$this->http_client = new \GuzzleHttp\Client();
	}

	function get_holidays(DateTimeInterface $start=null, DateTimeInterface $end=null) {

	}

	private function fetch_holidays_for_year($year) {
		$html = $this->fetch_html(sprintf(self::URL, $year));
		phpQuery::newDocument($html);

		$holiday_nodes = pq('table tr.holiday span.mobile_ad');
		return collect($holiday_nodes)
			->map(function ($dom_node) use ($year) {
				return new DateTime($dom_node->nodeValue . ' ' . $year);
			});
	}
	
	private function fetch_html($url) {
		$cache_file = CACHE_DIR . md5($url) . '.html';

		if (file_exists($cache_file)) {
			return file_get_contents($cache_file);
		}

		
		$res = $this->http_client->request('GET', $url);
		$html = $res->getBody();
		file_put_contents($cache_file, $html);
		return $html; 
	}

	function is_holiday(DateTimeInterface $date) {
		$year = $date->format('Y');

		return $this
			->fetch_holidays_for_year($year)
			->contains( function ($holiday) use ($date) {
				return $date->format('Y-m-d') === $holiday->format('Y-m-d');
			});

	}
};