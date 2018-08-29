<?php
class Wifi
{
    /**
     * Get current Wifi status and SSID
     * @url Get info
     *
     * @return json
    */
    public function info() {

        $info = array('ssid' => "");

        $ssid_str = shell_exec('cat /etc/wpa_supplicant/wpa_supplicant.conf | grep "ssid"');

        $info['ssid'] = preg_replace( array("/\s+/", "/ssid=/", '/\"/') , "" , $ssid_str );

        return $info;
    }

    /**
     * Get current Wifi status and SSID
     * @url POST change_creds
     *
     * @param string ssid
     * @param string password
     * @return json
    */
    public function change_creds($ssid, $password) {

        shell_exec( "sudo " . SYSTEM_CMD_DIR . "/change_wifi " . escapeshellarg($ssid) . " " . escapeshellarg($password) );

        return array( 'success' => true );
    }

    
}
?>