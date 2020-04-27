<?php
class Node
{

    /**
     * Endpoint for getting node information and basic system stats (memory, disk, cpu)
     * @url GET info
     *
     * @return json
    */
    public function info() {
        $json_output = shell_exec( SYSTEM_CMD_DIR . '/basic_system_stats' );
        $output = json_decode($json_output, true);

        $output['daemon_user'] = trim(shell_exec( "sudo uci get icryptonode.@info[0].daemon_user" ));
        $output['daemon_rpc_port'] = trim(shell_exec( "sudo uci get icryptonode.@info[0].daemon_rpc_port" ));
        $output['daemon_enabled'] = trim(shell_exec( "sudo uci get icryptonode.@info[0].daemon_enabled" ));

        $return_code = -1;
        $exec_output = array();
        exec( 'sudo ' . NODE_CMD . ' status', $exec_output, $return_code);

        if (intval($return_code) == 5) {
            $output['daemon_running'] = true;
        }
        else {
            $output['daemon_running'] = false;
        }

        return $output;
    }

    /**
     * Endpoint for changing node config settings. Restarts the node after changing it
     * @url POST update_settings
     *
     * @param string daemon_user
     * @param string daemon_rpc_port
     * @param bool daemon_enabled
     * @param string daemon_pass
     * @return json
    */
    public function update_settings($daemon_user, $daemon_rpc_port, $daemon_enabled, $daemon_pass = '') {

        // Block crons
        shell_exec( "sudo uci set icryptonode.@info[0].is_updating='yes'; sudo uci commit" );

        $this->execute_node_cmd('stop');

        sleep(6);

        shell_exec( "sudo uci set icryptonode.@info[0].daemon_user=" . escapeshellarg($daemon_user));

        if (strlen(trim($daemon_pass)) > 0) {
            shell_exec( "sudo uci set icryptonode.@info[0].daemon_pass=" . escapeshellarg($daemon_pass));
        }

        shell_exec( "sudo uci set icryptonode.@info[0].daemon_rpc_port=" . escapeshellarg($daemon_rpc_port));

        if ($daemon_enabled) {
            shell_exec( "sudo uci set icryptonode.@info[0].daemon_enabled='yes'" );
        }
        else {
            shell_exec( "sudo uci set icryptonode.@info[0].daemon_enabled='no'" );
        }

        $output = shell_exec( "sudo uci commit" );

        // Re-enable crons
        shell_exec( "sudo uci set icryptonode.@info[0].is_updating='no'; sudo uci commit" );

        if ($daemon_enabled) {
            $this->execute_node_cmd('start');
        }
        
        return array('success' => true);
    }

    /**
     * Endpoint for stopping, starting, and restarting the node
     * @url POST control
     *
     * @param string operation
     * @return json
    */
    public function control($operation) {

        $ops = array('stop', 'start', 'restart');

        if (!in_array($operation, $ops) ) {
            return array('success' => false);
        }

        $output = shell_exec( 'sudo ' . NODE_CMD . ' ' . $operation );
        $vpn_status = json_decode($output);
        return $vpn_status;
    }

    private function execute_node_cmd($operation) {
        return shell_exec( 'sudo ' . NODE_CMD . ' ' . escapeshellarg($operation) );
    }
}
?>