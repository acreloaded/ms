<?
	require "config.php"; // get our configuration
	require_once "config.auth.php"; // auths
	require_once "config.bans.php"; // banning sysrem
	
	require_once "inc/ip.php"; // functions to detect our IP
	require_once "inc/cron.php"; docron(); // take care of the tasks
	
	function isbanned($ip, $mask){
		global $config;
		$banned = false;
		foreach($config['sbans'] as $b) if($b[0] <= $ip && $ip <= $b[1] && $b[2] & $mask){ $banned = true; break; }
		foreach($config['sallows'] as $b) if($b[0] <= $ip && $ip <= $b[1] && $b[2] & $mask){ $banned = false; break; }
		return $banned;
	}
	
	function getServers(){ // {string, int, bool, int}[] (void)
		global $config;
		$buffer = array();
		$q = mysql_query("SELECT `ip`, `port`, `add` FROM `{$config['db']['pref']}servers`");
		$q2 = mysql_query("SELECT `add` FROM `{$config['db']['pref']}servers` WHERE `add`");
		foreach($config['servers']['force'] as $srv){ // forced servers
			preg_match("/([^:]+):([0-9]{1,5})/", $srv, $s);
			$buffer[] = array($s[1], $s[2], true);
		}
		if(!mysql_num_rows($q2)) $buffer[] = array($config['servers']['placeholder'], $config['servers']['defaultport'], true);
		while ($r = mysql_fetch_row($q)){
			$io = $ip = $r[0];
			$i = long2ip($io);
			foreach($config['servers']['translate'] as $t) if($ip == $t[0] && (!$t[2] || $r[1] == $t[2])){
				$i = $t[1];
				break;
			}
			$buffer[] = array($i, $r[1], (bool)$r[2], $io); // {long2ip2domain($r[ip]), $r[port], (bool)$r[add], $r[ip]}
		}
		return $buffer;
	}
	// clients
	if(isset($_GET['cube'])){ // cubescript
		header('Content-type: text/plain');
		$srvs = getServers();
		$lines = array("current_version {$config['servers']['currentgame']} {$config['servers']['minprotocol']}");
		foreach($srvs as $s){
			$wt = '';
			foreach($config['servers']['weights'] as $w) if($w[0] == $s[3] && (!$w[2] || $w[2] == $s[1])){
				if($w[1]) $wt = ' '.$w[1];
				break;
			}
			$lines[] = ($s[2] ? '' : "//")."addserver {$s[0]}".($wt || $s[1] != $config['servers']['defaultport'] ? " {$s[1]}{$wt}" : "");
		}
		echo implode("\n", $lines);
	}
	
	// servers
	elseif(isset($_GET['register'])){ // register
		function addserver($ip, $port, $add){ // returns if it is renewed
			global $config;
			if($add) mysql_query("DELETE FROM `{$config['db']['pref']}auth` WHERE `ip`={$ip}"); // clear auth server
			if($q = mysql_num_rows(mysql_query("SELECT `port` FROM `{$config['db']['pref']}servers` WHERE `ip`={$ip} AND `port`={$port}"))){ // renew
				mysql_query("UPDATE `{$config['db']['pref']}servers` SET `time`=".time().", `add`=".($add ? 1 : 0)." WHERE `ip`={$ip} AND `port`={$port}");
				return true;
			}
			else{ // register
				mysql_query("INSERT INTO `{$config['db']['pref']}servers` (`ip`, `port`, `time`, `add`) VALUES ({$ip}, {$port}, ".time().", ".($add ? 1 : 0).")");
				return false;
			}
		}
		if($config['servers']['autoapprove'] === false) exit("Automatic registration is closed. {$config['contact']}");
		$ip = getiplong();
		// check bans
		if(isbanned($ip, 2)) exit("You are not authorized to register a server. {$config['contact']}");
		// check port
		$port = intval($_GET['port']);
		if($port < $config['servers']['minport'] || $port > $config['servers']['maxport']){
			addserver($ip, $port, false);
			exit("You may only register a server with ports between {$config['servers']['minport']} and {$config['servers']['maxport']}");
		}
		
		// check protocol
		if($config['servers']['minprotocol'] > $_GET['proto']){
			addserver($ip, $port, false);
			exit("!!!UPDATE NOW!!!! You must run a server at least protocol {$config['servers']['minprotocol']}. {$config['contact']}");
		}
		
		// good: can register
		
		if($config['servers']['check-socket']){
			// sockets pwn!
			function nosock($port, $p1pass = false){
				global $ip;
				addserver($ip, $port, false);
				exit("Your server is unreachable. Please make sure UDP port".($p1pass ? " " : "s ".$port." and ").($port + 1)." ".($p1pass ? "is" : "are")." properly forwarded and reachable.");
			};
			// noob socket 101
			$sock = fsockopen("udp://".getip(), $port, $errno, $errstr, 2);
			if(!$sock) nosock($port); // lazy test doesn't always catch it
			fclose($sock);
			$sock = fsockopen("udp://".getip(), $port + 1, $errno, $errstr, 3);
			if(!$sock) nosock($port);
			stream_set_timeout($sock, 3);
			fwrite($sock, "1"); // "standard ping is not equal to the null byte"
			if(!fread($sock, 1)) nosock($port, true); // if anything comes back...
			fclose($sock);
		}
		
		// connect_db(); // already connected from cronjobs
		// find server
		if(addserver($ip, $port, true)) // renewed
			echo 'Your server has been renewed.';
		else // registered
			echo 'Your server has been registered.'.($config['servers']['check-socket'] ? "" : " We cannot verify if your server is reachable.");
	}
	
	// compulsory auth
	elseif(isset($_GET['connectcheck'])){ // connect checks
		$ip = intval($_GET['id']);
		$nick = base64_decode(str_replace(array('-', '_'), array('+', '/'), $_GET['nick']));
		
		// check ip bans
		if(isbanned($ip, 1)) exit("*bi");
		
		// check nick whitelist
		foreach($config['snickw'] as $w){
			$w = (array) $w;
			$callback = $w[1] ? "strcasecmp" : "strcmp";
			if(!$callback($nick, $w[0])){
				if($w['password']) exit("*bp"); // needs further auth
				if($w['ip']){
					$wiprs = (array)$w['ip'];
					$iprok = false;
					foreach($wiprs as $ipr) if($ip >= $ipr[0] && $ip <= $ipr[1]) { $iprok = true; break; }
					if(!$iprok) exit("*bI"); // ip fail
				}
				exit("*bw"); // OK
			}
		}
		// check nick blacklist
		foreach($config['snickb'] as $b){
			$b = (array) $b;
			$match = false;
			$fullmatch = true;
			$callback = $b['nocase'] ? "stripos" : "strpos";
			unset($b['nocase']);
			foreach($b as $n){
				if($callback($nick, $n) === false){
					$fullmatch = false;
					break;
				}
				else $match = true;
			}
			if($match && $fullmatch) exit("*bn"); // nickname is blacklisted
		}
		
		echo "*a"; // default
	}
	
	// auth
	elseif(isset($_GET['authreq'])){ // request auth
		$ip = getiplong();
		$q = mysql_num_rows(mysql_query("SELECT `ip` FROM `{$config['db']['pref']}servers` WHERE `ip`={$ip}"));
		if(!$q) exit("*f{$id}");
		$id = intval($_GET['id']);
		$q = mysql_num_rows(mysql_query("SELECT `id` FROM `{$config['db']['pref']}auth` WHERE `ip`={$ip} AND `id`={$id}"));
		if($q) exit("*f{$id}");
		
		$q = mysql_query("SELECT `time` FROM `{$config['db']['pref']}authtimes` WHERE `ip`={$ip}");
		if(!mysql_num_rows($q)) mysql_query("INSERT INTO `{$config['db']['pref']}authtimes` (`ip`, `time`) VALUES ({$ip}, ".time().")");
		else if(mysql_result($q, 0, 0) + 1 >= time()) exit("*f{$id}");
		else mysql_query("UPDATE `{$config['db']['pref']}authtimes` SET `time`=".time()." WHERE `ip`={$ip}");
		
		$nonce = mt_rand(0, 2147483647); // 31-bits signed
		mysql_query("INSERT INTO `{$config['db']['pref']}auth` (`ip`, `time`, `id`, `nonce`) VALUES ({$ip}, ".time().", {$id}, {$nonce})");
		echo "*c{$id}|".$nonce;
	}
	elseif(isset($_GET['authchal'])){ // answer auth
		$ip = getiplong();
		$q = mysql_num_rows(mysql_query("SELECT `ip` FROM `{$config['db']['pref']}servers` WHERE `ip`={$ip}"));
		if(!$q) exit("*f{$id}");
		$id = intval($_GET['id']);
		$q = mysql_num_rows(mysql_query("SELECT `id` FROM `{$config['db']['pref']}auth` WHERE `ip`={$ip} AND `id`={$id}"));
		if(!$q) exit("*f{$id}");
		$q = mysql_result(mysql_query("SELECT `nonce` FROM `{$config['db']['pref']}auth` WHERE `ip`={$ip} AND `id`={$id}"), 0, 0);
		mysql_query("DELETE FROM `{$config['db']['pref']}auth` WHERE `ip`={$ip} AND `id`={$id}"); // used auth
		$ans = &$_GET['ans'];
		foreach($config['auth'] as $authkey) if($ans == sha1($q.':'.$authkey[0])) exit("*s{$id}|".$authkey[2].$authkey[1]); // match
		echo "*d{$id}"; // no match
	}
	
	// user output
	elseif(isset($_GET['json'])){ // JSON
		$buf = getServers();
		foreach($buf as &$s) $s = array("server" => $s[0], "port" => $s[1], "ip" => long2ip($s[3]), "ipd" => $s[3]); // automatically converted to object...
		echo json_encode($buf);
	}
	elseif(isset($_GET['bans'])){ // list bans
		function banflag2s($f){ return ($f&1 ? "playing" : "").($f&3 ? " and " : "").($f&2 ? "registering a server" : ""); } // FIXME
		$ip = getiplong();
		$banned = $allowed = 0;
		foreach($config['sbans'] as $r){
			if($r[0] <= $ip && $ip <= $r[1]){ ++$banned; echo "*"; }
			echo "ban ".long2ip($r[0]).($r[0] != $r[1] ? " - ".long2ip($r[1]) : '')." from ".banflag2s($r[2]).($r[3] ? "; remarks: ".$r[3] : "")."<br>\n";
		}
		foreach($config['sallows'] as $r){
			if($r[0] <= $ip && $ip <= $r[1]){ ++$allowed; echo "*"; }
			echo "allow ".long2ip($r[0]).($r[0] != $r[1] ? " - ".long2ip($r[1]) : '')." to be ".banflag2s($r[2]).($r[3] ? "; remarks: ".$r[3] : "")."<br>\n";
		}
		echo "Your IP is: ".long2ip($ip)." which is banned {$banned} times and allowed {$allowed} times<br>\nThat is all.";
	}
	else include "index-ms.php";
?>