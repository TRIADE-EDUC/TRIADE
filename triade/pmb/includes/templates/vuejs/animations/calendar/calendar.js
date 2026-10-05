import Vue from "vue";
import calendarform from "./components/calendarForm.vue";
import calendarlist from "./components/calendar.vue";


new Vue({
	el : "#calendar",
	data : {
		calendar : $data.calendar,
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
	},
	components : {
		calendarform : calendarform,
		calendarlist : calendarlist
	}
});