<template>
	<div class="container">
		<div v-for="(field, index) in customfields">
			<template v-if="field.customField.opacShow == '1'">
				<div :id="'move_' + field.customField.name" movable='yes' :title="field.customField.titre">
					<div class="row">
						<label :for="field.customField.name">
							{{ field.customField.titre }} :
						</label>
						<customfield :field="field.customField" :values="field.customValues" :customprefixe="customprefixe" :img="img" :pmb="pmb"></customfield>
					</div>
				</div>
			</template>
		</div>
	</div>
</template>

<script>
	import customfield from "./customField.vue";

	export default {
		props : ["customfields", "customprefixe", "img", "pmb"],
		
		components : {
			customfield
		}
	}
</script>