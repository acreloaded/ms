<?php

$working_dir = dirname( __FILE__ );
if ( !$working_dir ) $working_dir = '.';

// Load the core file which begins all of the magic
require_once $working_dir.'/inc/init.php';

// connect to our database!
$db->connect( $config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['name'] );

// load cache manager
require_once ACRMS_ROOT.'/inc/class_cache_db.php';
$cache = new cache_db();

// run cron jobs
require_once ACRMS_ROOT.'/inc/class_cron.php';
cron::run();

// we definitely want to load the settings
$settings = array();
// load settings
$q = $db->select( 'settings' );
while ( $r = $db->fetch( $q ) ) $settings[$r['key']] = $r['val'];
