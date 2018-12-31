<?php
class Updates
{

    /**
     * Endpoint for getting the raw update config
     * @url GET update_config
     *
     * @return json
    */
    public function update_config() {
        return $this->get_update_config();
    }

    /**
     * Endpoint for getting update information
     *
     * @url GET update_info
     *
     * @return json
    */
    public function update_info() {

        $response = array(
            'has_update' => false,
            'size' => 0,
            'version_short' => "",
            'version_long' => "",
            'download_url' => "",

            'icryptonode_has_update' => false,
            'icryptonode_size' => 0,
            'icryptonode_version_short' => "",
            'icryptonode_download_url' => ""
        );

        $update_config = $this->get_update_config();

        $response['size'] = $update_config[NODE_TYPE]['size'];
        $response['version_short'] = $update_config[NODE_TYPE]['daemon_version_short'];
        $response['version_long'] = $update_config[NODE_TYPE]['daemon_version_long'];
        $response['download_url'] = $update_config[NODE_TYPE]['url'];

        $current_daemon_version = (int)shell_exec( 'sudo uci get icryptonode.@info[0].daemon_version' );

        if ($update_config[NODE_TYPE]['daemon_version'] > $current_daemon_version) {
            $response['has_update'] = true;
        }

        $response['icryptonode_size'] = $update_config["iCryptoNode"]['size'];
        $response['icryptonode_version_short'] = $update_config["iCryptoNode"]['version_short'];
        $response['icryptonode_download_url'] = $update_config["iCryptoNode"]['url'];

        if ($update_config["iCryptoNode"]['version'] > ICRYPTONODE_VERSION) {
            $response['icryptonode_has_update'] = true;
        }

        return $response;
    }

    /**
     * Endpoint for deleting corrupt files so users don't get stuck
     *
     * @url POST clear_downloads
     *
     * @return json
    */
    public function clear_downloads() {

        $response = array(
            'success' => true,
            'daemon_download' => false,
            'icn_download' => false
        );

        // Re-enable crons if disabled because of some weird issue
        shell_exec( "sudo uci set icryptonode.@info[0].is_updating='no'; sudo uci commit" );

        $update_config = $this->get_update_config();

        $daemon_download_path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config[NODE_TYPE]['url']);
        
        if (file_exists( $daemon_download_path )) {
            unlink($daemon_download_path);
            $response['daemon_download'] = true;
        }

