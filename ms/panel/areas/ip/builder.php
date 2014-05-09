<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$canedit = $type >= 1;

$page->add( $section = new PageSection( $table == 'allow_ip' ? 'IP Exceptions' : 'Ban List' ) );
if ( $canedit ) $section->add1( new HTMLBlock( '<form method="POST">' ) );
$section->add1( $section2 = new PageSubSection( 'List' ) );
$section2->add1( $list = new UnorderedList() );

if ( $canedit ) {
	// do edits...
	if ( is_array( $_POST['del'] ) ) {
		$del = 0;
		foreach ( $_POST['del'] as $val ) {
			$v = explode( '-', $val );
			$ipl = intval( $v[0] );
			$ipr = intval( $v[1] );
			if ( $ipr <= $ipl ) $ipr = $ipl; // constrain IP range
			$extra = $type >= 2 ? '' : " AND owner={$auth->data->id}";
			$db->delete( $table, "`ipl`=$ipl AND `ipr`=$ipr$extra" );
			$e = $db->effect();
			if ( $e > 0 ) $del += $e;
		}
		$list->add1( new Paragraph( "$del ".( $del == 1 ? 'entry' : 'entries' ).' deleted' ) );
	}
	if ( $_POST['ipl'] && $_POST['ipr'] ) {
		$db->insert( $table, array(
				'ipl' => ip2long( $_POST['ipl'] ),
				'ipr' => ip2long( $_POST['ipr'] ),
				'owner' => $auth->data->id,
				'reason' => preg_replace( "#[^a-zA-Z0-9_! -]#", '', $_POST['reason'] ),
			) );
		$list->add1( new Paragraph( $db->effect() > 0 ? 'Entry added' : 'Entry addition failed' ) );
	}
}

$q = $db->select( $table, '*', $type == 1 ? "owner={$auth->data->id}" : '', 'ipl ASC, ipr ASC' );
$i = 0;
while ( $r = $db->fetch( $q ) ) {
	$ipl = long2ip( $r['ipl'] );
	$ipr = long2ip( $r['ipr'] );
	$reason = $r['reason'];

	$q2 = $auth->asName( $r['owner'] );

	$edit = '';
	if ( $canedit )
		$edit = ' (<input type="checkbox" name="del[]" value="'.$r['ipl'].'-'.$r['ipr'].'"/> Delete)';
	$list->add1( new Paragraph( "$ipl - $ipr by $q2 (for $reason)$edit" ) );
	++$i;
}
if ( !$i ) $list->add1( new Paragraph( 'No entries found' ) );

if ( $canedit ) {
	$section->add1( $section2 = new PageSubSection( 'Add a new IP' ) );
	$section2->add1( $list = new UnorderedList( array(
				new Paragraph( '<label>Left IP: <input name="ipl" /></label>' ),
				new Paragraph( '<label>Right IP: <input name="ipr" /></label>' ),
				new Paragraph( '<label>Reason: <input name="reason" /></label>' ),
			) ) );

	$section->add1( $section2 = new PageSubSection( 'Save' ) );
	$section2->add1( new Paragraph( '<input type="submit" value="Save Changes"/> <input type="reset" value="Reset"/>' ) );

	$section->add1( new HTMLBlock( '</form>' ) );
}
