<template>
    <fieldset class="dsi-fieldset-aggregator">
        <legend class="dsi-legend-aggregagor">{{ messages.get('dsi', 'items_form_agregator_data') }}</legend>
        <div class="dsi-form-group-item">
            <label class="etiquette"
                for="itemTypeList">{{ messages.get('dsi', 'items_form_agregator_with_model') }}</label>
            <select @change="addChild">
                <option value="" disabled selected>{{ messages.get('dsi', 'model_selector_default_value') }}</option>
                <option v-for="(model, index) in getFilterListModel" :key="index" :value="index">{{ model.name }}
                </option>
            </select>
        </div>
        <div class="dsi-form-group-item">
            <label class="etiquette"
                for="itemTypeList">{{ messages.get('dsi', 'items_form_agregator_with_new') }}</label>
            <button class="bouton dsi-button" type="button" @click="openNewTab(null)"
                :title="messages.get('dsi', 'add')">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>
        <div v-if="item.childs.length" class="dsi-form-item-agregator">
            <paginationList :filter-fields="fields" :list="item.childs" format="table" :perPage="5" :startPage="1"
                :nbPage="5" :nbResultDisplay="false">
                <template #content="{ list }">
                    <table class="uk-table uk-table-small uk-table-striped uk-table-middle">
                        <thead>
                            <tr>
                                <th>{{ messages.get('dsi', 'items_form_agregator_name') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(child, subindex) in list" :key="subindex">
                                <td>{{ child.name }}<b>{{ child.id ? "" : " *" }}</b></td>
                                <td class="dsi-table-right dsi-inline">
                                    <button class="bouton" type="button" @click="editChild(child.uid)"
                                        :title="messages.get('dsi', 'agregator_edit_item')">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </button>
                                    <button class="bouton" type="button" @click="removeChild(child.uid)"
                                        :title="messages.get('dsi', 'agregator_edit_remove')">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </paginationList>
        </div>
    </fieldset>
</template>

<script>
import paginationList from "@dsi/components/paginationList.vue";
export default {
    props: ["item", "rootItem", "uid", "parentUid", "is_model"],
    data: function () {
        return {
            listModels: [],
            fields: [
                {
                    name: "name",
                    label: "items_form_agregator_name",
                    type: "text"
                }
            ],
            uniqueId: this.getUID(),
        }
    },
    components: {
        paginationList
    },
    created: function () {
        this.getListModel();

        let nameEvent = "saveOnAgregator";
        nameEvent += ("_" + this.uid);
        this.$root.$on(nameEvent, this.saveOnAgregator);
        for (let child of this.item.childs) {
            child.uid = this.getUID();
        }
    },
    computed: {
        getFilterListModel: function () {
            let findInRoot = (id, item) => {
                if (item.id === id) {
                    return true;
                }
                for (const child of item.childs) {
                    if (child.childs.length) {
                        return findInRoot(id, child)
                    }
                }
                return false;
            }

            let listModel = [];

            if (this.listModels.length) {
                for (let model of this.listModels) {
                    if (!findInRoot(model.id, this.rootItem === "" ? this.item : this.rootItem)) {
                        listModel.push(model);
                    }
                }
            }

            return listModel;
        }
    },
    methods: {
        getListModel: async function (refresh = false) {
            var list = [];

            if (this.$root.models && this.$root.models["items"] && !refresh) {
                list = JSON.parse(JSON.stringify(this.$root.models["items"]));
            } else {
                list = await this.ws.get("items", "getModels");
            }

            if (list.length) {
                const index = list.findIndex(item => item.id === this.item.id);
                if (index !== -1) {
                    list.splice(index, 1);
                }
            }

            this.$set(this, "listModels", list);
        },
        removeChild: function (uid) {
            let i = this.item.childs.findIndex(child => child.uid === uid);
            if (i !== -1) {
                this.item.childs.splice(i, 1);
            }
        },
        addChild: function ($event) {
            let child = this.listModels[$event.target.selectedIndex - 1];

            child.numModel = child.id;
            child.model = 0;
            child.numParent = this.item.id;
            child.id = 0;
            child.uid = this.getUID();

            this.$set(this.item.childs, this.item.childs.length, this.listModels[$event.target.selectedIndex - 1])

            $event.target.selectedIndex = "";
        },
        editChild: function (uid) {
            let i = this.item.childs.findIndex(child => child.uid === uid);
            if (i !== -1) {
                this.openNewTab(this.item.childs[i]);
            }
        },
        openNewTab: function (item) {
            this.$root.$emit("openNewTab", this.uid, item);
        },
        saveOnAgregator: function (item, uid, action) {
            if (action === "add") {
                this.$set(this.item.childs, this.item.childs.length, item)
            }
            this.$root.$emit("closeNewTab", uid, this.uid);
        },
        getUID: function () {
            return Date.now().toString(36) + Math.random().toString(36).substring(2);
        }
    }
}

</script>