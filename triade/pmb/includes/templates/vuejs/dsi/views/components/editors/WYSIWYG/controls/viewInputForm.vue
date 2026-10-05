<template>
    <div v-if="!view.model">
        <formModal v-if="displayModalItem"
            :title="messages.get('dsi', 'view_wysiwyg_associated_add_item')"
            formClass="wysiwyg-form-view" ref="modal_add_item"
            @close="closeModalItem">

            <div class="form-contenu">
                <formItem
                    :types="itemTypes"
                    :item="item"
                    :is_model="false"
                    :viewType="viewSelected.type"
                    :tab_index="0"
                    :is-only-for-aggregator="false"
                    :modalItem="true"
                    :uid="Date.now().toString(36) + Math.random().toString(36).substring(2)"
                    @saveDiffusionItem="closeModalItem(true)">
                </formItem>
            </div>
        </formModal>

        <formModal v-if="displayModalModelView"
            :title="messages.get('dsi', 'view_wysiwyg_associated_add_view_model')"
            formClass="wysiwyg-form-view" ref="modal_add_model_view"
            @close="closeModalModelView">

            <div class="form-contenu">
                <ul class="wysiwyg-form-modal-view-list">
                    <li v-for="(model, index) in viewModels" :key="index" @click="addViewFromModel(index)">
                        <img :src="getViewModelImg(model)" :title="getViewTypeNameById(model.type)" :alt="getViewTypeNameById(model.type)">
                        <span>{{ model.name }}</span>
                    </li>
                </ul>
            </div>
        </formModal>

        <div v-if="! viewSelected && viewModels.length > 0" class="dsi-form-group-item">
            <label class="etiquette">{{ messages.get('dsi', 'view_wysiwyg_form_with_model') }}</label>
            <button type="button"
                class="bouton"
                @click="showModalModelView">
                {{ messages.get('dsi', 'view_wysiwyg_form_with_model_btn') }}
            </button>
        </div>

        <div v-if="! viewSelected" class="dsi-form-group-item">
            <label class="etiquette">{{ messages.get('dsi', 'view_wysiwyg_load_view') }}</label>
            <button class="bouton dsi-button" type="button" @click="$root.$emit('addTabView', {blockId : block.id, view : null, item : null})">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>
        <div v-if=" viewSelected" class="dsi-form-group-item">
            <label class="etiquette" for="viewSelected">{{ messages.get('dsi', 'view_wysiwyg_associated_view_label') }}</label>
            <span name="viewSelected">
                <span class="wysiwyg-form-view-label">
                    <b>{{ viewSelected.name }}</b> <i>({{ getViewTypeNameById(viewSelected.type) }})</i>
                </span>
                <span role="button" class="span-button" @click="editView">
                    <i class="fa fa-pencil" aria-hidden="true" :title="messages.get('dsi', 'view_wysiwyg_associated_edit_view_title')"></i>
                </span>
                <span role="button" class="span-button" @click="removeView">
                    <i class="fa fa-times" aria-hidden="true" :title="messages.get('dsi', 'view_wysiwyg_associated_remove_view_title')"></i>
                </span>
            </span>
        </div>

        <div v-if="viewSelected && showSelectedItem">
            <div class="dsi-form-group-item">
                <label class="etiquette" for="itemSelected" style="margin-right: 1rem;">{{ messages.get('dsi', 'item_associated_label') }}</label>
                <select id="itemSelected" name="itemSelected" v-model="block.itemSelected" required>
                    <option value="" disabled>{{ messages.get('dsi', 'items_form_choose_type') }}</option>
                    <option v-for="(item, index) in filteredItems" :key="index" :value="item.id">
                        {{ item.name }}
                    </option>
                </select>
                <span role="button" class="span-button" @click="showModalItem">
                    <i class="fa fa-plus" aria-hidden="true" :title="messages.get('dsi', 'view_wysiwyg_associated_add_item')"></i>
                </span>
                <span role="button" class="span-button" @click="editItem">
                    <i class="fa fa-pencil" aria-hidden="true" :title="messages.get('dsi', 'view_wysiwyg_associated_edit_item_title')"></i>
                </span>
            </div>
        </div>

        <div class="row" v-if="viewSelected && viewSelected.settings.groups && block.itemSelected">
            <hr />
        </div>

        <fieldset v-if="viewSelected && itemSelected" class="dsi-fieldset-filters">
            <div class="dsi-form-group-line">
                <legend>{{ messages.get('dsi', 'view_form_filter') }}</legend>
            </div>
            <form-filters :view="viewSelected" :item="itemSelected"></form-filters>
            <form-group :view="viewSelected" :item="itemSelected"></form-group>
        </fieldset>

        <fieldset v-if="customizableFieldsSet.length" class="dsi-fieldset-filters">
            <div class="dsi-form-group-line">
                <legend>{{ messages.get('dsi', 'customizable_fields_title_edition') }}</legend>
            </div>
            <component v-for="(field, index) in customizableFieldsSet"
                :is="getCustomizableFieldName(field.type)" :key="index" :field="field"></component>
        </fieldset>

        <div class="dsi-form-group-line">
            <input
                class="bouton"
                type="button"
                @click="loadRender"
                :disabled="(!itemSelected) && showSelectedItem"
                :value="messages.get('dsi', 'view_wysiwyg_load_simple_view')">
        </div>
    </div>
