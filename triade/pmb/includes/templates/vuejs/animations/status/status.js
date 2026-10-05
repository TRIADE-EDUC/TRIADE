import Vue from "vue";
import statusform from "./components/statusForm.vue";
import statuslist from "./components/status.vue";


new Vue({
	el : "#status",
	data : {
		status : $data.status,
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
	},
	components : {
		statusform : statusform,
		statuslist : statuslist
	}
});