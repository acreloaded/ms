<?
	// general
	$config['contact'] = 'Please contact the master-server administrator.'; // include some form of contact
	// servers
	$config['servers']['minprotocol'] = 106; // protocol requirements
	$config['servers']['currentgame'] = 230; // broadcast this as the current version
	$config['servers']['autoapprove'] = true; // servers can be registered
	$config['servers']['translate'] = array( // IP translation
		// array(ip, "domain", port), // use port 0 or omit for wildcard
);
	$config['servers']['force'] = array( // always forced if it cannot register
		// "server:port",
);
	$config['servers']['weights'] = array( // only for cubelist
		// array(ip, weight, port), // use port 0 or omit for wildcard
	);
	$config['servers']['defaultport'] = 28770;
	$config['servers']['placeholder'] = "localhost"; // dummy when out of servers
	$config['servers']['minport'] = 1024; // end of primary reserved ports
	$config['servers']['maxport'] = 65534; // 65535 is the max
	$config['servers']['check-socket'] = false; // check server with UDP sockets or not

	// database
	$config['db']['host'] = ''; // database host (domain or IP)
	$config['db']['name'] = ''; // database name
	$config['db']['user'] = ''; // database user
	$config['db']['pass'] = ''; // database pass
	$config['db']['pref'] = 'cubems_'; // table prefix
	
	function connect_db(){ // global function to connect to the database
		global $config;
		$r = mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['pass']);
		mysql_select_db($config['db']['name'], $r);
		return $r;
	}
?>