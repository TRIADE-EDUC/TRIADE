import Vue from "vue";
import InitVue from "../../common/helper/InitVue.js";


/**
 * Components
 */
import loader from "../../common/components/loader.vue";
import searchhub from "./components/searchhub.vue";

InitVue(Vue, { useLoader: true });
Vue.prototype.cmsMessages = $data.msg ?? {};
Vue.prototype.urlWebservice = $data.url_webservice;
Vue.prototype.categories = $data.categories;
Vue.prototype.csrfTokens = $data.csrf_tokens;
delete $data.csrf_tokens;

new Vue({
    el: "#cms_module_searchhub",
    data: { ...$data },
    components: {
      loader,
      searchhub
    }
});