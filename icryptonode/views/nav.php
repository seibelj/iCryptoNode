<div class="row">
    <div class="col-3">
        <img src="/img/icryptonode_logo.png" class="img-fluid" alt="iCryptoNode Logo">
    </div>
    <div class="col-7">&nbsp;</div>
    <div class="col-2">
        <img src="/img/<?php echo NODE_TYPE; ?>.png" width="80" class="img-fluid" alt="iCryptoNode Logo">
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link"
                   v-bind:class="{ active: tabs.node.isActive }"
                   v-on:click="changeTab('node')"
                   href="#">Node</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   v-bind:class="{ active: tabs.wifi.isActive }"
                   v-on:click="changeTab('wifi')"
                   href="#">Wifi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   v-bind:class="{ active: tabs.vpn.isActive }"
                   v-on:click="changeTab('vpn')"
                   href="#">VPN</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   v-bind:class="{ active: tabs.logs.isActive }"
                   v-on:click="changeTab('logs')"
                   href="#">Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   v-bind:class="{ active: tabs.update.isActive }"
                   v-on:click="changeTab('update')"
                   href="#">Update</a>
            </li>
        </ul>
    </div>
</div>