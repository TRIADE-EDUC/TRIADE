import Vue from "vue";
import loader from "../../common/loader/loader.vue"
import settings from "./components/settings.vue"

import InitVue from "../../common/helper/InitVue.js";
InitVue(Vue, {
	urlWebservice: $data.url_webservice,
	useLoader: true
});

new Vue({
	el : "#otp",
	data : {
		...$data
	},
	components : {
		loader,
		settings
	}
});