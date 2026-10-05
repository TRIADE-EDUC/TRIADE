
<template>
	<div id="tooltips" :class="computedClass" :style="position" v-html="tooltipTemplate" ref="tooltips" @click="linkClicked"></div>
</template>

<script>
	export default {
		name: "tooltips",
		props: [
		    "svg",
	    ],
	    data: function() {
	    	return {
	    		templates: {},
	    	    templateLoaded: true,
	    		itemTimeout: {},
	    		timeout: 5, // en second
	    		failedItem: {},
	    		nbOfTrials: 5
	    	}
	    },
	    updated: function () {
			this.$nextTick(function () {
			    var btn = document.getElementById("tooltip_submit_form");
			    if (btn) {
				    this.parseSearchForm(btn);
			    }
			})
	    },
	    computed: {
	    	tooltipTemplate: function() {
	    		
	    	    var itemId = this.svg.itemHover.itemId;
	    	    
	    	    // On est sur aucun item
	    		if (itemId == 0) {
		    		return "";
	    		}
	    	    
	    	    if (!this.failedItem[this.svg.itemHover.itemId]) {
					this.failedItem[this.svg.itemHover.itemId] = 0;
	    	    }
	    	    
	    	    // Le serveur n'a jamais retourné de réponse/template on stop
	    	    if (this.failedItem[this.svg.itemHover.itemId] == this.nbOfTrials) {
	    	        this.svg.itemHover.itemId = 0;
	    	        return "";
	    	    }
	    	    
	    	    // On attent avent de refaire une requête
	    	    if (this.isInTimeout()) {
	    	        this.svg.itemHover.itemId = 0;
	    	        return "";
	    	    }
	    		
	    	    // On va chercher le template de l'item
	    		if (!this.templates[itemId]) {
	    		    this.templateLoaded = false;
		    		this.getTemplate();
	    		}
	    		
	    	    // On retourne le template
	    		if (this.templateLoaded && this.templates[itemId] && this.templates[itemId] != "") {
	    		    this.$refs.tooltips.focus();
					return this.templates[itemId];
	    		}
				return "";
	    	},
	    	computedClass: function () {
	    	    if (this.svg.itemHover.itemId != 0 && this.templateLoaded) {
		    	    return "show";
	    	    }
	    	    return "hidden";
	    	},
	    	position: function () {
	    	    if (this.tooltipTemplate != "" && this.svg.itemHover.x && this.svg.itemHover.y) {
	    	        return `left: ${this.svg.itemHover.x}px; top: ${this.svg.itemHover.y}px;`;
	    	    }
	    	    return "left: 0px; top: 0px;";
	    	}
	    },
	    methods: {
	        isInTimeout: function () {
	            var itemId = this.svg.itemHover.itemId;
	            if (this.itemTimeout && this.itemTimeout[itemId]) {
	                let time = Date.now();
	                let duration = (time - this.itemTimeout[itemId])/1000;
	                if (duration <= this.timeout) {
	                    return true;
	                } else {
	                    delete this.itemTimeout[itemId];
	                }
	            }
	            return false;
	    	},
	        linkClicked: function (e) {
				if (e.target && e.target.nodeName == "A") {
				    e.preventDefault()
				    let a = e.target;
				    let form = this.$el.querySelector('form');
					
				    // Lien dans la page identique au point
				    if (form && a && form.action == a.href) {
				        form.submit();
				    } else if (a && a.href) {
				        let recentNavigationTmp = sessionStorage.getItem("recentNavigation");
						if (recentNavigationTmp) {
							let recentNavigation = JSON.parse(recentNavigationTmp);
							if (recentNavigation) {
							    recentNavigation[$navHistoryData.opacView] = this.svg.itemHover.itemId
								sessionStorage.setItem("recentNavigation", JSON.stringify(recentNavigation));
						        window.open(a.href, "_self"); 
							}
						}
				    }
				}
	        },
	    	getTemplate: function() {
				let sessionData = JSON.stringify({
					"item_id": this.svg.itemHover.itemId
				});
				var postData = "session_data=" + sessionData;
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = () => {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						this.templates[this.svg.itemHover.itemId] = "";
					    if (xhttp.response) {
							this.templates[this.svg.itemHover.itemId] = xhttp.response;
					    } else {
					        // On met l'item en attente si on a pas reçu le template
					        // pour éviter le spam
					        this.itemTimeout[this.svg.itemHover.itemId] = Date.now();
							this.failedItem[this.svg.itemHover.itemId]++;
					    }
		    		    this.templateLoaded = true;
					}
				};
				xhttp.open("POST", "./ajax.php?module=ajax&categ=session&action=get_nav_item_tpl", true);
				xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xhttp.send(postData);
	    	},
	    	parseSearchForm: function (btn) {
	    	    
	    	    if (btn.attributes['data-form_name'] && btn.attributes['data-form_name'].value != "") {
	    	        
	    	        let formName = btn.attributes['data-form_name'].value;
	    	        let form = this.$el.querySelector('#tooltips form[name="'+formName+'"]');
				    
	    	        if (form) {
	    	            
	    	            let itemId = this.svg.itemHover.itemId;
	    	            
	    	            if (!form.navId) {
					        let input = document.createElement("INPUT");
					        input.type = "hidden";
					        input.name = "navId";
					        input.value = itemId;
					        form.appendChild(input);
					        
					        btn.onclick = () => {
					            form.submit();    
					        }
	    	            }
	    	            
	    	            if (form.navId && form.navId != itemId) {
	    	                form.navId.value = itemId;
	    	            }
				    }
	    	    }
	    	}
	    }
	}
</script>