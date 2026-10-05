<template>
	<div class="rmc_search_sub_fields">
		<div v-for="(field, key) in fields" :key="key">
			<template v-if="field.TYPE == 'input'">
				<fieldvarsFormQueryList v-if="field.INPUT_TYPE == 'query_list'" :id="computedId(field.NAME, key)" :name="computedName(field.NAME)" :field="field" :value="getFieldvarValue(field.NAME)"></fieldvarsFormQueryList>
<!-- 				<label v-if="field.COMMENT" :for="computedId(field.NAME, key)">{{ field.COMMENT }}</label> -->
<!-- 				<input :id="computedId(field.NAME, key)" v-if="field.INPUT_TYPE == 'checkbox'" type="checkbox" :name="computedName(field.NAME)" :value="field.VALUE"> -->
<!-- 				<input :id="computedId(field.NAME, key)" v-if="field.INPUT_TYPE == 'number'" type="number" :name="computedName(field.NAME)" :value="field.VALUE"> -->
<!-- 				<input :id="computedId(field.NAME, key)" v-if="field.INPUT_TYPE == 'hidden'" type="hidden" :name="computedName(field.NAME)" :value="field.VALUE"> -->
			</template>
		</div>
	</div>
</template>

<script>
import fieldvarsFormQueryList from "./fieldvarsform/fieldvarsFormQueryList.vue";
export default {
	name: "fieldvars",
	props : ['index', 'fieldId', 'fields', 'searchData'],
	components : {
		fieldvarsFormQueryList,
	},
	methods: {
	    computedName: function(name) {
	        return `fieldvar_${this.index}_${this.fieldId}[${name}][]`;
	    },
	    computedId: function(name, index) {
	        return `fieldvar_${this.index}_${this.fieldId}_${name}_${index}`;
	    },
		getFieldvarValue: function(name) {
			if (this.searchData[this.index] && this.searchData[this.index].FIELDVAR[name]) {
				return this.searchData[this.index].FIELDVAR[name];
			}
			return null;
		}
	}
}
</script>