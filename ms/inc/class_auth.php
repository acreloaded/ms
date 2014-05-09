<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_cookies.php';
require_once 'class_random.php';

class auth_checks {
	// a-zA-Z0-9 and dash
	static function usr( $u ) { return preg_replace( '#[^a-z0-9-]+#i', '', trim( $u ) ); }
	// hexadecimal (SHA1 or MD5)
	static function pwd( $p ) { return substr( preg_replace( '#[^a-f0-9]+#', '', strtolower( trim( $p ) ) ), 0, 40 ); }
	// plain passwords
	static function pwd_plain( $p ) { return preg_replace( '#[^a-z0-9-]+#i', '', trim( $p ) ); }
}

class auth_cookies extends cookies{
	static $logincookies = array( 'key' );
	static function key() { return self::get( 'key' ); }
	static function set_key( $k, $r = false ) {
		self::set( 'key', trim( $k ), $r ? ( time() + self::remember ) : 0 );
	}
	static function check() {
		foreach ( self::$logincookies as $c )
			if ( !cookies::has( $c ) )
				return false;
			return true;
	}
	static function destroy() {
		foreach ( self::$logincookies as $c )
			if ( cookies::has( $c ) )
				cookies::set( $c );
	}
}
