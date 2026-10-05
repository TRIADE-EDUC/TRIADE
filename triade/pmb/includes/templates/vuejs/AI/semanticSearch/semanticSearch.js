import Vue from "vue";

import semanticsearchlist from "./components/semanticSearchList.vue";
import semanticsearchform from "./components/semanticSearchForm.vue";

import InitVue from "../../common/helper/InitVue.js";

InitVue(Vue, {
    useLoader: true,
    urlWebservice: $data.url_webservice,
	webserviceCachingOptions: {
		lifetime: 1000
	},
});

new Vue({
    el: "#semantic_search",
    data: { ...$data },
    components: {
        semanticsearchlist: semanticsearchlist,
        semanticsearchform: semanticsearchform
    }
});