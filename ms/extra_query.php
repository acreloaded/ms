<?php
include 'init.php';

// JSONP start
if (isset($_GET['c'])) {
    // prepend \r\n or /**/ for security reasons
    echo '\r\n' . $_GET['c'] . '(';
}

// Ensure there is a query
if (!isset($_GET['q'])) {
    $_GET['q'] = '';
}

// Query type: what do they want?
switch ($_GET['q']) {
    // Server count
    case 'servers':
        $sockcap = $settings['check-socket'] ? $settings['check-socket'] : 255;
        $q = $db->fetch_array($db->simple_select(
            "acrms_servers", "COUNT(*) As n", "failures < $sockcap AND proto >= {$settings['minprotocol']}"
        ));

        // Write output
        $json = array('active' => $q['n'], 'total' => (int) $cache->read("acrms_servs"));
        $json['hidden'] = $json['total'] - $json['active'];
        break;

    // JSON server list
    case 'json':
        $servers = array();

        $sockcap = $settings['check-socket'] ? $settings['check-socket'] : 255;
        $q = $db->simple_select("acrms_servers", "ip,port", "failures < $sockcap AND proto >= {$settings['minprotocol']}");
        while ($r = $db->fetch_array($q)) {
            $host = $r['ip'];
            $port = $r['port'];

            // Substitute hostname
            if (isset($settings['translations'][$host])) {
                $host = $settings['translations'][$host];
            }

            // Add to list
            $servers[] = "$host:$port";
        }

        $counts = array('active' => count($servers));

        $json = array('servers' => $servers, 'count' => $counts);
        break;

    // Unknown query
    default:
        $json = array('error' => 'invalid query');
        break;
}

// Write the JSON output
echo json_encode($json); // (object)

// JSONP end
if (isset($_GET['c'])) {
    echo ')';
}
