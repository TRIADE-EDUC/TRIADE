<template>
    <div class="rmc_criteria_form_text">
        <operators 
        	v-if="criteria.QUERIES" 
        	:fieldId="criteria.FIELD_ID" 
        	:index="index" 
        	:queries="criteria.QUERIES" 
        	:selected="selectedOp">
       	</operators>
        <input
        	:value="searchValue" 
       		class="rmc_search_txt" 
       		type="text" 
       		:name="name" 
			@input.stop="$emit('updateValue', $event.target.value)">
		<fieldvars v-if='showfieldvars' :fields="criteria.VAR" :fieldId="criteria.FIELD_ID" :index="index"></fieldvars>
    </div>
</template>

<script>
import operators from "./operators.vue";
import fieldvars from "./fieldvars.vue";

export default {
    name: "criteriaFormText",
    props : ['criteria', 'searchData', 'index', 'showfieldvars'],
    
    components : {
        operators,
        fieldvars,
    },
    computed: {
        name: function() {
            return `field_${this.index}_${this.criteria.FIELD_ID}[]`;
        },
        selectedOp: function() {
        	if ("undefined" !== typeof this.searchData[this.index] && (this.searchData[this.index].SEARCH == this.criteria.FIELD_ID)) {
	        	if(this.searchData[this.index] && this.searchData[this.index].OP){
		            for (var i = 0; i < this.criteria.QUERIES.length; i++) {
		                var operator = this.criteria.QUERIES[i];
		                if (this.searchData[this.index].OP == operator['OPERATOR']) {
			        		return this.searchData[this.index].OP;
		                }
		            }
	        	}
        	}
            return this.criteria.OPERATOR[0];
        },
        searchValue: function() {
        	if(this.searchData[this.index] && this.searchData[this.index].FIELD){
        		return this.searchData[this.index].FIELD[0];
        	}
            return "";
        }
    }
}
</script>