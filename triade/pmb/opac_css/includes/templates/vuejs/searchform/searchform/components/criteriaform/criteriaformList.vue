<template>
	<div class="rmc_criteria_form_list">
		<operators v-if="criteria.QUERIES" 
			:fieldId="criteria.FIELD_ID" 
			:index="index" 
			:queries="criteria.QUERIES" 
			:selected="selectedOp" 
			@changeOp=" e => changeOperator(e)">
		</operators>
		<select v-if="formListOperator == 'EQ'" :value="searchValue" :name="name">
			<option v-for="(item, order) in criteria.INPUT_OPTIONS.VALUES" :key="order" :value="item.id">{{item.value}}</option>
		</select>
		<input v-else type="text" :value="searchValue" :name="name" />
		<fieldvars v-if="showfieldvars" :fields="criteria.VAR" :field="criteria_id" :index="index"></fieldvars>	
	</div>
</template>


<script>
import operators from "./operators.vue";
import fieldvars from "./fieldvars.vue";

export default {
	name : "criteriaFormList",
	props : ['criteria', 'index', 'searchData', 'showfieldvars'],
	data : function(){
		return {
			selectedValues : [],
			formListOperator : "",
		}
	},
	components : {
	    operators,
	    fieldvars
	},
	computed : {
		name: function() {
			return `field_${this.index}_${this.criteria.FIELD_ID}[]`;
		},
        selectedOp: function(event) {
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
        	if (this.selectedOp == this.formListOperator) {
	        	if(this.searchData[this.index] && this.searchData[this.index].FIELD){
	        		return this.searchData[this.index].FIELD[0];
	        	}
        	}
            return "";
        }
	},
	mounted() {
		this.formListOperator = this.selectedOp;
	},
	methods : {
		/**
		 * methode appelee par le composant enfant pour mettre a jour l'operateur selectionne.
		 * @param array data
		 * @returns {void}
		 */
		changeOperator(data) {
			if(data[1] == this.index){
				this.formListOperator = data[0];
			}
		},
	}
}
</script>