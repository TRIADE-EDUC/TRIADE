<template>
	<div class="container">
		<template v-if="field.options[0].autorite[0].value == 'yes'">
			<div v-if="field.options[0].multiple[0].value == 'yes'" class="row">
				<input @click="$parent.addCustomMultipleField(field.name, values.length - 1, $event)" class="bouton" type="button" value="+"/>
			</div>
			<div v-for="(customValue, customIndex) in values" class="row">
				<input :id="field.name + customIndex" v-model="customValue.displayLabel" class="saisie-50emr" type="text" :completion="'perso_' + customprefixe" :persofield="field.name" :autfield="field.name + 'Id' + customIndex"/>
				<input :id="field.name + 'Id' + customIndex" type="hidden" @change="$parent.changeCustomMultipleField(field.name, customIndex, $event)"/>
				<input @click="$parent.deleteCustomMultipleField(customIndex)" class="bouton" type="button" value="X"/>
				<input v-if="field.options[0].multiple[0].value == 'yes' && customIndex == values.length - 1" @click="$parent.addCustomMultipleField(field.name, customIndex, $event)" class="bouton" type="button" value="+"/>
			</div>
		</template>
		
		<template v-else-if="field.options[0].checkbox[0].value == 'yes'">
			<template v-if="field.options[0].unselectItem[0].value">
				<input :id="index + '_' + field.name + 'Default'" v-model="values[0].value" :type="field.options[0].typeList" :value="field.options[0].unselectItem[0].id"/>
				<label :for="index + '_' + field.name + 'Default'" >{{ field.options[0].unselectItem[0].value }}</label>
			</template>
			<template v-for="(label, customIndex) in field.listValues">
				<br v-if="maxNbOnLine(customIndex)"/>
				<input :id="index + '_' + field.name + 'Id' + customIndex" v-model="values[0].value" :type="field.options[0].typeList" :value="customIndex"/>
				<label :for="index + '_' + field.name + 'Id' + customIndex">{{ label }}</label>
			</template>
		</template>
		
		<template v-else>
			<select :id="field.name" v-model="values[0].value" :multiple="field.options[0].multiple[0].value == 'yes'">
				<option v-if="field.options[0].unselectItem[0].value" :value="field.options[0].unselectItem[0].id">{{ field.options[0].unselectItem[0].value }}</option>
				<option v-for="(label, customIndex) in field.listValues" :value="customIndex">{{ label }}</option>
			</select>
		</template>
	</div>
</template>

<script>
	export default {
		props : ["field", "values", "customprefixe", "index"],
		methods: {
		    maxNbOnLine: function (index) {
				var nbOnLine = (this.field.options[0].unselectItem[0].value) ? parseInt(index) : parseInt(index) - 1;
				if (this.field.options[0].checkboxNbOnLine[0].value == nbOnLine) {
					return true;
				} 
				return false;
			}
		}
	}
</script>