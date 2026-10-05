
import Vue from "vue";
import configgestion from "./components/configGestion.vue";

import Images from "../../common/helper/Images.js";
Vue.prototype.images = Images;

import InitVue from "../../common/helper/InitVue.js"
InitVue(Vue, {
    urlWebservice: $data.url_webservice,
    useLoader: true
});

new Vue({
    el : "#configGestion",
    data : {...$data},
    components : {
        configgestion
    }
});