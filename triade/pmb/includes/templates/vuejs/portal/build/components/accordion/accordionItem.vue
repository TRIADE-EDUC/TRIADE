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
			<select class="portal-accordion-filter" v-model="filter">
				<option value="">{{ $cms.getMessage('filter_all') }}</option>
				<option v-for="(filter, key) in filters" :value.trim="filter.title" :key="key">{{ filter.title }}</option>
			</select>
		</nav>
		<div  v-show="item.active"  
			:class="['portal-accordion-content', item.active ? 'active' : '']">
			<accordion_content 
				v-for="(child, key) in children" 
				:item="child"
				:active="item.active" 
				:key="key"
			></accordion_content>
		</div>
	</div>
</template>

<script>
	import accordion_content from './accordionContent.vue'
	export default {
		props: ['item', 'index'],
		data: function () {
			return {
			    filter: "",
			}    
		},
		components: {
			accordion_content
		},
		computed: {
		    filters: function() {
		        let filters = [];
		        if(this.item.children) {
			        for(const index in this.item.children) {
			            filters.push(this.item.children[index]);
			        }
		        }
		        
		        return filters;
		    },
		    children: function() {
		        var children = this.item.children;
		        return (this.filter == "") ? children : children.filter(child => child.title.trim() == this.filter.trim());
		    }
		},
		methods: {
		    add: function (event) {
		        event.stopPropagation();
		        if (typeof this.item['add'] == 'function') {
		            this.item.add()
		        }
		    }
		}
	}
</script>