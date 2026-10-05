<template>
	<nav class="portal-sub-nav">
		<div class="portal-nav-items">
			<nav_item 
				v-for="(item, key) in items" :key="key" 
				:index="key" 
				:item="item"
				:isActive="navActive == key"
				@changeActive="changeActive($event)">
			</nav_item>
		</div>
	</nav>
</template>

<script>
	import nav_item from './navItem.vue';
	export default {
		name: "sous_navigation",
		props: ["item"],
		components: {
			nav_item
		},
	    data: function () {
	        return {
	        	navActive: ""
	        }
	    },
	    created: function() {
	    	this.navActive = 0;
	    },
		computed: {
		    items: function() {
			    var items = [];
		        for (let item of this.item.subNav) {
		            items.push(item);
		        }
		        return items;
			},
		    itemActive: function() {
		        return this.items[this.navActive];
			},
			
		},
		beforeUpdate: function()  {
		    if (this.items.length > 0) {
		        if (this.navActive == -1) {
		            this.navActive  = 0;
			    }
		        if (!(JSON.stringify(this.$cms.container.component) === JSON.stringify(this.itemActive))) {		            
				    this.$cms.container = this.itemActive;
		        }
		    }
		},
		methods: {
			changeActive: function (index) {
			    if (this.navActive != index) {
			    	this.navActive = index;
			    }
			    this.$cms.container = this.itemActive;
			}
		}
	}
</script>