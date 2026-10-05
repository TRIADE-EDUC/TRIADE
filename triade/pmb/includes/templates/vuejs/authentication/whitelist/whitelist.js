import Vue from "vue";
import InitVue from "../../common/helper/InitVue.js"

import loader from "../../common/loader/loader.vue";
import whitelist from "./components/whitelist.vue";


InitVue(Vue, {
    urlWebservice: $data.url_webservice,
    useLoader: true
});

new Vue({
    el : "#auth-whitelist",
    data : {...$data},
    components : {
        loader,
        whitelist
    }
});