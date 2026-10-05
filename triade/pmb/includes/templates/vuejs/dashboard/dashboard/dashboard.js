import Vue from "vue";

import dashboard from "./components/dashboard.vue";
import loader from "../components/loader.vue"

import Const from "../../common/helper/Const.js";
import InitVue from "@/common/helper/InitVue.js";

InitVue(Vue, {
	useLoader: true,
	urlWebservice: $data.url_webservice,
	webserviceCachingOptions: {
		lifetime: 1000
	},
	plugins : {
		"Const" : new Const("dashboard", ["main"])
	}
});

new Vue({
	el: "#dashboard",
	data: {
		...$data
	},
	components: {
		dashboard,
		loader
	}
});