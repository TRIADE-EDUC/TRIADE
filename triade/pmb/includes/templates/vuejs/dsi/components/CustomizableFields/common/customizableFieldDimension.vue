<template>
    <div class="dsi-form-group-item" >
        <div class="dsi-form-group-line">
            <label class="etiquette" :for="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')">{{ field.name }}</label>
        </div>
        <div>
            <div class="dsi-form-group-filter dsi-form-group-flex">
                <div class="dsi-form-group">
                    <input type="number" step="0.01" :name="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')" v-model="field.data.values.value" />
                    <select name="customizable-field-dimension-select" v-model="field.data.values.dimension">
                        <option value="" disabled>{{ messages.get("dsi", "customizable_fields_dimension_default") }}</option>
                        <option v-for="(dimension, i) in field.data.dimensions" :key="i" :value="dimension.value">{{ dimension.label }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name : "customizableFieldDimension",
    props: ["field"],
    created: function() {
        if(!this.field.data.values) {
            this.$set(this.field.data, "values", {
                "dimension" : "",
                "value" : ""
            });
        }
    }
}
</script>