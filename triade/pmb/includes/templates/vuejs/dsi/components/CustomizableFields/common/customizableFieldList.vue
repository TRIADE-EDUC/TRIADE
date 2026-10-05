<template>
    <div class="dsi-form-group-item" >
        <div class="dsi-form-group-line">
            <label class="etiquette" :for="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')">{{ field.name }}</label>
        </div>
        <div>
            <div class="dsi-form-group-filter dsi-form-group-flex">
                <div class="dsi-form-group" v-for="(element, i) in field.data.list" :key="i">
                    <div class="dsi-form-group-content">
                        <input v-model="element.value" type="text" :name="'field-' + field.name.toLowerCase().replace(/[^a-zA-Z0-9]/g, '')" />
                        <button class="bouton" @click.prevent="removeListElement(i)">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="dsi-form-group dsi-br-s">
                    <button class="bouton" @click.prevent="addListElement()">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name : "customizableFieldList",
    props: ["field"],
    created: function() {
        if(!this.field.data.list) {
            this.$set(this.field.data, "list", []);
            this.addListElement();
        }
    },
    methods : {
        addListElement : function() {
            let element = {
                value : ""
            };
            this.$set(this.field.data.list, this.field.data.list.length, element);
        },
        removeListElement : function(i) {
            if(this.field.data.list[i]) {
                this.$delete(this.field.data.list, i);
            }
        }
    }
}
</script>