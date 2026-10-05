import Vue from "vue";
import emprAnimationList from "./components/emprAnimationList.vue";

new Vue({
	el : "#empr",
	data : {
		action : $data.action,
		pmb : pmbDojo.messages,
		formdata : $data.formData,
	},
	components : {
		empranimationlist : emprAnimationList,
	}
});