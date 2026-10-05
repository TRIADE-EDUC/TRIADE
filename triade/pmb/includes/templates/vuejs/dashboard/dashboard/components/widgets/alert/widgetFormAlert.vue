<template>
    <div v-if="configuration">
        <form-refresh :settings="settings"></form-refresh>
        <form-display :settings="settings" :display_formats="widget_type.display_formats"></form-display>

        <div class="mb-s">
            <label class="widget-form-label" for="widget-alert-module">
                {{ messages.get('dashboard', 'form_module_label') }}
            </label>

            <select 
                id="widget-alert-module"
                v-model="widget.widgetSettings.modules"
                @change="$forceUpdate()"
                required
                multiple>

                <!-- <option value="" disabled>{{ messages.get('dashboard', 'form_stat_module_widget') }}</option> -->
                <option v-for="(module, key) in configuration.modules" :value="module" :key="module">
                    {{ widget_type.msg[module] }}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
    import formRefresh from "../common/formRefresh.vue";
    import formDisplay from "../common/formDisplay.vue";

    export default {
        props: ["widget", "from", "current_user", "widget_type"],
        components: {
            formRefresh,
            formDisplay
        },
        data: function() {
            return {
                configuration: null,
            }
        },
        created: async function () {
            if(this.widget.widgetSettings) {
                if(!this.widget.widgetSettings.modules) {
                    this.widget.widgetSettings.modules = [];
                }
            }

            await this.fetchConfiguration();
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
            fetchConfiguration: async function () {
                let response = await this.ws.post('widget', 'getConfiguration', { source: this.widget_type.source });

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    return;
                }

                this.configuration = response;
            }
        }
    };
</script>