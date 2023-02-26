<?php
include 'init.php';

$ip = get_ip();
$port = isset($_GET['p']) ? (int) ($_GET['p']) : 28770;
$proto = isset($_GET['v']) ? (int) ($_GET['v']) : 0;
$gameVersion = isset($_GET['g']) ? (int) ($_GET['g']) : 0;
// ignore $_GET['guid32']

// are we open for business?
if (!$settings['autoapprove']) {
    die("automatic registration is closed");
}

// check bans
if (ip_in_list(inet_pton(ip4to6($ip)), $settings['bans_server']) !== false) {
    die("ERROR: your IP is blacklisted");
}

// check port
if ($port < 0) {
    die("ERROR: port must not be negative");
} elseif ($port >= 65535) {
    die("ERROR: port must be under 65535");
}

// check socket?
$sock_check = $proto >= $settings['minprotocol']
    && $proto <= $settings['curprotocol']
    && ($settings['check-socket'] || $settings['check-socket-force']);

if ($sock_check) {
    $sock = false;

    if ($fsock = @fsockopen("udp://$ip", $port + 1, $errno, $errstr, 3)) {
        stream_set_timeout($fsock, 3);
        fwrite($fsock, "1"); // standard ping: any char not equal to the null byte

        if (fread($fsock, 1)) {
            $sock = true; // if anything comes back...
        } // otherwise fail

        // clean up
        fclose($fsock);
    }
} else {
    // bypass the check
    $sock = true;
}
$sock_good = $sock || $settings['check-socket-force'];

// are we renewing?
$renew = $db->fetch_array($db->simple_select("acrms_servers", "failures", "ip='$ip' AND port=$port"));
if ($renew) {
    $failures = ($sock_good ? 0 :
        ($renew['failures'] == 255 ? 255
            : min($renew['failures'] + 1, $settings['check-socket'])));

    $db->update_query("acrms_servers", array(
        "time" => time(),
        "proto" => $proto,
        "failures" => $failures,
    ), "ip='$ip' AND port=$port");
} else { // register it
    $cache->update("acrms_servs", (int) $cache->read("acrms_servs") + 1);
    $db->insert_query("acrms_servers", array(
        "ip" => $ip,
        "port" => $port,
        "time" => time(),
        "proto" => $proto,
        "failures" => ($failures = ($sock_good ? 0 : 255)),
        "authtime" => 0,
    ));
}

// check for errors...
$error = false;
// check protocol
if ($proto < $settings['minprotocol']) {
    $error = "!!! UPDATE !!! You must update to a newer version!";
}

// output the final answer
$act = $renew ? "renewed" : "registered";
if ($error !== false) {
    $msg = "ERROR: $error - server not $act";
} else {
    $msg = "server $act";

    if ($proto < $settings['curprotocol']) {
        $msg .= ' (!!! UPDATE !!! new protocol ' . $settings['curprotocol'] . ' version ' . $settings['currentgame'] . ')';
    } elseif ($gameVersion < $settings['currentgame']) {
        $msg .= ' (!!! UPDATE !!! new version ' . $settings['currentgame'] . ')';
    }

    // check socket result
    if ($failures >= ($settings['check-socket'] || 255)) {
        $msg .= " -- ERROR {$settings['check-socket']}/{$settings['check-socket']} unreachable (UDP $port/" . ($port + 1) . ")";
    } else if (!$sock) {
        $failures_show = (!$failures || $failures == 255) ? '' : " $failures/{$settings['check-socket']}";
        $msg .= " -- WARNING$failures_show unreachable (UDP $port/" . ($port + 1) . ")";
    } elseif (!$sock_check && !$renew) {
        $msg .= " -- port-forward/firewall not checked";
    }
}
echo $msg;
