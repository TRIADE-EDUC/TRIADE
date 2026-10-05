import "regenerator-runtime/runtime";
import Vue from "vue";
import searchautocomplete from "./components/searchautocomplete.vue";
import Webservice from "../../common/helper/WebService.js";

const firstData = "$data_1";
Vue.prototype.ws = new Webservice(window[firstData].webservice_url);
Vue.prototype.pmb = pmbDojo.messages;

window.addEventListener('DOMContentLoaded', function(event) {
	let componentId = 1;
	while(document.getElementById("searchautocomplete_" + componentId) != null) {
		let vm = new Vue({
			el: "#" + "searchautocomplete_" + componentId,
			components: {
				searchautocomplete
			},
			data : {
				universeId : window["$data_" + componentId].universe_id ?? 0,
				segmentId : window["$data_" + componentId].segment_id ?? 0,
				inputId : window["$data_" + componentId].input_id,
				inputName : window["$data_" + componentId].input_name,
				inputValue : window["$data_" + componentId].input_value,
				inputClass : window["$data_" + componentId].input_class,
				inputSize : window["$data_" + componentId].input_size ?? 65,
				inputPlaceholder : window["$data_" + componentId].input_placeholder ?? "",
				showEntities : window["$data_" + componentId].show_entities ?? 1,
				images : pmbDojo.images,
				formId : window["$data_" + componentId].form_id,
				html : window["$data_" + componentId].html ?? "",
				startSearch : window["$data_" + componentId].start_search ?? 2,
				id : componentId,
				cmsSearch : window["$data_" + componentId].cms_search ?? 0,
				rgaaActive : window["$data_" + componentId].rgaaActive ?? 0
			}
		});
		vm.$root.$on('update-segment-id', (segmentId) => vm.$data.segmentId = segmentId);
		vm.$root.$on('update-universe-id', (universeId) => vm.$data.universeId = universeId);
		
		componentId++;
	}
})