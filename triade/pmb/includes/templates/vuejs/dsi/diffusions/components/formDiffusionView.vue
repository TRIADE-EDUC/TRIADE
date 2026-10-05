<template>
	<div class="dsi-form-diffusion-view" ref="view_form">
		<splitpanes class="default-theme">
			<pane min-size="50" size="70">
				<div v-if="viewTypes.length" ref="dsi-diffusion-view" class="dsi-diffusion-view">
					<formView
						:item-type="item.type"
						:types="viewTypes"
						:channelCompatibility="channelCompatibility"
						:view="view"
						:item="item"
						:entities="entityType"
						:is_model="false"
						:fullscreen="isFullScreen"
						:selectedAttachment="$parent.selectedAttachment"
						:idDiffusion="diffusion.id"
						:diffusion="diffusion"
						@saveDiffusionView="saveDiffusionView">
					</formView>
				</div>
  			</pane>
			<pane size="30">
				<div class="dsi-diffusion-aside" ref="dsi-diffusion-aside">
					<!-- <div aria-expanded="true"
						aria-controls="dsi-diffusion-accordion"
						class="dsi-diffusion-hide"
						ref="dsi-diffusion-hide" :style="style">
						<button type="button" class="bouton" @click.prevent="hideAccordion()">
							<i style="font-size: small"
								class="dsi-diffusion-accordion-arrow fa fa-chevron-right"
								aria-hidden="true"
								ref="dsi-diffusion-accordion-arrow">
							</i>
						</button>
					</div> -->
					<div class="dsi-diffusion-accordion dsi-sticky-menu"
						:class="scrollDetection"
						ref="dsi-diffusion-accordion">
						<accordion ref="accordionData" :title="messages.get('dsi', 'view_form_accordion_data')" index="1" key="1">
							<div class="dsi-diffusion-item">
								<formItemAdd
									:types="entityType"
									:item="item"
									:is_model="false"
									:viewType="view.type"
									@saveDiffusionItem="saveDiffusionItem"
									:is-only-for-aggregator="isOnlyForAggregator">
								</formItemAdd>
							</div>
						</accordion>
						<accordion
							ref="accordionEdit"
							v-if="(view.type == 2 || view.type == 9) && Object.keys(editedBlock).length !== 0"
							:title="messages.get('dsi', 'view_form_accordion_edit')"
							expanded="true"
							index="2"
							key="2">

							<div class="dsi-form-group-flex">
								<component
									:is="types[editedBlock.type]"
									:view="view"
									:block="editedBlock"
									:viewTypes="viewTypes"
									:item="item"
									:entityId="diffusion.id"
									:itemTypes="entityType"
									@saveDiffusionItem="saveDiffusionItem">
								</component>
							</div>
						</accordion>
					</div>
				</div>
  			</pane>
		</splitpanes>
	</div>
</template>

