<?php define('IN_ACRMS', 1);require_once 'global.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Official ACR MasterServer!</title>
	<link rel="stylesheet" type="text/css" href="<?=$config['cdn'];?>s/style.css" media="screen,projection" />
</head>
<body>
<div id="container" class="clearfix">
	<h1><em>AssaultCube Reloaded</em> Official MasterServer</h1>
	<h2>the official master-server of assaultcube reloaded!</h2>

	<div id="content">
		<h3>Welcome</h3>

		<h4>An Advertisment</h4>
			<script type="text/javascript" src="//d.cdn.victorz.ca/a.php?a=acr_ms_head"></script>

		<h4>What this is</h4>
			<p>This is the official AssaultCube Reloaded MasterServer.<br/>Come play AssaultCube Reloaded!</p>

		<h4>Builtin Functions</h4>
			<p>Use <a href="/cube">/cube</a> for CubeScript server list<br/>
			Supported methods: /cube/(<a href="/cube/update">update</a>|<a href="/cube/list">list</a>|<a href="/cube/version">version</a>) or /cube*<br/>
			Clients are encouraged to have game version <?=$settings['currentgame']?></p>

			<!--<p>Use <a href="/json" >/json</a> for JSON server list</p>-->
			<p>We no longer provide a JSON server list. &#9785;<br/>
			Use <a href="/bans">/bans</a> for a list of banned IPs</p>

			<p>Your server may register on any port between <?=$settings['minport']?> and <?=$settings['maxport']?> if:<br/>
			your server is running protocol <?=$settings['minprotocol']?> or later.</p>

		<h4>Other sites</h4>
			<p>The main site is at <a href="http://acr.victorz.ca">http://acr.victorz.ca</a>. Download the game and join the fun if you don't already have it!</p>

			<p>Talk with the AssaultCube Reloaded community at <a href="http://forum.acr.victorz.ca">http://forum.acr.victorz.ca</a>.</p>

		<h4>Rules (.pdf downloads)</h4>
			<p>In order to ensure a decent gaming experience for everyone, we have created some rules. These rules can and may change anytime, and are provided here.</p>
			<p>
				The rules are administered by the AssaultCube Reloaded Task Force.<br />
				<b><a href="http://team.acr.victorz.ca/ms-rules.html">Click here to see/get/view them.</a></b>
			</p>
	</div>

	<ul id="nav">
		<li><a href='' title="Reload the page">Reload Page</a></li>
		<li><a href="http://team.acr.victorz.ca/ms-rules.html" title="Rules">The Rules!</a></li>
		<li><a href="/cube" title="CubeScript">CubeScript</a></li>
		<!--<li><a href="/json" title="JSON">JSON</a></li>-->
		<li><a href="/bans" title="Bans">IP Bans</a></li>
		<li><a href="http://acr.victorz.ca" title="Main Site">Main Site</a></li>
		<li><a href="http://forum.acr.victorz.ca" title="Forum">Forum Support</a></li>
	</ul>

</div>
<address>
	&copy; 2011 AssaultCube Reloaded (<a href="http://victorz.ca">Victor</a>) All Rights Reserved.
	W3C Valid
		<a href="http://validator.w3.org/check?uri=referer" target="_blank">XHTML 1.0</a> and
		<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_blank">CSS 2.1</a>!<br />
	Designed by <a href="http://www.caddoo.net" rel="nofollow">Matthew Caddoo</a> for <a href="http://www.zymic.com"  rel="nofollow">Zymic</a>'s <a href="http://www.zymic.com/free-templates" rel="nofollow">Free Templates</a>.
</address>
</body>
</html>