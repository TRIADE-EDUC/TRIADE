<template>
	<div class="dsi-view-add" ref="view_form">
		<splitpanes class="default-theme">
			<pane min-size="50" size="70">
				<div id="dsi-view-form" :style="getViewStyle">
					<formView
						:types="types"
						:view="view"
						:entities="entities"
						:is_model="true"
						:fullscreen="isFullScreen"></formView>
				</div>
			</pane>

			<pane size="30" v-if="displayEdition">
				<div class="dsi-diffusion-aside" ref="dsi-diffusion-aside">
					<!-- <div class="dsi-diffusion-hide" aria-expanded="true" aria-controls="dsi-diffusion-accordion">
						<button type="button" class="bouton" @click="hideAccordion($event)">
							<i aria-hidden="true" class="dsi-diffusion-accordion-arrow fa fa-chevron-right" style="font-size: small;"></i>
						</button>
					</div> -->
					<div class="dsi-diffusion-accordion" ref="dsi-diffusion-accordion">
						<accordion :title="messages.get('dsi', 'view_form_accordion_edit')" expanded="true" index="1" key="1">
							<div>
								<component :is="typesControls[editBlock.type]" :block="editBlock" :view="view"></component>
							</div>
						</accordion>
					</div>
				</div>
			</pane>
		</splitpanes>
	</div>
</template>

<script>
	import formView from "./formView.vue";
	import accordion from "../../../common/accordion/accordion.vue";

	import blockForm from "./editors/WYSIWYG/controls/blockForm.vue";
	import textInputForm from "./editors/WYSIWYG/controls/textInputForm.vue";
	import listInputForm from "./editors/WYSIWYG/controls/listInputForm.vue";
	import imageInputForm from "./editors/WYSIWYG/controls/imageInputForm.vue";
	import videoInputForm from "./editors/WYSIWYG/controls/videoInputForm.vue";
	import textEditorInputForm from "./editors/WYSIWYG/controls/textEditorInputForm.vue";
    import viewInputForm from "./editors/WYSIWYG/controls/viewInputForm.vue";
    import viewImportInputForm from "./editors/WYSIWYG/controls/viewImportInputForm.vue";

	import { Splitpanes, Pane } from 'splitpanes';
	import 'splitpanes/dist/splitpanes.css';

	export default {
		props : ["view", "types", "is_model", "entities"],
		components : {
		    formView,
			accordion,
			blockForm,
			textInputForm,
			listInputForm,
			imageInputForm,
			videoInputForm,
			textEditorInputForm,
            viewInputForm,
            viewImportInputForm,
			Splitpanes,
			Pane
		},
		data: function() {
			return {
				editBlock: {},
				typesControls: {},
				authorizedViewTypes: [],
				unAuthorizedEditBlockTypes: [],
				isFullScreen: false
			}
		},
		created: function() {
			this.typesControls = this.Const.views.wysiwygControls;
			this.authorizedViewTypes = this.Const.views.editableViews;
			this.unAuthorizedEditBlockTypes = this.Const.views.uneditableViews;
			this.view.model = this.is_model;
			window.addEventListener("fullscreenchange", (e) => {
				if (!document.fullscreenElement && this.isFullScreen) {
					this.isFullScreen = false;
				}
			});
			this.$root.$on("fullscreen", this.switchFullScreen);
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
				}, 75);

				let blockElement = document.getElementById(block.id);
				if(blockElement) {
					blockElement.className = "wysiwyg-section wysiwyg-section-selected";
				}
			});
		},
		computed: {
			getViewStyle: function() {
				if(this.view.type == 2 && Object.keys(this.editBlock).length !== 0) {
					return "grid-column-end: 5;";
				}
				return "grid-column-end: 7;";
			},
			displayEdition: function() {
				if(this.view.model && this.unAuthorizedEditBlockTypes.includes(this.editBlock.type)) {
					return false;
				}

				return this.authorizedViewTypes.includes(this.view.type) && !this.utils.isEmptyObj(this.editBlock);
			}
		},
		methods: {
			hideAccordion: function(event) {
				let aside = this.$refs["dsi-diffusion-aside"];
				let accordion = this.$refs["dsi-diffusion-accordion"];
				let view = document.getElementById("dsi-view-form");

				let enabled = event.target.parentElement.getAttribute("aria-expanded") === 'true' ? true : false;
				event.target.parentElement.setAttribute("aria-expanded", !enabled);

				if(!enabled) {
					aside.style.gridColumnStart = "5";
					view.style.gridColumnEnd = "5";
					accordion.style.display = "block";
					event.target.innerText = ">"
					return;
				}

				view.style.gridColumnEnd = "7";
				aside.style.gridColumnStart = "7";
				accordion.style.display = "none";
				event.target.innerText = "<"
			},
			switchFullScreen: function() {
				this.isFullScreen = !this.isFullScreen;
				if(document.fullscreenElement) {
					document.exitFullscreen();
                    return;
                }
				this.$refs.view_form.requestFullscreen();
			}
		},
    }
</script>