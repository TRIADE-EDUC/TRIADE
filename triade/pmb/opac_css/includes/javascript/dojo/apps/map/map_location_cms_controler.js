// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_location_cms_controler.js,v 1.3 2022/09/23 09:50:50 tsamson Exp $

const TYPE_RECORD = 11;
const TYPE_LOCATION = 15;
define(["dojo/_base/declare", "apps/pmb/PMBDialog", "dojo/dom", "dojox/widget/Standby", "dojo/dom-construct", "dojo/dom-style", "dojo/query", "dojo/request", "dojo/on", "dojo/_base/lang", "dojo/json", "dojox/geo/openlayers/widget/Map", "apps/map/dialog_notice", "apps/map/map_location_controler"], function (declare, Dialog, dom, standby, domConstruct, domStyle, query, request, on, lang, json, Map, DialogNotice, map_location_controler) {
    return declare("map_location_cms_controler", map_location_controler, {
        constructor: function () {
        },

        buildRendering: function () {
            this.inherited(arguments);
        },
        
        initControls: function (layer) {
            this.map_controls = {};
            this.panel = {};
            switch (this.mode) {
                case 'cms' :
                    this.initToggleCluster();
                    this.initNavigate();
                    this.map_controls.toggleCluster.activate();
                    this.initControleAffinage();
                        layer.events.register("featureover", this, this.highlightLocation);
                        layer.events.register("featureout", this, this.downlightLocation);
    					layer.events.register("featureclick", this, this.showFeaturePage);
                    if (!this.alreadyZoomed) {
                        layer.map.events.register("zoomend", this, this.zoomEnd);
                        this.alreadyZoomed = true;
                    }
                    break;
            }
        },
        
        highlightLocation: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var type = this.dataLayers[indiceLayer[indiceLayer.length - 1]].type;
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects[type];
            var toHighlight = [];
            
            for (var i = 0; i < listeIds.length; i++) {
                if (this.hoveredFeature.indexOf(e.feature) == -1) {
                    this.hoveredFeature.push(e.feature);
                }
                toHighlight.push(listeIds[i]);
                this.highlightFeatures(this.featureByNotice[listeIds[i]], "blue");
            }
        },
        
        highlightFeatures:function(arrayFeature, color) {
        	if (arrayFeature[1]) {
        		arrayFeature = arrayFeature[0];
        	}
			if(arrayFeature){
				if(this.layerHighlight==null){
					this.layerHighlight = new OpenLayers.Layer.Vector("highlight");				
					this.map.olMap.addLayer(this.layerHighlight);
			    	this.map.olMap.setLayerIndex(this.layerHighlight,100);
				}
				var style = {fillColor: color, strokeWidth: 1, strokeColor: color, fillOpacity: 0.7, title: ''};
				for(var i=0 ; i<arrayFeature.length ; i++){
                	//on utilise la fonction native de l'objet feature
                	//c'est plus propre pour la duplication des components (pas de conflit d'id, pas d'emprise qui disparait)
					var clonedFeature = arrayFeature[i].clone();
					var numLayer = arrayFeature[i].layer.name.split('_');
					var numFeature = arrayFeature[i].id.split('_');		
					clonedFeature.id = numLayer[numLayer.length-1]+'_'+numFeature[numFeature.length-1];				
					if(clonedFeature.geometry.CLASS_NAME == "OpenLayers.Geometry.Point"){
						style.pointRadius = arrayFeature[i].style.pointRadius;
					}
					clonedFeature.style = style;
					this.map.olMap.getLayersByName('highlight')[0].addFeatures([clonedFeature]);
				}
			}
		},
       
        downlightLocation: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var type = this.dataLayers[indiceLayer[indiceLayer.length - 1]].type;       
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects[type];
            var index = this.hoveredFeature.indexOf(e.feature);
            this.hoveredFeature.splice(index, 1);
            
            for (var i = 0; i < listeIds.length; i++) {
                this.destroyEmpriseById(listeIds[i]);
            }
            for (var i = 0; i < listeIds.length; i++) {
                this.destroyEmpriseById(listeIds[i]);
            }
        },
       
		showFeaturePage:function(e){
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var type = this.dataLayers[indiceLayer[indiceLayer.length - 1]].type;
            var type_record = this.dataLayers[indiceLayer[indiceLayer.length - 1]].type_record;
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects[type];
            
            if (listeIds.length >= 1) {
				if (this.popup == null) {
					this.popup = new DialogNotice({
				        style: "width: 900px; height:auto;",
				    });
				}
				// Pas correct puisqu'on appelle categ notice pour une authorité..
				this.popup.ajaxUrl = "./ajax.php?module=ajax&categ=notice&show_map=0&show_expl=1&show_explnum=1&popup_map=1&type=" + type_record + "&id=";
				for (var i = 0; i < listeIds.length; i++) {
					if (!this.popup.checkPresence(listeIds[i])) {
						this.popup.addNotice(listeIds[i]);
						this.popup.show();
				    } else {
						this.popup.show();
					}	
				}
			}
		},
		     
        zoomEnd: function (event) {
            if (this.cluster) {
                this.showPatience();
                var bounds = this.map.olMap.calculateBounds();
                var geom = bounds.toGeometry();
                geom = geom.transform(this.projTo, this.projFrom);
                this.nbLayersReceived = 0;
                for (var j = 0; j < this.dataLayers.length; j++) {
                    var currentLayer = this.map.olMap.getLayersByName(this.dataLayers[j].name + "_" + j)[0];
                    var textRoot = currentLayer.renderer.textRoot;
                    for (var h = textRoot.children.length - 1; h > 0; h--) {
                        if (textRoot.children[h] != undefined)
                            textRoot.removeChild(textRoot.children[h]);
                    }
                }
                if (this.featuresByZoom && this.featuresByZoom[event.object.zoom]) {
                    for (var i = 0; i < this.dataLayers.length; i++) {
                        this.printByZoomLevel(event.object.zoom, i);
                    }
                } else {
                    for (var i = 0; i < this.dataLayers.length; i++) {
                        var callbackHolds = lang.hitch(this, "callbackCluster", i, event.object.zoom);
                        request.post(this.dataLayers[i].data_url, {
                            'data': "indice=" + i + "&wkt_map_hold=" + geom + "&zoom_level=" + event.object.zoom + "&cluster=" + this.cluster,
                            'handleAs': "json",
                        }).then(callbackHolds);
                    }
                }
            }
        },   
        
        callbackCluster: function (i, zoomLevel, data) {
            if (!this.featuresByZoom) {
                this.featuresByZoom = {};
            }
            if (!this.featuresByZoom[zoomLevel]) {
                this.featuresByZoom[zoomLevel] = {};
            }
            if (!this.featuresByZoom[zoomLevel][i]) {
                this.featuresByZoom[zoomLevel][i] = new Array();
                for (var j = 0; j < data.length; j++) {
                    var styleEmprise = {
                        strokeWidth: 2,
                        strokeColor: this.dataLayers[i].color,
                        fillOpacity: 1, //0.4
                        fillColor: this.dataLayers[i].color
                    };
                    var featureI = this.formatWKT.read(data[j].wkt);
                    featureI.style = styleEmprise;
                    var dataLoc = "";
                    var notices_number = 0;
                    if (data[j].objects["record"]) {   
                        notices_number = data[j].objects["record"].length;                                                           
                    }
                    if (notices_number || 1) {
                        if (featureI.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
                            featureI.style.pointRadius = 8;
                            if(notices_number) {
                                featureI.style.label = String(notices_number);
                            }
                            if (notices_number > 20) {
                                featureI.style.pointRadius = 14;
                                if (notices_number > 100) {
                                    featureI.style.pointRadius = 20;
                                }
                            }
                        }
                        featureI.records_ids = data[j].objects["record"];
                        featureI.attributes.records_length = data[j].objects["record"].length;
                        featureI.attributes.class = featureI.geometry.CLASS_NAME;
                        featureI.id = i + "_" + "feature_" + this.map.olMap.id + "_" + j;
                        featureI.geometry.transform(this.projFrom, this.projTo);
                        
                        this.featuresByZoom[zoomLevel][i].push(featureI);
                    }
                }
            }
            this.printByZoomLevel(zoomLevel, i);
        },
    });
});