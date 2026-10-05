<template>
    <div>
        <label for="customizable-dimension">{{ messages.get("dsi", "customizable_fields_dimension_form_label") }}</label>
        <div v-for="(dimension, i) in field.data.dimensions" :key="i">
            <span :title="messages.get('dsi', 'customizable_fields_dimension_form_name_desc')">
                <label class="etiquette">{{ messages.get("dsi", "customizable_fields_dimension_form_name") }}</label>
                <input v-model="dimension.label" />
            </span>
            <span :title="messages.get('dsi', 'customizable_fields_dimension_form_value_desc')">
                <label class="etiquette">{{ messages.get("dsi", "customizable_fields_dimension_form_value") }}</label>
                <input v-model="dimension.value" />
            </span>
            <button class="bouton" @click.prevent="removeDimension(i)" :title="messages.get('dsi', 'customizable_fields_selector_form_remove_option')">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <button class="bouton" @click.prevent="addDimension()" :title="messages.get('dsi', 'customizable_fields_selector_form_add_option')">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
    </div>
</template>

<script>
export default {
    name : "customizableFieldDimensionForm",
    props : ["field"],
    created : function() {
        if(! this.field.data.dimensions ) {
            this.$set(this.field.data, "dimensions", []);
            this.addDimension();
        }
    },
    methods : {
        addDimension : function() {
            let dimension = {
                "value" : "",
                "label" : ""
            };
            this.$set(this.field.data.dimensions, this.field.data.dimensions.length, dimension);
        },
        removeDimension : function(i) {
            if(this.field.data.dimensions[i]) {
                this.$delete(this.field.data.dimensions, i);
            }
        }
    }
}
</script>