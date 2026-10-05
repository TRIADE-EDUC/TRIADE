<template>
    <form v-if="entities && formData.messages">

        <div class="dsi-form-group">
			<label class="etiquette" for="rss_name">
                {{ formData.messages.rss_name }}
            </label>
			<div class="dsi-form-group-content">
                <input type="text" id="rss_name" name="rss_name" class="saisie-50em" v-model="item.settings.rssName" required />
			</div>
		</div>

        <div class="dsi-form-group">
			<label class="etiquette" for="rss_description">
                {{ formData.messages.rss_description }}
            </label>
			<div class="dsi-form-group-content">
                <input type="text" id="rss_description" name="rss_description" class="saisie-50em" v-model="item.settings.rssDescription" required />
			</div>
		</div>

		<div class="row">
			<hr>
		</div>

		<div class="dsi-form-group">
			<label class="etiquette" for="viewEntityTypeList">{{ messages.get('dsi', 'view_form_entity_type') }}</label>
			<div class="dsi-form-group-content">
				<select id="viewEntityTypeList" name="viewEntityTypeList" class="saisie-50em" @focus="currentType = item.settings.entityType" v-model="item.settings.entityType">
					<option value="0" disabled>{{ messages.get('dsi', 'view_form_default_entity_type') }}</option>
					<option v-for="(entityType, index) in availableEntities"
                        :value="entityType.value"
						:key="index" :disabled="entityType.disabled">
							{{ entityType.label }}
					</option>
				</select>
			</div>
		</div>

        <div class="dsi-form-group" v-if="templates['tplTitle']">
			<label class="etiquette" for="rss_entity_name">
                {{ formData.messages.rss_entity_name }}
            </label>
			<div class="dsi-form-group-content">
				<select id="rss_entity_name" name="rss_entity_name" class="saisie-50em" v-model="item.settings.rssEntityName">
					<option
						v-for="(label, value) in templates['tplTitle']"
						:value="value"
						:key="value">{{ label }}
					</option>
				</select>
			</div>
		</div>
        <div class="dsi-form-group" v-if="templates['tplDescription']">
			<label class="etiquette" for="rss_entity_description">
                {{ formData.messages.rss_entity_description }}
            </label>
			<div class="dsi-form-group-content">
				<select id="rss_entity_description" name="rss_entity_description" class="saisie-50em" v-model="item.settings.rssEntityDescription">
					<option
						v-for="(label, value) in templates['tplDescription']"
						:value="value"
						:key="value">{{ label }}
					</option>
				</select>
			</div>
		</div>
        <div class="dsi-form-group" v-if="templates['tplLink']">
			<label class="etiquette" for="rss_entity_link">
                {{ formData.messages.rss_entity_link }}
            </label>
			<div class="dsi-form-group-content">
				<select id="rss_entity_link" name="rss_entity_link" class="saisie-50em" v-model="item.settings.rssEntityLink">
					<option
						v-for="(label, value) in templates['tplLink']"
						:value="value"
						:key="value">{{ label }}
					</option>
				</select>
			</div>
		</div>

		<template v-if="isEditorialContent">
			<div class="row">
				<hr>
			</div>
			<EditorialContentLink v-bind="item.settings.rssAdditionalData" @update="updateAdditionalData"></EditorialContentLink>
		</template>
	</form>
</template>

<script>
import EditorialContentLink from "../../../../components/EditorialContentLink.vue";

export default {
    name : "rss",
    props : ["item", "entities"],
    data: function () {
        return {
            formData: {
				messages: null,
				availableTypes: [],
				availableItems: [],
			}
        }
    },
	components: {
		EditorialContentLink
	},
	computed: {
        isEditorialContent: function() {
			const entityType = parseInt(this.item.settings.entityType);
			return [13, 14].includes(entityType);
        },
        templates: function() {
			if (
				this.item.settings.entityType &&
				this.formData.templates[this.item.settings.entityType]
			) {
				return this.formData.templates[this.item.settings.entityType];
			}
			return [];
        },
		availableEntities: function() {
			let availableEntities = [];
			for (const entityType in this.entities) {
                const find = this.formData.availableItems.find(availableItem => availableItem == entityType);
                availableEntities.push({
                    value: entityType,
                    disabled: find != undefined ? false : true,
                    label: this.entities[entityType]
                });
			}

			return availableEntities;
		}
	},
	watch: {
		"item.settings.entityType": function (newValue, oldValue) {
			this.$set(this.item.settings, 'rssAdditionalData', {});
		}
	},
    created : async function () {

        if (!this.item.settings.rssName) {
            this.$set(this.item.settings, "rssName", "");
        }

        if (!this.item.settings.rssDescription) {
            this.$set(this.item.settings, "rssDescription", "");
        }

        if (typeof this.item.settings.rssEntityName === "undefined") {
            this.$set(this.item.settings, "rssEntityName", "");
        }

        if (typeof this.item.settings.rssEntityDescription === "undefined") {
            this.$set(this.item.settings, "rssEntityDescription", "");
        }

        if (typeof this.item.settings.rssEntityLink === "undefined") {
            this.$set(this.item.settings, "rssEntityLink", "");
        }

        if (!this.item.settings.rssAdditionalData) {
            this.$set(this.item.settings, 'rssAdditionalData', {});
        }

		if (!this.item.settings.entityType) {
            this.$set(this.item.settings, "entityType", 0);
        }

        this.getAdditionalData();
    },
    methods : {
        getAdditionalData: async function() {
            let response = await this.ws.get("views", `form/data/${this.item.type}/${this.item.id}`);
            if (response.error) {
                this.notif.error(response.messages);
            } else {
                this.$set(this, "formData", response);
            }
        },
		updateAdditionalData: function (additionalData) {
            this.$set(this.item.settings, 'rssAdditionalData', additionalData || {});
		}
    }
}
</script>