<script>
	import formView from "@dsi/views/components/formView.vue";
	import formItemAdd from "@dsi/items/components/add.vue";
	import accordion from "@/common/accordion/accordion.vue";

	import blockForm from "@dsi/views/components/editors/WYSIWYG/controls/blockForm.vue";
	import textInputForm from "@dsi/views/components/editors/WYSIWYG/controls/textInputForm.vue";
	import listInputForm from "@dsi/views/components/editors/WYSIWYG/controls/listInputForm.vue";
	import imageInputForm from "@dsi/views/components/editors/WYSIWYG/controls/imageInputForm.vue";
	import videoInputForm from "@dsi/views/components/editors/WYSIWYG/controls/videoInputForm.vue";
	import textEditorInputForm from "@dsi/views/components/editors/WYSIWYG/controls/textEditorInputForm.vue";
	import viewInputForm from "@dsi/views/components/editors/WYSIWYG/controls/viewInputForm.vue";
	import viewImportInputForm from "@dsi/views/components/editors/WYSIWYG/controls/viewImportInputForm.vue";

	import { Splitpanes, Pane } from 'splitpanes';
	import 'splitpanes/dist/splitpanes.css';

	export default {
		props : ["diffusion", "channelCompatibility", "attachment"],
        components: {
            formView,
            formItemAdd,
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
		data: function () {
			return {
			    entityType: [],
			    viewTypes: [],
				editedBlock: {},
				types: {},
				scrollDetection: "",
				isFullScreen: false
			}
		},
		created: async function() {
			this.types = this.Const.views.wysiwygControls;
			this.initListeners();
			await this.fetchData();
		},
		computed : {
			isOnlyForAggregator : function() {
				const aggregatorNamespace = this.Const.aggregatorItemNamespace;
				if(! this.viewTypes.length || ! this.view.type) {
					return false;
				}

				let currentType = this.viewTypes.find((type) => type.id == this.view.type);
				if (currentType && currentType.compatibility.item) {
					return currentType.compatibility.item.length == 1 && currentType.compatibility.item[0] == aggregatorNamespace;
				}

				return false;
			},
			view: function() {
				if (typeof this.attachment != "undefined") {
					return this.attachment.view;
				}
				return this.diffusion.view;
			},
			item: function() {
				if(typeof this.attachment != "undefined") {
					return this.attachment.item;
				}
				return this.diffusion.item;
			}
		},
		methods: {
			fetchData: async function() {
				const promises = [];

				// Vérifie si le type d'entité n'est pas déjà chargé
				const shouldFetchEntities = !this.$root.entities;
				if (shouldFetchEntities) {
					promises.push(this.ws.get('diffusions', 'getEntityList'));
				}

				// Vérifie si les types de vues ne sont pas déjà chargés
				const shouldFetchViews = !this.$root.types?.views;
				if (shouldFetchViews) {
					promises.push(this.ws.get('views', 'getTypeListAjax'));
				}

				// Exécute les requêtes en parallèle
				const results = await Promise.all(promises);

				// Index pour récupérer les résultats dans le bon ordre
				let index = 0;

				if (shouldFetchEntities) {
					this.entityType = results[index++];
				} else {
					this.entityType = JSON.parse(JSON.stringify(this.$root.entities));
				}

				if (shouldFetchViews) {
					this.viewTypes = results[index++];
				} else {
					this.viewTypes = JSON.parse(JSON.stringify(this.$root.types.views));
				}
			},
			initListeners: function() {
				let startScrollPosition = 0;

				window.addEventListener("scroll", (e) => {
					let endScrollPosition = window.pageYOffset;

					if(endScrollPosition >= startScrollPosition){
						this.scrollDetection = "dsi-scroll-down";
					}else{
						this.scrollDetection = "dsi-scroll-up";
					}

					startScrollPosition = endScrollPosition;
				});

				window.addEventListener("fullscreenchange", (e) => {
					if (!document.fullscreenElement && this.isFullScreen) {
						this.isFullScreen = false;
					}
				});

				this.$root.$on("editBlock", this.editBlock);
				this.$root.$on('removeBlock', this.removeBlock);

				this.$root.$on("saveDiffusion", this.saveDiffusion);
				this.$root.$on("fullscreen", this.switchFullScreen);

				//Event pour ouvrir les tabs
				this.$root.$on("openDiffusionViewTab", this.openDiffusionViewTab);
			},
			saveDiffusionView: async function(idView) {
				if(typeof this.attachment != "undefined") {
					this.diffusion.settings.attachments[this.$parent.selectedAttachment].view = idView
				} else {
					this.diffusion.numView = idView;
				}
				await this.saveDiffusion();
			},
			saveDiffusionItem: async function(idItem) {
				if(typeof this.attachment != "undefined") {
					this.diffusion.settings.attachments[this.$parent.selectedAttachment].item = idItem
				} else {
					this.diffusion.numItem = idItem;
				}
				await this.saveDiffusion();
			},
			saveDiffusion: async function() {
				let response = await this.ws.post('diffusions', 'save', this.diffusion);
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					this.notif.info(this.messages.get('common', 'success_save'));
				}
			},
			hideAccordion: function() {
				let aside = this.$refs["dsi-diffusion-aside"];
				let accordion = this.$refs["dsi-diffusion-accordion"];
				let view = this.$refs["dsi-diffusion-view"];
				let arrow = this.$refs["dsi-diffusion-accordion-arrow"];
				let hide = this.$refs["dsi-diffusion-hide"];

				let enabled = hide.getAttribute("aria-expanded") === 'true' ? true : false;
				hide.setAttribute("aria-expanded", !enabled);

				if(!enabled) {
					aside.style.gridColumnStart = "5";
					view.style.gridColumnEnd = "5";
					accordion.style.display = "block";
					arrow.classList = "fa fa-chevron-right";
					return;
				}

				view.style.gridColumnEnd = "7";
				aside.style.gridColumnStart = "7";
				accordion.style.display = "none";
				arrow.classList = "fa fa-chevron-left";
			},
			switchFullScreen: function() {
				this.isFullScreen = !this.isFullScreen;
				if(document.fullscreenElement) {
					document.exitFullscreen();
                    return;
                }
				this.$refs.view_form.requestFullscreen();
			},
			blockIsInView: function(blockId, blocks) {
				for (let block of blocks) {
					if (block.id == blockId) {
						return true;
					}

					if (block.blocks.length && this.blockIsInView(blockId, block.blocks)) {
					    return true;
					}
				}

				return false;
			},
			editBlock: function(block) {
				if(this.attachment == undefined && this.diffusion.view.settings && this.diffusion.view.settings.layer == undefined) {
					return;
				}

				let view = null;
				if(this.attachment == undefined) {
					view = this.diffusion.view;
				} else {
					view = this.attachment.view;
				}

				let foundBlock = false;
				if(this.blockIsInView(block.id, view.settings.layer.blocks)) {
					foundBlock = true;
				}

				if(!foundBlock) {
					for(let childView of view.childs) {
						if(
							childView.settings.layer &&
							childView.settings.layer.blocks &&
							this.blockIsInView(block.id, childView.settings.layer.blocks)
						) {
							foundBlock = true;
							break;
						}
					}
				}

				// Ancien fonctionnement, on regarder pas si le block est présent dans les vue enfantes (Cause des problèmes avec l'import de vue WYSIWYG)
				//let blocks = this.attachment == undefined ? this.diffusion.view.settings.layer.blocks : this.attachment.view.settings.layer.blocks;
				//if(this.blockIsInView(block.id, blocks))
				if (foundBlock) {
					if(this.editedBlock && this.editedBlock.id) {
						let lastBlockElement = document.getElementById(this.editedBlock.id);
						if(lastBlockElement) {
							lastBlockElement.className = "wysiwyg-section";
						}

						if(this.editedBlock.id === block.id) {
							this.$set(this, "editedBlock", {});
							return;
						}
					}

					this.$set(this, "editedBlock", {});

					setTimeout(() => {
						this.$set(this, "editedBlock", block);
					}, 100);

					let blockElement = document.getElementById(block.id);
					if(blockElement) {
						blockElement.className = "wysiwyg-section wysiwyg-section-selected";
					}
				}
			},
			removeBlock: function(data) {
				if (data.block.id === this.editedBlock.id) {
					this.$set(this, "editedBlock", {});
				}
			},
			openDiffusionViewTab: function(tabName) {
				let name = "accordion" + this.utils.capitalize(tabName);
				if(this.$refs[name] && (typeof this.$refs[name].open === "function")) {
					this.$refs[name].open();
				}
			}
		},
	}
</script>