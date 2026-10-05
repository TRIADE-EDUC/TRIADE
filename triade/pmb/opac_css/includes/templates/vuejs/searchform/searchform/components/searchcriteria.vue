<template>
	<!-- Représente un critère (inter + sélecteur de critère -> update du composant enfant criteria_form)  -->
	<div :class="[is_hidden ? 'rmc_row_hidden' : 'rmc_row']">
		<div v-if="!is_hidden" class="rmc_search_criteria">
		<div class="rmc_container rmc_inter_container">
			<label v-if="inc>0" :for="interName" class="visually-hidden">{{ pmb.getMessage('searchform', 'operatorGlobalLabel') }}</label>
			<select class="rmc_inter" :id="interName" v-if="inc>0" v-model="interValue" :name="interName" >
				<option v-for="(operator, key) in inter" :value="key">{{operator}}</option>
			</select>
		</div>
		
		<div class="rmc_container rmc_criteria_container">
			<label :for="'rmc_label_criteria_' + inc" class="visually-hidden">{{ pmb.getMessage('searchform', 'criteriaLabel') }}</label>
			<select name="search[]" :id="'rmc_label_criteria_' + inc" class="rmc_criteria" v-model="dataFieldId">
				<option disabled value="">{{pmb.getMessage("searchform", "criteriaPlaceholder")}}</option>
				<optgroup v-for="(type, name) in criterias" :label="name">
					<option v-for="(criteria, key) in type" :value="key">{{criteria.TITLE}}</option>
				</optgroup>
			</select>
		</div>
		
		<div class="rmc_container rmc_criteria_form_container">
			<template v-if="criteria">
				<criteriaformtext 
					v-if="(criteria.INPUT_TYPE == 'text' || criteria.INPUT_TYPE == 'comment') && !isBetween" 
					:index="inc" 
					:criteria="criteria" 
					:searchData="searchData"
					:showfieldvars="showfieldvars"
					@updateValue="$emit('updateValue', $event)"
					@updateOperator="$emit('updateOperator', $event)" />	
				<criteriaformdate 
					v-if="criteria.INPUT_TYPE == 'date'" 
					:index="inc" 
					:criteria="criteria" 
					:searchData="searchData"
					:showfieldvars="showfieldvars"
					@updateValue="$emit('updateValue', $event)"
					@updateOperator="$emit('updateOperator', $event)" />
				<criteriaformyeardate 
					v-if="criteria.INPUT_TYPE == 'text' && isBetween" 
					:index="inc" 
					:criteria="criteria" 
					:searchData="searchData"
					:showfieldvars="showfieldvars"
					@updateValue="$emit('updateValue', $event)"
					@updateOperator="$emit('updateOperator', $event)" />
				 <criteriaformList v-if="criteria.INPUT_TYPE.includes('list')" 
					:index="inc" 
					:criteria="criteria" 
					:searchData="searchData"
					:showfieldvars="showfieldvars"
					@updateValue="$emit('updateValue', $event)"
					@updateOperator="$emit('updateOperator', $event)" />
				<criteriaformauthority v-if="criteria.INPUT_TYPE == 'authoritie'"
					:index="inc"
					:criteria="criteria"
					:searchData="searchData"
					:showfieldvars='showfieldvars'
					@updateValue="$emit('updateValue', $event)"
					@updateOperator="$emit('updateOperator', $event)"/>
				<!--<criteriaformspecial
					v-if="criteria.INPUT_TYPE == 'SPECIAL'" 
					:index="inc" 
					:criteria="criteria" 
					:searchData="searchData" /> -->
			</template>
			<template v-else>&nbsp;</template>
		</div>
		<input type="button" v-if="isLast" class="bouton_add_criterie_rmc bouton" @click.prevent="$emit('addCriteria')" value="+" :aria-label="pmb.getMessage('searchform', 'addCriteriaLabel')" :title="pmb.getMessage('searchform', 'addCriteriaLabel')">
	</div>
		<template v-else>
			<input type="hidden" :value="searchData.INTER" :name="interName">
            <input type="hidden" v-for="(data, index) in getHiddenData()" :key="index" :value="data.value" :name="data.name">
		</template>
	</div>
</template>

<script>
import criteriaformList from "./criteriaform/criteriaformList.vue";
import criteriaformtext from "./criteriaform/criteriaFormText.vue";
import criteriaformdate from "./criteriaform/criteriaFormDate.vue";
import criteriaformyeardate from "./criteriaform/criteriaFormYearDate.vue";
import criteriaformspecial from "./criteriaform/criteriaFormSpecial.vue";
import criteriaformauthority from "./criteriaform/criteriaFormAuthority.vue";

export default {
	name : 'searchcriteria',
	props : [
	    'criterias', 
	    'inc', 
	    'searchData', 
	    'inter', 
	    'pmb', 
	    'isLast', 
	    'showfieldvars', 
	    'is_hidden',
	    'criterias_hidden',
	    'fieldId',
		'rgaa_active'
    ],
	components : {
		criteriaformList,
		criteriaformtext,
		criteriaformspecial,
		criteriaformdate,
		criteriaformauthority,
		criteriaformyeardate
	},
	data : function(){
		return {
			dataFieldId : this.fieldId,
			interValue : ((this.searchData[this.inc] && this.searchData[this.inc].INTER) ? this.searchData[this.inc].INTER : "and")
		}
	},
	computed : {
        criteria : function(){
            var criteria_list = this.criterias
            if (this.is_hidden) {
                 criteria_list = this.criterias_hidden
            }
            for(let group in criteria_list) {
                for(let field_id in criteria_list[group]) {
                    if (this.dataFieldId == field_id) {
                        return criteria_list[group][field_id];
                    }
                }
            }     
            return '';
        },
		interName : function() {
		    return `inter_${this.inc}_${this.dataFieldId}`;
		},
		isBetween : function(){
			for(let querie of this.criteria.QUERIES) {
                if ("BETWEEN" == querie.OPERATOR) {
                    return true;
                }
            }
            return false;
		}
	},
	methods: {
	    getNameField: function() {
	        return `field_${this.inc}_${this.dataFieldId}[]`;
	    },
	    getNameFieldVar: function(name) {
	        return `field_${this.inc}_${this.dataFieldId}[${name}][]`;
	    },
	    getHiddenData: function(){
			var data = [];
			
			for(let index in this.criteria.VALUES) {
				data.push({value: this.criteria.VALUES[index], name: this.getNameField() })
			}
			
			if (this.showfieldvars && this.criteria.VAR) {
			    /*
			    for(let index in this.criteria.VAR) {
			        var fieldvar = this.criteria.VAR[index];
					data.push({value: fieldvar.value, name: this.getNameFieldVar(fieldvar.NAME) })
				}
			    */
			}
			
			return data;
		}
	}
}
</script>