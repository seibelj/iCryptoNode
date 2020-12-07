<?php

define('ICRYPTONODE_VERSION', 5);

define('DEBUG_ENABLED', true);
define('APP_ROOT', __DIR__);
define('SYSTEM_CMD_DIR', APP_ROOT . '/system_commands');
define('API_ROOT', "/index.php");

define('VPN_DIR', APP_ROOT . '/vpn');
define('VPN_CMD_DIR', VPN_DIR . '/commands');

define('NODE_TYPE', trim(preg_replace('/\s\s+/', '', shell_exec( 'sudo uci get icryptonode.@info[0].node_type' ))));
define('NODE_CMD', APP_ROOT . '/node_commands/' . NODE_TYPE);

define('UPDATE_ENDPOINT', 'https://updates.icryptonode.com');
define('TESTMODE_ENABLED', false);

define('DAEMON_DIR', '/etc/icryptonode/daemon');
define('FIX_DAEMON_DIR_SCRIPT', '/etc/icryptonode/fix_folders.sh');
define('DAEMON_DOWNLOAD_PATH', '/etc/icryptonode');
define('BLOCKCHAIN_DIR', '/etc/icryptonode/blockchain');

define('GNUPG_HOME', '/etc/icryptonode/.gnupg');
define('GPG_FINGERPRINT', "EDE83F81A48109E1911865009FD65527370A7166");

define('ICRYPTONODE_UPDATE_FILE', '/etc/icryptonode/icn.tar.gz');
?>