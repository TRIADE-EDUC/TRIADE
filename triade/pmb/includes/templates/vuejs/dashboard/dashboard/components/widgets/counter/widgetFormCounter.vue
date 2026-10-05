<template>
    <div v-if="configuration">
        <form-refresh :settings="settings"></form-refresh>
        <form-display :settings="settings" :display_formats="widget_type.display_formats"></form-display>

        <div v-if="widget.widgetEditable || widget.numUser == current_user">
            <label class="widget-form-label" for="widget-counter-location">
                {{ messages.get('common', 'location') }}
            </label>
            <select id="widget-counter-location" v-model="widget.widgetSettings.location" required>
                <option value="" disabled>
                    {{ messages.get('dashboard', 'form_location_select') }}
                </option>
                <option v-for="location in configuration.locations" :value="location.idlocation">
                    {{ location.location_libelle }}
                </option>
            </select>
        </div>

        <div>
            <fieldset v-if="widget.widgetSettings.counters">
                <legend>
                    <!-- What ?? -->
                    <label>
                        {{ messages.get('dashboard', 'form_counters_widget') }}
                    </label>
                </legend>
                <div 
                    v-for="(service, key) in configuration.services"
                    :key="key" 
                    class="widget-counter-service">

                    <div>
                        <input 
                            type="checkbox" 
                            name="widget-counter-service" 
                            v-model="settings.counters[key].enabled"
                            :value="key"
                            :id="'widget-counter-service' + key">
                    </div>
                    <div>
                        <input 
                            type="color" 
                            name="widget-counter-service-color" 
                            v-model="settings.counters[key].color"
                            :id="'widget-counter-service-color' + key">
                    </div>
                    <div>
                        <label :for="'widget-counter-service' + key">
                            {{ service.name }}
                        </label>
                    </div>
                </div>
            </fieldset>
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
                configuration: null
            }
        },
        created: async function () {
            if(this.widget.widgetSettings) {
                if(!this.widget.widgetSettings.location) {
                    this.widget.widgetSettings.location = "";
                }
                if(!this.widget.widgetSettings.counters) {
                    this.widget.widgetSettings.counters = {};
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

                if(Object.keys(this.widget.widgetSettings.counters).length == 0) {
                    for(let key in this.configuration.services) {
                        this.$set(this.widget.widgetSettings.counters, key, {
                            name: this.configuration.services[key].name,
                            enabled: false,
                            color: this.configuration.services[key].color
                        })
                    }
                }
            },
            checkForm: function() {
                for(let counter in this.widget.widgetSettings.counters) {
                    if(this.widget.widgetSettings.counters[counter].enabled) {
                        return true;
                    }
                }

                this.notif.error(this.messages.get('dashboard', 'form_counters_counter_error_widget'));
                return false;
            }
        }
    };
</script>