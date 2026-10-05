import Vue from "vue";
import list from "./components/list.vue";
import add from "./components/add.vue";
import loader from "../components/loader.vue"
import DsiMessages from "../helper/DsiMessages";
import Utils from "../helper/Utils";
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
		"utils": Utils,
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
	el : "#diffusions",
	data : {
		...$data
	},
	components : {
		list,
		add,
		loader
	}
});