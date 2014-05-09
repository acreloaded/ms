<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

// dependencies
require_once 'class_page_head.php';
require_once 'class_page_foot.php';
require_once 'class_menu.php';
require_once 'class_sidebar.php';
require_once 'class_rightbar.php';
require_once 'class_pagebody.php';

class AdminPage {
	// parts
	public $head, $foot;
	public $menu, $sidebar, $rightbar, $body;
	// storage
	public $active_area, $active_module, $active_action;
	// some stuffs
	public $user = 'nobody';
	public $role = 'guest';

	function __construct() {
		$active_area = 'home';
		$this->head = new PageHead;
		$this->foot = new PageFoot;
		$this->menu = new Menu;
		$this->sidebar = new Sidebar;
		$this->rightbar = new Rightbar;
		$this->body = new PageBody;
	}

	function add( PageSection $b ) {
		$this->body->add_section( $b );
	}

	function output() {
		$opts = array(
			'area' => $this->active_area,
			'module' => $this->active_module,
		);
		$renders = array( 'head', 'foot', 'body', 'menu', 'sidebar', 'rightbar' );
		foreach ( $renders as $render ) $$render = $this->$render->output( $opts );
		echo <<<PAGE
$head
<!-- wrap starts here -->
<div id="wrap">
	<div id="header">
		<h1 id="logo">acr <span class="orange">panel</span></h1>
		<h2 id="slogan">AssaultCube Reloaded's Official Master Server Panel&hellip;</h2>
		<form method="get" class="searchform" action="index.php">
			<p style="color:#FFF"><br /> Welcome back, {$this->user} ({$this->role})

			<input type="hidden" name="action" value="logout" />
  			<input type="submit" class="button" value="Log out" /></p>
		</form>
	</div>
	<div id="menu">$menu</div>
	<!-- content-wrap starts here -->
	<div id="content-wrap">
		<div id="sidebar">$sidebar</div>
		<div id="main">$body</div>
		<div id="rightbar">
			$rightbar
		</div>
	<!-- content-wrap ends here -->
	</div>
<!-- wrap ends here -->
</div>
$foot
PAGE;
	}
}
