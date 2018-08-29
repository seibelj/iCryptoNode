<?php
class WebApp
{
    /**
     * Endpoint for getting the web app
     * @view app
    */
    public function index() {
        $html = "";
        return $html;
    }

    /**
     * Endpoint for getting the web app
     *
     * @url GET icn_version
     *
     * @return json
    */
    public function icn_version() {
        return array('icn_version' => ICRYPTONODE_VERSION);
    }

    /**
     * Endpoint for getting logs
     * @url GET logs
     *
     * @return json
    */
    public function logs() {
        $output = shell_exec( NODE_CMD . ' logs' );
        return array('log' => $output);
    }
}
?>