<?php
class Vpn
{
    /**
     * Endpoint for getting VPN status
     * @url GET status
     *
     * @return json
    */
    public function status() {
        $output = shell_exec( VPN_CMD_DIR . '/get_status' );
        $vpn_status = json_decode($output);
        return $vpn_status;
    }

    /**
     * Endpoint for stopping the VPN
     * @url POST disable
     *
     * @return json
    */
    public function disable() {
        $output = shell_exec( "sudo uci set icryptonode.@info[0].vpn_enabled='no'; sudo uci commit" );
        $output = shell_exec( 'sudo ' . VPN_CMD_DIR . '/vpn_control stop' );
        return array('success' => true);
    }

    /**
     * Endpoint for restarting the VPN
     * @url POST restart
     *
     * @return json
    */
    public function restart() {
        $output = shell_exec( "sudo uci set icryptonode.@info[0].vpn_enabled='yes'; sudo uci commit" );
        $output = shell_exec( 'sudo ' . VPN_CMD_DIR . '/vpn_control restart' );
        return array('success' => true);
    }

    /**
     * Endpoint for changing VPN config (country)
     * @url POST change_config
     *
     * @param string config_name
     * @return json
    */
    public function change_config($config_name) {

        $config_clean = escapeshellcmd($config_name);

        if (!file_exists( VPN_DIR . '/' . $config_clean )) {
            error_log("Cannot find " . VPN_DIR . '/' . $config_clean );
            return array('success' => false);
        }
        $output = shell_exec( "sudo uci set icryptonode.@info[0].vpn_file='" . $config_clean . "'; sudo uci commit" );
        return array('success' => true);
    }

    /**
     * Endpoint for changing VPN credentials
     * @url POST change_creds
     *
     * @param string username
     * @param string password
     * @return json
    */
    public function change_creds($username, $password) {
        $output = shell_exec( VPN_CMD_DIR . '/change_pia_auth ' . escapeshellarg($username) . ' ' . escapeshellarg($password) );
        return array('success' => true);
    }
}
?>