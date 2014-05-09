<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// CREDITS!
$page->add( $section = new PageSection( 'Credits' ) );
$section->add1( $section2 = new PageSubSection( 'Foundation' ) );
$section2->add1( new Paragraph(
		'This script was made for AssaultCube Reloaded, which was founded by Victor.'
	) );

$section->add1( $section2 = new PageSubSection( 'Coding' ) );
$section2->add1( new Paragraph(
		'This script was purely coded by Victor.'
	) );

$section->add1( $section2 = new PageSubSection( 'Nobody else' ) );
$section2->add1( new Paragraph(
		'Almost nobody else contributed to this script. By that, I mean: the script was coded in PHP, and the server has to be run by someone.'
	) );
