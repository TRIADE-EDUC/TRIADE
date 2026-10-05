<template>
	<div class="container">
		<div v-if="field.options[0].repeatable[0].value === '1'" class="row">
			<input @click="addCustomMultipleField(field.name, values.length - 1, $event)" class="bouton" type="button" value="+"/>
		</div>
		<div v-for="(customValue, index) in values" class="row">
			<input :id="field.name + index" v-model="customValue.value" type="text" :size="field.options[0].size[0].value" :maxlength="field.options[0].maxsize[0].value"/>
			<select v-if="field.listValues" v-model="customValue.qualifiedValue">
				<option v-for="(label, index) in field.listValues" :value="index">{{ label }}</option>
			</select>
			{{ field.msg.langSelect }}
			<input :id="field.name + 'Lang' + index" v-model="customValue.displayLang" class="saisie-10emr" type="text" completion="lang" :autfield="field.name + 'Code' + index"/>
			<input :id="field.name + 'Code' + index" type="hidden" @change="changeCustomMultipleField(field.name, index, $event)"/>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
			<input v-if="field.options[0].repeatable[0].value == '1' && index == values.length - 1" @click="addCustomMultipleField(field.name, index, $event)" class="bouton" type="button" value="+"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"],
		
		methods : {
			addCustomMultipleField : function(fieldName, index, event) {
				this.values.push({
					displayLang : this.field.options[0].defaultLang[0].displayLabel,
					lang : this.field.options[0].defaultLang[0].value,
					value : ''
				});
				
				if (typeof fieldName !== 'undefined' && fieldName) {
					index = parseInt(index, 10) + 1;
					this.$nextTick(() => {
						let elt = document.getElementById(fieldName + 'Lang' + index);
						ajax_pack_element_without_spans(elt, event);
					});
				}
				document.activeElement.blur();
			},
			
			changeCustomMultipleField : function(fieldName, index, event) {
				let label = document.getElementById(fieldName + 'Lang' + index).value;
				let id = event.target.value;
				
				if (typeof this.values[index] !== 'undefined' || this.values[index] == 0) {
					this.values[index].lang = id;
					this.values[index].displayLang = label;
				} else {
					this.values.push({ displayLang : label, lang : id, value : '' });
				}
			}
		}
	}
</script>