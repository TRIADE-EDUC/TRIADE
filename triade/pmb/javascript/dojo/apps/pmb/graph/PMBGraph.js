// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PMBGraph.js,v 1.9 2020/06/05 08:44:33 gneveu Exp $

define(["dojo/_base/declare", 
        "dijit/layout/ContentPane",
        "d3/d3", 
        "dojo/on", 
        "dojo/_base/lang",
        "dojo/topic",
        "dojo/mouse",
        "dojo/dom",
        "dojo/_base/event",
        "apps/pmb/PMBDialog",
        "dojo/topic",
        "dojo/query",
        "apps/pmb/PMBConfirmDialog",
        'dojo/request/xhr',
        "dojo/dom-construct"
    ], 
	function(declare, ContentPane, d3, on, lang, topic, mouse, dom, dojoEvent, Dialog, topic, query, ConfirmDialog, xhr, domConstruct){
	
	return declare('PMBGraph', [ContentPane], {
		width: "100%",
		height: "100%",
		svg: null,
		data: null,
		linkSvg: null,
		nodeSvg:null, 
		simulation: null,
		constructor: function(){
			this.nodes = [];
			this.links = [];
		},
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
			
			}
		},
		postCreate: function(){
			this.inherited(arguments);	
		    this.svg = d3.select(this.domNode).append("svg")
		        .attr("width", this.width)
		        .attr("id", "svgGraph")
		        .attr("height", this.height)
		        .call(d3.zoom().scaleExtent([0.2, 7]).on("zoom", lang.hitch(this, this.zoomed)))
		      .append("g")
		        .attr("transform", "translate(40,0)");
		    
		    var svgSizes = d3.select('svg').node().getBBox();
		    this.simulation = d3.forceSimulation()
		    	 .force("link", d3.forceLink().id(function (d) {                    
                    return d.id;
                 }).distance(function (d) {                	 
//                	 if(d.target.name != null){
                		 return  Math.log(parseInt((5*(d.target.name.length))+30))*30;
//                	 }else{
//                		 var e = new Error();
//                		 console.log(e.stack);
//                	 }
                 }))
		    	.force("charge", d3.forceManyBody().strength(function(){
		    		return -80;
		    	}))
		    	.force("center", d3.forceCenter().x(250).y(250))
		    	.force("collide", d3.forceCollide(function(){
		    		return 25;
		    	}))
		    	.on("tick", lang.hitch(this, this.ticked));
		    
		    // On initialise les markers
		    this.setDefs();
		    
		    this.linkSvg = this.svg.append('g').attr("class","links").selectAll(".graphlink").data(this.getLinks(), function(d){return d.target.id});
		    this.nodeSvg = this.svg.append('g').attr("class","nodes").selectAll(".node").data(this.getNodes(), function(d){return d.id});
		    
		    
		    this.simulation.alphaDecay(0.1);
		    this.update();
		},
		zoomed: function() {
		      this.svg.attr("transform", d3.event.transform);
		},
		
	    update: function() {	    	
	    	/** Création des noeuds temporaires représentants les propriétés de chaques formulaires **/ 
	    	var links = this.getLinks();  
	    	var nodes = this.getNodes();

		    	this.linkSvg = this.svg.select(".links").selectAll('.graphlink')
			    	.data(links, function(d) { return d.target.id; })
	
			    this.linkSvg.exit().remove();
			      
			    var linkEnter = this.linkSvg.enter()
		        	.append("line")
		        	.attr("stroke-width", function (d) {
		        		return 2;
		        	})
		        	.attr("style", function(d){
		        		if(d.color){
		        			return  "stroke: rgb("+d.color+")";	
		        		}
		        		return  "stroke: #999";
		        	})
		        	.attr("class", "graphlink")
	        		.attr("marker-end", "url(#arrow)");
			    	
			    this.linkSvg = linkEnter.merge(this.linkSvg);
			    this.nodeSvg = this.svg.select('.nodes').selectAll(".node")
			    	.data(nodes, function(d) { 
			    		var domNode = dom.byId(d.id);
			    		if(domNode && domNode.nextElementSibling){
			    			while(domNode.nextElementSibling.tagName != "text"){
			    				domNode = domNode.nextElementSibling;
			    			}
			    			domNode.nextElementSibling.innerHTML = d.name;
			    		}
			    		return d.id; 
			    	});
			    
			    this.nodeSvg.exit().remove(function(d){
			    	d.destroy();
			    });		      
			    		      
			    var nodeEnter = this.nodeSvg.enter()
			    	.append("g")
			    	.on("click", function(d){
			    		d.clicked(arguments);
			    	})
			    	.on("dragover", function(d){
			    		d.dragOver(arguments);
			    	})
			    	.on("dragleave", function(d){
			    		if(d.dragLeave){
			    			d.dragLeave(arguments);  
			    		}
			    	}) 
			    	.on("drop", function(d){
			    		d.dragDrop(d, d3.event);
			    	})
			    	.attr("class", "node")
			    	.attr("transform", function(d) {
			    		if(d.x && d.y){
			    			return "translate(" + d.x + ", " + d.y + ")";   
			        	}
			        	return "translate(0, 0)";
			    	})
			    	.call(d3.drag()
			    		.on("start", lang.hitch(this, this.dragstarted))
			    	.on("drag", lang.hitch(this, this.dragged))
			    	.on("end", lang.hitch(this, this.dragended)));
	
			    nodeEnter.append("circle")
			    	.attr("r", function(d) { return d.radius; })
			    	.attr("class", function(d) { return d.type; })
			    	.attr("id", function(d) { return d.id; })
			    	.attr("data-type", function(d) { return d.type; })
			    	.on("dblclick.zoom", null)
			    	.style("fill", function(d){ return "rgb("+d.color+")"; })
//			    	.each(function(d){
//			    		if(typeof d.getContextualMenu == "function"){
//				    		d.getContextualMenu();
//			    		}	
//			    	});			    	
			            
			    nodeEnter.append("text")
			    	.attr("dy", 3)
			    	.attr("x", function(d) { return parseInt(d.radius)+3; })
			    	.style("text-anchor", function(d) { return "start"; })
			    	.text(function(d) { return d.name; });
			    
			    nodeEnter.append("image")
		            .attr("width", 16)
		            .attr("height", 16)	            
		            .attr("x", -8)
		            .attr("y", -8)
		            .attr("xlink:href", function(d){
		            	return d.img;
		            })
		            .attr("id", function(d){
		            	return d.id+'_'+'img';
		            });
			        
			    this.nodeSvg = nodeEnter.merge(this.nodeSvg);
			      
			    this.simulation
			    	.nodes(nodes)
			    this.simulation.force("link")
			    	.links(links);
	//		      this.simulation.alphaTarget(0.3).restart() //restart        /** Reprise des evts du dragend **/
	//		      this.simulation.alphaTarget(0); //-> STOP     
			      //A voir pour laisser un temps plus long sur la premiére initialisation
	
			    this.simulation.velocityDecay(0.1);
			    this.simulation.alphaTarget(1).restart();
			    setTimeout(lang.hitch(this, function(){
			    	this.simulation.alphaTarget(0);
			    	this.simulation.velocityDecay(0.4);
			    }),1000);
    	},
    	
		ticked: function() {
			this.linkSvg
		          .attr("x1", function(d) { return d.source.x; })
		          .attr("y1", function(d) { return d.source.y; })
		          .attr("x2", function(d) { return d.target.x; })
		          .attr("y2", function(d) { return d.target.y; });

			this.nodeSvg
		          .attr("transform", function(d) {
		        	  return "translate(" + d.x + ", " + d.y + ")"; 
		          });
	    },
	    dragstarted: function(d) {
	      if (!d3.event.active) this.simulation.alphaTarget(0.3).restart()
	    },
	    dragged: function(d) { 
	      	d.fy = d3.event.y;
	      	d.fx = d3.event.x;
	    },
	    dragended: function(d) {   
	      d.fy = null;
	      d.fx = null;
	      if (!d3.event.active) this.simulation.alphaTarget(0);
	    },
	    removeNode : function(nodeID) {
	    	var node = graphStore.get(nodeID); 
	    	var confirmDialog = new ConfirmDialog({title : node.name, content : pmbDojo.messages.getMessage('contribution_area','contribution_area_confirm_deleting'), onExecute : lang.hitch(this,function(){
	    		graphStore.removeNode(nodeID);	
	    		graphStore.save();
				this.update()
	    	})});
	    	confirmDialog.show();	    	
	    },
	    
	    getNodes : function() {
	    	return this.nodes;
	    },
	    
	    getLinks : function() {
	    	return this.links;
	    },
	    
	    setDefs: function() {
		    this.svg.append("defs")
		    	.append('marker')
			    	.attr("id", "arrow")
			    	.attr("viewBox", "0 0 10 10")
			    	.attr("refX", "10")
			    	.attr("refY", "5")
			    	.attr("markerUnits", "strokeWidth")
			    	.attr("markerWidth", "10")
			    	.attr("markerHeight", "10")
			    	.attr("orient", "auto")
			    	.append("path")
			    		.attr("d", "M 0 0 L 10 5 L 0 10 z")
		    	;
	    },

	});
});