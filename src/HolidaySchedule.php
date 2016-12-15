<?php
require_once(__DIR__ . '/../load.php');
require_once(__DIR__ . '/Cache.php');

class HolidaySchedule {
	public $http_client;
	public $cache;
	private $years_data = [];

	const URL = "http://www.officeholidays.com/countries/usa/%s.php";
	function __construct() {
		$this->http_client = new \GuzzleHttp\Client();
		$this->cache = new Cache(CACHE_DIR);
	}

	private function fetch_holidays_for_year($year) {
		if (isset($this->years_data[$year])) {
			return $this->years_data[$year];
		}
		$html = $this->fetch_html(sprintf(self::URL, $year));
		phpQuery::newDocument($html);

		$holiday_nodes = pq('table tr.holiday span.mobile_ad');
		$result = collect($holiday_nodes)
			->map(function ($dom_node) use ($year) {
				return new DateTime($dom_node->nodeValue . ' ' . $year);
			});

		$this->years_data[$year] = $result;

		return $result;
	}
	
	private function fetch_html($url) {
		if ($this->cache->has($url)) {
			return $this->cache->read($url);
		}

		try {
			$res = $this->http_client->request('GET', $url);
			$html = $res->getBody();
		} catch(\GuzzleHttp\Exception\TransferException $e) {
			throw new HolidayScheduleException("Could not fetch holidays for: " . $e->getMessage());
		}

		$this->cache->write($url, $html);
		return $html; 
	}

	function is_holiday(DateTimeInterface $date) {
		$year = $date->format('Y');
		$holidays = $this->fetch_holidays_for_year($year);

		return $holidays->contains( function ($holiday) use ($date) {
			return $date->format('Y-m-d') === $holiday->format('Y-m-d');
		});
	}
};
class HolidayScheduleException extends \Exception {}