<template>
    <div class="dsi-form-group-item" >
        <div class="dsi-form-group-line">
            <label class="etiquette" :for="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')">{{ field.name }}</label>
        </div>
        <div>
            <div class="dsi-form-group-filter dsi-form-group-flex">
                <div class="dsi-form-group">
                    <select :multiple="field.data.multiple" :name="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')" v-model="value">
                        <option value="" disabled>{{ messages.get("dsi", defaultMessage) }}</option>
                        <option v-for="(option, i) in field.data.options" :key="i" :value="option.value">{{ option.label }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name : "customizableFieldSelector",
    props: ["field"],
    created: function() {
        if(!this.field.data.value) {
            this.$set(this.field.data, "value", "");
        }
    },
    computed : {
        defaultMessage : function() {
            if(this.field.data.multiple) {
                return "customizable_fields_selector_default_multiple";
            }
            return "customizable_fields_selector_default";
        },
        value : {
            get : function() {
                if(this.field.data.multiple) {
                    if(Array.isArray(this.field.data.value)) {
                        return this.field.data.value
                    } else {
                        return [];
                    }
                } else {
                    if(typeof this.field.data.value === "string") {
                        return this.field.data.value
                    } else {
                        return "";
                    }
                }
            },
            set : function(value) {
                this.$set(this.field.data, "value", value);
            }
        }
    }
}
</script>