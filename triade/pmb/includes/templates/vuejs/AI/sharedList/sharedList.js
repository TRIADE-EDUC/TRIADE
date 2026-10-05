import Vue from "vue";

import sharedlistform from "./components/sharedListForm.vue";

import InitVue from "../../common/helper/InitVue.js";

InitVue(Vue, {
    useLoader: true,
    urlWebservice: $data.url_webservice,
	webserviceCachingOptions: {
		lifetime: 1000
	},
});

new Vue({
    el: "#shared_list",
    data: { ...$data },
    components: {
        sharedlistform: sharedlistform
    }
});