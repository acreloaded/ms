<?php
if ( !defined( 'IN_ADMINCP' ) ) die( 'Direct initialization of this file is not allowed.' );

class LoginPage {
	static function output_header() {
		global $config;
		$copy_year = COPY_YEAR;
		echo <<<HEAD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head profile="http://gmpg.org/xfn/1">
<title>ACRms Panel - Login</title>
<meta name="author" content="Victor" />
<meta name="copyright" content="Copyright {$copy_year} Victor." />
<meta name="robots" content="noindex, nofollow, noarchive" />
<meta name="googlebot" content="noarchive" />
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<link rel="stylesheet" href="{$config['cdn']}login/style.css" type="text/css" />
</head>
<body>
HEAD;
	}

	static function output_body( $info ) {
		echo <<<BODY1
<div id="container">
	<div id="header">
		<div id="logo"><h1><a href="./" title="Refresh"><span class="invisible">ACRms ACP</span></a></h1></div>
	</div>
<div id="content">
	<h2>Authentication Required</h2>
BODY1;
		if ( $info ) echo '<p id="message" class="'.$info[0].'"><span class="text">'.$info[1].'</span></p>';
		echo <<<BODY2
<p>Enter your authuser and authkey to continue.</p>
	<form method="post" action="index.php">
	<div class="form_container">

		<div class="label"><label for="authuser">authname:</label></div>

		<div class="field"><input type="text" name="usr" id="authuser" class="text_input initial_focus" /></div>

		<div class="label"><label for="authkey">authkey:</label></div>
		<div class="field"><input type="password" name="pwd" id="authkey" class="text_input" /></div>
	</div>
	<p class="submit">
		<span class="forgot_password">
			<a href="index.php?action=lostpw">Forgot your password?</a>
		</span>

		<input type="submit" value="Login" />
		<input type="hidden" name="do" value="login" />
	</p>
	</form>
</div>
</div>
BODY2;
	}

	static function output_footer() {
		echo <<<FOOT
</body>
</html>
FOOT;
	}

	static function output( $info = false ) {
		self::output_header();
		self::output_body( $info );
		self::output_footer();
	}
}
