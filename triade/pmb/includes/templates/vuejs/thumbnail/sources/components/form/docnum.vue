<template>
	<div>
		<h2>{{ data.messages.name }}</h2>
		<p>{{ data.messages.description }}</p>
		<form class="form-admin" action="" method="POST" @submit.prevent="submit">
			<h3>{{ data.messages.form_parameters }}</h3>
			<div class="form-contenu">
				<div class="row">
					<div class="colonne3">
						<label class="etiquette" for="active_found_first_thumbnail">{{ data.messages.active_found_first_thumbnail }}</label>
					</div>
					<div>
						<input class="switch" type="checkbox" id="active_found_first_thumbnail" name="active_found_first_thumbnail" v-model="activeFoundFirstThumbnail" />
						<label for="active_found_first_thumbnail">&nbsp;</label>
					</div>
				</div>
				<div class="row">
					<div class="colonne3">
						<label class="etiquette" for="disable_custom_field_selection">{{ data.messages.activate_custom_field_selection }}</label>
					</div>
					<div>
						<input class="switch" type="checkbox" id="disable_custom_field_selection" name="disable_custom_field_selection"  v-model="disableCustomFieldSelection" />
						<label for="disable_custom_field_selection">&nbsp;</label>
					</div>
				</div>
				<div class="row">
					<div class="colonne3">
						<label class="etiquette" for="custom_field_select">{{ data.messages.custom_field_select }}</label>					
					</div>
					<div class="colonne_suite">
						<select class="saisie-25em" id="custom_field_select" name="custom_field_select" v-model="customField">
							<option value="">{{ data.messages.custom_field_select_default_value }}</option>
							<option v-for="(field, index) in data.parameters.docnum_custom_fields" :key="index" :value="field.customField.name">{{field.customField.titre}}</option>
						</select>
					</div>
				</div>
				<div class="row" v-if="customField != ''">
					<div class="colonne3">
						<label class="etiquette" for="custom_field_display">{{ data.messages.custom_field_display }}</label>
					</div>
					<div class="colonne_suite">
						<custom-fields class="saisie-50em" id="custom_field_display" :customfields="selectedCustomField" customprefixe="explnum" index="0" :disableTitle="true"></custom-fields>
					</div>
				</div>
				<div class="row">
					<button class="bouton btnCancel" type="button" @click="cancel">
						{{ messages.get("common", "cancel") }}
					</button>
					<button class="bouton" type="submit">
						{{ messages.get("common", "submit") }}
					</button>
				</div>
			</div>
		</form>
	</div>
</template>

<script>
	import customFields from "../../../../common/customFields/form/customFields.vue";
	export default {
		props : ["data"],
		data: function () {
			return {
			    activeFoundFirstThumbnail : 1,
			    disableCustomFieldSelection : 1,
				customField : ""
			}
		},
		components: {
			customFields
		},
		created : function() {
			this.activeFoundFirstThumbnail = this.helper.cloneObject(this.data.parameters.active_found_first_thumbnail);
			this.disableCustomFieldSelection = this.helper.cloneObject(this.data.parameters.disable_custom_field_selection);
			this.customField = this.helper.cloneObject(this.data.parameters.custom_field);
		},
		methods: {
		    cancel: function () {
		        this.$emit('cancel');
		    },
		    submit : async function() {
		    	let data = {
					"active_found_first_thumbnail" : this.activeFoundFirstThumbnail,
					"disable_custom_field_selection" : this.disableCustomFieldSelection,
					"custom_field" : this.customField,
					"custom_field_value" : this.selectedCustomField[0]
				}
				let response = await this.ws.post(this.data.entityType, "docnum/save", { values: data });
				if (response.error) {
                    if (response.errorMessage) {
                        console.error(response.errorMessage);
                    }
                    this.notif.error(this.messages.get("common", "failed_save"));
                } else {
                    this.notif.info(this.messages.get("common", "success_save"));
                }
		    }
		},
		computed : {
			selectedCustomField : function() {
				if(this.customField == "") {
					return [];
				}
				if(this.data.parameters.custom_field_value && this.data.parameters.custom_field_value.customField.name == this.customField) {
					return [this.data.parameters.custom_field_value];
				}
				return [
					this.data.parameters.docnum_custom_fields.find(cf => cf.customField.name == this.customField)
				];
			}
		}
	}
</script>