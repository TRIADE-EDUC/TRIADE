<template>
	<div class="container">
		<div v-for="(field, customIndex) in customfields">
			<template v-if="field.customField.opacShow == 1">
				<div :id="'move_' + field.customField.name" movable='yes' :title="field.customField.titre">
					<div class="row">
						<label :for="field.customField.name">
							{{ field.customField.titre }} <sup v-if="field.customField.mandatory == '1'">*</sup>
						</label>
					</div>
					<div v-if="field.customField.comment" class="row">
						{{ field.customField.commentDisplay }}
					</div>
					<customfield :field="field.customField" :values="field.customValues" :customprefixe="customprefixe" :img="img" :pmb="pmb" :index="index"></customfield>
				</div>
			</template>
		</div>
	</div>
</template>

<script>
	import customfield from "./customField.vue";

	export default {
		props : ["customfields", "customprefixe", "img", "pmb", "index"],
		
		components : {
			customfield
		}
	}
</script>