<template>
	<div id="nav">
		<nav class="portal-nav">
			<section class="portal-nav-items">
				<nav_item 
					v-for="(item, key) in items" :key="key" 
					:index="key" 
					:item="item"
					:isActive="$cms.itemNavActive == key"
					@closeSubNav="closeSubNav"
					@changeActive="changeActive($event)">
				</nav_item>
			</section>
			<div class="cache">
	            <div data-dojo-type='dijit/form/Button' data-dojo-props='id:"clean_cache_button_img"'>{{ $cms.clean_cache_img.name }}</div>
	            <div data-dojo-type='dijit/form/Button' data-dojo-props='id:"clean_cache_button"'>{{ $cms.clean_cache.name }}</div>
			</div>
		</nav>
		<sub_nav v-if="itemActive && itemActive.subNav" :item="itemActive"></sub_nav>
	</div>
</template>

<script>
	import nav_item from './navItem.vue';
	import sub_nav from './subNav.vue';
	export default {
		name: "navigation",
		props: ["subItem"],
		components: {
			nav_item,
			sub_nav
		},
	    data: function () {
	        return {
	        }
	    },
	    mounted: function() {
	        this.$nextTick(() => {
	            const parser = dojo.require('dojo.parser');
	            parser.parse();

				var node = document.getElementById('clean_cache_button');
				if (node) {
					node.title = this.formatString(this.$cms.clean_cache.title);
					node.addEventListener('click', () => {
						if (confirm(this.$cms.clean_cache.confirm)) { 
							cms_clean_cache()
							node.title = "";
						}
					});
				}
				
				node = document.getElementById('clean_cache_button_img');
				if (node) {
					node.addEventListener('click', () => {
						if (confirm(this.$cms.clean_cache_img.confirm)) { 
							cms_clean_img_cache()
						}
					});
				}
	        });
	    },
		computed: {
		    items: function() {
			    var items = [];
		        for (let index in this.$cms.itemsNav) {
		            items.push(this.$cms.itemsNav[index]);
		        }
		        return items;
			},
		    itemActive: function() {
		        return this.items[this.$cms.itemNavActive];
			}
		},
		beforeUpdate: function()  {
		    if (this.items.length > 0) {
		        if (this.$cms.itemNavActive == -1) {
		            this.$cms.itemNavActive = 0;
			    }
		        if (!(JSON.stringify(this.$cms.container.component) === JSON.stringify(this.itemActive))) {		            
				    this.$cms.container = this.itemActive;
		        }
		    }
		},
		methods: {
			changeActive: function (index) {
			    if (this.$cms.itemNavActive != index) {
			        this.$cms.itemNavActive = index;
			    }
			    this.$cms.container = this.itemActive;
			},
			closeSubNav: function(index) {
				this.$cms.itemsNav.splice(index, 1);
				if(this.$cms.itemNavActive == index) {
					this.$cms.itemNavActive = index-1;
				}
			},
			formatString : function (encodedStr) {
				var parser = new DOMParser();
				// convertie les "&eacute;" en "é", etc.
				var dom = parser.parseFromString(encodedStr, 'text/html');
				// remplace les multiples espaces en 1 seul
				var str = dom.body.textContent.replace(/(\s){2,}/gm, ' ');
				return str.trim();
			}
		}
	}
</script>