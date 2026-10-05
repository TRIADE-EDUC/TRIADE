// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_location_controler.js,v 1.2 2017/09/05 08:37:29 vtouchard Exp $


const TYPE_RECORD = 11;
const TYPE_LOCATION = 15;

define(["dojo/_base/declare", "apps/pmb/PMBDialog", "dojo/dom", "dojox/widget/Standby", "dojo/dom-construct", "dojo/dom-style", "dojo/query", "dojo/request", "dojo/on", "dojo/_base/lang", "dojo/json", "dojox/geo/openlayers/widget/Map", "apps/map/dialog_notice", "apps/map/map_controler"], function (declare, Dialog, dom, standby, domConstruct, domStyle, query, request, on, lang, json, Map, DialogNotice, map_controler) {
    /*
     *Classe map_controler. C'est la classe qui va contenir l'objet openLayer assoce a une carte
     */
    return declare("map_location_controler", map_controler, {
        countMapUpdate : 0,
        
        //Les parametres du constructeur sont un noeud dom auquel sera rattach� la carte OpenLayers & un objet json repr�sentant les donnees de l'emprises
        constructor: function () {
            //this.inherited(arguments);
        },
        /*
         * C'est � cet instant que les attributs du widget son vraiment initialis�s
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

            for (var i = 0; i < listeIds.length; i++) {
                //La notice n'est pas d�j� highlight�e
                if (this.hoveredFeature.indexOf(e.feature) == -1) {
                    this.hoveredFeature.push(e.feature);
                }
                toHighlight.push(listeIds[i]);
                for (var type in this.data) {
                    for (var key in this.data[type]) {
                        if (key == listeIds[i]) {
                            for (var j = 0; j < this.data[type][key].length; j++) {
                                this.highlightElt(type + "_" + this.data[type][key][j]);
                            }
                        }
                    }
                }
                this.highlightFeatures(this.featureByNotice[listeIds[i]], "blue");
            }

        },
        downlightLocation: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects['location'];
            var index = this.hoveredFeature.indexOf(e.feature);
            this.hoveredFeature.splice(index, 1);

            for (var i = 0; i < listeIds.length; i++) {
                this.destroyEmpriseById(listeIds[i]);
                for (var type in this.data) {
                    for (var key in this.data[type]) {
                        if (key == listeIds[i]) {
                            for (var j = 0; j < this.data[type][key].length; j++) {
                                this.downlightElt(type + "_" + this.data[type][key][j]);
                            }
                        }
                    }
                }
            }
        },
        /*
         * Methode de surbrillance des elements dans le dom
         * prend en param�tre un id de l'element
         */
        highlightElt: function (idElt) {
            if (dom.byId(idElt) != null) {
                domStyle.set(dom.byId(idElt), "border", "1px red solid");
            }
        },
        /*
         * Methode de downbrillance des element dans le dom
         * Prend en param�tre un id de l'element
         */
        downlightElt: function (idElt) {
            if (dom.byId(idElt) != null) {
                domStyle.set(dom.byId(idElt), "border", "");
            }
        },
        highlightHolds: function (idHold) {
            this.highlightFeatures(this.featureByNotice[idHold], "blue");
        },
        downlightHolds: function (idHold) {
            this.destroyEmpriseById(idHold);
        },
        initFeatureByNotice: function () {
            this.featureByNotice = new Object();
            for (var i = 0; i < this.dataLayers.length; i++) {
                //ajout d'un autre type que "record"
                var type = this.dataLayers[i].type;
                for (var j = 0; j < this.dataLayers[i].holds.length; j++) {
                    if (this.dataLayers[i].holds[j].objects && this.dataLayers[i].holds[j].objects[type]) {
                        for (var h = 0; h < this.dataLayers[i].holds[j].objects[type].length; h++) {
                            if (this.map.olMap.getLayersByName(this.dataLayers[i].name + '_' + i)[0].getFeatureById(i + '_feature_' + this.map.olMap.id + '_' + j) != null) {
                                if (!this.featureByNotice[this.dataLayers[i].holds[j].objects[type][h]]) {
                                    this.featureByNotice[this.dataLayers[i].holds[j].objects[type][h]] = new Array();
                                }
                                if (this.featureByNotice[this.dataLayers[i].holds[j].objects[type][h]].indexOf(this.map.olMap.getLayersByName(this.dataLayers[i].name + '_' + i)[0].getFeatureById(i + '_feature_' + this.map.olMap.id + '_' + j)) == -1) {
                                    this.featureByNotice[this.dataLayers[i].holds[j].objects[type][h]].push(this.map.olMap.getLayersByName(this.dataLayers[i].name + '_' + i)[0].getFeatureById(i + '_feature_' + this.map.olMap.id + '_' + j));
                                }
                            }
                        }
                    }
                }
            }
        },
        initControls: function (layer) {
            this.map_controls = {};
            this.panel = {};
            switch (this.mode) {
                case 'visualization':
                    this.initPanel();
                    this.initToggleCluster();
                    this.initNavigate();
                    this.addToPanel();
                    this.map_controls.toggleCluster.activate();
                    this.initControleAffinage();
                    if (this.type == TYPE_LOCATION) {
                        layer.events.register("featureover", this, this.highlightLocation);
                        layer.events.register("featureout", this, this.downlightLocation);
                    }
                    if (!this.alreadyZoomed) {
                        layer.map.events.register("zoomend", this, this.zoomEnd);
                        this.alreadyZoomed = true;
                    }
                    break;
            }
        },
        zoomEnd: function (event) {
            if (this.cluster) {

                this.showPatience();
                //console.log('moveEnd', event);
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
                            //on envoie 0 comme indice car dans la carte des locations d'exemplaires d'une notice on a deux layers (expl et explnum) 
                            //mais on veut toutes les loc confondues pour le clustering
                            'data': "indice=0&" + this.getLocIds(this.dataLayers[i].type_objet) + "&wkt_map_hold=" + geom + "&zoom_level=" + event.object.zoom + "&cluster=" + this.cluster,
                            'handleAs': "json",
                        }).then(callbackHolds);
                    }
                }
            }
            
            this.map.olMap.updateSize(); 
        },
        printByZoomLevel: function (zoomLevel, i) {
            var currentLayer = this.map.olMap.getLayersByName(this.dataLayers[i].name + "_" + i)[0];
            currentLayer.removeAllFeatures();
            var features = new Array();
            var type = this.dataLayers[i].type;
            if (this.featuresByZoom[zoomLevel][i]) {
                currentLayer.addFeatures(this.featuresByZoom[zoomLevel][i]);
                this.dataLayers[i].holds = [];
                for (var j = 0; j < this.featuresByZoom[zoomLevel][i].length; j++) {
                    var obj = {};
                    obj.objects = {};
                    //ajout d'un autre type que "record"
                    obj.objects[type] = this.featuresByZoom[zoomLevel][i][j].records_ids;
                    this.dataLayers[i].holds.push(obj);
                }
                this.initFeatureByNotice();
            }
            this.nbLayersReceived++;

            if (this.nbLayersReceived == (this.dataLayers.length)) {
                this.hidePatience();
            }
            this.map.olMap.updateSize(); 
        },
        callbackCluster: function (i, zoomLevel, data) {
            if (!this.featuresByZoom) {
                this.featuresByZoom = {};
            }
            if (!this.featuresByZoom[zoomLevel]) {
                this.featuresByZoom[zoomLevel] = {};
            }           
            // Nombre d'exemplaires par localisation
            var nb_expl_by_location = new Array();
            for (var type in this.data) {
                for (var key in this.data[type]) {
                    if(!nb_expl_by_location[key])   nb_expl_by_location[key]=0;
                    for (var j = 0; j < this.data[type][key].length; j++) {                        
                        nb_expl_by_location[key]++;
                    }
                }
            }
            if (!this.featuresByZoom[zoomLevel][i]) {
                this.featuresByZoom[zoomLevel][i] = new Array();
                for (var j = 0; j < data.length; j++) {
                    var styleEmprise = {
                        strokeWidth: 2,
                        strokeColor: this.dataLayers[i].color,
                        fillOpacity: 1,
                        fillColor: this.dataLayers[i].color
                    };
                    var featureI = this.formatWKT.read(data[j].wkt);
                    featureI.style = styleEmprise;
                    var dataLoc = "";
                    if (data[j].objects["location"]) {
                        dataLoc = data[j].objects["location"]
                    } else if (data[j].objects["sur_location"]) {
                        dataLoc = data[j].objects["sur_location"]
                    }
                    if (dataLoc) {
                        var nb_expl=0;
                        for(var k=0; k<dataLoc.length; k++){
                            if(nb_expl_by_location[dataLoc[k]]) nb_expl+=nb_expl_by_location[dataLoc[k]];
                        }
                        if (featureI.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
                            featureI.style.pointRadius = 8;
                            //featureI.style.label = dataLoc.length.toString();
                            featureI.style.label = nb_expl.toString();

                            if (dataLoc.length > 20) {
                                featureI.style.pointRadius = 14;
                                if (dataLoc.length > 100) {
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
                    //on envoie 0 comme indice car dans la carte des locations d'exemplaires d'une notice on a deux layers (expl et explnum) 
                    //mais on veut toutes les loc confondues pour le clustering
                    'data': "indice=0&" + this.getLocIds(this.dataLayers[i].type_objet) + "&wkt_map_hold=" + geom + "&zoom_level=" + this.map.olMap.zoom + "&cluster=" + this.cluster,
                    'handleAs': "json",
                }).then(callbackHolds);
            }
        },
        getLocIds: function (type_objet) {
            var loc_ids = '';
            for (var type in this.data) {
                if (type_objet == type) {
                    for (var key in this.data[type]) {
                        if (loc_ids.length) {
                            loc_ids += '&';
                        }
                        loc_ids += 'loc_ids[location][]=' + key;
                    }
                }
            }
            //retourne des ids de localisations
            return loc_ids;
        },
        initEvt: function () {
            this.hidePatience();
            if (this.initialBounds) {
                this.map.olMap.zoomToExtent(this.initialBounds);
                this.map.olMap.baseLayer.events.register("loadend", this, function (e) {
                    this.map.olMap.zoomToExtent(this.initialBounds);
                    this.map.olMap.baseLayer.events.remove("loadend");
                    if (this.mode == "search_result") {
                        this.map.olMap.events.register("move", this, function (e) {
                            var arrayInitial = this.initialBoundsPrinted.toArray();
                            var arrayActual = this.initialBounds.toArray();
                            var condition = false;
                            for (var i = 0; i < arrayActual.length; i++) {
                                if (arrayInitial[i] != arrayActual[i]) {
                                    condition = true;
                                }
                            }
                            if (condition) {
                                if (document.affineRecherche == undefined) {
                                    var bounds = this.map.olMap.calculateBounds();
                                    var geom = bounds.toGeometry();
                                    geom = geom.transform(this.projTo, this.projFrom);
                                    domConstruct.place("<form method='post' name='affineRecherche' action='./catalog.php?categ=search&mode=6&sub=launch'>" +
                                            "<input type='hidden' name='search[0]' value='s_1' />" +
                                            "<input type='hidden' name='search[1]' value='f_78' />" +
                                            "<input type='hidden' name='inter_0_s_1' value='' />" +
                                            "<input type='hidden' name='op_0_s_1' value='EQ' />" +
                                            "<input type='hidden' name='field_0_s_1[]' value='" + this.searchId + "' />" +
                                            "<input type='hidden' name='inter_1_f_78' value='and' />" +
                                            "<input type='hidden' name='op_1_f_78' value='CONTAINS' />" +
                                            "<input type='hidden' id='wktAffinage' name='field_1_f_78[]' value='" + this.formatWKT.extractGeometry(geom) + "' />" +
                                            "<input type='hidden' name='explicit_search' value='1' />" +
                                            "<input type='hidden' name='launch_search' value='1' />" +
                                            "<input type='button' class='bouton' value='" + pmbDojo.messages.getMessage("carto", "carto_btn_affiner") + "' id='affinageButton'/></form>", dom.byId('map_search'), "after");
                                    on(dom.byId('affinageButton'), 'click', lang.hitch(this, this.callbackAffinage));
                                    this.map.olMap.updateSize();
                                }
                            }
                        });
                    }
                });

            }
            switch (this.mode) {
                case 'edition':
                case 'search_criteria':
                    break;
                case 'visualization':
                case 'search_result':
                    var callbackMapOut = lang.hitch(this, "downlightAll");
                    this.initialBoundsPrinted = this.map.olMap.calculateBounds();
                    for (var key in this.featureByNotice) {
                        for (var type in this.data) {
                            for (var locId in this.data[type]) {
                                if (key == locId) {
                                    for (var j = 0; j < this.data[type][locId].length; j++) {
                                        if (dom.byId(type + "_" + this.data[type][locId][j]) != null) {
                                            on(dom.byId(type + "_" + this.data[type][locId][j]), "mouseover", lang.hitch(this, "highlightHolds", key));
                                            on(dom.byId(type + "_" + this.data[type][locId][j]), "mouseout", lang.hitch(this, "downlightHolds", key));
                                        }
                                    }
                                }
                            }
                        }
                    }                    
                    
                    if(document.getElementById(this.id_img_plus)) {
                        on(dom.byId(this.id_img_plus), "click", lang.hitch(this, "mapUpdateSize")); 
                    }                        
                    //on(query('.map_location_img_plus'), "click", lang.hitch(this, "mapUpdateSize", this.domNode));
                    
                    this.domNode.addEventListener("mouseleave", callbackMapOut, false);
                    break;
            }
        },
        
        mapUpdateSize: function () {
            if (this.countMapUpdate == 0) {
                this.map.olMap.updateSize(); 
                this.countMapUpdate ++;
            }
        }
        
    });
});