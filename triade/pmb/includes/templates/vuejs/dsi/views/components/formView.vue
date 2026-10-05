<template>
	<div class="dsi_form">
		<form name="dsi_form_view" action="#" method="POST" class="dsi-form-view" @submit.prevent="submit">
			<modelSelector
				v-if="!fullscreen"
				entity="views"
			 	:idSelected="view.numModel"
				:id="view.id"
				:restricted-fields="restrictedFields"
				:compatibility="compatibilityModel"
				@updateSelectedModel="addModel"
				ref="modelSelector"
				:item="view"
				:showLock="view.type != Const.views.types.wysiwyg"
				:parentId="id">
			</modelSelector>
			<lockable :locked="view.settings.locked">
				<div v-if="!fullscreen">
					<formModal
					:title="messages.get('dsi', 'view_configuration_modal_title')"
					formClass="modal-form-view-configuration" ref="modal">

						<formViewConfiguration
							:view="view"
							:types="types"
							:entities="entities"
							:isModel="is_model"
							:channelCompatibility="channelCompatibility"
							:parent-id="id"
							:isAttachment="selectedAttachment != -1 && selectedAttachment != undefined ? true : false">
						</formViewConfiguration>
					</formModal>

					<div v-if="(is_model || isTabForm)" class="dsi-form-group">
						<label class="etiquette" for="view-name">{{ messages.get('dsi', 'view_form_name') }}</label>
						<div class="dsi-form-group-content">
							<input type="text" id="view-name" name="view-name" v-model="view.name" required>
						</div>
					</div>

					<div class="dsi-form-group">
						<label class="etiquette" for="viewTypeList">{{ messages.get('dsi', 'view_form_type') }}</label>
						<div class="dsi-form-group-content view-form-selected-type">
							<!-- <select id="viewTypeList" name="viewTypeList" v-model="view.type" required>
								<option value="" disabled>{{ messages.get('dsi', 'view_form_default_type') }}</option>
								<option v-for="(type, index) in filteredTypes" :key="index" :value="type.id" :disabled="isTabForm && type.id == 2">
									{{ type.name }}
								</option>
							</select> -->
							<span>
								{{ currentType && currentType.name ? currentType.name : messages.get('dsi', 'view_form_change_type_empty') }}
							</span>
							<button
								class="bouton"
								type="button"
								:title="messages.get('dsi', 'view_form_change_type_title')"
								@click.prevent="$root.$emit('openViewConfiguration', { method : 2, id : id });">

								<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
							</button>
						</div>
					</div>

					<div v-if="is_model && view.type" class="dsi-form-group">
						<label class="etiquette" for="view-image">{{ messages.get('dsi', 'view_form_image') }} :</label>
						<div v-if="!view.settings.image" class="dsi-form-group-content">
							<input type="file" accept="image/*" id="view-image" name="view-image" @change="changeImage">
						</div>

						<div v-else class="dsi-form-group-content">
							<img :src="getViewImage" width="32" height="32">
							<button type="button" class="bouton" @click="view.settings.image = ''">
								<i class="fa fa-times" aria-hidden="true"></i>
							</button>
						</div>
					</div>

					<tags
						v-if="view.id"
						:tags="view.tags"
						entity="views"
						:entity-id="view.id"
						@newTagList="$set(view, 'tags', $event)"></tags>
					<fieldset v-if="diffusion !== undefined && item != undefined && item.type" class="dsi-fieldset-filters">
						<div class="dsi-form-group">
							<legend class="etiquette">{{ messages.get('dsi', 'view_form_filter') }}</legend>
							<div class="dsi-form-group-content">
								<form-filters :view="view" :item="item"></form-filters>
								<form-group :view="view" :item="item"></form-group>
							</div>
						</div>
					</fieldset>


					<div class="dsi-form-group" v-if="! is_model && currentType.limitable">
						<label class="etiquette" for="viewTypeList">{{ messages.get('dsi', 'view_form_limit') }}</label>
						<div class="dsi-form-group-content">
							<input v-model="view.settings.limit" type="number" min="0" />
						</div>
					</div>
				</div>
				<component :is="componentName"
					:view="view"
					:id-diffusion="idDiffusion"
					:id-item="item && item.idItem && item.type == 0 ? item.idItem : 0"
					:entities="entities"
					:item="view"
					:item-entities="itemEntities"
					:show-entity-type="showEntityType"
					:selectedAttachment="selectedAttachment">
				</component>
				<template v-if="!is_model && currentType.previewable && idDiffusion">
					<button class="dsi-button bouton" type="button" @click="displayPreview"
						:title="showPreview ? messages.get('dsi', 'hidden_preview') : messages.get('dsi', 'show_preview')">
						<i :class="['fa', showPreview ? 'fa-eye-slash' : 'fa-eye']" aria-hidden="true"></i>
					</button>
					<button class="dsi-button bouton" type="button" @click="realoadPreview" v-if="showPreview"
						:title="messages.get('dsi', 'refresh_preview')">
						<i class="fa fa-refresh" aria-hidden="true"></i>
					</button>
					<iframe v-if="showPreview"
						id="dsi-preview-frame"
						:srcdoc="messages.get('dsi', 'dsi_preview_loading')"
						:src="previewUrl"
						width="100%"
						:key="updatePreview"
						sandbox="allow-same-origin"
						@load="resizeFrame">
					</iframe>
				</template>
				<customizableFields v-if="showCustomizableFields" :settings="view.settings"></customizableFields>
			</lockable>
			<div class='row dsi-form-action'>
				<div class="left">
					<template v-if="is_model">
						<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
						<input name="submit_model" type="submit" class="bouton" :value="$root.action == 'edit' ? messages.get('common', 'submit') : messages.get('dsi', 'submit_model')">
					</template>

					<template v-if="!is_model">
						<input name="submit"  type="submit" class="bouton" :value="messages.get('common', 'submit')">
						<input @click="showModal = true" type="button" class="bouton" :value="messages.get('dsi', 'submit_model')">
					</template>
				</div>
				<div v-if="is_model && view.id" class="right">
					<input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')"/>
				</div>
			</div>
			<modalModelSelector :showModal="showModal" :entity="view" @close="showModal = false"></modalModelSelector>
		</form>
	</div>
