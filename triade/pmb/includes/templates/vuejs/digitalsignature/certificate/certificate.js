import Vue from "vue";
import certificateList from "./components/certificateList.vue";
import certificateEdit from "./components/certificateEdit.vue";

import { VueNestable, VueNestableHandle } from 'vue-nestable'

Vue.component('VueNestable', VueNestable);
Vue.component('VueNestableHandle', VueNestableHandle);

new Vue({
	el : "#certificate",
	data : {
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
		list : $data.list,
		certificate : $data.certificate,
	},
	components : {
		certificatelist : certificateList,
		certificateedit : certificateEdit,
	}
});