<template>
	<span :class="'search_variable_'+field.NAME">
		<span :class="field.SPAN">
			<label :for="id">{{ field.COMMENT }}</label>
		</span>
		<span class="search_value">
			<select :id="id" :name="name" :value="dataSelected">
				<option v-for="(option, key) in field.OPTIONS" v-if="isNumeric(key)" :key="key" :value="option.VALUE">
					{{ option.LABEL }}
				</option>
			</select>
		</span>
	</span>
</template>

<script>
export default {
	name: "fieldvarsFormQueryList",
	props : ['field', 'id', 'name', 'value'],
	data : function(){
		return {
			dataSelected : null,
		}
	},
	watch:{
		selected: function(val) {
			this.dataSelected = val;
		}
	},
	computed: {
	},
	created : function() {
		this.dataSelected = this.initDataSelected();
	},
	methods: {
		isNumeric : function(key) {
			return !isNaN(key);
		},
		initDataSelected : function() {
			if (this.value) {
				return	this.value;
			}
			if (this.field.VALUE) {
				return	this.field.VALUE;
			}
			return null;
		}
	}
}
</script>