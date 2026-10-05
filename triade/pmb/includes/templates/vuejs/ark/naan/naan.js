import Vue from "vue";
import naanForm from "./components/naanForm.vue";

import { VueNestable, VueNestableHandle } from 'vue-nestable'

Vue.component('VueNestable', VueNestable);
Vue.component('VueNestableHandle', VueNestableHandle);

new Vue({
	el : "#naan",
	data : {
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
		naan : $data.naan,
	},
	components : {
		naanform : naanForm,
	}
});