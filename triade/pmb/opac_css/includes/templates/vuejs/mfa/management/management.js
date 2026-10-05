import Vue from "vue";
import initialization from "./components/initialization.vue"
import before_initialization from "./components/beforeInitialization.vue"
import after_initialization from "./components/afterInitialization.vue"
import Notif from "../../common/helper/Notif.js";

Vue.prototype.req = new http_request();
Vue.prototype.url_base = $data.url_base;
Vue.prototype.pmb = pmbDojo.messages;
Vue.prototype.notif = Notif;

window.addEventListener('DOMContentLoaded', function(event) {
	new Vue({
		el: "#mfa-init",
		data: {
			...$data,
			init: false,
		},
		mounted: function() {
			this.$refs["mfa-title"].innerHTML = this.pmb.getMessage('mfa', 'mfa_title');
		},
		components: {
			initialization,
			before_initialization,
			after_initialization
		}
	});
})