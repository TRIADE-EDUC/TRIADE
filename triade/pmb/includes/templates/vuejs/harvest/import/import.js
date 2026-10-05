import Vue from "vue";

import Messages from "@/common/helper/Messages.js";
Vue.prototype.messages = Messages;

import Notif from "@/common/helper/Notif.js";
Vue.prototype.notif = Notif;

import Helper from "@/common/helper/Helper.js";
Vue.prototype.helper = Helper;

import WebService from "@/common/helper/WebService.js";
Vue.prototype.ws = new WebService($data.url_webservice, false);
import list from "../common/list.vue";
import formHarvestImport from "./components/formHarvestImport.vue";

new Vue({
	el : "#harvest-import",
	data : {
		profil : $data.profil ?? {},
		list : $data.list ?? [],
		flags : $data.flags ?? [],
		action : $data.action,
		url : $data.url
	},
	components : {
        list,
		formHarvestImport
	}
});