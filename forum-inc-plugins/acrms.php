<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");

$plugins->add_hook("usercp_start", "acrms_usercp_start");
$plugins->add_hook("usercp_menu", "acrms_usercp_menu");

function acrms_info()
{
	return array(
		"name"          => "ACR Master-Server",
		"description"   => "The ACR Master-Server is now integrated into MyBB!",
		"website"       => "http://acr.victorz.ca",
		"author"        => "Victor (AssaultCube Reloaded Task Force)",
		"authorsite"    => "http://victorz.ca",
		"version"       => "1.0.0",
		//"guid"          => "6075380637e266aecd9b00a3aa99ce04",
		"compatibility" => "16*",
	);
}

function acrms_install()
{
	global $mybb, $db;
	// MS tables
	//$collation = $db->build_create_table_collation();
	$db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."acrms_servers` (
						`ip` varchar(45) NOT NULL,
						`port` smallint(5) unsigned NOT NULL,
						`time` bigint(20) unsigned NOT NULL,
						`proto` int(10) unsigned NOT NULL,
						`failures` tinyint(4) unsigned NOT NULL,
						`authtime` bigint(20) unsigned NOT NULL,
						PRIMARY KEY (`ip`,`port`)
					) ENGINE=MEMORY DEFAULT CHARSET=latin1;");
	$db->write_query("CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."acrms_auth` (
						`ip` varchar(45) NOT NULL,
						`port` smallint(5) unsigned NOT NULL,
						`id` int(10) unsigned NOT NULL,
						`time` bigint(20) unsigned NOT NULL,
						`nonce` int(11) NOT NULL,
						`uid` int(10) unsigned NOT NULL,
						PRIMARY KEY (`ip`,`port`,`id`)
					) ENGINE=MEMORY DEFAULT CHARSET=latin1;");
	// Key column
	$db->add_column("users", "acrms_key", "varchar(40) NOT NULL");
}

function acrms_is_installed()
{
	global $db;
	return $db->table_exists("acrms_servers");
}

function acrms_uninstall()
{
	global $mybb, $db;
	if($db->table_exists("acrms_servers"))
		$db->drop_table("acrms_servers");
	if($db->table_exists("acrms_auth"))
		$db->drop_table("acrms_auth");
	// Key column
	if($db->field_exists("acrms_key", "users"))
		$db->drop_column("users", "acrms_key");
}

function acrms_activate()
{
	global $mybb, $db;
	// Templates
	$ins = array(
		"tid"           => NULL,
		"title"         => 'usercp_acrms',
		"template"      => $db->escape_string(<<<ENDTEMPLATE
<html>
<head>
<title>ACR Master-Server Auth</title>
{\$headerinclude}
</head>
<body>
{\$header}
<form action="usercp.php?action=acrms" method="post">
<input type="hidden" name="my_post_key" value="{\$mybb->post_code}" />
<table width="100%" border="0" align="center">
<tr>
{\$usercpnav}
<td valign="top">
{\$errors}
<table border="0" cellspacing="{\$theme['borderwidth']}" cellpadding="{\$theme['tablespace']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>ACR Master Server</strong></td>
</tr>
<tr>
<td class="tcat" colspan="2"><strong>Key:</strong></td>
</tr>
<tr>
<td class="trow1" colspan="2" align="center"><input type="textbox" class="textbox" name="" size="80" value="{\$acrms_authkey}" style="text-align:center" readonly="readonly" /></td>
</tr>
<tr>
<td class="tcat" colspan="2"><strong>Install Command:</strong></td>
</tr>
<tr>
<td class="trow1" colspan="2" align="center"><input type="textbox" class="textbox" name="" size="80" value="/connectauth 1;authuser {\$mybb->user['uid']};authkey {\$acrms_authkey}" style="text-align:center" readonly="readonly" /></td>
</tr>
</table>
<br />
<div align="center">
<input type="hidden" name="action" value="do_acrms_regen" />
<input type="submit" class="button" name="submit" value="Regenerate" />
</div>
</td>
</tr>
</table>
</form>
{\$footer}
</body>
</html>
ENDTEMPLATE
),
		"sid"           => "-2",
		"version"       => $mybb->version + 1,
		"dateline"      => time(),
	);
	$db->insert_query("templates", $ins);
}

function acrms_deactivate()
{
	global $mybb, $db;
	// Templates
	$db->delete_query("templates", "title='usercp_acrms' AND sid='-2'");
}

function acrms_usercp_start()
{
	global $mybb, $db;

	// Regenerate an authkey?
	if($mybb->input['action'] == "do_acrms_regen" && $mybb->request_method == "post") {
		// Verify incoming POST request
		verify_post_check($mybb->input['my_post_key']);

		// Use some "random" data to build a new key
		$acrms_authkey = $namespace;
		$acrms_authkey .= $_SERVER['REQUEST_TIME'];
		$acrms_authkey .= $_SERVER['HTTP_USER_AGENT'];
		$acrms_authkey .= $_SERVER['LOCAL_ADDR'];
		$acrms_authkey .= $_SERVER['LOCAL_PORT'];
		$acrms_authkey .= $_SERVER['REMOTE_ADDR'];
		$acrms_authkey .= $_SERVER['REMOTE_PORT'];
		$acrms_authkey = sha1(uniqid("", true) . sha1($acrms_authkey));
		$db->update_query("users", array("acrms_key" => $acrms_authkey), "uid='{$mybb->user['uid']}'");
		redirect("usercp.php?action=acrms", "Your ACR Master-Server authkey was regenerated!");
	}
	else if($mybb->input['action'] == "acrms")
	{
		global $templates, $footer, $header, $navigation, $headerinclude, $themes, $usercpnav;
		// Make navigation
		add_breadcrumb($lang->nav_usercp, "usercp.php");
		add_breadcrumb('ACR Master-Server Auth');
		
		// Does the user have an authkey?
		if(isset($mybb->user["acrms_key"]) && $mybb->user["acrms_key"]) {
			// Get the authkey
			$acrms_authkey = $mybb->user["acrms_key"];
		} else {
			// Use a placeholder
			$acrms_authkey = '[Click the generate button]';
		}

		eval("\$output = \"".$templates->get("usercp_acrms")."\";");
		output_page($output);
	}
}

function acrms_usercp_menu()
{
	global $templates;

	$template = "\n\t<tr><td class=\"trow1 smalltext\"><a href=\"usercp.php?action=acrms\" class=\"usercp_nav_item usercp_nav_usergroups\">ACR MS Auth</a></td></tr>";
	$templates->cache["usercp_nav_misc"] = str_replace("<tbody style=\"{\$collapsed['usercpmisc_e']}\" id=\"usercpmisc_e\">", "<tbody style=\"{\$collapsed['usercpmisc_e']}\" id=\"usercpmisc_e\">{$template}", $templates->cache["usercp_nav_misc"]);
}
