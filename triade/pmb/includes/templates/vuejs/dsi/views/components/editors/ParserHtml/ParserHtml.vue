<template>
    <form>
        <div class="dsi-form-group">
			<label class="etiquette" for="viewEntityTypeList">{{ messages.get('dsi', 'view_form_entity_type') }}</label>
			<div class="dsi-form-group-content">
				<select id="viewEntityTypeList" name="viewEntityTypeList" v-model="item.settings.entityType" v-if="availableEntities.length > 1">
					<option value="0" disabled>{{ messages.get('dsi', 'view_form_default_entity_type') }}</option>
					<option v-for="(entityType, index) in availableEntities"
                        :value="entityType.value"
						:key="index">
							{{ entityType.label }}
					</option>
				</select>
				<p v-else-if="availableEntities.length == 1">
					{{ availableEntities[0].label }}
				</p>
				<p v-else>
					{{ messages.get('dsi', 'view_no_compatible_entity_type') }}
				</p>
			</div>
		</div>

		<div class="dsi-form-group">
			<label class="etiquette" for="template">
                {{ messages.get('dsi', 'parser_html_template') }}
            </label>
			<div class="dsi-form-group-content">
				<select id="template" name="template" v-model="item.settings.template">
					<option value="0">{{ messages.get('dsi', 'parser_html_default_template') }}</option>
					<option
						v-for="(template, index) in formData.templates"
						:value="template.value"
						:key="index">
							{{ template.label }}
					</option>
				</select>
			</div>
		</div>
	</form>
</template>

<script>
export default {
    props: {
		idDiffusion: {
			type: Number,
			default: () => { return 0 }
		},
		entities: {
			type: Object,
			default: () => { return {} }
		},
		item: {
			type: Object,
			default: () => {
				return {
					id: 0,
					idView: 0,
					model: true,
					name: "",
					tags: [],
					type: 7,
					settings: {
						entityType: 0,
						template: 0
					}
				}
			}
		}
	},
	data: function () {
        return {
            template: 0,
            formData: {
				templates: [],
				availableTypes: [],
				availableItems: [],
			}
        }
    },
	computed: {
		availableEntities: function() {
			let availableEntities = [];
			for (const entityType of this.formData.availableItems) {
				if (this.entities[entityType]) {
					availableEntities.push({
						value: entityType,
						label: this.entities[entityType]
					});
				}
			}

			if (availableEntities.length == 0) {
				this.$set(this.item.settings, "entityType", 0);
			}

			if (availableEntities.length == 1) {
				this.$set(this.item.settings, "entityType", availableEntities[0].value);
			}

			return availableEntities;
		}
	},
    created : async function () {

		if (!this.item.settings) {
            this.$set(this.item, "settings", {
				entityType: 0,
				template: 0
			});
		}

        if (this.item.settings && !this.item.settings.template) {
            this.$set(this.item.settings, "template", 0);
        }

		if (this.item.settings && !this.item.settings.entityType) {
            this.$set(this.item.settings, "entityType", 0);
        }
    },
	mounted: async function () {
		let response = await this.ws.get("views", `form/data/${this.item.type}/${this.item.id}`);
		if (response.error) {
			this.notif.error(response.messages);
		} else {
			this.$set(this, "formData", response);
		}
	}
}
</script>