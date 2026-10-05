<template>
    <div id="controls" class="groups_bouton" v-if="svg.accesControls">
    	<div class="left">
	        <button id="advance_mode" :class="[svg.advanceMode ? 'active' : '', 'bouton']" @click="advanceMode">{{ pmbmessages.getMessage('nav_history', 'nav_history_advance_mode') }}</button>
	        <!-- <button id="move_mode" :class="[svg.moveMode ? 'move-mode-up' : 'move-mode-down', 'bouton']" @click="moveMode" :title="pmbmessages.getMessage('nav_history', 'nav_history_move')">
	        	<svg id="move_icon" width="16" height="16" viewBox="0 0 8.4666665 8.4666669" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg">
	        		<g transform="translate(0 -288.533)">
        				<path d="m4.2206235 289.05563a.26460982.26460982 0 0 0 -.2051555.10335.26460982.26460982 0 0 0 -.0062.008l-1.0288778 1.02939a.26460982.26460982 0 1 0 .3731037.3731l.6071989-.60719v2.53679h-2.5352476l.6056478-.60513a.26460982.26460982 0 0 0 -.1917196-.45527.26460982.26460982 0 0 0 -.1834513.0801l-1.03301195 1.03508a.26460982.26460982 0 0 0 -.007752.006.26460982.26460982 0 0 0 .005684.41444.26460982.26460982 0 0 0 .007752.006l1.02732755 1.0294a.26460982.26460982 0 1 0 .3751709-.37311l-.6077149-.60771h2.537313v2.53731l-.6071981-.6072a.26460982.26460982 0 1 0 -.3731037.37466l1.0407634 1.04128.017571.0176a.26460982.26460982 0 0 0 .3963583-.0238l1.0355956-1.03508a.26460982.26460982 0 1 0 -.3731038-.37466l-.6077159.60511v-2.53524h2.5373129l-.6077148.60771a.26460982.26460982 0 1 0 .3731037.37311l1.0216431-1.02371a.26460982.26460982 0 0 0 .00775-.006.26460982.26460982 0 0 0 .00775-.42581.26460982.26460982 0 0 0 -.00568-.004l-1.0314599-1.03162a.26460982.26460982 0 1 0 -.3731038.37518l.6056476.60513h-2.5352486v-2.53525l.6077149.60565a.26460982.26460982 0 1 0 .3731037-.37311l-1.0355955-1.03508-.00568-.008a.26460982.26460982 0 0 0 -.2087726-.0977z"/>
        			</g>
        		</svg>
	        </button> -->
	    	<template v-if="svg.advanceMode">
			    <button class="bouton" id="zoom_plus" @click="zoomPlus" :disabled="disabledZoomPlus">{{ pmbmessages.getMessage('nav_history', 'nav_history_zoom_more') }}</button>
			    <button class="bouton" id="zoom_moin" @click="zoomMoins" :disabled="disabledZoomMoins">{{ pmbmessages.getMessage('nav_history', 'nav_history_zoom_minus') }}</button>
			    <button class="bouton" id="recenter" @click="recenter">{{ pmbmessages.getMessage('nav_history', 'nav_history_refocus') }}</button>
			    <div class="bookmarks-controls">
				    <button id="bookmarks" :class="[!hiddenBookmarks ? 'active' : '', 'bouton']" @click="showBookmarks" :disabled="disabledBookmarks()">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
				    		<path class="favoris active" d="M 0 5.87 L 6.13 5.87 L 8 0 L 9.87 5.87 L 16 5.87 L 11.12 9.8 L 13.05 16 L 8 12.16 L 2.95 16 L 4.88 9.8 Z"/>
				    	</svg>
				    	{{ pmbmessages.getMessage('nav_history', 'nav_history_bookmarks') }}
				    </button>
			    	<div class="bookmarks" v-if="!hiddenBookmarks">
			    		<ul class="bookmarks_list">
			    			<li class="bookmark" v-for="bookmark in svg.bookmarksList" @click.stop="moveTo(bookmark)">
			    				{{ bookmark.title }} 
			    				<span class="remove-bookmark" @click.stop="removeBookmark(bookmark)">
			    					{{ pmbmessages.getMessage('nav_history', 'nav_history_bookmark_remove') }}
		    					</span>
		    				</li>
			    		</ul>
			    	</div>
			    </div>
	    	</template>
    	</div>
    	<div class="right" v-if="svg.advanceMode">
		    <button class="bouton" @click="goToLeft" :disabled="!hasPreviousRoute">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
				  <path d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
				</svg>
			</button>
		    <button class="bouton" @click="goToRight" :disabled="!hasNextRoute">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
				  <path d="M1.5 1.5A.5.5 0 0 0 1 2v4.8a2.5 2.5 0 0 0 2.5 2.5h9.793l-3.347 3.346a.5.5 0 0 0 .708.708l4.2-4.2a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 8.3H3.5A1.5 1.5 0 0 1 2 6.8V2a.5.5 0 0 0-.5-.5z"/>
				</svg>
			</button>
    	</div>
	    <div class="right-bottom">
	    	<img :class="['bouton-resize', expand ? 'cursor-grabbing' : '']" 
	    		:src="img_expand_arrows" 
	    		:alt="pmbmessages.getMessage('nav_history', 'nav_history_search_universe')" 
	    		:title="pmbmessages.getMessage('nav_history', 'nav_history_search_universe')"
	    		@mousedown="mouseDown" 
    		/>
	    </div>
    </div>
