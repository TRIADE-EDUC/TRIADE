import Vue from "vue";

// Helper
import Webservice from "../../common/helper/WebService.js";
import messages from "../../common/helper/Messages.js";

// Components
import sharedbuttons from "./components/sharedButtons.vue";
import loader from "../../common/loader/loader.vue";

Vue.prototype.showLoader = () => {
  window.dispatchEvent(new Event("showLoader"));
};
Vue.prototype.hiddenLoader = () => {
  window.dispatchEvent(new Event("hiddenLoader"));
};

document.addEventListener('DOMContentLoaded', function(event) {
  $sharedListData = $sharedListData || {};

  Vue.prototype.messages = messages;
  Vue.prototype.ws = new Webservice($sharedListData.webservice_url);

  new Vue({
    el: "#ai_shared_list",

    data: {
      ...$sharedListData
    },
    components: {
      sharedbuttons,
      loader
    },
    mounted() {
      window.addEventListener('DocumentUploaded', () => {
        this.nb_docnums++;
      });
      window.addEventListener('DocumentRemoved', () => {
        this.nb_docnums--;
      });
    }
  });
})
