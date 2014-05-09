<?php
// servers
$config['cdn'] = 'static/'; // static file cdn, with a trailing slash
$config['acdn'] = 'static/'; // admin panel static cdn (for dev only, change later)
// still used by: panel/inc/class_page_head.php, panel/areas/home/info.php
$config['servers']['autoapprove'] = true; // servers can be registered
$config['servers']['check-socket'] = 0; // check server with UDP sockets or not (maximum failures > 2 , or = 0)
$config['servers']['check-socket-force'] = true; // force a check if the above is 0

// used to have a forcelist...

// database
$config['db']['host'] = 'localhost'; // database host (domain or IP)
$config['db']['name'] = 'ms'; // database name
$config['db']['user'] = ''; // database user
$config['db']['pass'] = ''; // database pass
$config['db']['pref'] = 'acrms_'; // table prefix

// SMTP logger flush
$config['smtp']['from'] = 'someone@domain.example';
$config['smtp']['to'] = array( 'ms-admin-group@groups.domain.example' );
$config['smtp']['host'] = 'localhost';
$config['smtp']['user'] = '';
$config['smtp']['pass'] = '';
