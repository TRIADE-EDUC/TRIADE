<template>
    <div v-if="!view.id">
        <span>{{ messages.get('dsi', 'view_wysiwyg_view_unable_to_save') }}</span>
    </div>
    <div v-else :id="block.id" class="wysiwyg-section" :style="block.style" @click.self="$root.$emit('editBlock', block)">
        <actionsBlock 
			:block="block" 
			:parentBlock="$parent.block" 
			:blockLabels="blockLabels" 
			:blockTypes="blockTypes" 
			:root="root" 
			:parentView="parent">
		</actionsBlock>

        <div v-if="childView && childView.settings">
            <lockable :locked="childView.settings.locked">
                <div v-if="childView && childView.settings && childView.settings.layer">
                    <block v-for="(block, index) in childView.settings.layer.blocks" :key="index" :block="block"
                        :root="true" :blockTypes="blockTypes" :blockLabels="blockLabels" :view="view"
                        :parent="childView"></block>
                </div>
                <div v-else>
                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                </div>
            </lockable>
        </div>
    </div>
</template>

<script>
import actionsBlock from "../actionsBlock.vue";
import lockable from '../../../../../components/lockable.vue';
export default {
    name: 'viewImportInput',
    props: ['block', 'blockTypes', 'blockLabels', 'view', "root", "parent"],
    components: {
        //VUEJS fait chier quand on utilise un composant parent en sous composant
        //Donc on force l'import
        block: () => import('./block.vue'),
        lockable,
        actionsBlock
    },
    created: function () {
        if (!this.block.id) {
            this.block.id = `${Date.now().toString(36)}${Math.random().toString(36).substring(2)}`;
        }
        if (!this.block.style) {
            this.$set(this.block, "style", {});
        }
        if (!this.block.content) {
            this.$set(this.block, "content", {});
        }
        if (!this.block.content.viewId) {
            this.$set(this.block.content, "viewId", 0);
        }
        this.block.style["display"] = "flex";

        if (!this.block.style.flexDirection) {
            this.$set(this.block.style, "flexDirection", "column")
        }
        this.block.style["flex-grow"] = 1;
        this.block.style["flex"] = 1;
        // this.block.style["min-height"] = "32px";
        this.block.style["min-height"] = "min-content";
    },
    computed: {
        childView: function () {
            if (this.block.content.viewId == 0) {
                return {}
            }
            let view = this.view.childs.find((v) => v.id == this.block.content.viewId);
            return typeof view === 'undefined' ? {} : view;
        }
    },
    methods: {
        deleteView: async function() {
            if(this.block.content && this.block.content.viewId) {
                let response = await this.ws.post("views", 'delete', { id: this.block.content.viewId});
                if (response.error) {
                    this.notif.error(this.messages.get('dsi', response.errorMessage));
                } else {
                    this.$root.$emit('removeBlock', { block: this.block, parent: this.parent })
                }
            }
        }
    }
}
</script>
<style scoped>
::v-deep .wysiwyg-section:hover {
    border: 1px solid #ff9ed0 !important;
    padding-top: 25px;
}

::v-deep .wysiwyg-section-selected {
    border: 1mm ridge #ff9ed0 !important;
}
</style>