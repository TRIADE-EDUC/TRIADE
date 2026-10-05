// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntitiesGraph.js,v 1.10 2021/12/21 10:30:24 qvarin Exp $


define(["dojo/_base/declare",
    "dojo/topic",
    "dojo/_base/lang",
    "dojo/request/xhr",
    "d3/d3",
    "dijit/_WidgetBase",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/dom-attr",
    "dojo/dom-style",
    "dojo/on"
    ], function (declare, topic, lang, xhr, d3, WidgetBase, dom, domConstruct, domAttr, domStyle, on) {

    return declare('EntitiesGraph', [WidgetBase], {
        nodes: null, //Propri�t�s renseign�es via la classe elements_list_tabs
        links: null,
        domNode: null, //-> Noeud Svg 
        simulation: null, //D3Simulation
        svgGraph: null,
        tooltipDiv: null,
        centerNode: null,
        constructor: function () {
            this.width = 800;
            this.height = 800;
            /**
             * Todo: cr�er un param�tre contenant une structure JSON d�finissant la taille du svg, les couleurs des diff�rents �l�ments
             */
            window.d3 = d3;
        },
        postCreate: function () {
            this.inherited(arguments);
            var parent = this.domNode.parentNode;
            var parentSize = window.getComputedStyle(parent);
            this.svg = d3.select(this.domNode).append("svg")
                .attr("width", parseInt(parentSize.width) - 10)
                .attr("height", parseInt(parentSize.height) - 10)
                .attr("id", "svgGraph")
                .attr('xmlns',"http://www.w3.org/2000/svg")
                .attr('xmlns:xlink',"http://www.w3.org/1999/xlink")
                .attr('version',"1.1")
                .attr('baseProfile',"full")
                .call(d3.zoom().scaleExtent([0, 8]).on("zoom", lang.hitch(this, this.zoomed)))
            this.svg = d3.select('#svgGraph').append("g")
            	.attr("id", 'svgMainGroup')
                .attr("transform", "translate(40,0) scale(0.8)");

            this.svgNode = dom.byId('svgGraph');
            d3.select('#svgGraph').append("defs");
            
            this.initTooltip();

            this.simulation = d3.forceSimulation()
                .force("link", d3.forceLink().id(function (d) {                    
                	return d.id;
                }).distance(function (d) {
                   //return  (d.target.name.length < 80) ? (parseInt(5*(d.target.name.length)+100)) : parseInt(500);
                   return  Math.log(parseInt((5*(d.target.name.length))+30))*30;
                }))
                .force("charge", d3.forceManyBody())
                .force("collision", d3.forceCollide())
                .force("center", d3.forceCenter((parseInt(parentSize.width) - 10) / 2, (parseInt(parentSize.height) - 10) / 2));


            this.linkSvg = this.svg.append("g").attr("id", "graph_links_container").selectAll("line")
                .data(this.links).enter().append("line")
                .attr("class", "graphlink")
                .attr("stroke-width", function (d) {
                    return 2;
                })
                .attr("style", function(d){
                	if(d.color){
                		return  "stroke: rgb("+d.color+")";	
                	}
                	return  "stroke: #999";
                })
	        	.attr("marker-end", "url(#arrow)");

            
            this.initNodes();
            // On initialise les markers
		    this.setDefs();
		    
            this.simulation
                .nodes(this.nodes)
                .on("tick", lang.hitch(this, this.ticked));

            this.simulation.force("link")
                .links(this.links);
            this.simulation.velocityDecay(0.1);
            this.simulation.alphaTarget(1).restart();
            setTimeout(lang.hitch(this, function(){
            	this.simulation.alphaTarget(0);
            	this.simulation.velocityDecay(0.4);
            }),3000)
            this.clickCapturingFct = lang.hitch(this,function(e){
    			dojo.stopEvent(e);
    			return false;
    		});
        },
        
        initNodes : function () {        	
        		
        	this.nodeSvg = this.svg.append("g")
            .attr("id", "graph_nodes_container")
            .selectAll(".graphnode")
            .data(this.nodes)
            .enter().append("g")
            .attr("class", "graphnode")
            .call(d3.drag()
                .on("start", lang.hitch(this, this.dragstarted))
                .on("drag", lang.hitch(this, this.dragged))
                .on("end", lang.hitch(this, this.dragended)))
                .on('mouseover', lang.hitch(this, this.displayTooltip))
            .on('mousemove', lang.hitch(this, this.fillTooltip))
            .on('mouseout', lang.hitch(this, this.hideTooltip));
            
        
        	this.embellishNode();
	        
        },
        
        ticked: function () {
            this.linkSvg
                .attr("x1", function (d) {
                	return d.source.x;
                })
                .attr("y1", function (d) {
                	return d.source.y;
                })
                .attr("x2", function(d) {
		        	  var sx = d.source.x;
		        	  var sy = d.source.y;
		        	  var tx = d.target.x;
		        	  var ty = d.target.y;
		        	  
		        	  // Notre ami Thal�s nous permet de raccourcir les liens pour y faire apparaitre des fl�ches
		        	  var h = (d.target.radius*Math.abs(tx-sx))/Math.sqrt((tx-sx)*(tx-sx)+(ty-sy)*(ty-sy));
		        	  
		        	  return ((tx > sx) ? (tx - h) : (tx + h));
		          })
		          .attr("y2", function(d) {
		        	  var sx = d.source.x;
		        	  var sy = d.source.y;
		        	  var tx = d.target.x;
		        	  var ty = d.target.y;
		        	  
		        	  var h = (d.target.radius*Math.abs(ty-sy))/Math.sqrt((tx-sx)*(tx-sx)+(ty-sy)*(ty-sy));
		        	  
		        	  return ((ty > sy) ? (ty - h) : (ty + h));
		          });

            this.nodeSvg.attr("transform", function (d) {
                return "translate(" + d.x + ", " + d.y + ")";
            });

        },
        dragstarted: function (d) {
            if (!d3.event.active) {
                this.simulation.alphaTarget(0.1).restart();
            }
        },
        dragged: function (d) {
            d.fy = d3.event.y
            d.fx = d3.event.x
        },
        dragended: function (d) {
            d.fy = null;
            d.fx = null;
            if (!d3.event.active) {
                this.simulation.alphaTarget(0);
            }
        },
        zoomed: function () {
            this.svg.attr("transform", d3.event.transform);
        },
        nodeClicked: function (node) {
        	if(node.ajaxParams){
                node.fx = node.x;
                node.fy = node.y;
        		domStyle.set(this.svgNode, 'cursor', 'wait');
        		this.svgNode.addEventListener('click', this.clickCapturingFct, true);
        		if(this.centerNode){
        			this.centerNode.fx = null;
        			this.centerNode.fy = null;
        		}
        		this.centerNode = node;
        		xhr.post('./ajax.php?module=ajax&categ=entity_graph&sub=get_graph', {
        			data: node.ajaxParams
        		}).then(lang.hitch(this, this.loadSubGraph));
        		node.ajaxParams = null;
        	} else if (node.type == "additionnal_nodes") {
				
				var elements = node.elements.slice(0, node.limit);
				node.elements.splice(0, node.limit);
				
				if (node.elements.length > 0) {
					var new_name = node.name.replace(/^([0-9]+)/, node.elements.length);
					this.renameNode(node.id, new_name);
				} else {
					this.removeNode(node.id);
				}
				
				node.info.elements = elements;
        		domStyle.set(this.svgNode, 'cursor', 'wait');
        		this.svgNode.addEventListener('click', this.clickCapturingFct, true);
        		if(this.centerNode){
        			this.centerNode.fx = null;
        			this.centerNode.fy = null;
        		}
        		this.centerNode = node;

    			xhr.post('./ajax.php?module=ajax&categ=entity_graph&sub=get_next_additionnal', {
        			data: {node: JSON.stringify(node.info)}
        		}).then(lang.hitch(this, this.loadSubGraph)); 
			}
        },
        labelClicked: function(node){
        	if(node.url){
	    		window.open(node.url, '_blank')	
	    	}
        },
        fillCircle: function (d) {
            if(d.color){
            	return 'rgb('+d.color+')';
            }
            return '';
//            return "#"+parseInt(this.getRandomInt(0,255)).toString(16)+parseInt(this.getRandomInt(0,255)).toString(16)+parseInt(this.getRandomInt(0,255)).toString(16);
        },
        createPatterns: function (d) {
            /**
             * Traitement � ajouter en fonction du radius
             */
            this.defs = this.svgNode.querySelector('defs');
            
            var pattern = document.createElementNS('http://www.w3.org/2000/svg','pattern');
            pattern.setAttributeNS(null,'id','image'+d.id);
            pattern.setAttributeNS(null,'x', 0);
            pattern.setAttributeNS(null,'y', 0);
            pattern.setAttributeNS('http://www.w3.org/2000/svg','patternUnits', "objectBoundingBox");
            pattern.setAttributeNS(null,'height', '100%');
            pattern.setAttributeNS(null,'width', '100%');

            var image = document.createElementNS('http://www.w3.org/2000/svg','image');
            image.setAttributeNS(null,'x', d.radius - 8);
            image.setAttributeNS(null,'y', d.radius - 8);
            image.setAttributeNS(null,'width', 16);
            image.setAttributeNS(null,'height',16);
            image.setAttributeNS('http://www.w3.org/1999/xlink','href', d.img);

            pattern.appendChild(image);
            this.defs.appendChild(pattern);
        },
        initTooltip: function(){
            
            this.tooltipDiv = domConstruct.create('div', {'class':'graph_tooltip', 
                style:{
                    opacity:1e-6,
                    position: 'absolute',
                    textAlign: 'center',
                    width: '100px',
//                    height: '100px',
                    padding: '8px',
                    font: '10px sans-serif',
                    background: 'rgb(239,239,239)',
                    border: 'solid 1px #aaa',
                    borderRadius: '8px',
                    pointerEvents:'none',
                }}, document.body, 'last');
            
        },
        displayTooltip: function(e){
            d3.select('div.graph_tooltip').transition()
                .duration(200)
                .style("opacity", 1);
        },
        fillTooltip: function(elt){
            d3.select('div.graph_tooltip')
                .text(elt.name + '\n')
                .style("left", (d3.event.pageX ) + "px")
                .style("top", (d3.event.pageY) + "px");
        },
        hideTooltip: function(e){
            d3.select('div.graph_tooltip').transition()
                .duration(200)
                .style("opacity", 1e-6);
        },
        nodeChecker: function(id){
        	for(var j=0 ; j<this.nodes.length ; j++){
        		if(this.nodes[j].id == id){
        			return false;
    			}	
    		}
        	return true;
        },
        loadSubGraph: function(data) {
			try {				
				data = this.formatString(data)
	        	data = JSON.parse(data);
			} catch(e) {
				// on affiche l'erreur
				console.error(e);
				// on evite de bloquer la page
				data = {nodes: [], links: []};
			}
        	for(var i=0 ; i<data.nodes.length ; i++){
        		if(this.nodeChecker(data.nodes[i].id)) {
        			this.nodes.push(data.nodes[i]);
        		}
        	}
        	for(var i=0 ; i<data.links.length ; i++){
        		this.links.push(data.links[i]);
        	}
      
    		this.linkSvg = this.svg.select('#graph_links_container').selectAll("line")
	        	.data(this.links);
    
    		var linkEnter = this.linkSvg.enter().append("line")
	        	.attr("class", "graphlink")
	        	.attr("stroke-width", function (d) {
	        		return 2;
	        	})
	        	.attr("style", function(d){
	        		if(d.color){
	        			return  "stroke: rgb("+d.color+")";	
	        		}
	        		return  "stroke: #999";
	        	})
	        	.attr("marker-end", "url(#arrow)");
    		this.linkSvg = linkEnter.merge(this.linkSvg);
    		this.linkSvg.exit().remove();
    		
    		this.simulation
         		.nodes(this.nodes)
                .on("tick", lang.hitch(this, this.ticked));

    		this.simulation.force("link")
	        	.links(this.links);
    		
    		this.nodeSvg = this.svg.select('#graph_nodes_container').selectAll(".graphnode")
	        	.data(this.nodes, function(d) { return d.id; });
		      
		      this.nodeSvg.exit().remove();
		      

		      var nodeEnter = this.nodeSvg.enter()
		        .append("g")
	            .attr("class", "graphnode")
	
	            .call(d3.drag()
	                .on("start", lang.hitch(this, this.dragstarted))
	                .on("drag", lang.hitch(this, this.dragged))
	                .on("end", lang.hitch(this, this.dragended)))
	                .on('mouseover', lang.hitch(this, this.displayTooltip))
	            .on('mousemove', lang.hitch(this, this.fillTooltip))
	            .on('mouseout', lang.hitch(this, this.hideTooltip));

		       
		        this.nodeSvg = nodeEnter.merge(this.nodeSvg);

	        this.embellishNode();
		        
            this.simulation.velocityDecay(0.1);
            this.simulation.alphaTarget(1).restart();
            setTimeout(lang.hitch(this, function(){
            	this.simulation.alphaTarget(0);
            	this.simulation.velocityDecay(0.4);
            	domStyle.set(this.svgNode, 'cursor', '');
	        	this.svgNode.removeEventListener('click', this.clickCapturingFct, true);
            }),3000)
	        
        },
        embellishNode: function(){
        	this.svg.selectAll('.graphnode').filter(function(node, index, nodeList) {
        		if(nodeList[index].querySelector('circle')){
        			return false;
        		}
        		return true;
        	})
        	.data(this.nodes, function(d){
        		return d.id;
        	})
        	.attr('machin', function(d){
        		return d.id;
        	})
            .append('circle')
            .attr("r", function (d) {
                return (d.radius ? d.radius : 10);
            })
            .attr('fillOpacity','1')
            .attr("fill", lang.hitch(this, this.fillCircle))
            .attr('stroke', "#000")
            .attr('strokeWidth', "1");

	        this.svg.selectAll(".graphnode")
	        	.filter(function(node, index, nodeList) {
		    		if(nodeList[index].querySelector('text')){
		    			return false;
		    		}
		    		return true;
		    	})
	            .data(this.nodes,function(d){
	        		return d.id;
	        	})
	            .append("text")
	            .attr("dy", 3)
	            .attr("x", function (d) {
	                return 20;
	            })
	            .text(function (d) {
	                return (d.name.length < 80 ? d.name : (d.name.slice(0,80))+' [...]');
	            }).on("click", lang.hitch(this, this.labelClicked));
	        
	        this.svg.selectAll(".graphnode")
	        	.filter(function(node, index, nodeList) {
		    		if(nodeList[index].querySelector('image')){
		    			return false;
		    		}
		    		return true;
		    	})
	            .data(this.nodes, function(d){
	        		return d.id;
	        	})
	            .append("image")
	            .attr("width", 16)
	            .attr("height", 16)	            
	            .attr("x", -8)
	            .attr("y", -8)
	            .attr("xlink:href", function(d){
	            	return d.img;
	            })
	            .text(function (d) {
	                return d.name;
	            })
	            .on("click", lang.hitch(this, this.nodeClicked));
        },
	    
	    setDefs: function() {
		    this.svg.append("defs")
		    	.append('marker')
			    	.attr("id", "arrow")
			    	.attr("viewBox", "0 0 10 10")
			    	.attr("refX", "10")
			    	.attr("refY", "5")
			    	.attr("markerUnits", "strokeWidth")
			    	.attr("markerWidth", "5")
			    	.attr("markerHeight", "5")
			    	.attr("orient", "auto")
			    	.append("path")
			    	.attr("d", "M 0 0 L 10 5 L 0 10 z")
		    	;
	    },
	    renameNode: function (id, name) {
			var node = d3.select("g[machin='" + id + "']");
			if (node) {
				node.select('text').text(function(d){ return name; });
				node.select('image').text(function(d){ return name; });
				for(var i=0 ; i < this.nodes.length; i++){
	        		if(this.nodes[i].id == id) {
						this.nodes[i].name = name;
	        			break;
	    			}	
	    		}
			}
		},
	    removeNode: function (id) {
			var node = d3.select("g[machin='" + id + "']");
			if (node) {
				node.remove();
			}

			for (var i=0; i < this.nodes.length; i++) {
        		if(this.nodes[i].id == id) {
		    		this.nodes.splice(i, 1);
        			break;
    			}	
    		}
    		
    		var length = this.links.length;
			for (var i=0; i < length; i++) {
        		if (this.links[i].source.id == id || this.links[i].target.id == id) {
	    			// On supprime le lien
	    			var index = this.links[i].index
	    			this.linkSvg.filter(function (d, i) { 
						return i == index;
					}).remove();
	    			this.links.splice(i, 1);
	    			
	    			// On recommence a 0
	    			length = this.links.length;
	    			i = 0;
    			}
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
    });
});