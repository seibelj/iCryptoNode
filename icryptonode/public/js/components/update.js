VUE_GLOBALS.update = {}

VUE_GLOBALS.update.data = {
    isActive: false,
    isLoading: false,
    isUpdateAvailable: false,
    latestDaemonVersionShort: "",
    latestDaemonVersionLong: "",
    latestDaemonUrl: "",
    latestDaemonSize: 0,
    downloadedBytes: 0,
    isDownloadingDaemon: false,
    isDaemonDownloadValid: false,
    daemonDownloadErrorMsg: null,
    isInstallingDaemon: false,

    isIcnUpdateAvailable: false,
    latestIcnVersionShort: "",
    latestIcnUrl: "",
    latestIcnSize: 0,
    downloadedIcnBytes: 0,
    isDownloadingIcn: false,
    isIcnDownloadValid: false,
    icnDownloadErrorMsg: null,
    isInstallingIcn: false,
    icnInstallCountdown: 0
}

VUE_GLOBALS.update.computed = {
    updatePrefix: function() {
        return this.tabs.update.isUpdateAvailable ? "Update" : "Latest"
    },
    updateAvailableString: function() {
        return this.tabs.update.isUpdateAvailable ? "Yes" : "No (Latest Version Already Installed)"
    },
    icnUpdateAvailableString: function() {
        return this.tabs.update.isIcnUpdateAvailable ? "Yes" : "No (Latest iCryptoNode Version Already Installed)"
    },
    updateSizeMB: function() {
        return (this.tabs.update.latestDaemonSize  / 1048576).toFixed(2)
    },
    icnUpdateSizeMB: function() {
        return (this.tabs.update.latestIcnSize  / 1048576).toFixed(2)
    },
    showDownloadDaemonUpdateButton: function() {
        return this.tabs.update.isUpdateAvailable &&
               !this.tabs.update.isDownloadingDaemon &&
               !this.tabs.update.isDaemonDownloadValid &&
               !this.tabs.update.isInstallingDaemon
    },
    showInstallDaemonUpdateButton: function() {
        return this.tabs.update.isUpdateAvailable &&
               !this.tabs.update.isDownloadingDaemon &&
               this.tabs.update.isDaemonDownloadValid &&
               !this.tabs.update.isInstallingDaemon
    },
    showDownloadIcnUpdateButton: function() {
        return this.tabs.update.isIcnUpdateAvailable &&
               !this.tabs.update.isDownloadingIcn &&
               !this.tabs.update.isIcnDownloadValid &&
               !this.tabs.update.isInstallingIcn
    },
    showInstallIcnUpdateButton: function() {
        return this.tabs.update.isIcnUpdateAvailable &&
               !this.tabs.update.isDownloadingIcn &&
               this.tabs.update.isIcnDownloadValid &&
               !this.tabs.update.isInstallingIcn
    },
    allowClearFiles: function() {
        return !this.tabs.update.isDownloadingIcn &&
               !this.tabs.update.isInstallingIcn &&
               !this.tabs.update.isDownloadingDaemon &&
               !this.tabs.update.isInstallingDaemon
    },
    isRefreshDisabled: function() {
        return this.tabs.update.isDownloadingIcn ||
               this.tabs.update.isInstallingIcn ||
               this.tabs.update.isDownloadingDaemon ||
               this.tabs.update.isInstallingDaemon
    }
}

