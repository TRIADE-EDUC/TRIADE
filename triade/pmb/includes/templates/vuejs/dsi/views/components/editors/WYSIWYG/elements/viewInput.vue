<template>
    <div v-if="! view.id">
        <span>{{ messages.get('dsi', 'view_wysiwyg_view_unable_to_save') }}</span>
    </div>
    <div v-else :id="block.id" class="wysiwyg-section" :style="block.style" @click.prevent="$root.$emit('editBlock', block)">
        <actionsBlock 
			:block="block" 
			:parentBlock="$parent.block" 
			:blockLabels="blockLabels" 
			:blockTypes="blockTypes" 
			:root="root" 
			:parentView="parent">
		</actionsBlock>

        <div v-if="block.content">
            <div style="all: initial" v-html="block.content"></div>
        </div>
        <div v-else>
            <i class="fa fa-clipboard" aria-hidden="true"></i>
        </div>
    </div>
</template>

<script>
import actionsBlock from "../actionsBlock.vue";

export default {
    name : 'viewInput',
    props : ['block', 'blockTypes', 'blockLabels', 'view', "root", "parent"],
    components: {
        actionsBlock
    },
    created: function() {
        if (!this.block.id) {
            this.block.id = `${Date.now().toString(36)}${Math.random().toString(36).substring(2)}`;
        }
        if(! this.block.style) {
            this.$set(this.block, "style", {});
        }

        this.block.style["display"] = "flex";

        if(!this.block.style.flexDirection) {
            this.$set(this.block.style, "flexDirection", "column")
        }

        this.block.style["flex-grow"] = 1;
        this.block.style["flex"] = 1;
        // this.block.style["min-height"] = "32px";
        this.block.style["min-height"] = "min-content";
    }
}
</script>