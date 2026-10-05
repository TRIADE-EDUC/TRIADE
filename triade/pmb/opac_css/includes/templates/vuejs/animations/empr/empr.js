import Vue from "vue";
import emprregistration from "./components/emprRegistration.vue";

var vm = new Vue({
	el : "#empr_registration",
	data : {
		pmb : pmbDojo.messages,
		registrations : $data.registrations,
	},
	components : {
		emprregistration : emprregistration
	}
});
