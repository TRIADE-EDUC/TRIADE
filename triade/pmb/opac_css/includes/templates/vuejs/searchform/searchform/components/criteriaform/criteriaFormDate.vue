<template>
	<div class="rmc_criteria_form_date">
		<label :for="'op_' + index + '_' + criteria.FIELD_ID " class="visually-hidden">{{ pmb.getMessage('searchform', 'operatorDateLabel') }}</label>
		<operators v-if="criteria.QUERIES" 
			:fieldId="criteria.FIELD_ID" 
			:index="index" 
			:queries="criteria.QUERIES" 
			:selected="selectedOp" 
			@changeOp=" e => changeOperator(e)"></operators>
		<label :for="'rmc_label_date_' + index" class="visually-hidden">{{ pmb.getMessage('searchform', 'dateLabel') }}</label>
		<input v-for="(value, key) in searchValuesDate" 
			:id="'rmc_label_date_' + index"
			:key="key" 
			class="rmc_search_date" 
			type="date" 
			:name="nodeId(key)" 
			:value="value">
		<fieldvars v-if="showfieldvars" :fields="criteria.VAR" :fieldId="criteria.FIELD_ID" :index="index"></fieldvars>
	</div>
</template>

<script>
import operators from "./operators.vue";
import fieldvars from "./fieldvars.vue";

export default {
	name: "criteriaFormDate",
	props : ['criteria', 'searchData', 'index', 'showfieldvars'],
	components : {
	    operators,
	    fieldvars,
	},
	data : function(){
		return {
			searchValuesDate : []
		}
	},
	created : function(){
       	if(this.searchData[this.index] && this.searchData[this.index].FIELD){
       		this.searchValuesDate.push(this.searchData[this.index].FIELD[0]);
       	}
       	if(this.searchData[this.index] && this.searchData[this.index].FIELD1){
       		this.searchValuesDate.push(this.searchData[this.index].FIELD1[0]);
       	}
       	if(!this.searchValuesDate.length){
       		this.searchValuesDate = this.criteria.VALUES;         		
       	}
	},
	computed: {
		nodeId: function(id) {
			return (id) => `field_${this.index}_${this.criteria.FIELD_ID}${id == 0 ? "" : "_1"}[]`;
		},
        selectedOp: function() {
        	if(this.searchData[this.index] && this.searchData[this.index].OP){
	            for (var i = 0; i < this.criteria.QUERIES.length; i++) {
	                var operator = this.criteria.QUERIES[i];
	                if (this.searchData[this.index].OP == operator['OPERATOR']) {
		        		return this.searchData[this.index].OP;
	                }
	            }
        	}
            return this.criteria.OPERATOR[0];
        },
	},
	methods : {
		changeOperator: function(data) {
			if(data[1] == this.index){
				var selected = data[0];
				if(selected == "BETWEEN" && this.searchValuesDate.length == 1){
					this.searchValuesDate.push("");
				}else if(selected != "BETWEEN" && this.searchValuesDate.length == 2) {
					this.searchValuesDate.pop();
				}
			}
		},
	}
}
</script>