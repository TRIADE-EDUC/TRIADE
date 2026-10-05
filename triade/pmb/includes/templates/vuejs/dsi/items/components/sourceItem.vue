<template>
	<div id="dsi-form-source-item">
		<div class="dsi-form-group">
			<label class="etiquette" for="itemTypeList">{{ messages.get('dsi', 'source_form_type') }}</label>
			<div class="dsi-form-group-content">
				<select id="itemTypeList" name="itemTypeList" v-model="item.type" @change="reset" :disabled="importType" required>
					<option value="" disabled>{{ messages.get('dsi', 'source_form_default_type') }}</option>
					<option v-if="availableTypes.includes(parseInt(type.id))" v-for="(type, index) in sortedTypes" :key="index" :value="type.id" :disabled="!availableTypes.includes(parseInt(type.id))">
						{{ type.label }}
					</option>
				</select>
			</div>
		</div>
		<ItemSource v-if="item.type != ''" :type="item.type" :settings="item.settings" @updateSettings="updateSettings" :key="item.type"></ItemSource>
	</div>
</template>

<script>
	import ItemSource from "./source/source.vue";
	export default {
		props: ["item", "types", "viewType", "isChild", "importType", "modalItem"],
		components: {
            ItemSource
		},
        data: function () {
            return {
                filteredTypes : [],
                availableTypes: []
            }
        },
		mounted: function() {
			this.availableTypes = this.Const.items.availableItemSources;
            if(this.item.type == 0) {
                this.item.type = "";
            }
			
			if(typeof this.importType !== "undefined") {
				this.$set(this.item, "type", this.importType);
			}

			if(this.item.settings == "") {
				this.$set(this.item, "settings", {})
			}
		},
        watch : {
            "viewType": async function() {
                await this.getFilteredTypes();
            },
			"item": function() {
				if(typeof this.importType !== "undefined") {
					this.$set(this.item, "type", this.importType);
				}
			}
        },
		computed: {
			sortedTypes: function() {
				// Creer le tableau
				let types = Object.entries(this.types);

				// Trier le tableau
				types.sort((a, b) => a[1].localeCompare(b[1]));

				let sortedTypes = {};
				for (let i = 0; i < types.length; i++) {
					const key = types[i][0];
					const value = types[i][1];

					sortedTypes[i] = {id: key, label: value};
				}

				return sortedTypes;
			}
		},
		methods: {
			updateSettings: function(settings) {
				this.$set(this.item, "settings", settings);
			},
			reset: function() {
				this.$set(this.item, "settings", {});
				if(this.item.model || this.isChild || this.modalItem) {
					return;
				}
				if(confirm(this.messages.get('dsi', "item_update_view_type"))) {
					this.$root.$emit("updateItemType", this.item.type);
				}
			},
            getFilteredTypes : async function() {
                if(! this.itemType) {
                    this.$set(this, "filteredTypes", this.types);
                    return;
                }
                this.$set(this, "filteredTypes", []);
                let compatibility = await this.ws.get('views', 'getCompatibility/'+ this.viewType);
                for(let i in compatibility) {
                    if(this.types.find(t => t.id == compatibility[i]) !== undefined) {
                        this.$set(this.filteredTypes, this.filteredTypes.length, this.types.find(t => t.id == compatibility[i]));
                    }
                }
            }
		}
	}
</script>