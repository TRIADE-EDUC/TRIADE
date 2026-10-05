<template>
    <div v-if="settings.display_format !== ''">
        <fieldset class="widget-fieldset">
            <legend>{{ messages.get('dashboard', 'form_stat_display_params') }}</legend>

            <!-- Affichage titre -->
            <div class="mb-s">
                <label 
                    class="widget-form-label" 
                    for="widget-stat-display-title">
    
                    {{ messages.get('dashboard', 'form_stat_display_title') }}
                </label>
                <input type="checkbox" id="widget-stat-display-title" v-model="settings.title.display">
            </div>
            
            <div v-if="settings.title.display" class="mb-s">
                <label 
                    class="widget-form-label" 
                    for="widget-stat-text">
    
                    {{ messages.get('dashboard', 'form_stat_title') }}
                </label>
                <input 
                    type="text" 
                    id="widget-stat-text" 
                    v-model="settings.title.text"
                    required>
            </div>

            <!-- Affichage légendes --> 
            <div v-if="settings.display_format != 'table'" class="mb-s">
                <label 
                    class="widget-form-label" 
                    for="widget-stat-legend">
    
                    {{ messages.get('dashboard', 'form_stat_display_legend') }}
                </label>
                <input type="checkbox" id="widget-stat-legend" v-model="settings.legend.display">
            </div>

            <!-- Affichage tooltips --> 
            <div v-if="settings.display_format != 'table'" class="mb-s">
                <label 
                    class="widget-form-label" 
                    for="widget-stat-display-tooltip">
    
                    {{ messages.get('dashboard', 'form_stat_display_tooltip') }}
                </label>
                <input type="checkbox" id="widget-stat-display-tooltip" v-model="settings.tooltip.enabled">
            </div>
                    
            <!-- Configuration affichage -->
            <div class="mb-s" v-if="settings.display_format == 'table'" >
            </div>
        </fieldset>
    </div>
</template>

<script>
    export default {
        props: ["settings"],
        created: function() {
            if(!this.settings.title) {
                this.$set(this.settings, "title", {
                    display: false,
                    text: ""
                });
            }

            if(!this.settings.legend) {
                this.$set(this.settings, "legend", {
                    display: false
                });
            }

            if(!this.settings.tooltip) {
                this.$set(this.settings, "tooltip", {
                    enabled: false
                });
            }
        }
    }
</script>