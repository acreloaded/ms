<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->add( $section = new PageSection( 'Begin' ) );
$section->add1( new Paragraph(
		'To use the administration panel, simply click a link to the left.'
	) );
