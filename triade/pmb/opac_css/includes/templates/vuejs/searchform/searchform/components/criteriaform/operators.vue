<template>
	<select :title='pmb.getMessage("searchform", "operator_input_title")' :id="name" class="rmc_search_op" v-model="dataSelected" @change="e => emit_event(e)" :name="name">
		<option v-for="(operator, key) in operators" :key="key" :value="operator.value">
			{{ operator.label }}
		</option>
	</select>
</template>

<script>
export default {
	name: "operators",
	props : ['queries', 'selected', 'index', 'fieldId'],
	data : function(){
		return {
			dataSelected : this.selected,
		}
	},
	watch:{
		selected: function(val) {
			this.dataSelected = val;
		}
	},
	computed: {
	    operators: function() {
	        var operators = new Array();
	        if (this.queries && this.queries.length) {
	            for (var i = 0; i < this.queries.length; i++) {
	                var operator = this.queries[i];
	                if (operator) {
		                operators.push({value: operator['OPERATOR'], label: operator['LABEL']});
	                }
	            }
	        }
	        return operators;
	    },
		name: function() {
		    return `op_${this.index}_${this.fieldId}`;
		}
	},
	methods: {
		emit_event: function(e) {
			this.$emit('update:selected', this.dataSelected)
			this.$emit('changeOp', [e.target.value, this.index])
		}
	}
}
</script>