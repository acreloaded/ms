<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

$page->add( $section = new PageSection( $name ) );
$section->add1( new Paragraph( $remarks ) );
$section->add1( new HTMLBlock( '<form method="POST">' ) );
$section->add1( $section2 = new PageSubSection( 'List' ) );
$section2->add1( $list = new UnorderedList() );

// do edits...
$del = 0;
if ( is_array( $_POST['del'] ) ) {

	foreach ( $_POST['del'] as $val ) {
		$db->delete( $table, fromDel( $val ) );
		$e = $db->effect();
		if ( $e > 0 ) $del += $e;
	}
	$list->add1( new Paragraph( "$del ".( $del == 1 ? 'entry' : 'entries' ).' deleted' ) );
}
$insert = array();
$valid = true;
foreach ( $type as $k => $v ) {
	switch ( $v[1] ) {
	case 1: // string
		if ( $_POST[$k] == '' ) $valid = false;
		else $insert[$k] = $db->escape( $_POST[$k] );
		break;

	case 2: // IP
		if ( $_POST[$k] == '' ) $valid = false;
		else $insert[$k] = ip2long( $_POST[$k] );
		break;

	case 3: // bool
		// unchecked
		$insert[$k] = $_POST[$k] ? '1' : '0';
		break;

	case 4: // int
		if ( $_POST[$k] == '' ) $valid = false;
		else $insert[$k] = intval( $_POST[$k] );
		break;
	}

	if ( !$valid ) break;
}
if ( $valid ) {
	$db->insert( $table, $insert );
	$list->add1( new Paragraph( $db->effect() > 0 ? 'Entry added' : 'Entry addition failed' ) );
}
if ( $table == 'bans_nick' && ( $del || $valid ) )
	$cache->put( 'nb', false );

$q = $db->select( $table, '*', '', $sort );
$i = 0;
while ( $r = $db->fetch( $q ) ) {
	$edit = ' (<input type="checkbox" name="del[]" value="'.toDel( $r ).'"/> Delete)';
	$list->add1( new Paragraph( toEntry( $r ).$edit ) );
	++$i;
}
if ( !$i ) $list->add1( new Paragraph( 'No entries found' ) );

if ( true ) {
	$section->add1( $section2 = new PageSubSection( 'Add a new entry' ) );
	$section2->add1( $list = new UnorderedList() );
	foreach ( $type as $key => $val ) {
		list( $name, $t ) = $val;
		switch ( $t ) {
			//case 1: // string
			//case 2: // IP
			//case 4: // int
		default:
			$f = "<input name=\"$key\"/>";
			break;

		case 3: // bool
			$f = "<input name=\"$key\" type=\"checkbox\"/>";
			break;
		}
		$list->add1( new Paragraph( "<label>$name: $f</label>" ) );
	}

	$section->add1( $section2 = new PageSubSection( 'Save' ) );
	$section2->add1( new Paragraph( '<input type="submit" value="Save Changes"/> <input type="reset" value="Reset"/>' ) );

	$section->add1( new HTMLBlock( '</form>' ) );
}
