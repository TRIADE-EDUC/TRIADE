<template>
	<div class="row">
		<customfieldstext v-if="field.type == 'text'" :field="field" :values="values" :img="img"></customfieldstext>
		<customfieldslist v-if="field.type == 'list' || field.type == 'query_list'" :field="field" :values="values" :customprefixe="customprefixe" :img="img" :index="index"></customfieldslist>
		<customfieldsqueryauth v-if="field.type == 'query_auth'" :field="field" :values="values" :img="img"></customfieldsqueryauth>
		<customfieldsdate v-if="field.type == 'date_box'" :field="field" :values="values" :img="img"></customfieldsdate>
		<customfieldscomment v-if="field.type == 'comment'" :field="field" :values="values" :img="img"></customfieldscomment>
		<customfieldsexternal v-if="field.type == 'external'" :field="field" :values="values"></customfieldsexternal>
		<customfieldsresolve v-if="field.type == 'resolve'" :field="field" :values="values" :img="img"></customfieldsresolve>
		<customfieldshtml v-if="field.type == 'html'" :field="field" :values="values"></customfieldshtml>
		<customfieldsmarclist v-if="field.type == 'marclist'" :field="field" :values="values" :img="img"></customfieldsmarclist>
		<customfieldstexti18n v-if="field.type == 'text_i18n'" :field="field" :values="values" :img="img"></customfieldstexti18n>
		<customfieldsqualifiedtexti18n v-if="field.type == 'q_txt_i18n'" :field="field" :values="values" :img="img"></customfieldsqualifiedtexti18n>
		<customfieldsdateinterval v-if="field.type == 'date_inter'" :field="field" :values="values" :pmb="pmb" :img="img"></customfieldsdateinterval>
		<customfieldsurl v-if="field.type == 'url'" :field="field" :values="values" :img="img"></customfieldsurl>
		<customfielddateflot v-if="field.type == 'date_flot'" :field="field" :values="values" :img="img"></customfielddateflot>
	</div>
</template>

<script>
	import customfieldstext from "./types/customFieldsText.vue";
	import customfieldslist from "./types/customFieldsList.vue";
	import customfieldsqueryauth from "./types/customFieldsQueryAuth.vue";
	import customfieldsdate from "./types/customFieldsDate.vue";
	import customfieldscomment from "./types/customFieldsComment.vue";
	import customfieldsexternal from "./types/customFieldsExternal.vue";
	import customfieldsresolve from "./types/customFieldsResolve.vue";
	import customfieldsmarclist from "./types/customFieldsMarclist.vue";
	import customfieldstexti18n from "./types/customFieldsTextI18n.vue";
	import customfieldsqualifiedtexti18n from "./types/customFieldsQualifiedTextI18n.vue";
	import customfieldsdateinterval from "./types/customFieldsDateInterval.vue";
	import customfieldsurl from "./types/customFieldsURL.vue";
	import customfieldshtml from "./types/customFieldsHtml.vue";
	import customfielddateflot from "./types/customFieldsDateFlot.vue";

	export default {
		props : ["field", "values", "customprefixe", "img", "pmb", "index"],
		
		data: function(){
			return {
				dndId : this.values.length,
			}
		},

		components : {
			customfieldstext,
			customfieldslist,
			customfieldsqueryauth,
			customfieldsdate,
			customfieldsurl,
			customfieldscomment,
			customfieldsexternal,
			customfieldsresolve,
			customfieldsmarclist,
			customfieldstexti18n,
			customfieldsqualifiedtexti18n,
			customfieldsdateinterval,
			customfieldshtml,
			customfielddateflot
		},
		
		methods : {
			addCustomMultipleField : function(fieldName, index, event) {
				let keys = Object.keys(this.values[0]);
				let key = '';
				let newObj = {};
				for (let i = 0; i < keys.length; i++) {
					key = keys[i];
					if (Array.isArray(this.values[0][key])) {
						newObj[key] = [];
					} else if (typeof this.values[0][key] === 'boolean') {
						newObj[key] = false;
					} else if(key === 'dndId'){
						newObj[key] = this.dndId;
						this.dndId ++;
					} else{
						newObj[key] = '';
					}
				}
				this.values.push(newObj);
				
				if (typeof fieldName !== 'undefined' && fieldName) {
					index = parseInt(index, 10) + 1;
					this.$nextTick(() => {
						let elt = document.getElementById(fieldName + index);
						ajax_pack_element_without_spans(elt, event);
					});
				}
				document.activeElement.blur();
			},
			
			deleteCustomMultipleField : function(index) {
				if (this.values.length > 1) {
					this.values.splice(index, 1);
				} else {
					let keys = Object.keys(this.values[index]);
					let key = '';
					for (let i = 0; i < keys.length; i++) {
						key = keys[i];
						if (Array.isArray(this.values[index][key])) {
							this.values[index][key] = [];
						} else if (typeof this.values[0][key] === 'boolean') {
							this.values[index][key] = false;
						} else {
							this.values[index][key] = '';
						}
					}
				}
				document.activeElement.blur();
			},
			
			changeCustomMultipleField : function(fieldName, index, event) {
				let label = document.getElementById(fieldName + index).value;
				let id = event.target.value;
				
				if (typeof this.values[index] !== 'undefined' || this.values[index] == 0) {
					this.values[index].value = id;
					this.values[index].displayLabel = label;
				} else {
					this.values.push({ id : id, displayLabel : label });
				}
			}
		} 
	}
</script>