<template>
    <div v-if="data" class="widget-rss">
        <h2 class="widget-rss-title">
            {{ data.title }}
        </h2>
        <ul class="widget-rss-items">
            <li v-for="(item, key) in data.items" :key="key" class="widget-rss-item">
                <a :href="item.link" target="_blank">
                    {{ item.title }}
                </a>
                <span class="widget-rss-item-date">
                    {{ item.pubDate }}
                </span>
                <p v-html="item.description"></p>
            </li>
        </ul>
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
                    link: this.widget.widgetSettings.link,
                    nbItems: this.widget.dashboardWidgetSettings.nbItems,
                    timeout: this.widget.dashboardWidgetSettings.timeout
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
  