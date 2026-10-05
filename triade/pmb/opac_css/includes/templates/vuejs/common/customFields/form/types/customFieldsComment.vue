<template>
	<div class="container">
		<div v-if="field.options[0].repeatable[0].value === '1'" class="row">
			<input @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
		<div v-for="(customValue, index) in values" class="row">
			<textarea v-model="customValue.value" :type="field.options[0].datatype" :rows="field.options[0].rows[0].value" :cols="field.options[0].cols[0].value" :maxlength="field.options[0].maxsize[0].value" wrap=virtual></textarea>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>