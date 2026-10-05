<template>
    <div>
        <div :title="messages.get('dsi', 'customizable_fields_options_multiple')">
            <label for="selector-type">{{ messages.get("dsi", "customizable_fields_selector_form_multiple") }}</label>
            <input type="checkbox" name="selector-type" v-model="field.data.multiple" @change="$emit('updateTree')" />
        </div>
        <div v-for="(option, i) in field.data.options" :key="i">
            <span :title="messages.get('dsi', 'customizable_fields_selector_form_option_name_desc')">
                <label :for="'option-name-' + i" class="etiquette">{{ messages.get('dsi', 'customizable_fields_selector_form_option_name') }}</label>
                <input :name="'option-name-' + i" required v-model="option.label" />
            </span>
            <span :title="messages.get('dsi', 'customizable_fields_selector_form_option_value_desc')">
                <label :for="'option-value-' + i" class="etiquette">{{ messages.get('dsi', 'customizable_fields_selector_form_option_value') }}</label>
                <input :name="'option-value-' + i" required v-model="option.value" />
            </span>
            <button class="bouton" @click.prevent="removeOption(i)" :title="messages.get('dsi', 'customizable_fields_selector_form_remove_option')">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <button class="bouton" @click.prevent="addOption()" :title="messages.get('dsi', 'customizable_fields_selector_form_add_option')">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
    </div>
</template>

<script>
export default {
    name : "customizableFieldSelectorForm",
    props : ["field"],
    created : function() {
        if(! this.field.data.options) {
            this.$set(this.field.data, "options", []);
            this.addOption();
        }
        if(! this.field.data.multiple) {
            this.$set(this.field.data, "multiple", false);
        }
    },
    methods : {
        addOption : function() {
            let option = {
                value : "",
                label : ""
            };
            this.$set(this.field.data.options, this.field.data.options.length, option);
        },
        removeOption : function(i) {
            if(this.field.data.options[i]) {
                this.$delete(this.field.data.options, i);
            }
        }
    }
}
</script>