VUE_GLOBALS.update.methods = {
    checkUpdate: function() {
        let vm = this
        this.tabs.update.isLoading = true
        axios.get( API_ROOT + '/updates/update_info')
            .then(function (response) {
                // handle success
                console.log(response)
                vm.tabs.update.latestDaemonSize = response.data.size
                vm.tabs.update.latestDaemonVersionShort = response.data.version_short
                vm.tabs.update.latestDaemonVersionLong = response.data.version_long
                vm.tabs.update.isUpdateAvailable = response.data.has_update
                vm.tabs.update.latestDaemonUrl = response.data.download_url
                vm.tabs.update.isDaemonDownloadValid = false

                vm.tabs.update.latestIcnSize = response.data.icryptonode_size
                vm.tabs.update.latestIcnVersionShort = response.data.icryptonode_version_short
                vm.tabs.update.isIcnUpdateAvailable = response.data.icryptonode_has_update
                vm.tabs.update.latestIcnUrl = response.data.icryptonode_download_url
                vm.tabs.update.isIcnDownloadValid = false

                vm.tabs.update.isLoading = false
            })
            .catch(function (error) {
                // handle error
                console.log(error);
                vm.tabs.update.isLoading = false
            })
    },

    downloadDaemon: function() {
        
        let vm = this
        this.tabs.update.isDownloadingDaemon = true
        this.tabs.update.isDaemonDownloadValid = false

        let verifyDaemonDownload = function() {
            return axios.get( API_ROOT + '/updates/verify_daemon_download').then(function (response) {
                vm.tabs.update.isDaemonDownloadValid = response.data.valid
            })
        }

        verifyDaemonDownload().then(function (response) {
            if (vm.tabs.update.isDaemonDownloadValid) {
                console.log("Download valid!")
                vm.tabs.update.isDownloadingDaemon = false
                return Promise.resolve(true)
            }
            else {
                console.log("Download NOT valid, downloading!")
                return axios.get( API_ROOT + '/updates/download_daemon').then(response => {

                    return new Promise((resolve, reject) => {
                        let isChecking = false
                        let errorCount = 0
                        let i = setInterval(function() {

                            if (!isChecking) {
                                isChecking = true
                            
                                axios.post( API_ROOT + '/updates/download_status', {
                                    
                                    file_url: vm.tabs.update.latestDaemonUrl

                                }).then(function(response) {

                                    vm.tabs.update.downloadedBytes = response.data.downloaded
                                    isChecking = false

                                    if (response.data.downloaded >= vm.tabs.update.latestDaemonSize) {
                                        console.log("Finished downloading daemon update")
                                        clearInterval(i);
                                        resolve(false)
                                    }
                                }).catch(function(error) {
                                    console.log("Error fetching download_status", error)
                                    isChecking = false
                                    errorCount++
                                    if (errorCount > 5) {
                                        console.log("download_status not working, crashing out")
                                        clearInterval(i);
                                        reject(error)
                                    }
                                })
                            }
                        }, 1000)
                    })
                })
            }
        }).then(function(isVerified) {
            console.log("in isVerified block", isVerified)
            if (!isVerified) {
                return verifyDaemonDownload()
            }
            else {
                return Promise.resolve()
            }
            
        }).then(() => {
            console.log("in isDaemonDownloadValid block", vm.tabs.update.isDaemonDownloadValid)
            if (!vm.tabs.update.isDaemonDownloadValid) {
                vm.tabs.update.daemonDownloadErrorMsg = "Unable to verify downloaded update"
            }
            vm.tabs.update.isDownloadingDaemon = false
        }).catch(error => {
            console.error("Unable to download daemon update", error)
            vm.tabs.update.isDownloadingDaemon = false
        })
    },
    
    installDaemon: function() {
        let vm = this
        this.tabs.update.isInstallingDaemon = true

        axios.post( API_ROOT + '/updates/install_daemon', {}, {timeout: 60000}).then(response => {

            if (response.data.success) {
                vm.checkUpdate()
            }
            else {
                console.error("Unable to install daemon update (unknown)")
            }
            vm.tabs.update.isInstallingDaemon = false
            vm.tabs.update.downloadedBytes = 0
        
        }).catch(error => {
            console.error("Unable to install daemon update", error)
            vm.tabs.update.isInstallingDaemon = false
        })
    },

    downloadIcn: function() {

        let vm = this
        this.tabs.update.isDownloadingIcn = true
        this.tabs.update.isIcnDownloadValid = false

        let verifyIcnDownload = function() {
            return axios.get( API_ROOT + '/updates/verify_icn_download').then(function (response) {
                vm.tabs.update.isIcnDownloadValid = response.data.valid
            })
        }

        verifyIcnDownload().then(function (response) {
            if (vm.tabs.update.isIcnDownloadValid) {
                console.log("ICN Download valid!")
                vm.tabs.update.isDownloadingIcn = false
                return Promise.resolve(true)
            }
            else {
                console.log("ICN Download NOT valid, downloading!")
                return axios.get( API_ROOT + '/updates/download_icn').then(response => {

                    return new Promise((resolve, reject) => {

                        if (!response.data.downloading) {
                            return reject("Could not download.");
                        }

                        let isChecking = false
                        let errorCount = 0
                        let i = setInterval(function() {

                            if (!isChecking) {
                                isChecking = true
                            
                                axios.post( API_ROOT + '/updates/download_status', {
                                    
                                    file_url: vm.tabs.update.latestIcnUrl

                                }).then(function(response) {

                                    vm.tabs.update.downloadedIcnBytes = response.data.downloaded
                                    isChecking = false

                                    if (response.data.downloaded >= vm.tabs.update.latestIcnSize) {
                                        console.log("Finished downloading ICN update")
                                        clearInterval(i);
                                        resolve(false)
                                    }
                                }).catch(function(error) {
                                    console.log("Error fetching ICN download_status", error)
                                    isChecking = false
                                    errorCount++
                                    if (errorCount > 5) {
                                        console.log("ICN download_status not working, crashing out")
                                        clearInterval(i);
                                        reject(error)
                                    }
                                })
                            }
                        }, 1000)
                    })
                })
            }
        }).then(function(isVerified) {
            console.log("in isVerified block", isVerified)
            if (!isVerified) {
                return verifyIcnDownload()
            }
            else {
                return Promise.resolve()
            }
            
        }).then(() => {
            console.log("in isIcnDownloadValid block", vm.tabs.update.isIcnDownloadValid)
            if (!vm.tabs.update.isIcnDownloadValid) {
                vm.tabs.update.icnDownloadErrorMsg = "Unable to verify downloaded update"
            }
            vm.tabs.update.isDownloadingIcn = false
        }).catch(error => {
            console.error("Unable to download ICN update", error)
            vm.tabs.update.isDownloadingIcn = false
        })
    },

    installIcn: function() {

        let vm = this
        this.tabs.update.isInstallingIcn = true

        axios.post( API_ROOT + '/updates/install_icn', {}).then(response => {

            if (response.data.success) {
                $('#icnUpdateModal').modal({
                    keyboard: false,
                    backdrop: 'static'
                })

                vm.tabs.update.icnInstallCountdown = 180
                let counter = setInterval(function() {
                    vm.tabs.update.icnInstallCountdown = vm.tabs.update.icnInstallCountdown - 1
                }, 1000)
                setTimeout(function() {
                    clearInterval(counter)
                    vm.tabs.update.isInstallingIcn = false
                }, 180000)
            }
            else {
                console.error("Unable to install ICN update (unknown)")
                alert("Unable to install ICN update (unknown)")
            }
        
        }).catch(error => {
            console.error("Unable to install daemon update", error)
            alert("Unable to install ICN update (unknown)")
            vm.tabs.update.isInstallingIcn = false
        })
    },
    refreshPage: function() {
        location.reload()
    },
    clearFiles: function() {
        let vm = this

        axios.post( API_ROOT + '/updates/clear_downloads', {}).then(response => {

            console.log(response)
            vm.checkUpdate()
        
        }).catch(error => {
            console.error("Unable to clear downloaded files", error)
        })
    },
    allowDaemonUpdate: function() {
        let vm = this

        axios.post( API_ROOT + '/updates/force_allow_update', {update_type: "daemon"}).then(response => {

            console.log(response)
            vm.checkUpdate()

        }).catch(error => {
            console.error("Unable to force-allow daemon update", error)
        })
    },
    allowIcnUpdate: function() {
        let vm = this

        vm.tabs.update.isIcnUpdateAvailable = true;
    }
}