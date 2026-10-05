import Vue from "vue";
import status from "../components/status.vue";
import statusform from "../components/statusForm.vue";
import DsiMessages from "../helper/DsiMessages";
import Const from "../../common/helper/Const.js";

import InitVue from "../../common/helper/InitVue.js";
InitVue(Vue, {
	urlWebservice: $data.url_webservice,
	useLoader: true,
	plugins : {
		"dsiMessages" : new DsiMessages($data.url_webservice),
		"Const" : new Const("dsi", ["tags", "items", "subscriberlist", "views"])
	}
});

new Vue({
	el : "#statusProducts",
	data : {
		...$data
	},
	components : {
		status,
		statusform
	}
});