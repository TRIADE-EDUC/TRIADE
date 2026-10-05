import Vue from "vue";
import svgHistory from "./components/svgHistory.vue";
import controls from "./components/controls.vue";
import tooltips from "./components/tooltips.vue";

window.addEventListener("load", ()=>{
	new Vue({
		el: "#nav_history",
		data: {
			pmbMessages: pmbDojo.messages,
			img_expand_arrows: $navHistoryData.img_expand_arrows,
			svg: {
	            bookmarksList: {},
				originX: 0,
				originY: 0,
				width: 0,
				height: 0,
				viewBox: {
					x: 0,
					y: 0,
					width: 0,
					height: 0,
					_height: 0
				},
				scale: 0.6,
				defaultScale: 0.6,
				moving: false, // Indique si on ce deplace on non
				moveMode: true, // Mode "deplacement"
				moveOptions: {
					enableTooltip: true, // Autoriser l'affichage du tooltip en deplacement
					enableFocus: true, // Autoriser le centrage sur un noeud en deplacement
					enableBookmarks: true // Autoriser les favoris en deplacement
				},
				focus: {
					itemClicked: false,
					active: false,
					idItem: 0,
					item: {},
			    	recentItem: {} // item récent
				},
				itemHover: {
					y: 0,
					x: 0,
					itemId: 0
				},
				load: true,
				advanceMode: false,
				accesControls: true
			},
			navHistory: {
				init: false,
				height: 100,
				defaultHeight: 100,
				margin: 10,
			},
			routes: []
		},
		components: {
			svghistory: svgHistory,
			controls: controls,
			tooltips: tooltips
		},
		created: function() {
			let lastItem = sessionStorage.getItem("lastItem");
			let opacView = sessionStorage.getItem("opacView");
			
			if ($navHistoryData.no_data) {
				 // Aucune donnée dans la session
				lastItem = false;
				this.resetData();
			}
			
			if (opacView && $navHistoryData.opacView && opacView != $navHistoryData.opacView) {
				// On vient de changer de vue on reset tout
				sessionStorage.removeItem('lastItem');
				sessionStorage.removeItem('route');
				
				// On défnis la nouvelle vue
				sessionStorage.setItem("opacView", $navHistoryData.opacView);
			} else if (!opacView) {
				sessionStorage.setItem("opacView", $navHistoryData.opacView);
			}
			
			if (lastItem) {
				lastItem = JSON.parse(lastItem);
				if (lastItem && $navHistoryData && lastItem.idEmpr != $navHistoryData.idEmpr) {
					// On vient de se connecter/déconnecter
					this.resetData();
				}
			}
		},
		mounted: function() {
			// Si on sort du container on masque le tooltips
			this.$el.addEventListener("mouseleave", () => {
				if (this.svg.itemHover.itemId != 0) {
					this.svg.itemHover.itemId = 0;
				}
			})
			this.initSessionStorage();
			this.getAllBookmarks();
		},
		
		computed: {
			advanceModeStyle: function () {
				let height = this.navHistory.height;
				
				if (this.svg.advanceMode && (
					height == this.navHistory.defaultHeight ||
					(height-this.navHistory.margin) < (this.navHistory.defaultHeight*2))
				) {
					height *= 2;
				}
				this.svg.viewBox.height = height;
				this.svg.height = height;
				
				return `height: ${height}px;`;
			}
		},
		methods: {
			resetData: function () {
				$navHistoryData.navId = 0;
				sessionStorage.removeItem('lastItem');
				sessionStorage.removeItem('recentNavigation');
				sessionStorage.removeItem('route');
			},
			getAllRoute: function () {
				
				// this.navHistory.init permet de savoir si on a déjà fait la requête
				if (this.navHistory.init) {
					return;
				}
				
				// On désactive le déplacement
				this.svg.moving = false;
				// On active l'overlay
				this.svg.load = true;
				
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = () => {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						let routes = JSON.parse(xhttp.response);
						if (routes) {
							this.routes = routes;
							// reset du focus
				        	this.svg.focus.item = {};
				        	this.svg.focus.recentItem = {};
							this.focusOnRecentItem();
							this.navHistory.init = true;
						} else {
							console.error(typeof routes);
						}
						this.svg.load = false;
					}
				};
				xhttp.open("GET", "./ajax.php?module=ajax&categ=session&action=get_all_nav_history", true);
				xhttp.withCredentials = true;
				xhttp.send();
			},
			isRefresh: function() {
				let lastItem = sessionStorage.getItem("lastItem");
	
				if (lastItem) {
					lastItem = JSON.parse(lastItem);
					// Si on sort du container on masque le tooltips
					if (lastItem.link == encodeURIComponent(window.location.href)) {
						return true;
					}
				}
				return false;
			},
			initSessionStorage: function() {
				
				if (this.isRefresh() && !($navHistoryData && $navHistoryData.navId && $navHistoryData.navId != 0)) {
					// On a refresh la page on ne crée pas de nouvel item
					this.getNavHistoryData();
				} else {
	
					let id = 0;
					let parentId = 0;
					let timestamp = 0;
					let item = {};
					let diversion = false; // Permet de savoir si on fait une dérivation
					let recentNavigation = {}; // Navigation récente
	
					if ($navHistoryData && $navHistoryData.navId && $navHistoryData.navId != 0) {
						// On vient d'ouvrir l'url d'un ancien point
						id = $navHistoryData.navId;
						timestamp = $navHistoryData.navId;
						diversion = true;
					} else {
						let recentNavigationTmp = sessionStorage.getItem("recentNavigation");
						if (recentNavigationTmp) {
							recentNavigation = JSON.parse(recentNavigationTmp);
							if (recentNavigation && recentNavigation[$navHistoryData.opacView]) {
								parentId = recentNavigation[$navHistoryData.opacView];
							}
						}
	
						timestamp = Date.now();
						id = timestamp.toString();
						if (!parentId) {
							parentId = 0;
						}
					}
	
					// On définit les valeurs
					item.id = id;
					item.date = timestamp;
					item.title = document.title;
					item.link = encodeURIComponent(window.location.href);
					item.parent = parentId;
					item.idEmpr = $navHistoryData.idEmpr ?? null;
					
					recentNavigation[$navHistoryData.opacView] = id;
					sessionStorage.setItem("recentNavigation", JSON.stringify(recentNavigation));
					sessionStorage.setItem("lastItem", JSON.stringify(item));
					
					if (!diversion) {
						this.recNavItemInSession(item);
					} else {
						// On fait une dérivation, on va chercher les items
						this.getNavHistoryData();
					}
				}
			},
			recNavItemInSession: function(item) {
				let postData = "session_data=" + JSON.stringify({
					"nav_item": item,
				});
	
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = () => {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						this.getNavHistoryData();
					}
				};
				xhttp.open("POST", "./ajax.php?module=ajax&categ=session&action=rec_nav_history", true);
				xhttp.withCredentials = true;
				xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xhttp.send(postData);
			},
			getNavHistoryData: function() {
				let recentItem = 0;
				
				// Focus le point le plus récent
				let recentNavigationTmp = sessionStorage.getItem("recentNavigation");
				if (recentNavigationTmp) {
					let recentNavigation = JSON.parse(recentNavigationTmp);
					if (recentNavigation && recentNavigation[$navHistoryData.opacView]) {
						recentItem = recentNavigation[$navHistoryData.opacView];
					}
				}
				
				if (recentItem && recentItem != 0) {
					let postData = "session_data=" + JSON.stringify({
						"current_item_id": recentItem,
					});
					
					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = () => {
						if (xhttp.readyState == 4 && xhttp.status == 200) {
							if (xhttp.response != "") {
								let response = JSON.parse(xhttp.response);
								if (response) {
									this.formatData(response);
								}
							}
						}
					};
					xhttp.open("POST", "./ajax.php?module=ajax&categ=session&action=get_nav_history", true);
					xhttp.withCredentials = true;
					xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					xhttp.send(postData);
				}
			},
			formatData: function(data) {
				if (!data) {
					return;
				}
	
				// On récupère toutes les routes
				this.routes = data;
				
				this.focusOnRecentItem();
				this.svg.load = false;
			},
			focusOnRecentItem: function () {
				// Focus le point le plus récent
				let recentNavigationTmp = sessionStorage.getItem("recentNavigation");
				if (recentNavigationTmp) {
					let recentNavigation = JSON.parse(recentNavigationTmp);
					if (recentNavigation && recentNavigation[$navHistoryData.opacView]) {
						let recentItem = recentNavigation[$navHistoryData.opacView];
						if (recentItem && recentItem != 0) {
							this.svg.focus.active = true;
							this.svg.focus.idItem = recentItem;
						}
					}
				}
			},
	        removeBookmark: function(bookmark) {
	            let postData = "session_data=" + JSON.stringify({
					"bookmark": bookmark,
				});
	
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = (response) => {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						this.getAllBookmarks();
					}
				};
				xhttp.open("POST", "./ajax.php?module=ajax&categ=session&action=remove_bookmark_nav_history", true);
				xhttp.withCredentials = true;
				xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xhttp.send(postData);
	        },
	        getAllBookmarks: function() {
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = () => {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						let bookmarksList = JSON.parse(xhttp.response);
						// On veut un object
						if (this.isObject(bookmarksList)) {
						    this.svg.bookmarksList = bookmarksList;
						} else {
						    this.svg.bookmarksList = {};
						}
					}
				};
				xhttp.open("GET", "./ajax.php?module=ajax&categ=session&action=get_bookmarks_nav_history", true);
				xhttp.withCredentials = true;
				xhttp.send();
	        },
			isObject: function (obj) {
				/**
					typeof [] // object
					typeof {} // object
					
					Object.prototype.toString.call([]) === '[object Array]'
					Object.prototype.toString.call({}) === '[object Object]'
				 */
				return Object.prototype.toString.call(obj) === '[object Object]'
			}
		}
	});
});