import Vue from "vue";
import typesform from "./components/typesForm.vue";
import typeslist from "./components/types.vue";


new Vue({
	el : "#types",
	data : {
		types : $data.types,
		action: $data.action,
		pmb : pmbDojo.messages,
		img : $data.img,
	},
	components : {
		typesform : typesform,
		typeslist : typeslist
	}
});