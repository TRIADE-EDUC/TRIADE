<template>
    <div>
        <form-refresh :settings="settings"></form-refresh>
        <form-display :settings="settings" :display_formats="widget_type.display_formats"></form-display>

        <div v-if="widget.widgetEditable || widget.numUser == current_user" class="mb-s">
            <label class="widget-form-label" for="widget-rss-link">
                {{ messages.get('dashboard', 'form_rss_link') }}
            </label>
            <input 
                type="text" 
                id="widget-rss-link" 
                v-model="widget.widgetSettings.link" 
                placeholder="https://example.com" 
                required>
        </div>
        <div class="mb-s">
            <label class="widget-form-label" for="widget-rss-nb-items">
                {{ messages.get('dashboard', 'form_rss_nb_items') }}
            </label>
            <input type="number" id="widget-rss-nb-items" min="0" v-model="settings.nbItems" required>
        </div>
        <div class="mb-s">
            <label class="widget-form-label" for="widget-rss-timeout">
                {{ messages.get('dashboard', 'form_rss_timeout') }}
            </label>
            <input type="number" id="widget-rss-timeout" min="5" v-model="settings.timeout" required>
        </div>
    </div>
</template>

<script>
    import formRefresh from "../common/formRefresh.vue";
    import formDisplay from "../common/formDisplay.vue";
    
    export default {
        props: ["widget", "from", "current_user", "widget_type"],
        components: {
            formDisplay,
            formRefresh
        },
        mounted: async function () {
            if(this.widget.widgetSettings) {
                if(!this.widget.widgetSettings.link) {
                    this.$set(this.widget.widgetSettings, "link", "");
                }

                if(!this.widget.widgetSettings.nbItems) {
                    this.$set(this.widget.widgetSettings, "nbItems", 5);
                }

                if(!this.widget.widgetSettings.timeout) {
                    this.$set(this.widget.widgetSettings, "timeout", 5);
                }
            }
        },
        computed: {
            settings: function() {
                if(this.from == 'widget') {
                    return this.widget.widgetSettings;
                }

                return this.widget.dashboardWidgetSettings;
            }
        },
        methods: {

        }
    };
</script>