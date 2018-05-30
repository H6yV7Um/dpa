<?php
/**
 * 获取IP相关功能
 *
 */
class IP
{
	/**
	 * 获取本机IP
	 * @param type $interface
	 * @return type
	 */
	public static function getLocalIp($interface = "eth0") {
		$str = exec("/sbin/ifconfig " . $interface . " | grep 'inet addr'");
		$str = explode(":", $str, 2);
		$str = explode(" ", $str[1], 2);
		return $str[0];
	}

	/**
	 * 获取客户端IP
	 * @return type
	 */
	public static function getRemoteIp() {
		return self::_getIP();
	}

	/**
	 * 获取客户端IP，剥去代理IP
	 * @return type
	 */
	public static function getRemoteIpOverProxy() {
		return self::_getIP(true);
	}

	private static function _getIP($reverse = false) {
		$fip = getenv('HTTP_X_FORWARDED_FOR');
		$oip = self::getOuterIP($fip, $reverse);
		if ($oip != "unknown")
			return $oip;

		$rip = getenv('REMOTE_ADDR');
		return self::getOuterIP($rip, $reverse);
	}

	private static function getOuterIP($str, $reverse = false) {
		$ips = preg_split("/;|,|\s/", $str);
		if ($reverse)
			$ips = array_reverse($ips);
		$rip = "unknown";
		foreach ($ips as $ip) {
			$ip = trim($ip);
			if (ip2long($ip) === false)
			  continue;
			if (!self::isInnerIP($ip))
				return $ip;
			else
			  $rip = $ip;
		}
		return $rip;
	}

	public static function isInnerIP($ip) {
		if ($ip == "127.0.0.1") {
			return true;
		}
		list($i1, $i2, $i3, $i4) = explode(".", $ip, 4);
		return ($i1 == 10 || ($i1 == 172 && 16 <= $i2 && $i2 < 32) || ($i1 == 192 && $i2 == 168));
	}
}
