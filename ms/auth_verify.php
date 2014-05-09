<?php
include "init.php";
require_once MYBB_ROOT."inc/functions_user.php";

$ip = preg_replace("#[^a-f0-9.:%/]#", "", strtolower(get_ip()));
$port = isset($_GET['port']) ? (int)($_GET['port']) : 0;

$id = isset($_GET['id']) ? (int)($_GET['id']) : 0;
$answer = isset($_GET['hash']) ? $_GET['hash'] : '';

// are they unregistered?
$q = $db->fetch_array($db->simple_select("acrms_servers", "COUNT(*) AS n, authtime", "ip='$ip' AND port=$port"));
if(!$q['n'])
	exit("*f"); // auth verify fail - unregistered

// fetch it
$q = $db->fetch_array($db->simple_select("acrms_auth", "nonce,uid", "ip='$ip' AND port=$port AND id=$id"));
// does it NOT already exist?
if(!$q)
	exit("*f"); // auth not found

// do auth
// delete entry
$db->delete_query("acrms_auth", "ip='$ip' AND port=$port AND id=$id");

// is the user still valid?
if(!user_exists($q['uid']))
	exit("*f"); // auth user disappeared

// get the user's key
$info = get_user($q['uid']);

if($answer == sha1("{$q['uid']}:{$info['acrms_key']}!{$q['nonce']}")) {
	// get the user's privilege
	$priv = 0;
	if(is_super_admin($q['uid']))
		$priv = 3;
	else {
		$user_perms = user_permissions($q['uid']);
		if($user_perms['cancp'])
			$priv = 2;
		elseif($user_perms['issupermod'])
			$priv = 1;
	}
	// match
	echo "*s{$priv}{$info['username']}"; // auth pass
}
else
	// no match
	echo "*d"; // auth mismatch
