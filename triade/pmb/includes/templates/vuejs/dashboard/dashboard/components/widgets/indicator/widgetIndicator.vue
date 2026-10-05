<template>
    <div v-if="data" class="height-100">
        <component 
            v-if="widget.dashboardWidgetSettings.display_format" 
            :is="`widget-indicator-${widget.dashboardWidgetSettings.display_format}`" 
            :widget="widget"
            :widget_type="widget_type" 
            :data="data">
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
    import widgetIndicatorTile from './widgetIndicatorTile.vue';
    import widgetIndicatorList from './widgetIndicatorList.vue';
    export default {
        props: ["widget", "editMode", "current_user", "widget_type"],
        components: {
            widgetIndicatorTile,
            widgetIndicatorList
        },
        data: function() {
            return {
                data: null
            }
        },
        created: function() {
            this.fetchData(false);

            if(this.widget.dashboardWidgetSettings && this.widget.dashboardWidgetSettings.refresh.enabled) {
                const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                setTimeout(this.refresh, refresh * 1000);
            }
        },
        watch: {
            "widget.widgetSettings.methods": function() {
                this.fetchData(false);
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
            fetchData: async function(loader = true) {
                let post = new URLSearchParams();
                post.append("data", JSON.stringify({ 
                    source: this.widget_type.source,
                    params: {
                        module: this.widget.widgetSettings.module,
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

                this.data = result;
                this.$forceUpdate();

                if (loader) {
                    this.hiddenLoader();
                }
            },
            refresh: function (loop = true, loader = false) {
                this.$root.$emit("refreshWidget", this.widget, loader);
                this.fetchData(loader);

                if(this.widget.dashboardWidgetSettings.refresh.enabled && loop) {
                    const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
                    setTimeout(this.refresh, refresh * 1000);
                }
            }
        }
    };
</script>