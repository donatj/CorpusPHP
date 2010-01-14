<?

/**
* Cache Control Class
*
* @package CorpusPHP
* @subpackage Data
* @author Jesse G. Donat
* @version .9b
* @todo Add file caching capabilities
*/
class Cache {
	
	const SECOND = 'SECOND';
	const MINUTE = 'MINUTE';
	const HOUR = 'HOUR';
	const DAY = 'DAY';
	const WEEK = 'WEEK';
	const MONTH = 'MONTH';
	const QUARTER = 'QUARTER';
	const YEAR = 'YEAR';

	static function get($module, $key) {
		self::cleanup();
		return db::fetch("select value from cache where module = '" .db::input($module). "' and `key` = '".db::input($key)."'",'scalar');
	}
	
	static function isCached($module, $key) {
		return db::fetch("select count(*) from cache where module = '" .db::input($module). "' and `key` = '".db::input($key)."'",'scalar') > 0;
	}
	
	static function isExpired($module, $key) {
		$cache = db::fetch("select now() > expires from cache where module = '" .db::input($module). "' and `key` = '".db::input($key)."'",'scalar');
		return $cache || is_null($cache);
	}
	
	static private function cleanup(){
		db::query("Delete from cache where autoclear and now() > expires");
	}
	
	static function set($module, $key, $value, $expires, $interval = self::MINUTE, $autoclear = true) {
		self::cleanup();
		db::perform('cache', array(
			'module' => $module, 
			'key' => $key, 
			'value' => $value, 
			'expires' => array(true, 'date_add(now(), INTERVAL '.abs($expires).' ' .$interval. ')'),
			'autoclear' => (int)$autoclear, 
		), true);
	}
	
}