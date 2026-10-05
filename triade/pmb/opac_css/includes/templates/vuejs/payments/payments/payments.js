import Vue from "vue";
import paymentslist from "./components/paymentsList.vue";
import Messages from "../../common/helper/Messages.js";
Vue.prototype.messages = Messages;

new Vue({
	el : "#payments",
	data : {
		...$paymentsData
	},
	components : {
		paymentslist : paymentslist,
	}
});