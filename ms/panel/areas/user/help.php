<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->add( $section = new PageSection( 'Warning' ) );
$section->add1( new Paragraph(
		'Do not fool around in this area. You may even lose access to your account! The only bad things that occur here result from ID-ten-T errors.'
	) );

$section->add1( new Paragraph( 'Have fun!' ) );

$page->add( $section = new PageSection( 'Storage' ) );
$section->add( array(
		new Paragraph( 'Be careful of what you put in here. We store all passwords in cleartext so it will work with the ACR auth system.' ),
		new Paragraph( 'If these passwords had no use with the auth system, we would encrypt your password with quick, yet secure hashes and salts.' ),
	) );
