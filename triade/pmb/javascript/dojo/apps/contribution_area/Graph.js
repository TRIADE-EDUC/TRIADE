// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Graph.js,v 1.38 2024/03/05 15:26:16 dgoron Exp $

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
        "dojo/dom-construct",
        "dijit/registry",
        'dojo/dom-attr',
        "dojo/text!apps/contribution_area/templates/createForm.html",
        "dojo/text!apps/contribution_area/templates/duplicateScenario.html",
        "dojo/text!apps/contribution_area/templates/editAttachment.html",
        "dojo/dom-style",
    ], 
	function(declare, ContentPane, d3, on, lang, topic, mouse, dom, dojoEvent, SvgContextMenu, ScenarioNode, Dialog, createScenarioTpl, FormNode, Link, topic, query, ConfirmDialog, xhr, domConstruct, registry, domAttr, createFormTpl, duplicateScenarioTpl, editAttachmentTpl,domStyle){
	
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
		zoom: null,
		constructor: function(){
			this.generateLegend();
			randomizer = function(){return Math.floor(Math.random() * (10000 - 10)) + 10;};
			this.formsListHandler = new Array();
			this.own(
				topic.subscribe('SvgContextMenu', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Dialog', lang.hitch(this, this.handleEvents)),
				topic.subscribe('FormsList', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Node', lang.hitch(this, this.handleEvents)),
				topic.subscribe('FormNode', lang.hitch(this, this.handleEvents)),
				topic.subscribe('GraphStore', lang.hitch(this, this.handleEvents)),
				topic.subscribe('ScenariosList', lang.hitch(this, this.handleEvents))
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
				case "formEditionRequested":
					var form = graphStore.get(evtArgs.nodeID);
					this.generatePopupForm(form, false);
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
				case "zoomOnNode":
					var node = graphStore.get(evtArgs);
					this.zoomOnNode(node);
					break;
				case "pasteToArea":
					var node = graphStore.get(evtArgs);
					this.generatePopupPasteToOtherAreaScenario(node);
					break;
				case "duplicateScenarioToOtherContributionArea":
					this.duplicateToOtherAreaScenario(evtArgs);
					break;
				case "duplicateScenarioToOthercontributionAreaAndGo":
					this.duplicateScenarioToOthercontributionAreaAndGo(evtArgs);
					break;
				case "formAttachmentRequested":
					var attachment = graphStore.get(evtArgs.nodeID);
					this.generatePopupAttachment(attachment, false);
					break;	
				case "deleteScenario":
					this.deleteScenario(evtArgs);
					break;
			}
		},
		postCreate: function(){
			this.inherited(arguments);
			
			// Zoom
			this.zoom = d3.zoom().scaleExtent([0.2, 7]).on("zoom", lang.hitch(this, this.zoomed));
			
			// Container
		    this.svg = d3.select(this.domNode).append("svg")
		        .attr("width", this.width)
		        .attr("height", this.height)
		        .attr("id", "svgGraph")
//		        .attr("shape-rendering", "crispEdges")
		        .call(this.zoom)
		        .on("wheel.zoom", null)
		        .append("g")
		        .attr("id", "svgContainer")
		        .attr("transform", "translate(40,0) scale(0.1)");
		    
		    var svgSizes = d3.select('svg').node().getBBox();
		    
		    // Event click contribution_resize_button
			d3.select('button[data-type="contribution_resize_button"]').on("click", lang.hitch(this, this.resetTheGraph));
			// Event click contribution_zoom_in_button
			d3.select('button[data-type="contribution_zoom_in_button"]').on("click", lang.hitch(this, this.zoomIn));
			// Event click contribution_zoom_out_button
			d3.select('button[data-type="contribution_zoom_out_button"]').on("click", lang.hitch(this, this.zoomOut));
		    
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

		    this.contextMenu = new SvgContextMenu({targetNodeIds: ['svgGraph']});
		},
		startup: function () {
			this.update();
			this.resetTheGraph()
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
		    			var value = d.name
			    		if (d.type == "form") {
							var form = graphStore.get(d.id);
							if (form) {
								var realform = availableEntities.query({type:"form",form_id:form.eltId});
								if (realform[0] && realform[0].name) {
									value += " ( "+realform[0].name+" )";
								}
							}
						}
		    			domNode.nextElementSibling.innerHTML = value;
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
		    	.style("cursor", "pointer")
		    	.append("title")
		    	.text(function(d) { return d.name; })
		            
		    nodeEnter.append("text")
		    	.attr("dy", function(d) {return (d.shape == 'circle' ? 3 : d.radius + 3)})
		    	.attr("x", function(d) {return (d.shape == 'circle' ? d.radius+3 : d.radius * 2 + 3)})
		    	.attr("data-circle-id", function(d) { return d.id; })
		    	.attr("data-type", function(d) { return d.type; })
			    .style("cursor", "pointer")
		    	.style("text-anchor", function(d) { return d.children ? "end" : "start"; })
		    	.text(function(d) {
		    		var value = d.name
		    		if (d.type == "form") {
						var form = graphStore.get(d.id);
						if (form) {
							var realform = availableEntities.query({type:"form",form_id:form.eltId});
							if (realform[0] && realform[0].name) {
								value += " ( "+realform[0].name+" )";
							}
						}
					}
		    		return value; 
	    		});
		        
		    nodeEnter.append("image")
		    .style("cursor", "pointer")
            .attr("width", function(d) { return d.radius })
            .attr("height", function(d) { return d.radius })	            
            .attr("x", function(d) { return (d.shape == 'circle' ? - d.radius / 2 : d.radius / 2)})
            .attr("y", function (d) { return (d.shape == 'circle' ? - d.radius / 2 : d.radius / 2)})
	    	.attr("data-type", function(d) { return d.type; })
	    	.attr("data-circle-id", function(d) { return d.id; })
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
	    	var selector = '<select name="'+name+'" id="'+name+'" '+ (disabled ? 'disabled' : '') +'>';
	    	for(var key in values){
	    		var value = values[key];
    			selector+= '<option '+(key == selected ? 'selected="selected" ' : '') +' value="'+key+'">'+value.name+'</option>';
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
	    		result[queryResults[i].pmb_name] = {
	    				"name": queryResults[i].name,
	    				"type": queryResults[i].type
	    		}
	    		
	    		if (queryResults[i].type == "contributionStatus") {
	    			result[queryResults[i].pmb_name]["available_for"] = queryResults[i].available_for
				}
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
	    	
	    	//Recupération des scenario
	    	xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=equation&action=get_list&type="+params.entityType).then(lang.hitch(this, function(data) {
                
                var defaultList = {0:{name :pmbDojo.messages.getMessage('contribution_area','contribution_area_no_equation')}}; 
                var equationsList = Object.assign(defaultList, JSON.parse(data));
		    	var selectorContent = this.generateSelector('entityType', this.generateOptionsFromQuery({type:'entity'}, availableEntities),params.typeRequested, disabled);
				var popupContent = createScenarioTpl.replace('!!selector!!', selectorContent);
				if (params.id){
					var selectorEquation = '<br/><label for="equation">!!msg_form_equation!!</label>';
					selectorEquation += this.generateSelector('equation',  equationsList, params.equation ? params.equation : 0);
					popupContent = popupContent.replace("!!formEquation!!",selectorEquation);
					popupContent = popupContent.replace("!!msg_form_equation!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_equation'));
				} else {
					popupContent = popupContent.replace("!!formEquation!!",'');
				}
				
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
				var scenarioStatus = this.generateSelector('scenarioStatus', this.generateOptionsFromQuery({type:'contributionStatus'}, availableEntities), params.statusRequested, false);
				popupContent = popupContent.replace("!!scenarioStatus!!",scenarioStatus);
				popupContent = popupContent.replace("!!msg_scenario_status!!",pmbDojo.messages.getMessage('contribution_area','contribution_area_status'));
				
		    	popupContent = popupContent.replace("!!msg_form_response!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_response'));
				popupContent = popupContent.replace("!!formResponse!!",params.response ? params.response : '');
		    	
		    	popupContent = popupContent.replace("!!msg_order_response!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_order_response_label'));
				popupContent = popupContent.replace("!!formOrderResponse!!",params.orderResponse ? params.orderResponse : '');
	
	
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
					this.currentDialog.on("show", lang.hitch(this, this.updateScenarioStatus));
					this.currentDialog.show();
				}));
	    	}))
	    },
	    generatePopupForm :function(form, isNew){
	    	// Edition d'un formulaire dans le graph
    		var popupTitle = pmbDojo.messages.getMessage('contribution_area', 'contribution_area_editing_form');
    		form.typeRequested = form.entityType;
    		form.isStartScenario = form.startScenario;
    		form.statusRequested = form.status;
    		
    		var formNode = availableEntities.query({"type":"form", "form_id":form.eltId});

    		var popupContent = createFormTpl.replace("!!msg_form_name!!", pmbDojo.messages.getMessage('contribution_area','admin_contribution_area_name_form_fields_opac'));
	    	popupContent = popupContent.replace("!!msg_form_comment!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_comment'));
	    	popupContent = popupContent.replace("!!msg_form_validate!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_validate'));
	    	popupContent = popupContent.replace("!!formName!!", form.name);
	    	popupContent = popupContent.replace("!!idForm!!", form.id);
	    	popupContent = popupContent.replace("!!formComment!!", form.comment ? form.comment : '');
	    	popupContent = popupContent.replace("!!admin_contribution_area_form_name!!", pmbDojo.messages.getMessage('contribution_area','admin_contribution_area_form_name'));
	    	popupContent = popupContent.replace("!!admin_contribution_area_edit_form_fields!!", pmbDojo.messages.getMessage('contribution_area','admin_contribution_area_edit_form_fields'));
	    	popupContent = popupContent.replace("!!idForm!!", form.id);
	    	popupContent = popupContent.replace("!!formNameValue!!", formNode[0].name);
	    	popupContent = popupContent.replace("!!formType!!", form.entityType);
	    	popupContent = popupContent.replace("!!formId!!", form.eltId);

	    	popupContent = popupContent.replace("!!msg_form_response!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_response'));
	    	popupContent = popupContent.replace("!!formResponse!!", form.response ? form.response : '');
	    	
	    	popupContent = popupContent.replace("!!msg_order_response!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_order_response_label'));
			popupContent = popupContent.replace("!!formOrderResponse!!",form.orderResponse ? form.orderResponse : '');
	    	
	    	
	    	this.currentDialog = new Dialog({
	    		title: popupTitle,
	    		content:popupContent,
	    		width: '400px',
	    		id: 'createFormPopup',
	    		type: 'createForm',
	    		onHide : function(){
	    			this.destroyRecursive(); 
	    			this.destroy();
	    		}
	    	});
	    	this.currentDialog.startup();
			this.currentDialog.on("show", lang.hitch(this, function() {
				var button = dom.byId('formEditField');
				on(button, "click", function(evt){
					evt.stopPropagation();
					evt.preventDefault();
					
					var url = "./modelling.php?categ=contribution_area&sub=form&type="+form.entityType+"&action=edit&form_id="+form.eltId;
					if(evt.ctrlKey){
						window.open(url, "_blank")
					}else{
						document.location = url;
					}
				})
			}));
	    	this.currentDialog.show();
	    },
	    generatePopupAttachment :function(attachment, isNew){
	    	// Edition d'un attachment dans le graph
    		var popupTitle = pmbDojo.messages.getMessage('contribution_area', 'contribution_area_editing_attachment');
	    	var popupContent = editAttachmentTpl.replace("!!msg_attachment_question!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_question'));
	    	popupContent = popupContent.replace("!!msg_form_comment!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_comment'));
	    	popupContent = popupContent.replace("!!msg_form_validate!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_validate'));
	    	popupContent = popupContent.replace("!!formName!!", attachment.name);
	    	popupContent = popupContent.replace("!!idForm!!", attachment.id);
	    	popupContent = popupContent.replace("!!formType!!", attachment.entityType);	    	
			popupContent = popupContent.replace("!!attachmentQuestion!!",attachment.question ? attachment.question : '');
			popupContent = popupContent.replace("!!attachmentComment!!", attachment.comment ? attachment.comment : '');
			
	    	this.currentDialog = new Dialog({
	    		title: popupTitle,
	    		content:popupContent,
	    		width: '400px',
	    		id: 'editAttachmentPopup',
	    		type: 'createAttachment',
	    		onHide : function(){
	    			this.destroyRecursive(); 
	    			this.destroy();
	    		}
	    	});
	    	this.currentDialog.startup();
	    	this.currentDialog.show();
	    },
	    updateScenarioStatus : function () {
	    	on(dom.byId('entityType'), "change", lang.hitch(dom.byId('scenarioStatus'), function(entityType){
				
	    		
	    		var entitySelected = entityType.target.value;
            	var params = availableEntities.query({type:'contributionStatus'})
            	for (var i = 0; i < this.options.length; i++) {
            		
            		var option = this.options[i];
            		var optionParams = params[i].available_for;
					
					if (optionParams && !optionParams.includes(entitySelected)) {
						option.disabled = true;
					}else{
						option.disabled = false;
					}
				}
            	
			}));
	    },
	    hasDraft : async function(node) {
	        var nodeHasDraft = false
	        if (node && node.type == "form") {
	            response = await xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub="+node.type+"&action=check_draft&uri="+node.id);
	            nodeHasDraft = JSON.parse(response);
	        }
	        return nodeHasDraft;
	    },
	    removeNode : async function(nodeID) {
	    	var node = graphStore.get(nodeID); 
	    	var hasDraftNode = await this.hasDraft(node);
	    	
	        if (!hasDraftNode) {
	            var confirmDialog = new ConfirmDialog({
	                title : node.name, 
	                content : pmbDojo.messages.getMessage('contribution_area','contribution_area_confirm_deleting'), 
	                onExecute : lang.hitch(this, function(){
	                    graphStore.removeNode(nodeID);        
	                    graphStore.save();
	                    this.update();
	                    topic.publish('Graph', 'removeOptionScenario', {scenarioId:nodeID});
	                })
	            });
	            
	        } else {
	        	var confirmDialog = new ConfirmDialog({
	        		title : node.name, 
	        		content : pmbDojo.messages.getMessage('contribution_area','contribution_has_draft_popup'), 
	        	});
	        	//Suppression du bouton cancel
	        	let cancelBtnDom = confirmDialog.cancelButton.domNode;
	        	domStyle.set(cancelBtnDom, 'display', 'none');
	        } 	    	
	        //On redimensionne pour ne pas utiliser le viewport du navigateur
	        let dimension  = {
	        		w: 425,
	        		h: 250,
	        };
	        
	        confirmDialog.set("dim", dimension)
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
	    },
	    resetTheGraph: function() {
	    	var max_x = 0;
	    	var min_x = 0;

	    	var max_y = 0;
	    	var min_y = 0;
	    	
			var nodes = graphStore.getGraphNodes();
			
			for (var i = 0; i < nodes.length; i++) {
			    var x = nodes[i].x;
			    var y = nodes[i].y;
			    
			    if (x > max_x) {
					max_x = x;
				}else if(x < min_x){
					min_x = x;
				}

			    if (y > max_y) {
			    	max_y = y;
			    }else if(y < min_y){
			    	min_y = y;
			    }
			}
			
			var svgGraph = d3.select("#svgGraph");
			var width = svgGraph._groups[0][0].clientWidth;
			var height = svgGraph._groups[0][0].clientHeight;
			
			widthBox = ((max_x - min_x) / 2 ) + min_x;
			heightBox = ((max_y - min_y) / 2 ) + min_y;
			
			const scaleSize = Math.min(0.4, 1 / Math.max((max_x - min_x) / width, (max_y - min_y) / height));
			
			this.setZoom(widthBox, heightBox, scaleSize);
			
		},
		setZoom: function(x, y, scale) {
			var svgGraph = d3.select("#svgGraph");
			
			width = svgGraph._groups[0][0].clientWidth;
			height = svgGraph._groups[0][0].clientHeight;
			
			svgGraph.transition().duration(2500).call(
				this.zoom.transform,
				d3.zoomIdentity.translate(width/2, height/2).scale(scale).translate(-x, -y)
		    );
		},
		zoomIn: function() {
			var svgGraph = d3.select("#svgGraph");
			// duration = durée de l'animation
			svgGraph.transition().duration(1500).call(this.zoom.scaleBy, 2);
		},
		zoomOut: function() {
			var svgGraph = d3.select("#svgGraph");
			// duration = durée de l'animation
			svgGraph.transition().duration(1500).call(this.zoom.scaleBy, 0.5);
		},
		zoomOnNode: function(node) {
			if (node && node.x && node.y) {
				var boundingBox = this.getBoundingBoxOnScenario(node.id);
				this.setZoom(boundingBox.x, boundingBox.y, boundingBox.scale);
			}
		},
		getBoundingBoxOnScenario: function(scenarioId) {
			const scenarioNode = graphStore.get(scenarioId);
			
			if (scenarioNode && (scenarioNode.x && scenarioNode.y)) {
				
				var xMax = scenarioNode.x;
				var xMin = scenarioNode.x;
				
				var yMax = scenarioNode.y;
				var yMin = scenarioNode.y;
				
				if (graphStore.hasChildren(scenarioNode.id)) {
					const childrensList = graphStore.getAllChildren(scenarioNode.id)
					
					for (var i = 0; i < childrensList.length; i++) {
						var childrenId = childrensList[i].toString();
						var childrenNode = graphStore.get(childrenId);
						if (childrenNode && (childrenNode.x && childrenNode.y)) {
							
							if (childrenNode.x > xMax) {
								xMax = childrenNode.x
							} else if (childrenNode.x < xMin) {
								xMin = childrenNode.x
							}
							
							if (childrenNode.y > yMax) {
								yMax = childrenNode.y
							} else if (childrenNode.y < yMin) {
								yMin = childrenNode.y
							}
						}
					}
				}
				

				var svgGraph = d3.select("#svgGraph");
				var width = svgGraph._groups[0][0].clientWidth;
				var height = svgGraph._groups[0][0].clientHeight;
				
				const xSize = ( (xMax - xMin)/2 ) + xMin;
				const ySize = ( (yMax - yMin)/2 ) + yMin;
				const defaultScale = 1.75;
				var scaleSize = Math.min(defaultScale, 0.4 / Math.max((xMax - xMin) / width, (yMax - yMin) / height));
				
				if (scaleSize > defaultScale) {
					scaleSize = defaultScale;
				}
				
				
				return {
					x: xSize,
					y: ySize,
					scale: scaleSize
				}
				
			}
		},
		
		generateSelectorArea: function(listArea){
		    var selector = '<select id="contributionArea" name="contributionArea">';
		    for ( var index in listArea) {
		    	if (index != "total") {
			        var area = listArea[index];
			        if (area.id_area != graphStore.area_id) {
			        	selector += '<option value="'+area.id_area+'">'+area.area_title+'</option>';
		        	}
		    	}
		    }
		    selector+= '</select>';
		    return selector;
		},
		
		generatePopupPasteToOtherAreaScenario: function(node){
	    	// Copie d'un scénario dans un autre espace
    		var popupTitle = pmbDojo.messages.getMessage('contribution_area', 'contribution_area_paste_to_other_contribution_area');

			xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=list",{
			}).then(lang.hitch(this, function(response) {
				var listArea = JSON.parse(response); 
				
		    	var popupContent = duplicateScenarioTpl.replace("!!msg_contribution_area_list!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_list_area'));
		    	popupContent = popupContent.replace("!!contribution_area_options_list!!", this.generateSelectorArea(listArea));
		    	popupContent = popupContent.replace("!!scenarioId!!", node.id);
		    	popupContent = popupContent.replace("!!msg_scenario_validate!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_validate'));
		    	popupContent = popupContent.replace("!!msg_duplicate_forms!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_duplicate_forms'));
		    	popupContent = popupContent.replace("!!msg_scenario_validate_and_go!!", pmbDojo.messages.getMessage('contribution_area','contribution_area_validate_and_go'));
		    	popupContent = popupContent.replace("!!msg_contribution_area_duplication!!", pmbDojo.messages.getMessage('contribution_area','information_duplication_form'));
		    	
		    	this.currentDialog = new Dialog({
		    		title: popupTitle,
		    		content:popupContent,
		    		width: '200px',
		    		id: 'duplicateScenarioPopup',
		    		type: 'DuplicateScenarioForm',
		    		onHide : function(){
		    			this.destroyRecursive(); 
		    			this.destroy();
		    		}
		    	});
		    	this.currentDialog.startup();
		    	this.currentDialog.show();
			}))
		}, 
		
		duplicateToOtherAreaScenario: function(formValues){
		    var duplicate = false;

		    if (formValues.duplicateForms) {
		        duplicate = true;
		    }

		    var dataList = graphStore.getAllChildren(formValues.scenarioId, true);
		    dataList.push(graphStore.get(formValues.scenarioId));
		    
		    var postData = {
		        scenario_id: formValues.scenarioId,
		        area_id: formValues.contributionArea,
		        source_area_id: graphStore.area_id,
		        duplicate_forms: duplicate,
		        data: JSON.stringify(dataList)
		    }
		    
		    xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=duplicate_scenario", {
		        data: postData
		    })
		},
		
		duplicateScenarioToOthercontributionAreaAndGo: function (formValues){
		    var duplicate = false;

		    if (formValues.duplicateForms) {
		        duplicate = true;
		    }

		    var dataList = graphStore.getTree(formValues.scenarioId);
		    
		    var postData = {
		        scenario_id: formValues.scenarioId,
		        area_id: formValues.contributionArea,
		        source_area_id: graphStore.area_id,
		        duplicate_forms: duplicate,
		        data: JSON.stringify(dataList)
		    }
		    
		    xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=clipboard", {
		        data: postData
		    })
		    .then(lang.hitch(this, function(response) {
		    	data = JSON.parse(response)
		        document.location = "./modelling.php?categ=contribution_area&sub=area&action=define&id="+formValues.contributionArea+"&id_clipboard="+data.id
		    }))
		},
		
		generateLegend: function (){
			// select the svg area
			var svgLegend = d3.select("#graph_legend_svg")

			var x = 10,
				y = 5,
				fontSize = 15, // taile du text "px"
				rectSize = 20, // taile du rectangle
				space = 25; // espace entre les noeuds
			
			// Nombre max dans une colones
			var itemMaxDefault = 2;
			var currentNbrItem = 0;
			
			// Make legend
			for (var i = 0; i < graphStore.graphShapes.length; i++) {
				
				var item = graphStore.graphShapes[i],
					size = parseInt(item.size);
				
				if (currentNbrItem == itemMaxDefault) {
					currentNbrItem = 0;
					x += 200; // On ajoute 200 pour faire la 2eme colonne
					y = 5;
				}
				
				if (currentNbrItem > 0) {
					y += space;
				}
				
				// Couleur
				svgLegend.append("rect")
				.attr("width", rectSize)
				.attr("height", rectSize)	            
				.attr("x", x)
				.attr("y", y)
				.attr("r", size)
				.style("fill", item.color);
				
				// legend
				var message = pmbDojo.messages.getMessage('contribution_area', 'node_type_'+item.type);
				svgLegend.append("text")
				.attr("x", x+rectSize)
				.attr("y", y+fontSize)
				.style("font-size", fontSize+"px")
				.text(message)
				
				currentNbrItem++;
			}
		},
		
		deleteScenario: function(nodeId){
			if (!graphStore.canDeleteScenario(nodeId)){
				return false;
			}
				    
		    var nodeList = graphStore.query({eltId:nodeId})
		    for (var i = 0; i < nodeList.length; i++) {
		        var nodeElt = nodeList[i];
		        graphStore.removeNode(nodeElt.id);
			}
		    
		    var nodeList = graphStore.query({parentScenario:nodeId})
		    for (var i = 0; i < nodeList.length; i++) {
		        var nodeElt = nodeList[i];
		        graphStore.removeNode(nodeElt.id);
		    }

		    graphStore.removeNode(nodeId);
		    graphStore.save('refreshNodes');
		},
		
	});
});