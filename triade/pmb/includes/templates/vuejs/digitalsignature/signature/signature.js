import Vue from "vue";
import signatureList from "./components/signatureList.vue";
import signatureEdit from "./components/signatureEdit.vue";

import { VueNestable, VueNestableHandle } from 'vue-nestable'

Vue.component('VueNestable', VueNestable);
Vue.component('VueNestableHandle', VueNestableHandle);

new Vue({
	el : "#signature",
	data : {
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
		list : $data.list,
		signdata : $data.signdata,
		certificates : $data.certificates,
	},
	components : {
		signaturelist : signatureList,
		signatureedit : signatureEdit,
	}
});