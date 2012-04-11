<?php
/**
 * Free geo ip service implementation for SilverStripe
 * 
 * @author Tim Klein <tim at dodat dot co dot nz>
 * https://github.com/dodat/
 * Usage:
 * add this to your mysite/_config.php
 * Geoip::$default_country_code = FreeGeoipService::get_country_code();
 *
 * Please note that the free lookup service is restricted to 1000 requests per hour,
 * run your own if you need more.
 * 
 * set static $lookup_url to the url of your service
 **/

class FreeGeoipService {
	
	static $session_key = "VisitorCountryCode";
	
	static $default_country_code = "NZ";
	
	static $lookup_url = "http://freegeoip.net/";
	
	public static function get_country_code() {
		if($code = self::get_country_code_from_session()) {
			return $code;
		}
		$code = self::$default_country_code;
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
			$url = self::$lookup_url."json/{$ip}";
			if($response = @file_get_contents($url)) {
				$data = json_decode($response);
				if(Geoip::countryCode2name($data->country_code)) {
					$code = $data->country_code;
				}
			}
		}
		self::set_country_code_to_session($code);
		return $code;
	}
	
	function set_country_code_to_session($code) {
		Session::set(self::$session_key, $code);
	}
	
	function get_country_code_from_session() {
		if(!isset($_SESSION)) {
			Session::start();
		}
		return !isset($_GET['flush']) ? Session::get(self::$session_key) : false;
	}
	
}
