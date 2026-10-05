<template>
    <div :id="block.id" class="wysiwyg-section" :style="block.style"  @click.prevent="$root.$emit('editBlock', block)">
        <actionsBlock 
			:block="block" 
			:parentBlock="$parent.block" 
			:blockLabels="blockLabels" 
			:blockTypes="blockTypes" 
			:root="false" 
			:parentView="parent">
		</actionsBlock>

        <div style="all: initial" v-html="block.content"></div>
    </div>
</template>

<script>
import actionsBlock from "../actionsBlock.vue";

export default {
    props : ['block', 'blockTypes', 'blockLabels', 'parent'],
    components: {
        actionsBlock
    },
    created : function() {
        if(! this.block.content) {
            this.block.content = "<p style='margin: 0px;'>Texte...<p>";
        }

        if(!this.block.id) {
            this.block.id = Date.now().toString(36) + Math.random().toString(36).substring(2);
        }

        this.block.style["display"] = "flex";

        if(!this.block.style.flexDirection) {
            this.$set(this.block.style, "flexDirection", "column")
        }

        this.block.style["flex-grow"] = 1;
        this.block.style["flex"] = 1;
        // this.block.style["min-height"] = "32px";
        this.block.style["min-height"] = "min-content";

        if(!this.block.text) {
            this.$set(this.block, "text", {});
            this.$set(this.block.text, "style", {});
        }
    }
}
</script>