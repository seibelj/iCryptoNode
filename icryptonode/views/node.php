<div v-if="tabs.node.isActive" class="tab-view" id="node-view">
    <div class="marg">
        <h3><strong>{{ blockchain }} Node Info</strong></h3>

        <div class="marg">
            <button :disabled="tabs.node.isLoading"
                    type="button"
                    v-on:click="loadNodeInfo"
                    class="btn btn-success">Refresh</button>
            <span v-if="tabs.node.isLoading"><i class="fa fa-spinner fa-spin icn-spinner"></i></span>
        </div>

        <div class="marg">
            <h4>Daemon Status</h4>
            <div>
                <strong>{{ blockchain }} Currently Running:</strong>&nbsp;&nbsp;{{ daemonRunningString }}
            </div>
        </div>

        <div class="marg">
            <h4>Device Statistics</h4>
            <div>
                <strong>Memory:</strong>&nbsp;&nbsp;{{ tabs.node.memoryUsed }} MB used of {{ tabs.node.memoryTotal }} MB total memory ({{ tabs.node.memoryLoad }}%)
            </div>
            <div>
                <strong>CPU Load:</strong>&nbsp;&nbsp;{{ tabs.node.cpuLoad }}%
            </div>
            <div>
                <strong>Disk:</strong>&nbsp;&nbsp;{{ tabs.node.diskUsed }} GB used of {{ tabs.node.diskTotal }} GB total
            </div>
        </div>

        <div class="marg">
            <form>
                <div class="marg">
                    <div>
                        <strong>{{ blockchain }} Daemon Enabled</strong>
                    </div>
                    <div class="form-check">
                      <input :disabled="tabs.node.isLoading" class="form-check-input" type="radio" name="node-enabled-radio" id="node-enabled-radio-false" v-bind:value="false" v-model="tabs.node.isNewlyEnabled">
                      <label class="form-check-label" for="node-enabled-radio-false">
                        Disabled
                      </label>
                    </div>
                    <div class="form-check">
                      <input :disabled="tabs.node.isLoading" class="form-check-input" type="radio" name="node-enabled-radio" id="node-enabled-radio-true" v-bind:value="true" v-model="tabs.node.isNewlyEnabled">
                      <label class="form-check-label" for="node-enabled-radio-true">
                        Enabled
                      </label>
                    </div>
                </div>
                <div class="form-group">
                    <strong><label for="node-user">Daemon RPC Username</label></strong>
                    <input :disabled="tabs.node.isLoading" v-model="tabs.node.newDaemonUser" type="text" class="form-control" id="node-user" placeholder="Enter Daemon RPC Username">
                </div>
                <div class="form-group">
                    <strong><label for="node-password">Daemon RPC Password</label></strong><br>
                    <small>If empty, daemon password is unchanged. For safety, you cannot remove the password.</small>
                    <input :disabled="tabs.node.isLoading" v-model="tabs.node.newDaemonPassword" type="password" class="form-control" id="node-password" placeholder="Enter New RPC Password">
                </div>
                <div class="form-group">
                    <strong><label for="node-port">Daemon RPC Port</label></strong>
                    <small>Must be a valid port (1 to 65535)</small>
                    <input :disabled="tabs.node.isLoading" v-model="tabs.node.newDaemonPort" type="text" class="form-control" id="node-port" placeholder="Enter Daemon RPC Port">
                </div>
                <button :disabled="!allowNodeCancel" class="btn btn-secondary" v-on:click.prevent="nodeCancel">Cancel</button>
                <button :disabled="!allowNodeSave" class="btn btn-primary" v-on:click.prevent="nodeSave">Save</button>&nbsp;&nbsp;
                <span v-if="tabs.node.isLoading"><i class="fa fa-spinner fa-spin icn-spinner"></i></span>&nbsp;&nbsp;
                <span v-if="tabs.node.showSuccess" class="has-success">Saved</span>
                <span v-if="tabs.node.error" class="has-error">{{ tabs.node.error }}</span>
            </form>
        </div>
    </div>
</div>