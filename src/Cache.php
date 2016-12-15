<?php
require_once(__DIR__ . '/../load.php');

class Cache {
	private $cache_dir;

	function __construct($cache_dir) {
		$this->cache_dir = $cache_dir;
	}

	private function get_file_for_key($key) {
		return $this->cache_dir . '/' . md5($key);
	}

	function has($key) {
		return file_exists($this->get_file_for_key($key));
	}

	function write($key, $content) {
		file_put_contents($this->get_file_for_key($key), $content);
	}

	function read($key) {
		if (!$this->has($key)) {
			throw new CacheException("Key $key does not exist. ");
		}
		return file_get_contents($this->get_file_for_key($key));
	}
}
class CacheException extends \Exception {};

class DummyCache extends Cache {

	function __construct() {}
	function has($key) {
		return false;
	}

	function write($key, $content) {}

	function read($key) {
		return '';
	}
}