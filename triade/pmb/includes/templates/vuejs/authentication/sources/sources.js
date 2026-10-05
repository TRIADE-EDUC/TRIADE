import Vue from "vue";
import sources from "./components/sources.vue";

import Images from "../../common/helper/Images.js";
Vue.prototype.images = Images;

import InitVue from "../../common/helper/InitVue.js"
InitVue(Vue, {
    urlWebservice: $data.url_webservice,
    useLoader: true
});

new Vue({
    el : "#sources",
    data : {...$data},
    components : {
        sources
    }
});