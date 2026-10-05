
import Vue from "vue";
import configopac from "./components/configOpac.vue";

import Images from "../../common/helper/Images.js";
Vue.prototype.images = Images;

import InitVue from "../../common/helper/InitVue.js"
InitVue(Vue, {
    urlWebservice: $data.url_webservice,
    useLoader: true
});

new Vue({
    el : "#configOpac",
    data : {...$data},
    components : {
        configopac
    }
});