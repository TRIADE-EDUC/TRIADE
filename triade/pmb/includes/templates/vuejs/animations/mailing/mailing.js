import Vue from "vue";
import mailingform from "./components/mailingForm.vue";
import mailinglist from "./components/mailing.vue";
import mailingtemplate from "./components/mailingTemplate.vue";

import { VueNestable, VueNestableHandle } from 'vue-nestable'

Vue.component('VueNestable', VueNestable);
Vue.component('VueNestableHandle', VueNestableHandle);

new Vue({
	el : "#mailing",
	data : {
		mailingsTypes : $data.mailingsTypes,
		animation : $data.animation,
		registration : $data.registration,
		formData : $data.formData,
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
		mailingDetail : $data.mailingDetail,
		senders : $data.senders,
		type : $data.type,
		typeComIsSet : $data.typeComIsSet,
		deflt_associated_campaign : $data.deflt_associated_campaign
	},
	components : {
		mailingform : mailingform,
		mailinglist : mailinglist,
		mailingtemplate : mailingtemplate
	}
});