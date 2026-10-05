import Vue from "vue";
import generateForm from "./components/generateForm.vue";

import { VueNestable, VueNestableHandle } from 'vue-nestable'

Vue.component('VueNestable', VueNestable);
Vue.component('VueNestableHandle', VueNestableHandle);

new Vue({
	el : "#generate",
	data : {
		action: $data.action,
		count: $data.count,
		start: $data.start,
		next: $data.next,
		pmb : pmbDojo.messages,
		img : $data.img,
	},
	components : {
		generateform : generateForm,
	}
});