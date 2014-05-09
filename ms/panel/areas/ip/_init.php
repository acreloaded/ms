<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->sidebar->add( 'IP Blacklist' );
$page->sidebar->append( 'list', 'List' );
$page->sidebar->append( 'ip', 'My IPs' );
$page->sidebar->append( 'ips', 'All' );

$page->sidebar->add( 'IP Whitelist' );
$page->sidebar->append( 'list-w', 'List' );
$page->sidebar->append( 'ip-w', 'My IPs' );
$page->sidebar->append( 'ips-w', 'All' );

$page->sidebar->set_module( $page->active_module, $_GET['module'] );

$enforce = 2; // only allow admins...
if ( $page->active_module == 'list' || $page->active_module == 'list-w' ) $enforce = 0; // allow all...
if ( $page->active_module == 'ip' ) $enforce = 1; // only allow masters...
