<template>
    <import-table v-if="Object.keys(items).length" :items="items" :id-entity="idEntity" @cancelImport="resetComponent" @addItem="addItem"></import-table>
    <div v-else class="dsi-import-items">
        <source-item v-if="Object.keys(item).length" :types="types" :item="item" :importType="idEntity"></source-item>
        <div class="align-right">
            <input v-if="sourceFilled" type="button" style="cursor:pointer;" :value="messages.get('common', 'search')" @click.prevent="importItems" />
        </div>
    </div>
</template>
<script>
import sourceItem from "../../../items/components/sourceItem.vue";
import importTable from './importTable.vue';

export default {
    props : ["types", "idEntity"],
    components : {
        sourceItem,
        importTable
    },
    computed: {
        sourceFilled : function() {
            if(this.item.settings && this.item.settings.namespace) {
                if(this.item.settings.selector && this.item.settings.selector.namespace) {
                    if(this.item.settings.selector.namespace != "") {
                        return true;
                    }
                }
            }
            return false;
        }
    },
    data: function() {
        return {
            item: {},
            items: []
        }
    },
    created : async function() {
        await this.init();
    },
    watch: {
        idEntity: async function() {
            await this.init();
        }
    },
    methods: {
        init: async function()
        {
            this.$set(this, "item", await this.ws.get("items", "getEmptyInstance"));
        },
        importItems: async function()
        {
            let response = await this.ws.post("items", "getItemsFromList", this.item);
            if (response.error) {
				this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
                this.$set(this, "items", response);
			}
        },
        addItem: function(item) {
            this.$emit("importItem", item);
        },
        resetComponent : async function()
        {
            this.$set(this, "items", []);
            await this.init();
        }
    }
}
</script>