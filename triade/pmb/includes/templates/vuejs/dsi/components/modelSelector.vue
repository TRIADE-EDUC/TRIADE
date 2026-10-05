<template>
	<div v-if="list.length > 0" class="model-selector-container">
		<div class="dsi-form-group">
			<label class="etiquette" for="model-selector">{{ messages.get('dsi', 'model_heritage_form') + "&nbsp;" }}</label>
			<div class="dsi-form-group-content">
				<select v-if="entity != 'views'" name="model-selector" v-model="selected" @change.prevent="updateSelectedModel(false)">
					<option disabled value="">{{ messages.get('dsi', 'model_selector_default_value') }}</option>
					<option v-for="(element, index) in compatibleModelList" :key="index" :value="element.id">{{ element.name }}</option>
				</select>
				<span v-else class="model-selector-span">
					<span>{{ selectedItem ? selectedItem.name : messages.get('dsi', 'view_form_change_model_empty') }}</span>
					<button v-if="!selected"
						class="bouton"
						type="button"
						:title="messages.get('dsi', 'view_form_change_model_title')"
						@click.prevent="openViewConfiguration">

						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
				</button>
				</span>
				<button v-if="id && selected && selectedItem && selectedItem.tags.length" type="button" class="dsi-button bouton" @click="importModelTags()">{{ messages.get('dsi', 'tags_import_model_tags') }}</button>
				<span v-if="selected != ''" role="button" type="button" class="model-reset" @click="updateSelectedModel(true)" :title="messages.get('dsi', 'model_selector_reset')">
					<i class="fa fa-times" aria-hidden="true" style="cursor: pointer"></i>
				</span>
				<span v-if="isModified && showLock" class="model-modified" :title="messages.get('dsi', 'model_is_modified_form')">M</span>
				<span v-if="selected != '' && showLock">
					<span v-if="item && item.settings && item.settings.locked" role="button" type="button" @click="lock(false)" class="model-lock"
						:title="messages.get('dsi', 'model_unlocked')">
						<i class="fa fa-lock" aria-hidden="true"></i>
					</span>
					<span v-else role="button" type="button" @click="lock(true)" class="model-lock"
						:title="messages.get('dsi', 'model_locked')">
						<i class="fa fa-unlock" aria-hidden="true"></i>
					</span>
				</span>
			</div>
		</div>
		<hr v-if="entity != 'views'">
	</div>
</template>

<script>
export default {
	props: ['entity', 'id', 'restrictedFields', 'item', 'compatibility', 'showLock', 'parentId'],
	data: function () {
		return {
			list: [],
			selected: ""
		}
	},
	watch: {
		"item.numModel": function () {
			this.selected = this.item.numModel ? this.item.numModel : "";
		}
	},
	created: function () {
		//this.updateSelectedModel(true)
		this.selected = this.item.numModel ? this.item.numModel : "";
		this.updateSelectedModel(false);

		if(this.$root.models && this.$root.models[this.entity]) {
			this.list = JSON.parse(JSON.stringify(this.$root.models[this.entity]));
		} else {
			this.getList();
		}
		
		this.$root.$on("FilterModelSelectorByCompatibility" + this.entity.charAt(0).toUpperCase() + this.entity.slice(1), this.filterModelByCompatibility);
	},
	computed: {
		compatibleModelList: function () {
			return this.list.filter((model) => {
				return this.compatibility?.includes(model.type) ?? true;
			});
		},
		model: function () {
			return this.list.find((m) => m.id == this.item.numModel);
		},
		fullRestrictedFields: function () {
			if (!this.restrictedFields) {
				return [];
			}
			const commonRestrictedFields = this.Const.modelSelectorExcludeElements;
			return this.restrictedFields.concat(commonRestrictedFields);
		},
		base_url: function () {
			return window.location.origin + '/' + window.location.pathname.split('/')[1];
		},
		isModified: function () {
			if (this.item.settings.locked) {
				return false;
			}
			if (this.model && this.fullRestrictedFields) {
				for (const [key, value] of Object.entries(this.model)) {
					if (key === "settings") {
						for (let settingKey in this.item.settings) {
							if (!this.fullRestrictedFields.includes(settingKey)) {
								if (this.item.settings[settingKey] instanceof Array || typeof this.item.settings[settingKey] === 'object') {
									if (JSON.stringify(this.item.settings[settingKey]) === JSON.stringify(this.model.settings[settingKey])) {
										continue;
									}
								} else if (this.item.settings[settingKey] === this.model.settings[settingKey]) {
									continue;
								}
								return true;
							}
						}
					} else if (!this.fullRestrictedFields.includes(key)) {
						if (this.item[key] instanceof Array) {
							if (JSON.stringify(this.item[key]) === JSON.stringify(value)) {
								continue;
							}
						}
						if (this.item[key] === value) {
							continue;
						}
						return true;
					}
				}
			}
			return false;
		},
		selectedItem : function() {
			return this.list.find((m) => m.id == this.selected);
		}
	},
	methods: {
		getList: async function () {
			let list = await this.ws.get(this.entity, "getModels");
			if (list && !list.error) {
				const index = list.findIndex(model => model.id == this.id);
				if (index != -1) {
					list.splice(index, 1);
				}
			}

			this.list = list;
		},
		updateSelectedModel: function (reset) {
			if (reset) {
				this.$set(this, "selected", "");
				if (this.item.settings) {
					this.$delete(this.item.settings, "locked");
				}
				this.$set(this.item, "numModel", "0");
				this.$emit('updateSelectedModel', "0");
				this.$root.$emit("resetModel");
				return;
			}
			let i = this.list.findIndex((e) => {
				return e.id == this.selected;
			})
			if (i != -1) {
				let model = Object.assign({}, this.list[i]);
				//Modification du model avant l'envoi
				model.tags = [];
				this.$emit('updateSelectedModel', model);
			}
		},
		lock: function (lock) {
			this.$set(this.item.settings, "locked", lock);
			this.$root.$emit("locked", { "lock": lock, "model": this.model });
		},
		filterModelByCompatibility: async function (compatibility) {
			if(!this.$root.models) {
				await this.getList();
			}

			let list = [];
			for (let i in compatibility) {
				list = [...list, ...this.list.filter(model => model.type == compatibility[i])];
			}
			this.list = list;
		},
		importModelTags : function() {
			this.$root.$emit("importModelTags", {
				entityType : this.entity,
				entityId : this.id
			});
		},
		openViewConfiguration: function() {
			this.$root.$emit('openViewConfiguration', { method : 1, id : this.parentId });
		}
	}
}
</script>