<template>
	<div :class="['portal-accordion-item', item.active ? 'active' : '']">
		<h3 class="portal-accordion-title cursor-pointer" @click="$emit('showItem', index)">
			{{ item.title }}
			<span class="portal-accordion-icon">
				<i class="portal-accordion-icon-add fa fa-plus-circle"
					@click="add" 
					aria-hidden="true"
					:title="$cms.getMessage('accordion_add')"></i>
				<i @click="$emit('showItem', index)" 
					:class="['fa cursor-pointer', item.active ? 'fa-caret-down' : 'fa-caret-left']" 
					aria-hidden="true"></i>
			</span>
		</h3>
		<nav class="portal-accordion-filters" v-if="item.active">
			<div>
				<input id="radio_classement" name="radio_display_type" type="radio" v-model="$cms.gabaritsDisplayType" value="0"  >
				<label for="radio_classement">{{this.$cms.getMessage('radio_classement')}}</label>
				<input id="radio_tree" name="radio_display_type" type="radio" v-model="$cms.gabaritsDisplayType" value="1">
				<label for="radio_tree">{{this.$cms.getMessage('radio_tree')}}</label>
			</div>
			<div>
				<input id="show_unused_gabarit" name="show_unused_gabarit" type="checkbox" v-model="$cms.showUnusedGabarit" value="1"  >
				<label for="show_unused_gabarit">{{this.$cms.getMessage('show_unused_gabarit')}}</label>
			</div>
			<select class="portal-accordion-filter" v-model="filter" v-if="$cms.gabaritsDisplayType == 0">
				<option value="">{{ $cms.getMessage('filter_all') }}</option>
				<option v-for="(filter, key) in filters" :value.trim="filter" :key="key">{{ filter }}</option>
			</select>
		</nav>
		<div v-show="item.active" 
			:class="['portal-accordion-content', item.active ? 'active' : '']"
			@scroll="hiddenEditClassment">
			<accordion_gabarit_content 
				v-for="(child, key) in children" 
				:item="child"
				:active="item.active" 
				:key="key">
			</accordion_gabarit_content>
		</div>
	</div>
</template>

<script>
	import accordion_gabarit_content from './accordionGabaritContent.vue'
	export default {
		props: ['item', 'index'],
		data: function () {
			return {
			    filter: "",
			    display_type : 0
			}    
		},
		components: {
		    accordion_gabarit_content
		},
		computed: {
		    filters: function() {
		        let filters = [];
		        if(this.item.children) {
			        for(const index in this.item.children) {
			            filters.push(this.item.children[index].title);
			        }
		        }

		        if (!filters.includes(this.filter)) {
		            this.filter = "";
		        }
		        
		        return filters;
		    },
		    children: function() {
		        var children = this.item.children;
		        if (this.$cms.gabaritsDisplayType != 0) {
		            return children;
		        }
		        return (this.filter == "") ? children : children.filter(child => child.title.trim() == this.filter.trim());
		    }
		},
		methods: {
		    add: function (event) {
		        event.stopPropagation();
		        if (typeof this.item['add'] == 'function') {
		            this.item.add()
		        }
		    },
		    hiddenEditClassment: function (event) {
				window.dispatchEvent(new Event("hiddenEditClassment"));
		    },
		    updateDisplay : function() {
		    	this.$cms.formatGabaritsAccordion(this.display_type);
		    }
		},
		watch : {
			display_type : function() {
		    	//this.$cms.gabaritsDisplayType = this.display_type;
				
			}
		}
	}
</script>