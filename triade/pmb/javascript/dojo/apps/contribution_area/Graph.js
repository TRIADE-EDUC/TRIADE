// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Graph.js,v 1.12 2018-10-17 14:25:40 ccraig Exp $

define(["dojo/_base/declare", 
        "dijit/layout/ContentPane",
        "d3/d3", 
        "dojo/on", 
        "dojo/_base/lang",
        "dojo/topic",
        "dojo/mouse",
        "dojo/dom",
        "dojo/_base/event",
        "apps/contribution_area/SvgContextMenu",
        "apps/contribution_area/svg/ScenarioNode",
        "apps/pmb/PMBDialog",
        "dojo/text!apps/contribution_area/templates/createScenario.html",
        "apps/contribution_area/svg/FormNode",
        "apps/contribution_area/svg/Link",
        "dojo/topic",
        "dojo/query",
        "apps/pmb/PMBConfirmDialog",
        'dojo/request/xhr',
        "dojo/dom-construct"
    ], 
	function(declare, ContentPane, d3, on, lang, topic, mouse, dom, dojoEvent, SvgContextMenu, ScenarioNode, Dialog, createScenarioTpl, FormNode, Link, topic, query, ConfirmDialog, xhr, domConstruct){
	
	return declare(ContentPane, {
		currentDialog: null,
		formsListHandler: null,
		width: "100%",
		height: "100%",
		svg: null,
		data: null,
		linkSvg: null,
		nodeSvg:null, 
		simulation: null,
		constructor: function(){
			randomizer = function(){return Math.floor(Math.random() * (10000 - 10)) + 10;};
			this.formsListHandler = new Array();
			this.own(
				topic.subscribe('SvgContextMenu', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Dialog', lang.hitch(this, this.handleEvents)),
				topic.subscribe('FormsList', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Node', lang.hitch(this, this.handleEvents)),
				topic.subscribe('FormNode', lang.hitch(this, this.handleEvents)),
				topic.subscribe('GraphStore', lang.hitch(this, this.handleEvents))	
			);
		},
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case "scenarioCreationRequested" :
					this.generatePopupScenario({typeRequested:evtArgs.typeRequested,isStartScenario:evtArgs.isStartScenario}, true);
					break;
				case "scenarioEditionRequested":
					var scenario = graphStore.get(evtArgs.nodeID);
					this.generatePopupScenario(scenario, false);
					break;	
				case "nodeRemoveRequested":
					this.removeNode(evtArgs.nodeID);
					break;	
				case "nodeAdded":
				case "refreshNodes":
					this.update();
					break;
				case "createFormNode": 
					this.createFormNode(evtArgs);
					break;
				case "createGhost":
					this.createGhost(evtArgs.node);
					break;
			}
		},
		postCreate: function(){
			this.inherited(arguments);	
		    this.svg = d3.select(this.domNode).append("svg")
		        .attr("width", this.width)
		        .attr("id", "svgGraph")
//		        .attr("shape-rendering", "crispEdges")
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
                		 return  parseInt((5*(d.target.name.length))+30);               		 
//                	 }else{
//                		 var e = new Error();
//                		 console.log(e.stack);
//                	 }
                 }))
		    	.force("charge", d3.forceManyBody())
		    	.force("center", d3.forceCenter().x(250).y(250))
		    	.on("tick", lang.hitch(this, this.ticked));
		    
		    // On initialise les markers
		    this.setDefs();
		    		    
		    this.linkSvg = this.svg.append('g').attr("class","links").selectAll(".graphlink").data(graphStore.getGraphLinks(), function(d){return d.target.id});
		    this.nodeSvg = this.svg.append('g').attr("class","nodes").selectAll(".node").data(graphStore.getGraphNodes(), function(d){return d.id});
		    
		    this.simulation.alphaDecay(0.1);
		    this.update();
		    this.contextMenu = new SvgContextMenu({targetNodeIds: ['svgGraph']});
		},
		zoomed: function() {
		      this.svg.attr("transform", d3.event.transform);
		},
		
	    update: function() {
	    	/** Création des noeuds temporaires représentants les propriétés de chaques formulaires **/ 
	    	var links = graphStore.getGraphLinks();  
	    	var nodes = graphStore.getGraphNodes()
	    	
	    	this.linkSvg = this.svg.select(".links").selectAll('.graphlink')
		    	.data(links, function(d) { return d.target.id; })

		    this.linkSvg.exit().remove();
		      
		    var linkEnter = this.linkSvg.enter()
	        	.append("line")
	        	.attr("stroke", "#6b6b6b")
	        	.attr("strokeWidth", "2px")
	        	.attr("class", "graphlink")
	        	.attr("marker-end", "url(#arrow)");
		    	
		    this.linkSvg = linkEnter.merge(this.linkSvg)
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
		    
		    nodeEnter.append(function(d) {
		    		return document.createElementNS("http://www.w3.org/2000/svg", d.shape);
		    	})
		    	.attr("r", function(d) { return d.radius; })
		    	.attr("width", function(d) { return d.radius*2; })
		    	.attr("height", function(d) { return d.radius*2; })
		    	.attr("class", function(d) { return d.type; })
		    	.attr("id", function(d) { return d.id; })
		    	.attr("data-type", function(d) { return d.type; })
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
		    		d.dragDrop(arguments);
		    	})
		    	.on("dblclick.zoom", null)
		    	.on("dblclick", lang.hitch(this, this.hideChildren))		    		
		    	.style("fill", function(d){ return d.color; })
		    	.append("title")
		    	.text(function(d) { return d.name; })
		            
		    nodeEnter.append("text")
		    	.attr("dy", function(d) {return (d.shape == 'circle' ? 3 : d.radius + 3)})
		    	.attr("x", function(d) {return (d.shape == 'circle' ? d.radius+3 : d.radius * 2 + 3)})
		    	.style("text-anchor", function(d) { return d.children ? "end" : "start"; })
		    	.text(function(d) { return d.name; });
		        
		    nodeEnter.append("image")
            .attr("width", function(d) { return d.radius })
            .attr("height", function(d) { return d.radius })	            
            .attr("x", function(d) { return (d.shape == 'circle' ? - d.radius / 2 : d.radius / 2)})
            .attr("y", function (d) { return (d.shape == 'circle' ? - d.radius / 2 : d.radius / 2)})
            .attr("xlink:href", function(d){
            	return d.img;
            }).on("click", function(d){
	    		d.clicked(arguments);
            }).on("dragover", function(d){
	    		d.dragOver(arguments);
	    	})
	    	.on("dragleave", function(d){
	    		if(d.dragLeave){
	    			d.dragLeave(arguments);  
	    		}
	    	}) 
	    	.on("drop", function(d){
	    		d.dragDrop(arguments);
	    	})
	    	.on("dblclick.zoom", null)
	    	.on("dblclick", lang.hitch(this, this.hideChildren))	
		    
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
		    }),1000)
    	},
    	
		ticked: function() {
			this.linkSvg
		          .attr("x1", function(d) { return d.source.x; })
		          .attr("y1", function(d) { return d.source.y; })
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
			if (this.nodeSvg) {
			
				this.nodeSvg
			          .attr("transform", function(d) {
			        	  /**
			        	   * TODO: valoriser la position dans les structures json du store des noeuds
			        	   */
			        	  var result = graphStore.query({id:d.id});
			        	  var dx = d.x;
			        	  var dy = d.y;
			        	  if (d.shape == 'rect') {
			        		  dx = dx - d.radius;
			        		  dy = dy - d.radius;
			        	  }
			        	  if(result.length){
			        		  result[0].x = dx;
			        		  result[0].y = dy;
			        	  }
			        	  return "translate(" + dx + ", " + dy + ")"; 
			          });
			}
			
	    },
	    dragstarted: function(d) {
	      if (!d3.event.active) this.simulation.alphaTarget(0.3).restart()
	    },
	    dragged: function(d) { 
	      	d.fy = d3.event.y
	      	d.fx = d3.event.x
	    },
	    dragended: function(d) {   
	      d.fy = null;
	      d.fx = null;
	      if (!d3.event.active) this.simulation.alphaTarget(0);
	    },
	    /**
	     * Values est un objet clé / valeur ; Clé -> value de l'option , valeur : libellé
	     */
	    generateSelector: function(name, values, selected, disabled){
	    	var selector = '<select data-dojo-id="'+name+'" name="'+name+'" id="'+name+'"  data-dojo-type="dijit/form/Select" '+ (disabled ? 'disabled' : '') +'>';
	    	for(var key in values){
	    		selector+= '<option '+(key == selected ? 'selected="selected" ' : '') +' value="'+key+'">'+values[key]+'</option>';
	    	}
	    	selector+= '</select>';
	    	if (disabled) {
	    		selector+= '<input type="hidden" name="'+name+'" value="'+selected+'" data-dojo-type="dijit/form/TextBox"/>';
	    	}
	    	return selector;
	    },
	    generateOptionsFromQuery: function(query, store){
	    	var result = {};
	    	var queryResults = store.query(query);
	    	for(var i=0 ; i<queryResults.length ; i++){
	    		result[queryResults[i].pmb_name] = queryResults[i].name;
	    	}
	    	return result;
	    }, 
	    generateCheckbox : function(checked, disabled) {
	    	var checkBox = '<input type="checkbox" name="startScenario" value="" '+ (checked ? 'checked="checked"' : '') +' id="startScenario" data-dojo-type="dijit/form/CheckBox" '+ (disabled ? 'disabled' : '') +'/>';
	    	return checkBox;
	    },
	    
	    generatePopupScenario :function(params, isNew){
	    	if(isNew){ //Nous sommes en train de créer un nouveau scénario
	    		var popupTitle = pmbDojo.messages.getMessage('contribution_area', 'contribution_area_creating_new_scenario');
	    		params.typeScenario = "";
	    		params.name = "";
	    		params.id = "";
	    		var disabled = false;
	    	}else{	    		
	    		var popupTitle = pmbDojo.messages.getMessage('contribution_area', 'contribution_area_editing_scenario');
	    		params.typeRequested = params.entityType;
	    		params.isStartScenario = params.startScenario;
	    		params.statusRequested = params.status;
	    		var disabled = true;
	    		//var deleteButton = #code déclaratif d'un bouton supprimer 
	    	}
	    	var selectorContent = this.generateSelector('entityType', this.generateOptionsFromQuery({type:'entity'}, availableEntities),params.typeRequested, disabled);
			var popupContent = createScenarioTpl.replace('!!selector!!', selectorContent);
			popupContent = popupContent.replace("!!msg_start_scenario!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_start_scenario'));
			popupContent = popupContent.replace("!!msg_scenario_name!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_name'));
			popupContent = popupContent.replace("!!msg_scenario_validate!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_validate'));
			popupContent = popupContent.replace("!!msg_scenario_question!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_question'));
			popupContent = popupContent.replace("!!msg_scenario_comment!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_comment'));
			popupContent = popupContent.replace("!!scenarioName!!",params.name);
			popupContent = popupContent.replace("!!idScenario!!",params.id);
			popupContent = popupContent.replace("!!checkStartScenario!!",this.generateCheckbox(params.isStartScenario, disabled));
			popupContent = popupContent.replace("!!scenarioQuestion!!",params.question ? params.question : '');
			popupContent = popupContent.replace("!!scenarioComment!!",params.comment ? params.comment : '');
			var scenarioStatus = this.generateSelector('scenarioStatus', this.generateOptionsFromQuery({type:'contributionStatus'}, availableEntities),params.statusRequested);
			popupContent = popupContent.replace("!!scenarioStatus!!",scenarioStatus);
			popupContent = popupContent.replace("!!msg_scenario_status!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_status'));
			

			xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=scenario&action=get_rights_form&current_scenario="+params.id,{
				handleAs : "html"
			}).then(lang.hitch(this, function(data) {
				popupContent = popupContent.replace("!!scenarioRights!!",data);
				this.currentDialog = new Dialog({
					title: popupTitle,
					content:popupContent,
					width: '400px',
					id: 'createScenarioPopup',
					type: 'createScenario',
					onHide : function(){
						this.destroyRecursive(); 
						this.destroy();
					}
				});
				this.currentDialog.startup();
				this.currentDialog.show();
			}))
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
	    
	    hideChildren : function(d) {
	    	//console.log(d.id);
	    	
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
	    }

	});
});