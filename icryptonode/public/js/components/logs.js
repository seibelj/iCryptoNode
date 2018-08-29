VUE_GLOBALS.logs = {}

VUE_GLOBALS.logs.data = {
    isActive: false,
    isLoading: false,
    logHTML: "",
    error: ""
}

VUE_GLOBALS.logs.computed = {}

var ansi_up = new AnsiUp;

VUE_GLOBALS.logs.methods = {
    loadLogs: function() {
        let vm = this
        this.tabs.logs.isLoading = true
        axios.get( API_ROOT + '/logs')
            .then(function (response) {
                // handle success
                console.log(response)
                vm.tabs.logs.logHTML = ansi_up.ansi_to_html(response.data.log)
                vm.tabs.logs.isLoading = false
            })
            .catch(function (error) {
                // handle error
                console.log(error);
                vm.tabs.logs.isLoading = false
            })
    },
}