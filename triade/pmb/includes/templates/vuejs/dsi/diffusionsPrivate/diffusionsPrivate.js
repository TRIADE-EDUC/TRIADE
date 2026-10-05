import Vue from "vue";
import formDiffusionsPrivate from "./components/formDiffusionsPrivate.vue";
import loader from "../components/loader.vue"
import DsiMessages from "../helper/DsiMessages";
import Const from "../../common/helper/Const.js";

import InitVue from "../../common/helper/InitVue.js";
InitVue(Vue, {
	useLoader: true,
	urlWebservice: $data.url_webservice,
	webserviceCachingOptions: {
		lifetime: 1000
	},
	plugins : {
		"dsiMessages" : new DsiMessages($data.url_webservice),
		"Const" : new Const("dsi", ["tags", "items", "subscriberlist", "views"])
	}
});

// Directive globale appelee v-focus
Vue.directive('focus', {
	inserted: function (el) {
		el.focus()
	}
})

new Vue({
	el : "#diffusionsPrivate",
	data : {
		...$data
	},
	components : {
		formDiffusionsPrivate,
		loader
	}
});