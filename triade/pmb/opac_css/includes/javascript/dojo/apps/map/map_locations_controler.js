// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_locations_controler.js,v 1.2 2017/09/05 08:37:29 vtouchard Exp $

const TYPE_RECORD = 11;
const TYPE_LOCATION = 15;


define(["dojo/_base/declare", "apps/pmb/PMBDialog", "dojo/dom", "dojox/widget/Standby", "dojo/dom-construct", "dojo/dom-style", "dojo/query", "dojo/request", "dojo/on", "dojo/_base/lang", "dojo/json", "dojox/geo/openlayers/widget/Map", "apps/map/dialog_notice","apps/map/map_controler"], function(declare, Dialog, dom, standby, domConstruct, domStyle, query, request, on, lang ,json, Map, DialogNotice,map_controler){
	/*
	 *Classe map_controler. C'est la classe qui va contenir l'objet openLayer associ� � une carte
	 */
	return declare("map_locations_controler", map_controler, {

		//Les param�tres du constructeur sont un noeud dom auquel sera rattach� la carte OpenLayers & un objet json repr�sentant les donn�es de l'emprises
		constructor:function(){
			this.inherited(arguments);			
		},
		
		/*
		 * C'est � cet instant que les attributs du widget son vraiment initialis�s
		 * 
		 */
		buildRendering:function(){
			this.inherited(arguments);
		},
			
		
		highlightLocation:function(e){
			var indiceFeature = e.feature.id.split('_');
			var indiceLayer = e.feature.layer.name.split('_');
			
			var listeIds = this.dataLayers[indiceLayer[indiceLayer.length-1]].holds[indiceFeature[indiceFeature.length-1]].objects['location'];
			var toHighlight = [];

			for(var i=0 ; i<listeIds.length ; i++){
				//La notice n'est pas d�j� highlight�e
				if(this.hoveredFeature.indexOf(e.feature)==-1){
					this.hoveredFeature.push(e.feature);
				}
				toHighlight.push(listeIds[i]);
				for(var type in this.data){
					for(var key in this.data[type]){
						if (key == listeIds[i]){
							for(var j = 0 ; j<this.data[type][key].length; j++){
								this.highlightElt(type + "_" + this.data[type][key][j]);
							}
						}
					}
				}
				this.highlightFeatures(this.featureByNotice[listeIds[i]], "blue");	
			}		
			
//			for(var i=0 ; i<toHighlight.length ; i++){				
//					
//				this.highlightFeatures(this.featureByNotice[toHighlight[i]], "blue");				
//			}
			
		},
		
		downlightLocation:function(e){
			var indiceFeature = e.feature.id.split('_');
			var indiceLayer = e.feature.layer.name.split('_');
			var listeIds = this.dataLayers[indiceLayer[indiceLayer.length-1]].holds[indiceFeature[indiceFeature.length-1]].objects['location'];
			var index = this.hoveredFeature.indexOf(e.feature);
			this.hoveredFeature.splice(index, 1);
			
			for(var i=0 ; i<listeIds.length ; i++){
				this.destroyEmpriseById(listeIds[i]);
				for(var type in this.data){
					for(var key in this.data[type]){
						if (key == listeIds[i]){
							for(var j = 0 ; j<this.data[type][key].length; j++){
								this.downlightElt(type + "_" + this.data[type][key][j]);
							}
						}
					}
				}			
			}
		},
		
		highlightHolds:function(idHold){
			this.highlightFeatures(this.featureByNotice[idHold], "blue");
		},
		
		
		downlightHolds:function(idHold){
			this.destroyEmpriseById(idHold);
		},
			
		
		
		
		downlightAll:function(){
			var style = {fillColor: "#0000ff", strokeColor: "#0000ff", strokeWidth: 1, fillOpacity: 0.4};
			if(this.layerHighlight==null){
				this.layerHighlight = new OpenLayers.Layer.Vector("highlight");				
				this.map.olMap.addLayer(this.layerHighlight);
		    	this.map.olMap.setLayerIndex(this.layerHighlight,200);
			}
			this.map.olMap.getLayersByName('highlight')[0].destroyFeatures();
			var divNotice = query('.notice-parent');
			for(var i=0 ; i<divNotice.length ; i++){
				if(divNotice[i].style.border!=""){
					var patternNombre = /\d+/g;
					var idNotice = divNotice[i].id.match(patternNombre)[0]; 
					domStyle.set(dom.byId('el'+idNotice+'Parent'), "border", "");
					domStyle.set(dom.byId('el'+idNotice+'Child'), "border", "");
				}
			}

		},
		
		/*
		 * Methode de surbrillance des elements dans le dom
		 * prend en param�tre un id de l'element
		 */
		highlightElt:function(idElt){
			if(dom.byId(idElt)!=null){
				domStyle.set(dom.byId(idElt), "border", "1px red solid");
			}
		},
			
		/*
		 * Methode de downbrillance des element dans le dom
		 * Prend en param�tre un id de l'element
		 */
		downlightElt:function(idElt){
			if(dom.byId(idElt)!=null){
				domStyle.set(dom.byId(idElt), "border", "");
			}
		},
		
		initFeatureByNotice:function(){
			this.featureByNotice = new Object();
			for(var i=0 ; i<this.dataLayers.length ; i++){
				for(var j=0 ; j<this.dataLayers[i].holds.length ; j++){
					if(this.dataLayers[i].holds[j].objects && this.dataLayers[i].holds[j].objects['location']){
						for(var h=0 ; h<this.dataLayers[i].holds[j].objects['location'].length ; h++){
							if(this.map.olMap.getLayersByName(this.dataLayers[i].name+'_'+i)[0].getFeatureById(i+'_feature_'+this.map.olMap.id+'_'+j)!=null){
								if(!this.featureByNotice[this.dataLayers[i].holds[j].objects['location'][h]]){
									this.featureByNotice[this.dataLayers[i].holds[j].objects['location'][h]] = new Array();
								}
								if(this.featureByNotice[this.dataLayers[i].holds[j].objects['location'][h]].indexOf(this.map.olMap.getLayersByName(this.dataLayers[i].name+'_'+i)[0].getFeatureById(i+'_feature_'+this.map.olMap.id+'_'+j))==-1){
									this.featureByNotice[this.dataLayers[i].holds[j].objects['location'][h]].push(this.map.olMap.getLayersByName(this.dataLayers[i].name+'_'+i)[0].getFeatureById(i+'_feature_'+this.map.olMap.id+'_'+j));
								}	
							}
						}				
					}
				}
			}
		},
	
		initControls:function(layer){
			this.map_controls = {};
			this.panel = {};
			switch(this.mode){
				case 'search_criteria':
					this.initPanel();
					this.initEdition(layer);	
					this.initExportEdition(layer);
					this.initImport();
					this.initPopupEdition(layer)
					this.initNavigate();
					this.initSubPanel();
					this.addToPanel();
					break;
				case 'search_result':
					this.initPanel();
					this.initToggleCluster();
					this.addToPanel();
					this.map_controls.toggleCluster.activate();
					this.initControleAffinage();
					layer.events.register("featureclick", this, this.showFeaturePage);
					layer.events.register("featureover", this, this.highlightRecord);
					layer.events.register("featureout", this, this.downlightRecord);
					if(!this.alreadyZoomed){
						layer.map.events.register("zoomend", this, this.zoomEnd);
						this.alreadyZoomed = true;
					}
					break;
				case 'visualization':
					this.initPanel();
					this.initExport(layer);
					this.initNavigate();
					this.addToPanel();
					if(this.type == TYPE_LOCATION) {
						layer.events.register("featureover", this, this.highlightLocation);
						layer.events.register("featureout", this, this.downlightLocation);
					}
					break;
				case 'edition':
					this.initPanel();
					this.initEdition(layer);
					this.initSaisieFineEtPopup(layer);
					this.initExportEdition(layer);
					this.initImport();
					this.initNavigate();
					this.initSubPanel();
					this.addToPanel();
					break;
			}
		},
		
		initEvt:function(){
			this.hidePatience();
			if(this.initialBounds){
				this.map.olMap.zoomToExtent(this.initialBounds);
				this.map.olMap.baseLayer.events.register("loadend", this, function(e){
					this.map.olMap.zoomToExtent(this.initialBounds);
					this.map.olMap.baseLayer.events.remove("loadend");
					if(this.mode == "search_result"){
						this.map.olMap.events.register("move", this, function(e){
							var arrayInitial = this.initialBoundsPrinted.toArray();
							var arrayActual = this.initialBounds.toArray();
							var condition = false;
							for(var i=0; i<arrayActual.length ; i++){
								if(arrayInitial[i]!=arrayActual[i]){
									condition = true;
								}
							}
							if(condition){
								if(document.affineRecherche == undefined){
									var bounds = this.map.olMap.calculateBounds();
									var geom = bounds.toGeometry();
									geom = geom.transform(this.projTo, this.projFrom);
									domConstruct.place("<form method='post' name='affineRecherche' action='./catalog.php?categ=search&mode=6&sub=launch'>" +
											"<input type='hidden' name='search[0]' value='s_1' />" +
											"<input type='hidden' name='search[1]' value='f_78' />" +
											"<input type='hidden' name='inter_0_s_1' value='' />" +
											"<input type='hidden' name='op_0_s_1' value='EQ' />" +
											"<input type='hidden' name='field_0_s_1[]' value='"+this.searchId+"' />" +
											"<input type='hidden' name='inter_1_f_78' value='and' />" +
											"<input type='hidden' name='op_1_f_78' value='CONTAINS' />" +
											"<input type='hidden' id='wktAffinage' name='field_1_f_78[]' value='"+this.formatWKT.extractGeometry(geom)+"' />" +
											"<input type='hidden' name='explicit_search' value='1' />" +
											"<input type='hidden' name='launch_search' value='1' />" +
											"<input type='button' class='bouton' value='" +pmbDojo.messages.getMessage("carto", "carto_btn_affiner")+ "' id='affinageButton'/></form>", dom.byId('map_search'), "after");		
									on(dom.byId('affinageButton'), 'click', lang.hitch(this, this.callbackAffinage));
									this.map.olMap.updateSize();
								}
							}
						});
					}
				});			
				
			}
			switch (this.mode){
				case 'edition':	
				case 'search_criteria':
					
					break;
				case 'visualization':
				case 'search_result':
					var callbackMapOut = lang.hitch(this, "downlightAll");
					this.initialBoundsPrinted = this.map.olMap.calculateBounds();
					for(var key in this.featureByNotice){						
						for(var type in this.data){
							for(var locId in this.data[type]){
								if (key == locId){
									for(var j = 0 ; j<this.data[type][locId].length; j++){
										if(dom.byId(type + "_" + this.data[type][locId][j])!=null){											
											on(dom.byId(type + "_" + this.data[type][locId][j]),"mouseover",lang.hitch(this, "highlightHolds",key));
											on(dom.byId(type + "_" + this.data[type][locId][j]),"mouseout",lang.hitch(this, "downlightHolds",key));
										}
									}
								}
							}
						}			
					}
					this.domNode.addEventListener("mouseleave", callbackMapOut, false);
					break;
			}
		}
	
	}); 
 });