<template>
    <import-view v-if="block.blocks.length == 0" :parent-view-id="view.id" @addView="addView($event)"></import-view>
    <div v-else>
        <div>
            <h3>{{ messages.get('dsi', 'view_wysiwyg_view') }}</h3>
            <div class="dsi-form-group-item dsi-form-wysiwyg-35">
                <label class="etiquette" for="wysiwyg-placement-width">{{ messages.get('dsi', 'view_wysiwyg_delete_view') }}</label>
                <button class="dsi-button bouton right" type="button" @click="removeView">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="dsi-form-group-item dsi-form-wysiwyg-35">
                <label class="etiquette" for="itemSelected" style="margin-right: 1rem;">Verrou :</label>
                <button v-if="childView && childView.settings && childView.settings.locked" type="button" @click="lock(false)" class="bouton dsi-button right">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </button>
                <button v-else type="button" @click="lock(true)" class="bouton dsi-button right">
                    <i class="fa fa-unlock" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import importView from '../../../importView.vue';

export default {
    name : "viewImportInputForm",
    props : ['block', 'view'],
    components : {
        importView
    },
    computed : {
        childView : function() {
            if(this.block.content.viewId == 0) {
                return {}
            }
            let view = this.view.childs.find((v) => v.id == this.block.content.viewId);
            return typeof view === 'undefined' ? {} : view;
        }
    },
    methods : {
        addView : function(view) {
            // if(! view.settings.layer.blocks.length) {
            //     return;
            // }
            this.emptyBlocksIds(view.settings.layer.blocks);
            this.$set(this.block.content, 'viewId', view.id);
            this.$set(this.block, "blocks", view.settings.layer.blocks);
            this.$set(this.view.childs, this.view.childs.length, view);
            this.$root.$emit("saveView");
        },
        removeView : async function() {
            this.$set(this.block, "blocks", []);
            if(this.block.content.viewId) {
                let response = await this.ws.post("views", "delete", {"id" : this.block.content.viewId});
                if( ! response.error) {
                    let i = this.view.childs.findIndex(v => v.id == this.block.content.viewId);
                    if(i != -1) {
                        this.$delete(this.view.childs, i);
                        this.$set(this.block.content, "viewId", 0);
                        this.$root.$emit("saveView");
                    }
                }
            }
        },
        emptyBlocksIds : function(blocks) {
            for(let block of blocks) {
                block.id = "";
                if(block.blocks.length) {
                    this.emptyBlocksIds(block.blocks);
                }
            }
        },
        lock : function(lock) {
            this.$set(this.childView.settings, "locked", lock);
        }
    }
}
</script>