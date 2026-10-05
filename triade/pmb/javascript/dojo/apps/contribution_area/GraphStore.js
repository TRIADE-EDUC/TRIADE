// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: GraphStore.js,v 1.32 2022/12/20 10:23:03 qvarin Exp $


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
		selectedNodeId: null,
		selectedPasteNodeId: null,
		isPasteAndApdate: false,
		sourceDuplicateArea: null,
		clipboardAlreadyPaste: false,
		graphShapes: null, 
		newForm: null, 
		isChildrenNode: false, 
		old_form_id: null, 
		current_form: null, 
		current_scenario: null, 
		duplicateAttachements: null, 
		constructor:function(){
			topic.subscribe('Graph',lang.hitch(this,this.handleEvents));
			topic.subscribe('GraphStore', lang.hitch(this, this.handleEvents)),
			topic.subscribe('Dialog', lang.hitch(this, this.handleEvents));
			topic.subscribe('Node', lang.hitch(this, this.handleEvents));
			topic.subscribe('FormNode', lang.hitch(this, this.handleEvents));
			topic.subscribe('FormsList', lang.hitch(this, this.handleEvents));
			topic.subscribe('SvgContextMenu', lang.hitch(this, this.handleEvents));
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
				case 'editForm' :
					this.editForm(evtArgs);
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
				case 'setSelectedNode':
					this.setSelectedNodeId(evtArgs);
					break;
				case 'pasteSelectedNode':
					this.pasteSelectedNode(evtArgs);
					break;
				case "nodeAdded":
					this.zoomOnLastPaste();
					break;
				case "pasteNodeClipboard":
					this.pasteNodeClipboard(evtArgs);
					break;
				case "editAttachment":
					this.editAttachment(evtArgs);
					this.save('refreshNodes');
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
				response : data.response,
				orderResponse : data.orderResponse,
				status : data.scenarioStatus,
				equation : data.equation ? data.equation : "" 
			};
			if (data.startScenario.length) {
				node.startScenario = true;
				node.displayed = true;
			}
			this.current_scenario = this.add(node);
			this.save('refreshNodes');
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
				node.response = params.elt.response;
				node.orderResponse = params.elt.orderResponse;
				node.equation = params.elt.equation;
			}
			var addedNode = this.add(node);
			if (params.elt.type == "scenario") {
				this.current_scenario = addedNode;
			}
			this.save();
			
			if (params.elt.type == "form") {
				this.current_form = addedNode;
				this.createTemporaryNode();
			}			
		},
		createTemporaryNode: function(){
			var formsNode = this.query({type:'form'});
			formsNode.forEach(lang.hitch(this, function(params){
				var needed = availableEntities.query({type:'property',form_id:params.eltId});
				for(var i=0 ; i<needed.total ; i++){
					if (needed[i].flag.length){
						var test = this.query({propertyPmbName: needed[i].pmb_name, parent: params.id});
						if(test.total == 0){						
							var nodeData = { 
									name : needed[i].name,
									entityType : needed[i].flag,
									type: "attachment",
									parent: params.id,
									comment:'',
									question:'',
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
							//On vérifie si le noeuds est déjà présent, si non on l'ajoute
							let nodeExisting = false;
							for (let x = 0; x < nodes.length; x++){
								if (nodes[x].id == this.data[i].id){
									nodeExisting = true;
								}
							}
							if (!nodeExisting){
								nodes.push(new AttachmentNode(this.data[i], this.graphShapes.find(function(element) {
									return element.type == 'attachment';
								})));
							}
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
			
			if (this.data.length) {
				for (var i = 0; i < this.data.length; i++) {
					if (this.data[i].name == data.name) {
						this.data[i].entityType = data.entityType
						this.data[i].status = data.scenarioStatus
						if (typeof data.equation !== undefined){
							this.data[i].equation = data.equation;
						}
					}
				}
			}
			
			data.area_id = this.area_id;
			data.data = JSON.stringify(this.data);
			data.current_scenario = 0;
			if (this.current_scenario) {
				data.current_scenario = this.current_scenario;
			}
			
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
			scenario.response = data.response;
			scenario.orderResponse = data.orderResponse;
			scenario.equation = data.equation;
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
			currentScenario.response = newProperties.response;
			currentScenario.orderResponse = newProperties.orderResponse;
			currentScenario.equation = newProperties.equation;
			return currentScenario;
		},
		editForm:function(data){
			var form = this.get(data.id);
			
			form.name = data.name;
			form.response = data.response;
			form.orderResponse = data.orderResponse;
			form.comment = data.comment;
			
			this.put(form);
		},
		editAttachment:function(data){
			var attachment = this.get(data.id);
			
			attachment.name = data.name;
			attachment.question = data.question;
			attachment.comment = data.comment;
			this.put(attachment);
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
					if (!children.includes(link.target)) {
						children.push(link.target);
					}
				}
			}));
			return children;
		},
		
		getAllChildren : function(nodeID, getNode = false) {

		    const node = this.get(nodeID);
		    var childrensList = [];
		    
		    if (node && this.hasChildren(node.id)) {
		        const childrens = graphStore.getChildren(node.id)
		        
		        
		        if (getNode) {
		            var childrensNode = [];
		            for (var i = 0; i < childrens.length; i++) {
		                childrenNode = this.get(childrens[i]);
		                childrensNode.push(childrenNode)
		            }
		            childrensList = childrensList.concat(childrensNode);
		        }else{
		            childrensList = childrensList.concat(childrens);
		        }
		        
		        for (var i = 0; i < childrens.length; i++) {
		            const childrenId = childrens[i].toString();
		            const children = this.get(childrenId);
		            if (children && this.hasChildren(children.id)) {
		                childrensList = childrensList.concat(this.getAllChildren(children.id, getNode))
		            }
		        }
		        
		    }
		    return childrensList;
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
	    
	    setSelectedNodeId : function(nodeId) {
			this.selectedNodeId = nodeId;
		},
		
		pasteSelectedNode : function(parentNodeId) {
			// On clone le noeud à copier
			var currentNode = lang.clone(this.get(this.selectedNodeId));
			
			if (this.hasChildren(currentNode.id)) {
				// On récupère tous les enfants du noeud si il y'en a
				var children = this.getChildren(currentNode.id);
			}
			
			if (currentNode.type == "scenario" && (!parentNodeId || parentNodeId == "svgGraph")) {
				// On est dans le cas où l'on copie dans le vide, on crée donc un scénario de départ
				if (!currentNode.startScenario || !currentNode.startScenario.length) {
					// Si on colle un sous-scénario sur le Graph, on force le passage en scénario par défaut
			        currentNode.startScenario = [true]; 
			        currentNode.parent_type = "";
			        currentNode.parent = "";
			    }
			    this.addScenario(currentNode);
			} else {
				// On copie les noeuds sur un scénario déjà existant
				
				if (currentNode.eltId) {
					if (currentNode.type == "form") {
						this.old_form_id = currentNode.id;
					}
					currentNode.id = currentNode.eltId;
				}
				
				// Si on fait un "coller et adapter" on vas dupliquer le formulaire
				if (this.isPasteAndApdate && currentNode.type == "form") {
					this.isChildrenNode = false;
					this.duplicateForm(currentNode, parentNodeId);
				} else {
					this.addCopyElement(currentNode, parentNodeId)
				}
	        }

			// Dans le cas ou on colle un scenario sur le graph
			// On fait une update pour avoir une position x et y du noeud créer
			topic.publish('GraphStore', 'refreshNodes');
			
			if (children && children.length > 0) {
				// Il y a des enfants, on les traite de façon récursive
				
				if (currentNode.type == "scenario") {
					currentNode = this.get(this.current_scenario);
				}
				
				if (currentNode.type == "form") {
					currentNode = this.get(this.current_form);
				}
			
				this.pasteChildrensNode(children, currentNode.id);
			}
			
			// On retire l'élément copié
			this.selectedNodeId = null;
			
			// On stock l'id du noeud sur le qu'elle on vient de coller
			this.selectedPasteNodeId = parentNodeId;
			if (!this.selectedPasteNodeId || this.selectedPasteNodeId == "svgGraph") {
				// si on colle sur le graph, on récupère le scénario courant
				this.selectedPasteNodeId = currentNode.id;
			}
		},
		
		pasteChildrensNode : function(children, parentNodeId = "") {
			// On clone le noeud parent
			var parentNode = this.get(parentNodeId);
			if (parentNode && children.length > 0) {
				for (let i = 0; i < children.length; i++) {
					var childrenId = children[i].toString();
					var childrenNode = lang.clone(this.get(childrenId));
					
					if (childrenNode.type == "attachment") {
						if (this.hasChildren(childrenId)) {
							// nextChildrensList contient les scenarios à copier
							var nextChildrensList = this.getChildren(childrenId);
							
							// nextParentList contient les nouveaux attachments
							var nextParentList = this.getChildren(parentNodeId);
							
							// Si on a qu'un seul attachment, pas besoin de boucler
							if (nextChildrensList.length == 1 && nextParentList.length == 1) {
								this.pasteChildrensNode(nextChildrensList, nextParentList[0].toString());
								continue;
							}
							
							for (let j = 0; j < nextParentList.length; j++) {
								var parentId = nextParentList[j].toString();
								var parentNode = lang.clone(this.get(parentId));
								
								// On cherche celui qui correspond à l'attachment courrent (childrenNode)
								if (childrenNode.name == parentNode.name) {
									this.pasteChildrensNode(nextChildrensList, parentId);
									break;
								}
							}
						}
					} else {
						// On formate les données du scénario/formulaire
						if (childrenNode.eltId) {
							if (childrenNode.type == "form") {
								this.old_form_id = childrenNode.id;
							}
							childrenNode.id = childrenNode.eltId;
						}

						// Si on fait un "coller et adapter" on vas dupliquer le formulaire
						if (this.isPasteAndApdate && childrenNode.type == "form") {
							this.isChildrenNode = true;
							this.duplicateForm(childrenNode, parentNode, childrenId);
						}else{
							this.addCopyChildrenNodeElement(childrenNode, parentNode, childrenId)
						}
						
					}
				}
			}
		},
		
		canPaste : function(pasteNodeId, copiedNode = null) {
			
			if (copiedNode == null) {
				copiedNode = this.get(this.selectedNodeId);
			}
			
		    // On vérifie si on a copié un noeud
		    if (copiedNode != null) {
		        if (pasteNodeId && pasteNodeId != "svgGraph") {
		            const pasteNode = this.get(pasteNodeId);

		            // Le noeud doit être présent dans le GraphStore
		            if (!pasteNode) {
		                return false;
		            }

		            // On ne peut pas coller plusieurs fois le même noeud sur un même scénario/formulaire/attachment
		            if (this.hasChildren(pasteNode.id)) {
		                const childrensList = this.getChildren(pasteNode.id);
		                
		                if (childrensList.includes(copiedNode.id)) {
		                    return false;
		                }
		                
		                // Pour les formulaires, on parcourt les enfants voir si l'accès n'a pas été vérifié
		                if (copiedNode.type == "form") {
		                    for (let i = 0; i < childrensList.length; i++) {
		                        var childrenNode = this.get(childrensList[i]);
		                        if (childrenNode.eltId == copiedNode.eltId) {
		                            return false;
		                        }
		                    }
		                }
		            }
		            
		            // On ne peut pas coller un scénario sur un autre scénario (pareil pour les formulaires)
		            if (pasteNode.type == copiedNode.type) {
		                return false;
		            }
		            
		            // On ne peut pas coller un scénario sur un formulaire
		            if (pasteNode.type == "form" && copiedNode.type == "scenario") {
		                return false;
		            }
		            
		            // On ne peut pas coller un formulaire sur un attachment
		            if (pasteNode.type == "attachment" && copiedNode.type == "form") {
		                return false;
		            }
		            
		            // On ne peut pas coller des entités qui ne correspondent pas
		            if (pasteNode.entityType != copiedNode.entityType) {
		                return false;
		            }
		        }
		        
		        // On ne peut pas coller un formulaire sur le graph, il faut un scénario de départ
		        if ((!pasteNodeId || pasteNodeId == "svgGraph") && copiedNode.type != "scenario") {
		            return false;
		        }
		        
		        // Tout est OK, on autorise le collage
		        return true;
		    }
		    
		    // Aucun noeud n'est copié
		    return false;
		},
		
		canCopy : function(nodeId) {
			
		    // Impossible de ne rien copier
		    if (!nodeId || nodeId == "svgGraph") {
		        return false;
		    }

		    const selectedNode = this.get(nodeId);

		    // Le noeud doit être présent dans le GraphStore
		    if (!selectedNode) {
		        return false;
		    }

		    // Les attachments ne peuvent pas être copiés (générés automatiquement)
		    if (selectedNode.entityType == "attachment") {
		        return false;
		    }

		    return true;
		},
		
		getLastParent : function(nodeId) {
			
			// On récupère le noeud courant
			const node = this.get(nodeId);
			if (!node) {
				// si il est pas présent dans le graphStore on fait rien
	            return false;
			}

			// on définis le "parentId"
			var parentId = node.id
			if (node.parent) {
				// si on a un parent on vas le chercher
				parentId = this.getLastParent(node.parent)
			}

			// on retourne le dernier "parentId"
			return parentId;
			
		},
		
		zoomOnLastPaste : function() {

			// Si on a pas de noeud on fait pas le zoom 
	        if (!this.selectedPasteNodeId || this.selectedPasteNodeId == "svgGraph") {
        		return false;
	        }

			// On récupère le scenario de départ
	        var lastParentNodeId = this.getLastParent(this.selectedPasteNodeId);
			topic.publish('GraphStore', 'zoomOnNode', lastParentNodeId);
			
			// Une fois que l'on a zoom sur le "lastPasteNodeId" on le vide
			this.selectedPasteNodeId = null;
		},
		
		duplicateForm : async function(childrenNode, parentNode, childrenId) {
			let url = "./ajax.php?module=modelling&categ=contribution_area&sub=form&action=duplicate&form_id="+childrenNode.eltId+"&type="+childrenNode.type;
			if (this.isPasteAndApdate) {
				url += "&duplicate_grid=1&duplicate_grid_origin_id="+childrenNode.eltId;
			}
			var data = await xhr.post(url); 
			this.newForm = JSON.parse(data);
			this.formatChildrenNode(childrenNode, parentNode, childrenId)
		},
		
		formatChildrenNode : function(childrenNode, parentNode, childrenId) {
			childrenNode.id = this.newForm.form_id
			childrenNode.name = this.newForm.name
			
			this.addAvailableEntitieForm(childrenNode)
			if (this.isChildrenNode) {
				this.addCopyChildrenNodeElement(childrenNode, parentNode, childrenId);
			} else {
				this.addCopyElement(childrenNode, parentNode);
			}
			
			this.newForm = null;
		},
		
		addCopyChildrenNodeElement : function(childrenNode, parentNode, childrenId) {
			var oldParentNode = this.get(childrenNode.parent);
			childrenNode.parent_type = oldParentNode.entityType;
			childrenNode.parent = parentNode.id;
			
			if (childrenNode.type == "scenario" && parentNode.type == "attachment") {
				childrenNode.id = childrenNode.parentScenario;
			}
			
			if (childrenNode.propertyPmbName && parentNode.type != "attachment") {
				childrenNode.pmb_name = childrenNode.propertyPmbName;
			}
			
			// On crée le nouvel élément
			this.addElement({elt: childrenNode, target: parentNode});
			topic.publish('GraphStore', 'refreshNodes');
			
			if (childrenNode.type == "form") {
				this.duplicatedComputedField();
			}
			
			if (this.hasChildren(childrenId)) {
				var nextChildrensList = this.getChildren(childrenId);
				
				// On récupère le nouveau scenario / formulaire crée
				if (childrenNode.type == "scenario") {
					nextParentId = this.current_scenario;
				}
				if (childrenNode.type == "form") {
					nextParentId = this.current_form;
				}
				
				this.pasteChildrensNode(nextChildrensList, nextParentId);
			}
		},
		
		addCopyElement : function(currentNode, parentNodeId) {
			var parentNode = this.get(parentNodeId);
			if (currentNode.parent && this.get(currentNode.parent)) {
				var oldParentNode = this.get(currentNode.parent);
			} else {
				var oldParentNode = parentNode;
			}
			
			if (!oldParentNode) {
				oldParentNode = parentNode;
			}
			
			currentNode.parent_type = oldParentNode.entityType;
			
			currentNode.parent = "";
			if (parentNodeId) {
				currentNode.parent = parentNodeId;
			}
			
			
			if (currentNode.type == "scenario" && parentNode.type == "attachment") {
				currentNode.startScenario = false;
				if (currentNode.parentScenario) {
					currentNode.id = currentNode.parentScenario;
				} else {
					currentNode.parentScenario = parentNode.id;
				}
			}
			
			if (currentNode.propertyPmbName && parentNode.type != "attachment") {
				currentNode.pmb_name = currentNode.propertyPmbName;
			}
			
			// On crée le nouveau noeud
            this.addElement({elt: currentNode, target: parentNode});
			topic.publish('GraphStore', 'refreshNodes');
            
			if (currentNode.type == "form") {
				this.duplicatedComputedField();
			}
		},
		
		addAvailableEntitieForm : function(currentNode) {
			availableEntitiesList = availableEntities.query({pmb_name:this.newForm.parent_type, type:"entity"});
			var entitieForm = {
					comment: this.newForm.comment,
					form_id: currentNode.id,
					name: currentNode.name,
					parent: availableEntitiesList[0].id,
					parent_type: this.newForm.parent_type,
					pmb_name: availableEntitiesList[0].pmb_name,
					type: this.newForm.type,
			};
			
			var temp = availableEntities.add(entitieForm);
			
			if (this.old_form_id && !this.sourceDuplicateArea) {
				var formNode = graphStore.get(this.old_form_id)
				var properties = availableEntities.query({type:'property',form_id:formNode.eltId})
			}else{
				var properties = [];
				if (currentNode.childrens.length > 0) {
					for ( var key in currentNode.childrens) {
						var attachement = currentNode.childrens[key];
						var temp_property = {
								flag: attachement.entityType,
								name: attachement.name,
								parent_type: attachement.parentType,
								pmb_name: attachement.propertyPmbName,
								type: 'property',
						};
						properties.push(temp_property);
					}
				}
			}

			for(var i=0 ; i < properties.length ; i++){
				var propertyClone = lang.clone(properties[i]);
				if (propertyClone.flag != "") {
					var new_property = {
							flag: propertyClone.flag,
							form_id: currentNode.id,
							name: propertyClone.name,
							parent_type: propertyClone.parent_type,
							pmb_name: propertyClone.pmb_name,
							type: propertyClone.type,
					};
					availableEntities.add(new_property);
				}
			}
			
			availableEntities.setData(availableEntities.data);
			topic.publish('GraphStore', 'refreshNodes');
		},
		
		duplicatedComputedField : function() {
			
			var url = "./ajax.php?module=modelling&categ=contribution_area&sub=form&action=duplicate_computed_field";
			
			if (this.sourceDuplicateArea) {
				url += "&area_id="+this.sourceDuplicateArea;
				url += "&new_area_id="+this.area_id;
			} else{
				url += "&area_id="+this.area_id;
			}
			
			url += "&form_identifier="+this.old_form_id;
			url += "&new_form_identifier="+this.current_form;
			
			xhr.post(url); 
		},
		
		getTree : function(nodeId) {
			
			const node = this.get(nodeId);
			var tree = lang.clone(node)
			tree.childrens = [];
		    
	    	const childrens = graphStore.getChildren(node.id)
	    	if (childrens.length > 0) {
	    		for (var i = 0; i < childrens.length; i++) {
	    			tree.childrens.push(this.getTree(childrens[i]))
	    		}
			}
		    
		    return tree;
		},

		pasteNodeClipboard: function(params){
			if (!this.clipboardAlreadyPaste) {
				xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=get_clipboard", {
					data: {"id_clipboard": params.clipboardId}
				})
				.then(lang.hitch(this, function(response) {
					var clipboard = JSON.parse(response);
					this.sourceDuplicateArea = clipboard.source_area_id
					this.isPasteAndApdate = clipboard.duplicate_forms
					

					if (this.canPaste(params.nodeId, clipboard.data)) {
						this.pasteNodeOfClipboard(clipboard.data, params.nodeId)
						this.deleteClipboard(params.clipboardId);
					}
				}))
			}
		},
		
		deleteClipboard: function(clipboardId){
			xhr.post("./ajax.php?module=modelling&categ=contribution_area&sub=area&action=delete_clipboard", {
				data: { "id_clipboard": clipboardId }
			})
			.then(lang.hitch(this, function(response) {
				this.clipboardAlreadyPaste = true;
			}))
		},
		
		pasteNodeOfClipboard: async function(nodeCopied, parentNodeId, isChildren = false) {
			var nextParentId = await this.createNodeCopied(nodeCopied, parentNodeId, isChildren);
			for (var i = 0; i < nodeCopied.childrens.length; i++) {
				var children = nodeCopied.childrens[i];
				this.pasteNodeOfClipboard(children, nextParentId, true)
			}
			topic.publish('GraphStore', 'refreshNodes');
		},
		
		canDeleteScenario: function(nodeId) {
		    
		    var node = this.get(nodeId)
		    var childrens = this.getChildren(node.id);
		    if (childrens.length > 0) {
		        return false;
		    }
		    
		    var nodeList = graphStore.query({eltId:node.id})
		    for (var i = 0; i < nodeList.length; i++) {
		        var nodeElt = nodeList[i];
		        var childrensElt = this.getChildren(nodeElt.id);
		        if (childrensElt > 0) {
		            return false;
		        }
		    }
		    
		    var nodeList = graphStore.query({parentScenario:node.id})
		    for (var i = 0; i < nodeList.length; i++) {
		        var nodeElt = nodeList[i];
		        var childrensElt = this.getChildren(nodeElt.id);
		        if (childrensElt > 0) {
		            return false;
		        }
		    }

		    return true;
		},
		
		createNodeCopied: async function (nodeCopied, parentNodeId, isChildren = false) {
			
			if (!isChildren) {
				if (nodeCopied.type == "scenario" && (!parentNodeId || parentNodeId == "svgGraph")) {
					
					if (!nodeCopied.startScenario || !nodeCopied.startScenario.length) {
						nodeCopied.startScenario = [true]; 
						nodeCopied.parent_type = "";
						nodeCopied.parent = "";
					}
					
					this.addScenario(nodeCopied);
				} else {
					
					if (nodeCopied.eltId) {
						if (nodeCopied.type == "form") {
							this.old_form_id = nodeCopied.id;
						}
						nodeCopied.id = nodeCopied.eltId;
					}
					
					if (this.isPasteAndApdate == "true" && nodeCopied.type == "form") {
						await this.duplicateForm(nodeCopied, parentNodeId);
					} else {
						this.addCopyElement(nodeCopied, parentNodeId);
					}
		        }
				
			} else {
				
				if (nodeCopied.type == "attachment") {
					
					let temp_current_form = this.current_form;
					this.current_form = parentNodeId;
					this.createTemporaryNode();
					
					this.current_form = temp_current_form;
					var attachements = this.getChildren(parentNodeId);
					
					for (var i = 0; i < attachements.length; i++) {
						var attachementId = attachements[i].toString();
						var attachementNode = lang.clone(this.get(attachementId));
						
						if (attachementNode.parent == parentNodeId && attachementNode.propertyPmbName == nodeCopied.propertyPmbName) {
							nextParentId = attachementNode.id;
							break;
						}
					}
				}else{
					if (nodeCopied.eltId) {
						if (nodeCopied.type == "form") {
							this.old_form_id = nodeCopied.id;
						}
						nodeCopied.id = nodeCopied.eltId;
					}
					
					if (this.isPasteAndApdate == "true" && nodeCopied.type == "form") {
						await this.duplicateForm(nodeCopied, parentNodeId);
					} else {
						this.addCopyElement(nodeCopied, parentNodeId);
					}
					
					if (nodeCopied.type == "scenario" && !this.get(nodeCopied.parentScenario)) {
						
						let node = {
							comment: "",
							displayed: "",
							entityType: nodeCopied.entityType,
							id: nodeCopied.parentScenario,
							name: nodeCopied.name,
							question: "",
							response: "",
							orderResponse: 0,
							status: "1",
							type: nodeCopied.type,
						}
						
						this.add(node);
						this.save('refreshNodes');
					}
				}
			}
			
			if (nodeCopied.type != "attachment" && !nextParentId) {
				if (nodeCopied.type == "form") {
					var nextParentId = this.current_form;
				}
				if (nodeCopied.type == "scenario") {
					var nextParentId = this.current_scenario;
				}
			}
			
			return nextParentId;
		}
	});
});