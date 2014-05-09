<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->sidebar->add( 'Bans' );
$page->sidebar->append( 'begin', 'Begin' );

$page->sidebar->add( 'Alerts' );
$page->sidebar->append( 'guid', 'GUIDs - TODO' );

$page->sidebar->add( 'Server Transformations' );
$page->sidebar->append( 'translate', 'Domain Translation' );
$page->sidebar->append( 'weight', 'Weights' );

$page->sidebar->set_module( $page->active_module, $_GET['module'] );

$enforce = $page->active_module == 'begin' ? 0 : 2; // admin panel!
