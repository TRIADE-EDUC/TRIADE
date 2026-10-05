<template>
	<svg id="svg_history" :class="computedClass" version="1.1"
		xmlns="http://www.w3.org/2000/svg" :viewBox="computedViewBox"
		@mousedown="mouseDown" @mouseup="mouseUp" @mousemove="mouseMove"
		@mouseleave="mouseUp" @click="click">
		
		<routes :svg="svg" :routes="routes" :firstitem="firstItem"></routes>
		<timeline :svg="svg" :pmbmessages="pmbmessages" :firstitem="firstItem"></timeline>
	</svg>
</template>

<script>
	import timeline from './timeLine.vue';
	import routes from './routes.vue';
	
	export default {
		name: "svg_history",
		props: [
			"svg",
			"routes",
			"pmbmessages"
		],
		components: {
			timeline,
			routes
		},
		mounted: function() {
		    
		    // touchstart, touchmove, touchend, touchcancel
		    // Non compatible en @touchstart ...
		    this.$el.addEventListener('touchstart', this.touchStart, false)
		    this.$el.addEventListener('touchmove', this.touchMove, false)
		    this.$el.addEventListener('touchend', this.touchEnd, false)
		    this.$el.addEventListener('touchcancel', this.touchEnd, false)  
			
		    
		    var containerSVG = document.getElementById("nav_history");
			if (containerSVG) {
				this.svg.viewBox.width = containerSVG.clientWidth;
				this.svg.width = containerSVG.clientWidth;
				this.svg.viewBox.height = containerSVG.clientHeight;
				this.svg.height = containerSVG.clientHeight;
			}
		},
		computed: {
		    firstItem: function () {
	            let firstItem = {};
	            
	            if (this.routes && this.routes.length > 0) {
	                firstItem = this.routes[0];
	                for (let i = this.routes.length-1; i > -1; i--) {
	                    if (this.routes[i].timestamp < firstItem.timestamp) {
	                        firstItem = this.routes[i];
	                    }
	                }
	            }
	            
	            return firstItem;
	        },
		    lastItem: function () {
		        let lastItem = this.firstItem;
		        if (this.routes && this.routes.length > 0) {
	                for (let i = 0; i < this.routes.length; i++) {
	                    let last = this.getLastOfItem(this.routes[i]);
	                    if (last && last.timestamp > lastItem.timestamp) {
	                        lastItem = last;
	                    }
	                }
	            } 
	            return lastItem;
	        },
			computedViewBox: function() {
			    
			    // Calcul du zoom
			    this.svg.viewBox.width = this.svg.width/this.svg.scale;
				this.svg.viewBox._height = this.svg.height;
				this.svg.viewBox.height = this.svg.height/this.svg.scale;
				
				// Calcul de la position pour être sur le point le plus récent ou cliquer
				if (this.svg.focus.active && this.svg.focus.idItem && this.svg.focus.item) {
				    
				    let item = this.svg.focus.item;
				    if (!item && this.svg.focus.idItem == this.lastItem.id) {
				        item = this.lastItem
				    }
				    
				    if (typeof item.x != "undefined" && typeof item.y != "undefined") {
				        
						// Calcul position X
					    let x = 0;
					    if (this.svg.focus.itemClicked) {
					        // On a cliqué sur un point on le centre sur le svg
						    x = item.x - (this.svg.viewBox.width/2);
					    } else {
						    x = (item.x - this.svg.viewBox.width) + 200;
					    }
					    
						this.svg.viewBox.x = x;
					    
						// Calcul position Y
						let height = this.svg.viewBox.height;
						let middle = height/2
						this.svg.viewBox.y = (item.y - middle);
						
						// Focus effectuer on le désactive
					    this.svg.focus.active = false;
				    }
				}
				
				if (isNaN(this.svg.viewBox.y)) {
				    this.svg.viewBox.y = 0;
				}
				
				return`${this.svg.viewBox.x}, ${this.svg.viewBox.y}, ${this.svg.viewBox.width}, ${this.svg.viewBox.height}`;
			},
			computedClass: function() {
				if (this.svg.moveMode) {
					return (this.svg.moving) ? "svg-cursor-grabbing" : "svg-cursor-grab";
				}
				return "svg-cursor-auto";
			}
		},
		methods: {
		    touchStart: function (e) {
				if (!this.svg.moving && this.svg.moveMode) {
				    if(e.preventDefault) e.preventDefault();
				    this.mouseDown(e.touches[0])
				}
			},
			touchEnd: function (e) {
				if (this.svg.moving) {
				    if(e.preventDefault) e.preventDefault();
				    this.mouseUp(e)
				}
			},
			touchMove: function (e) {
				if (this.svg.moving && this.svg.moveMode) {
				    if(e.preventDefault) e.preventDefault();
				    this.mouseMove(e.touches[0])
				}
			},
			mouseDown: function (e) {
				if (!this.svg.moving && this.svg.moveMode) {
		            this.hiddenTooltip();
					this.setOrigin(e.clientX, e.clientY)
					this.svg.moving = true;
				}
			},
			mouseMove: function (e) {
				if (this.svg.moving && this.svg.moveMode) {
				    if(e.preventDefault) e.preventDefault();
					this.svg.viewBox.x += ((this.svg.originX - e.clientX)/this.svg.scale);
					this.svg.viewBox.y += ((this.svg.originY - e.clientY)/this.svg.scale);
					this.setOrigin(e.clientX, e.clientY)
				}
			},
			mouseUp: function (e) {
				if (this.svg.moving) {
					this.svg.moving = false;
				}
			},
			click: function (e) {
			    // On veut que les cliques sur le svg
			    if(e.target !== e.currentTarget) return;
				
				if (!this.svg.moveMode && !this.svg.moving) {
		            this.hiddenTooltip();
	            }
			},
			hiddenTooltip: function () {
			    if (this.svg.itemHover.itemId != 0) {
		            this.svg.itemHover.itemId = 0;
	            }
			},
			setOrigin: function (x, y) {
				this.svg.originX = x;
				this.svg.originY = y;
			},
			getLastOfItem: function(item) {
			    var isLast = false;
		        var currentItem = item;
		        var lastItem = item;
		        
		        while (!isLast) {
			        if (currentItem.childs && currentItem.childs.length > 0) {
			        	lastItem = currentItem.childs[currentItem.childs.length-1];
				        if (lastItem.childs && lastItem.childs.length > 0) {
				            currentItem = lastItem;
				        } else {
				            isLast = true;
				        }
			        } else {
			            isLast = true;
			        }
		        }
		        
		        return lastItem
			}
		}
	}
</script>