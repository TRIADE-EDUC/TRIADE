<template>
	<div class="container">
		<div v-if="field.options[0].repeatable[0].value == '1'" class="row">
			<input @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
		<div v-for="(customValue, index) in values" class="row">
			<input :id="field.name + index" v-model="customValue.value" type="date"/>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
			<input v-if="field.options[0].repeatable[0].value == '1' && index == values.length - 1" @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>