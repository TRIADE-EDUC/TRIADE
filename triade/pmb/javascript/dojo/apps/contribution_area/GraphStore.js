// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GraphStore.js,v 1.10 2018-10-16 12:06:57 apetithomme Exp $


define(["dojo/_base/declare", 
        "dojo/topic", 
        "dojo/_base/lang", 
        "dojo/store/Memory", 
        'dojo/request/xhr', 
        'dojo/json',
        "apps/contribution_area/svg/Node", 
        "apps/contribution_area/svg/FormNode", 
        "apps/contribution_area/svg/AttachmentNode", 
        "apps/contribution_area/svg/Link",
        "apps/contribution_area/svg/ScenarioNode",
        "dojo/dom-form",
        "dojo/dom"
        ], 
        function(declare, topic, lang, Memory, xhr, json,
        		SvgNode, FormNode,AttachmentNode, Link, ScenarioNode, domForm, dom){
	return declare(Memory, {
		nodes: null,
		tabID: null,
		graphShapes: null, 
		constructor:function(){
			topic.subscribe('Graph',lang.hitch(this,this.handleEvents));
			topic.subscribe('Dialog', lang.hitch(this, this.handleEvents));
			topic.subscribe('Node', lang.hitch(this, this.handleEvents));
			topic.subscribe('FormNode', lang.hitch(this, this.handleEvents));
			topic.subscribe('FormsList', lang.hitch(this, this.handleEvents));
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'addScenario' :
					this.addScenario(evtArgs);
					break;
				case 'editScenario' :
					this.editScenario(evtArgs,true);
					this.tabID = null;
					this.save('refreshNodes');
					break;
				case 'elementDropped':
					this.addElement(evtArgs);
					break;
				case 'needTemporaryNode':
//					this.createTemporaryNode(evtArgs);
					break;
				case 'formDragEnd':
//					this.query({temporary:true}).forEach(function(node){
////						this.remove(node.id);
//					},this);
//					topic.publish('GraphStore',"nodeAdded",{});
					break;
				case 'createAttachmentNode':
					this.createTemporaryNode();
					break;
			}
		},
		
		addScenario: function(data){
			var node = { 
				name : data.name,
				entityType : data.entityType,
				type : 'scenario',
				displayed : false,
				question : data.question,
				comment : data.comment,
				status : data.scenarioStatus
			};
			if (data.startScenario.length) {
				node.startScenario = true;
				node.displayed = true;
			}
			this.current_scenario = this.add(node);
			this.save();
		},
		
		addElement: function(params){
			var node = { 
				name : params.elt.name,
				entityType : params.elt.parent_type,
				type: params.elt.type,
				eltId: params.elt.id,
				parent: params.target.id,
				parentType: params.target.type,
				propertyPmbName: (params.elt.pmb_name ? params.elt.pmb_name :  params.elt.parent_type),
				displayed : true
			};
			if (params.elt.type == "scenario") {
				node.parentScenario = params.elt.id;
				node.question = params.elt.question;
				node.comment = params.elt.comment;
				node.status = params.elt.status;
			}
			var addedNode = this.add(node);
			if (params.elt.type == "scenario") {
				this.current_scenario = addedNode;
			}
			this.save();
			
			if (params.elt.type == "form") {
				this.createTemporaryNode();
			}			
		},
		createTemporaryNode: function(){
			var formsNode = this.query({type:'form'});
			formsNode.forEach(lang.hitch(this, function(params){
				var needed = availableEntities.query({type:'property',form_id:params.eltId});
				for(var i=0 ; i<needed.total ; i++){
					if (needed[i].flag) {
						var test = this.query({propertyPmbName: needed[i].pmb_name, parent: params.id});
						if(test.total == 0){						
							var nodeData = { 
									name : needed[i].name,
									entityType : needed[i].flag,
									type: "attachment",
									parent: params.id,
									parentType: needed[i].parent_type,
									temporary: false,
									destType: needed[i].flag,
									propertyPmbName: needed[i].pmb_name,
									x: (100*(i+1)),
									y: (100*(i+1))
							};
							
							this.add(nodeData);
							this.nodes.push(new AttachmentNode(nodeData));
						}
					}
				}
			}));
		},
		
		getGraphNodes : function(){
			var nodes = [];
			if (this.graphShapes) {
				for(var i=0 ; i<this.data.length ; i++){
					switch(this.data[i].type){
						case 'scenario' :
							if (this.data[i].displayed) {
								if(this.data[i].parentScenario){
									nodes.push(new ScenarioNode(this.data[i], this.graphShapes.find(function(element) {
										return element.type == 'scenario';
									})));
								}else{
									nodes.push(new ScenarioNode(this.data[i], this.graphShapes.find(function(element) {
										  return element.type == 'start_scenario';
										})
									));	
								}
							}
							break;
						case 'form': 
							nodes.push(new FormNode(this.data[i], this.graphShapes.find(function(element) {
								return element.type == 'form';
							})));
							break;
						case 'attachment': 
							nodes.push(new AttachmentNode(this.data[i], this.graphShapes.find(function(element) {
								return element.type == 'attachment';
							})));
							break;
						default :
							if (this.data[i].displayed) {
								nodes.push(new SvgNode(this.data[i], this.graphShapes));
							}
							break;
					}
				
				}
			}
			this.nodes = nodes;
			this.createTemporaryNode();
			return this.nodes;
		},
		
		getGraphLinks : function(){
			var links = [];
			for(var i=0 ; i<this.data.length ; i++){
				if(this.data[i].parent){
					links.push(new Link({id:i,source: this.data[i].parent, target: this.data[i].id,distance:10}));
				}			
			}
			return links;
		},
		
		save:function(event){
			if(!event) event = 'nodeAdded';
			var data = {};
			if (dom.byId('scenarioCreationForm')) {
				data = JSON.parse(domForm.toJson('scenarioCreationForm'));
			} else if (this.current_scenario) {
				// on envoie quelques infos du scénario courant pour le calcul des droits
				var current_scenario_data = this.query({type:'scenario', id:this.current_scenario});
				data.entityType = current_scenario_data[0].entityType;
				data.scenarioStatus = current_scenario_data[0].status;
			}
			data.area_id = this.area_id;
			data.data = JSON.stringify(this.data);
			data.current_scenario = this.current_scenario;
			xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=save_graph",{
				data : data
			}).then(function() {topic.publish('GraphStore',event,{});})
		},
		removeNodes: function(nodeID){
			if(this.nodes){
				for(var i=0 ; i<this.nodes.length ; i++){
					this.nodes[i].destroy();
					this.nodes[i] = null;
				}
			}
		},
		editScenario:function(data, isNew){
			if (isNew) {
				this.tabID = new Array();
			}
			if (this.tabID.indexOf(data.id) != -1) {
				return;
			} else {
				this.tabID.push(data.id);
			}
			var scenario = this.get(data.id);
			scenario.name = data.name;
//			scenario.entityType = data.scenarioType;
			/**
			 * Penser à ajouter l'édition de l'image et de la question ici ?!
			 */
			scenario.question = data.question;
			scenario.comment = data.comment;
			scenario.status = data.scenarioStatus;
			this.put(scenario);
			if(scenario.parentScenario){
				var parentScenario = this.get(scenario.parentScenario);
				this.editScenario(this.editScenarioProperties(parentScenario, scenario), false);
			}
			var queryResult = this.query({type:'scenario', parentScenario:scenario.id});
			queryResult.forEach(lang.hitch(this, function(scenario,subScenario){
				this.put(this.editScenarioProperties(subScenario, scenario));	
			}, scenario));	
			this.current_scenario = data.id;
		},
		editScenarioProperties: function(currentScenario,newProperties){
			currentScenario.name = newProperties.name;
			currentScenario.comment = newProperties.comment;
			currentScenario.question = newProperties.question;
			currentScenario.status = newProperties.status;
			return currentScenario;
		},
		
		hasChildren : function(nodeID) {
			var flag =  false;
			var links = this.getGraphLinks();
			switch(this.get(nodeID).type){
				case "form":
					links.forEach(lang.hitch(this, function(link){
						if(link.source == nodeID){
							if (!flag) {
								flag = this.hasChildren(link.target);
							}							
						}
					}));
					break;
				case "scenario":
				default:
					links.forEach(lang.hitch(this, function(link){
						if(link.source == nodeID){
							flag = true;
						}
					}));
					break;
			}
			return flag;
		},
		
		getChildren : function(nodeID) {
			var children = new Array();
			var links = this.getGraphLinks();
			links.forEach(lang.hitch(this, function(link){
				if(link.source == nodeID){
					children.push(link.target);		
				}
			}));
			return children;
		},

	    removeNode : function(nodeId) {
			this.getChildren(nodeId).forEach(lang.hitch(this, function(childId){
				this.remove(childId);
			}));
			if (this.get(nodeId).type == 'scenario') {
				this.current_scenario = 0;
				this.removeScenario(nodeId);
			}
			this.remove(nodeId);
	    },
	    
	    removeScenario : function(idScenario) {
			xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=scenario&action=delete&current_scenario="+idScenario);
	    },
	});
});
	/**
 * 
 */