<template>
	<div>
		<div style="height : 40px;">
			<input
				type="text"
				:id="inputId + '_' + id"
				:name="inputName"
				:title="inputPlaceholder"
				:class="inputClass + ' vue-js-input'"
				:size="inputSize"
				:placeholder="inputPlaceholder"
				:list="datalistId + '_' + id"
				v-model="inputText"
				autocomplete="off"
				@input.stop="updateDatalist"
				@focus="seeDatalist"
				@keydown.down.prevent="increaseIndex"
				@keydown.up.prevent="decreaseIndex"
				@keydown.tab.exact.prevent="increaseIndex"
				@keydown.shift.tab.prevent="decreaseIndex"
				@blur="hideDatalist(true)" />
			<label v-if="rgaaActive" class="visually-hidden" :for="inputId + '_' + id">{{inputPlaceholder}}</label>
			<span v-html="html"></span>
			<entitiesselector v-if="showEntities"
				:entities="entities"
				v-model="selectedEntities"
				:key="'selector_' + id" />
		</div>
		<datalistoptions
			:key="'datalist_' + id"
			:datalist-id="datalistId + '_' + id"
			:items="items"
			:input-id="inputId + '_' + id"
			:datalist-index="datalistIndex"
			:show-datalist="showDatalist"
			:show-loader="showLoader"
			@update-label="setLabel($event)"
			@reset-index="resetIndex" />
	</div>
</template>

<script>

import datalistoptions from "./datalistoptions.vue";
import entitiesselector from "./entitiesselector.vue";
const TIMEOUT = 500;

export default {
	name: "searchautocomplete",
	props : [
	    'universeId',
		'segmentId',
	    'inputId',
		'inputName',
	    'inputClass',
	    'inputValue',
	    'inputSize',
	    'inputPlaceholder',
	    'images',
	    'showEntities',
		'formId',
		'html',
		'startSearch',
		'id',
		'cmsSearch',
		'rgaaActive'
    ],
	components : {
		datalistoptions,
		entitiesselector
	},
	data : function(){
		return {
			inputText : "",
			items : [],
			datalistIndex : -1,
			entities : {},
			selectedEntities : [],
			cmsPageId : "",
			tempInputText : "",
			showDatalist : false,
			showLoader : false,
			requestTimeout : null
		}
	},
	created : function() {
		if(this.inputValue != "*") {
			this.inputText = this.inputValue;
		}
		this.getEntities();
	},
	computed: {
		datalistId : function() {
			return this.inputId + "_list";
		},
	},
	methods :{
		updateDatalist : async function() {
			if (this.inputText.length > this.startSearch) {

				if(! this.mustBeUpdated()) {
					return false;
				}

				// Si on a une requete en cours, on l'arrete
				if (this.requestTimeout != null) {
					clearTimeout(this.requestTimeout);
					this.requestTimeout = null;
				}

				// On attend 500ms avant de lancer la requete
				this.requestTimeout = setTimeout(
					this.fetchDataList,
					TIMEOUT
				);
			} else {
				this.items = [];
			}
		},
		fetchDataList: async function() {
			this.showLoader = true;

			if(this.cmsSearch) {
				this.getCmsSettings();
			}
			var searchData = {
				"user_query" : this.inputText,
			};
			let response = false;
			switch(true) {
				case parseInt(this.universeId) > 0:
					response = await this.ws.post('universe', this.universeId, searchData);
				break;
				case parseInt(this.segmentId) > 0:
					response = await this.ws.post('segment', this.segmentId, searchData);
				break;
				case parseInt(this.cmsPageId) > 0:
					response = await this.ws.post('cms', this.cmsPageId, searchData);
					break;
				default:
					if(this.selectedEntities.length) {
						searchData["entities_types"] = this.selectedEntities;
					}
					response = await this.ws.post('search', 'simple', searchData);
				break;
			}

			if (! response.error) {
				this.items = response;
			}

			this.showLoader = false;
		},
		setLabel : async function(label) {
			await this.$set(this, "inputText", label);
			this.sendSearch();
		},
		increaseIndex : function() {
			if(this.items.length == 0 || this.datalistIndex + 1 >= this.items.length) {
				return;
			}
			this.datalistIndex++;
			if(typeof this.items[this.datalistIndex] == "undefined") {
				this.datalistIndex = 0;
			}
			this.$set(this, "inputText", this.items[this.datalistIndex].value);
		},
		decreaseIndex : function() {
			if(this.items.length == 0 || this.datalistIndex == 0) {
				return;
			}
			this.datalistIndex--;
			if(typeof this.items[this.datalistIndex] == "undefined") {
				this.datalistIndex = 0;
			}
			this.$set(this, "inputText", this.items[this.datalistIndex].value);
		},
		resetDatalist : function() {
			setTimeout(() => {
				this.$set(this, "items", []);
				this.resetIndex();
			}, 100)
		},
		sendSearch : function() {
			let form = document.forms[this.formId];
			if(form !== null) {
				form.requestSubmit();
			}
		},
		resetIndex :  function() {
			this.datalistIndex = -1;
		},
		getEntities : async function() {
			this.entities = await this.ws.get("entities", "list");
			for(let key in this.entities) {
				if(this.entities[key].checked) {
					this.selectedEntities.push(key);
				}
			}
		},
		getCmsSettings : function() {
			if(document.forms[this.formId].dest) {
				var dests = document.forms[this.formId].dest;
				for(var i = 0; i < dests.length; i++){
					if(dests[i].checked || dests[i].selected ){
						this.cmsPageId = dests[i].getAttribute('page');
						this.$root.$emit('update-universe-id', dests[i].getAttribute('universe'));
						this.$root.$emit('update-segment-id', dests[i].getAttribute('default_segment'));
						return;
					}
				}
			}
		},
		/**
		 * On ne met a jour que
		 * si une requete n'est pas deja en cours (this.showLoader)
		 * si le user input est different depuis la derniere requete (this.tempInputText)
		 */
		mustBeUpdated : function() {
			if (this.showLoader) {
				return false;
			}

			if (
				this.tempInputText != "" &&
				this.tempInputText == this.inputText
			) {
				return false;
			}

			this.tempInputText = this.inputText;
			return true;
		},
		seeDatalist : function() {
			this.showDatalist = true;
		},
		hideDatalist : function(cooldown = false) {
			if(cooldown) {
				//On attend un peu que le click soit pris en compte
				setTimeout(() => this.showDatalist = false, 100)
			} else {
				this.showDatalist = false
			}
		}
	}
}
</script>

<style scoped>
.vue-js-input {
	margin-bottom : 0 !important;
}
</style>