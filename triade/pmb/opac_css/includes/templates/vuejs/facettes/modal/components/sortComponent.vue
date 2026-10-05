<template>
	<div class="modal-sort">
		<ol class="modal-filters">
			<li v-for="(letter, index) in letters" :key="index">
				<button type='button' class="modal-filter" :class='letterIsActive(letter)'
					:aria-pressed="letterIsActive(letter) ? 'true' : 'false'"
					aria-controls='modal-list-container'
					@click="feedLetter(letter)"
					:disabled="checkLetterValidity(letter)">
					{{letter}}
				</button>
			</li>
		</ol>
		<hr class="modal-separator">
		<list :facettes="formattedFacettes[getLetter]"
			:letter="getLetter" @check="$emit('check', $event)" @facetteclicked="$emit('facetteclicked', $event)" ></list>
	</div>
</template>

<script>
import list from "./listComponent.vue";

export default {
	name : "sort",
	props : ['facettes'],
	components : {
		list
	},
	data : function() {
		return {
			numericValue : "",
			letters : [],
			selectedLetter : ""
		}
	},
	created : function() {
		this.numericValue = this.getMessage('numeric_facettes');
		this.letters = this.getMessage('alphabet').trim().split(',');
		this.$set(this.letters, this.letters.length, this.numericValue);
	},
	mounted: function() {
		window.addEventListener("close_modal", this.reset.bind(this));
	},
	destroy: function() {
		window.removeEventListener("close_modal", this.reset.bind(this));
	},
	computed: {
	    formattedFacettes : function() {
	        var formattedFacettes = {};
			for (let i in this.letters) {
				let filteredLetters = this.facettes.filter((facette) => {
					const firstRaw = facette.facette_libelle.trim().toLowerCase()[0];
				    const first = firstRaw.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
				    
					if(isNaN(first) === false && this.letters[i] == this.numericValue){
						return true;
					}
					return first == this.letters[i].toLowerCase();
				});
				formattedFacettes[this.letters[i]] = filteredLetters.sort((a, b) => {
				    if (a.facette_libelle.toLowerCase() == b.facette_libelle.toLowerCase()) {
				        return 0;
				    }
				    return a.facette_libelle.toLowerCase() > b.facette_libelle.toLowerCase() ? 1 : -1;
			    });
			}
			return formattedFacettes;
		},
		getLetter : function() {
			if(this.selectedLetter != ""){
				return this.selectedLetter;
			}
			for(let letter of this.letters) {
				if(this.formattedFacettes[letter].length > 0) {
					return letter;
				}
			}
		}
	},
	methods : {
	    reset: function() {
			this.selectedLetter = "";
	    },
		feedLetter : function(letter) {
			if(this.formattedFacettes[letter] && this.formattedFacettes[letter].length > 0){
				this.selectedLetter = letter;
			}
		},
		letterIsActive : function(letter) {
			var result = '';
			if(this.getLetter == letter) {
				result = "active";
			}		
			return result;
		},
		checkLetterValidity: function(letter) {
			if(this.formattedFacettes[letter] && this.formattedFacettes[letter].length == 0) {
				return true;
			}
			return false;
		}
		
	}
}
</script>