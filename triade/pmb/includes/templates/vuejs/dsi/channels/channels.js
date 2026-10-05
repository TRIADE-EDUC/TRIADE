import Vue from "vue";
import list from "./components/list.vue";
import edit from "./components/formChannel.vue";
import loader from "../components/loader.vue"
import DsiMessages from "../helper/DsiMessages";

import InitVue from "../../common/helper/InitVue.js";
InitVue(Vue, {
	urlWebservice: $data.url_webservice,
	useLoader: true,
    webserviceCachingOptions: {
        lifetime: 1000
    },
	plugins : {
		"dsiMessages" : new DsiMessages($data.url_webservice)
	}
});

new Vue({
	el : "#channels",
	data : {
		...$data
	},
	components : {
		list,
		edit,
		loader
	}
});