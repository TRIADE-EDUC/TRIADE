<template>
	<div id="form">
		<form :id="'form-' + uid" action="#" method="POST" @submit.prevent="submit" class="dsi-form-item">
			<modelSelector entity="items" :idSelected="tempItemModal.numModel" :id="tempItemModal.id" :restricted-fields="restrictedFields" @updateSelectedModel="addModel" ref="modelSelector" :item="tempItemModal" :showLock="true"></modelSelector>
			<lockable :locked="item.settings.locked">
				<div v-if="is_model || tab_index !== undefined" class="dsi-form-group">
					<label class="etiquette" for="item-name">{{ messages.get('dsi', 'items_form_name') }}</label>
					<div class="dsi-form-group-content">
						<input type="text" id="item-name" name="item-name" v-model="tempItemModal.name" required>
					</div>
				</div>

				<div v-if="isOnlyForAggregator || is_model" class="dsi-form-item-agregator">
					<div class="dsi-form-group-content">
						<label class="etiquette" for="item-agregator-true">{{ messages.get('dsi', 'items_form_agregator') }}</label>
						<input type="checkbox" name="item-agregator-true" value="true" v-model="aggregator" :disabled="isOnlyForAggregator && rootItem == ''">
					</div>
				</div>

				<agregatorItem v-show="(isOnlyForAggregator || is_model) && aggregator"
					:item="item"
					:rootItem="rootItem !== undefined ? rootItem : ''"
					:uid="uid"
					:parentUid="parentUid"
					:is_model="is_model">
				</agregatorItem>
				<sourceItem v-if="((is_model || !isOnlyForAggregator) && !aggregator) || (!aggregator && tab_index)"
					:types="types"
					:item="tempItemModal"
					:viewType="viewType"
					:is-child="rootItem"
					:modalItem="modalItem">
				</sourceItem>
				<tags
					v-if="item.id"
					:tags="item.tags"
					entity="items"
					:entity-id="item.id"
					@newTagList="$set(item, 'tags', $event)"></tags>
			</lockable>
			<div class='row dsi-form-action'>
				<div class="left">
					<template v-if="is_model">
						<input type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
						<input name="submit_model" type="submit" class="bouton" :value="btnValue">
					</template>

					<template v-if="!is_model">
                        <input v-if="tab_index > 0" type="button" class="bouton" :value="messages.get('common', 'cancel')" @click="cancel">
						<input name="submit" @click.prevent="submit" type="submit" class="bouton" :value="btnValue">
<!--					<input @click="openModal" type="button" class="bouton" :value="messages.get('dsi', 'submit_model')">-->
					</template>
				</div>
				<div v-if="is_model && item.id" class="right">
					<input @click="del" class="bouton btnDelete" type="button" :value="messages.get('dsi', 'del')"/>
				</div>
			</div>
            <modalModelSelector :showModal="showModal" :entity="item" @close="showModal = false"></modalModelSelector>
        </form>
    </div>
</template>

