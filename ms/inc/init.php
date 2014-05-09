<?php
if ( !defined( 'IN_ACRMS' ) ) die( 'Direct initialization of this file is not allowed.' );

if ( function_exists( 'unicode_decode' ) ) {
	// Unicode extension introduced in 6.0
	error_reporting( E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_STRICT );
}
elseif ( defined( 'E_DEPRECATED' ) ) {
	// E_DEPRECATED introduced in 5.3
	error_reporting( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
}
else {
	error_reporting( E_ALL & ~E_NOTICE );
}

// ACR MS root
//define('ACRMS_ROOT', './');
// attempt auto-detection
if ( !defined( 'ACRMS_ROOT' ) ) define( 'ACRMS_ROOT', dirname( dirname( __FILE__ ) ).'/' );

define( 'TIME_NOW', time() );

//if(function_exists('date_default_timezone_set') && !ini_get('date.timezone')) date_default_timezone_set('GMT');

require_once $working_dir.'/config.php';

require_once $working_dir.'/inc/class_db.php';
$db = new DB_MySQL;

require_once $working_dir.'/inc/class_log.php';
require_once $working_dir.'/inc/class_log_db.php';
$log = new LogDB;

require_once $working_dir.'/inc/class_ip.php';
