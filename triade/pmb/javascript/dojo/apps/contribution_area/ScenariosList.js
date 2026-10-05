// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ScenariosList.js,v 1.13 2022/03/15 14:40:51 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dojo/topic", 
        "dojo/_base/lang", 
        "dijit/layout/ContentPane", 
        "dojo/dom", 
        "dojo/dom-construct", 
        "dojo/on",
        "dojo/query", 
        "dojo/dom-class",
        "dojo/dnd/Moveable",
        "dojo/dom-construct",
        "dojo/dom-class",
        "dojo/dom-style",
        "apps/contribution_area/SvgContextMenu",
    ], function(declare, topic, lang, ContentPane, dom, domConstruct, on, query, domClass, Moveable, domConstruct, domClass, domStyle, SvgContextMenu){
	return declare(ContentPane, {
		scenariosListHandler:null,
		currentParentType:null,
		currentParent:null,		
		count:null,
		selectlist:null,
		constructor: function(){
			this.scenariosListHandler = new Array();
		},
		postCreate: function(){
			this.inherited(arguments);
			this.own(
				topic.subscribe('EntitiesList', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Form', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Node', lang.hitch(this,this.handleEvents)),
				topic.subscribe('Dialog', lang.hitch(this, this.handleEvents)),
				topic.subscribe('GraphStore', lang.hitch(this, this.handleEvents)),
				topic.subscribe('Graph', lang.hitch(this, this.handleEvents))
			);
			
			this.selectlist = query('select[data-type="contribution_scenario_list"]').forEach(lang.hitch(this,function(selectNode) {
				on(selectNode, "change", lang.hitch(this, this.zoomOnScenario, selectNode));
			}));
			
			this.buildList();			
		},
		handleEvents: function(evtType,evtArgs){	
			switch(evtType){
				case "scenarioSaved" :			
					this.currentParent = evtArgs.scenario.parent;
					this.currentParentType = evtArgs.scenario.parent_type;
					this.buildList(evtArgs.scenario.id);
					break;
				case 'scenarioDeleted' :
				case 'nodeUnselected':
					this.buildList();
					break;
				case 'nodeSelected':
					this.buildList('', evtArgs.node);
					break;
				case 'addScenario' :
					this.buildList(evtArgs.entityType);
					break;
				case 'refreshNodes' :
					this.buildList();
					break;
				case 'removeOptionScenario' :
					this.removeScenariosOptions(evtArgs.scenarioId);
					break;
			}
		},
		generateScenarioLink: function (scenarioDetail, node){
			this.scenariosListHandler.push(on(node, 'click', lang.hitch(this, this.scenarioClicked, scenarioDetail)));
			this.scenariosListHandler.push(on(node, 'dragstart', lang.hitch(this, this.dragStartHandler, scenarioDetail)));
			this.scenariosListHandler.push(on(node, 'dragend', lang.hitch(this, this.dragEnd)));
		},
		scenarioClicked: function(scenario, event){
			if(domClass.contains('scenario_'+scenario.id, "selected")){
				domClass.remove('scenario_'+scenario.id,'selected');
				topic.publish('ScenariosList', 'scenarioEltUnselected', {});
			}else{
				query('#scenarios_list .scenario_line').forEach(function(node){domClass.remove(node,'selected')});
				domClass.add('scenario_'+scenario.id,'selected');
				topic.publish('ScenariosList', 'scenarioEltClicked', {scenarioElt: scenario});
				topic.publish('ScenariosList', 'scenarioEltDragStart', {scenarioElt: scenario});
			}
		},
		cleanEvents: function(){
			this.scenariosListHandler.forEach(function(item){item.remove()});
			this.scenariosListHandler = new Array();
		},
		buildList: function(selected, nodeFilter){	
			var entities = availableEntities.query({type:'entity'});
			var entitiesContainer = domConstruct.create("div", {id:'scenarios_list'});
			var divRowTitle = domConstruct.create("div", {"class":'row'}, entitiesContainer);
			var titleScenariosList = domConstruct.create("h3", {innerHTML:pmbDojo.messages.getMessage('contribution_area', 'contribution_area_scenarios_list')}, divRowTitle);
			var divRow = domConstruct.create("div", {"class":"row"}, entitiesContainer);
			var spanExpand = domConstruct.create("span", {"class":"item-expand"}, divRow);	
			var aExpand = domConstruct.create("a", {}, spanExpand);			
			var imgExpand = domConstruct.create("img",{id:"expandall", src:pmbDojo.images.getImage('expand_all.gif'),border:"0"}, aExpand);
			var divRowContext = domConstruct.create("div", {"class":'row'}, entitiesContainer);
			var context = divRowContext;

			on(imgExpand, "click",lang.hitch(this, function(context){
				expandAll(context);  
				  return false;
			}, context))
			
			var aCollapse = domConstruct.create("a", {}, spanExpand);
			var imgCollapse = domConstruct.create("img",{id:"collapseall", src:pmbDojo.images.getImage('collapse_all.gif'),border:"0"}, aCollapse);
			
			on(imgCollapse, "click",lang.hitch(this, function(context){
				collapseAll(context);  
				  return false;
			}, context))
			
			for(var i=0 ; i<entities.length ; i++){
				this.count = 0;
				var divHeader = domConstruct.create('div', {id:entities[i].pmb_name+'_div', "class":'notice-parent contribution_area_scenario'}, divRowContext);
				var divRowParent = domConstruct.create("div", {"class":"row item-expandable"}, divHeader);
				var img = domConstruct.create('img', {'class':'img_plus', id:entities[i].pmb_name+'_scenario_divImg', src:pmbDojo.images.getImage('plus.gif'), name:'imEx', border:0, hspace:3}, divRowParent);
				var entityName = entities[i].pmb_name+'_scenario_div';
								
				on(img, 'click', lang.hitch(this, this.expendBaseEntity, entityName));
				
				var divContent = domConstruct.create('div', {id:entities[i].pmb_name+'_scenario_divChild', 'class':'notice-child contribution_area_scenario', style:{marginBottom:'6px', display:'none'}}, divHeader);
				this.buildScenariosListByEntity(entities[i].pmb_name, divContent);
				var span = domConstruct.create('span', {'class':'notice-heada', innerHTML: entities[i].name+' ('+this.count+')'}, divRowParent);
				
				var divRowButton = domConstruct.create("div", {"class":"row"}, divContent);
				var divLeft = domConstruct.create("div", {"class":"left"}, divRowButton);
				var addButton = domConstruct.create("input", {type:"button","class":"bouton", name:"add_scenario_"+entities[i].pmb_name, id : "add_scenario_"+entities[i].pmb_name, value: pmbDojo.messages.getMessage('contribution_area', 'contribution_area_add')}, divLeft);
				on(addButton, 'click', 
						lang.hitch(this, this.addScenarioOpener, {
							entityName: entities[i].pmb_name,
						}));
				var divRowButtonAfter = domConstruct.create("div", {"class":"row"}, divContent);
				var divNbsp = domConstruct.create("div", {innerHTML:"&nbsp;&nbsp;"}, divRowButton);
			}			
			this.setContent(entitiesContainer);
			
			if (selected) {
				expandBase(selected + '_scenario_div', true);
			}
			
			if (nodeFilter && nodeFilter.type == "attachment") {
				for(var i = 0; i < nodeFilter.entityType.length ; i++){
					expandBase(nodeFilter.entityType[i] + '_scenario_div', true);
				}
			}
			
		},
		buildScenariosListByEntity: function(entityType, node){
			var scenarios = graphStore.query({type:'scenario', entityType:entityType});
			if(scenarios.length){
				for(var i=0 ; i<scenarios.length ; i++){
					if (!scenarios[i].parentScenario) {
						this.count++;
						var scenarioNode = domConstruct.create('div', {draggable: true, id: 'scenario_'+scenarios[i].id, innerHTML: scenarios[i].name, 'class':'scenario_line'}, node);
						new SvgContextMenu({targetNodeIds: [scenarioNode], contextType : 'scenarioList'});
						this.generateScenarioLink(scenarios[i],scenarioNode);
						this.buildScenariosOptionsByEntity(scenarios[i], entityType);
					}
				}
				return true;
			}
			return false;
		},
		dragStartHandler: function(scenarioElt, e){
			topic.publish('ScenariosList', 'scenarioEltDragStart', {scenarioElt: scenarioElt});
			e.dataTransfer.effectAllowed = 'move';
			e.dataTransfer.setData('scenario', JSON.stringify(scenarioElt));
			window.draggedContributionElt = scenarioElt;
		},
		dragEnd: function(){
			topic.publish('ScenariosList', 'scenarioDragEnd', {});
			query('#scenarios_list .scenario_line').forEach(function(node){domClass.remove(node,'selected')});
			window.draggedContributionElt = null;
		},
		toggleScenariosList: function(elementClicked){
			var alias = elementClicked.nextElementSibling;
			if(alias != null){
				if(domClass.contains(alias, 'scenario_entity_displayed')){
					domClass.remove(alias, 'scenario_entity_displayed');
					domClass.add(alias, 'scenario_entity_undisplayed');
				}else{
					domClass.remove(alias, 'scenario_entity_undisplayed'); 
					domClass.add(alias, 'scenario_entity_displayed');
				}
			}
		},
		addScenarioOpener: function(params){
			topic.publish('SvgContextMenu', 'scenarioCreationRequested', {typeRequested:params.entityName, isStartScenario:false, callback:params.callback});
		},
		expendBaseEntity: function(entityName){
			expandBase(entityName, true);
			return false;
		},
		zoomOnScenario: function (scenarioId) {
			topic.publish('ScenariosList', 'zoomOnNode', scenarioId)
		},
		buildScenariosOptionsByEntity(scenario, entityType) {
			// On ajoute que les scenario présent dans le graph
			if (scenario.displayed) {

				// Si l'optgroup est déjà présente on le récupère
				var entities = availableEntities.query({type:'entity', pmb_name:entityType});
				if (this.selectlist[0].querySelector("optgroup[label='"+entities[0].name+"']")) {
					var optGroup = this.selectlist[0].querySelector("optgroup[label='"+entities[0].name+"']")
				} else {
					var optGroup = document.createElement('optgroup');
					optGroup.label = entities[0].name;
				}
				
				// Si l'option est déjà présente on ne l'ajoute pas
				// Mais qu'il est modifié
				
				var optScen = this.selectlist[0].querySelector("option[value='"+scenario.id+"']");
				
				if (!optScen) {
					var option = document.createElement('option');
					option.value = scenario.id;
					option.innerHTML = scenario.name;
					on(option, "click", lang.hitch(this, this.zoomOnScenario, scenario.id));
					optGroup.appendChild(option);
					this.selectlist[0].appendChild(optGroup);
				}else{
					optScen.value = scenario.id;
					optScen.innerHTML = scenario.name;
				}
			}
		},
		removeScenariosOptions(scenarioId) {
			if (this.selectlist[0].querySelector("option[value='"+scenarioId+"']")) {
				var option = this.selectlist[0].querySelector("option[value='"+scenarioId+"']")
				if (option.parentNode && option.parentNode.nodeName == "OPTGROUP") {
					var optGroup = option.parentNode;
				}
				
				// On retire pas la valeur par defaut
				if (option && option.value != "") {
					this.selectlist[0].options.remove(option.index);
					
					// Si on a plus d'option dans OPTGROUP on le supprime
					if (optGroup && optGroup.children.length == 0) {
						this.selectlist[0].removeChild(optGroup);
					}
				}
			}
		}
	});
});