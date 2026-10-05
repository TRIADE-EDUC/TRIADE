// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_location_facette_controler.js,v 1.3 2017/09/12 09:48:22 jpermanne Exp $

const TYPE_RECORD = 11;
const TYPE_LOCATION = 15;
define(["dojo/_base/declare", "apps/pmb/PMBDialog", "dojo/dom", "dojox/widget/Standby", "dojo/dom-construct", "dojo/dom-style", "dojo/query", "dojo/request", "dojo/on", "dojo/_base/lang", "dojo/json", "dojox/geo/openlayers/widget/Map", "apps/map/dialog_notice", "apps/map/map_location_controler"], function (declare, Dialog, dom, standby, domConstruct, domStyle, query, request, on, lang, json, Map, DialogNotice, map_location_controler) {
    /*
     *Classe map_controler. C'est la classe qui va contenir l'objet openLayer associe a une carte
     */
    return declare("map_location_facette_controler", map_location_controler, {
        // Les param�tres du constructeur sont un noeud dom auquel sera rattach� la carte OpenLayers & un objet json repr�sentant les donn�es de l'emprises
        constructor: function () {
            //this.inherited(arguments);
        },
        /*
         * C'est a cet instant que les attributs du widget sont vraiment initialises
         * 
         */
        buildRendering: function () {
            this.inherited(arguments);
        },
        highlightLocation: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var type = this.dataLayers[indiceLayer[indiceLayer.length - 1]].type;

            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects[type];
            var toHighlight = [];

            var libelle = "";
            for (var i = 0; i < listeIds.length; i++) {
                //La notice n'est pas d�j� highlight�e
                if (this.hoveredFeature.indexOf(e.feature) == -1) {
                    this.hoveredFeature.push(e.feature);
                }

                for (var key in this.data) {
                    if (this.data[key]['id'] == listeIds[i]) {
                        if (libelle.length) {
                            libelle += " - ";
                        }
                        libelle += this.data[key]["libelle"];
                    }
                }
                toHighlight.push(listeIds[i]);
                this.highlightFeatures(this.featureByNotice[listeIds[i]], "blue", libelle);
            }
        },
        /*
         * Methode highlight Features
         * Copie les features constituant une emprise sur le layer d'highlight
         * On la derive ici pour y ajouter le 'title' sur les features
         * 
         */
       
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
            //�a fonctionne mieux ainsi, mais on ne sait pas trop pourquoi, d�sol�....
            for (var i = 0; i < listeIds.length; i++) {
                this.destroyEmpriseById(listeIds[i]);
            }
        },
       
        initControls: function (layer) {
            this.map_controls = {};
            this.panel = {};
            switch (this.mode) {
                case 'facette' :
                    this.initPanel();
                    this.initToggleCluster();
                    this.initNavigate();
                    this.addToPanel();
                    this.map_controls.toggleCluster.activate();
                    this.initControleAffinage();
                    if (this.type == TYPE_LOCATION) {
                        layer.events.register("featureover", this, this.highlightLocation);
                        layer.events.register("featureout", this, this.downlightLocation);
                        layer.events.register("featureclick", this, this.loadFacette);
                    }
                    if (!this.alreadyZoomed) {
                        layer.map.events.register("zoomend", this, this.zoomEnd);
                        this.alreadyZoomed = true;
                    }
                    break;
            }
        },
        loadFacette: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var type = this.dataLayers[indiceLayer[indiceLayer.length - 1]].type;
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects[type];

            var param = '';
            var url = '';
            var flag_home_page = false;

            if (listeIds.length >= 1) {
                for (var i = 0; i < listeIds.length; i++) {
                    for (var key in this.data) {
                        //pour la carte des localisations sur la page principale a l'opac
                        if (this.data[key]['flag_home_page']) {
                            flag_home_page = true;
                        }
                        if (this.data[key]['id'] == listeIds[i]) {
                            param += this.data[key]['param'];
                            url = this.data[key]['url'];
                        }
                    }
                }
                //a l'opac, s'il y a plusieurs emprises de selectionner, on ne fait rien
                if (listeIds.length > 1 && flag_home_page) {
                    return ;
                }
                if (param.length && url.length) {
                        document.location = url + param;
                }
            }            
        },
     
        zoomEnd: function (event) {
            if (this.cluster) {
                this.showPatience();
                //console.log('moveEnd', event);
                var bounds = this.map.olMap.calculateBounds();
                var geom = bounds.toGeometry();
                geom = geom.transform(this.projTo, this.projFrom);
                //console.log('features displayed: ', this.displayedFeaturesFromExtent(this.map.olMap.getExtent()));
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
                            'data': "indice=" + i + "&" + this.getLocIds() + "&wkt_map_hold=" + geom + "&zoom_level=" + event.object.zoom + "&cluster=" + this.cluster,
                            'handleAs': "json",
                        }).then(callbackHolds);
                    }
                }
            }
        },   
        
        swapMode: function (e) {
            if (this.cluster) {
                this.cluster = false;
                this.map_controls.toggleCluster.deactivate();
            } else {
                this.cluster = true;
                this.map_controls.toggleCluster.activate();
            }
            this.showPatience();
            this.featuresByZoom = {};
            var bounds = this.map.olMap.calculateBounds();
            var geom = bounds.toGeometry();
            geom = geom.transform(this.projTo, this.projFrom);
            this.nbLayersReceived = 0;         

            for (var i = 0; i < this.dataLayers.length; i++) {
                var callbackHolds = lang.hitch(this, "callbackCluster", i, this.map.olMap.zoom);
                this.dataLayers[i].holds = [];
                request.post(this.dataLayers[i].data_url, {
                    //'data': "indice="+i+"&search_id="+this.searchId+"&wkt_map_hold="+geom+"&zoom_level="+this.map.olMap.zoom+"&cluster="+this.cluster,
                    'data': "indice=" + i + "&" + this.getLocIds() + "&wkt_map_hold=" + geom + "&zoom_level=" + this.map.olMap.zoom + "&cluster=" + this.cluster,
                    'handleAs': "json",
                }).then(callbackHolds);
            }
        },
        
        getLocIds : function() {
            var loc_ids = '';
            for (var k = 0; k < this.data.length; k++) {
                if (loc_ids.length) {
                    loc_ids += '&';
                }
                var layer = '';
                if (this.data[k].code_champ == 90 && this.data[k].code_ss_champ == 4) {
                    layer = 'location';
                } else if (this.data[k].code_champ == 90 && this.data[k].code_ss_champ == 9) {
                    layer = 'sur_location';
                }
                if (layer) {
                    loc_ids += 'loc_ids[' + layer + '][]=' + this.data[k].id;
                }
            }
            //retourne des ids de localisations ET de sur-localisations
            return loc_ids;
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
                    if (data[j].objects["location"]) {   
                        dataLoc = data[j].objects["location"]
                        for(var key in data[j].objects["location"]) {
                            for(var keyLoc in this.data) {
                                //console.log(data[j].objects["location"][key],this.data[keyLoc].id);
                                if (this.data[keyLoc].id == data[j].objects["location"][key] && this.data[keyLoc].code_champ == 90 && this.data[keyLoc].code_ss_champ == 4) {
                                    notices_number = notices_number + parseInt(this.data[keyLoc].notices_number,10);
                                }
                            }
                        }                                                
                    } else if (data[j].objects["sur_location"]) {
                        dataLoc = data[j].objects["sur_location"];
                        for(var key in data[j].objects["sur_location"]) {
                            for(var keySurLoc in this.data) {
                                if (this.data[keySurLoc].id == data[j].objects["sur_location"][key] && this.data[keySurLoc].code_champ == 90 && this.data[keySurLoc].code_ss_champ == 9) {
                                    notices_number = notices_number + parseInt(this.data[keySurLoc].notices_number,10);
                                }
                            }
                        } 
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
                        featureI.records_ids = dataLoc;
                        featureI.attributes.records_length = dataLoc.length;
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