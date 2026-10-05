<template>
    <form v-if="entities">
		<div class="dsi-form-group">
			<label class="etiquette" for="viewEntityTypeList">{{ messages.get('dsi', 'view_form_entity_type') }}</label>
			<div class="dsi-form-group-content">
				<select id="viewEntityTypeList" name="viewEntityTypeList" @focus="currentType = item.settings.entityType" v-model="item.settings.entityType">
					<option value="0" disabled>{{ messages.get('dsi', 'view_form_default_entity_type') }}</option>
					<option v-for="(entityType, index) in availableEntities"
                        :value="entityType.value"
						:key="index" :disabled="entityType.disabled">
							{{ entityType.label }}
					</option>
				</select>
			</div>
		</div>
		<div class="dsi-form-group" v-show="item.settings.entityType">
			<label class="etiquette" for="viewTemplateDirectory">{{ messages.get('dsi', 'view_form_template_directory') }}</label>
			<div class="dsi-form-group-content">
				<select id="viewTemplateDirectory" name="viewTemplateDirectory" v-model="item.settings.templateDirectory">
					<option value="0" disabled>{{ messages.get('dsi', 'view_form_default_template_directory') }}</option>
					<option v-for="(dir, index) in templateDirectories" :value="dir" :key="index">{{dir}}</option>
				</select>
			</div>
		</div>
	</form>
</template>

<script>
export default {
    name : "simple",
    props : ["item", "entities"],
    data: function () {
        return {
            templates : "",
            templateDirectories : [],
            formData: {
				availableTypes: [],
				availableItems: [],
			}
        }
    },
	computed: {
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
        if(this.item.settings && ! this.item.settings.entityType) {
            this.$set(this.item.settings, "entityType", 0);
        }
        if(this.item.settings && ! this.item.settings.templateDirectory) {
            this.$set(this.item.settings, "templateDirectory", 0);
        }
        this.getAdditionalData();
        this.getTemplates();
        this.getTemplateDirectories();
    },
    watch : {
        "item.settings.entityType" : function() {
            //On met a jour le dossier de templates selon l'entite
            this.getTemplateDirectories();
        }
    },
    methods : {
        getTemplateDirectories : async function() {
            this.templateDirectories = await this.ws.get("views", `getTemplateDirectories/${this.item.type}/${this.item.settings.entityType}`);
            //On reset le champ si on ne trouve plus l'element dans le nouveau tableau
            if(! this.templateDirectories.includes(this.item.settings.templateDirectory)) {
                this.item.settings.templateDirectory = 0;
            }
        },
        getTemplates : async function() {
            if(! this.templates) {
                this.templates = await this.ws.get("views", 'getEntitiesDefaultTemplates');
            }
        },
        getAdditionalData: async function() {
            let response = await this.ws.get("views", `form/data/${this.item.type}/${this.item.id}`);
            if (response.error) {
                this.notif.error(response.messages);
            } else {
                this.$set(this, "formData", response);
            }
        }
    }
}
</script>