</template>

<script>
	export default {
		name: "controls",
		props : [
		    "svg",
		    "routes",
		    "pmbmessages",
		    "navhistory",
		    "img_expand_arrows"
	    ],
	    data: function () {
	        return {
	            scale: 1.5,
	            zoomMin: 0.00009,
	            zoomMax: 437.90,
	            hiddenBookmarks: true,
	            expand: false,
	            originPosition: {
	                y: 0,
	                height: 0
	            }
	        }
	    },
	    mounted: function () {
			window.addEventListener('mouseup', this.mouseUp)
			window.addEventListener('mousemove', this.mouseMove)  
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
	        disabledZoomMoins: function () {
	            return (this.svg.scale == this.zoomMin) ? true : false;
	        },
	        disabledZoomPlus: function () {
	            return (this.svg.scale == this.zoomMax) ? true : false;
	        },
	        hasNextRoute: function () {
	            return (this.nextRoute && this.nextRoute.id) ? true : false;
	        },
	        hasPreviousRoute: function () {
	            return (this.previousRoute && this.previousRoute.id) ? true : false;
	        },
	        nextRoute: function () {
	            let nextRoute = {};
		        for (let i = this.routes.length-1; i > -1; i--) {
					let route = this.routes[i];
					if (route.x > this.svg.viewBox.x && (route.x - this.svg.viewBox.x) >= 1000) {
					    nextRoute = route;
						break;
					}
				}
		        return nextRoute;
	        },
	        previousRoute: function () {
	            let previousRoute = {};
			    for (let i = 0; i < this.routes.length; i++) {
					let route = this.routes[i];
					if (route.x < this.svg.viewBox.x && (this.svg.viewBox.x - route.x) >= 100) {
					    previousRoute = route;
						break;
					}
				}
		        return previousRoute;
	        }
	    },
		methods: {
			hiddenTooltip: function () {
				// On masque le tooltip de l'item
	            this.svg.itemHover.itemId = 0;
			},
	        disabledBookmarks: function () {
	            if (Object.values(this.svg.bookmarksList).length <= 0) {
	                this.hiddenBookmarks = true;
		            return true;
	            }
	            return false;
	        },
		    goToRight: function () {
		        if (this.hasNextRoute === true) {
					this.moveToItem(this.nextRoute);
			    }
			},
			goToLeft: function () {
			    if (this.hasPreviousRoute === true) {
					this.moveToItem(this.previousRoute);
			    }
			},
			moveToItem: function (item) {
			 	
				this.svg.moving = false;
			 	this.svg.load = true;
			 	
				let height = this.svg.viewBox.height;
				let middle = height/2
			    let y = (item.y - middle);
				
				let x = item.x - (this.svg.viewBox.width/2);

				this.svg.viewBox.x = x;
				this.svg.viewBox.y = y;

			 	this.svg.load = false;
			},
			moveMode: function () {
			    this.hiddenTooltip();
			    this.svg.moveMode = !this.svg.moveMode;
			},
			recenter: function () {
			    this.hiddenTooltip();
				// Reset des positions / zoom
				this.svg.viewBox.x = 0;
				this.svg.viewBox.y = 0;
			    this.svg.scale = this.svg.defaultScale;
			    
			    // On active le focus
				this.svg.focus.active = true;
			},
			zoomPlus: function () {
			    this.hiddenTooltip();
			    let zoom = this.svg.scale*this.scale;
			    if (zoom > this.zoomMax) {
			        zoom = this.zoomMax;
			    }
			    this.svg.scale = zoom;
			},
			zoomMoins: function () {
			    this.hiddenTooltip();
			    let zoom = this.svg.scale/this.scale;
			    if (zoom < this.zoomMin) {
			        zoom = this.zoomMin;
			    }
			    this.svg.scale = zoom;
			},
			advanceMode: function () {
			    
			    this.svg.advanceMode = !this.svg.advanceMode;
			    if (!this.navhistory.init) {
				    this.$parent.getAllRoute();
			    } else {
			        this.recenter();
			    }
		        
		     	/*
		     	// On désactive le déplacement
			    if (this.svg.moveMode) {
			        this.moveMode();
			    }
		     	*/
			},
			showBookmarks: function () {
			    this.hiddenBookmarks = !this.hiddenBookmarks;
			},
	        removeBookmark: function(bookmark) {
	            this.$parent.removeBookmark(bookmark);
	        },
	        moveTo: function(bookmark) {
	            let x = this.computedXFromTime(bookmark.time);
	            this.svg.viewBox.x = x;
	        },
	        computedXFromTime: function (time) {
	            if (this.firstItem) {
	    	     	// Calcule du point x (1 min = 100 px)
					let minutes = ((time - this.firstItem.timestamp) / 1000) / 60;
					let posX = (minutes * 100);
	    	     	// On fait en sorte de centrer la position
					return posX - (this.svg.viewBox.width/2);
	            }
	        },
	        mouseDown: function(e) {
	            e.preventDefault()
	            this.expand = true;
	            document.body.classList.add("cursor-grabbing");
	            this.$root.$el.classList.remove("transition")
	            this.originPosition.y = e.clientY;
	            this.originPosition.height = this.svg.height;
	            
	        },
	        mouseUp: function(e) {
	            e.preventDefault();
	            document.body.classList.remove("cursor-grabbing");
	            this.$root.$el.classList.add("transition")
	            this.expand = false;
	            this.originPosition.y = 0;
	            this.originPosition.height = 0;
	        },
	        mouseMove: function(e) {
	            if (this.expand && e) {
	                e.preventDefault()
	                
        			const height = this.originPosition.height + (e.clientY - this.originPosition.y)
        			const diff = (height - this.navhistory.height);
	                const new_height = this.navhistory.height + diff;
	                
	                if (this.svg.advanceMode && (new_height-this.navhistory.margin) > (this.navhistory.defaultHeight*2)) {
		                this.navhistory.height = new_height;
	                } else if (!this.svg.advanceMode && new_height >= this.navhistory.defaultHeight) {
		                this.navhistory.height = new_height;
	                }
	            }
	        },
		}
    }
</script>
