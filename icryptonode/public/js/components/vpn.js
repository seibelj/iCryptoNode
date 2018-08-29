VUE_GLOBALS.vpn = {}

VUE_GLOBALS.vpn.data = {
    isActive: false,
    isLoading: false,
    isCurrentlyEnabled: 'no',
    isNewlyEnabled: 'no',
    publicIp: '',
    currentUser: '',
    newUser: '',
    newPassword: '',
    currentVpnFile: '',
    newVpnFile: '',
    showSuccess: false,
    error: ""
}

VUE_GLOBALS.vpn.computed = {
    allowVpnCancel: function() {
        return !this.tabs.vpn.isLoading
    },
    allowVpnSave: function() {
        // if turning VPN off, simple. If it's on, must have all settings filled out
        if (this.tabs.vpn.isLoading) {
            return false
        }
        else if (!this.tabs.vpn.isNewlyEnabled) {
            return true
        }
        else {
            if (this.tabs.vpn.newUser.length === 0) {
                return false
            }
            if (this.tabs.vpn.newUser !== this.tabs.vpn.currentUser &&
                (this.tabs.vpn.newUser.length === 0 ||
                    this.tabs.vpn.newPassword.length === 0)) {
                return false
            }
            return true
        }
    },
    vpnCountryList: function() {
        return PIA_LIST.map((key, idx) => {
            let displayText = VPN_MAP[key].country + ' - ' + VPN_MAP[key].name
            if (VPN_MAP[key].country === VPN_MAP[key].name) {
                displayText = VPN_MAP[key].country
            }
            return {
                text: displayText,
                value: key + '.ovpn',
                id: idx
            }
        })
    }
}

VUE_GLOBALS.vpn.methods = {
    loadVpnInfo: function() {
        let vm = this
        this.tabs.vpn.isLoading = true
        axios.get( API_ROOT + '/vpn/status')
            .then(function (response) {
                // handle success
                console.log(response)
                vm.tabs.vpn.isCurrentlyEnabled = response.data.vpn_enabled === 'yes' ? true : false
                vm.tabs.vpn.isNewlyEnabled = vm.tabs.vpn.isCurrentlyEnabled
                vm.tabs.vpn.currentUser = response.data.current_vpn_username
                vm.tabs.vpn.newUser = response.data.current_vpn_username
                vm.tabs.vpn.newPassword = ''
                vm.tabs.vpn.publicIp = response.data.public_ip
                vm.tabs.vpn.currentVpnFile = response.data.vpn_file
                vm.tabs.vpn.newVpnFile = response.data.vpn_file
                vm.tabs.vpn.isLoading = false
            })
            .catch(function (error) {
                // handle error
                console.log(error);
                vm.tabs.vpn.isLoading = false
            })
    },

    vpnCancel: function(event) {
        this.tabs.vpn.newUser = this.tabs.vpn.currentUser
        this.tabs.vpn.newPassword = ''
        this.tabs.vpn.isNewlyEnabled = this.tabs.vpn.isCurrentlyEnabled
        this.tabs.vpn.newVpnFile = this.tabs.vpn.currentVpnFile
    },

    vpnSave: function() {
        let vm = this
        vm.tabs.vpn.isLoading = true

        new Promise((resolve, reject) => {
            // Disable VPN if needed
            if (vm.tabs.vpn.isNewlyEnabled) {
                resolve()
            }
            else {
                axios.post( API_ROOT + '/vpn/disable' , {}, {timeout: 15000}).then( response => {
                    resolve()
                }).catch( error => {
                    console.log(error)
                    reject(error)
                })
            }
        }).then(() => {
            // Change country if needed
            if (vm.tabs.vpn.newVpnFile !== vm.tabs.vpn.currentVpnFile) {
                console.log("Changing VPN country to " + vm.tabs.vpn.newVpnFile)
                return axios.post( API_ROOT + '/vpn/change_config' , {
                    config_name: vm.tabs.vpn.newVpnFile
                }, {timeout: 15000}).then(() => {
                    vm.tabs.vpn.currentVpnFile = vm.tabs.vpn.newVpnFile
                })
            }
            else {
                return Promise.resolve()
            }
        }).then(() => {
            // Change VPN creds if needed
            if (vm.tabs.vpn.newPassword.length > 0) {
                console.log("Changing VPN creds")
                return axios.post( API_ROOT + '/vpn/change_creds' , {
                    username: vm.tabs.vpn.newUser,
                    password: vm.tabs.vpn.newPassword,
                }, {timeout: 15000}).then(() => {
                    vm.tabs.currentUser = vm.tabs.vpn.newUser
                })
            }
            else {
                return Promise.resolve()
            }
        }).then(() => {
            // (Re)start VPN if needed
            if (vm.tabs.vpn.isNewlyEnabled) {
                console.log("(Re)starting VPN")
                return axios.post( API_ROOT + '/vpn/restart' , {}, {timeout: 15000})
            }
            else {
                return Promise.resolve()
            }
        }).then(() => {
            vm.loadVpnInfo()
        }).catch( error => {
            console.log(error)
            vm.loadVpnInfo()
        })

    }
}