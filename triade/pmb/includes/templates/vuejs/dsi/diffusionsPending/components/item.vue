<template>
    <div>
        <h2 class="section-sub-title">{{ history.diffusion.name }}</h2>
        <div class="dsi-form-diffusion-edit">
            <div class="dsi-diffusion-view">
                <div class="wysiwyg-tree">
                    <itemTree :items="childs" :parent="parent" @displayItem="displayItem" :entities="entities"></itemTree>
                </div>
            </div>
            <div class="dsi-diffusion-aside">
                <div class="dsi-diffusion-item" v-if="selectedItem && selectedItem.name">
                    <h3 style="text-align: center;">{{ selectedItem.name }}</h3><hr>
                    <pagination-list :list="selectedItemList" :nbPage="4" :perPage="5" :startPage="1" :nbResultDisplay="false">
                        <template #content="{ list }">
                            <ul>
                                <li v-for="(item, index) in list" :key="index">
                                    <span>{{ item.value }}</span>
                                    <button v-if="!isRemoved(item.id)" class="bouton" type="button" @click="remove(item.id)">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                    <button v-else class="bouton" type="button" @click="remove(item.id)">
                                        <i class="fa fa-undo" aria-hidden="true"></i>
                                    </button>
                                </li>
                            </ul>
                        </template>
                    </pagination-list>
                </div>
                <div class="dsi-diffusion-add-item" v-if="selectedItem && selectedItem.name">
                    <manual-add :selectedItem="selectedItem" :entities="entities" @importItems="importItems"></manual-add>
                </div>
            </div>
        </div>
        <div class='row dsi-form-action dsi-diffusion-pending-actions'>
            <div class="dsi-diffusion-pending-actions-left">
                <input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
                <input name="submit" type="submit" class="bouton" :value="messages.get('common', 'submit')" @click="save">
            </div>
            <div class="dsi-diffusion-pending-actions-right">
                <input @click="reset" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'diffusion_pending_action_reset')"/>
            </div>
        </div>
    </div>
</template>
<script>
import itemTree from "./item/itemTree.vue";
import manualAdd from "./item/manualAdd.vue";
import paginationList from "../../components/paginationList.vue";

export default {
    name: "item",
    props: {
        history: {
            type: Object,
            required: true
        },
        entities: {
            type: Object,
            required: true
        }
    },
    components: {
        itemTree,
        paginationList,
        manualAdd
    },
    data: function() {
        return {
            type: 2,
            selectedItem: {},
            selectedRemovedItemList: {},
            selectedItemList: []
        }
    },
    mounted: function () {
        if (typeof domUpdated === "function") {
            domUpdated();
        }
    },
    computed: {
        items: function() {
            return this.history.contentBuffer[this.type];
        },
        parent: function() {
            return this.items.find(element => element.content.numParent == 0).content;
        },
        childs: function() {
            const childs = [];
            this.items.forEach(element => {
                if(element.content.numParent != 0) {
                    childs.push(element.content);
                }
            });
            return childs;
        }
    },
    methods: {
        switchTab: function(tab) {
            this.tabActive = tab;
        },
        displayItem: async function(item) {
            if(item.type != 0) {
                this.$set(this, "selectedItem", item);

                this.selectedItemList = [];
                await this.getSelectedItemList();
            }
        },
        getSelectedItemList: async function() {
            let response = await this.ws.post("items", "getItemsListLabel/", { "namespace": this.selectedItem.__class, "ids": this.selectedItem.results});
			if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
			} else {
                Object.entries(response).forEach(([key, value], index) => {
                    this.selectedItemList.push({"id": key, "value": value});
                });
			}
        },
        importItems: function(items) {
            for(const [item, key] of Object.entries(items)) {
                if(typeof this.selectedItem.results === 'array') {
                    if(!this.selectedItem.results.includes(item)) {
                        this.$set(this.selectedItem.results, this.selectedItem.results.length, item);
                        this.selectedItemList.push({"id": item, "value": key});
                    }
                } else {
                    let keys = Object.keys(this.selectedItem.results);
                    if(!keys.includes(item)) {
                        this.$set(this.selectedItem.results, item, key);
                        this.selectedItemList.push({"id": item, "value": key});
                    }
                }
            }
        },
        remove: function(id) {
            if(this.isRemoved(id)) {
                this.selectedItem["removed"].splice(this.selectedItem["removed"].findIndex(itemId => itemId == id), 1);
                return;
            }
            this.$set(this.selectedItem.removed, this.selectedItem.removed.length, id);
        },
        isRemoved: function(id) {
            if(this.selectedItem["removed"].find(itemId => itemId == id )) {
                return true;
            }
            return false;
        },
        save: async function() {
            await this.$root.saveContent(this.history.id, this.type, {"data": this.items});
        },
        cancel: function() {
            this.$root.close();
        },
        reset: async function() {
            this.$set(this.history.contentBuffer, this.type, await this.$root.resetContent(this.history.id, this.type));
        }
    }
}
</script>