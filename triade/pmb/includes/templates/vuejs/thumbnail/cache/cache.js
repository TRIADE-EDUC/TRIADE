import Vue from "vue";

import InitVue from "../../common/helper/InitVue.js";
InitVue(Vue, {
	urlWebservice: $data.url_webservice,
});

import cache from "./components/cache.vue";

new Vue({
	el : "#cache",
	data : {...$data},
	components : {
		cache
	}
});