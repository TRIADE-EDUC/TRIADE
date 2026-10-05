import Vue from "vue";

import InitVue from "@/common/helper/InitVue.js";
import loader from "@/common/loader/loader.vue";

import formSearch from "./components/formSearch.vue";

InitVue(Vue, { useLoader: true });

new Vue({
	el:"#search",
	data: {
		pmb: pmbDojo.messages,
		formData: $data.formData
	},
	methods: {},
	components : {
		loader: loader,
		formsearch: formSearch
	}
});
