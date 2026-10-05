<template>
    <div class="dsi-form-group" v-if="items.length">
        <label for="selectItem">{{ messages.get("dsi", "dsi_private_form_selected_item") }}</label>
        <div class="dsi-form-group-content">
            <div v-for="(item, i) in items" :key="i">
                <label :for="'item-' + item.id">{{item.name}}</label>
                <input v-model="formData.selectedItem" required name="selectItem" :id="'item-' + item.id" type="radio" :value="item.id" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name : "formItemsDiffusionPrivate",
    props : ["formData"],
    data : function() {
        return {
            items : []
        }
    },
    watch : {
        "formData.diffusionModel" : function() {
            this.getItems();
        }
    },
    mounted : function() {
        if(this.formData.diffusionModel) {
            this.getItems();
        }
    },
    methods : {
        getItems : async function() {
            this.items = await this.ws.get("diffusionsPrivate", "items/" + this.formData.diffusionModel);
        }
    }
}
</script>