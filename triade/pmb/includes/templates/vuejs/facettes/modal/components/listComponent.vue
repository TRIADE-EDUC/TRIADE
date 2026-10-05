<template>
	<div class="modal-content-list">
		<h3 class="modal-title">{{letter}}</h3>
		<fieldset id="modal-list-container" class="modal-list">
			<legend class="visually-hidden">{{ getMessage('facettes_modal_legend') }}</legend>
			<div class="modal-item" v-for="(facette, index) in facettes" :key="index">
				<label :for="`facette-modal-${letter}-${facette.facette_code_champ}-${index}`"  class="visually-hidden">{{ facette.facette_libelle }}</label>
				<input :id="`facette-modal-${letter}-${facette.facette_code_champ}-${index}`" type="checkbox" v-model="selectedFacettes"
					:value="facette.facette_value" @change="check"
					class="modal-item-checkbox" />
				<a href="#" :aria-label="getMessage('facettes_modal_trigger_filter_aria_label')" @click.prevent="startFacette(facette);" v-html="`${facette.facette_libelle} (${facette.facette_number})`"></a>
			</div>
		</fieldset>
	</div>
</template>

<script>
export default {
	name : "list",
	props : ['facettes', 'letter'],
	data : function() {
		return {
			selectedFacettes : []
		}
	},
	mounted: function() {
		window.addEventListener("close_modal", this.reset.bind(this));
	},
	destroy: function() {
		window.removeEventListener("close_modal", this.reset.bind(this));
	},
	methods : {
	    reset: function() {
			this.selectedFacettes = [];
	    },
		check : function() {
			this.$emit('check', this.selectedFacettes);
		},
		startFacette : function(facette) {
			if(facette.facette_link != "") {
				this.showLoader();
				try {
					//gestion des cas ou la chaine inclut le document.location
					if(facette.facette_link.match(".location")){
						eval(facette.facette_link);
					} else {
						window.location = facette.facette_link;
					}
				} catch(e) {
					console.error(e);
					this.hiddenLoader();
				}
			} else {
				this.$emit("facetteclicked", facette.facette_value);
			}
		}
	}
}
</script>