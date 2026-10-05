import Vue from "vue";
import searchform from "./components/searchform.vue";

Vue.prototype.pmb = pmbDojo.messages;
window.addEventListener('DOMContentLoaded', function(event) {
	new Vue({
		el: "#searchform",
		components: {
			searchform : searchform
		},
		data : {
			form_url : $data.form_url,
			criterias : $data.criterias,
			search : $data.search ?? [],
			inter : $data.inter,
			intervalues : $data.inter_values,
			defaultinter : $data.default_inter,
			hidden : $data.hidden,
			pmb : pmbDojo.messages,
			showfieldvars : $data.show_fieldvars,
			images : pmbDojo.images,
			criterias_hidden: $data.criterias_hidden ?? [],
			rgaa_active: $data.globals.opac_rgaa_active ?? 0,
			persopac: $data.persopac ?? null
		}
	});
})