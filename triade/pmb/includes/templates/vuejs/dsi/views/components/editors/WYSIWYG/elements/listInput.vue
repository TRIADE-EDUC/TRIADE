<template>
    <div :id="block.id" class="wysiwyg-section" :style="block.style" @click.prevent="$root.$emit('editBlock', block)">
        <actionsBlock 
			:block="block" 
			:parentBlock="$parent.block" 
			:blockLabels="blockLabels" 
			:blockTypes="blockTypes" 
			:root="false" 
			:parentView="parent">
		</actionsBlock>

        <ul>
            <li :style="block.list.style" v-for="(element, index) in block.list.elements" :key="index">{{ element }}
            </li>
        </ul>
    </div>
</template>

<script>
import actionsBlock from "../actionsBlock.vue";

export default {
    name: "listInput",
    props: ['block', 'blockTypes', 'blockLabels', 'parent'],
    components: {
        actionsBlock
    },
    created: function () {
        if (!this.block.id) {
            this.block.id = `${Date.now().toString(36)}${Math.random().toString(36).substring(2)}`;
        }

        this.block.style = {
            display: "flex",
            flexDirection: this.block.style.flexDirection || "column",
            flexGrow: 1,
            flex: 1,
            minHeight: "32px"
        };

        if (!this.block.list) {
            this.$set(this.block, "list", {});
            this.$set(this.block.list, "style", { lineHeight: "10px", fontSize: "18px" });
            this.$set(this.block.list, "elements", ["Item 1", "Item 2", "Item 3"]);
        }
    }
}
</script>