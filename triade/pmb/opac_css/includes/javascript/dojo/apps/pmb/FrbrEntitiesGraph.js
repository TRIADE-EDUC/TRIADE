// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FrbrEntitiesGraph.js,v 1.4.6.1.2.1 2025/04/29 12:27:40 rtigero Exp $


define(["dojo/_base/declare",
    "dojo/topic",
    "dojo/_base/lang",
    "d3/d3",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/dom-style",
    "dojo/on",
    "apps/pmb/EntitiesGraph",
    "dojo/store/Memory"
    ], function (declare, topic, lang, d3, dom, domConstruct, domStyle, on, EntitiesGraph, Memory) {

    return declare(EntitiesGraph, { 
    	memoryNodes : null,
    	memoryLinks : null,
    	defaultHeigt : 500,

    	postCreate : function () {
    		this.memoryNodes = new Memory({
    			data : this.memoryNodes
			});
    		this.memoryLinks = new Memory({
    			data : this.memoryLinks
    		});    		
    		var rootNode = this.memoryNodes.query({type : 'root'})[0];
    		var rootChildren = this.getDirectChildren(rootNode);
    		this.nodes = rootChildren.nodes.concat(rootNode);
    		this.links = rootChildren.links;
            this.inherited(arguments);          
        },
        
        nodeClicked: function(node) {
			if (node.type == 'additionnal_nodes') {
				// On appel la methode parente
            	this.inherited(arguments);    
			} else if(node.type != 'root' && node.type != 'subroot'){
	        	if (this.hasChildren(node)) {
	        		this.hideChildren(node);
	        	}else {
	        		var children = this.getDirectChildren(node);
	        		if (children.nodes && children.links) {
		                node.fx = node.x;
		                node.fy = node.y;
		        		domStyle.set(this.svgNode, 'cursor', 'wait');
		        		this.svgNode.addEventListener('click', this.clickCapturingFct, true);
		        		if(this.centerNode){
		        			this.centerNode.fx = null;
		        			this.centerNode.fy = null;
		        		}
		        		this.centerNode = node;
		        		this.loadSubGraph(children);
	        		}
	        	}
        	}
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
        
        loadSubGraph: function(data) {
			if (typeof data == 'string') {
				try {				
					data = this.formatString(data)
	        	    data = JSON.parse(data);
				} catch(e) {
					// on affiche l'erreur
					console.error(e);
					// on evite de bloquer la page
					data = {nodes: [], links: []};
				}
			} else if (!data || !data.nodes || !data.links) {
				data = {nodes: [], links: []};
			}
			
        	for(var i=0 ; i<data.nodes.length ; i++){
        		if(this.nodeChecker(data.nodes[i].id)){
        			this.nodes.push(data.nodes[i]);
        		}
        	}
        	for(var i=0 ; i<data.links.length ; i++){
        		this.links.push(data.links[i]);
        	}        	
        	this.updateGraph();	        
        },
        
        updateGraph : function() {
        	this.linkSvg = this.svg.select('#graph_links_container').selectAll("line")
	        	.data(this.links);
        	
        	this.linkSvg.exit().remove();
        	
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
			//this.linkSvg.exit().remove();
			
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
	        }),2000)
        },
        
        getDirectChildren : function(node) {
        	var directChildren = {};
        	var subrootLinks = Array.prototype.slice.call(this.memoryLinks.query({source : node.id}));
        	if (!subrootLinks.length) {
        		subrootLinks = Array.prototype.slice.call(this.memoryLinks.query({source : node}));
        	}
        	subrootLinks.forEach( 
        			lang.hitch(this, function(subrootLink) {
        				if (!directChildren.nodes) {
        					directChildren.nodes = new Array();
        				}
        				//traitement particulier
        				//au chargement les liens (this.links) contiennent des identifiants (source et target) 
        				//une fois affichés, les liens contiennent des objets (traitement particulier de d3)
        				var idNode = subrootLink.target;
        				var linkNode = idNode;
        				if (typeof idNode != 'string') {
        					idNode = subrootLink.target.id;
        					linkNode = subrootLink.target;
        				}
        				
        				this.memoryNodes.query({id : idNode}).forEach(function(node) {
        					if (directChildren.nodes.indexOf(node) == -1) {
        						directChildren.nodes.push(node);
        					}
        				});
        				//directChildren.nodes = directChildren.nodes.concat(Array.prototype.slice.call(this.memoryNodes.query({id : idNode})));
        				if (!directChildren.links) {
        					directChildren.links = new Array();
        				}
        				directChildren.links = directChildren.links.concat(Array.prototype.slice.call(this.memoryLinks.query({source : linkNode})));
        			})   		
        	);
        	if (directChildren.links) {
        		directChildren.links.forEach(
        				lang.hitch(this,function(link){
        					if (!directChildren.nodes) {
        						directChildren.nodes = new Array();
        					}
        					var idLink = link.target;
            				if (typeof idLink != 'string') {
            					idLink = link.target.id;
            				}
            				this.memoryNodes.query({id : idLink}).forEach(function(node) {        					
            					if (directChildren.nodes.indexOf(node) == -1) {
            						directChildren.nodes.push(node);
            					}
            				});
        					//directChildren.nodes = directChildren.nodes.concat(Array.prototype.slice.call(this.memoryNodes.query({id : idLink})));
        				})
        		);
        		directChildren.links = directChildren.links.concat(subrootLinks);
        	}
        	return directChildren;
        },
        
        getChildSubrootNode : function() {
        	
        },
        
        hasChildren : function(node) {        	
        	for(var i = 0; i < this.links.length ; i++) {
        		if (this.links[i].source == node) {
        			return true;
        		}
        	}
        	return false;
        },
        
        hasSeveralParents : function(node) {
        	var petitCompteur = 0;
        	for(var i = 0; i < this.links.length ; i++) {
        		if (this.links[i].target == node) {
        			petitCompteur++;
        			if (petitCompteur > 1 ) {
        				return true;
        			}
        		}
        	}        	
        	return false;
        },
        
        getAllChildren : function(node) {
        	var children = this.getDirectChildren(node);
        	var nodes = new Array();
        	var links = new Array();
        	if(children.nodes) {
	        	for( var i = 0; i < children.nodes.length; i++) {
	        		if (children.nodes[i].type != 'subroot') {
	        			var node = this.getDirectChildren(children.nodes[i]).nodes;
	        			var link = this.getDirectChildren(children.nodes[i]).links;
	        			if (node) {
	        				nodes = nodes.concat(node);
	        			}
		        		if (link) {
		        			links = links.concat(link);
		        		}		        			        			
	        		}
	        	}
        	}
        	if (nodes.length) {
        		children.nodes = children.nodes.concat(nodes);
        	}
        	if (links.length) {
        		children.links = children.links.concat(links);
        	}
        	return children;
        },
        
        hideChildren : function(node) {
        	var children = this.getAllChildren(node);
        	var newNodes = new Array();
        	var newLinks = new Array();
        	for(var i = 0; i < this.nodes.length; i++) {
				if (children.nodes.indexOf(this.nodes[i]) == -1 || this.hasSeveralParents(this.nodes[i])) {					
					newNodes.push(this.nodes[i]);
				}
        	}
        	for(var j = 0; j < this.links.length; j++) {
				if (children.links.indexOf(this.links[j]) == -1) {
					newLinks.push(this.links[j]);
				}
        	}
        	if (newNodes.length) {
        		this.nodes = newNodes; 
        	}
        	if (newLinks.length) {
        		this.links = newLinks; 
        	}
        	this.updateGraph();
        }
        
    });
});