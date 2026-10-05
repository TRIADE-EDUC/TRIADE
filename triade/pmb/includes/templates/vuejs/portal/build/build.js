import Vue from "vue";
import Cms from "./classes/Cms.js";

// Do not remove !
import init from "./classes/Egg.js";

/**
 * Components
 */
import loader from "./components/loader.vue";
import accordion from "./components/accordion/accordion.vue";
import navigation from "./components/nav/nav.vue";
import page_form from "./components/page/pageForm.vue";
import gabarit_form from "./components/gabarit/gabaritForm.vue";
import frame_form from "./components/frame/frameForm.vue";
import layout from "./components/layout/layout.vue";
import preview from "./components/preview/preview.vue";
import page_type_form from "./components/page/pageTypeForm.vue";

/**
 * Cms est un plugin fait pour être utilisé dans Vuejs
 * Pour l'utiliser dans le composant, il faut faire "this.$cms"
 */
Vue.use(Cms, {...$data, ...$cmsData});

window.addEventListener("load", () => {

	init();
	
	const input = document.createElement("input");
	input.id = "cms_build_info";
	input.name = "cms_build_info";
	input.type = "hidden";
	if ($data.cms_build_info) {
		input.value = $data.cms_build_info;
	}
	document.head.appendChild(input);

	new Vue({
		el: "#portal",
		components : {
			loader,
			accordion,
			navigation,
			page_form,
			gabarit_form,
			frame_form,
			layout,
			preview,
			page_type_form
		},
		mounted: function () {
			this.$el.style = "";
		},
		computed: {
			container:  function() {
				return this.$cms.container;
			}
		}
	});
});