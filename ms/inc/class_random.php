<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

class random {
	static function salt( $l ) {
		$salt_c = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$salt_l = strlen( $salt_c );
		$salt = '';
		for ( $p=0; $p < $l; ++$p )
			$salt .= $salt_c[mt_rand( 0, $salt_l - 1 )];
		return $salt;
	}
}
