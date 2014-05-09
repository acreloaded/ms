<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// supreme panel is supreme panel...
$enforce = 3;

$page->sidebar->add( 'Settings' );
$page->sidebar->append( 'home', 'Home' );
$page->sidebar->append( 'settings', 'Settings' );

$page->sidebar->set_module( $page->active_module, $_GET['module'] );
