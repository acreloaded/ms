<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->sidebar->add( 'Home' );
$page->sidebar->append( 'dash', 'Dashboard' );
//$page->sidebar->append('pref', 'Preferences');
$page->sidebar->append( 'credit', 'Credits' );

$page->sidebar->add( 'Debug' );
$page->sidebar->append( 'info', 'Debug' );

$page->sidebar->set_module( $page->active_module, $_GET['module'] );

switch ( $page->active_module ) {
	// limit debug info...
case 'info':
	$enforce = 3;
	break;
}
