<template>
    <fieldset class="dsi-fieldset-setting">
        <legend class="dsi-legend-setting">{{ messages.get('dsi', 'customizable_fields_title') }}</legend>
        <div class="dsi-form-group-m dsi-br-xl" v-for="(field, index) in settings.customizableFields" :key="index">
            <div>
                <div class="dsi-container-move-btn">
                    <button v-if="index > 0" type="button" class="bouton"
                        @click="moveUp(index)" :title=" messages.get('dsi', 'customizable_fields_move_top_arrow')">
                        <i class="fa fa-arrow-up" aria-hidden="true"></i>
                    </button>
                    <button v-if="index < settings.customizableFields.length - 1" type="button" class="bouton"
                        @click="moveDown(index)" :title=" messages.get('dsi', 'customizable_fields_move_bottom_arrow')">
                        <i class="fa fa-arrow-down" aria-hidden="true"></i>
                    </button>
                </div>
                <span class="dsi-inline-block" :title="messages.get('dsi', 'customizable_fields_selector_type_desc')">
                    <label class="etiquette" :for="'customizable-type' + index">
                        {{ messages.get('dsi', 'customizable_fields_type') }}
                    </label>
                    <select v-model="field.type"
                        :id="'customizable-type-' + index"
                        :name="'customizable-type-' + index">
                        <option value="" disabled>{{ messages.get('dsi', 'customizable_fields_selector_type') }}</option>
                        <option v-for="(type, subIndex) in types" :key="subIndex" :value="type">
                            {{ messages.get('dsi', `customizable_fields_${type}`) }}
                        </option>
                    </select>
                </span>
            </div>
            <div class="dsi-form-group-content">
                <span :title=" messages.get('dsi', 'customizable_fields_edition_name_desc')">
                    <label class="etiquette" :for="'customizable-edition-name' + index">
                        {{ messages.get('dsi', 'customizable_fields_edition_name') }}
                    </label>
                    <input type="text" 
                        :id="'customizable-edition-name-' + index"
                        :name="'customizable-edition-name-' + index"
                        v-model="field.name"
                        @change="changeTree(index)"
                        required>
                </span>

                <span class="dsi-tooltip-container" :title=" messages.get('dsi', 'customizable_fields_template_name_desc')">
                    <label class="etiquette" :for="'customizable-template-name-' + index">
                        {{ messages.get('dsi', 'customizable_fields_template_name') }}
                    </label>
                    <input type="text"
                        :id="'customizable-template-name-' + index"
                        :name="'customizable-template-name-' + index"
                        v-model="field.templateName"
                        required 
                        @change="changeTree(index)"
                        @keyup="controlTemplateName(index, $event)">
                    <span class="dsi-tooltip hide">{{ messages.get('dsi', `customizable_fields_template_name_validator`) }}</span>
                </span>

                <button type="button" class="bouton" @click="removeField(index)" :title=" messages.get('dsi', 'customizable_fields_remove')">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <div class="row dsi-margin-left-m dsi-br-s dsi-border-left" v-if="field.type && $options.components[fieldComponentName(field.type)]" >
                    <component :is="fieldComponentName(field.type)" :field="field" @updateTree="changeTree(index)"></component>
                </div>
            </div>
            
        </div>
        <div class="dsi-br-xl">
            <button type="button" class="bouton" @click="addField">
                {{ messages.get('dsi', 'customizable_fields_add') }}
            </button>
        </div>
    </fieldset>
</template>

<script>

import customizableFieldSelectorForm from './common/customizableFieldSelectorForm.vue';
import customizableFieldDimensionForm from './common/customizableFieldDimensionForm.vue';

export default {
    props: ["settings"],
    components: {
        customizableFieldSelectorForm,
        customizableFieldDimensionForm
    },
    data: function() {
        return {
            types: ["text", "selector", "list", "color", "dimension"]
        }
    },
    created: function() {
        if(!this.settings.customizableFields) {
            this.$set(this.settings, "customizableFields", []);
            // { type: "", name: "", data: {}}
        }

        this.updateTree();
    },
    methods: {
        addField: function() {
            this.$set(this.settings.customizableFields, this.settings.customizableFields.length, {
                type: "",
                templateName: "",
                name: "",
                data: {}
            });
        },
        removeField: function(index) {
            this.$delete(this.settings.customizableFields, index);
        },
        fieldComponentName: function(type) {
            return `customizableField${this.utils.capitalize(type)}Form`;
        },
        changeTree: function(index) {
            const customizableField = this.settings.customizableFields[index];

            if(customizableField && customizableField.templateName && customizableField.name && customizableField.type) {
                this.updateTree();
            }
        },
        updateTree: function() {
            let fields = [];
            this.settings.customizableFields.forEach(element => {
                if(element.type && element.templateName) {
                    fields.push(element);
                }
            });

            if(fields.length) {
                this.$root.$emit("updateCustomizableFieldsTree", fields);
            }
        },
        resetData : function(i) {
            if(this.settings.customizableFields[i]) {
                if(this.settings.customizableFields[i].data) {
                    this.$set(this.settings.customizableFields[i], "data", {});
                }
            }
        },
        controlTemplateName: function(i, event){
            let templateName = this.settings.customizableFields[i].templateName;
            let templateNameRegex = /[^a-zA-Z]+/g;
            
            if(templateName.match(templateNameRegex)) {
                event.target.nextElementSibling.classList.remove('hide');
                setTimeout(() => {event.target.nextElementSibling.classList.add('hide');}, 3000);
            }

            return this.settings.customizableFields[i].templateName = this.settings.customizableFields[i].templateName.replace(templateNameRegex, '');
        },

        moveUp: function(i) {
            if(i > 0) {
                const temp = this.settings.customizableFields[i];
                
                this.$set(this.settings.customizableFields, i, this.settings.customizableFields[i - 1]);
                this.$set(this.settings.customizableFields, i - 1, temp);
            }
        },
        moveDown: function(i) {
            if(i < this.settings.customizableFields.length-1) {
                const temp = this.settings.customizableFields[i];

                this.$set(this.settings.customizableFields, i, this.settings.customizableFields[i + 1]);
                this.$set(this.settings.customizableFields, i + 1, temp);
            }
        }
    },
        
}

</script>