<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// Everybody's dashboard!
$page->add( $section = new PageSection( 'Welcome' ) );
$section->add1( new Paragraph(
		'This Master-Server panel, designed by Victor, for AssaultCube Reloaded,
is a high-quality object-oriented script, containing over 16 MB of code.'
	) );

$page->add( $section = new PageSection( 'Security' ) );
$section->add( array(
		new Paragraph( 'This script is almost impregnable. Only the stupidity of users may compromise the security of this script.' ),
		new Paragraph( 'You have '.$page->role.' access. You will be logged out from 1 hour of inactivity.' ),
	) );

$section->bar( 'Today is '.date( DATE_RFC850 ) );
$section->note( new HTMLBlock( '<a href="index.php?area=home&module=credit" class="readmore">Read more</a>' ) );
