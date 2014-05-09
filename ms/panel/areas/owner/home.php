<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->add( $section = new PageSection( 'Settings' ) );
$section->add1( new Paragraph( 'This is where the most restricted settings can be set!' ) );

$q = $db->fetch( $db->select( 'users_sessions', 'COUNT(*) AS n' ) );
$qs = ( $q = $q['n'] ) == 1 ? '' : 's';
$qi = $qs ? 'were' : 'was';
$section->add1( new Paragraph( "There $qi $q active session$qs right now." ) );

$section->add1( $section2 = new PageSubSection( 'Sessions' ) );
$section2->add1( new HTMLBlock( '<form method="POST">' ) );
$section2->add1( $list = new UnorderedList() );

// do it, ...
if ( $_POST['kick'] ) {
	$k = auth_checks::pwd( $_POST['kick'] );
	$db->delete( 'users_sessions', "`key`='$k'" );
	$list->add1( new Paragraph( $db->effect() > 0 ? 'User kicked' : 'Key not found' ) );
}

$q = $db->select( 'users_sessions', '*' );
while ( $r = $db->fetch( $q ) ) {
	$list->add1( new Paragraph( '<input type="submit" name="kick" value="'.$r['key'].'" />' ) );
	// who operates it?
	$q2 = $auth->asName( $r['uid'] );
	$ip = long2ip( $r['ip'] );
	// when was it last operated?
	$t = date( DATE_RFC850, $r['time'] );
	$list->add1( new Paragraph( "Operated by $q2 ($ip)" ) );
	$list->add1( new Paragraph( $t ) );
}
$section->add1( new HTMLBlock( '</form>' ) );
