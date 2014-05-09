<?php
/**
 * Victor's IP class
 */
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class ip {
	static function get() {
		//$ip = $_SERVER['REMOTE_ADDR'];
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		// can change hooks here...
		//$ip = self::transform_varnish($ip); // local Varnish
		//$ip = self::transform_cf($ip); // CloudFlare
		return $ip; // return it!
	}

	static function getint() { // int can be negative - but it still fits
		return ip2long( self::get() );
	}

	static function getlong() { // as a uint in a string
		return sprintf( '%u', self::getint() );
	}

	// compat with local Varnish cache
	static function transform_varnish( $ip ) {
		if ( isset( $_SERVER['HTTP_X_REMOTE_ADDR'] ) ) {
			$localregex = '#^(1?0|172\.(1[6-9]|2[0-9]|3[0-1])|192\.168)\.#';
			if ( preg_match( $localregex, $ip ) ) {
				$ip = $_SERVER['HTTP_X_REMOTE_ADDR'];
				//if(preg_match($localregex, $ip)) return '127.255.255.255';
			}
		}
		return $ip;
	}

	// compat with CloudFlare
	static function transform_cf( $ip ) {
		if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			$cipok = false;
			$cf_check_ips = array( // WARNING: only supports the check of the first 3 octets of an IPv4 address
				'204\.93\.(240|177)',
				// 204.93.240.0/24 (204.93.240.0 - 204.93.240.255)
				// 204.93.177.0/24 (204.93.177.0 - 204.93.177.255)
				'199\.27\.(128|129|13[0-5])',
				//// 199.27.128.0/21 (199.27.128.0 - 199.27.135.255)
				'173\.245\.(48|49|5[0-9]|6[0-3])',
				// 173.245.48.0/20 (173.245.48.0 - 173.245.63.255)
				'103\.22\.20[0-3]',
				// 103.22.200.0/22 (103.22.200.0 - 103.22.203.255)
				'141\.101\.(6[4-9]|[7-9][0-9]|1([01][0-9]|2[0-7]))',
				// 141.101.64.0/18 (141.101.64.0 - 141.101.127.255)
				'108\.162\.(19[2-9]|2[0-4][0-9]|25[0-5])',
				// 108.162.192.0/18(108.162.192.0 - 108.162.255.255)

				// IPv6 not supported yet :(
				// 2400:cb00::/32
				// 2606:4700::/32
			);
			foreach ( $cf_check_ips as $cip ) if ( preg_match( '#^'.$cip.'\.#', $ip ) ) { $cipok = true; break; }
			if ( $cipok ) $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		return $ip;
	}
}