</template>

<script>
    import ParserHtml from './editors/ParserHtml/ParserHtml.vue';
    import previousDSI from './editors/PreviousDSI/previousDSI.vue';
    import ace_editor from './editors/Django/aceEditor.vue';
    import WYSIWYGEditor from './editors/WYSIWYG/WYSIWYG.vue';
	import simple from './editors/Simple/simple.vue';
	import rss from './editors/RSS/rss.vue';
	import exportView from './editors/Export/export.vue';
	import group from './editors/Group/group.vue';
	import groupSummary from './editors/Group/summary.vue';
	import cartView from "./editors/Cart/cart.vue";
	import cartSimpleView from "./editors/Cart/cartSimple.vue";
	import agnosticAceEditor from './editors/Django/agnosticAceEditor.vue';
	import modelSelector from "@dsi/components/modelSelector.vue";
	import modalModelSelector from "@dsi/components/modalModelSelector.vue";

	import tags from '@dsi/components/tags.vue';
	import lockable from '@dsi/components/lockable.vue';
	import formFilters from '@dsi/diffusions/components/formFilters.vue';
	import formGroup from './formGroup.vue';
	import formViewConfiguration from './formViewConfiguration.vue';

	import customizableFields from '@dsi/components/CustomizableFields/customizableFields.vue';

	import formModal from '@/common/components/FormModal.vue';

	export default {
		props : [
			"view",
			"types",
			"entities",
			"is_model",
			"itemType",
			"isTabForm",
			"fromWysiwygViewId",
			"fullscreen",
			"item",
			"channelCompatibility",
			"idDiffusion",
			"selectedAttachment",
			"diffusion"
		],
		components: {
			modelSelector,
			modalModelSelector,
			tags,
			simple,
			lockable,
            formFilters,
			formGroup,

            ace_editor,
			WYSIWYGEditor,
			ParserHtml,
			previousDSI,
			rss,
			exportView,
			group,
			groupSummary,
			cartView,
			cartSimpleView,
			agnosticAceEditor,
			formViewConfiguration,

			customizableFields,
			formModal
        },
		data: function () {
			return {
				model: null,
				showModal: false,
				filteredTypes: [],
				restrictedFields: [],
				filters: [],
				showPreview: false,
				updatePreview: false,
				fromAddModel: false,
				id : 0
			}
		},
		watch : {
			"itemType": async function() {
				await this.getFilteredTypes();
			},
			"channelCompatibility": async function() {
				await this.getFilteredTypes();

				let isCompatibleWithView = this.channelCompatibility.compatibility.view.find((view) => {
					if(view == this.currentType.namespace) {
						return true;
					}
				});

				if(!isCompatibleWithView) {
					this.view.settings = {};
					this.view.type = ""
				}
			},
			"view.type": function() {
				let isAttachment = false;
				for(let attachment of this.diffusion.attachments) {
					if(attachment.view.id == this.view.id) {
						isAttachment = true;
						break;
					}
				}

				if(!this.fromAddModel && !isAttachment) {
					this.reset();
				}
			}
		},
		created: async function() {
			this.initView();
			this.initListeners();
			this.restrictedFields = this.Const.views.restrectedFields;
			this.id = Date.now().toString(36) + Math.random().toString(36).substring(2);
			await this.getFilteredTypes();
		},
		updated: function() {
			if(this.view.type == 0) {
				this.view.type = "";
			}
		},
		computed: {
			previewUrl: function() {
                return "rest.php/dsi/diffusions/preview/" + this.idDiffusion;
            },
			itemEntities : function() {
				let entities = [];
				if(! this.item) {
					if(! this.view.settings.entityType) {
						return [];
					}
					return [this.view.settings.entityType];
				}
				if(this.item.type != 0) {
					return [this.item.type];
				}
				for(let child of this.item.childs) {
					if(child.type) {
						entities.push(child.type);
					}
				}
				return [...new Set(entities)];
			},
			componentName : function() {
				const types = this.Const.views.types;
				switch(this.view.type) {
					case types.django:
					case types.rawText:
					case types.aggregatedDjango:
					case types.aggregatedRawText:
						return "ace_editor";
					case types.wysiwyg:
					case types.wysiwygPdf:
						return "WYSIWYGEditor";
					case types.simple:
						return "simple";
					case types.parserHtml:
						return "ParserHtml";
					case types.rss:
						return "rss";
					case types.export:
						return "exportView";
					case types.group:
						return "group";
					case types.summary:
						return "group-summary";
					case types.cart:
						return "cartView";
					case types.agnosticDjango:
						return "agnosticAceEditor";
					case types.previousDSI:
					case types.previousDSIPdf:
						return "previousDSI";
					case types.simpleCart:
						return "cartSimpleView";
				}
			},
			showEntityType: function() {
				const aggregatedTypes = this.Const.views.aggregatedViewsIds;
				return !aggregatedTypes.includes(this.view.type);
			},
			compatibilityModel: function() {
				return this.filteredTypes?.map(view => view.id) ?? [];
			},
			getViewImage: function() {
				if(this.view.settings.image) {
					return this.view.settings.image
				}

				let type = this.types.find(t => t.id == this.view.type);
				if(type) {
					return `images/dsi/views/${type.default_model_image}`;
				}

				return "";
			},
			currentType: function() {
				return this.types.find(type => type.id == this.view.type) || {};
			},
			showCustomizableFields : function() {
				if(! this.currentType.customizable) {
					return false;
				}
				if(! this.fromWysiwygViewId && ! this.view.model) {
					return false;
				}
				return true;
			}
		},
		methods: {
			cancel: function() {
				document.location = "./dsi.php?categ=views";
			},
			submit: async function(e = "") {
				let response = "";

				// Le formulaire à été soumis a partir du formulaire de modèle d'une vue
				let isSubmitFromModel = e?.submitter?.name === "submit_model";


				// Le formulaire à été soumis a partir du formulaire de modèle d'une vue dans une diffusion
				let isSubmitFromForm = e?.submitter?.name === "submit_model_from_modal" || e?.name === "submit_model_from_modal";

				if(isSubmitFromForm) {
					response = await this.ws.post('views', 'createModelFromDiffusion', this.view);

					if (!response.error) {
						this.$refs.modelSelector.getList();
						this.notif.info(this.messages.get('common', 'success_save'));

					} else {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					}

					this.showModal = false;
					return;
				}

				if(isSubmitFromModel) {
					this.view.model = true;
				}

				if (this.fromWysiwygViewId?.view) {
					this.$set(this.view, "numParent", this.fromWysiwygViewId.view)
				}

				response = await this.ws.post('views', 'save', this.view);
				if (!response.error) {
					if(this.is_model) {
						document.location = `./dsi.php?categ=views&action=edit&id=${response.id}`;
						return;
					}

					this.view.id = response.id;

					if (this.isTabForm) {
						this.$root.$emit("saveViewTab", {blockId : this.fromWysiwygViewId.block, view: this.view});
						return;
					}
					this.$emit("saveDiffusionView", response.id);

				} else {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				}
			},
			del: async function() {
				if (confirm(this.messages.get('dsi', 'confirm_del'))) {
					let response = await this.ws.post("views", 'delete', this.view);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
						document.location = "./dsi.php?categ=views";
                    }
                }
			},
			updateSettings: function(settings) {
				this.$set(this.view, "settings", settings);
			},
			addModel(model) {
				this.fromAddModel = true;
				if (model !== 0) {
					const clone = JSON.parse(JSON.stringify(model));
					const ignoreKeys = this.Const.views.ignoredKeys;

					for (const property in clone) {
						if (!ignoreKeys.includes(property)) {
							this.view[property] = clone[property];
						}
					}

					this.view.numModel = clone["id"];
				} else {
					this.view.numModel = 0;
					this.view.type = "";
					this.view.settings = {};
					this.initView();
				}

				this.view.model = 0;
				this.$nextTick(()=>{
					this.fromAddModel = false;
				})
			},
			initListeners: function() {
				this.$root.$on("saveView", () => {
					this.submit();
				});
				//evt venant de l'item
				this.$root.$on('updateItemType', (itemType) => {
					if(itemType != 0) {
						this.$set(this.view.settings, "entityType", itemType);
					}
				});
				this.$root.$on("importViewModel", (model) => {
					this.addModel(model);
				})
			},
			getFilteredTypes : async function() {
				if(! this.itemType || this.itemType == "") {
					this.$set(this, "filteredTypes", this.types);

					// Compatibility with channel
					this.filterTypesByChannel();
					return;
				}

				let compatibility = await this.ws.get('items', 'getCompatibility/'+ this.itemType);
                this.$set(this, "filteredTypes", []);

                for (let i in compatibility) {
					if (this.types.find(t => t.id == compatibility[i]) !== undefined) {
						this.$set(this.filteredTypes, this.filteredTypes.length, this.types.find(t => t.id == compatibility[i]));
					}
				}

				if (!this.filteredTypes.find(view => view.id == this.view.type)) {
					this.view.type = this.filteredTypes[0].id;
					this.$set(this, "view", this.view)
				}

				// Compatibility with channel
				this.filterTypesByChannel();
			},
			initView : function() {
				this.view.type = this.view.type == 0 ? "" : this.view.type;
				
				if(this.view.settings == "") {
					this.$set(this.view, "settings", {});
				}
				if((! this.view.settings.filter) && (! this.is_model)) {
					this.$set(this.view.settings, "filter", {});
					this.$set(this.view.settings.filter, "namespace", "");
				}
				if((! this.view.settings.limit) && (! this.is_model)) {
					this.$set(this.view.settings, "limit", 0);
				}
				if((! this.view.settings.image) && (this.is_model)) {
					this.$set(this.view.settings, "image", "");
				}
			},
			displayPreview: function() {
				this.showPreview = !this.showPreview;
			},
			realoadPreview: function() {
				this.updatePreview = !this.updatePreview;
			},
			reset: function() {
				this.resetPreview();
				this.$set(this.view, "settings", {});
				this.initView();
			},
			resetPreview: function() {
				let frame = document.getElementById("dsi-preview-frame");
				if (frame) {
					this.showPreview = false;
				}
			},
			resizeFrame: function() {
                let frame = document.getElementById("dsi-preview-frame");
                if (frame) {
                    const iframeDocument = (frame.contentDocument) ? frame.contentDocument : frame.contentWindow.document;

					let height = iframeDocument.documentElement.scrollHeight;
					if(height >= 800) {
						height = 800;
					}

					frame.removeAttribute('srcdoc');
					frame.style.height = height + 'px';

					const links = iframeDocument.querySelectorAll('a');
					// Boucler sur chaque lien et ajouter l'attribut target
					links.forEach(link => {
						link.setAttribute('target', '_top');
					});
                }
            },
			filterTypesByChannel: function() {
				if(this.channelCompatibility && typeof this.channelCompatibility != "undefined") {
					if(!this.channelCompatibility.compatibility.view) {
						this.$set(this, "filteredTypes", []);
					} else {
						let compatibilities = this.selectedAttachment != undefined ? this.channelCompatibility.compatibility.attachments.view : this.channelCompatibility.compatibility.view;
						let types = this.filteredTypes.filter(type => {
							let compatibleView = compatibilities.find(view => view == type.namespace);
							return compatibleView != undefined;
						})
						this.$set(this, "filteredTypes", types);
					}
				}
			},
			changeImage(event) {
				let files = event.target.files || event.dataTransfer.files;
				if (!files.length) return;

				const maxKo = this.Const.views.vignetteMaxSize; // 100 Ko
				const maxAllowedSize = maxKo * 1024;

				if (files[0].size > maxAllowedSize) {
					event.target.value = ''
					alert(`${this.messages.get('dsi', 'view_form_image_max_size')} (${maxKo}Ko maximum)`);

					return;
				}

				this.createImage(files[0]);
        	},
			createImage(file) {
				let image = new Image();
				let reader = new FileReader();

				reader.onload = (e) => {
					image = e.target.result
					this.$set(this.view.settings, "image", image);
					this.$forceUpdate();
				};
				reader.readAsDataURL(file);
			},
			openViewConfiguration: function() {
				this.$refs.modal.show();
			}
		}
	}
</script>