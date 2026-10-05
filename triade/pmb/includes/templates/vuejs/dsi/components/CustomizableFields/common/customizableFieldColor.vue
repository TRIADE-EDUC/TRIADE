<template>
    <div class="dsi-form-group-item" >
        <div class="dsi-form-group-line">
            <label class="etiquette" :for="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')">{{ field.name }}</label>
        </div>
        <div>
            <div class="dsi-form-group-filter dsi-form-group-flex">
                <div class="dsi-form-group">
                    <input type="color" :name="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')" v-model="field.data.value">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["field"],
        created: function() {
            if(!this.field.data.value) {
                this.$set(this.field.data, "value", "#ffffff");
            }
        }
    }
</script>