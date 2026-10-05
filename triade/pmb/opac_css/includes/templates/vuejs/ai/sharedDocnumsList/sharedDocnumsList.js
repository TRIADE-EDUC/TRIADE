import Vue from "vue";

// Helper
import Webservice from "../../common/helper/WebService.js";
import Messages from "../../common/helper/Messages.js";
import Images from "../../common/helper/Images.js";

// Components
import docnumsList from "./components/docnumsList.vue";

// Init
$sharedDocnumsListData = $sharedDocnumsListData || { webservice_url: "", shared_list_id: 0 };
Vue.prototype.ws = new Webservice($sharedDocnumsListData.webservice_url);
Vue.prototype.messages = Messages;
Vue.prototype.images = Images;

document.addEventListener('DOMContentLoaded', () => {
  new Vue({
    el: "#shared_docnums_list",
    data: {
      ...$sharedDocnumsListData
    },
    components: {
      docnumsList
    }
  });
})