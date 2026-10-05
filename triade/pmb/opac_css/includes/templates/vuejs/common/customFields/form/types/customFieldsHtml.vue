<template>
	<div class="container">
<!-- 		<div v-if="field.options[0].repeatable[0].value === '1'" class="row">
			<input @click="addCustomMultipleField()" class="bouton" type="button" value="+"/>
		</div> -->
		<div v-for="(customValue, index) in values" class="row">
			<div :id="field.name + index" 
				v-model="customValue.value"
				:height="field.options[0].height[0].value"
				:width="field.options[0].width[0].value"
				data-dojo-type="dijit/Editor"
				:data-form-name="field.name + index"
				:data-dojo-props="field.options[0].dataProps[0].value" 
			>
			</div>
			<input @click="$parent.deleteCustomMultipleField(index)" class="bouton" type="button" value="X"/>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["field", "values"],
		
		methods : {
			addCustomMultipleField : function() {
				this.values.push({
					value : '',
					displayLabel : '',
				});
				this.$nextTick(() => {
					let index = this.values.length-1;
					let elt = document.getElementById(this.field.name+index.toString());
					new dijit.Editor({id : elt}).startup();
				});
			},
		}
	}
</script>