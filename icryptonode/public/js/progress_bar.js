$(document).ready(function() {

    Vue.component('progress-bar', {
        props: {
            'downloaded': Number,
            'total': Number
        },
        computed: {
            percentComplete: function() {
                return Math.round((this.downloaded  / this.total) * 100)
            },
            totalSizeMB: function() {
                return (this.total  / 1048576).toFixed(2)
            },
            downloadedSizeMB: function() {
                return (this.downloaded  / 1048576).toFixed(2)
            }
        },
        template: `
            <div>
                <div class="progress" style="height: 40px;">
                    <div class="progress-bar"
                         role="progressbar"
                         v-bind:style="{ width: percentComplete + '%' }"
                         v-bind:aria-valuenow="percentComplete"
                         aria-valuemin="0"
                         aria-valuemax="100">
                         <strong>{{ percentComplete }}%</strong>
                    </div>
                </div>
                <span><i class="fa fa-spinner fa-spin icn-spinner"></i>&nbsp;
                      {{ downloadedSizeMB }} MB downloaded out of {{ totalSizeMB }} MB</span>
            </div>
        `
    })
})