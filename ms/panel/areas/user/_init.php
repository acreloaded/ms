<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->sidebar->add( 'User CP' );
$page->sidebar->append( 'help', 'Assistance' );
$page->sidebar->append( 'alter', 'Change Password' );

$page->sidebar->add( 'EXTRA CAUTION' );
$page->sidebar->append( 'cubegen', 'Auth Code' );

$page->rightbar->add( 'Exercise caution', "If you're stupid enough to click on a link under EXTRA CAUTION on the LEFT while your enemy is behind you, it's all your fault." );

$page->sidebar->set_module( $page->active_module, $_GET['module'] );
