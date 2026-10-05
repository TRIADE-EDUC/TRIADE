<template>
	<div class="container">
		<div v-if="field.options[0].repeatable[0].value" class="row">
			<input @click="$parent.addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
		<div v-for="(customValue, index) in values" class="row">
			<input :id="field.name + index" :size="field.options[0].size[0].value" type="text" v-model="customValue.value"/>
			<select :id="field.name + '_select' + index" v-model="customValue.selectValue">
				<template v-if="field.options[0].resolve">
					<option v-for="(element, index) in field.options[0].resolve" :value="element.id">{{ element.label }}</option>
				</template>
			</select>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
			<input v-if="field.options[0].repeatable[0].value && index == values.length - 1" @click="addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"],
		
		methods : {
			addCustomMultipleField : function() {
				let selectValue = '';
				if (typeof this.field.options[0].resolve !== 'undefined') {
					selectValue = this.field.options[0].resolve[0].id;
				}
				this.values.push({
					value : '',
					selectValue : selectValue
				});
			}
		}
	}
</script>