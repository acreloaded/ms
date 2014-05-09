<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

define( 'ACRMS_COOKIE_PREF', 'acrms' );
class cookies {
	const remember = 31556926; // 1 year
	static function get( $name ) {
		return $_COOKIE[ACRMS_COOKIE_PREF.$name];
	}

	static function set( $name, $value = '', $expire = 0 ) { // like setcookie, but 'faster'!
		if ( $value == '' ) {
			$_COOKIE[ACRMS_COOKIE_PREF.$name] = NULL;
			setcookie( ACRMS_COOKIE_PREF.$name, '', time() - 3600 );
		}
		else {
			$_COOKIE[ACRMS_COOKIE_PREF.$name] = $value;
			setcookie( ACRMS_COOKIE_PREF.$name, $value, $expire );
		}
	}

	static function has( $name ) {
		return isset( $_COOKIE[ACRMS_COOKIE_PREF.$name] );
	}
}
