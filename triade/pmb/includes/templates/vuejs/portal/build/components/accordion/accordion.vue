<template>
	<div class="portal-accordion">
		<component 
			v-for="(item, key) in items"
			:is="getComponentName(item.component)"  
			:item="item" 
			:index="key" 
			:key="`item_${key}`"
			@showItem="showItem($event)"
		></component>
	</div>
</template>

<script>
	import accordionItem from './accordionItem.vue';
	import accordionGabaritItem from './accordionGabaritItem.vue';
	import accordionFrameItem from './accordionFrameItem.vue';
	import accordionVersionItem from './accordionVersionItem.vue';
	
	export default {
		components: {
		    accordionItem,
		    accordionGabaritItem,
		    accordionFrameItem,
		    accordionVersionItem
		},
		data: function () {
		    return {
		        itemActive: 0
		    }
		},
		computed: {
		    items: function () {
		        let items = [];
		        for (let index in this.$cms.itemsAccordion) {
		            let item = this.$cms.itemsAccordion[index];
		            item.children = this.arrayOrderAlphaRecursive(item.children);
		            item.active = this.itemActive == index;
		            items.push(item);
		        }
		        return items;
		    }
		},
		methods: {
		    arrayOrderAlphaRecursive: function (list) {
		        list.sort((a, b) => {
		            var titleA = a.title.toLowerCase();
		            var titleB = b.title.toLowerCase();
		            
		            if (a.children.length > 0) {
		                a.children = this.arrayOrderAlphaRecursive(a.children);
		            }
		            if (b.children.length > 0) {
		                b.children = this.arrayOrderAlphaRecursive(b.children);
		            }
		            
		            if(titleA < titleB) { return -1; }
		            if(titleA > titleB) { return 1; }
		            return 0;
				});
		        return list;
			},
			showItem: function (value) {
			    if (this.itemActive == value) {
			        this.itemActive = -1;
			    } else {
			        this.itemActive = value
			    }
			},
			componentExist: function(name) {
		        return (name && typeof this.$options.components[name] != "undefined");
		    },
		    getComponentName: function(component_name) {
		        return this.componentExist(component_name) ? component_name : 'accordionItem';
		    }
		}
	}
</script>