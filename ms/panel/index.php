<?php

define( 'IN_ACRMS', 1 );
define( 'IN_ADMINCP', 1 );

// load globals
require_once dirname( dirname( __FILE__ ) ).'/global.php';

// some defines
define( 'ACRMS_ADMIN_DIR', dirname( __FILE__ ).'/' );
define( 'COPY_YEAR', date( 'Y' ) );

// send our headers
header( 'Cache-Control: no-cache' );
header( 'Pragma: no-cache' );

// check login state
require_once ACRMS_ROOT.'/inc/class_auth_full.php';
$auth = new auth;
$requirelogin = false;

// do they want to auth?
if ( $_GET['action'] == 'lostpw' )
	$requirelogin = array( 'success', "Well, that's too bad for you." );
else if ( $_GET['action'] == 'logout' && $auth->logout() )
	$requirelogin = array( 'success', 'You have been logged out successfully.' );
else if ( $_POST['do'] == 'login' ) {
		// the message can be bypassed later
		$requirelogin = array( 'error', 'The username and password combination you entered is invalid.' );
		// login!
		$auth->login( $_POST['usr'], $_POST['pwd'] );
	}

// are we logged in?
// login OK
if ( $auth->authed() ) { // easier
	//if($requirelogin === null){ // note the 3 equal signs
	include_once ACRMS_ADMIN_DIR.'inc/class_page.php';
	$page = new AdminPage;
	// our top menu (areas)
	$allowed_areas = array(
		'home' => 'Home',
		'user' => 'Inferior User CP',
		'ip' => 'IP Control',
		'admin' => 'Advanced Panel',
		'owner' => 'Superior Supreme Settings',
	);
	$page->menu->blocks = &$allowed_areas;
	// some info
	$page->user = $auth->data->name;
	$page->role = $auth::privname( $auth->data->level );
	// sanitize it
	$page->active_area = $_GET['area'];
	if ( !array_key_exists( $page->active_area, $allowed_areas ) ) $page->active_area = 'home';
	// include it
	$page->active_module = '_init';
	$enforce = 0; // by default all users can use modules
	include_once ACRMS_ADMIN_DIR."areas/{$page->active_area}/{$page->active_module}.php";
	// with the module
	if ( $auth->reaches( $enforce ) )
		include_once ACRMS_ADMIN_DIR."areas/{$page->active_area}/{$page->active_module}.php";
	else
		$page->add( new PageSection( 'Access Denied', new Paragraph( 'You need to be at least '.$auth::privname( $enforce ).' to use this module.' ) ) );
	// globals
	$quotes = array(
		'Mark Twain' => 'Keep away from people who try to belittle your ambitions. Small people always do that, but the really great make you feel that you, too, can become great.',
		'John F. Kennedy' => 'Forgive your enemies, but never forget their names.',
		'John Muir' => 'The power of imagination makes us infinite.',
		'Confucius' => 'A journey of a thousand miles begins with a single step.',
	);
	$quote = array_rand( $quotes );
	$page->rightbar->add_quote( 'Wise Words', $quote, $quotes[$quote] );
	$page->rightbar->add( 'Ads', 'The only thing supporting this project. <script type="text/javascript" src="http://cdn.victorz.tk/ads.php?a=w"></script>' );
	// output the result!
	$page->output();
}
// we need a login!
else {
	include_once ACRMS_ADMIN_DIR.'areas/login.php';
	LoginPage::output( $requirelogin );
}
