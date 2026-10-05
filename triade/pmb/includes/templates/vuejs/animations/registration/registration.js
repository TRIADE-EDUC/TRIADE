import Vue from "vue";
import registration from "./components/registration.vue";
import registrationform from "./components/registrationForm.vue";
import registrationview from "./components/registrationView.vue";

var vm = new Vue({
	el:"#registration",
	data: {
		registrationlist : $data.registrationList,
		action : $data.action,
		formdata : $data.formData,
		animationlist : $data.animationList,
		pmb: pmbDojo.messages,
		statuslist : $data.statusRegistrationlist,
		selectedstatus : $data.selectedStatusRegistration,
		localisationlist : $data.localisationList,
	},
	components : {
		registration : registration,
		registrationform : registrationform,
		registrationview : registrationview,
	}
});
