<template>
	<div class="container">
		<div v-for="(field, customIndex) in customfields" :key="customIndex">
			<div :id="'move_' + field.customField.name" movable='yes' :title="field.customField.titre">
				<div class="row" v-if="! disableTitle">
					<label :for="field.customField.name">
						{{ field.customField.titre }} <sup v-if="field.customField.mandatory == '1'">*</sup>
					</label>
				</div>
				<div v-if="field.customField.comment" class="row">
					{{ field.customField.commentDisplay }}
				</div>
				<customfield :field="field.customField" :values="field.customValues" :customprefixe="customprefixe" :img="img" :pmb="pmb" :index="index" :csrftokens="csrftokens"></customfield>
			</div>
		</div>
	</div>
</template>

<script>
	import customfield from "./customField.vue";

	export default {
		props : ["customfields", "customprefixe", "img", "pmb", "index", "disableTitle", "csrftokens"],
		
		components : {
			customfield
		}
	}
</script>