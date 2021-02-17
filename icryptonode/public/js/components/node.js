VUE_GLOBALS.node = {}

VUE_GLOBALS.node.data = {
    isActive: true,
    isLoading: false,
    isShuttingDown: false,
    error: "",
    memoryUsed: 0,
    memoryTotal: 0,
    memoryLoad: 0.0,
    diskUsed: 0,
    diskTotal: 0,
    diskLoad: 0,
    cpuLoad: 0.0,
    currentDaemonUser: '',
    currentDaemonPort: '',
    newDaemonUser: '',
    newDaemonPassword: '',
    newDaemonPort: '',
    isCurrentlyEnabled: 'yes',
    isNewlyEnabled: 'yes',
    isDaemonRunning: true
}

VUE_GLOBALS.node.computed = {
    allowNodeCancel: function() {
        return !this.tabs.node.isLoading
    },
    allowNodeSave: function() {
        // if turning VPN off, simple. If it's on, must have all settings filled out
        if (this.tabs.node.isLoading) {
            return false
        }
        else {
            if (this.tabs.node.newDaemonUser.length === 0 ||
                this.tabs.node.newDaemonPort.length === 0) {
                return false
            }

            let port = parseInt($.trim(this.tabs.node.newDaemonPort), 10)
            if (isNaN(port)) {
                return false
            }
            if (port < 1 || port > 65535) {
                return false
            }

            return true
        }
    },
    allowNodeShutdown: function() {
        return !this.tabs.node.isLoading && !this.tabs.node.isShuttingDown
    },
    confirmNodeShutdown: function() {
        return !this.tabs.node.isLoading && this.tabs.node.isShuttingDown
    },
    daemonRunningString: function() {
        return this.tabs.node.isDaemonRunning ? "Yes" : "No"
    }
}

VUE_GLOBALS.node.methods = {
    loadNodeInfo: function() {
        let vm = this
        this.tabs.node.isLoading = true
        axios.get( API_ROOT + '/node/info')
            .then(function (response) {
                // handle success
                console.log(response)
                vm.tabs.node.isCurrentlyEnabled = response.data.daemon_enabled === 'yes' ? true : false
                vm.tabs.node.isNewlyEnabled = vm.tabs.node.isCurrentlyEnabled
                vm.tabs.node.currentDaemonUser = response.data.daemon_user
                vm.tabs.node.newDaemonUser = response.data.daemon_user
                vm.tabs.node.currentDaemonPort = response.data.daemon_rpc_port
                vm.tabs.node.newDaemonPort = response.data.daemon_rpc_port
                vm.tabs.node.newDaemonPassword = ''

                vm.tabs.node.memoryUsed = response.data.mem.current
                vm.tabs.node.memoryTotal = response.data.mem.total
                vm.tabs.node.memoryLoad = response.data.mem.load

                vm.tabs.node.diskUsed = response.data.disk.current
                vm.tabs.node.diskTotal = response.data.disk.total
                vm.tabs.node.diskLoad = response.data.disk.used

                vm.tabs.node.cpuLoad = response.data.cpu.load

                vm.tabs.node.isDaemonRunning = response.data.daemon_running

                vm.tabs.node.isLoading = false
            })
            .catch(function (error) {
                // handle error
                console.log(error);
                vm.tabs.node.isLoading = false
            })
    },

    nodeCancel: function(event) {
        this.tabs.node.newDaemonUser = this.tabs.node.currentDaemonUser
        this.tabs.node.newDaemonPassword = ''
        this.tabs.node.isNewlyEnabled = this.tabs.node.isCurrentlyEnabled
        this.tabs.node.newDaemonPort = this.tabs.node.currentDaemonPort
    },

    nodeSave: function() {
        let vm = this
        vm.tabs.node.isLoading = true

        axios.post( API_ROOT + '/node/update_settings', {
            'daemon_user': vm.tabs.node.newDaemonUser,
            'daemon_pass': vm.tabs.node.newDaemonPassword,
            'daemon_rpc_port': $.trim(vm.tabs.node.newDaemonPort),
            'daemon_enabled': vm.tabs.node.isNewlyEnabled
        }, {timeout: 30000}).then(response => {
            
            vm.loadNodeInfo()
        
        }).catch( error => {
            console.log(error)
            vm.loadNodeInfo()
        })
    },
    startNodeShutdown: function() {
        let vm = this
        if (vm.tabs.node.isDaemonRunning) {
            alert("You must disable the blockchain daemon before shutting down to prevent data corruption.")
            return
        }
        vm.tabs.node.isShuttingDown = true
    },
    cancelNodeShutdown: function() {
        let vm = this
        vm.tabs.node.isShuttingDown = false
    },
    executeNodeShutdown: function() {
        let vm = this
        vm.tabs.node.isLoading = true
        axios.post( API_ROOT + '/node/shutdown', {'execute': true}, {timeout: 5000}).then(response => {
            console.log(response)
            alert("The node is shutting down. This website will no longer load. "
                + "Please unplug power from the node and re-plug power in to restart.")
        }).catch( error => {
            console.log(error)
            alert("The node is shutting down. This website will no longer load. "
                + "Please unplug power from the node and re-plug power in to restart.")
        })
    },
}