        $icn_download_path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config["iCryptoNode"]['url']);
        
        if (file_exists( $icn_download_path )) {
            unlink($icn_download_path);
            $response['icn_download'] = true;
        }

        return $response;
    }

    /**
     * Endpoint for force-allowing updates in case of some sort of corruption
     *
     * @url POST force_allow_update
     * @param string update_type
     *
     * @return json
    */
    public function force_allow_update($update_type) {

        $response = array(
            'success' => true
        );

        // Update UCI
        if ($update_type == "daemon") {
            shell_exec( "sudo uci set icryptonode.@info[0].daemon_version='0'; sudo uci commit" );
        }
        else {
            throw new Exception('ERROR: UNKNOWN UPDATE TYPE');
        }

        return $response;
    }

    /**
     * Endpoint for getting the amount downloaded which can be compared on client.
     * Quick response because no need to re-fetch the update config.
     *
     * @url POST download_status
     *
     * @param string file_url
     * @return json
    */
    public function download_status($file_url) {

        $response = array(
            'downloaded' => 0
        );
        $path = DAEMON_DOWNLOAD_PATH . '/' . basename($file_url);
        $file_size = filesize ( $path );

        if ($file_size) {
            $response['downloaded'] = $file_size;
        }

        return $response;
    }

    /**
     * Endpoint for validating daemon download
     * @url GET verify_daemon_download
     *
     * @return json
    */
    public function verify_daemon_download() {
        $update_config = $this->get_update_config();
        $total_size = $update_config[NODE_TYPE]['size'];

        $response = array(
            'downloaded' => 0,
            'total' => $total_size,
            'valid' => false
        );
        $path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config[NODE_TYPE]['url']);

        $file_size = false;
        
        if (file_exists( $path )) {
            $file_size = filesize( $path );
        }
        
        if ($file_size) {
            $response['downloaded'] = $file_size;
        }

        if ($file_size == $total_size) {
            $response['valid'] = $this->validate_daemon_download($update_config);
        }

        return $response;
    }

    /**
     * Endpoint for validating iCryptoNode download
     * @url GET verify_icn_download
     *
     * @return json
    */
    public function verify_icn_download() {
        $update_config = $this->get_update_config();
        $total_size = $update_config["iCryptoNode"]['size'];

        $response = array(
            'downloaded' => 0,
            'total' => $total_size,
            'valid' => false
        );
        $path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config["iCryptoNode"]['url']);

        $file_size = false;
        
        if (file_exists( $path )) {
            $file_size = filesize( $path );
        }
        
        if ($file_size) {
            $response['downloaded'] = $file_size;
        }

        if ($file_size == $total_size) {
            $response['valid'] = $this->validate_icn_download($update_config);
        }

        return $response;
    }

    /**
     * Endpoint for removing old version and installing new version of daemon
     *
     * @url POST install_daemon
     *
     * @return json
    */
    public function install_daemon() {
        $update_config = $this->get_update_config();

        $response = array('success' => false);
        
        if (!$this->validate_daemon_download($update_config)) {
            return $response;
        }

        // Block crons
        shell_exec( "sudo uci set icryptonode.@info[0].is_updating='yes'; sudo uci commit" );

        // Stop the daemon if it's running
        shell_exec( 'sudo ' . NODE_CMD . ' stop' );
        sleep(6);

        $daemon_compressed_path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config[NODE_TYPE]['url']);
        
        if ($this->endsWith($daemon_compressed_path, '.tar.bz2')) {
            shell_exec( "sudo " . SYSTEM_CMD_DIR . "/update_daemon " . escapeshellarg($daemon_compressed_path) );
        }
        else {
            throw new Exception('ERROR: UNKNOWN FILE EXTENSION');
        }

        // If extracted with 1 parent folder, we fix to normalize paths
        shell_exec( FIX_DAEMON_DIR_SCRIPT . ' ' . DAEMON_DIR . ' ' . NODE_TYPE );

        // Update UCI
        shell_exec( "sudo uci set icryptonode.@info[0].daemon_version='" . $update_config[NODE_TYPE]['daemon_version'] . "'; sudo uci commit" );

        // Remove downloaded file
        unlink($daemon_compressed_path);

        // Re-enable crons
        shell_exec( "sudo uci set icryptonode.@info[0].is_updating='no'; sudo uci commit" );

        // If the daemon was enabled, will start within 60 seconds automatically via cron
        $response['success'] = true;
        return $response;
    }

    /**
     * Endpoint for installing new version of ICN - which happens via the cron job.
     * This puts the file in the right location, then the cron installs it
     *
     * @url POST install_icn
     *
     * @return json
    */
    public function install_icn() {
        $update_config = $this->get_update_config();
        
        if (!$this->validate_icn_download($update_config)) {
            return array('success' => false);
        }

        $path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config["iCryptoNode"]['url']);

        shell_exec( "mv " . $path . " " . ICRYPTONODE_UPDATE_FILE );

        return array('success' => true);
    }

    /**
     * Endpoint for starting the download of a new daemon
     * @url GET download_daemon
     *
     * @return json
    */
    public function download_daemon() {

        $update_config = $this->get_update_config();
        $current_daemon_version = (int)shell_exec( 'sudo uci get icryptonode.@info[0].daemon_version' );

        $update_available = false;
        if ($update_config[NODE_TYPE]['daemon_version'] > $current_daemon_version) {
            $update_available = true;
            $this->download_file($update_config[NODE_TYPE]['url']);
        }
        // Hack for local-server testing
        // https://stackoverflow.com/a/30311555
        // header("Content-length: 27");
        return array('downloading' => $update_available);
    }

    /**
     * Endpoint for starting the download of iCryptoNode update
     * @url GET download_icn
     *
     * @return json
    */
    public function download_icn() {

        $update_config = $this->get_update_config();

        $this->download_file($update_config["iCryptoNode"]['url']);
        // Hack for local-server testing
        // https://stackoverflow.com/a/30311555
        // header("Content-length: 27");
        return array('downloading' => true);
    }

    // Instantly return
    private function download_file($url) {
        exec('wget -bq -O ' . DAEMON_DOWNLOAD_PATH . '/' . basename($url) . ' ' . $url . ' --timeout=600 --limit-rate=1m >/dev/null 2>&1 &');
    }

    private function validate_daemon_download($update_config) {
        $correct_hash = $update_config[NODE_TYPE]['sha256'];
        $path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config[NODE_TYPE]['url']);
        $file_hash = $this->sha256($path);

        if ($file_hash == $correct_hash) {
            return true;
        }
        else {
            return false;
        }
    }

    private function validate_icn_download($update_config) {
        $correct_hash = $update_config["iCryptoNode"]['sha256'];
        $path = DAEMON_DOWNLOAD_PATH . '/' . basename($update_config["iCryptoNode"]['url']);
        $file_hash = $this->sha256($path);

        if ($file_hash == $correct_hash) {
            return true;
        }
        else {
            return false;
        }
    }

    // Works for large files by reading in chunks
    private function sha256($path) {
        $ctx = hash_init('sha256');

        $file = fopen($path, 'r');

        if (!$file) {
            return "DEADBEEF";
        }
        
        while(!feof($file)){
            $buffer = fgets($file, 1024);
            hash_update($ctx, $buffer);
        }
        $hash = hash_final($ctx, false);
        fclose($file);

        return $hash;
    }


    private function get_update_config() {
        putenv('GNUPGHOME=' . GNUPG_HOME);
        $res = gnupg_init();
        $signed = file_get_contents( UPDATE_ENDPOINT . '/latest.json.asc' );
        $plain = "";
        $info = gnupg_verify($res, $signed, false, $plain);

        if ($info[0]['fingerprint'] != GPG_FINGERPRINT) {
            throw new Exception('SECURITY ERROR: GPG SIGNATURE FAIL: ' . $info[0]['fingerprint']);
        }

        return json_decode($plain, true);
    }

    private function endsWith( $str, $sub ) {
        return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
    }
}
?>