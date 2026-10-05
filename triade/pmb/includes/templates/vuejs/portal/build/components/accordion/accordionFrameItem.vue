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
			<input class="portal-accordion-filter" name="search" v-model="search" type="text" :placeholder="$cms.getMessage('frame_filter_placeholder')">
			<select class="portal-accordion-filter" v-model="filter">
				<option value="">{{ $cms.getMessage('filter_all') }}</option>
				<option v-for="(filter, key) in filters" :value.trim="filter" :key="key">{{ filter }}</option>
			</select>
		</nav>
		<div v-show="item.active"
			:class="['portal-accordion-content', item.active ? 'active' : '']" 
			@scroll="hiddenEditClassment">
			<accordion_frame_content 
				v-for="(child, key) in children"
				:item="child"
				:active="item.active" 
				:key="`frame_${key}`"
			></accordion_frame_content>
		</div>
	</div>
</template>

<script>
	import accordion_frame_content from './accordionFrameContent.vue'
	export default {
		props: ['item', 'index'],
		data: function () {
			return {
			    filter: "",
			    search: ""
			}    
		},
		components: {
		    accordion_frame_content
		},
		computed: {
		    filters: function() {
		        let filters = [];
		        
		        if (this.children) {
		            const children = this.item.children.filter(child => child.children.length > 0);
			        for (const index in children) {
			            filters.push(children[index].title);
			        }
			        
					const default_classement = this.$cms.getMessage('default_classement');
			        var classementIndex = filters.findIndex((classement) => {
			            return classement.toLowerCase() == default_classement.toLowerCase();
			        });
			        
			        if (-1 != classementIndex) {
			            filters.splice(classementIndex, 1);
			            filters.splice(0, 0, default_classement);
			        }
		        }
		        
		        if (!filters.includes(this.filter)) {
		            this.filter = "";
		        }
		        
		        return filters;
		    },
		    children: function() {
				const default_classement = this.$cms.getMessage('default_classement');
				
		        var children = this.$cms.cloneObject(this.item.children);
		        var childIndex = children.findIndex((child) => {
		            return child.title.toLowerCase() == default_classement.toLowerCase();
		        });
		        
		        if (-1 != childIndex) {
		            var child = this.$cms.cloneObject(children[childIndex]);
		            children.splice(childIndex, 1);
		            children.splice(0, 0, child);
		        }
		        
		        
		        if (this.filter != "") {
		            children = children.filter(child => child.title.trim() == this.filter.trim());
		        }

		        if (this.search != "") {
		            
		            const regexDefault = new RegExp(this.search.trim(), 'i');
		            const regexID = new RegExp(`_${this.search.trim()}$`, 'i');
		            
		            const DEFAULT_SEARCH = 0;
		            const SEARCH_BY_NODE_ID = 1;
		            const SEARCH_BY_ID = 2;
		            
		            let search;
		            if (this.search.match('cms_module_')) {
		                search = SEARCH_BY_NODE_ID;
		            } else if (!isNaN(this.search)) {
		                search = SEARCH_BY_ID;		                
		            } else {
			            search = DEFAULT_SEARCH;		                
		            }
		            
		            for (var i = 0; i < children.length; i++) {
		                
		                switch (search) {
		                	case SEARCH_BY_NODE_ID: 
			                    children[i].children = children[i].children.filter(child => child.data.item.semantic.id_tag == this.search.trim());
		                		break;
		                	case SEARCH_BY_ID:
			                    const search1 = children[i].children.filter(child => regexID.exec(child.data.item.semantic.id_tag));
		                	    const search2 = children[i].children.filter(child => regexDefault.exec(child.title.trim()))
		                	    children[i].children = [...search1, ...search2]; 
		                		break;
		                	case DEFAULT_SEARCH:
	                	    default:
			                    children[i].children = children[i].children.filter(child => regexDefault.exec(child.title.trim()));
		                		break;
		                }
		            }
		        }
		        return children.filter(child => child.children.length > 0);
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
		    }
		}
	}
</script>