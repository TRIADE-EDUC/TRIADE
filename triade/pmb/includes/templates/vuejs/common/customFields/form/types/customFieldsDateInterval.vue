<template>
	<div class="container">
		<div v-if="field.options[0].repeatable[0].value == '1'" class="row">
			<input @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
		<div v-for="(customValue, index) in values" class="row">
			<label>{{ pmb.getMessage('date', 'date_begin') }}</label>
			<input v-model="customValue.value" class="saisie-15em" type="date"/>
			<input v-model="customValue.valueTime" class="saisie-15em" type="time"/>
			
			<label>{{ pmb.getMessage('date', 'date_end') }}</label>
			<input v-model="customValue.value1" class="saisie-15em" type="date"/>
			<input v-model="customValue.value1Time" class="saisie-15em" type="time"/>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
			<input v-if="field.options[0].repeatable[0].value == '1' && index == values.length - 1" @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values", "pmb"]
	}
</script>