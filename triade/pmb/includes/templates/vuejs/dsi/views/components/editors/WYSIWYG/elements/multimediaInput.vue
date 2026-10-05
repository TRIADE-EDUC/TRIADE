<template>
    <div :id="block.id" class="wysiwyg-section" :style="block.style.block"  @click.prevent="$root.$emit('editBlock', block)">
        <actionsBlock 
			:block="block" 
			:parentBlock="$parent.$parent.block" 
			:blockLabels="blockLabels" 
			:blockTypes="blockTypes" 
			:root="false" 
			:parentView="parent">
		</actionsBlock>

        <slot name="media"></slot>
    </div>
</template>

<script>
import actionsBlock from "../actionsBlock.vue";

export default {
    props: ['block', 'parent', 'blockLabels', 'blockTypes'],
    components: {
        actionsBlock
    },
    created: function() {
        if (!this.block.id) {
            this.block.id = `${Date.now().toString(36)}${Math.random().toString(36).substring(2)}`;
        }

        if (!this.block.style.block) {
            this.$set(this.block.style, "block", {});
        }

        this.block.style.block["display"] = "flex";
        this.block.style.block["flex-grow"] = 1;
        this.block.style.block["flex"] = 1;
        // this.block.style["min-height"] = "32px";
        this.block.style["min-height"] = "min-content";

        if (! this.block.style.block["justify-content"]) {
            this.$set(this.block.style.block, "justify-content", "start");
        }
        if (! this.block.style.block["align-items"]) {
            this.$set(this.block.style.block, "align-items", "start");
        }

        if (this.block.style.block.textAlign) {
            this.block.style.block["justify-content"] = this.block.style.block.textAlign;
            delete this.block.style.block.textAlign;
        }
    },
    methods: {
        changeImage(event) {
            let files = event.target.files || event.dataTransfer.files;
            if (!files.length) {
                return;
            }
            this.createImage(files[0]);
        },
        createImage(file) {
            let image = new Image();
            let reader = new FileReader();

            reader.onload = (e) => {
                image = e.target.result;
                this.$set(this.block, "content", image);
            };
            reader.readAsDataURL(file);
        }
    }
}
</script>