<div v-if="tabs.update.isActive" class="tab-view" id="update-view">
    <div class="marg">
        <button v-if="!tabs.update.isLoading"
                :disabled="isRefreshDisabled"
                type="button"
                v-on:click="checkUpdate"
                class="btn btn-success">Check for Updates</button>
        <span v-if="tabs.update.isLoading"><i class="fa fa-spinner fa-spin icn-spinner"></i></span>
    </div>
    <div v-if="!tabs.update.isLoading">
        <div class="marg-40">
            <h3>{{ blockchain }} Software</h3>
            <div>
                Update Available: <strong>{{ updateAvailableString }}</strong>
            </div>
            <div>
                {{ updatePrefix }} Version: <strong>{{ tabs.update.latestDaemonVersionLong }}</strong>
            </div>
            <div>
                {{ updatePrefix }} Version Size: <strong>{{ updateSizeMB }} MB</strong>
            </div>
            <div>
                {{ updatePrefix }} Version Download URL: <strong>{{ tabs.update.latestDaemonUrl }}</strong>
            </div>

            <button v-show="showDownloadDaemonUpdateButton"
                type="button"
                v-on:click="downloadDaemon"
                class="btn btn-primary">Download {{ blockchain }} Update</button>

            <progress-bar v-bind:total="tabs.update.latestDaemonSize"
                          v-bind:downloaded="tabs.update.downloadedBytes"
                          v-if="tabs.update.isDownloadingDaemon"></progress-bar>

            <div v-show="showInstallDaemonUpdateButton">
                <div><strong>Downloaded &amp; Verified {{ blockchain }} Update!</strong></div>
                <button
                    type="button"
                    v-on:click="installDaemon"
                    class="btn btn-primary">Install {{ blockchain }} Update</button>
            </div>

            <div v-show="tabs.update.isInstallingDaemon">
                <span><strong><i class="fa fa-spinner fa-spin icn-spinner"></i>&nbsp;Installing daemon update, please wait, do not refresh the page...</strong></span>
            </div>
        </div>

        <div class="marg-40">
            <h3>iCryptoNode Software</h3>
            <div>
                Update Available: <strong>{{ icnUpdateAvailableString }}</strong>
            </div>
            <div>
                iCryptoNode Version: <strong>{{ tabs.update.latestIcnVersionShort }}</strong>
            </div>
            <div>
                iCryptoNode Version Size: <strong>{{ icnUpdateSizeMB }} MB</strong>
            </div>
            <div>
                iCryptoNode Version Download URL: <strong>{{ tabs.update.latestIcnUrl }}</strong>
            </div>

            <button v-show="showDownloadIcnUpdateButton"
                type="button"
                v-on:click="downloadIcn"
                class="btn btn-primary">Download iCryptoNode Update</button>

            <progress-bar v-bind:total="tabs.update.latestIcnSize"
                          v-bind:downloaded="tabs.update.downloadedIcnBytes"
                          v-if="tabs.update.isDownloadingIcn"></progress-bar>

            <div v-show="showInstallIcnUpdateButton">
                <div><strong>Downloaded &amp; Verified iCryptoNode Update!</strong></div>
                <button
                    type="button"
                    v-on:click="installIcn"
                    class="btn btn-primary">Install iCryptoNode Update</button>
            </div>

            <div v-show="tabs.update.isInstallingIcn">
                <span><strong><i class="fa fa-spinner fa-spin icn-spinner"></i>&nbsp;Installing iCryptoNode update, please wait, do not refresh the page...</strong></span>
            </div>
        </div>

        <div>
            <h3>Clear Updates</h3>
            <div>
                If you get into an error state that cannot be recovered, use this to clear out
                any malformed or corrupt update files.
            </div>

            <button :disabled="!allowClearFiles"
                type="button"
                v-on:click="clearFiles"
                class="btn btn-primary">Clear Downloaded Updates</button>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="icnUpdateModal" tabindex="-1" role="dialog" aria-labelledby="icnUpdateModalTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="icnUpdateModalLongTitle">Installing iCryptoNode Update</h5>
          </div>
          <div class="modal-body">
            <span v-if="tabs.update.isInstallingIcn" >
                <i class="fa fa-spinner fa-spin icn-spinner"></i>&nbsp;Please wait while the iCryptoNode update installs ( {{ tabs.update.icnInstallCountdown }} seconds remaining )
            </span>
            <span v-if="!tabs.update.isInstallingIcn" >
                Install success! Please refresh the page.
            </span>
          </div>
          <div class="modal-footer">
            <button v-if="tabs.update.isInstallingIcn"
                    :disabled="true"
                    type="button"
                    class="btn btn-primary">Please wait...</button>
            <button v-if="!tabs.update.isInstallingIcn"
                    type="button"
                    class="btn btn-primary"
                    v-on:click="refreshPage">Refresh</button>
          </div>
        </div>
      </div>
    </div>
</div>