<template>
	<div 
		:id="block.id" 
		class="wysiwyg-section" 
		:style="style" 
		@click.self="$root.$emit('editBlock', block)">

		<actionsBlock 
			:block="block" 
			:parentBlock="$parent.block" 
			:blockLabels="blockLabels" 
			:blockTypes="blockTypes" 
			:root="root" 
			:parentView="parent" 
			@showAddBlock="show = !show">
		</actionsBlock>

		<component v-for="(element, index) in block.blocks"
                   :key="index"
                   :is="blockTypes[element.type]"
                   :view="view" :block="element"
                   :blockTypes="blockTypes"
                   :blockLabels="blockLabels"
                   :root="false"
                   :parent="parent">
        </component>
		<addBlock :view="view" :blocks="block.blocks" :show="show" @close="show = false" :root="false"></addBlock>
	</div>
</template>

<script>
	import addBlock from "../addBlock.vue";
	import textInput from "./textInput.vue";
	import imageInput from "./imageInput.vue";
	import videoInput from "./videoInput.vue";
	import listInput from "./listInput.vue";
	import textEditorInput from "./textEditorInput.vue";
	import viewInput from "./viewInput.vue";
	import viewImportInput from "./viewImportInput.vue";
	import actionsBlock from "../actionsBlock.vue";

	export default {
        name: "block",
		props: ["block", "blockTypes", "blockLabels", "root", "view", "parent"],
		components: {
			addBlock,
			textInput,
			imageInput,
			videoInput,
			listInput,
			textEditorInput,
			viewInput,
			viewImportInput,
			actionsBlock
        },
		data: function () {
			return {
				show: false,
				STYLE_CONST: {
					HORIZONTAL: "row",
					VERTICAL: "column"
				}
			}
		},
		created: function() {
			if(!this.block.id) {
				this.block.id = Date.now().toString(36) + Math.random().toString(36).substring(2);
			}

			this.block.style["display"] = "flex";

			if(!this.block.style.flexDirection) {
				if (this.root) {
					this.$set(this.block.style, "flexDirection", this.STYLE_CONST.VERTICAL);
				} else {
					this.$set(this.block.style, "flexDirection", this.STYLE_CONST.HORIZONTAL);
				}
			}

			if(!this.block.widthEnabled) {
				this.block.style["flex"] = 1;
				this.block.style["flex-grow"] = 1;
			}
			// this.block.style["min-height"] = "32px";
			this.block.style["min-height"] = "min-content";
		},
		computed: {
			style: function() {
				let style = this.helper.cloneObject(this.block.style);
				if (style['padding-top']) {
					style['padding-top'] = `CALC(25px + ${style['padding-top']})`;
				}
				return style;
			}
		}
	}
</script>