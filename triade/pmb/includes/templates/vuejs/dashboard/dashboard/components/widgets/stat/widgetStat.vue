<template>
    <div v-if="datasets" class="height-100">

        <component 
            v-if="widget.dashboardWidgetSettings.display_format" 
            :is="`widget-stat-${widget.dashboardWidgetSettings.display_format}`"
            :datasets="datasets"
            :widget="widget">
        </component>

        <div v-show="!editMode" class="dashboard-item-actions">
            <button 
                type="button" 
                class="dashboard-button" 
                :title="messages.get('common', 'refresh')"
                @click="refresh(false, true)">

                <i class="fa fa-refresh" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</template>

<script>
    import widgetStatTable from './widgetStatTable.vue';
    import widgetStatBar from './widgetStatBar.vue';
    import widgetStatLine from './widgetStatLine.vue';
    import widgetStatPie from './widgetStatPie.vue';
    import widgetStatDoughnut from './widgetStatDoughnut.vue';

    export default {
        props: ["widget", "editMode", "current_user", "widget_type"],
        components: {
            widgetStatTable,
            widgetStatBar,
            widgetStatLine,
            widgetStatPie,
            widgetStatDoughnut,
        },
        data: function() {
            return {
                datasets: null,
            }
        },
        created: function() {
            this.fetchDatasets(false);

            if(this.widget.dashboardWidgetSettings && this.widget.dashboardWidgetSettings.refresh.enabled) {
                const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                setTimeout(this.refresh, refresh * 1000);
            }
        },
        watch: {
            "widget.widgetSettings.methods": function() {
                this.fetchDatasets(false);
            }
        },
        computed: {

            /**
             * Retourne l'editabilite
             *
             * @return {bool}
             */
             editable: function() {
                
                if(this.editMode) {
                    return false;
                }
                if(this.widget.numUser == this.current_user) {
                    return true;
                }
                if(this.widget.widgetEditable == 1) {
                    return true;
                }
                return false;
            }
        },
        methods: {
            fetchDatasets: async function(loader = true) {
                let post = new URLSearchParams();
                post.append("data", JSON.stringify({ 
                    source: this.widget_type.source,
                    params: {
                        methods: this.widget.widgetSettings.methods
                    }
                }));

                if (loader) {
                    this.showLoader();
                }

                let response = await fetch(this.$root.url_webservice + "widget/getData", {
                    method: "POST",
                    cache: 'no-cache',
                    body: post
                });

                let result = await response.json();
                if (result.error) {
                    this.notif.error(this.messages.get('dashboard', result.errorMessage));
                    return;
                }

                this.datasets = result;

                if (loader) {
                    this.hiddenLoader();
                }
            },
            refresh: function (loop = true, loader = false) {
                this.$root.$emit("refreshWidget", this.widget, loader);
                this.fetchDatasets(loader);

                if(this.widget.dashboardWidgetSettings.refresh.enabled && loop) {
                    const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                    setTimeout(this.refresh, refresh * 1000);
                }
            }
        }
    };
</script>