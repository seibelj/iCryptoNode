$(document).ready(function() {

    var app = new Vue({
        el: '#app',
        data: {
            blockchain: NODE_TYPE.capitalize(),
            tabs: {
                node: VUE_GLOBALS.node.data,
                wifi: VUE_GLOBALS.wifi.data,
                vpn: VUE_GLOBALS.vpn.data,
                logs: VUE_GLOBALS.logs.data,
                update: VUE_GLOBALS.update.data
            }
        },
        computed: _.assign(
            VUE_GLOBALS.node.computed,
            VUE_GLOBALS.wifi.computed,
            VUE_GLOBALS.vpn.computed,
            VUE_GLOBALS.logs.computed,
            VUE_GLOBALS.update.computed
        ),
        methods: _.assign(
            VUE_GLOBALS.node.methods,
            VUE_GLOBALS.wifi.methods,
            VUE_GLOBALS.vpn.methods,
            VUE_GLOBALS.logs.methods,
            VUE_GLOBALS.update.methods, {
                
                changeTab: function(tab) {

                    if (this.tabs[tab].isActive) {
                        return
                    }

                    let key
                    for (key in this.tabs) {
                        this.tabs[key].isActive = false
                    }

                    if (tab === 'node') {
                        this.loadNodeInfo()
                    }
                    else if (tab === 'wifi') {
                        this.loadWifiInfo()
                    }
                    else if (tab === 'vpn') {
                        this.loadVpnInfo()
                    }
                    else if (tab === 'logs') {
                        this.loadLogs()
                    }
                    else if (tab === 'update') {
                        this.checkUpdate()
                    }

                    this.tabs[tab].isActive = true
                }
            }
        )
    })

    app.loadNodeInfo()

})
