<template>
    <div v-if="configuration">
        <form-refresh :settings="settings"></form-refresh>
        <form-display :settings="settings" :display_formats="widget_type.display_formats"></form-display>

        <div class="mb-s">
            <label class="widget-form-label" for="widget-indicator-module">
                {{ messages.get('dashboard', 'form_module_label') }}
            </label>
            <select 
                id="widget-indicator-module"
                v-model="widget.widgetSettings.module"
                @change="setConfigurationModule">

                <option value="" disabled>{{ messages.get('dashboard', 'form_module_select') }}</option>
                <option v-for="(module, key) in configuration.modules" :value="key" :key="key">
                    {{ widget_type.msg[`${key}_name`] }}
                </option>
            </select>
        </div>

        <!-- Choix methode (Indicateur) -->
        <div v-if="configurationModule" class="mb-s">
            <div class="mb-s">
                <label class="widget-form-label" for="widget-indicator-type">
                    {{ messages.get('dashboard', 'form_indicator_widget') }}
                </label>
                <select id="widget-indicator-type" v-model="selectedMethod">
                    <option value="" disabled>
                        {{ messages.get('dashboard', 'form_indicator_type_widget') }}
                    </option>
                    <option 
                        v-for="(method, key) in configurationModule.methods" 
                        :key="key"
                        :value="key">

                        {{ widget_type.msg[`${widget.widgetSettings.module}_${key}`] }}
                    </option>
                </select>

                <!-- Bouton ajout methode -->
                <button
                    v-if="(selectedMethod != '')"  
                    type="button" class="bouton" @click="addMethod" :title="messages.get('common', 'more_label')">
                    <i class="fa fa-plus"></i>
                </button>
            </div>

            <!-- Configuration methode -->
            <fieldset 
                v-for="(method, index) in widget.widgetSettings.methods" 
                :key="index"
                class="widget-indicator-method widget-fieldset">

                <legend class="widget-legend">{{ widget_type.msg[`${widget.widgetSettings.module}_${method.name}`] }}</legend>

                <!-- Bouton suppression methode -->
                <button 
                    type="button" 
                    class="dashboard-button widget-indicator-method-remove" 
                    @click="removeMethod(index)"
                    :title="messages.get('common', 'cancel')">
                    <i class="fa fa-trash"></i>
                </button>

                <!-- Libelle methode -->
                <div class="mb-s">
                    <label 
                        class="widget-form-label" 
                        :for="'widget-indicator-label' + index ">
                        {{ messages.get('dashboard', 'form_label') }}
                    </label>
                    <input 
                        type="text" 
                        :id="'widget-indicator-label' + index" 
                        v-model="widget.widgetSettings.methods[index].label">
                </div>
                
                <!-- conditions -->
                <div class="mb-s" v-for="(condition, key) in configurationModule.methods[method.name].conditions" :key="key">

                    <label :for="condition + '-' +  widget.idWidget + '-' + index" class="widget-form-label" >{{ widget_type.msg[`${widget.widgetSettings.module}_${condition}`] }}</label>
                    
                    <!-- condition de type select --> 
                    <select 
                        v-if="configurationModule.conditions[condition].type == 'select' &&
                              !Array.isArray(widget.widgetSettings.methods[index].conditions)" 

                        :id="condition + '-' +  widget.idWidget + '-' + index"
                        v-model="widget.widgetSettings.methods[index].conditions[condition]"
                        :multiple="configurationModule.conditions[condition].multiple == 1 ? true : false"
                        :size="configurationModule.conditions[condition].multiple == 1 ? 3 : 0">
    
                        <option
                            v-for="(option, index) in configurationModule.conditions[condition].values"
                            :key="index"
                            :value="index">
                            {{ option }}
                        </option>
                    </select>

                    <!-- condition de type period --> 
                    <formPeriod 
                        v-else-if="configurationModule.conditions[condition].type == 'period'"
                        :period="widget.widgetSettings.methods[index].conditions[condition]" >
                    </formPeriod>
                    
                    <!-- condition de type input --> 
                    <input
                        v-else
                        :type="configurationModule.conditions[condition].type"
                        :id="condition + '-' +  widget.idWidget + '-' + index"
                        v-model="widget.widgetSettings.methods[index].conditions[condition]">
                
                </div>
                
                <!-- Choix couleur fond -->
                <div class="mb-s">
                    <label 
                        class="widget-form-label" 
                        :for="'widget-indicator-color' + index">
                        {{ messages.get('common', 'background_color_label') }}
                        </label>
                    <input 
                        type="color" 
                        :id="'widget-indicator-color' + index"
                        v-model="widget.widgetSettings.methods[index].backgroundColor">
                </div>
            </fieldset>
        </div>
    </div>
</template>

<script>
    import formRefresh from "../common/formRefresh.vue";
    import formPeriod from "../common/formPeriod.vue";
    import formDisplay from "../common/formDisplay.vue";

    export default {
        props: ["widget", "from", "current_user", "widget_type"],
        components: {
            formRefresh,
            formPeriod,
            formDisplay
        },
        data: function() {
            return {
                configuration: null,
                configurationModule: null,
                selectedMethod: ""
            }
        },
        mounted: async function () {
            if(this.widget.widgetSettings) {
                if(!this.widget.widgetSettings.module) {
                    this.$set(this.widget.widgetSettings, 'module', "");
                }

                if(!this.widget.widgetSettings.methods) {
                    this.$set(this.widget.widgetSettings, 'methods', []);
                }
            }

            this.configuration = await this.getConfiguration(this.widget_type.source);

            if(this.widget.widgetSettings.module) {
                this.setConfigurationModule();
            }
        },
        watch: {
            "widget.widgetSettings.module": function () {
                this.$set(this.widget.widgetSettings, 'methods', []);
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
            getConfiguration: async function (source = "") {
                let response = await this.ws.post('widget', 'getConfiguration', { source: source });

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    return;
                }

                return response;
            },
            setConfigurationModule: async function () {
                this.$nextTick(async () => {
                    const selectedModule = this.configuration.modules[this.widget.widgetSettings.module];
                    if(selectedModule) {
                        this.configurationModule = await this.getConfiguration(selectedModule);
                    }
                })
            },
            addMethod: function() {
                if(this.selectedMethod) {
                    this.$set(
                        this.widget.widgetSettings.methods,
                        this.widget.widgetSettings.methods.length,
                        { 
                            name: this.selectedMethod,
                            label: "",
                            backgroundColor: "#ffffff",
                            conditions: this.getInitializedConditions()
                        }
                    );
                    this.$forceUpdate();
                }
            },
            removeMethod: function(index) {
                this.widget.widgetSettings.methods.splice(index, 1);
                this.$forceUpdate();
            },
            getInitializedConditions: function() {
                let conditions = {};

                if(this.selectedMethod) {
                    for(let condition of this.configurationModule.methods[this.selectedMethod].conditions) {
                        if(
                            this.configurationModule.conditions[condition].type == 'select' && 
                            this.configurationModule.conditions[condition].multiple &&
                            this.configurationModule.conditions[condition].multiple == 1
                        ) 
                        {
                            conditions[condition] = [];
                            continue;
                        }

                        if(this.configurationModule.conditions[condition].type == 'period') {
                            conditions[condition] = {};
                            continue;
                        }

                        conditions[condition] = "";
                    }
                }

                return conditions;
            },
            checkForm: function() {
                if(!this.widget.widgetSettings.methods.length) {
                    this.notif.error(this.messages.get('dashboard', 'form_indicator_nb_method_error_widget'));
                    return false;
                }

                return true;
            }
        }
    };
</script>