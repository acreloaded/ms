<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

if ( is_array( $_POST['settings'] ) ) {
	$allowed_settings = array(
		// 'key' => type, // 1: integer, 2: domain
		'currentgame' => 1,
		'minprotocol' => 1,

		'placeholder' => 2,
		'defaultport' => 1,
		'minport' => 1,
		'maxport' => 1,
	);
	$write = array();
	foreach ( $_POST['settings'] as $k => $v ) switch ( $allowed_settings[$k] ) {
		case 1:
			$write[$k] = intval( $v );
			break;
		case 2:
			$write[$k] = auth_checks::usr( $v ); // not optimal
			break;
		default:
			// not allowed
			break;
		}
	// only update if set
	foreach ( $write as $k => $v )
		$db->update( 'settings', array( 'val' => $settings[$k] = $v ), "`key`='$k'" );
}

$page->add( $section = new PageSection( 'Change Settings' ) );
$section->add1( new HTMLBlock( '<form method="POST">' ) );
$section->add1( new Paragraph( "Don't screw anything up... I only sanitize so much&hellip;" ) );

$section->add1( $section2 = new PageSubSection( 'Versions' ) );
$section2->add1( $list = new UnorderedList( array(
			new Paragraph( '<label>Current Version: <input name="settings[currentgame]" value="'.$settings['currentgame'].'" /></label>' ),
			new Paragraph( '<label>Required Protocol: <input name="settings[minprotocol]" value="'.$settings['minprotocol'].'" /></label>' ),
		) ) );

$section->add1( $section2 = new PageSubSection( 'Servers' ) );
$section2->add1( $list = new UnorderedList( array(
			new Paragraph( '<label>Placeholder: <input name="settings[placeholder]" value="'.$settings['placeholder'].'" /></label>' ),
			new Paragraph( '<label>Default Port: <input name="settings[defaultport]" value="'.$settings['defaultport'].'" /></label>' ),
			new Paragraph( '<label>Minimum Port: <input name="settings[minport]" value="'.$settings['minport'].'" /></label>' ),
			new Paragraph( '<label>Maximum Port: <input name="settings[maxport]" value="'.$settings['maxport'].'" /></label>' ),
		) ) );

$page->add( $section = new PageSection( 'Save' ) );
$section->add1( new Paragraph( '<input type="submit" value="Save Changes"/> <input type="reset" value="Reset"/>' ) );

$section->add1( new HTMLBlock( '</form>' ) );