</template>
<script>
import formModal from '@/common/components/FormModal.vue';
import formItem from "@dsi/items/components/formItem.vue";
import formFilters from '@dsi/diffusions/components/formFilters.vue';
import formGroup from '@dsi/views/components/formGroup.vue';

import customizableFieldText from '@dsi/components/CustomizableFields/common/customizableFieldText.vue';
import customizableFieldColor from '@dsi/components/CustomizableFields/common/customizableFieldColor.vue';
import customizableFieldSelector from '@dsi/components/CustomizableFields/common/customizableFieldSelector.vue';
import customizableFieldList from '@dsi/components/CustomizableFields/common/customizableFieldList.vue';
import customizableFieldDimension from '@dsi/components/CustomizableFields/common/customizableFieldDimension.vue';

export default {
    name : "viewInputForm",
    props : ['block', 'view', "viewTypes", "item", "entityId", "itemTypes"],
    components: {
        formFilters,
        formGroup,
        formModal,
        formItem,

        customizableFieldText,
        customizableFieldColor,
        customizableFieldSelector,
        customizableFieldList,
        customizableFieldDimension
    },
    data: function () {
        return {
            viewModels: [],
            previousEntityType: "",
            displayModalItem: false,
            displayModalModelView: false
        }
    },
    computed : {
        itemSelected : function() {
            if(this.block.itemSelected && this.item) {
                return this.item.childs.find(child => child.id == this.block.itemSelected);
            }
            return null;
        },
        customizableFieldsSet : function() {
            if(this.viewSelected && this.viewSelected.settings && this.viewSelected.settings.customizableFields) {
                return this.viewSelected.settings.customizableFields.filter(f => f.type != '');
            }
            return [];
        },
        viewSelected: function() {
            if (this.block.viewSelected && this.view) {
                return this.view.childs.find(child => child.id == this.block.viewSelected);
            }
            return null;
        },
        filteredItems: function() {
            if(! this.item.id) {
                return [];
            }
            if(this.block.viewSelected && this.item && this.item.childs && this.item.childs.length) {
                return this.getFilteredItems(this.item.childs, this.viewSelected.settings.entityType);
            }
            return [];
        },
        showSelectedItem : function() {
            const unshowViewType = this.Const.views.viewsWithoutItemsIds;
            if(this.viewSelected && this.viewSelected.type) {
                return !unshowViewType.includes(this.viewSelected.type);
            }
            return true;
        }
    },
    created: async function() {
        if(this.block && this.block.viewSelected === undefined) {
            this.$set(this.block, "viewSelected", "");
        }
        if(this.block && this.block.itemSelected === undefined) {
            this.$set(this.block, "itemSelected", "");
        }
        this.getListModel();

        this.$root.$on("saveViewTab", await this.saveViewTab);
    },
    methods: {
        getFilteredItems(items, entityType) {
            let filteredItems = items.filter(item => item.id && item.type == entityType);
            for (let item of items) {
                if (item.childs && item.childs.length) {
                    filteredItems.push(...this.getFilteredItems(item.childs, entityType));
                }
            }
            return filteredItems;
        },
        getViewTypeNameById: function(idViewType) {
            if (!this.viewTypes) {
                return "";
            }
            let i = this.viewTypes.findIndex(t => t.id === idViewType);
            if(i == -1) {
                return "";
            }
            return this.viewTypes[i].name;
        },
        loadRender: async function() {
            const typeWYSIWYG = this.Const.views.types.wysiwyg;

            const limit = this.view.type == typeWYSIWYG ? this.Const.views.limit : 0;
            let item = this.block.itemSelected ? this.block.itemSelected : 0;
            const method = `render/${this.block.viewSelected}/${item}/${this.entityId}/${limit}/${this.$root.categ}`;

            let response = await this.ws.post("views", 'save', this.view);
            if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
                return;
            }

            const content = await this.ws.get('views', method);
            if (content.error) {
                this.notif.error(this.messages.get('dsi', content.errorMessage));
                return;
            }

            this.$set(this.block, "content", content[0]);
        },
        getListModel: async function() {
            let list = await this.ws.get("views", "getModels");
            if (list.length && this.item && this.item.id) {
                const index = list.findIndex(item => item.id === this.item.id);
                if(index !== -1) {
                    list.splice(index, 1);
                }
            }

            // Filtre des modèles de vue WYSIWYG
            list = list.filter((m) =>  m.type != 2);

            this.$set(this, "viewModels", list);
        },
        addViewFromModel: async function(index) {
            let view = this.viewModels[index];

            view.id = 0;
            view.model = false;
            view.settings.image = "";

            let response = await this.ws.post('views', 'save', view);
            if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
            } else {
                view.id = response.id;

                this.$set(this.block, "itemSelected", "");

                this.$set(this.view.childs, this.view.childs.length, view);
                this.$set(this.block, "viewSelected", view.id);

                await this.ws.post("views", 'save', this.view);

                this.closeModalModelView();
            }
        },
        removeView: async function() {
            if (confirm(this.messages.get('dsi', 'confirm_del'))) {
                let response = await this.ws.post("views", 'delete', this.viewSelected);
                if (response.error) {
                    this.notif.error(this.messages.get('dsi', response.errorMessage));
                    return false;
                }

                // On met a jour le tableau child
                const index = this.view.childs.findIndex(viewChild => viewChild.id == this.block.viewSelected);
                if (index != -1) {
                    this.$delete(this.view.childs, index);
                }

                this.$set(this.block, "viewSelected", "");
                this.$set(this.block, "itemSelected", "");
                this.$set(this.block, "content", "");

                await this.ws.post("views", 'save', this.view);
            }
        },
        saveViewTab: async function(event) {
            if(event.blockId != this.block.id) return;

            this.$set(this.block, "viewSelected", event.view.id);

            if (
                event.view.id != this.block.viewSelected ||
                event.view.settings.entityType != this.previousEntityType
            ) {
                this.$set(this.block, "itemSelected", "");
                this.$set(this.block, "content", "");
            }

            // On met a jour la vue dans le tableau child
            const index = this.view.childs.findIndex(viewChild => viewChild.id == event.view.id);
            if (index != -1) {
                this.$set(this.view.childs, index, event.view);
            } else {
                this.view.childs.push(event.view);
            }

            await this.ws.post("views", 'save', this.view);
            this.$root.$emit("closeViewTab", {view: this.view});
        },
        editView: function() {
            this.previousEntityType = this.viewSelected.settings.entityType || "";
            this.$root.$emit('addTabView', {blockId : this.block.id, view : this.viewSelected, item : this.itemSelected});
        },
        showModalItem: function() {
            this.displayModalItem = true;
            this.$nextTick(() => {
                this.$refs.modal_add_item.show();
            });
        },
        closeModalItem: function(setLastItem = false) {
            this.displayModalItem = false;
            if(setLastItem) {
                this.$refs.modal_add_item.close();

                setTimeout(() => {
                    this.block.itemSelected = this.filteredItems[this.filteredItems.length-1].id;
                }, 300);

                this.$emit("saveDiffusionItem", this.item.id);
            }
        },
        showModalModelView: function() {
            this.displayModalModelView = true;
            this.$nextTick(() => {
                this.$refs.modal_add_model_view.show();
            });
        },
        closeModalModelView: function() {
            this.displayModalModelView = false;
        },
        getViewModelImg: function(viewModel) {
            if(viewModel.settings.image) {
                return viewModel.settings.image;
            }

            let i = this.viewTypes.findIndex(t => t.id == viewModel.type);

            if(i != -1 && this.viewTypes[i].default_model_image) {
                return this.viewTypes[i].default_model_image;
            }

            return "";
        },
        getCustomizableFieldName: function(type) {
            return `customizableField${this.utils.capitalize(type)}`;
        },
        editItem : function() {
            this.$root.$emit("openNewTab", null, this.itemSelected);
            this.$root.$emit("openDiffusionViewTab", "data");
        }
    }
}
</script>