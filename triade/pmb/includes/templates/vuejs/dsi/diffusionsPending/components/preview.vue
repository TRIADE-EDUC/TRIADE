<template>
    <div>
        <h2 class="section-sub-title">{{ history.diffusion.name }}</h2>
        <div class="dsi-form-diffusion-view dsi-form-diffusion-pending-view">
            <div class="dsi-diffusion-view">
                <ace_editor v-if="view.type == 1 || view.type == 4" :entities="entities" :item="view" :itemEntities="[view.settings.entityType]"></ace_editor>
				<WYSIWYGEditor v-if="view.type == 2" :view="view"></WYSIWYGEditor>
				<simple v-if="view.type == 3" :entities="entities" :item="view"></simple>
            </div>
            <div class="dsi-diffusion-aside">
                <accordion v-if="view.type == 2 && Object.keys(editBlock).length !== 0"
                    :title="messages.get('dsi', 'view_form_accordion_edit')"
                    expanded="true"
                    index="2"
                    key="2">

					<div>
						<component v-if="item"
                            :is="WYSIWYGtypes[editBlock.type]"
                            :view="view"
                            :block="editBlock"
                            :viewTypes="viewTypes"
                            :item="item"
                            :entityId="history.numDiffusion">
                        </component>
					</div>
				</accordion>
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
import accordion from "../../../common/accordion/accordion.vue";

import ace_editor from '../../views/components/editors/Django/aceEditor.vue';
import WYSIWYGEditor from '../../views/components/editors/WYSIWYG/WYSIWYG.vue';
import simple from '../../views/components/editors/Simple/simple.vue';

import blockForm from "../../views/components/editors/WYSIWYG/controls/blockForm.vue";
import textInputForm from "../../views/components/editors/WYSIWYG/controls/textInputForm.vue";
import listInputForm from "../../views/components/editors/WYSIWYG/controls/listInputForm.vue";
import imageInputForm from "../../views/components/editors/WYSIWYG/controls/imageInputForm.vue";
import videoInputForm from "../../views/components/editors/WYSIWYG/controls/videoInputForm.vue";
import textEditorInputForm from "../../views/components/editors/WYSIWYG/controls/textEditorInputForm.vue";
import viewInputForm from "../../views/components/editors/WYSIWYG/controls/viewInputForm.vue";
import viewImportInputForm from "../../views/components/editors/WYSIWYG/controls/viewImportInputForm.vue";

export default {
    name: "preview",
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
        accordion,
        ace_editor,
        WYSIWYGEditor,
        simple,
        blockForm,
        textInputForm,
        listInputForm,
        imageInputForm,
        videoInputForm,
        textEditorInputForm,
        viewInputForm,
        viewImportInputForm
    },
    data: function() {
        return {
            WYSIWYGtypes: {},
            viewTypes: [],
            viewType: 0,
            itemType: 0,
            editBlock: {}
        }
    },
    mounted: async function() {
        if (typeof domUpdated === "function") {
            domUpdated();
        }
    },
    created: async function() {
        this.WYSIWYGtypes = this.Const.views.wysiwygControls;
        this.viewType = this.Const.views.types.simple;
        this.itemType = this.Const.items.types.aggregator;
        this.viewTypes = await this.ws.get('views', 'getTypeListAjax');
        this.$root.$on("editBlock", (block) => {
            if(this.editBlock && this.editBlock.id) {
                let lastBlockElement = document.getElementById(this.editBlock.id);
                if(lastBlockElement) {
                    lastBlockElement.className = "wysiwyg-section";
                }
                
                if(this.editBlock.id === block.id) {
                    this.$set(this, "editBlock", {});
                    return;
                }
            }
            
            this.$set(this, "editBlock", {});

            setTimeout(() => {
                this.$set(this, "editBlock", block);
            }, 100);

            let blockElement = document.getElementById(block.id);
            if(blockElement) {
                blockElement.className = "wysiwyg-section wysiwyg-section-selected";
            }
        });
    },
    computed: {
        view: function() {
            let parent = this.history.contentBuffer[this.viewType].find((view) => view.content.numParent == 0);
            return parent.content;
        },
        item: function() {
            const items = this.history.contentBuffer[this.itemType];
            let item = {};
            items.forEach(element => {
                if(Object.keys(this.editBlock).length && this.editBlock.itemSelected == element.content.id) {
                    item = element.content;
                }
            });
            return item;
        }
    },
    methods: {
        save: async function() {
            await this.$root.saveContent(this.history.id, this.viewType, {"data": this.history.contentBuffer[this.viewType]});
        },
        cancel: function() {
            this.$root.close();
        },
        reset: async function() {
            this.$set(this.history.contentBuffer, this.viewType, await this.$root.resetContent(this.history.id, this.viewType));
            
            setTimeout(() => {
                this.$root.$emit("updateEditor");
            }, 500);
        }
    }
}
</script>