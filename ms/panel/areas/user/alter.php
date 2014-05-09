<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->add( $section = new PageSection( 'Want a new password?' ) );
$section->add1( new Paragraph(
		'You are required to enter your password twice, even though you will still have an active session.
<a href="index.php?area=user&module=cubegen">Use this</a> before you forget!'
	) );

// do change if needed
if ( $_POST['p1'] || $_POST['p2'] ) {
	$alters = 0;
	$p1 = $_POST['p1'];
	$p2 = $_POST['p2'];
	if ( $p1 != auth_checks::pwd_plain( $p1 ) ) $alters |= 1;
	if ( $p2 != auth_checks::pwd_plain( $p2 ) ) $alters |= 2;

	if ( $alters ) {
		// invalid password(s)
		if ( $alters == 1 ) $reason = 'First password is';
		elseif ( $alters == 2 ) $reason = 'Second password is';
		else $reason = 'Both passwords are';
		$section->bar( "$reason invalid (a-z A-Z 0-9 and - (dash) allowed)" );
	}
	elseif ( $p1 != $p2 ) {
		// no match
		$section->bar( 'Passwords differ (please re-enter)' );
	}
	else {
		// update
		$db->update( 'users', array( 'pwd' => $p1 ), 'id='.$auth->data->id );
		$section->bar( 'View your new password (use Auth Code)!' );
	}
}

$page->add( $section = new PageSection( 'Change Password' ) );
$section->add1( new HTMLBlock( '<form method="POST">' ) );
$section->add1( new Paragraph( 'We will not update your password unless it is <strong>fully</strong> successful.' ) );
$section->add1( new Paragraph( '<label>Enter new password: <input type="password" name="p1" /></label>' ) );
$section->add1( new Paragraph( '<label>Enter it again: <input type="password" name="p2" /></label>' ) );

$page->add( $section = new PageSection( 'Save' ) );
$section->add1( new Paragraph( '<input type="submit" value="Change It"/> <input type="reset" value="I\'m scared..."/>' ) );
$section->add1( new HTMLBlock( '</form>' ) );