<script>
	import sourceItem from "./sourceItem.vue";
	import agregatorItem from "./agregatorItem.vue";
	import modelSelector from "@dsi/components/modelSelector.vue";
	import modalModelSelector from "@dsi/components/modalModelSelector.vue";
	import tags from "@dsi/components/tags.vue";
	import lockable from "@dsi/components/lockable.vue";
	export default {
		props : ["types", "item", "is_model", "tab_index", "rootItem", "parentUid", "uid", "action", "viewType", "isOnlyForAggregator", "modalItem"],
		components: {
			sourceItem,
			agregatorItem,
			modelSelector,
			modalModelSelector,
			tags,
			lockable
		},
		data: function () {
			return {
				model: null,
				showModal: false,
                lastItemName: "",
				aggregator: false,
				restrictedFields: ["model", "numModel", "id", "idItem", "itemSource"],
				tempItemModal: {},
			}
		},
		mounted: async function() {
			this.aggregator = this.isAgregatorItem;
			if(this.viewType != "" && this.viewType !== undefined) {
				this.$root.$emit("FilterModelSelectorByCompatibility" + 'Items', await this.getCompatiblityView());
			}
		},
		created: async function() {
			if(this.modalItem) {
				const emptyItem = await this.getEmptyItem();
				this.tempItemModal = emptyItem;
			} else {
				this.tempItemModal = this.item;
			}
		},
        watch: {
            viewType: async function() {
                if(this.viewType !== "") {
					this.item.numModel = 0;
                    let compatibility = await this.getCompatiblityView();
                    this.$root.$emit("FilterModelSelectorByCompatibility" + 'Items', compatibility);
                    for(let i in compatibility) {
                        if(compatibility[i] == 0) {
                            this.item.type = "";
                            this.item.settings = "";
                            return true;
                        }
                    }
                }
            },
			aggregator : function() {
				//On reset les enfants si on n'est plus en agregateur
				if(! this.aggregator && this.tempItemModal) {
					this.$set(this.tempItemModal, "childs", []);
				}

				//On reset le type et les settings si on n'est plus en agregateur
				if(this.aggregator && this.item.type) {
					this.$set(this.item, "type", "");
					this.$set(this.item, "settings", {});
				}
			},
			"isOnlyForAggregator" : function() {
				this.aggregator = this.isOnlyForAggregator;
			},
			"item.settings.locked" : async function() {
				if(! this.item.settings.locked) {
					this.$set(this.item, "childs", this.emptyChilds(this.item.childs));
				} else {
					let list = await this.ws.get("items", "getModels");
					if(! list.error) {
						let model = list.find((m) => m.id == this.item.numModel);
						if(model != undefined) {
							this.addModel(model);
						}
					}
				}
			}
        },
		computed : {
            isCompatibleWithAgregatorItem: function() {
                if(this.is_model) {
                    return true;
                }

                if(this.viewType == 2) {
                    return true;
                }

                return this.tab_index > 0;
            },
            btnValue: function() {
                if(this.$root.action === "edit") {
                    return this.messages.get('common', 'submit')
                }

                if(!this.tab_index) {
                    if(!this.is_model) {
                        return this.messages.get('common', 'submit')
                    }
                    return this.messages.get('dsi', 'submit_model')
                }else {
                    return this.messages.get('dsi', 'add')
                }

            },
			isAgregatorItem: function() {
				return this.tempItemModal.childs && this.tempItemModal.childs.length > 0;
			}
		},
		methods: {
			cancel: function() {
                if(this.tab_index) {
                    this.$root.$emit("closeNewTab", this.uid, this.parentUid);
                    return;
                }
				document.location = "./dsi.php?categ=items";
			},
			checkForm: function() {
				for (const el of document.getElementById('form-' + this.uid).querySelectorAll("[required]")) {
					if (!el.reportValidity()) {
						return false;
					}
				}
				return true;
			},
			submit: async function(e) {
				if(!this.checkForm()) {
					return;
				}

				if(this.modalItem) {
					if(!this.item.name) {
						this.item.name = this.messages.get('dsi', 'view_wysiwyg_associated_empty_item_name');
					}

					this.item.childs.push(this.tempItemModal);

				} else {
					if((this.item.type == "" || this.item.type == 0) && !this.item.childs.length) {
						this.notif.error(this.messages.get('dsi', 'items_form_agregator_children_error'));
						return;
                	}

					let lastItem = 0;
					// On enregistre en tant que modèle
					if(e.submitter && (e.submitter.name === "submit_model" || e.submitter.name === "submit_model_from_modal") || e.name === "submit_model_from_modal") {
						lastItem = this.item;
						if(this.item.id == 0 && this.item.model == false) {
							this.item = this.cloneItem(this.item);
						}
					}
					// On ajoute l'item à son item agrégator sans le sauvegarder
					if(this.tab_index) {
						let nameEvent = "saveOnAgregator";
						nameEvent += ("_" + (this.parentUid !== '' ? this.parentUid: this.uid));
						this.$root.$emit(nameEvent, this.item, this.uid, this.action);

						return;
					}
				}

				// Enregistrement de l'item
				let response = await this.ws.post('items', 'save', this.item);
				// Le webservice renvoi un erreur
				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				// On arrive des modèles on redirige sur la liste des modèles
				} else if (this.is_model){
					document.location = `./dsi.php?categ=items&action=edit&id=${response.id}`;

				// On arrive de la diffusion après l'enregistrement d'un modèle
				} else if (this.item.model && this.item.id === 0) {
					//Reset le formulaire

					this.$set(this, "item", lastItem);
                    this.$set(this.item, "name", this.lastItemName);

					// Ferme la popup
					this.showModal = false;

					// Update le sélecteur de modèles
					await this.$refs.modelSelector.getList();
					this.notif.info(this.messages.get('common', 'success_save'));

				// Sauvegarde de la diffusion
				}else {
					this.item.id = response.id;
					if(! this.item.settings.locked) {
						this.$set(this.item, "childs", response.childs);
					}
                    this.$emit("saveDiffusionItem", response.id);
				}
			},
			del: async function() {
				if (confirm(this.messages.get('dsi', 'confirm_del'))) {
					let response = await this.ws.post("items", 'delete', this.item);
					if (response.error) {
						this.notif.error(this.messages.get('dsi', response.errorMessage));
					} else {
                        document.location = "./dsi.php?categ=items";
                    }
                }
			},
			updateSettings: function(settings) {
				this.$set(this.item, "settings", settings);
			},
			addModel: function(model) {
				if(model != 0) {
					let clone = JSON.parse(JSON.stringify(model));
					if(clone.settings instanceof Array) {
						clone.settings = {};
					}
					clone.settings.locked = this.tempItemModal.settings.locked;
					const ignoreKeys = this.Const.items.itemIgnoreKeys;
                    for(let property in clone) {
						if(property == "childs" && ! this.tempItemModal.settings.locked) {
							clone[property] = this.emptyChilds(clone[property]);
						}
                        if(ignoreKeys.indexOf(property) === -1) {
                            this.$set(this.tempItemModal, property, clone[property]);
                        }
                    }
                    this.$set(this.tempItemModal, "numModel", clone["idItem"]);
                } else {
                    this.$set(this.tempItemModal, "numModel", 0);
                    this.$set(this.tempItemModal, "childs", []);
                    this.$set(this.tempItemModal, "name", "");
					this.$set(this.tempItemModal, "type", "");
					this.$set(this.tempItemModal, "settings", {});
                }

                this.$set(this.tempItemModal, "model", 0);
				this.aggregator = this.isOnlyForAggregator;
            },
			emptyChilds : function(childs) {
				for(let child of childs) {
					child.id = 0;
					child.idItem = 0;
					if(child.childs.length) {
						child.childs = this.emptyChilds(child.childs);
					}
				}
				return childs;
			},
            cloneItem: function(item) {
                let clone = item;

                clone.id = 0;

                let childs = [];
                for (const child of item.childs) {
                    childs.push(this.cloneItem(child));
                }
                clone.childs = childs;

                return clone;
            },
            openModal: function() {
                this.showModal = true;
                this.lastItemName = this.item.name;
            },
            getCompatiblityView: async function() {
				return await this.ws.get('views', 'getCompatibility/'+ this.viewType);
            },
			// Récupère une instance d'item vide
			getEmptyItem: async function() {
				let response = await this.ws.get("items", 'getEmptyInstance');

				if (response.error) {
					this.notif.error(this.messages.get('dsi', response.errorMessage));
				} else {
					return response;
				}
				return null;
			}
		}
	}
</script>