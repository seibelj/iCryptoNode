<div v-if="tabs.logs.isActive" class="tab-view" id="logs-view">
    <div class="marg">
        <h3><strong>{{ blockchain }} Logs</strong></h3>
        <span><strong>Displays the last 200 lines from the blockchain log</strong></span>&nbsp;&nbsp;
        <button v-if="!tabs.logs.isLoading"
                type="button"
                v-on:click="loadLogs"
                class="btn btn-success">Refresh</button>
        <span v-if="tabs.logs.isLoading"><i class="fa fa-spinner fa-spin icn-spinner"></i></span>
    </div>
    <pre v-if="!tabs.logs.isLoading" v-html="tabs.logs.logHTML" id="console"></pre>
</div>