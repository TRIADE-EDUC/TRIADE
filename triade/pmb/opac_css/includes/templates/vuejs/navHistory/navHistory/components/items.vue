<template>
	<g class="items">
		<template v-for="(childItem, index) in item.childs">
			<g class="childrens" :key="index">
				<links :item="item" :child="childItem"></links>
				<items :svg="svg" :item="childItem" :parent="item" :parity="parity ? 0 : 1"></items>
			</g>
		</template>
		<g class="item" @click="click" :data-id_item="item.id">
			<template v-if="item.picto">
				<rect :x="item.x-(image.size/2)" :y="item.y-(image.size/2)" :width="image.size" :height="image.size" :class="isActive"/>
				<image :width="image.size" :height="image.size" :x="item.x-(image.size/2)" :y="item.y-(image.size/2)" :href="item.picto"></image>
			</template>
			<circle v-else :cx="item.x" :cy="item.y" :r="cercle.r" :class="isActive"></circle>
			<text :transform="positionText" class="text_background" :font-size="fontSize" :stroke-width="strokeWidthBackground">{{ title }}</text>
			<text :transform="positionText" class="text" :font-size="fontSize" :stroke-width="strokeWidth">{{ title }}</text>
		</g>
		<g v-if="canShowBtn" class="btn-link" @click="moveToParent()" :transform="`translate(${item.x-(cercle.r*2.5)}, ${item.y+cercle.r}) scale(${btnParent.scale})`">
			<rect x="0" y="0" width="16" height="16"/>
			<path d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
		</g>
	</g>
</template>

<script>
	import links from './links.vue';
	
	export default {
		name: "items",
		props: {
		    svg: {
		        type: Object,
		        required: true
		    },
		    item: {
		        type: Object,
		        required: true
		    },
		    parent: {
		        type: Object,
		        required: false
		    },
		    parity: {
		        type: Number,
		    	default: 0,
		        required: false
		    }
		},
	    data: function () {
	        return {
	            btnParent: {
	              scale: 1.6
	            },
	            clickData: {
	            	count: 0,
	            	timer: null,
	            	delay: 300
	            },
	            cercle: {
	                r: 15
	            },
	            text: {
                    deg: 18,
                    bold: 1,
                    size: 17,
                    maxSize: 25,
                    minSize: 13,
                    maxLength: 30
	            },
	            image: {
                    size: 30
	            },
	        }
	    },
		components: {
		    links,
		},
		mounted() {
			this.$nextTick(this.checkFocusItem())
		},
		updated() {
			this.$nextTick(this.checkFocusItem())
		},
		computed: {
	        strokeWidth: function () {
	            return (this.svg.scale > 0) ? this.text.bold/this.svg.scale : this.text.bold*this.svg.scale;
	        },
	        strokeWidthBackground: function () {
	            return this.strokeWidth+3;
	        },
	        fontSize: function () {
	            let size = (this.svg.scale > 0) ? this.text.size/this.svg.scale : this.text.size*this.svg.scale;
	            if (size < this.text.minSize) {
	                return this.text.minSize;
	            } else if (size > this.text.maxSize) {
	                return this.text.maxSize;
	            } else {
	                return size;
	            }
	        },
	        title: function () {
	            
				// On prend sous type de page par défault
                let title = this.item.sub_page;
	            
	            if (
                    (this.item.entity_id || this.item.segment_id || this.item.universe_id || this.item.lvl == "cmspage") &&
                    this.item.title
                   ) {
                	title = this.item.title;
	            }
				
				// Aucun titre on prend le type de page
	            if (!title && this.item.page) {
                	title = this.item.page;
	            }
				
				if (title.length > this.text.maxLength) {
				    title = title.slice(0, this.text.maxLength);
				    title += "...";
				}
	            
                return title;
	        },
	        textDeg: function () {
	            return this.parity ? this.text.deg : -this.text.deg;
	        },
	        textY: function () {
	            return this.parity ? (this.item.y + (this.cercle.r/2 + this.fontSize)) : (this.item.y - ( this.cercle.r + this.strokeWidthBackground));
	        },
	        textX: function () {
	            return this.parity ? (this.item.x - (this.cercle.r/2)) : (this.item.x);
	        },
		    canShowBtn: function () {
	            return (this.parent && this.item.parent != 0 && this.parent.x <= this.svg.viewBox.x);
	        },
	        isActive: function () {
			    return (this.item.id == this.svg.focus.idItem) ? 'is_active' : '';
	        },
		    positionText: function () {
	        	return `translate(${this.textX}, ${this.textY}) rotate(${this.textDeg})`;  
	        }
		},
		methods: {
	        checkFocusItem: function () {
	            if (this.item.id == this.svg.focus.idItem) {
			        // Point le plus récent
			        this.svg.focus.item = this.item;
			        this.svg.focus.recentItem = this.item;
				}
	        },
	        moveToParent: function () {
	            if (this.canShowBtn) {
		            this.svg.viewBox.x = this.parent.x-200
					this.svg.viewBox.y = (this.parent.y - (this.svg.viewBox.height/2));
	            }
	        },
	        click: function (e) {
	            this.clickData.count++;
	            
	            if (this.clickData.count == 1) {	                
					this.clickData.timer = setTimeout( () => {
					    this.showTooltip(e);
					    this.clickData.count = 0
				    }, this.clickData.delay);
	            } else {
	                clearTimeout(this.clickData.timer);
	                this.focusItem();
				    this.clickData.count = 0
	            }
	        },
	        focusItem: function () {
	            if (this.svg.moveOptions.enableFocus || (!this.svg.moveMode && !this.svg.moving)) {
					// On masque le tooltip de l'item
		            this.svg.itemHover.itemId = 0;
				
		            if (this.svg.focus.idItem != this.item.id) {
						// Focus sur l'item cliqué
						this.svg.focus.itemClicked = true;
						this.svg.focus.active = true;
						this.svg.focus.idItem = this.item.id;
					    this.svg.focus.item = this.item;
		            } else {
						// Reset du focus
						this.svg.focus.itemClicked = false;
						this.svg.focus.active = false;
						
						let recentNavigationTmp = sessionStorage.getItem("recentNavigation");
						if (recentNavigationTmp) {
							let recentNavigation = JSON.parse(recentNavigationTmp);
							if (recentNavigation && recentNavigation[$navHistoryData.opacView]) {
								let recentItem = recentNavigation[$navHistoryData.opacView];
								if (recentItem) {
									// Focus le point le plus récent
									this.svg.focus.active = true;
									this.svg.focus.idItem = recentItem;
								    this.svg.focus.item = this.svg.focus.recentItem;
								}
							}
						}
		            }
				}
	        },
	        showTooltip: function (e) {
				if (this.svg.moveOptions.enableTooltip || (!this.svg.moveMode && !this.svg.moving)) {
		            if (this.svg.itemHover.itemId != this.item.id) {
			            this.svg.itemHover.y = e.clientY+5;
			            this.svg.itemHover.x = e.clientX+5;
			            this.svg.itemHover.itemId = this.item.id;
		            }
	            }
	        }
		}
	}
</script>