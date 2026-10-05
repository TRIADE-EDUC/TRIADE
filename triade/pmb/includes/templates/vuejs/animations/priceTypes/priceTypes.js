import Vue from "vue";
import pricetypesform from "./components/priceTypesForm.vue";
import pricetypeslist from "./components/priceTypes.vue";

import { VueNestable, VueNestableHandle } from 'vue-nestable'

Vue.component('VueNestable', VueNestable);
Vue.component('VueNestableHandle', VueNestableHandle);

new Vue({
	el : "#priceTypes",
	data : {
		pricetypes : $data.priceTypes,
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
	},
	components : {
		pricetypesform : pricetypesform,
		pricetypeslist : pricetypeslist
	}
});