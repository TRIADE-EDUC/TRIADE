<template>
	<div :id="field.name" class="custom_container">
		<customfieldstext v-if="field.type == 'text'" :values="values"></customfieldstext>
		<customfieldslist v-if="field.type == 'list' || field.type == 'query_list'" :field="field" :values="values"></customfieldslist>
		<customfieldsdateinterval  v-if="field.type == 'date_inter'" :values="values" :pmb="pmb"></customfieldsdateinterval>
		<customfieldscomment v-if="field.type == 'comment'" :values="values"></customfieldscomment>
		<customfieldsdate v-if="field.type == 'date_box'" :values="values"></customfieldsdate>
		<customfieldsdateflot v-if="field.type == 'date_flot'" :field="field" :values="values"></customfieldsdateflot>
		<customfieldsresolve v-if="field.type == 'resolve'" :field="field" :values="values" :img="img"></customfieldsresolve>
		<customfieldsmarclist v-if="field.type == 'marclist'" :field="field" :values="values"></customfieldsmarclist>
		<customfieldstexti18n v-if="field.type == 'text_i18n'" :field="field" :values="values"></customfieldstexti18n>
		<customfieldsqualifiedtexti18n v-if="field.type == 'q_txt_i18n'" :field="field" :values="values"></customfieldsqualifiedtexti18n>
		<customfieldsqueryauth v-if="field.type == 'query_auth'" :field="field" :values="values"></customfieldsqueryauth>
		<customfieldsurl v-if="field.type == 'url'" :values="values" :img="img" :csrftokens="csrftokens"></customfieldsurl>
	</div>
</template>

<script>
	import customfieldstext from "./types/customFieldsText.vue";
	import customfieldslist from "./types/customFieldsList.vue";
	import customfieldsdateinterval from "./types/customFieldsDateInterval.vue";
	import customfieldscomment from "./types/customFieldsComment.vue";
	import customfieldsdate from "./types/customFieldsDate.vue";
	import customfieldsdateflot from "./types/customFieldsDateFlot.vue";
	import customfieldsqueryauth from "./types/customFieldsQueryAuth.vue";
	import customfieldsurl from "./types/customFieldsURL.vue";
	import customfieldsresolve from "./types/customFieldsResolve.vue";
	import customfieldsmarclist from "./types/customFieldsMarclist.vue";
	import customfieldstexti18n from "./types/customFieldsTextI18n.vue";
	import customfieldsqualifiedtexti18n from "./types/customFieldsQualifiedTextI18n.vue";

	export default {
		props : ["field", "values", "customprefixe", "img", "pmb", "csrftokens"],
		
		components : {
			customfieldstext,
			customfieldslist,
			customfieldsdateinterval,
			customfieldscomment,
			customfieldsdate,
			customfieldsdateflot,
			customfieldsresolve,
			customfieldsmarclist,
			customfieldstexti18n,
			customfieldsqualifiedtexti18n,
			customfieldsqueryauth,
			customfieldsurl
		}
	}
</script>
<style>
.custom_container {
	display: inline;
}
</style>