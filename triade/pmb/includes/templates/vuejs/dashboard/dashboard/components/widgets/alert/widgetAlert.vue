<template>
    <div v-if="data" class="height-100">
        <div v-for="(module, key) in data" :key="key" class="widget-alerts-module">
            <span>{{ module.label }}</span>

            <ul class="widget-alerts">
                <li v-for="(alert, index) in module.alerts" :key="index" class="widget-alert">
                    <i class="fa fa-exclamation-triangle widget-alert-icon"></i>
                    <a :href="alert.destination_link" target="_blank">
                        {{ `${alert.label} (${alert.number})` }}
                    </a>
                </li>
            </ul>
        </div>

        <div v-if="!Object.keys(data).length" class="widget-alerts-empty height-100">
            <h3>
                <i class="fa fa-check" aria-hidden="true"></i>
                {{ messages.get('dashboard', 'form_alerts_empty') }}
            </h3>
        </div>

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
export default {
    props: ["widget", "editMode", "current_user", "widget_type"],
    data() {
        return {
            data: null
        };
    },
    mounted() {
        this.fetchData();

        if(this.widget.dashboardWidgetSettings && this.widget.dashboardWidgetSettings.refresh.enabled) {
            const refresh = Math.max(this.$root.widget_refresh_time, this.widget.dashboardWidgetSettings.refresh.value);
            setTimeout(this.refresh, refresh * 1000);
        }
    },
    methods: {
        fetchData: async function(loader = true) {
            let post = new URLSearchParams();
            post.append("data", JSON.stringify({ 
                source: this.widget_type.source,
                params: {
                    modules: this.widget.widgetSettings.modules
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
    },
};
</script>
  