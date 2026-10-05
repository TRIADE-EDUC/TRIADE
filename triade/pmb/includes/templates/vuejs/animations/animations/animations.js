import Vue from "vue";

import InitVue from "@/common/helper/InitVue.js";
import loader from "@/common/loader/loader.vue";

import animations from "./components/animations.vue";
import animationsform from "./components/animationsForm.vue";
import animationsview from "./components/animationsView.vue";
import animationsdnd from "./components/animationsDnD.vue";
import animationsdaughterlist from "./components/animationsDaughterList.vue";
import registration from "../registration/components/registration.vue";
import mailingsendlist from "../mailing/components/mailingSendList.vue";
import animationcalendar from "./components/animationCalendar.vue";

import { VueNestable, VueNestableHandle } from 'vue-nestable';
Vue.component('VueNestable', VueNestable);
Vue.component('VueNestableHandle', VueNestableHandle);

InitVue(Vue, { useLoader: true });

new Vue({
	el : "#animations",
	data : {
		animations : $data.animations || [],
		pagination : $data.pagination || [],
		animationDaugthterList : $data.animationDaughterList,
		action : $data.action,
		formdata : $data.formData || {},
		pmb : pmbDojo.messages,
		registrationList : $data.registrationList,
		registrationWaitingList : $data.registrationWaitingList,
		animationList : $data.animationList,
		mailingSendList : $data.mailingSendList,
		calendarAnimation : [],
		csrftokens : $data.csrftokens,
	},
	components : {
		loader: loader,
		animations : animations,
		animationsform : animationsform,
		animationsview : animationsview,
		animationsdnd : animationsdnd,
		animationsdaughterlist : animationsdaughterlist,
		registration : registration,
		mailingsendlist : mailingsendlist,
		animationcalendar : animationcalendar
	}
});