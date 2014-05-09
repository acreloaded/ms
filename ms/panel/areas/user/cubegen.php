<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// not stupid enough to make a function to get the pwd, except here
$q = $db->fetch( $db->select( 'users', 'pwd', "`id`={$auth->data->id}" ) );
if ( $q && $q['pwd'] ) $pwd = $q['pwd'];
else $pwd = 'fuck//looks like your passwd is not accessable';
$datas = "/authname {$auth->data->name};authkey $pwd";

// make the page
$page->add( $section = new PageSection( 'Quickly!' ) );
$section->add1( new Paragraph(
		'Your password is displayed in cleartext. Be sure to use this quick, before the enemy finds you! Also, clear your clipboard after.'
	) );

$page->add( $section = new PageSection( 'Your code' ) );
$section->add1( new Blockquote( $datas ) );

$page->add( $section = new PageSection( 'Instructions' ) );
$section->add1( new Paragraph( 'Copy the code, and "say" it into AssaultCube Reloaded. Clean your dirty clipboard, and use F9 in-game.' ) );
$section->add1( new Paragraph( 'Did you know that you can also use "/connectauth 1"?' ) );
$section->add1( new Paragraph( 'An OSI Layer 8 error may occur if you forget to use "/authlock 0"!' ) );
