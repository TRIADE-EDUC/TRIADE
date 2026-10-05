import Vue from "vue";
import unsubscribe from "./components/unsubscribe.vue";
import messages from "../../common/helper/Messages.js";

Vue.prototype.messages = messages;
new Vue({
	el:"#unsubscribe",
	data: {
		pmb: pmbDojo.messages,
		diffusion: $unsubscribeData.diffusion
	},
	components : {
        unsubscribe
	}
});