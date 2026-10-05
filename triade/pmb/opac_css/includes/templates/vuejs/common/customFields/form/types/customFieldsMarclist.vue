<template>
	<div class="container">
		<div v-if="field.options[0].repeatable[0].value" class="row">
			<input @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
		
		<template v-if="field.options[0].autorite[0].value == 'yes'">
			<div v-if="field.options[0].multiple[0].value == 'yes'" class="row">
				<input @click="$parent.addCustomMultipleField(field.name, values.length - 1, $event)" class="bouton" type="button" value="+"/>
			</div>
			<div v-for="(customValue, index) in values" class="row">
				<input :id="field.name + index" v-model="customValue.displayLabel" class="saisie-50emr" type="text" :completion="field.options[0].dataType[0].value" :persofield="field.name" :autfield="field.name + 'Id' + index"/>
				<input :id="field.name + 'Id' + index" type="hidden" @change="$parent.changeCustomMultipleField(field.name, index, $event)"/>
				<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
				<input v-if="field.options[0].multiple[0].value == 'yes' && index == values.length - 1" @click="$parent.addCustomMultipleField(field.name, index, $event)" class="bouton" type="button" value="+"/>
			</div>
		</template>
		
		<template v-else v-for="(customValue, index) in values" class="row">
			<select :id="field.name + index" v-model="customValue.value" name="marclist" :multiple="field.options[0].multiple[0].value == 'yes'">
				<option v-for="(marclistLib, marclistValue) in field.listValues.table"  :value="marclistValue">{{ marclistLib }}</option>
			</select>
			<input v-if="field.options[0].repeatable[0].value && index == values.length - 1" @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</template>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"]
	}
</script>