import Vue from "vue";

import paymentsOrganizationList from "./components/paymentsOrganizationList.vue";
import paymentOrganizationForm from "./components/paymentOrganizationForm.vue";

import InitVue from "../../common/helper/InitVue.js";
InitVue(Vue, {
	useLoader: true,
});

new Vue({
	el : "#paymentsOrganization",
	data : {
		...$data
	},
	components : {
		paymentsorganizationlist : paymentsOrganizationList,
		paymentorganizationform : paymentOrganizationForm,
	}
});