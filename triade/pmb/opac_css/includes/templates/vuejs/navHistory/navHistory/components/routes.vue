<template>
	<g id="routes" class="routes">
		<items v-for="(item, index) in formatRoutes" :svg="svg" :item="item" :key="index"></items>
	</g>
</template>

<script>
	import items from './items.vue';
	export default {
		name: "routes",
		props: [
		    "svg",
		    "routes",
		    "firstitem"
	    ],
	    data: function () {
	    	return {
	    	    positions: [],
	    	    disctanceY: 200,
	    	    maxX: 0,
	    	    ecart: 25
	    	}  
	    },
		components: {
		    items,
		},
		computed: {
	        formatRoutes: function () {
	            this.positions = new Array();
	            let routes = new Array();
	            
	            for (let i = 0; i < this.routes.length; i++) {
	                let route = this.computedPosition(this.routes[i]);
	                routes.push(route);
	            }
	            
	            return routes;
	        },
	    },
	    methods: {
	        computedPosition: function (item) {
	            this.maxX = 0;
	            
	            // On calcule la position X
	            item = this.computedPositionX(item, 0);
	            
	            // On calcule les lignes de chaque point
	            item = this.computedLines(item);
	            
	            // On calcul une première fois la position des points enfants
		        item = this.computedPositionY(null, item);
	            
	            let position = {
					startItem: item,
	            	startX: item.x,
	            	startY: 0,
	            	endX: this.maxX,
	            	endY: item.nbMaxLines*this.disctanceY
	            };
	            
	            // On regarde s'il y a un chevauchement
		        for (let j = 0; j < this.positions.length; j++) {
		            let pos = this.positions[j];
		            if (
                        ( // La position que l'on veut placer chevauche une autre
                            (position.startX >= pos.startX && !(position.startX >= pos.endX)) || 
							(position.endX <= pos.endX && !(position.endX <= pos.startX))
						) || ( // La position que l'on regarde est dans la position que l'on veut placer 
                            (pos.startX >= position.startX && !(pos.startX >= position.endX)) || 
							(pos.endX <= position.endX && !(pos.endX <= position.startX))
						) 
					) {
	                    position.startY = pos.endY+this.disctanceY;
	                    position.endY += position.startY;
					}
	            }
		        
		        this.positions.push(position);
		        
	            // Position Y du point de départ calculer
		        item.y = position.startY;
		        // On calcule la bonne position des points enfants
		        item = this.computedPositionY(null, item);
	            
	            return item;
	        },
		    computedPositionX: function (item, previousX) {
				let x = 0;
				let _x = 0;
				
				// Calcul de la position X
				if (item.timestamp != this.firstitem.timestamp) {
				 	
				    // Calcule du point x (1 min = 100 px)
					let minutes = ((item.timestamp - this.firstitem.timestamp) / 1000) / 60;
					x = (minutes * 100);
					_x = x;
					
					// On évite le chevauchement des points
					if (previousX >= 0) {
						while ((x - previousX) <= this.ecart) {
							x += 5;
						}
					}
				}

				// Calcul de la position X pour les enfants
				if (item.childs && item.childs.length > 0) {
					for (let i = 0; i < item.childs.length; i++) {
					    item.childs[i] = this.computedPositionX(item.childs[i], x);
		            }
				}
				
				// Position avec décalage pour éviter le chevauchement
				item.x = x;
				// Position réelle
				item._x = x;
				
				if (x > this.maxX) {
	                this.maxX = x;
	            }
				
	            return item;
	        },
	        computedLines: function (item) {
	            let lines = [];
	            let count = 1;
	            
                if (item.childs && item.childs.length > 0) {
                    count = 0;
                    // On boucle sur les enfants
                    for (let i = item.childs.length-1; i > -1; i--) {
	                    // Boucle | l'enfant a des enfants ?
                        item.childs[i] = this.computedLines(item.childs[i]);
                
                    	// Nombre max de ligne utilisé
                        count += item.childs[i].nbMaxLines;
	                    
                        // On va calculer la ligne pour chaque enfant
	                    lines = this.getLineOfChildren(item, item.childs[i], lines);
		            }
	            }
                
	            item.lineOfChilds = lines;
	            item.nbMaxLines = count;
                
	            return item;
	        },
	        getLineOfChildren: function(parent, itemChild, lines) {
				if (!lines) {
		            lines = []; 
				}

		        let insert = false;
		        for (let j = 0; j < lines.length; j++) {
		            let line = lines[j];
		            // Chevauchement ??
	                if (((parent.x >= line.startX && !(parent.x >= line.endX)) || 
						(item.x <= line.endX && !(item.x <= line.startX)))
					) {
	                    // Oui on place l'item et on descend tout le reste
						lines.splice(j, 0, {
			                startX: parent.x,
			                endX: itemChild.x,
			                item: itemChild
				        });
						insert = true;
						break;
					}
	            }
		        
                // Aucun chevauchement on ajoute
		        if (!insert) {
		            lines.push({
		                startX: parent.x,
		                endX: itemChild.x,
		                item: itemChild
			        });
		        }
	            
	            return lines;
	        },
	        computedPositionY: function (parent, item, previousChild) {
	            
	            if (parent) {
		            let position = 0;
	                let lines = parent.lineOfChilds;
		            
	
	                // Sûr qu'elle ligne le point doit être mis ?
		            for (let i = 0; i < lines.length; i++) {
		                let line = lines[i];
		                if (line.item.id == item.id) {
		                    position = i;
		                    break;
		                }
		            }
		            
	                // Calcule d'une première position 
		            let y = (this.disctanceY*position);
		            if (parent && parent.y) {
		                y += parent.y;
		            }
		            
	                // Un point précédent déjà placer !
		            if (previousChild) {
		                // Il prend combien de ligne ?
			            let maxLine = previousChild.nbMaxLines - 1;
		                // On se marche dessus on augmente la distance
			            if ((previousChild.y + (this.disctanceY*maxLine)) >= y) {
			                y = (previousChild.y + (this.disctanceY*maxLine)) + this.disctanceY
			            }
		            }
		            
		            item.y = y;
	            }
	            
                // On calcule la position y pour les enfants 
	            if (item.childs && item.childs.length > 0) {
                    for (let i = item.childs.length-1; i > -1; i--) {
                        let previous = null;
                        if (item.childs[i-1]) {
	                        previous = item.childs[i-1];
                        }
                        item.childs[i] = this.computedPositionY(item, item.childs[i], previous)
		            }
	            }
	            
	            return item;
	        }
		}
	}
</script>