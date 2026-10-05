<template>
    <div v-if="configuration && configuration.modules">

        <!-- Rafraichissement -->
        <form-refresh :settings="settings"></form-refresh>

        <!-- Format d'affichage -->
        <form-display :settings="settings" :display_formats="widget_type.display_formats"></form-display>
        <form-stat-options :settings="settings"></form-stat-options>

        <fieldset class="widget-fieldset">
            <legend>{{ messages.get('dashboard', 'form_stat_add_datasets_label') }}</legend>

            <!-- Choix module -->
            <div class="mb-s">
                <label class="widget-form-label" for="widget-stat-module">
                    {{ messages.get('dashboard', 'form_module_label') }}
                </label>
                <select 
                    id="widget-stat-module"
                    v-model="selectedModule">
    
                    <option value="" disabled>
                        {{ messages.get('dashboard', 'form_module_select') }}
                    </option>
                    <option 
                        v-for="(module, keyModule) in configuration.modules" 
                        :value="keyModule" 
                        :key="keyModule">
    
                        {{ widget_type.msg[`${keyModule}_name`] }}
                    </option>
                </select>
            </div>
    
            <!-- Formulaire du module sélectionné -->
            <div v-if="selectedModule" class="mb-s">
                <component 
                    :is="`form-${selectedModule}`" 
                    :module="configuration.modules[selectedModule]"
                    @addMethod="addMethod">
                </component>
            </div>
        </fieldset>

        <fieldset 
            v-if="widget.widgetSettings.methods.length" 
            class="widget-stat-datasets widget-fieldset">

            <legend>{{ messages.get('dashboard', 'form_stat_datasets_label') }}</legend>

            <fieldset 
                v-for="method, keyMethod in widget.widgetSettings.methods" 
                class="widget-stat-datasets widget-fieldset">

                <legend>{{ method.label }}</legend>

                <!-- Bouton suppression methode -->
                <button 
                    type="button" 
                    class="dashboard-button widget-stat-method-remove" 
                    @click="removeMethod(keyMethod)"
                    :title="messages.get('common', 'cancel')">

                    <i class="fa fa-trash"></i>
                </button>

                <component 
                    :is="`form-${method.module}-dataset`" 
                    :method="method"
                    :keyMethod="keyMethod"
                    :source="configuration.modules[method.module].source">
                </component>
            </fieldset>

        </fieldset>
    </div>
</template>

<script>
    import formRefresh from "../common/formRefresh.vue";

    import formDisplay from "../common/formDisplay.vue";
    import formStatOptions from "./widgetFormStatOptions.vue";

    import formProc from "./widgetFormProc.vue";
    import formProcDataset from "./widgetFormProcDataset.vue";

    export default {
        props: ["widget", "from", "current_user", "widget_type"],
        components: {
            formRefresh,

            formDisplay,
            formStatOptions,

            formProc,
            formProcDataset
        },
        data: function() {
            return {
                // Liste des modules
                configuration: {},

                // Module sélectionné
                selectedModule: "",
            }
        },
        created: async function () {
            if(this.widget.widgetSettings) {
                if(!this.widget.widgetSettings.methods) {
                    this.$set(this.widget.widgetSettings, 'methods', []);
                }
            }

            await this.fetchConfiguration(this.widget_type.source);
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
            fetchConfiguration: async function() {
                let response = await this.ws.post('widget', 'getConfiguration', { 
                        source: this.widget_type.source 
                    }
                );

                if (response.error) {
                    this.notif.error(this.messages.get('dashboard', response.errorMessage));
                    return;
                }

                this.$set(this, "configuration", response);
            },
            addMethod: function(method = null) {
                if(method) {
                    this.$set(
                        this.widget.widgetSettings.methods, 
                        this.widget.widgetSettings.methods.length,
                        method
                    );
                }
            },
            removeMethod: function(keyMethod) {
                this.widget.widgetSettings.methods.splice(keyMethod, 1);
            },
            checkForm: function() {
                if(!this.widget.widgetSettings.methods.length) {
                    this.notif.error(this.messages.get('dashboard', 'form_stat_nb_method_error_widget'));
                    return false;
                }

                return true;
            }
        }
    };
</script>