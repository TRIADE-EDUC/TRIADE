<template>
	<!-- Composant root du formulaire RMC -->
	<form class="form_criteria_rmc" name="search_form" :action="form_url" method="post" @submit="$root.$emit('beforeSubmit')">
		<template v-for="(field_id, i) in search.SEARCH">
			<searchcriteria 
				:searchData="search"
				:fieldId="field_id"
				:inc="i" 
				:inter="inter"
				:criterias="criterias"
				:criterias_hidden="criterias_hidden"
				:intervalues="intervalues"
				:showfieldvars='showfieldvars'
				:pmb="pmb"
				:isLast="isLast(i)"
				:is_hidden="isHidden(field_id)"
				@addCriteria="addCriteria"
				@updateValue="$set(searchValues, i ,$event)"
				@updateOperator="$set(searchOperators, i, $event)" />
		</template>
		<input v-for="(value, name) in hidden" type="hidden" :name="name" :id="name" :value="value" />
		<div class="rmc_row">
			<input id="search_form_submit" class="bouton" type="submit" :value="submitLabel"  @click="sendSearch()"/>
			<a v-if="rgaa_active" href="#"
				class="bouton button_search_help"
				onclick="window.open('./help.php?whatis=search_multi', 'search_help', 'scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes'); return false" >
				{{ pmb.getMessage('searchform', 'search_help')}}<span class='visually-hidden'>{{ pmb.getMessage('common', 'rgaa_window_open')}}</span>
			</a>
			<input v-else id="search_help_rmc_responsive" class="bouton button_search_help" type="button" 
				onclick="window.open('./help.php?whatis=search_multi', 'search_help', 'scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes'); return false" 
				:value="pmb.getMessage('searchform', 'search_help')"
			/>
			
		</div>
	</form>
</template>

<script>
import searchcriteria from "./searchcriteria.vue";

export default {
	name: "searchform",
	props : [
	    'criterias', 
	    'search', 
	    'inter', 
	    'intervalues', 
	    'hidden', 
	    'form_url', 
	    'images', 
	    'pmb', 
	    'defaultinter', 
	    'showfieldvars',
	    'criterias_hidden',
		'rgaa_active',
		'persopac'
    ],
	components : {
		searchcriteria : searchcriteria
	},
	data : function(){
		return {
			searchValues : [],
			searchOperators : []
		}
	},
	created : function(){
		if(this.search){
			for(let i=0; i<this.search.length; i++){
				for(let type in this.criterias){
					for(let criteriaId in this.criterias[type]){
						if(this.search[i] == criteriaId){
							this.searchOperators[i] = this.criterias[type][criteriaId].OPERATOR;
						}
					}
				}
			}
		}
		
		// Si search[SEARCH] est vide, cad qu'on a pas de recherche, on rempli avec tous les criteres de dispo (3 par defaut)
		if(this.search.SEARCH.length == 0){
			var j = 0;
			for(let type in this.criterias){
				for(let criteriaId in this.criterias[type]){
					if(j < 3){
						this.search.SEARCH.push(criteriaId);
						j++;
					}
					
				}
			}
		}
	},
	computed: {
		lastVisibleCriteria: function () {
			for (var i = this.search.SEARCH.length-1; i >= 0; i--) {
			    if (!this.criterias_hidden.length) {
			        return i;
			    } else if (this.criterias_hidden && !this.criterias_hidden['fields'].includes(this.search.SEARCH[i])) {
			        return i;
			    }
			}
			return 0;
		},
		submitLabel: function() {
			if(this.persopac && this.persopac.buttonlabel) {
				return this.persopac.buttonlabel;
			}
			return this.pmb.getMessage('searchform', 'sendSearch');
		}
	},
	methods :{
	    isHidden: function (searchCriteria) {
	        if (this.criterias_hidden.length > 0) {
		        return (this.criterias_hidden['fields'].includes(searchCriteria));
	        }
	        return false;
		},
	    isLast: function (index) {
	        return (index == this.lastVisibleCriteria);
		},
		addCriteria : function(){
			for(let criteriaGroup in this.criterias){
				for(let criteriaKey in this.criterias[criteriaGroup]){
					this.search.SEARCH.push(criteriaKey);
					return;
				}
			}
		},
		getCriteria : function(criteriaId) {
			for(let criteriaGroup in this.criterias){
				for(let criteriaKey in this.criterias[criteriaGroup]){
					if(criteriaKey == criteriaId){
						return this.criterias[criteriaGroup][criteriaId];
					}
				}
			}
		},
		sendSearch: function() {
			if(typeof valid_form_extented_search == "function") {
				valid_form_extented_search();
			}
			return true;
		}
	}
}
</script>