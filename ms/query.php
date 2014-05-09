<?php

// includes...
define( 'IN_ACRMS', 1 );
require_once 'global.php';
require_once ACRMS_ROOT.'query_classes.php';

// default object
$out = new MsQueryError;

// what do they want?
switch ( $_GET['q'] ) {
// number of servers
case 'servers':
	$out = new MsQueryServerNum;
	break; // never forget...
}

// send to user
$out->output();
