<div v-if="tabs.vpn.isActive" class="tab-view" id="vpn-view">
    <div class="marg">
        <h3><strong>VPN Settings</strong></h3>

        <div class="marg">
            <small>Using VPN requires an account with <a href="http://www.privateinternetaccess.com/pages/buy-vpn/icrypto" target="_blank">Private Internet Access</a> (PIA). Learn more about why iCryptoNode uses PIA <a href="https://icryptonode.com/pages/anonymous-vpn" target="_blank">here</a>. PIA is the leading anonymous VPN service provider, and enabling VPN on iCryptoNode can significantly improve your blockchain anonymity and prevent tracking of your transactions.</small>
        </div>

        <button :disabled="tabs.vpn.isLoading"
                type="button"
                v-on:click="loadVpnInfo"
                class="btn btn-success">Refresh</button>

        <div class="marg">
            <div>
                <strong>Device Public IP:</strong>&nbsp;&nbsp;{{ tabs.vpn.publicIp }}
            </div>
            <small>This is the public-facing IP address of the Raspberry Pi, <i>not</i> the IP address of this machine running this web browser.</small>
        </div>

        <div class="marg">
            <form>
                <div class="marg">
                    <div>
                        <strong>VPN Enabled</strong>
                    </div>
                    <div class="form-check">
                      <input :disabled="tabs.vpn.isLoading" class="form-check-input" type="radio" name="vpn-enabled-radio" id="vpn-enabled-radio-false" v-bind:value="false" v-model="tabs.vpn.isNewlyEnabled">
                      <label class="form-check-label" for="vpn-enabled-radio-false">
                        Disabled
                      </label>
                    </div>
                    <div class="form-check">
                      <input :disabled="tabs.vpn.isLoading" class="form-check-input" type="radio" name="vpn-enabled-radio" id="vpn-enabled-radio-true" v-bind:value="true" v-model="tabs.vpn.isNewlyEnabled">
                      <label class="form-check-label" for="vpn-enabled-radio-true">
                        Enabled
                      </label>
                    </div>
                </div>
                <div class="form-group">
                    <strong><label for="vpn-country">VPN Country</label></strong>
                    <select :disabled="tabs.vpn.isLoading" class="form-control" name="vpn-country" id="vpn-country" v-model="tabs.vpn.newVpnFile">
                        <option v-for="item in vpnCountryList"
                                :key="item.id"
                                :value="item.value"
                                :selected="item.value === tabs.vpn.newVpnFile">
                            {{ item.text }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <strong><label for="vpn-user">VPN Username</label></strong>
                    <input :disabled="tabs.vpn.isLoading" v-model="tabs.vpn.newUser" type="text" class="form-control" id="vpn-user" placeholder="Enter Username">
                </div>
                <div class="form-group">
                    <strong><label for="vpn-password">VPN Password</label></strong>
                    <input :disabled="tabs.vpn.isLoading" v-model="tabs.vpn.newPassword" type="password" class="form-control" id="vpn-password" placeholder="Enter Password">
                </div>
                <button :disabled="!allowVpnCancel" class="btn btn-secondary" v-on:click.prevent="vpnCancel">Cancel</button>
                <button :disabled="!allowVpnSave" class="btn btn-primary" v-on:click.prevent="vpnSave">Save</button>&nbsp;&nbsp;
                <span v-if="tabs.vpn.isLoading"><i class="fa fa-spinner fa-spin icn-spinner"></i></span>&nbsp;&nbsp;
                <span v-if="tabs.vpn.showSuccess" class="has-success">Saved</span>
                <span v-if="tabs.vpn.error" class="has-error">{{ tabs.vpn.error }}</span>
            </form>
        </div>
    </div>
</div>