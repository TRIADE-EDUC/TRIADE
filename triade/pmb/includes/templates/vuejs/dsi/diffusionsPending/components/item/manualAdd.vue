<template>
    <div>
        <h3 style="text-align: center;">{{ messages.get('dsi', 'subscriber_accordion_manual_add') }}</h3><hr>
        <div class="dsi-tabs">
            <div class="dsi-tab-registers">  
                <button @click.prevent="switchTab(1)" :class="[tabActive == 1 ? 'active-tab bouton' : 'bouton']">
                    {{ messages.get('dsi', 'subscriber_tabs_add') }}
                </button>    
                <button @click.prevent="switchTab(2)" :class="[tabActive == 2 ? 'active-tab bouton' : 'bouton']">
                    {{ messages.get('dsi', 'subscriber_tabs_import') }}
                </button>    
            </div>
            <div class="dsi-tab-bodies">
                <div class="dsi-content" v-show="tabActive == 1">
                    <label for="itemAddById">{{ messages.get('dsi', 'diffusion_pending_add_by_id') }}</label>
                    <input type="text" name="itemAddById" id="itemAddById" v-model="searchId">
                    <input type="button" class="bouton" value="Ajouter" :disabled="!searchId || searchId == 0" @click="importById">
                </div>
                <div class="dsi-content" v-show="tabActive == 2">
                    <import-items :types="entities" :idEntity="selectedItem.type" @importItem="importItem"></import-items>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import importItems from "./importItems.vue";
export default {
    name: "manualAdd",
    props: {
        selectedItem: {
            type: Object,
            required: true
        },
        entities: {
            type: Object,
            required: true
        }
    },
    components: {
        importItems
    },
    data: function() {
        return {
            tabActive: 1,
            searchId: ""
        }
    },
    methods: {
        switchTab: function(tab) {
            this.tabActive = tab;
        },
        importById: async function() {
            let response = await this.ws.post("items", "getItemsListLabel/", { "namespace": this.selectedItem.__class, "ids": [this.searchId]});
			if (response.length == 0) {
                this.notif.error(this.messages.get("dsi", "unknow_item"));
			} else {
                this.$emit("importItems", response);
			}
        },
        importItem: function(item) {
            this.$emit('importItems', item)
        }
    }
}
</script>