<div v-if="tabs.wifi.isActive" class="tab-view" id="wifi-view">
    <div class="marg">
        <h3><strong>Wifi Settings</strong></h3>

        <div>
            <small>NOTE: If this hangs or freezes while saving, you likely entered invalid Wifi credentials,
            and you may have to reboot your device to fix it. If the device was connected on Wifi and you saved invalid Wifi credentials, you will need to re-connect the device via Ethernet in order
            to fix it, as the device is now unable to connect to the network.</small>
        </div>

        <div style="padding-top: 20px;">
            <form>
                <div class="form-group">
                    <label for="wifi-ssid">Wifi Name (SSID)</label>
                    <input v-model="tabs.wifi.newSsid" type="text" class="form-control" id="wifi-ssid" placeholder="Enter Wifi Name">
                </div>
                <div class="form-group">
                    <label for="wifi-password">Password</label>
                    <input v-model="tabs.wifi.newPassword" type="password" class="form-control" id="wifi-password" placeholder="Wifi Password">
                </div>
                <button :disabled="!allowWifiCancel" class="btn btn-secondary" v-on:click="wifiCancel">Cancel</button>
                <button :disabled="!allowWifiSave" class="btn btn-primary" v-on:click="wifiSave">Save</button>&nbsp;&nbsp;
                <span v-if="tabs.wifi.isLoading"><i class="fa fa-spinner fa-spin icn-spinner"></i></span>&nbsp;&nbsp;
                <span v-if="tabs.wifi.showSuccess" class="has-success">Saved</span>
                <span v-if="tabs.wifi.error" class="has-error">{{ tabs.wifi.error }}</span>
            </form>
        </div>
    </div>
</div>