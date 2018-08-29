VUE_GLOBALS.wifi = {}

VUE_GLOBALS.wifi.data = {
    isActive: false,
    isLoading: false,
    currentSsid: '',
    newSsid: '',
    newPassword: '',
    showSuccess: false,
    error: ""
}

VUE_GLOBALS.wifi.computed = {
    allowWifiSave: function() {
        return !this.tabs.wifi.isLoading &&
               this.tabs.wifi.newSsid.length > 0 &&
               this.tabs.wifi.newPassword.length > 0
    },
    allowWifiCancel: function() {
        return !this.tabs.wifi.isLoading &&
               this.tabs.wifi.newSsid.length > 0 ||
               this.tabs.wifi.newPassword.length > 0
    }
}

VUE_GLOBALS.wifi.methods = {
    loadWifiInfo: function() {
        let vm = this
        this.tabs.wifi.isLoading = true
        axios.get( API_ROOT + '/wifi/info')
            .then(function (response) {
                // handle success
                console.log(response)
                vm.tabs.wifi.currentSsid = response.data.ssid
                vm.tabs.wifi.newSsid = response.data.ssid
                vm.tabs.wifi.isLoading = false
            })
            .catch(function (error) {
                // handle error
                console.log(error);
                vm.tabs.wifi.isLoading = false
            })
    },

    wifiCancel: function() {
        this.tabs.wifi.newSsid = this.tabs.wifi.currentSsid
        this.tabs.wifi.newPassword = ''
    },

    wifiSave: function() {
        let vm = this
        vm.tabs.wifi.isLoading = true
        vm.tabs.wifi.error = ""

        axios.post( API_ROOT + '/wifi/change_creds', {
            'ssid': vm.tabs.wifi.newSsid,
            'password': vm.tabs.wifi.newPassword
        }, {timeout: 15000}).then( response => {

            console.log(response)

            vm.tabs.wifi.currentSsid = this.tabs.wifi.newSsid
            vm.tabs.wifi.newPassword = ''

            vm.tabs.wifi.showSuccess = true

            setTimeout(() => { vm.tabs.wifi.showSuccess = false }, 2000)

            vm.tabs.wifi.isLoading = false

        }).catch( error => {
            console.log(error);
            vm.tabs.wifi.isLoading = false
            vm.tabs.wifi.error = error.toString()
        })
    }
}