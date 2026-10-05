<template>
	<div class="container">
		<div v-if="field.options[0].multiple[0].value == 'yes'" class="row">
			<input @click="$parent.addCustomMultipleField(field.name, values.length - 1, $event)" class="bouton" type="button" value="+"/>
		</div>
		<div v-for="(customValue, index) in values" class="row">
			<input :id="field.name + index" v-model="customValue.displayLabel" class="saisie-50emr" type="text" :completion="field.authInfos.completion" :att_id_filter:="field.attIdFilter" :param1="field.param1" :persofield="field.name" :autfield="field.name + 'Id' + index"/>
			<input :id="field.name + 'Id' + index" type="hidden" @change="$parent.changeCustomMultipleField(field.name, index, $event)"/>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
			<input v-if="field.options[0].multiple[0].value == 'yes' && index == values.length - 1" @click="$parent.addCustomMultipleField(field.name, index, $event)" class="bouton" type="button" value="+"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>