import Vue from "vue";
import InitVue from "../../common/helper/InitVue.js"

import loader from "../../common/loader/loader.vue";
import blacklist from "./components/blacklist.vue";


InitVue(Vue, {
    urlWebservice: $data.url_webservice,
    useLoader: true
});

new Vue({
    el : "#auth-blacklist",
    data : {...$data},
    components : {
        loader,
        blacklist
    }
});