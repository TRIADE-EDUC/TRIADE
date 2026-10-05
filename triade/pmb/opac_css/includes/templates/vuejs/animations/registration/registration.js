import Vue from "vue";
import registrationform from "./components/registrationForm.vue";
import registrationsaved from "./components/registrationSaved.vue";
import registrationdeleted from "./components/registrationDeleted.vue";

var vm = new Vue({
	el:"#registration",
	data: {
		pmb: pmbDojo.messages,
		formdata: $data.formData,
		action: $data.action,
		registration: $data.registration,
	},
	components : {
		registrationform: registrationform,
		registrationsaved: registrationsaved,
		registrationdeleted: registrationdeleted
	}
});
