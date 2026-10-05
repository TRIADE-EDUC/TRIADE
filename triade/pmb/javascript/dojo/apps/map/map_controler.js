// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: map_controler.js,v 1.88 2023/08/09 08:50:07 qvarin Exp $

const TYPE_RECORD = 11;
const TYPE_LOCATION = 15;
const MAX_POLYGON_POINTS = 10;

define(["dojo/_base/declare", "apps/pmb/PMBDialog", "dojo/dom", "dojox/widget/Standby", "dojo/dom-construct", "dojo/dom-style", "dojo/query", "dojo/request", "dojo/on", "dojo/_base/lang", "dojo/json", "dojox/geo/openlayers/widget/Map", "apps/map/dialog_notice", "dojo/Deferred"], function(declare, Dialog, dom, standby, domConstruct, domStyle, query, request, on, lang ,json, Map, DialogNotice, Deferred){
    /*
     *Classe map_controler. C'est la classe qui va contenir l'objet openLayer associ� � une carte
     */
    return declare("map_controler", Map, {
        mode: "",
        dataLayers: null,
        type: null,
        data: null,
        layersURL: "",
        searchId: 0,
        nbLayers: 0,
        featureByNotice: null,
        popup: null,
        layerHighlight: null,
        hoveredFeature: null,
        searchHolds: null,
        popupEdition: null,
        handleBtnOk: null,
        map_controls: {},
        panel: null,
        dialogWktImport: null,
        dialogWktExport: null,
        featuresByZoom: null,
        alreadyZoomed: null,
        cluster: true,
        nbLayersReceived: 0,
        editionStates: null,
        id_img_plus:"",
        hashIds: null,
        temporaryFeature: null,
        temporaryData: null,
        //Les param�tres du constructeur sont un noeud dom auquel sera rattach� la carte OpenLayers & un objet json repr�sentant les donn�es de l'emprises
        constructor: function () {
            //Conversion de degree decimaux en metre (la projection 4326 d�finie la terre en tant qu'une elipse alors que la 900913 la d�finie en tant qu'une sph�re)
            this.projFrom = new OpenLayers.Projection("EPSG:4326");
            this.projTo = new OpenLayers.Projection("EPSG:900913");
            this.formatWKT = new OpenLayers.Format.WKT();
            this.mode = arguments[0]['mode'];
            this.type = arguments[0]['type'];
            this.noticesIds = {};
            this.alreadyZoomed = false;
            this.hoveredFeature = new Array();
            switch (this.mode) {
                case "search_result" :
                case "visualization" :
                    if (arguments[0]['searchId']) {
                        this.searchId = arguments[0]['searchId'];
                    }
                    if (arguments[0]['layers_url']) {
                        this.layersURL = arguments[0]['layers_url'];
                    } else {
                        this.dataLayers = arguments[0]['layers'];
                        //Set la vue initiale sur la carte (la vue initiale contient toutes les emprises)
                        this.initialBounds = new OpenLayers.Bounds(this.transformInitialBounds(arguments[0]['initialFit']));
                        this.initialBounds = this.initialBounds.transform(this.projFrom, this.projTo);
                    }
                    if (arguments[0]['data']) {
                        this.data = arguments[0].data;
                    }
                    if(arguments[0]['id_img_plus']) {
                        this.id_img_plus = arguments[0]['id_img_plus'];
                    }
                    break;
                case "edition" :
                    this.hiddenField = arguments[0]['hiddenField'];
                    this.dataLayers = arguments[0]['layers'];
                    this.searchHolds = arguments[0]['searchHolds'];
                    //Set la vue initiale sur la carte (la vue initiale contient toutes les emprises)
                    this.initialBounds = new OpenLayers.Bounds(this.transformInitialBounds(arguments[0]['initialFit']));
                    this.initialBounds = this.initialBounds.transform(this.projFrom, this.projTo);
                    this.editionStates = new Array();
                    break;
                case 'search_criteria' :
                    this.hiddenField = arguments[0]['hiddenField'];
                    if (arguments[0]['initialFit']) {
                    	this.initialBounds = new OpenLayers.Bounds(this.transformInitialBounds(arguments[0]['initialFit']));
                    	this.initialBounds = this.initialBounds.transform(this.projFrom, this.projTo);
                    } else {
                    	this.searchHolds = arguments[0]['searchHolds'];
                    }
                    break;
            }
        },
        /*
         * C'est � cet instant que les attributs du widget son vraiment initialis�s
         * 
         */
        buildRendering: function () {
            this.inherited(arguments);
            this.standby = new standby({target: this.domNode, zIndex: 10000000});
            document.body.appendChild(this.standby.domNode);
            this.standby.startup();
            //chargement sp�cifique
            this.map.olMap.addControl(new OpenLayers.Control.PanZoomBar());
            switch (this.mode) {
                case 'search_result':
                    this.map.olMap.addControl(new OpenLayers.Control.LayerSwitcher());
                    break;
                case 'visualization':
                    this.map.olMap.addControl(new OpenLayers.Control.LayerSwitcher());
                    break;
                case 'edition':
                    //nothing
                    break;
            }
            //pour tous
            var formatLonlats = lang.hitch(this, "formatLonlats");
            this.map.olMap.addControl(new OpenLayers.Control.MousePosition({id: "ll_mouse", formatOutput: formatLonlats}));

            //Ajout de la mini carte
            var ovControl = new OpenLayers.Control.OverviewMap();
            ovControl.isSuitableOverview = function () {
                return false;
            };
            this.map.olMap.addControl(ovControl);

            //initilisation
            this.showPatience();
            this.initDatas();
        },
        /*
         * M�thode d'initialisation des donn�es des layers (appels Ajax pour r�cup�rer les donn�es)
         * 
         */
        initDatas: function () {
            switch (this.mode) {
                case "search_criteria" :
                    this.dataLayers = new Array({
                        name: this.mode,
                        type: this.mode,
                        color: "#000000",
                        holds: [],
                        editable: true,
                        ajax: false
                    });
                    if (this.searchHolds) {
                        this.dataLayers[0].holds = this.searchHolds;
                    }
                    this.drawLayer(this.dataLayers[0], 0);
                    break;
                case "visualization" :
                case "search_result" :
                    var bounds = this.map.olMap.calculateBounds();
                    var geom = bounds.toGeometry();
                    geom = geom.transform(this.projTo, this.projFrom);
                    if (this.layersURL) {
                        var callbackLayers = lang.hitch(this, "gotLayers");
                        request.post(this.layersURL, {
                            'data': "search_id=" + this.searchId + "&wkt_map_hold=" + geom,
                            'handleAs': "application/json",
                            'headers': {
                               'X-Requested-With': ''
                            }
                        }).then(callbackLayers);

                    } else {
                        //TODO ajax sur un autre mode que la recherche
                        for (var i = 0; i < this.dataLayers.length; i++) {
                            var callbackHolds = lang.hitch(this, "gotHolds", i);
                            if (this.dataLayers[i].ajax) {
                                request.post(this.dataLayers[i].data_url, {
                                    'data': "indice=" + i + "&search_id=" + this.searchId + "&wkt_map_hold=" + geom,
                                    'handleAs': "application/json",
                                    'headers': {
                                       'X-Requested-With': ''
                                    }
                                }).then(callbackHolds);
                            } else {
                                this.drawLayer(this.dataLayers[i], i);
                            }
                        }
                    }
                    break;
                case "edition" :
                    this.drawLayer(this.dataLayers[0], 0);
                    break;
            }
        },
        /*
         * Callback appel ajax chargement des layers
         * 
         */
        gotLayers: function (datas) {
            var obj = json.parse(datas);

            this.initialBounds = new OpenLayers.Bounds(this.transformInitialBounds(obj.initialFit));
            this.initialBounds = this.initialBounds.transform(this.projFrom, this.projTo);
            this.dataLayers = obj.layers;
            this.layersURL = null;
            this.initDatas();
        },
        /*
         * Callback de l'appel AJAX potentiellement fait dans initDatas
         * 
         * datas est une structure JSON
         */
        gotHolds: function (indice, datas) {
            this.dataLayers[indice].holds = json.parse(datas);
            this.drawLayer(this.dataLayers[indice], indice);

        },
        /*
         *Modification suite � l'ajout de Hash dans les ids de notices sur les �l�ments dom en r�sultat de recherche. 
         */
        initHashTable: function(){
        	this.hashIds = {};
        	for(var key in this.featureByNotice){
        		var queryResult = query('div[id^="el'+key+'_"][id$="Parent"]');
        		if(key && queryResult.length){
        			this.hashIds[key] = queryResult[0].getAttribute('id').split('_')[1].split('Parent')[0];
        		}
        	}
        },
        createPopup: function (closeBoxCallback) {
            if (this.data.lonlat != null) {
                if (!this.popup) {

                    var anchor = (this.marker) ? this.marker.icon : null;
                    var popupClass = this.popupClass ?
                            this.popupClass : OpenLayers.Popup.Anchored;
                    this.popup = new popupClass(this.id + "_popup",
                            this.data.lonlat,
                            new OpenLayers.Size(null, null),
                            this.data.popupContentHTML,
                            anchor,
                            true,
                            closeBoxCallback);
                }
                if (this.data.overflow != null) {
                    this.popup.contentDiv.style.overflow = this.data.overflow;
                }
                this.popup.panMapIfOutOfView = false;
                this.popup.feature = this;
            }
            this.popup.autoSize = true;
            return this.popup;
        },
        /*
         * M�thode d'ajout des layers
         * 
         */
        drawLayer: function (layerParam, numLayer) {
            var styleLayerDefault = {
                strokeWidth: 2,
                strokeColor: layerParam.color,
                fillOpacity: 0.4,
                fillColor: layerParam.color
            };
            var styleLayerSelect = {
                strokeWidth: 2,
                strokeColor: "rgba(0,0,0,0.5)",
                fillOpacity: 0.4,
                fillColor: "rgba(0,0,0,0.5)"
            };
            var styleDefault = OpenLayers.Util.applyDefaults(styleLayerDefault, OpenLayers.Feature.Vector.style["default"]);
            var style = new OpenLayers.StyleMap({
                'default': styleDefault,
                'select': styleLayerSelect
            });

            //Permet de d�coder du WKT (on r�cup�re une feature ou un tableau de features apr�s la lecture)
            this.formatWKT = new OpenLayers.Format.WKT();

            var layer = new OpenLayers.Layer.Vector(layerParam.name + "_" + numLayer, {
                styleMap: style,
                renderers: ["SVG"]
            });

            this.map.olMap.addLayer(layer);
            this.map.olMap.setLayerIndex(layer, numLayer);

            //Boucle sur Feature
            var features = new Array();
            for (var i = 0; i < layerParam.holds.length; i++) {
                var styleEmprise = {
                    strokeWidth: 2,
                    strokeColor: layerParam.color,
                    fillOpacity: 0.4,
                    fillColor: layerParam.color
                };
                var featureI = this.formatWKT.read(layerParam.holds[i].wkt);
                featureI.geometry.transform(this.projFrom, this.projTo);
                featureI.records_ids = layerParam.holds[i].objects.record;
                //la condition sur la visualisation fut ajout�e pour les cartes d'exemplaires. 
                //Depuis, il y'a un probleme de selection dans les cartes visualisation de notices (resultat de recherche)
                if (this.mode == "search_result" || this.mode == "visualization") {
                    featureI.style = styleEmprise;
                    if (layerParam.holds[i].color != null) {
                        featureI.style.fillColor = layerParam.holds[i].color;
                        featureI.style.strokeColor = layerParam.holds[i].color;
                    }

                    if (featureI.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
                        featureI.style.pointRadius = 8;
                        if (this.mode == "search_result") {
                            featureI.style.label = layerParam.holds[i].objects.record.length.toString();
                            if (featureI.records_ids.length > 20) {
                                featureI.style.pointRadius = 14;
                                if (featureI.records_ids.length > 100) {
                                    featureI.style.pointRadius = 20;
                                }
                            }

                        }
                    }
                }
                featureI.id = numLayer + "_" + "feature_" + this.map.olMap.id + "_" + i;
                features.push(featureI);
            }
            layer.addFeatures(features);
            this.initControls(layer);
            switch (this.mode) {
                case "search_criteria" :
                    if (layer.features && layer.features[0]) {
                        this.initialBounds = layer.features[0].geometry.getBounds();
                    }
                    if (layer.features) {
                        for (key in layer.features) {
                            this.setHiddenField(layer.features[key]);
                        }
                    }
                    break;
                case "edition" :
                    if (layer.features) {
                        for (key in layer.features) {
                            this.setHiddenField(layer.features[key]);
                        }
                        this.saveCurrentState();
                    }
                    break;
                case 'visualization':
                    break;
            }
            this.nbLayers++;
            if (this.nbLayers == this.dataLayers.length) {
                this.testDatas();
            }
        },
        featureEditionClick: function (e) {
            if (this.map_controls.edition.active && e.feature.geometry.CLASS_NAME == "OpenLayers.Geometry.Point" && e.feature._sketch) {
                if (this.popupEdition == null) {
                    var callbackMethodCreate = lang.hitch(e.feature, this.createPopup);
                    var point = e.feature.clone();
                    point = point.geometry.transform(this.projTo, this.projFrom);
                    var callbackContext = lang.hitch(this, this.closeBoxCallback)
                    var lonLatPop = new OpenLayers.LonLat(e.feature.geometry.x, e.feature.geometry.y);
                    var editCallbackDeg = lang.hitch(this, this.editDegMinSec);
                    var editCallbackLonLat = lang.hitch(this, this.editLongLat);
                    var latDeg = this.toSexagesimal(point.y);
                    var longDeg = this.toSexagesimal(point.x);
                    var callbackRadio = lang.hitch(this, this.clickRadioPopup);
                    var suppression = "";
                    if (e.feature._index == undefined) {
                        suppression = "<input type='button' class='bouton' id='btnDelPopup' value='X'/>";
                    }
                    e.feature.data.popupContentHTML = "<input type='radio' id='radioDegMinSec' checked='true' name='typeCoords' value='degre'/>" + pmbDojo.messages.getMessage("carto", "carto_sexagesimal_degrees") + ": <br/><input type='radio' id='radioLonLat' name='typeCoords' value='LonLat' />" + pmbDojo.messages.getMessage("carto", "carto_decimal_degrees") + ":<br/>" +
                            "<div id='lonLatPopup' style='display:none;'><label for='lon'>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + "</label><input id='inputLon' type='text' value='" + point.x + "' name='lon'/>" +
                            "<br/><label for='lat'>" + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + "</label><input id='inputLat' type='text' value='" + point.y + "' name='lat'/></div>" +
                            "<div id='degMinSec'>" +
                            "<div style='float:right;'><label>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + ":</label> <input id='degLonPopup' style='width:30px;' value=" + longDeg[0] + " type='text'/><label>\xB0</label><input id='minLonPopup' style='width:30px;' value=" + longDeg[1] + " type='text'/><label>'</label><input id='secLonPopup' style='width:30px;' value=" + longDeg[2] + " type='text'/><label>\"</label></div><br/>" +
                            "<div style='float:right;'><label>" + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + ":</label> <input id='degLatPopup' style='width:30px;' value=" + latDeg[0] + " type='text'/><label>\xB0</label><input id='minLatPopup' style='width:30px;' value=" + latDeg[1] + " type='text'/><label>'</label><input id='secLatPopup' style='width:30px;' value=" + latDeg[2] + " type='text'/><label>\"</label></div>" +
                            "</div>" +
                            "<input type='hidden' id='featureName' value='" + e.feature.id + "'/>" +
                            "<input type='hidden' id='layerName' value='" + e.feature.layer.name + "'/>" +
                            "<input type='button' class='bouton' id='btnPopup' value='" + pmbDojo.messages.getMessage("carto", "carto_ok_popup") + "'/>"
                            + suppression;
                    e.feature.data.popupSize = null;
                    e.feature.data.lonlat = lonLatPop;
                    callbackMethodCreate(callbackContext);

                    this.popupEdition = e.feature.popup;
                    this.map.olMap.addPopup(e.feature.popup);
                    this.handleBtnOk = on(dom.byId('btnPopup'), 'click', lang.hitch(this, "coordsChange"));
                    if (dom.byId('btnDelPopup') != null)
                        on(dom.byId('btnDelPopup'), 'click', lang.hitch(this, "delPtPop", e.feature));

                    on(dom.byId('radioLonLat'), 'click', callbackRadio);
                    on(dom.byId('radioDegMinSec'), 'click', callbackRadio);

                    on(dom.byId('degLonPopup'), 'change', editCallbackDeg);
                    on(dom.byId('degLatPopup'), 'change', editCallbackDeg);

                    on(dom.byId('secLonPopup'), 'change', editCallbackDeg);
                    on(dom.byId('secLatPopup'), 'change', editCallbackDeg);

                    on(dom.byId('minLonPopup'), 'change', editCallbackDeg);
                    on(dom.byId('minLatPopup'), 'change', editCallbackDeg);

                    on(dom.byId('inputLon'), 'change', editCallbackLonLat);
                    on(dom.byId('inputLat'), 'change', editCallbackLonLat);
                }
                else {
                    if (e.feature.popup == null) {
                        this.closeBoxCallback();
                        this.featureEditionClick(e);
                    }
                }
            }
        },
        coordsChange: function () {
            //make a for loop with layers length & layers name from datalayers attr
            var editedFeature = this.map.olMap.getLayersByName(dom.byId('layerName').value)[0].getFeatureById(dom.byId('featureName').value);
            var newLon = dom.byId('inputLon').value;
            var newLat = dom.byId('inputLat').value;
            var newPt = new OpenLayers.LonLat(newLon, newLat);
            newPt = newPt.transform(this.projFrom, this.projTo);
            this.map_controls.edition.dragStart(editedFeature);
            this.map_controls.edition.dragVertex(editedFeature, editedFeature.layer.getViewPortPxFromLonLat(newPt));
            this.map_controls.edition.dragComplete(editedFeature);
            this.closeBoxCallback();
        },
        closeBoxCallback: function (e) {
            this.popupEdition.destroy();
            this.popupEdition.feature.popup = null;
            this.popupEdition = null;
            this.handleBtnOk.remove();
        },
        setHiddenField: function (f) {
            var feature = f.clone();
            feature.geometry.transform(this.projTo, this.projFrom);
            var field = document.getElementById(f.id);
            if (!field) {
                var field = document.createElement("input");
                field.setAttribute("type", "hidden");
                field.setAttribute("name", this.hiddenField + "[]");
                field.setAttribute("id", f.id);
                this.domNode.parentNode.appendChild(field);
            }
            if (feature['attributes']) {
            	if (feature['attributes']['data-display_address']) {
                	field.setAttribute("data-display_address", feature['attributes']['data-display_address']);
                }
            	if (feature['attributes']['data-address']) {
                	field.setAttribute("data-address", JSON.stringify(feature['attributes']['data-address']));
                }
            }
            field.value = this.formatWKT.write(feature);
        },
        deleteHiddenField: function (f) {
            var field = document.getElementById(f.id);
            if (field) {
                field.parentNode.removeChild(field);
            }
        },
        /*
         * Initialise evenement sur les notices de la page
         * 
         */
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
                	this.initHashTable();
                	var callbackHover = lang.hitch(this, "highlightHolds");
                    var callbackOut = lang.hitch(this, "downlightHolds");
                    var callbackMapOut = lang.hitch(this, "downlightAll");
                    this.initialBoundsPrinted = this.map.olMap.calculateBounds();
                    for (var key in this.featureByNotice) {
                        if (typeof this.hashIds[key] != "undefined") {
                            on(dom.byId('el'+key+'_'+this.hashIds[key]+'Parent'), "mouseover", callbackHover);
                            on(dom.byId('el'+key+'_'+this.hashIds[key]+'Parent'), "mouseout", callbackOut);
                        }
                    }
                    this.domNode.addEventListener("mouseleave", callbackMapOut, false);
                    break;
            }
        },
        destroyHighlightedFeature: function (e) {
            this.map.olMap.getLayersByName('highlight')[0].destroyFeatures();
        },
        /* 
         * Met la notice correspondant � l'emprise survol�e en surbrillance
         * 
         */
        highlightRecord: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            //Notices � highlighter
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects['record'];
            var toHighlight = [];
            for (var i = 0; i < listeIds.length; i++) {
                //La notice n'est pas d�j� highlight�e
                if (this.hoveredFeature.indexOf(e.feature) == -1) {
                    this.hoveredFeature.push(e.feature);
                }
                toHighlight.push(listeIds[i]);
            }
            for (var i = 0; i < toHighlight.length; i++) {
                this.highlightNotice(toHighlight[i]);
//                this.highlightFeatures(this.featureByNotice[toHighlight[i]], "rgba(0,0,0,0.3)");

            }
        	this.highlightFeatures([e.feature], "rgba(0,0,0,0.3)");
        },
        downlightRecord: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects['record'];
            var index = this.hoveredFeature.indexOf(e.feature);
            this.hoveredFeature.splice(index, 1);
            for (var i = 0; i < listeIds.length; i++) {
//                this.destroyEmpriseById(listeIds[i]);
                this.downlightNotice(listeIds[i]);
            }
            this.destroyEmpriseByFeatures([e.feature]);
            
        },
        downlightAll: function () {
            var style = {fillColor: "#0000ff", strokeColor: "#0000ff", strokeWidth: 1, fillOpacity: 0.4};
            if (this.layerHighlight == null) {
                this.layerHighlight = new OpenLayers.Layer.Vector("highlight");
                this.map.olMap.addLayer(this.layerHighlight);
                this.map.olMap.setLayerIndex(this.layerHighlight, 200);
            }
            this.map.olMap.getLayersByName('highlight')[0].destroyFeatures();
            var divNotice = query('.notice-parent');
            for (var i = 0; i < divNotice.length; i++) {
                if (divNotice[i].style.border != "") {
                    var patternNombre = /\d+/g;
                    var idNotice = divNotice[i].id.match(patternNombre)[0];
                    domStyle.set(dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Parent'), "border", "");
                    domStyle.set(dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Child'), "border", "");
                }
            }
        },
        /*
         * Appell�e au survol d'une notice dans le dom, r�cup�re l'id de la notice et passe les features au layer d'highlight
         * 
         */
        highlightHolds: function (e) {
            var patternNombre = /\d+/g;
            var idNot = e.currentTarget.getAttribute('id').match(patternNombre)[0];

            this.highlightFeatures(this.featureByNotice[idNot], "rgba(0,0,0,0.3)");
        },
        /*
         * Methode highlight Features
         * Copie les features constituant une emprise sur le layer d'highlight
         * 
         */
        highlightFeatures: function (arrayFeature, color, libelle) {
            if (arrayFeature) {
                if (this.layerHighlight == null) {
                    this.layerHighlight = new OpenLayers.Layer.Vector("highlight");
                    this.map.olMap.addLayer(this.layerHighlight);
                    this.map.olMap.setLayerIndex(this.layerHighlight, 100);
                }
                var style = {fillColor: color, strokeWidth: 1, strokeColor: color, fillOpacity: 0.7, title: libelle};
                for (var i = 0; i < arrayFeature.length; i++) {
                	//on utilise la fonction native de l'objet feature
                	//c'est plus propre pour la duplication des components (pas de conflit d'id, pas d'emprise qui disparait)
                    var clonedFeature = arrayFeature[i].clone();
                    var numLayer = arrayFeature[i].layer.name.split('_');
                    var numFeature = arrayFeature[i].id.split('_');
                    clonedFeature.id = numLayer[numLayer.length - 1] + '_' + numFeature[numFeature.length - 1];
                    if (clonedFeature.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
                        style.pointRadius = arrayFeature[i].style.pointRadius;
                    }
                    clonedFeature.style = style;
                    this.map.olMap.getLayersByName('highlight')[0].addFeatures([clonedFeature]);
                }
            }
        },
        /* 
         * Appell�e � l'arr�t du survol d'une notice dans le dom, supprime toute les features du layer d'highlight 
         */
        downlightHolds: function (e) {
            var patternNombre = /\d+/g;
            var style = {fillColor: "#0000ff", strokeColor: "#0000ff", strokeWidth: 1, fillOpacity: 0.4};
            var idNot = e.currentTarget.getAttribute('id').match(patternNombre)[0];
            this.destroyEmpriseById(idNot);

        },
        /*
         * Methode de surbrillance des notices dans le dom
         * prend en param�tre un id de notice
         */
        highlightNotice: function (idNotice) {
            if (dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Parent') != null) {
                domStyle.set(dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Parent'), "border", "1px red solid");
                domStyle.set(dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Child'), "border", "1px red solid");
            }
        },
        /*
         * Methode de downbrillance des notices dans le dom
         * Prend en param�tre un id de notice
         */
        downlightNotice: function (idNotice) {
            if (dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Parent') != null) {
                domStyle.set(dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Parent'), "border", "");
                domStyle.set(dom.byId('el'+idNotice+'_'+this.hashIds[idNotice]+'Child'), "border", "");
            }
        },
        destroyEmpriseById: function (id) {
            if (this.layerHighlight != null) {
                var arrayFeat = this.featureByNotice[id];
                this.destroyEmpriseByFeatures(arrayFeat);
            }
        },
        /**
         * destruction d'une emprise en fonction des features et pas un id de notice
         */
        destroyEmpriseByFeatures: function (arrayFeat) {
            if (this.layerHighlight != null) {
	        	if (arrayFeat) {
	                var idHighlighted = [];
	                var arrayHigh = [];
	
	                for (var i = 0; i < arrayFeat.length; i++) {
	                    var id = arrayFeat[i].id.split('_');
	                    var idLayer = arrayFeat[i].layer.name.split('_');
	                    idHighlighted.push({
	                        'idFeature': id[id.length - 1],
	                        'idLayer': idLayer[idLayer.length - 1]
	                    });
	                }
	
	                for (var i = 0; i < idHighlighted.length; i++) {
	                    if (this.map.olMap.getLayersByName('highlight')[0].getFeatureById(idHighlighted[i].idLayer + '_' + idHighlighted[i].idFeature) != null) {
	                        this.map.olMap.getLayersByName('highlight')[0].getFeatureById(idHighlighted[i].idLayer + '_' + idHighlighted[i].idFeature).destroy();
	                    }
	                }
	            }
            }
        },
        /*
         * Affiche la notice si seulement une notice est contenue dans la feature
         */

        showFeaturePage: function (e) {
            var indiceFeature = e.feature.id.split('_');
            var indiceLayer = e.feature.layer.name.split('_');
            var listeIds = this.dataLayers[indiceLayer[indiceLayer.length - 1]].holds[indiceFeature[indiceFeature.length - 1]].objects['record'];

            if (listeIds.length >= 1) {
                if (this.popup == null) {
                    this.popup = new DialogNotice({
                        title: pmbDojo.messages.getMessage("carto", "carto_popup_linked_records"),
                        style: "width: 900px; height:auto;",
                    });
                }
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
        /*
         * Retourne une structure JSON d�crivant la box courante de la carte
         */
        get_visible_box: function () {
            var bounds = this.map.olMap.calculateBounds();
            var boundsCoords = bounds.transform(this.projTo, this.projFrom);
            var boundsArray = boundsCoords.toArray();
            var retourJSON =
                    {
                        'coords':
                                [
                                    {
                                        'lat': boundsArray[0],
                                        'long': boundsArray[1]
                                    },
                                    {
                                        'lat': boundsArray[2],
                                        'long': boundsArray[1]
                                    },
                                    {
                                        'lat': boundsArray[2],
                                        'long': boundsArray[3]
                                    },
                                    {
                                        'lat': boundsArray[0],
                                        'long': boundsArray[3]
                                    }
                                ]
                    }
            return retourJSON;
        },
        initFeatureByNotice: function () {
            this.featureByNotice = new Object();
            for (var i = 0; i < this.dataLayers.length; i++) {
                for (var j = 0; j < this.dataLayers[i].holds.length; j++) {
                    if (this.dataLayers[i].holds[j].objects && this.dataLayers[i].holds[j].objects['record']) {
                        for (var h = 0; h < this.dataLayers[i].holds[j].objects['record'].length; h++) {
                            if (this.map.olMap.getLayersByName(this.dataLayers[i].name + '_' + i)[0].getFeatureById(i + '_feature_' + this.map.olMap.id + '_' + j) != null) {
                                if (!this.featureByNotice[this.dataLayers[i].holds[j].objects['record'][h]]) {
                                    this.featureByNotice[this.dataLayers[i].holds[j].objects['record'][h]] = new Array();
                                }
                                if (this.featureByNotice[this.dataLayers[i].holds[j].objects['record'][h]].indexOf(this.map.olMap.getLayersByName(this.dataLayers[i].name + '_' + i)[0].getFeatureById(i + '_feature_' + this.map.olMap.id + '_' + j)) == -1) {
                                    this.featureByNotice[this.dataLayers[i].holds[j].objects['record'][h]].push(this.map.olMap.getLayersByName(this.dataLayers[i].name + '_' + i)[0].getFeatureById(i + '_feature_' + this.map.olMap.id + '_' + j));
                                }
                            }
                        }
                    }
                }
            }
        },
        /*
         * Check si des emprises sont pr�sentes dans les layers
         * 
         */
        testDatas: function () {
            var noEmprise = true;
            for (var i = 0; i < this.dataLayers.length; i++) {
                if (this.dataLayers[i].holds.length > 0 || this.dataLayers[i].editable) {
                    noEmprise = false;
                }
            }
//			if(noEmprise){
//				//this.destroy();
//			}else{
            this.initFeatureByNotice();
            this.initEvt();
//			}

        },
        formatLonlats: function (lonLat) {
            var lat = lonLat.lat;
            var long = lonLat.lon;
            var ns = OpenLayers.Util.getFormattedLonLat(lat);
            var ew = OpenLayers.Util.getFormattedLonLat(long, 'lon');
            return ns + ', ' + ew + ' (' + (Math.round(lat * 10000) / 10000) + ', ' + (Math.round(long * 10000) / 10000) + ')';
        },
        callbackAffinage: function () {
            var bounds = this.map.olMap.calculateBounds();
            var geom = bounds.toGeometry();
            geom = geom.transform(this.projTo, this.projFrom);
            dom.byId('wktAffinage').value = this.formatWKT.extractGeometry(geom);
            document.affineRecherche.submit();
        },
        /*
         * Handler sur l'activation des controles
         * 
         */
        handlerControlsActivated: function (e) {
            switch (this.mode) {
                case 'edition':
                    var callbackValidatePt = lang.hitch(this, this.validatePt);
                    var callbackAddButton = lang.hitch(this, this.addButtonEdit);
                    var callbackOnChangeInputSex = lang.hitch(this, this.onChangeInputSex);
                    var callbackOnChangeInputLonLat = lang.hitch(this, this.onChangeInputLonLat);
                    if (dom.byId('ptsDetails') == null) {
                        switch (e.object.CLASS_NAME) {
                            case 'OpenLayers.Control.DrawFeature':
                                this.map_controls.del.unselectAll();
                                this.map_controls.select.unselectAll();
                                this.featureSelected = null;
                                switch (e.object.displayClass) {
                                    case 'olControlDrawFeaturePolygon':
                                        this.createEditForm("creation", "OpenLayers.Geometry.Polygon");
                                        break;
                                    case 'olControlDrawFeaturePath':
                                        this.createEditForm("creation", "OpenLayers.Geometry.LineString");
                                        break;
                                    case 'olControlDrawFeaturePoint':
                                        this.createEditForm("creation", "OpenLayers.Geometry.Point");
                                        break;
                                    case 'olControlDrawFeatureRegPoly':
                                        var subPanel = query('.subpanel', e.object.map.div);
                                        for (var i = 0; i < subPanel.length; i++) {
                                            subPanel[i].className = "subpanelActive " + subPanel[i].className.split(' ')[1] + " olButton";
                                        }
                                        break;
                                }
                                break;
                            case "OpenLayers.Control.ModifyFeature":
                                switch (e.object.displayClass) {
                                    case 'olControlDragFeature':
                                        //Si select feature contient un element-> on l'utilise
                                        if (this.featureSelected != null) {
                                            this.map_controls.drag.selectFeature(this.featureSelected);
                                        }
                                        break;
                                    case 'olControlModifyFeature':
                                        //Si select feature contient un element-> on l'utilise
                                        if (this.featureSelected != null) {
                                            this.map_controls.edition.selectFeature(this.featureSelected);
                                        }
                                        break;
                                    case 'olControlResizeFeature':
                                        if (this.featureSelected != null) {
                                            this.map_controls.redimensionne.selectFeature(this.featureSelected);
                                        }
                                        break;
                                }
                                break;
                            case "OpenLayers.Control.SelectFeature":
                                switch (e.object.displayClass) {
                                    case 'olControlDelete':
                                        //Si select feature contient un element-> on l'utilise
                                        if (this.featureSelected != null) {
                                            this.map_controls.del.select(this.featureSelected);
                                            this.featureSelected = null;
                                        }
                                        break;
                                    case 'olControlSelectFeature':
                                        //Si select feature contient un element-> on l'utilise
                                        if (this.featureSelected != null) {
                                            this.map_controls.select.select(this.featureSelected);
                                        }
                                        break;
                                }
                                break;
                            case "OpenLayers.Control.Navigation":
                                this.map_controls.del.unselectAll();
                                this.map_controls.select.unselectAll();
                                this.featureSelected = null;
                                break;
                            default:
                                break;
                        }
                        this.removeTemporaryFeature();
                    }
                    else {
                        if (dom.byId('formManuel') != null)
                            domConstruct.destroy('formManuel');
                        if (dom.byId('ptsDetails') != null)
                            domConstruct.destroy('ptsDetails');
                        this.handlerControlsActivated(e);
                    }
                    break;
                case 'search_criteria':
                    switch (e.object.CLASS_NAME) {
                        case 'OpenLayers.Control.DrawFeature':
                            this.map_controls.del.unselectAll();
                            this.map_controls.select.unselectAll();
                            this.featureSelected = null;
                            if (e.object.displayClass == 'olControlDrawFeatureRegPoly') {
                                //Permet d'effectuer le changement uniquement sur la map courante
                                var subPanel = query('.subpanel', e.object.map.div);
                                for (var i = 0; i < subPanel.length; i++) {
                                    subPanel[i].className = "subpanelActive " + subPanel[i].className.split(' ')[1] + " olButton";
                                }
                            }
                            break;
                        case "OpenLayers.Control.ModifyFeature":
                            switch (e.object.displayClass) {
                                case 'olControlDragFeature':
                                    if (this.featureSelected != null) {
                                        this.map_controls.drag.selectFeature(this.featureSelected);
                                    }
                                    break;
                                case 'olControlModifyFeature':
                                    if (this.featureSelected != null) {
                                        this.map_controls.edition.selectFeature(this.featureSelected);
                                    }
                                    break;
                            }
                            break;
                        case "OpenLayers.Control.SelectFeature":
                            switch (e.object.displayClass) {
                                case 'olControlDelete':
                                    if (this.featureSelected != null) {
                                        if (this.map_controls.del.layer.selectedFeatures.indexOf(this.featureSelected) == -1) {
                                            this.map_controls.del.select(this.featureSelected);
                                            this.featureSelected = null;
                                        }
                                    }
                                    break;
                                case 'olControlSelectFeature':
                                    if (this.featureSelected != null) {
                                        if (this.map_controls.select.layer.selectedFeatures.indexOf(this.featureSelected) == -1) {
                                            this.map_controls.select.select(this.featureSelected);
                                        }
                                    }
                                    break;
                            }
                            break;
                        case "OpenLayers.Control.Navigation":
                            this.map_controls.del.unselectAll();
                            this.map_controls.select.unselectAll();
                            this.featureSelected = null;
                            break;
                        default:
                            break;
                    }
                    break;
                case 'visualization':
                    switch (e.object.CLASS_NAME) {
                        case "OpenLayers.Control.SelectFeature":
                            if (this.featureSelected != null) {
                                if (this.map_controls.select.layer.selectedFeatures.indexOf(this.featureSelected) == -1) {
                                    this.map_controls.select.select(this.featureSelected);
                                }
                            }
                            break;
                        case "OpenLayers.Control.Navigation":
                            this.map_controls.select.unselectAll();
                            this.featureSelected = null;
                            break;
                        default:
                            break;
                    }
                    break;
            }


        },
        handlerControlsDeactivated: function (e) {
            if (e.object.displayClass == "olControlDrawFeatureRegPoly") {
                for (var key in this.map_controls) {
                    if (this.map_controls[key].panel_div != null && this.map_controls[key].panel_div.className.match(/subpanel/g) != null) {
                        //Modif class & property active
                        this.map_controls[key].panel_div.className = this.map_controls[key].panel_div.className.replace('subpanelActive', 'subpanel');
                        this.map_controls[key].panel_div.className = this.map_controls[key].panel_div.className.replace('Enabled', '');
                        this.map_controls[key].active = false;
                        if (this.map_controls[key].title == pmbDojo.messages.getMessage("carto", "carto_control_rectangle")) {
                            this.map_controls[key].panel_div.className = this.map_controls[key].panel_div.className.replace('btnRectangle', 'btnRectangleEnabled');
                            this.map_controls[key].active = true;
                        }
                    }
                }
                this.map_controls.regPoly.handler.setOptions({sides: 4, irregular: true});
            }
        },
        /*
         * Fonction appel�e lors de l'appui sur le bouton d'ajout de point du formulaire de saisie fine des points d'une features (ligne & polygone uniquement)  
         * La fonction ajoute une ligne de plus aux formulaires de saisie
         */
        addButtonEdit: function (e) {
            var pts = query('.pt_lat');
            var nbPts = pts.length;
            var callbackOnChangeInputSex = lang.hitch(this, this.onChangeInputSex);
            var callbackOnChangeInputLonLat = lang.hitch(this, this.onChangeInputLonLat);
            domConstruct.place("<br/><div id='pt_" + parseInt(nbPts + 1) + "'><label>" + pmbDojo.messages.getMessage("carto", "carto_point_label") + " " + parseInt(nbPts + 1) + ":</label><br/>" +
                    "<label for='pt_" + parseInt(nbPts + 1) + "_lon'>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + ": </label><input type='text' class='pt_lon' id='pt_" + parseInt(nbPts + 1) + "_lon' name='pt_" + parseInt(nbPts + 1) + "_lon' value='' />" +
                    "<label for='pt_" + parseInt(nbPts + 1) + "_lat'> " + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + ": </label><input type='text' class='pt_lat' id='pt_" + parseInt(nbPts + 1) + "_lat' name='pt_" + parseInt(nbPts + 1) + "_lat' value='' /></div>", query('.ptManuel')[0].childNodes[query('.ptManuel')[0].childNodes.length - 1], "after");
            domConstruct.place("<div><label> " + pmbDojo.messages.getMessage("carto", "carto_point_label") + " " + parseInt(nbPts + 1) + ": </label></br>" +
                    "<div style='float:left;'><label>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + ": </label><input style='width:30px' type='text' value='' id='pt_" + parseInt(nbPts + 1) + "_lon_deg' name='pt_" + parseInt(nbPts + 1) + "_lon_deg'/><label>\xB0</label><input style='width:30px' type='text' value='' id='pt_" + parseInt(nbPts + 1) + "_lon_min' name='pt_" + parseInt(nbPts + 1) + "_lat_min'/><label>'</label><input style='width:30px' type='text' value='' id='pt_" + parseInt(nbPts + 1) + "_lon_sec' name='pt_" + parseInt(nbPts + 1) + "_lat_sec'/><label>\"</label>&nbsp;&nbsp;</div>" +
                    "<div><label>" + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + ": </label><input style='width:30px' type='text' value='' id='pt_" + parseInt(nbPts + 1) + "_lat_deg' name='pt_" + parseInt(nbPts + 1) + "_lat_deg'/><label>\xB0</label><input style='width:30px' type='text' value='' id='pt_" + parseInt(nbPts + 1) + "_lat_min' name='pt_" + parseInt(nbPts + 1) + "_lat_min'/><label>'</label><input style='width:30px' type='text' value='' id='pt_" + parseInt(nbPts + 1) + "_lat_sec' name='pt_" + parseInt(nbPts + 1) + "_lat_sec'/><label>\"</label></div></div><br/>", dom.byId('saisieDegSex').childNodes[dom.byId('saisieDegSex').childNodes.length - 1], "after");

            //TODO: add events sur edit de ce pt ajout�
            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lat_deg"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lat_min"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lat_sec"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lon_deg"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lon_min"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lon_sec"), 'change', callbackOnChangeInputSex);

            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lon"), 'change', callbackOnChangeInputLonLat);
            on(dom.byId("pt_" + parseInt(nbPts + 1) + "_lat"), 'change', callbackOnChangeInputLonLat);
        },
        /*
         * Validation des points en mode cr�ation
         */
        validatePt: function (e) {
        },
        selectFeatureEdition: function (e) {
            if (this.getCurrentActivatedControl().displayClass == "olControlModifyFeature") {
                this.editedFeature = e.feature;
                var arrayPtSketch = [];
                var callbackCreatePtForm = lang.hitch(this, this.createFormFromFeature);
                if (dom.byId('ptsDetails') != null) {
                    domConstruct.destroy('ptsDetails');
                }
                switch (e.feature.geometry.CLASS_NAME) {
                    case "OpenLayers.Geometry.Polygon":
                    	var nbPoints = e.feature.geometry.components[0].components.length - 1;
                        domConstruct.place("<div id='ptsDetails' featureid='" + e.feature.id + "' class='ptsDetails'><h2>" + pmbDojo.messages.getMessage("carto", "carto_polygon_form_label") + "</h2>" +
                                "<p>" + pmbDojo.messages.getMessage("carto", "carto_polygon_nb_pt").replace('%s', parseInt(nbPoints)) + ".</p>" + 
                        		((nbPoints > MAX_POLYGON_POINTS) ? '' : 
                    			"<input type='button' class='bouton' id='ptsEdit' value='" + pmbDojo.messages.getMessage("carto", "carto_show_points_label") + "'/>") +
                                "</div>", dom.byId('map_manual_edition'), "after");
                        if (nbPoints <= MAX_POLYGON_POINTS) {
                        	on(dom.byId('ptsEdit'), 'click', callbackCreatePtForm);
                        }
                        break;
                    case "OpenLayers.Geometry.LineString":
                    	var nbPoints = e.feature.geometry.components.length;
                        domConstruct.place("<div id='ptsDetails' featureid='" + e.feature.id + "' class='ptsDetails'><h2>" + pmbDojo.messages.getMessage("carto", "carto_path_form_label") + "</h2>" +
                                "<p>" + pmbDojo.messages.getMessage("carto", "carto_path_nb_pt").replace('%s', nbPoints) + ".</p>" +
                                ((nbPoints > MAX_POLYGON_POINTS) ? '' : 
                    			"<input type='button' class='bouton' id='ptsEdit' value='" + pmbDojo.messages.getMessage("carto", "carto_show_points_label") + "'/>") +
                                "</div>", dom.byId('map_manual_edition'), "after");
                        if (nbPoints <= MAX_POLYGON_POINTS) {
                        	on(dom.byId('ptsEdit'), 'click', callbackCreatePtForm);
                        }                        
                        break;
                    case "OpenLayers.Geometry.Point":
                        domConstruct.place("<div id='ptsDetails' featureid='" + e.feature.id + "' class='ptsDetails'><h2>" + pmbDojo.messages.getMessage("carto", "carto_point_form_label") + "</h2>" +
                                "<input type='button' class='bouton' id='ptsEdit' value='" + pmbDojo.messages.getMessage("carto", "carto_show_point_label") + "'/>" +
                                "</div>", dom.byId('map_manual_edition'), "after");
                        on(dom.byId('ptsEdit'), 'click', callbackCreatePtForm);
                        break;
                }
            }
        },
        /*
         * Fonction appel�e a la selection d'une feature en �dition 
         * La fonction va g�n�rer un formulaire pr�rempli contenant les coordon�es des points de l'emprises
         */
        createFormFromFeature: function (e) {
            //TODO:IF SUR LA GEOMETRY DE LA FEATURE EN COURS DEDITION, IF POINT CAS PARTICULIER, PAS DE DRAGVERTICE, SEULEMENT UN MOVE DE LA FEATURE
            this.createEditForm("edition", this.editedFeature.geometry.CLASS_NAME, this.editedFeature);
        },
        /*
         * Validation des points en mode edition
         */
        validateModification: function () {
            var ptsEdited = query('div[edited="true"]');

            //Recuperer toutes les valeurs des diff�rents inputs, les convertirs & les affecters aux diff�rents pts �dit�s 
            //End le false drag uniquement � la fin pour �viter de redraw toutes les sketchs features 
            if (dom.byId('typeForm').value == "edition") {
                var arrayObjCoords = [];
                if (this.editedFeature.geometry.CLASS_NAME == "OpenLayers.Geometry.Point" || this.editedFeature.geometry.CLASS_NAME == "OpenLayers.Geometry.Polygon") {
                    var newLat = dom.byId("pt_1_lat").value;
                    var newLon = dom.byId("pt_1_lon").value;
                    var newAddress = dom.byId("pt_1_search_address").value;
                    var newCity = dom.byId("pt_1_search_city").value;
                	var newPostcode = dom.byId("pt_1_search_postcode").value;
                    var isRadioAddressChecked = dom.byId("address").checked;
                    
                	if (isRadioAddressChecked) {
                		if (newAddress || newCity || newPostcode) {
                			var deferredPoint = new Deferred();
                			this.searchByAddress('', deferredPoint);
                			deferredPoint.then(lang.hitch(this, function(data) {
                        		if (data) {
                        			var newPt = new OpenLayers.LonLat(data.lon, data.lat);
                        			newPt = newPt.transform(this.projFrom, this.projTo);
                                    if (!this.checkPt(data.lat, data.lon, 1)) {
                                        return false;
                                    }
                        			this.editedFeature.move(newPt);
                        			this.map.olMap.panTo(newPt);
                        			this.editedFeature['attributes']['data-display_address'] = data.display_name;
                        			this.editedFeature['attributes']['data-address'] = data.address;
                        			this.setHiddenField(this.editedFeature);
                                    this.saveCurrentState();
                                    this.removeTemporaryFeature();
                        		}
                        	}));
                		}
                	} else {
                		var newPt = new OpenLayers.LonLat(newLon, newLat);
	                	newPt = newPt.transform(this.projFrom, this.projTo);
	                    if (!this.checkPt(newLat, newLon, 1)) {
	                        return false;
	                    }
	                    this.editedFeature.move(newPt);
	                    this.map.olMap.panTo(newPt);
	                    this.setHiddenField(this.editedFeature);
	                    this.saveCurrentState();
                	}
                } else {
                    var arrayPtSketch = [];
                    var arrayNewPt = [];
                    for (var i = 0; i < this.editedFeature.layer.features.length; i++) {
                        if (this.editedFeature.layer.features[i]._index == undefined && this.editedFeature.layer.features[i]._sketch != undefined && this.editedFeature.layer.features[i]._sketch == true) {
                            arrayPtSketch.push(this.editedFeature.layer.features[i]);
                        }
                    }
                    var latLon;
                    for (var i = 0; i < ptsEdited.length; i++) {
                        if (ptsEdited[i].parentNode.getAttribute('class') != "newPt") {
                            var inputs = query('input', ptsEdited[i]);
                            latLon = {
                                lat: inputs[1].value,
                                lon: inputs[0].value,
                                id: parseInt(inputs[0].id.split('_')[1]) - 1
                            }
                            arrayObjCoords.push(latLon);
                        }
                        else {
                            arrayNewPt.push(ptsEdited[i]);
                        }
                    }
                    for (var i = 0; i < arrayObjCoords.length; i++) {
                        var newLat = arrayObjCoords[i].lat;
                        var newLon = arrayObjCoords[i].lon;
                        if (!this.checkPt(newLat, newLon, arrayObjCoords[i].id + 1)) {
                            return false;
                        }
                    }
                    for (var i = 0; i < arrayObjCoords.length; i++) {
                        var newLat = arrayObjCoords[i].lat;
                        var newLon = arrayObjCoords[i].lon;
                        var newPt = new OpenLayers.LonLat(newLon, newLat);
                        newPt = newPt.transform(this.projFrom, this.projTo);
                        this.map_controls.edition.dragStart(arrayPtSketch[arrayObjCoords[i].id]);
                        this.map_controls.edition.dragVertex(arrayPtSketch[arrayObjCoords[i].id], this.editedFeature.layer.getViewPortPxFromLonLat(newPt));
                        this.map_controls.edition.dragComplete(arrayPtSketch[arrayObjCoords[i].id]);
                    }
                    if (arrayNewPt.length > 0) {
                        for (var i = arrayNewPt.length - 1; i >= 0; i--) {
                            var index = arrayNewPt[i].parentNode.id.split('_')[2];
                            var divDegreeDec = arrayNewPt[i];
                            var inputLon = query('input', divDegreeDec)[0];
                            var inputLat = query('input', divDegreeDec)[1];
                            if (!this.checkPt(inputLat.value, inputLon.value, i, true)) {
                                return false;
                            }
                            var newPtOL = new OpenLayers.Geometry.Point(inputLon.value, inputLat.value);
                            newPtOL = newPtOL.transform(this.projFrom, this.projTo);
                            if (this.editedFeature.geometry.CLASS_NAME == "OpenLayers.Geometry.Polygon") {
                                this.editedFeature.geometry.components[0].addComponent(newPtOL, index);
                            }
                            else {
                                this.editedFeature.geometry.addComponent(newPtOL, index);
                            }
                        }
                        this.setHiddenField(this.editedFeature);
                        this.saveCurrentState();
                    }
                }
                if (dom.byId('ptsDetails') != null) {
                    domConstruct.destroy('ptsDetails');
                }
                if (this.editedFeature.layer.events.listeners.featureover != null) {
                    this.editedFeature.layer.events.remove('featureout');
                    this.editedFeature.layer.events.remove('featureover');
                }


                //Feature Unselection
                this.map_controls.edition.unselectFeature(this.editionFeature);
            } else {
                var typeForm;
                if (this.map_controls.point.active) {
                	typeForm = "formulairePoint";
                } else if (this.map_controls.ligne.active) {
                	typeForm = "formulaireLigne";
                } else {
                	typeForm = "formulairePoly";
                }	
                switch (typeForm) {
                    case 'formulairePoint':
                		var deferredPoint = new Deferred();
                    	var address = dom.byId("pt_1_search_address");
                    	var city = dom.byId("pt_1_search_city");
                    	var postcode = dom.byId("pt_1_search_postcode");
                    	if ((address.value || city.value || postcode.value) && dom.byId("address").checked) {
                    		this.searchByAddress('', deferredPoint);
                    	} else {
                    		deferredPoint.reject();
                    	}
                    	deferredPoint.then(lang.hitch(this, function(data) {
                    		if (data) {
                    			dom.byId('pt_1_lon').value = data.lon;
                    			dom.byId('pt_1_lat').value = data.lat;
                    		}
                    		this.drawPoint(data);
                            this.removeTemporaryFeature();
                    	}), lang.hitch(this, this.drawPoint));
                        break;
                    case 'formulaireLigne':
                        var divPt = ptsEdited;
                        var nbPtEdited = divPt.length;
                        if (nbPtEdited >= 2) {
                            for (var i = 0; i < nbPtEdited; i++) {
                                if (!this.checkPt(query('input', divPt[i])[0].value, query('input', divPt[i])[1].value, query('input', divPt[i])[0].id.split('_')[1])) {
                                    return false;
                                }
                            }
                            var firstLonLat = new OpenLayers.LonLat(query('input', divPt[0])[0].value, query('input', divPt[0])[1].value);
                            firstLonLat = firstLonLat.transform(this.projFrom, this.projTo);
                            var pt = this.map.olMap.layers[0].getViewPortPxFromLonLat(firstLonLat);
                            var nbPts = query('.pt_lon');
                            this.map_controls['ligne'].handler.createFeature(pt);
                            for (var i = 1; i < nbPtEdited; i++) {
                                var lonTemp = query('input', divPt[i])[0].value;
                                var latTemp = query('input', divPt[i])[1].value;
                                var lonLatTempo = new OpenLayers.LonLat(lonTemp, latTemp);
                                lonLatTempo = lonLatTempo.transform(this.projFrom, this.projTo);
                                var lonLatPx = this.map.olMap.layers[0].getViewPortPxFromLonLat(lonLatTempo);
                                this.map_controls['ligne'].handler.addPoint(lonLatPx);
                            }
                            this.map_controls['ligne'].handler.addPoint(pt);
                            this.map_controls['ligne'].handler.finishGeometry();
                            this.map_controls.polygone.deactivate();
                            if (dom.byId('formManuel') != null)
                                domConstruct.destroy('formManuel');
                            this.map_controls.edition.activate();

                        }
                        else {
                            alert(pmbDojo.messages.getMessage("carto", "carto_warning_not_enough_points"));
                        }
                        break;
                    case 'formulairePoly' :
                    	var deferredPoly = new Deferred();
                    	var address = dom.byId("pt_1_search_address");
                    	var city = dom.byId("pt_1_search_city");
                    	var postcode = dom.byId("pt_1_search_postcode");
                    	
                    	if ((address.value || city.value || postcode.value) && dom.byId("address").checked) {
                    		this.searchByAddress('', deferredPoly);
                    		data = temporaryData;
                    		if (data) {
                    			var feature = this.formatWKT.read(data.geotext);
                                if (feature != undefined) {
                                    var newPoly = feature.geometry.transform(this.projFrom, this.projTo);
                                    this.map.olMap.getLayersByName(this.dataLayers[0].name + "_0")[0].addFeatures([feature]);
                        			this.map.olMap.zoomToExtent(feature.geometry.bounds);
                        			feature['attributes']['data-display_address'] = data.display_name;
                        			feature['attributes']['data-address'] = data.address;
                                    this.setHiddenField(feature);
                                    this.map_controls.polygone.deactivate();
                                    this.map_controls.edition.activate();
                                    this.removeTemporaryFeature();
                                    //Create feature associated
                                }
                    		}
                        
                    	} else {
                    		deferredPoly.cancel();
                    		var divPt = query('div[edited="true"]');
                            var nbPtEdited = divPt.length;
                            if (nbPtEdited >= 3) {
                                for (var i = 0; i < nbPtEdited; i++) {
                                    if (!this.checkPt(query('input', divPt[i])[0].value, query('input', divPt[i])[1].value, query('input', divPt[i])[0].id.split('_')[1])) {
                                        return false;
                                    }
                                }
                                var firstLonLat = new OpenLayers.LonLat(query('input', divPt[0])[0].value, query('input', divPt[0])[1].value);
                                firstLonLat = firstLonLat.transform(this.projFrom, this.projTo);
                                var pt = this.map.olMap.layers[0].getViewPortPxFromLonLat(firstLonLat);
                                var nbPts = query('.pt_lon');
                                this.map_controls['polygone'].handler.createFeature(pt);
                                for (var i = 1; i < nbPtEdited; i++) {
                                    var lonTemp = query('input', divPt[i])[0].value;
                                    var latTemp = query('input', divPt[i])[1].value;
                                    var lonLatTempo = new OpenLayers.LonLat(lonTemp, latTemp);
                                    lonLatTempo = lonLatTempo.transform(this.projFrom, this.projTo);
                                    var lonLatPx = this.map.olMap.layers[0].getViewPortPxFromLonLat(lonLatTempo);
                                    this.map_controls['polygone'].handler.addPoint(lonLatPx);
                                }
                                this.map_controls['polygone'].handler.addPoint(pt);
                                this.map_controls['polygone'].handler.finishGeometry();
                                this.map_controls.polygone.deactivate();
                                if (dom.byId('formManuel') != null)
                                    domConstruct.destroy('formManuel');
                                this.map_controls.edition.activate();
                            }
                            else {
                                alert(pmbDojo.messages.getMessage("carto", "carto_warning_not_enough_points"));
                            }
                    	}
                        break;
                    default:
                        break;
                }
            }

        },
        
        drawPoint : function(data) {
        	if (!this.checkPt(dom.byId('pt_1_lon').value, dom.byId('pt_1_lat').value, 1)) {
                return false;
            }
            var lonLat = new OpenLayers.LonLat(dom.byId('pt_1_lon').value, dom.byId('pt_1_lat').value);
            lonLat = lonLat.transform(this.projFrom, this.projTo);
            var pt = this.map.olMap.layers[0].getViewPortPxFromLonLat(lonLat);
            this.map_controls['point'].handler.createFeature(pt);
            this.map_controls['point'].handler.finalize();
            if (data) {
            	this.featureSelected['attributes']['data-display_address'] = data.display_name;
    			this.featureSelected['attributes']['data-address'] = data.address;
            	this.setHiddenField(this.featureSelected);
            } else {
            	this.setHiddenField(this.featureSelected);
            }
            this.map_controls.point.deactivate();
            if (dom.byId('formManuel') != null) {
                domConstruct.destroy('formManuel');
            }
            this.map_controls.edition.activate();
        },
        
        drawPolygon : function() {
        	var divPt = query('div[edited="true"]');
            var nbPtEdited = divPt.length;
            if (nbPtEdited >= 3) {
                for (var i = 0; i < nbPtEdited; i++) {
                    if (!this.checkPt(query('input', divPt[i])[0].value, query('input', divPt[i])[1].value, query('input', divPt[i])[0].id.split('_')[1])) {
                        return false;
                    }
                }
                var firstLonLat = new OpenLayers.LonLat(query('input', divPt[0])[0].value, query('input', divPt[0])[1].value);
                firstLonLat = firstLonLat.transform(this.projFrom, this.projTo);
                var pt = this.map.olMap.layers[0].getViewPortPxFromLonLat(firstLonLat);
                var nbPts = query('.pt_lon');
                this.map_controls['polygone'].handler.createFeature(pt);
                for (var i = 1; i < nbPtEdited; i++) {
                    var lonTemp = query('input', divPt[i])[0].value;
                    var latTemp = query('input', divPt[i])[1].value;
                    var lonLatTempo = new OpenLayers.LonLat(lonTemp, latTemp);
                    lonLatTempo = lonLatTempo.transform(this.projFrom, this.projTo);
                    var lonLatPx = this.map.olMap.layers[0].getViewPortPxFromLonLat(lonLatTempo);
                    this.map_controls['polygone'].handler.addPoint(lonLatPx);
                }
                this.map_controls['polygone'].handler.addPoint(pt);
                this.map_controls['polygone'].handler.finishGeometry();
                this.map_controls.polygone.deactivate();
                if (dom.byId('formManuel') != null)
                    domConstruct.destroy('formManuel');
                this.map_controls.edition.activate();
            }
            else {
                alert(pmbDojo.messages.getMessage("carto", "carto_warning_not_enough_points"));
            }
        },
        
        /* Callback appel� lors d'une modification dans un input de type degr�s decimal
         * Report de la modification dans le formulaire de saisie en degr�s sexagesimaux
         */
        onChangeInputLonLat: function (e) {
            var numPt = e.target.id.split('_')[1];
            var typePt = e.target.id.split('_')[2];
            var newValues = this.toSexagesimal(e.target.value);
            dom.byId('pt_' + numPt + '_' + typePt + '_deg').value = newValues[0];
            dom.byId('pt_' + numPt + '_' + typePt + '_min').value = newValues[1];
            dom.byId('pt_' + numPt + '_' + typePt + '_sec').value = newValues[2];
            var parent = e.target.parentNode;
            parent.setAttribute('edited', 'true');
        },
        /* 
         * Callback appel� lors d'une modification dans un input de type degr� sexag�simal
         * Report de la modification dans le formulaire de saisie en degr�s d�cimaux
         */
        onChangeInputSex: function (e) {
            var ligneModifie = e.target.parentNode;
            var inputs = ligneModifie.querySelectorAll('input');
            var numPt = e.target.id.split('_')[1];
            var typePt = e.target.id.split('_')[2];
            var deg = dom.byId('pt_' + numPt + '_' + typePt + '_deg').value;
            var min = dom.byId('pt_' + numPt + '_' + typePt + '_min').value;
            var sec = dom.byId('pt_' + numPt + '_' + typePt + '_sec').value;
            dom.byId('pt_' + numPt + '_' + typePt).value = this.toDecimal(deg, min, sec);
            dom.byId('pt_' + numPt + '_' + typePt).parentNode.setAttribute('edited', 'true');
        },
        onEnterValidForm: function(e) {
        	if (e.key === "Enter") {
        		this.listerAdresses();
        	}
        },
        highlightPoint: function (e) {
            var divPoint = e.target;
            var style = {pointRadius: 6, fillColor: "#0000ff", strokeColor: "#0000ff", strokeWidth: 1, fillOpacity: 0.4};
            var numPt = divPoint.id.split("_")[1];
            var arrayPtSketch = [];
            for (var i = 0; i < this.editedFeature.layer.features.length; i++) {
                if (this.editedFeature.layer.features[i]._index == undefined && this.editedFeature.layer.features[i]._sketch != undefined && this.editedFeature.layer.features[i]._sketch == true) {
                    arrayPtSketch.push(this.editedFeature.layer.features[i]);
                }
            }
            if (arrayPtSketch[numPt - 1] != null) {
                this.editedFeature.layer.drawFeature(arrayPtSketch[numPt - 1], style);
            }

        },
        downlightPoint: function (e) {
            var divPoint = e.target;
            var numPt = divPoint.id.split("_")[1];
            var style = {pointRadius: 6, fillColor: "#ee9900", strokeColor: "#ee9900", strokeWidth: 1, fillOpacity: 0.4};
            var arrayPtSketch = [];
            for (var i = 0; i < this.editedFeature.layer.features.length; i++) {
                if (this.editedFeature.layer.features[i]._index == undefined && this.editedFeature.layer.features[i]._sketch != undefined && this.editedFeature.layer.features[i]._sketch == true) {
                    arrayPtSketch.push(this.editedFeature.layer.features[i]);
                }
            }
            if (arrayPtSketch[numPt - 1] != null) {
                this.editedFeature.layer.drawFeature(arrayPtSketch[numPt - 1], style);
            }
        },
        featureEditOutOver: function (e) {
            var styleOver = {pointRadius: 6, fillColor: "#0000ff", strokeColor: "#0000ff", strokeWidth: 1, fillOpacity: 0.4};
            var styleDefault = {pointRadius: 6, fillColor: "#ee9900", strokeColor: "#ee9900", strokeWidth: 1, fillOpacity: 0.4};
            var arrayPtSketch = [];
            if (this.editedFeature.layer != null) {
            	for (var i = 0; i < this.editedFeature.layer.features.length; i++) {
                    if (this.editedFeature.layer.features[i]._index == undefined && this.editedFeature.layer.features[i]._sketch != undefined && this.editedFeature.layer.features[i]._sketch == true) {
                        arrayPtSketch.push(this.editedFeature.layer.features[i]);
                    }
                }
            }
            if (e.feature.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
                var indexFeature = arrayPtSketch.indexOf(e.feature);
                if (indexFeature != -1) {
                    if (e.type == "featureover") {
                        this.editedFeature.layer.drawFeature(arrayPtSketch[indexFeature], styleOver);
                        if (dom.byId('pt_' + parseInt(indexFeature + 1)) != null)
                            domStyle.set(dom.byId('pt_' + parseInt(indexFeature + 1)), "border", "1px solid red");
                    }
                    else {
                        this.editedFeature.layer.drawFeature(arrayPtSketch[indexFeature], styleDefault);
                        if (dom.byId('pt_' + parseInt(indexFeature + 1)) != null)
                            domStyle.set(dom.byId('pt_' + parseInt(indexFeature + 1)), "border", "");
                    }

                }
            }
        },
        buttonExportWkt: function () {
            if (this.map_controls.select.layer.selectedFeatures.length > 0 || this.featureSelected != null) {
                var wktString = "";
                var callbackPopupImportClose = lang.hitch(this, this.destroyPopup);
                if (this.map_controls.select.layer.selectedFeatures.length > 0) {
                    var arrayFeatureSelect = [];
                    var layer = this.map_controls.select.layer;
                    var nbFeature = layer.selectedFeatures.length;
                    for (var i = 0; i < nbFeature; i++) {
                        var feature = layer.selectedFeatures[i].clone();
                        feature.geometry.transform(this.projTo, this.projFrom);
                        wktString += "<textarea name='textarea' id='wktExport_" + i + "' rows='10' cols='80'>" + this.formatWKT.write(feature) + "</textarea><br/>";
                    }
                }
                else {
                    wktString = "<textarea name='textarea' id='wktExport_" + i + "' rows='10' cols='80'>" + this.formatWKT.write(this.featureSelected) + "</textarea><br/>";
                }
                if(!this.dialogWktExport) {
                	this.dialogWktExport = new Dialog({
                        title: pmbDojo.messages.getMessage("carto", "carto_wkt_export_label"),
                        content: wktString,
                        id: 'exportWkt',
                        style: "width: 450px"
                    });
                }
                this.dialogWktExport.show();
                this.dialogWktExport.onHide = callbackPopupImportClose;
            }
            else {
                alert(pmbDojo.messages.getMessage("carto", "carto_warning_no_feature_selected"));
            }
        },
        buttonImportWkt: function () {
            //Le popup doit �tre stock� pour la suppression au onHide
            var callbackValideImport = lang.hitch(this, this.valideImportWkt);
            var callbackAddTextArea = lang.hitch(this, this.addTextWkt);
            var callbackPopupImportClose = lang.hitch(this, this.destroyPopup);
            if(!this.dialogWktImport) {
            	this.dialogWktImport = new Dialog({
                    title: pmbDojo.messages.getMessage("carto", "carto_wkt_import_label"),
                    id: 'importWkt',
                    content: "<textarea name='textarea' class='textareaWkt' placeholder='" + pmbDojo.messages.getMessage("carto", "carto_wkt_import_placeholder") + ".' rows='10' cols='50'></textarea><br/><input type='button' class='bouton' id='addWktText' value='+'/><input type='button' class='bouton' value='" + pmbDojo.messages.getMessage("carto", "carto_validate_label") + "' id='valideImport'/>",
                    style: "width: 300px"
                });
            }
            this.dialogWktImport.show();
            on(dom.byId('valideImport'), 'click', callbackValideImport);
            on(dom.byId('addWktText'), 'click', callbackAddTextArea);
            this.dialogWktImport.onHide = callbackPopupImportClose;
            //this.dialogWktImport.onClose = callbackPopupImportClose;
        },
        destroyPopup: function (e) {
            if (this.dialogWktImport != null) {
                this.dialogWktImport.destroy();
                this.dialogWktImport = null;
            }

            if (this.dialogWktExport != null) {
                this.dialogWktExport.destroy();
                this.dialogWktExport = null;
            }

        },
        hidePopup: function (e) {
    		if (this.dialogWktImport != null) {
    			var textAreas = query('.textareaWkt');
                var nbEmprise = textAreas.length;
                for (var i = 0; i < nbEmprise; i++) {
                	textAreas[i].value = '';
                }
				this.dialogWktImport.hide();
    		}

    		if (this.dialogWktExport != null) {
				this.dialogWktExport.hide();
			}
    		
        },
        valideImportWkt: function (e) {
            var textAreas = query('.textareaWkt');
            var nbEmprise = textAreas.length;

            for (var i = 0; i < nbEmprise; i++) {
                var valueWkt = textAreas[i].value;
                var feature = this.formatWKT.read(valueWkt);
                if (feature != undefined) {
                    feature.geometry.transform(this.projFrom, this.projTo);
                    this.map.olMap.getLayersByName(this.dataLayers[0].name + "_0")[0].addFeatures([feature]);
                    this.setHiddenField(feature);
                    //Create feature associated
                }
                else {
                    if (textAreas[i].value != "") {
                        alert(pmbDojo.messages.getMessage("carto", "carto_warning_invalid_wkt_string").replace('%s', textAreas[i].value));
                    }
                }

            }
            this.hidePopup();
        },
        addTextWkt: function () {
            domConstruct.place("<textarea name='textarea' class='textareaWkt' rows='10' placeholder='" + pmbDojo.messages.getMessage("carto", "carto_wkt_import_placeholder") + ".' cols='50'></textarea><br/>", dom.byId('addWktText'), "before");
        },
        showPatience: function () {
            this.standby.show();
        },
        hidePatience: function () {
            this.standby.hide();
            //this.standby.destroy();
        },
        initControls: function (layer) {
            this.map_controls = {};
            this.panel = {};
            switch (this.mode) {
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
                    if (!this.alreadyZoomed) {
                        layer.map.events.register("zoomend", this, this.zoomEnd);
                        this.alreadyZoomed = true;
                    }
                    break;
                case 'visualization':
                    this.initPanel();
                    this.initExport(layer);
                    this.initNavigate();
                    this.addToPanel();
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
        initPanel: function () {
            this.panel = new OpenLayers.Control.Panel(
                    {'displayClass': 'olControlEditingToolbar'}
            );
            this.map.olMap.addControl(this.panel);
        },
        initExport: function (layer) {
            this.map_controls = {};
            this.map_controls.select = new OpenLayers.Control.SelectFeature(layer, {
                title: pmbDojo.messages.getMessage("carto", "carto_select_record"),
                multiple: true,
                toggle: true,
                box: true,
                deactivate: function () {
                    if (this.map.layers[0].selectedFeatures.length > 1) {
                        this.unselectAll();
                    }
                    if (this.active) {
                        this.handlers.feature.deactivate();
                        if (this.handlers.box) {
                            this.handlers.box.deactivate();
                        }
                        if (this.layers) {
                            this.map.removeLayer(this.layer);
                        }
                    }
                    return OpenLayers.Control.prototype.deactivate.apply(
                            this, arguments
                            );
                }
            });
            var callbackBoutonExportWkt = lang.hitch(this, this.buttonExportWkt);
            this.map_controls.exportWkt = new OpenLayers.Control.Button({displayClass: 'olControlExportWkt', trigger: callbackBoutonExportWkt, title: pmbDojo.messages.getMessage("carto", "carto_wkt_export_label")});
        },
        initExportEdition: function (layer) {
            this.map_controls.select = new OpenLayers.Control.SelectFeature(layer, {
                title: pmbDojo.messages.getMessage("carto", "carto_select_record"),
                multiple: true,
                toggle: true,
                box: true,
                deactivate: function () {
                    if (this.map.layers[0].selectedFeatures.length > 1) {
                        this.unselectAll();
                    }
                    if (this.active) {
                        this.handlers.feature.deactivate();
                        if (this.handlers.box) {
                            this.handlers.box.deactivate();
                        }
                        if (this.layers) {
                            this.map.removeLayer(this.layer);
                        }
                    }
                    return OpenLayers.Control.prototype.deactivate.apply(
                            this, arguments
                            );
                }
            });
            var callbackBoutonExportWkt = lang.hitch(this, this.buttonExportWkt);
            this.map_controls.exportWkt = new OpenLayers.Control.Button({displayClass: 'olControlExportWkt', trigger: callbackBoutonExportWkt, title: pmbDojo.messages.getMessage("carto", "carto_wkt_export_label")});
        },
        initImport: function () {
            var callbackBoutonImportWkt = lang.hitch(this, this.buttonImportWkt);
            this.map_controls.importWkt = new OpenLayers.Control.Button({displayClass: 'olControlImportWkt', trigger: callbackBoutonImportWkt, title: pmbDojo.messages.getMessage("carto", "carto_wkt_import_label")});
        },
        initNavigate: function () {
            this.map_controls.normal = new OpenLayers.Control.Navigation();
        },
        initEdition: function (layer) {
            var setHiddenField = lang.hitch(this, "setHiddenField");
            var deleteCallback = lang.hitch(this, this.deleteHiddenField);
            this.map_controls.regPoly = new OpenLayers.Control.DrawFeature(layer, OpenLayers.Handler.RegularPolygon, {handlerOptions: {sides: 4, irregular: true}, 'featureAdded': setHiddenField, 'title': pmbDojo.messages.getMessage("carto", "carto_draw_label") + " " + pmbDojo.messages.getMessage("carto", "carto_control_regular_polygon"), 'displayClass': 'olControlDrawFeatureRegPoly'});
            this.map_controls.polygone = new OpenLayers.Control.DrawFeature(layer, OpenLayers.Handler.Polygon, {'featureAdded': setHiddenField, 'title': pmbDojo.messages.getMessage("carto", "carto_draw_label") + " " + pmbDojo.messages.getMessage("carto", "carto_control_polygon"), 'displayClass': 'olControlDrawFeaturePolygon'});
            this.map_controls.ligne = new OpenLayers.Control.DrawFeature(layer, OpenLayers.Handler.Path, {'featureAdded': setHiddenField, 'title': pmbDojo.messages.getMessage("carto", "carto_draw_label") + " " + pmbDojo.messages.getMessage("carto", "carto_control_path"), 'displayClass': 'olControlDrawFeaturePath'});
            this.map_controls.point = new OpenLayers.Control.DrawFeature(layer, OpenLayers.Handler.Point,
                    {
                        'title': pmbDojo.messages.getMessage("carto", "carto_draw_label") + " " + pmbDojo.messages.getMessage("carto", "carto_control_point"),
                        'displayClass': 'olControlDrawFeaturePoint'
                    });

            this.map_controls.del = new OpenLayers.Control.SelectFeature(layer,
                    {
                        displayClass: "olControlDelete",
                        title: pmbDojo.messages.getMessage("carto", "carto_delete_label"),
                        eventListeners: {
                            featurehighlighted: function overlay_delete(event) {
                                var feature = event.feature;
                                if (confirm(pmbDojo.messages.getMessage("carto", "carto_warning_delete"))) {
                                    deleteCallback(feature);
                                    layer.removeFeatures([feature]);
                                }
                            }
                        }
                    });
            var self = this;
            var callbackCreateForm = lang.hitch(this, this.selectFeatureEdition);
            this.map_controls.edition = new OpenLayers.Control.ModifyFeature(layer,
                    {
                        title: pmbDojo.messages.getMessage("carto", "carto_control_edit_label"),
                        displayClass: "olControlModifyFeature"
                    });
            this.map_controls.drag = new OpenLayers.Control.ModifyFeature(layer,
                    {
                        title: pmbDojo.messages.getMessage("carto", "carto_control_move_label"),
                        displayClass: "olControlDragFeature",
                        mode: OpenLayers.Control.ModifyFeature.DRAG
                    });
            this.map_controls.redimensionne = new OpenLayers.Control.ModifyFeature(layer,
                    {
                        title: pmbDojo.messages.getMessage("carto", "carto_control_resize_label"),
                        displayClass: "olControlResizeFeature",
                        mode: OpenLayers.Control.ModifyFeature.RESIZE
                    });
            this.map_controls.ligne.events.register('featureadded', this, function (e) {
                //Select the feature & zoom on it
                this.map_controls.ligne.deactivate();
                this.map_controls.edition.activate();
                this.map_controls.edition.selectFeature(e.feature);
                this.map.olMap.zoomToExtent(e.feature.geometry.bounds);
            });
            this.map_controls.point.events.register('featureadded', this, function (e) {
                //Select the feature & zoom on it
                this.map_controls.point.deactivate();
                this.map_controls.edition.activate();
                this.map_controls.edition.selectFeature(e.feature);
                this.map.olMap.zoomToExtent(e.feature.geometry.bounds);
            });
            this.map_controls.polygone.events.register('featureadded', this, function (e) {
                //Select the feature & zoom on it
                this.map_controls.polygone.deactivate();
                this.map_controls.edition.activate();
                this.map_controls.edition.selectFeature(e.feature);
                this.map.olMap.zoomToExtent(e.feature.geometry.bounds);
            });
            this.map_controls.regPoly.events.register('featureadded', this, function (e) {
                //Select the feature & zoom on it
                this.map_controls.regPoly.deactivate();
                this.map_controls.edition.activate();
                this.map_controls.edition.selectFeature(e.feature);
                this.map.olMap.zoomToExtent(e.feature.geometry.bounds);
            });
            on(document.body, "click", lang.hitch(this, this.cancelDraw));
        },
        initSaisieFineEtPopup: function (layer) {
            layer.events.register("featureclick", this, this.featureEditionClick);
            layer.events.register("vertexmodified", this, function (e) {
                if (this.popupEdition != null) {
                    this.popupEdition.hide();
                }
                //Prendre le form du dessus, le supprimer et le recr�er une fois le drag termin�
                if (dom.byId('detailPoint') != null) {
                    domConstruct.destroy('introPt');
                    if (dom.byId('ptsEdit') != null)
                        domStyle.set(dom.byId('ptsEdit'), "display", "block");
                }
                if (this.editedFeature.layer.events.listeners.featureover != null) {
                    this.editedFeature.layer.events.remove('featureout');
                    this.editedFeature.layer.events.remove('featureover');
                }
            });
            layer.events.register("afterfeaturemodified", this, function (e) {
                if (this.map_controls.edition.active || this.map_controls.drag.active) {
                    this.featureSelected = null;
                }
                if (this.popupEdition != null) {
                    this.popupEdition.hide();
                }
                if (e.feature != undefined) {
                    var featureId = e.feature.id;
                    if (query('[featureid="' + featureId + '"]')[0] != null) {
                        query('[featureid="' + featureId + '"]')[0].parentNode.removeChild(query('[featureid="' + featureId + '"]')[0]);
                    }
                }
            });
            layer.events.register('beforefeaturemodified', this, function (e) {
                if (e.feature.layer) {
                    this.featureSelected = e.feature;
                }
                if (this.map_controls.edition.active || this.map_controls.drag.active || this.map_controls.redimensionne.active) {
                    this.selectFeatureEdition(e);
                }
            });
            layer.events.register("featuremodified", this, function (e) {
                if (dom.byId('ptsDetails') != null) {
                    this.selectFeatureEdition(e);
                }
                this.setHiddenField(arguments[0].feature);
                var clone = e.feature.clone();
                var ptGoodCoords = clone.geometry.transform(this.projTo, this.projFrom);
                this.fillAddressDataHiddenField(ptGoodCoords);
                this.saveCurrentState();
            });
            layer.events.register("featureselected", this, function (e) {
                this.featureSelected = e.feature;
            });
            layer.events.register("featureunselected", this, function (e) {
                this.featureSelected = null;
            });
            layer.events.register("featureadded", this, function (e) {
            	var clone = e.feature.clone();
                var ptGoodCoords = clone.geometry.transform(this.projTo, this.projFrom);
                if (dom.byId('searchResultList') && dom.byId('searchResultList').style.display == "none") {
                	this.fillAddressDataHiddenField(ptGoodCoords);
                }
                this.saveCurrentState();
            });
            layer.events.register("featureremoved", this, function (e) {
                this.saveCurrentState();
            });
        },
        initPopupEdition: function (layer) {
            layer.events.register("featureclick", this, this.featureEditionClick);
            layer.events.register("vertexmodified", this, function (e) {
                if (this.popupEdition != null) {
                    this.popupEdition.hide();
                }
            });
            layer.events.register("afterfeaturemodified", this, function (e) {
                if (this.popupEdition != null) {
                    this.popupEdition.hide();
                }
            });
            layer.events.register("featuremodified", this, function (e) {
                this.setHiddenField(arguments[0].feature);
            });
            layer.events.register("featureselected", this, function (e) {
                this.featureSelected = e.feature;
            });
            layer.events.register("featureunselected", this, function (e) {
                this.featureSelected = null;
            });
            layer.events.register('beforefeaturemodified', this, function (e) {
                this.featureSelected = e.feature;
            });
        },
        initControleAffinage: function () {
            var control = new OpenLayers.Control();
            var map = this.map.olMap;
            var self = this;
            OpenLayers.Util.extend(control, {
                draw: function () {
                    this.box = new OpenLayers.Handler.Box(control,
                            {
                                "done": this.affinage
                            },
                    {
                        keyMask: OpenLayers.Handler.MOD_CTRL,
                        "boxDivClassName": "olHandlerBoxAffinage"
                    });
                    this.box.activate();
                },
                affinage: function (bounds) {
                    var ll = map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.left, bounds.bottom));
                    var ur = map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.right, bounds.top));
                    var boundsBox = new OpenLayers.Bounds();
                    boundsBox.extend(new OpenLayers.LonLat(ll.lon, ll.lat));
                    boundsBox.extend(new OpenLayers.LonLat(ur.lon, ur.lat));
                    var geom = boundsBox.toGeometry();
                    geom = geom.transform(self.projTo, self.projFrom);
                    var boundsBefore = self.map.olMap.calculateBounds();
                    var zoomLevel = self.map.olMap.zoom;
                    self.map.olMap.zoomToExtent(boundsBox);
                    if (zoomLevel != self.map.olMap.zoom) {
                        //self.map.olMap.baseLayer.events.register('loadend' , false, function(){  
                        self.standby.onHide = function () {
                            if (confirm(pmbDojo.messages.getMessage("carto", "carto_refinement_popup"))) {
                                if (document.affineRecherche == undefined) {
                                    domConstruct.place("<form method='post' name='affineRecherche' action='./catalog.php?categ=search&mode=6&sub=launch'>" +
                                            "<input type='hidden' name='search[0]' value='s_1' />" +
                                            "<input type='hidden' name='search[1]' value='f_78' />" +
                                            "<input type='hidden' name='inter_0_s_1' value='' />" +
                                            "<input type='hidden' name='op_0_s_1' value='EQ' />" +
                                            "<input type='hidden' name='field_0_s_1[]' value='" + self.searchId + "' />" +
                                            "<input type='hidden' name='inter_1_f_78' value='and' />" +
                                            "<input type='hidden' name='op_1_f_78' value='CONTAINS' />" +
                                            "<input type='hidden' id='wktAffinage' name='field_1_f_78[]' value='" + self.formatWKT.extractGeometry(geom) + "' />" +
                                            "<input type='hidden' name='explicit_search' value='1' />" +
                                            "<input type='hidden' name='launch_search' value='1' />" +
                                            "<input type='button' class='bouton' value='" + pmbDojo.messages.getMessage("carto", "carto_btn_affiner") + "' id='affinageButton'/>" +
                                            "</form>", dom.byId('map_search'), "after");
                                    on(dom.byId('affinageButton'), 'click', lang.hitch(self, self.callbackAffinage));
                                } else {
                                    dom.byId('wktAffinage').value = self.formatWKT.extractGeometry(geom);
                                }
                                /*html2canvas(document.getElementById('map_search'), {onrendered: function(canvas) {
                                 var ctxMap = canvas.getContext("2d");
                                 //G�n�ration d'une chaine a partir du svg des emprises & creation du canvas sur lequel limage va �tre apos�e

                                 var svgXml = (new XMLSerializer()).serializeToString(document.querySelector('svg'));
                                 var img = new Image();
                                 //img.setAttribute('crossOrigin','anonymous');

                                 img.src = "data:image/svg+xml;base64," + btoa(svgXml);
                                 img.onload = function(){
                                 ctxMap.drawImage(img, 0,0 );
                                 localStorage[self.searchId] = canvas.toDataURL();
                                 document.affineRecherche.submit();
                                 }
                                 },taintTest:false,allowTaint:true});*/
                                document.affineRecherche.submit();
                            } else {
                                self.map.olMap.zoomToExtent(boundsBefore);
                                self.standby.onHide = "";
                            }
                        }
                    } else {
                        if (confirm(pmbDojo.messages.getMessage("carto", "carto_refinement_popup"))) {
                            if (document.affineRecherche == undefined) {
                                domConstruct.place("<form method='post' name='affineRecherche' action='./catalog.php?categ=search&mode=6&sub=launch'>" +
                                        "<input type='hidden' name='search[0]' value='s_1' />" +
                                        "<input type='hidden' name='search[1]' value='f_78' />" +
                                        "<input type='hidden' name='inter_0_s_1' value='' />" +
                                        "<input type='hidden' name='op_0_s_1' value='EQ' />" +
                                        "<input type='hidden' name='field_0_s_1[]' value='" + self.searchId + "' />" +
                                        "<input type='hidden' name='inter_1_f_78' value='and' />" +
                                        "<input type='hidden' name='op_1_f_78' value='CONTAINS' />" +
                                        "<input type='hidden' id='wktAffinage' name='field_1_f_78[]' value='" + self.formatWKT.extractGeometry(geom) + "' />" +
                                        "<input type='hidden' name='explicit_search' value='1' />" +
                                        "<input type='hidden' name='launch_search' value='1' />" +
                                        "<input type='button' class='bouton' value='" + pmbDojo.messages.getMessage("carto", "carto_btn_affiner") + "' id='affinageButton'/>" +
                                        "</form>", dom.byId('map_search'), "after");
                                on(dom.byId('affinageButton'), 'click', lang.hitch(self, self.callbackAffinage));
                            } else {
                                dom.byId('wktAffinage').value = self.formatWKT.extractGeometry(geom);
                            }
                            document.affineRecherche.submit();
                        }
                    }


                    //
                }
            });
            this.map_controls.affinage = control;
            this.map.olMap.addControl(this.map_controls.affinage);
        },
        initSubPanel: function () {
            var callbackBtn = lang.hitch(this, this.pushBtnSubPanel);
            this.map_controls.rectangle = new OpenLayers.Control.Button({displayClass: 'subpanel btnRectangleEnabled olButton', trigger: function (e) {
                    callbackBtn(this);
                }, title: pmbDojo.messages.getMessage("carto", "carto_control_rectangle")});
            this.map_controls.cercle = new OpenLayers.Control.Button({displayClass: 'subpanel btnCercle olButton', trigger: function (e) {
                    callbackBtn(this);
                }, title: pmbDojo.messages.getMessage("carto", "carto_control_circle")});
            this.map_controls.triangle = new OpenLayers.Control.Button({displayClass: 'subpanel btnTriangle olButton', trigger: function (e) {
                    callbackBtn(this);
                }, title: pmbDojo.messages.getMessage("carto", "carto_control_triangle")});
            this.map_controls.choiceregular = new OpenLayers.Control.Button({displayClass: 'subpanel choiceRegular olButton', trigger: function (e) {
                    if (this.active == null || this.active == false) {
                        this.active = true;
                        var controlRegPoly = this.map.getControlsBy('displayClass', "olControlDrawFeatureRegPoly")[0];
                        controlRegPoly.handler.setOptions({irregular: false});
                        this.panel_div.className = this.panel_div.className.replace('choiceRegular', 'choiceRegularEnabled');
                    }
                    else {
                        this.active = false;
                        var controlRegPoly = this.map.getControlsBy('displayClass', "olControlDrawFeatureRegPoly")[0];
                        this.panel_div.className = this.panel_div.className.replace('Enabled', '');
                        controlRegPoly.handler.setOptions({irregular: true});
                    }
                }, title: pmbDojo.messages.getMessage("carto", "carto_control_regular_polygon")});

        },
        addToPanel: function () {
            for (var key in this.map_controls) {
                if (this.panel.controls.indexOf(this.map_controls[key]) == -1) {
                    this.map_controls[key].events.register('activate', this, this.handlerControlsActivated);
                    this.map_controls[key].events.register('deactivate', this, this.handlerControlsDeactivated);
                    this.panel.addControls([this.map_controls[key]]);
                }
            }
        },
        initToggleCluster: function () {
            this.map_controls.toggleCluster = new OpenLayers.Control.Button({
                displayClass: 'toggleCluster',
                trigger: lang.hitch(this, this.swapMode),
                title: pmbDojo.messages.getMessage("carto", "carto_control_clustering")
            });
        },
        editDegMinSec: function (e) {
            var longitude = this.toDecimal(dom.byId('degLonPopup').value, dom.byId('minLonPopup').value, dom.byId('secLonPopup').value);
            var latitude = this.toDecimal(dom.byId('degLatPopup').value, dom.byId('minLatPopup').value, dom.byId('secLatPopup').value);

            dom.byId('inputLon').value = longitude;
            dom.byId('inputLat').value = latitude;
        },
        editLongLat: function (e) {
            var type = e.target.id.split('input')[1];
            var newValue = this.toSexagesimal(e.target.value);
            dom.byId('deg' + type + 'Popup').value = newValue[0];
            dom.byId('min' + type + 'Popup').value = newValue[1];
            dom.byId('sec' + type + 'Popup').value = newValue[2];
        },
        toSexagesimal: function (val) {
            val = parseFloat(val);
            var retour = new Array();
            var valASplit = OpenLayers.Util.getFormattedLonLat(val);
            retour[0] = parseInt(valASplit.split('\xB0')[0]);
            retour[1] = parseInt(valASplit.split('\xB0')[1].split('\'')[0]);
            retour[2] = Math.round(valASplit.split('\xB0')[1].split('\'')[1].split('\"')[0]);
            if (retour[2] == 60) {
                retour[2] = 0;
                retour[1] = retour[1] + 1;
                if (retour[1] == 60) {
                    retour[1] = 0;
                    retour[0] = retour[0] + 1;
                }
            }
            if (valASplit.split('\xB0')[1].split('\'')[1].split('\"')[1] == "S") {
                retour[0] = -retour[0];
            }
            return retour;
        },
        toDecimal: function (deg, min, sec) {
            if (parseInt(deg) < 0) {
                min = -min;
                sec = -sec;
            }
            return (parseInt(deg) + (parseInt(min) / 60) + (parseInt(sec) / 3600)).toFixed(4);
        },
        clickRadioPopup: function (e) {
            if (e.target.id == "radioLonLat") {
                if (dom.byId('degMinSec').style.display != "none") {
                    if (dom.byId('degMinSec') != null)
                        domStyle.set(dom.byId('degMinSec'), "display", "none");
                    if (dom.byId('lonLatPopup') != null)
                        domStyle.set(dom.byId('lonLatPopup'), "display", "block");
                    //Maj values 
                    dom.byId('inputLon').value = this.toDecimal(dom.byId('degLonPopup').value, dom.byId('minLonPopup').value, dom.byId('secLonPopup').value);
                    dom.byId('inputLat').value = this.toDecimal(dom.byId('degLatPopup').value, dom.byId('minLatPopup').value, dom.byId('secLatPopup').value);
                    var editedFeature = this.map.olMap.getLayersByName(dom.byId('layerName').value)[0].getFeatureById(dom.byId('featureName').value);
                    editedFeature.popup.updateSize();
                    e.target.parentNode.style.height = e.target.parentNode.offsetHeight + 10 + "px";
                }
                e.target.checked = "true";
            }
            else {
                if (dom.byId('lonLatPopup').style.display != "none") {
                    if (dom.byId('degMinSec') != null)
                        domStyle.set(dom.byId('degMinSec'), "display", "block");
                    if (dom.byId('lonLatPopup') != null)
                        domStyle.set(dom.byId('lonLatPopup'), "display", "none");
                    var editedFeature = this.map.olMap.getLayersByName(dom.byId('layerName').value)[0].getFeatureById(dom.byId('featureName').value);
                    editedFeature.popup.updateSize();
                    e.target.parentNode.style.height = e.target.parentNode.offsetHeight + 10 + "px";
                }
                e.target.checked = "true";
            }
        },
        switchTypeCoords: function (e) {
            if (e.target.id == "degSex") {
                if (dom.byId('degSex').getAttribute('checked') == null) {
                    dom.byId('degSex').setAttribute('checked', 'true');
                }
                if (dom.byId('degDec').getAttribute('checked') != null) {
                    dom.byId('degDec').removeAttribute('checked');
                }
                if (dom.byId('address')) {
	                if (dom.byId('address').getAttribute('checked') != null) {
	                	dom.byId('address').removeAttribute('checked');
	                }
                }
                if (dom.byId('searchResultList')) {
	                if (dom.byId('searchResultList').style.display == 'block') {
	                	dom.byId('searchResultList').style.display = 'none';
	                }
                }
                if (dom.byId('valideModif')) {
	                if (dom.byId('valideModif').style.display == 'none') {
	                	dom.byId('valideModif').style.display = 'inline-block';
	                }
                }
                for (var i = 0; i < query('div[typechamps="degreeDec"]').length; i++) {
                    query('div[typechamps="degreeDec"]')[i].style.display = 'none';
                }
                for (var i = 0; i < query('div[typechamps="degreeSexa"]').length; i++) {
                    query('div[typechamps="degreeSexa"]')[i].style.display = 'inline';
                }
                for (var i = 0; i < query('div[typechamps="address"]').length; i++) {
                	query('div[typechamps="address"]')[i].style.display = 'none';
                }

            } else if (e.target.id == "address") {
            	dom.byId('valideModif').style.display = 'none';
                if (dom.byId('degSex').getAttribute('checked') != null) {
                    dom.byId('degSex').removeAttribute('checked');
                }
            	if (dom.byId('degDec').getAttribute('checked') != null) {
            		dom.byId('degDec').removeAttribute('checked');
            	}
            	if (dom.byId('address').getAttribute('checked') == null) {
            		dom.byId('address').setAttribute('checked', 'true');
            	}
            	for (var i = 0; i < query('div[typechamps="degreeDec"]').length; i++) {
            		query('div[typechamps="degreeDec"]')[i].style.display = 'none';
            	}
            	for (var i = 0; i < query('div[typechamps="degreeSexa"]').length; i++) {
            		query('div[typechamps="degreeSexa"]')[i].style.display = 'none';
            	}
                for (var i = 0; i < query('div[typechamps="address"]').length; i++) {
                	query('div[typechamps="address"]')[i].style.display = 'inline-block';
                }
            	
            } else {
                if (dom.byId('degDec').getAttribute('checked') == null) {
                    dom.byId('degDec').setAttribute('checked', 'true');
                }
                if (dom.byId('degSex').getAttribute('checked') != null) {
                    dom.byId('degSex').removeAttribute('checked');
                }
                if (dom.byId('address')) {
	                if (dom.byId('address').getAttribute('checked') != null) {
	                	dom.byId('address').removeAttribute('checked');
	                }
                }
                if (dom.byId('searchResultList')) {
	                if (dom.byId('searchResultList').style.display == 'block') {
	                	dom.byId('searchResultList').style.display = 'none';
	                }
                }
                if (dom.byId('valideModif')) {
	                if (dom.byId('valideModif').style.display == 'none') {
	                	dom.byId('valideModif').style.display = 'inline-block';
	                }
                }
                for (var i = 0; i < query('div[typechamps="degreeDec"]').length; i++) {
                    query('div[typechamps="degreeDec"]')[i].style.display = 'inline';
                }
                for (var i = 0; i < query('div[typechamps="degreeSexa"]').length; i++) {
                    query('div[typechamps="degreeSexa"]')[i].style.display = 'none';
                }
                for (var i = 0; i < query('div[typechamps="address"]').length; i++) {
                	query('div[typechamps="address"]')[i].style.display = 'none';
                }
            }
        },
        transformInitialBounds: function (init) {
            for (var i = 0; i < 4; i++) {
                init[i] = parseFloat(init[i]).toFixed(4);
            }
            return init;
        },
        createEditForm: function (mode, shape, feature) {
            var header = "";
            var callbackOnChangeInputLonLat = lang.hitch(this, this.onChangeInputLonLat);
            var callbackOnChangeInputSex = lang.hitch(this, this.onChangeInputSex);
            var callbackSwitch = lang.hitch(this, this.switchTypeCoords);
            var callbackOnEnterValidForm = lang.hitch(this, this.onEnterValidForm);
            var callbackValidModif = lang.hitch(this, this.validateModification);
            var callbackListerAdresse = lang.hitch(this, this.listerAdresses);
            var callbackValidatePt = lang.hitch(this, this.validatePt);
            var callbackClickAddPt = lang.hitch(this, this.addPtForm);
            var lignePt = "";
            if (mode == "edition") {
                arrayPtSketch = [];
                if (shape == "OpenLayers.Geometry.Point") {
                    var clone = this.editedFeature.clone();
                    var ptGoodCoords = clone.geometry.transform(this.projTo, this.projFrom);
                    arrayPtSketch.push(ptGoodCoords);
                }
                else {
                    for (var i = 0; i < feature.layer.features.length; i++) {
                        if (feature.layer.features[i]._index == undefined && feature.layer.features[i]._sketch != undefined && feature.layer.features[i]._sketch == true) {
                            var clone = feature.layer.features[i].clone();
                            var ptGoodCoords = clone.geometry.transform(this.projTo, this.projFrom);
                            arrayPtSketch.push(ptGoodCoords);
                        }
                    }
                }
                var nbPt = arrayPtSketch.length;
                header += "<h2 style='color:red;'>" + pmbDojo.messages.getMessage("carto", "carto_warning_edit_form") + ".</h2>";
                var divAppend = dom.byId('ptsEdit');
                var place = "before";
                feature.layer.events.register('featureover', this, this.featureEditOutOver);
                feature.layer.events.register('featureout', this, this.featureEditOutOver);
            } else {
                var nbPt = 0;
                var arrayPtSketch = [];
                header = "<h2>%s</h2>";
                switch (shape) {
                    case 'OpenLayers.Geometry.Point':
                        nbPt = 1;
                        header = header.replace("%s", pmbDojo.messages.getMessage("carto", "carto_create_form_point"));
                        break;
                    case 'OpenLayers.Geometry.Polygon':
                        nbPt = 4;
                        header = header.replace("%s", pmbDojo.messages.getMessage("carto", "carto_create_form_polygon"));
                        break;
                    case 'OpenLayers.Geometry.LineString':
                        nbPt = 2;
                        header = header.replace("%s", pmbDojo.messages.getMessage("carto", "carto_create_form_path"));
                        break;
                }

                var divAppend = dom.byId('map_manual_edition');
                var place = "after";
            }
            //Create Form
            var buttonSup = "";
            var buttonAdd = "";
            var address = "";
            var city = "";
    		var postcode = "";

            if (mode == "edition") {
            	var ptEdited = dom.byId(this.featureSelected.id);
            	if (ptEdited != null) {
            		jsonDataDisplay = JSON.parse(ptEdited.getAttribute("data-address"));
            	}
            	if (jsonDataDisplay != null) {
            		if (jsonDataDisplay.parking) {
            			address = jsonDataDisplay.parking;
            		} else if (jsonDataDisplay.locality) {
            			address = jsonDataDisplay.locality;
            		} else if (jsonDataDisplay.road) {
        				if (jsonDataDisplay.house_number) {
        					address += jsonDataDisplay.house_number + " ";
        				}
        				address += jsonDataDisplay.road;
        			} else if (jsonDataDisplay.footway) {
        				address = jsonDataDisplay.footway;
        			} else if (jsonDataDisplay.neighbourhood) {
        				address = jsonDataDisplay.neighbourhood;
        			} else if (jsonDataDisplay.suburb) {
        				address = jsonDataDisplay.suburb;
        			}
            		
            		if (jsonDataDisplay.village) {
            			city = jsonDataDisplay.village;
        			} else if (jsonDataDisplay.city) {
        				city = jsonDataDisplay.city;
        			} else if (jsonDataDisplay.hamlet) {
        				city = jsonDataDisplay.hamlet;
        			} else if (jsonDataDisplay.town) {
        				city = jsonDataDisplay.town;
        			} else if (jsonDataDisplay.county) {
        				city = jsonDataDisplay.county;
        			} else if (jsonDataDisplay.state) {
        				city = jsonDataDisplay.state;
        			}
            		
            		if (jsonDataDisplay.postcode) postcode = jsonDataDisplay.postcode;
            	}
            	if ((address == "") && (city == "") && (postcode == "")) {
            		if (ptEdited.getAttribute("data-display_address")) {
            			address = ptEdited.getAttribute("data-display_address");
            		}
            	}
            }
            
            for (var i = 0; i < nbPt; i++) {
                if (arrayPtSketch[i] != undefined) {
                    var lon = (arrayPtSketch[i].x).toFixed(4);
                    var lat = (arrayPtSketch[i].y).toFixed(4);
                    var degSexLon = this.toSexagesimal(lon);
                    var degSexLat = this.toSexagesimal(lat);
                } else {
                    var lon = "";
                    var lat = "";
                    var degSexLon = [];
                    var degSexLat = [];
                    for (var j = 0; j < 3; j++) {
                        degSexLon[j] = "";
                        degSexLat[j] = "";
                    }
                }

                if (shape != "OpenLayers.Geometry.Point") {
                    buttonAdd = "<input type='button' class='bouton' value='+' id='add_" + parseInt(i + 1) + "_" + parseInt(i + 2) + "'/>";
                }

                if (mode == "edition" && feature.geometry.CLASS_NAME != "OpenLayers.Geometry.Point") {
                    buttonSup = "<input type='button' class='bouton' value='x' id='del_" + parseInt(i + 1) + "'/>";
                }
                lignePt += "<div id='pt_" + parseInt(i + 1) + "'>" +
                        "<div typeChamps='degreeSexa' style='display:inline;' id='pt_" + parseInt(i + 1) + "_sexa'>" +
                        "<label>Point " + parseInt(i + 1) + " :</label><br/>" +
                        "<label>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + "</label><input style='width:30px' id='pt_" + parseInt(i + 1) + "_lon_deg' value='" + degSexLon[0] + "'/><label>\xB0</label><input style='width:30px' id='pt_" + parseInt(i + 1) + "_lon_min' value='" + degSexLon[1] + "'/><label>'</label><input style='width:30px' id='pt_" + parseInt(i + 1) + "_lon_sec' value='" + degSexLon[2] + "'/><label>\"</label>" +
                        "&nbsp;&nbsp;" +
                        "<label>" + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + "</label><input style='width:30px' id='pt_" + parseInt(i + 1) + "_lat_deg' value='" + degSexLat[0] + "'/><label>\xB0</label><input style='width:30px' id='pt_" + parseInt(i + 1) + "_lat_min' value='" + degSexLat[1] + "'/><label>'</label><input style='width:30px' id='pt_" + parseInt(i + 1) + "_lat_sec' value='" + degSexLat[2] + "'/><label>\"</label>" +
                        buttonAdd +
                        buttonSup +
                        "</div>" +
                        "<div typeChamps='degreeDec' style='display:none;' id='pt_" + parseInt(i + 1) + "_dec'>" +
                        "<label>Point " + parseInt(i + 1) + " :</label><br/>" +
                        "<label>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + "</label><input id='pt_" + parseInt(i + 1) + "_lon' value='" + lon + "'/><label>" + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + "</label><input id='pt_" + parseInt(i + 1) + "_lat' value='" + lat + "'/>" +
                        buttonAdd +
                        buttonSup +
                        "</div>";
                if (!i && shape != 'OpenLayers.Geometry.LineString') {
                	lignePt +="<div typeChamps='address' style='display:none;' id='pt_" + parseInt(i + 1) + "_address'>" +
                			"<div style='display:inline-block'><label>" + pmbDojo.messages.getMessage("carto", "carto_address") + " : </label>" +
                			"<input id='pt_" + parseInt(i + 1) + "_search_address' value='"+address+"'/></div><br/>" +
                			"<div style='display:inline-block'><label>" + pmbDojo.messages.getMessage("carto", "carto_city") + " : </label>" +
                			"<input id='pt_" + parseInt(i + 1) + "_search_city' value='"+city+"'/></div><br/>" +
                			"<div style='display:inline-block'><label>" + pmbDojo.messages.getMessage("carto", "carto_postcode") + " : </label>" +
                			"<input id='pt_" + parseInt(i + 1) + "_search_postcode' value='"+postcode+"'/></div><br/>" +
                			"<input type='button' class='bouton' value='" + pmbDojo.messages.getMessage("carto", "carto_research_label") + "' id='rechercherAdresse' name='rechercheAdresse'/>"
                			"</div>";
                }
                lignePt += "</div>";
                //TODO button add sur dernier pt ajoute entre le 1er et le dernier
            }
            var html = "<div id='ptsDetails'>" + header +            		
            		pmbDojo.messages.getMessage("carto", "carto_sexagesimal_degrees") + 
            		": <input type='radio' checked='true' name='choixTypeCoords' id='degSex'/>" +
            		"<br/>" +
                    pmbDojo.messages.getMessage("carto", "carto_decimal_degrees") + ": " +
            		"<input type='radio' name='choixTypeCoords' id='degDec'/>" +
            		"<br/>";
    		if (shape != 'OpenLayers.Geometry.LineString') {
    			html += pmbDojo.messages.getMessage("carto", "carto_address") + " " + 
        				": <input type='radio' name='choixTypeCoords' id='address'/>" +
    					"<br/>";
    		}
    		html += "<input type='hidden' value='" + mode + "' id='typeForm'/> " +
            		"<div id='listePt'>" + lignePt + "</div>" +
            		"<div id='searchResultList' style='display:none;'></div>" +
    				"<input type='button' class='bouton' value='" + pmbDojo.messages.getMessage("carto", "carto_validate_label") + "' id='valideModif' name='validModif'/>" +
					"</div>";
            
            domConstruct.place(html, divAppend, place);
            
            if (dom.byId('ptsEdit') != null)
                domStyle.set(dom.byId('ptsEdit'), 'display', 'none');
            
            if (shape != 'OpenLayers.Geometry.LineString') on(dom.byId('pt_1_search_address'), 'keydown', callbackOnEnterValidForm);
            if (shape != 'OpenLayers.Geometry.LineString') on(dom.byId('pt_1_search_city'), 'keydown', callbackOnEnterValidForm);
            if (shape != 'OpenLayers.Geometry.LineString') on(dom.byId('pt_1_search_postcode'), 'keydown', callbackOnEnterValidForm);
            if (shape != 'OpenLayers.Geometry.LineString') on(dom.byId('rechercherAdresse'), 'click', callbackListerAdresse);
            on(dom.byId('valideModif'), 'click', callbackValidModif);


            //Put evt:	
            for (var i = 0; i < nbPt; i++) {
                if (dom.byId("add_" + parseInt(i + 1) + "_" + parseInt(i + 2)) != null) {
                	on(dom.byId("add_" + parseInt(i + 1) + "_" + parseInt(i + 2)), 'click', callbackClickAddPt);
                }
                on(dom.byId("pt_" + parseInt(i + 1) + "_lon"), 'change', callbackOnChangeInputLonLat);
                on(dom.byId("pt_" + parseInt(i + 1) + "_lat"), 'change', callbackOnChangeInputLonLat);
                on(dom.byId("pt_" + parseInt(i + 1) + "_lon_deg"), 'change', callbackOnChangeInputSex);
                on(dom.byId("pt_" + parseInt(i + 1) + "_lat_deg"), 'change', callbackOnChangeInputSex);
                on(dom.byId("pt_" + parseInt(i + 1) + "_lon_min"), 'change', callbackOnChangeInputSex);
                on(dom.byId("pt_" + parseInt(i + 1) + "_lat_min"), 'change', callbackOnChangeInputSex);
                on(dom.byId("pt_" + parseInt(i + 1) + "_lon_sec"), 'change', callbackOnChangeInputSex);
                on(dom.byId("pt_" + parseInt(i + 1) + "_lat_sec"), 'change', callbackOnChangeInputSex);
            }
            //put special evt
            if (mode == "edition") {
                var callbackHoverPtEdit = lang.hitch(this, this.highlightPoint);
                var callbackOutPtEdit = lang.hitch(this, this.downlightPoint);
                var callbackClickDelPt = lang.hitch(this, this.delPtForm);

                for (var i = 0; i < nbPt; i++) {
                    if (dom.byId("del_" + parseInt(i + 1)) != null) {
                        on(dom.byId("del_" + parseInt(i + 1)), 'click', callbackClickDelPt);
                    }
                    dom.byId("pt_" + parseInt(i + 1)).addEventListener('mouseover', callbackHoverPtEdit, true);
                    dom.byId("pt_" + parseInt(i + 1)).addEventListener('mouseout', callbackOutPtEdit, true);

                }
            }
            on(dom.byId('degSex'), 'click', callbackSwitch);
            on(dom.byId('degDec'), 'click', callbackSwitch);

            if (dom.byId('address')) {
            	on(dom.byId('address'), 'click', callbackSwitch);
            }
        },
        /*
         * Function d'ajout de point sur une feature
         */
        addPtForm: function (e) {
            var pt1 = e.target.id.split('_')[1];
            var pt2 = e.target.id.split('_')[2];
            var nbNewPt = query('.newPt').length;
            var callbackOnChangeInputLonLat = lang.hitch(this, this.onChangeInputLonLat);
            var callbackOnChangeInputSex = lang.hitch(this, this.onChangeInputSex);

            var lignePt = "<div class='newPt' id='pt_newPt" + nbNewPt + "_" + pt1 + "'>" +
                    		"<div typeChamps='degreeSexa' id='pt_newPt" + nbNewPt + "_sexa'>" +
                    			"<label>" + pmbDojo.messages.getMessage("carto", "carto_label_new_point") + "</label>" +
            					"<br/>" +
                    			"<label>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + "</label>" +
            					"<input style='width:30px' id='pt_newPt" + nbNewPt + "_lon_deg' value=''/>" +
            					"<label>\xB0</label>" +
            					"<input style='width:30px' id='pt_newPt" + nbNewPt + "_lon_min' value=''/>" +
    							"<label>'</label>" +
    							"<input style='width:30px' id='pt_newPt" + nbNewPt + "_lon_sec' value=''/>" +
								"<label>\"</label>" +
								"&nbsp;&nbsp;" +
								"<label>" + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + "</label>" +
								"<input style='width:30px' id='pt_newPt" + nbNewPt + "_lat_deg' value=''/>" +
								"<label>\xB0</label>" +
								"<input style='width:30px' id='pt_newPt" + nbNewPt + "_lat_min' value=''/>" +
								"<label>'</label>" +
								"<input style='width:30px' id='pt_newPt" + nbNewPt + "_lat_sec' value=''/>" +
								"<label>\"</label>" +
							"</div>" +
							"<div typeChamps='degreeDec' style='display:none;' id='pt_newPt" + nbNewPt + "_dec'>" +
								"<label>" + pmbDojo.messages.getMessage("carto", "carto_label_new_point") + "</label>" +
								"<br/>" +
								"<label>" + pmbDojo.messages.getMessage("carto", "carto_lon_abbr") + "</label>" +
								"<input id='pt_newPt" + nbNewPt + "_lon' value=''/>" +
								"<label>" + pmbDojo.messages.getMessage("carto", "carto_lat_abbr") + "</label>" +
								"<input id='pt_newPt" + nbNewPt + "_lat' value=''/>" +
							"</div>" +
						"</div>";
            domConstruct.place(lignePt, dom.byId('pt_' + pt1), 'after');
            on(dom.byId("pt_newPt" + nbNewPt + "_lon"), 'change', callbackOnChangeInputLonLat);
            on(dom.byId("pt_newPt" + nbNewPt + "_lat"), 'change', callbackOnChangeInputLonLat);
            on(dom.byId("pt_newPt" + nbNewPt + "_lon_deg"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_newPt" + nbNewPt + "_lat_deg"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_newPt" + nbNewPt + "_lon_min"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_newPt" + nbNewPt + "_lat_min"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_newPt" + nbNewPt + "_lon_sec"), 'change', callbackOnChangeInputSex);
            on(dom.byId("pt_newPt" + nbNewPt + "_lat_sec"), 'change', callbackOnChangeInputSex);
        },
        delPtForm: function (e) {
            var numPt = e.target.id.split('_')[1];
            //TODO if sur nbPt suivant la geometry display the alert only if a deletion is possible
            switch (this.editedFeature.geometry.CLASS_NAME) {
                case 'OpenLayers.Geometry.Polygon':
                    if (this.editedFeature.geometry.components[0].components.length > 3) {
                        if (confirm(pmbDojo.messages.getMessage("carto", "carto_warning_point_delete_number").replace('%s', numPt))) {
                            this.editedFeature.geometry.components[0].removePoint(this.editedFeature.geometry.components[0].components[parseInt(numPt - 1)]);
                            this.map_controls.edition.unselectFeature(this.editedFeature);
                            this.map_controls.edition.selectFeature(this.editedFeature);
                            this.createFormFromFeature();
                            this.setHiddenField(this.editedFeature);
                            this.saveCurrentState();
                        }
                    }
                    break;
                case 'OpenLayers.Geometry.LineString':
                    if (this.editedFeature.geometry.components.length > 2) {
                        if (confirm(pmbDojo.messages.getMessage("carto", "carto_warning_point_delete_number").replace('%s', numPt))) {
                            this.editedFeature.geometry.removePoint(this.editedFeature.geometry.components[parseInt(numPt - 1)]);
                            this.map_controls.edition.unselectFeature(this.editedFeature);
                            this.map_controls.edition.selectFeature(this.editedFeature);
                            this.createFormFromFeature();
                            this.setHiddenField(this.editedFeature);
                            this.saveCurrentState();
                        }
                    }
                    break;
            }

        },
        delPtPop: function (featureADel) {
            var ptADel = featureADel;
            var featureGlobale = this.featureSelected;
            //TODO if sur nbPt suivant la geometry display the alert only if a deletion is possible
            switch (this.featureSelected.geometry.CLASS_NAME) {
                case 'OpenLayers.Geometry.Polygon':
                    if (this.featureSelected.geometry.components[0].components.length > 3) {
                        if (confirm(pmbDojo.messages.getMessage("carto", "carto_warning_point_delete"))) {
                            this.featureSelected.geometry.components[0].removePoint(ptADel.geometry);
                            this.setHiddenField(featureGlobale);
                            this.map_controls.edition.unselectFeature(featureGlobale);
                            this.map_controls.edition.selectFeature(featureGlobale);
                            if (this.mode == 'edition' && this.featureSelected.geometry.components[0].components.length <= MAX_POLYGON_POINTS) {
                                this.createFormFromFeature();
                            }
                            this.saveCurrentState();
                        }
                    }
                    break;
                case 'OpenLayers.Geometry.LineString':
                    if (this.featureSelected.geometry.components.length > 2) {
                        if (confirm(pmbDojo.messages.getMessage("carto", "carto_warning_point_delete"))) {
                            this.featureSelected.geometry.removePoint(ptADel.geometry);
                            this.setHiddenField(featureGlobale);
                            this.map_controls.edition.unselectFeature(featureGlobale);
                            this.map_controls.edition.selectFeature(featureGlobale);
                            if (this.mode == 'edition' && this.featureSelected.geometry.components.length <= MAX_POLYGON_POINTS) {
                                this.createFormFromFeature();
                            }
                            this.saveCurrentState();
                        }
                    }
                    break;
            }
        },
        checkPt: function (lat, lon, i, newPt) {
            newPt = typeof newPt !== 'undefined' ? newPt : false;
            if ((lat != lat * 1) || (lon != lon * 1) || (lat == "") || (lon == "")) {
                if (!newPt) {
                    alert(pmbDojo.messages.getMessage("carto", "carto_warning_point_misinformed").replace('%s', i));
                    if (domStyle.get(dom.byId('pt_' + i + '_dec'), 'display') == "none") {
                        query('input', dom.byId('pt_' + i + '_sexa'))[0].focus();
                    }
                    else {
                        query('input', dom.byId('pt_' + i + '_dec'))[0].focus();
                    }
                } else {
                    alert(pmbDojo.messages.getMessage("carto", "carto_warning_new_point_misinformed").replace('%s', i));
                    if (domStyle.get(dom.byId('pt_newPt' + parseInt(i + 1) + '_sexa'), 'display') == "none") {
                        query('input', dom.byId('pt_newPt' + parseInt(i + 1) + '_sexa'))[0].focus();
                    }
                    else {
                        query('input', dom.byId('pt_newPt' + parseInt(i + 1) + '_dec'))[0].focus();
                    }
                }
                return false;
            }
            else {
                return true;
            }
        },
        createFormRegularPoly: function () {
            var callbackShape = lang.hitch(this, this.shapeSelection);
            var callbackCheckRegular = lang.hitch(this, this.regularSelection);
            domConstruct.place("<div id='ptsDetails'>" +
                    "<label>" + pmbDojo.messages.getMessage("carto", "carto_label_new_regular_polygon") + "</label><br/><br/>" +
                    "<label>" + pmbDojo.messages.getMessage("carto", "carto_label_shape_choosing") + ":</label>" +
                    "<select id='shapeType' name='shapeType'>" +
                    "<option value='1'>" + pmbDojo.messages.getMessage("carto", "carto_control_rectangle") + "</option>" +
                    "<option value='2'>" + pmbDojo.messages.getMessage("carto", "carto_control_circle") + "</option>" +
                    "<option value='3'>" + pmbDojo.messages.getMessage("carto", "carto_control_triangle") + "</option>" +
                    "</select><br/>" +
                    "<label for='choixRegular'>" + pmbDojo.messages.getMessage("carto", "carto_label_regular") + ":</label> <input type='checkbox' name='choixRegular' id='checkRegular' value='checkbox' /> " +
                    "</div>", dom.byId('map_manual_edition'), "after");
            on(dom.byId('shapeType'), "change", callbackShape);
            on(dom.byId('checkRegular'), "change", callbackCheckRegular);
        },
        shapeSelection: function (e) {
            switch (dom.byId('shapeType').value) {
                case '1':
                    this.map_controls.regPoly.handler.setOptions({sides: 4});
                    break;
                case '2':
                    this.map_controls.regPoly.handler.setOptions({sides: 45});
                    break;
                case '3':
                    this.map_controls.regPoly.handler.setOptions({sides: 3});
                    break;
            }
        },
        regularSelection: function (e) {
            if (dom.byId('checkRegular').checked)
                this.map_controls.regPoly.handler.setOptions({irregular: false});
            else
                this.map_controls.regPoly.handler.setOptions({irregular: true});
        },
        pushBtnSubPanel: function (btnPushed) {

            for (var key in this.map_controls) {
                if (this.map_controls[key].panel_div != null && this.map_controls[key].panel_div.className.match(/subpanel/g) != null && this.map_controls[key].panel_div.className != btnPushed.panel_div.className) {
                    //Modif class & property active
                    this.map_controls[key].active = false;
                    this.map_controls[key].panel_div.className = this.map_controls[key].panel_div.className.replace('Enabled', '');
                }
            }
            if (btnPushed.active == null || btnPushed.active == false) {
                btnPushed.active = true;
                switch (btnPushed.title) {
                    case pmbDojo.messages.getMessage("carto", "carto_control_circle"):
                        this.map_controls.regPoly.handler.setOptions({sides: 50});
                        btnPushed.panel_div.className = btnPushed.panel_div.className.replace('btnCercle', 'btnCercleEnabled');
                        break;
                    case pmbDojo.messages.getMessage("carto", "carto_control_triangle"):
                        this.map_controls.regPoly.handler.setOptions({sides: 3});
                        btnPushed.panel_div.className = btnPushed.panel_div.className.replace('btnTriangle', 'btnTriangleEnabled');
                        break;
                    case pmbDojo.messages.getMessage("carto", "carto_control_rectangle"):
                        this.map_controls.regPoly.handler.setOptions({sides: 4});
                    	if (btnPushed.panel_div.className.indexOf('Enabled') == -1) {
                    		btnPushed.panel_div.className = btnPushed.panel_div.className.replace('btnRectangle', 'btnRectangleEnabled');
                    	}
                        break;
                }
            }
        },
        destroy: function () {
            var inputsEmprises = query('input[name="map_wkt[]"]');
            if (inputsEmprises.length > 0) {
                for (var i = 0; i < inputsEmprises.length; i++) {
                    domConstruct.destroy(inputsEmprises[i]);
                }
            }
            var arrayChilds = this.domNode.childNodes;
            for (var i = 0; i < arrayChilds.length; i++) {
                arrayChilds[i].parentNode.removeChild(arrayChilds[i]);
            }
            this.inherited(arguments);
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
                //this.dataLayers[i].holds = [];
                for (var j = 0; j < data.length; j++) {
                    var styleEmprise = {
                        strokeWidth: 2,
                        strokeColor: this.dataLayers[i].color,
                        fillOpacity: 0.4,
                        fillColor: this.dataLayers[i].color
                    };
                    var featureI = this.formatWKT.read(data[j].wkt);
                    featureI.style = styleEmprise;
                    if (featureI.geometry.CLASS_NAME == "OpenLayers.Geometry.Point") {
                        featureI.style.pointRadius = 8;
                        featureI.style.label = data[j].objects.record.length.toString();
                        if (data[j].objects.record.length > 20) {
                            featureI.style.pointRadius = 14;
                            if (data[j].objects.record.length > 100) {
                                featureI.style.pointRadius = 20;
                            }
                        }
                    }
                    featureI.records_ids = data[j].objects.record;
                    featureI.attributes.records_length = data[j].objects.record.length;
                    featureI.attributes.class = featureI.geometry.CLASS_NAME;
                    featureI.id = i + "_" + "feature_" + this.map.olMap.id + "_" + j;
                    //this.dataLayers[i].holds.push(data[j]);
                    featureI.geometry.transform(this.projFrom, this.projTo);
                    this.featuresByZoom[zoomLevel][i].push(featureI);

                }
            }
            //Stocker avec le niveau de zoom;
            this.printByZoomLevel(zoomLevel, i);
        },
        printByZoomLevel: function (zoomLevel, i) {
            var currentLayer = this.map.olMap.getLayersByName(this.dataLayers[i].name + "_" + i)[0];
            currentLayer.removeAllFeatures();
            //this.map.olMap.layers[i].destroyFeatures();
            var features = new Array();
            if (this.featuresByZoom[zoomLevel][i]) {
                currentLayer.addFeatures(this.featuresByZoom[zoomLevel][i]);
                this.dataLayers[i].holds = [];
                for (var j = 0; j < this.featuresByZoom[zoomLevel][i].length; j++) {
                    var obj = {};
                    obj.objects = {};
                    obj.objects.record = this.featuresByZoom[zoomLevel][i][j].records_ids;
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
                        var callbackCluster = lang.hitch(this, "callbackCluster", i, event.object.zoom);
                        request.post(this.dataLayers[i].data_url, {
                            'data': "indice=" + i + "&search_id=" + this.searchId + "&wkt_map_hold=" + geom + "&zoom_level=" + event.object.zoom + "&cluster=" + this.cluster,
                            'handleAs': "json",
                            'headers': {
                               'X-Requested-With': ''
                            }
                        }).then(callbackCluster);
                    }
                }
            }
                
                this.map.olMap.updateSize(); 
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
                    'data': "indice=" + i + "&search_id=" + this.searchId + "&wkt_map_hold=" + geom + "&zoom_level=" + this.map.olMap.zoom + "&cluster=" + this.cluster,
                    'handleAs': "json",
                    'headers': {
                       'X-Requested-With': ''
                    }
                }).then(callbackHolds);
            }
        },
        cancelLastEdit: function () {
            //Check size ...
            var lastEdit = this.editionStates[this.editionStates.length - 2];
            var currentState = this.editionStates[this.editionStates.length - 1];
            this.deactivateAllControls();
            if (this.map.olMap.layers[0].getFeatureById(lastEdit['featureId'])) {
                this.deleteHiddenField(this.map.olMap.layers[0].getFeatureById(lastEdit['featureId']));
                this.map.olMap.layers[0].removeFeatures([this.map.olMap.layers[0].getFeatureById(lastEdit['featureId'])], {silent: true});
            }
            var backFeat = lastEdit['clonedFeature'].clone();
            backFeat.id = lastEdit['featureId'];
            this.map.olMap.layers[0].addFeatures([backFeat]);
            this.setHiddenField(backFeat);
            lastEdit['control'].activate();
            this.editedFeature = backFeat;
            if (typeof lastEdit['control'].selectFeature == "function") {
                lastEdit['control'].selectFeature(backFeat);
            }
            this.editionStates.pop();
            if (this.editionStates.length == 1) {
                if (dom.byId('cancelLastEdit') != null) {
                    domConstruct.destroy('cancelLastEdit');
                }
            }
        },
        getCurrentActivatedControl: function () {
            //var controls = this.map.olMap.getControlsByClass('OpenLayers.Control.ModifyFeature');
            for (var key in this.map_controls) {
                if (this.map_controls[key].active) {
                    return this.map_controls[key];
                }
            }
            return false;
        },


        /*
         * Déselectionne tous les contrôles de carte possibles (features, menu..)
         * 
         * @param void
         */
        deactivateAllControls: function () {
            for (var key in this.map_controls) {
                if (typeof this.map_controls[key].unselectAll == "function") {
                    this.map_controls[key].unselectAll();
                }
                if (typeof this.map_controls[key].resetVertices == "function") {
                    this.map_controls[key].resetVertices();
                }
                if (typeof this.map_controls[key].unselectFeature == "function" && this.map_controls[key].feature) {
                    this.map_controls[key].unselectFeature();
                }
                this.map_controls[key].deactivate();
            }
        },


        /*
         * Permet la sauvegarde de l'emplacement actuel d'un point et gère l'affichage des boutons d'annulation des modifications
         * 
         * @param void
         */
        saveCurrentState: function () {
            if (this.mode == "edition") {
                var featureObj = new Array();
                for (var i = 0; i < this.map.olMap.layers[0].features.length; i++) {
                    if (!this.map.olMap.layers[0].features[i]._sketch) {
                        featureObj.push({"featureId": this.map.olMap.layers[0].features[i].id, "feature": this.map.olMap.layers[0].features[i].clone()});
                    }
                }
                var selectedFeature = false;
                if (this.map.olMap.layers[0].selectedFeatures[0]) {
                    selectedFeature = this.map.olMap.layers[0].selectedFeatures[0].id;
                }
                this.editionStates.push({"features": featureObj, "control": this.getCurrentActivatedControl(), "selectedFeature": selectedFeature});
                if (!dom.byId('cancelEdit') && this.editionStates.length > 2) {
                    domConstruct.place("<input type='button' class='bouton' id='cancelEdit' value='" + pmbDojo.messages.getMessage("carto", "carto_label_cancel_modifications") + "'/>", dom.byId('map_manual_edition'), "before");
                    on(dom.byId('cancelEdit'), 'click', lang.hitch(this, this.loadState, true));
                }
                if (!dom.byId('cancelLastEdit') && this.editionStates.length > 1) {
                    domConstruct.place("<input type='button' class='bouton' id='cancelLastEdit' value='" + pmbDojo.messages.getMessage("carto", "carto_label_cancel_last_modification") + "'/>", dom.byId('map_manual_edition'), "before");
                    on(dom.byId('cancelLastEdit'), 'click', lang.hitch(this, this.loadState, false));
                }
            }
        },


        /*
         * Permet de revenir à un emplacement de point précédent
         * 
         * @param first (Boolean) : Si true alors on annule toutes les modifications, si false on annule la dernière modification
         */
        loadState: function (first) {
            var first = first != undefined && typeof first == "boolean" && first != false ? first : false;
            this.deactivateAllControls();
            if (this.editedFeature && this.editedFeature.layer && this.editedFeature.layer.events.listeners.featureover != null) {
                this.editedFeature.layer.events.remove('featureout');
                this.editedFeature.layer.events.remove('featureover');

            }
            this.editedFeature = null;
            this.featureSelected = null;
            this.map.olMap.layers[0].selectedFeatures = new Array();
            if (first) {
                var lastState = this.editionStates[0];
                this.editionStates = this.editionStates.slice(0, 1);
            } else {
                var lastState = this.editionStates[this.editionStates.length - 2];
                this.editionStates.pop();
            }
            for (var i = 0; i < this.map.olMap.layers[0].features.length; i++) {
                this.deleteHiddenField(this.map.olMap.layers[0].features[i]);
            }
            this.map.olMap.layers[0].removeAllFeatures({silent: true});
            for (var i = 0; i < lastState.features.length; i++) {
                var feat = lastState.features[i].feature.clone();
                feat.id = lastState.features[i].featureId;
                this.setHiddenField(feat);
                this.map.olMap.layers[0].addFeatures([feat], {silent: true});
            }
            if (dom.byId('formManuel') != null)
                domConstruct.destroy('formManuel');
            if (dom.byId('ptsDetails') != null)
                domConstruct.destroy('ptsDetails');
            if (lastState.control) {
                lastState.control.activate();
                if (lastState.selectedFeature) {
                    if (typeof lastState.control.selectFeature == "function") {
                        lastState.control.selectFeature(this.map.olMap.layers[0].getFeatureById(lastState.selectedFeature));
                    }
                    if (typeof lastState.control.select == "function") {
                        lastState.control.select(this.map.olMap.layers[0].getFeatureById(lastState.selectedFeature));
                    }
                }
            }
            if (this.editionStates.length == 1) {
                if (dom.byId('cancelLastEdit') != null) {
                    domConstruct.destroy('cancelLastEdit');
                }
            }
            if (this.editionStates.length < 3) {
                if (dom.byId('cancelEdit') != null) {
                    domConstruct.destroy('cancelEdit');
                }
            }

        },


        /*
         * Empeche l'utilisateur de créer un point en dehors du cadre de la map
         * 
         * @param e (Event) : Evenement associé à l'appel de cette fonction
         */
        cancelDraw: function (e) {
            var currentCtrl = this.getCurrentActivatedControl();
            if (currentCtrl && currentCtrl.CLASS_NAME == "OpenLayers.Control.DrawFeature") {
                currentCtrl.cancel();
            }
        },


        /*
         * Effectue une recherche sur nominatim en fonction des champs Adresse et affiche les résultats de la recherche dans une liste
         * 
         * @param query (String) : Contenu de la recherche à envoyer à Nominatim
         * @param deferred (Object) : Instance de la classe Deffered de dojo
         */
        searchByAddress : function(query, deferred, indice) {
        	this.showPatience();
        	var self = this;
        	var uri = "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&polygon_text=1&accept-language=fr-FR";
        	var selectAddress = dom.byId("searchResultList");
        	var addressSearched = encodeURI(dom.byId('pt_1_search_address').value);
        	var citySearched = encodeURI(dom.byId('pt_1_search_city').value);
        	var postcodeSearched = encodeURI(dom.byId('pt_1_search_postcode').value);
        	
        	if (!addressSearched) {
        		if (citySearched) {
        			uri += "&city=" + citySearched;
        		}
        		if (postcodeSearched) {
        			uri += "&postalcode=" + postcodeSearched;
        		}
        	} else {
        		uri += "&q=" + addressSearched;
        		if (citySearched) {
        			uri += " " + citySearched;
        		}
        		if (postcodeSearched) {
        			uri += " " + postcodeSearched;
        		}
        	}
        	if (selectAddress.style.display != "none") {
        		request(uri, {
					handleAs: 'json',
                    headers: {
                       'X-Requested-With': ''
                    }
				}).then(function(data) {
					if (!indice) {
						indice = 0;
					}
					if (!data[indice]) {
						indice = 0;
					}
					deferred.resolve(data[indice]);
					this.temporaryData = data[indice];
					deferred.reject();
					self.hidePatience();
				}, function(err) {
					deferred.reject();
				});
        	}
        },


        /*
         * Effectue une recherche sur nominatim en fonction des champs Adresse et affiche les résultats de la recherche dans une liste
         * 
         * @param query
         */
        listerAdresses: function(query) {
        	let callbackClickAddress = lang.hitch(this, this.highlightAddress);
        	let addressSearched = dom.byId('pt_1_search_address').value;
        	let citySearched = dom.byId('pt_1_search_city').value;
        	let postcodeSearched = dom.byId('pt_1_search_postcode').value;
        	let uri = "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=5&polygon_text=1&accept-language=fr-FR";

        	if (addressSearched != "") {
    			uri += "&q=" + addressSearched + " " + citySearched + " " + postcodeSearched;
        	} else {
        		if (citySearched != "") {
        			uri += "&city=" + citySearched;
        		}
        		if (postcodeSearched != "") {
        			uri += "&postalcode=" + postcodeSearched;
        		}
        	}

        	if (uri) {
        		query = encodeURI(uri);

        		request(query, {
                 	handleAs: 'json',
                 	headers: {
                        'X-Requested-With': ''
                    },
                }).then(function(data) {
					let selectAddress = dom.byId("searchResultList");
					selectAddress.style.display = "block";
					while (selectAddress.hasChildNodes()) {  
						selectAddress.removeChild(selectAddress.firstChild);
					}
					for (let i = 0; i < data.length; i++) {
						let newSearch = document.createElement('div');
						newSearch.setAttribute('id', 'searchResult' + i);
						newSearch.setAttribute('class', 'searchResult');
						selectAddress.appendChild(newSearch);

						let newAddress = document.createElement('span');
						newAddress.setAttribute('name', 'resultAddress');
						newAddress.innerHTML = data[i].display_name;
						newSearch.appendChild(newAddress);

						on(dom.byId('searchResult' + i), 'click', callbackClickAddress);
					}

					if (data.length == 0) {
						selectAddress.style.display = "none";
						if (dom.byId("pt_1").lastChild.nodeName == "H2") {
							dom.byId("pt_1").removeChild(dom.byId("pt_1").lastChild);
						}
						let noResult = document.createElement("h2");
						noResult.innerHTML = pmbDojo.messages.getMessage("carto", "carto_research_no_result");
						dom.byId("pt_1").appendChild(noResult);
					} else {
						if (dom.byId("pt_1").lastChild.nodeName == "H2") {
							dom.byId("pt_1").removeChild(dom.byId("pt_1").lastChild);
						}
					}
				});
        	}
        },


        /*
         * Permet de prévisualiser le point ou le polygone avant placement définitif lors d'une recherche par adresse
         * 
         * @param e (Event) : Evenement associé à l'appel de cette fonction
         */
        highlightAddress: function(e) {
        	if (dom.byId("searchResultList")) {
        		for (var i = 0; i < dom.byId("searchResultList").childElementCount; i++) {
            		if (dom.byId("searchResult" + i).getAttribute("class") ==  "searchResult selected") {
            			dom.byId("searchResult" + i).setAttribute("class", "searchResult");
            		}
            	}
        	}
        	if (e.target.tagName == "SPAN") {
        		selectedAddress = e.target.parentElement;
        	} else {
        		selectedAddress = e.target;
        	}
        	selectedAddress.setAttribute("class", "searchResult selected");

        	var deferred = new Deferred();
        	var address = e.target.firstChild.innerHTML;
        	indice = selectedAddress.id.substring(12);
        	this.searchByAddress(address, deferred, indice);
    		deferred.then(lang.hitch(this, function(data) {
        		if (data) {
        			var feature = this.formatWKT.read(data.geotext);
        			if (this.map_controls.point.active && data.geotext.includes('POLYGON')) {
        				feature.geometry = feature.geometry.getCentroid();
        			}
        				
                    if (feature != undefined) {
                        var newPoly = feature.geometry.transform(this.projFrom, this.projTo);
                        this.map.olMap.getLayersByName(this.dataLayers[0].name + "_0")[0].addFeatures([feature]);
                        if (this.temporaryFeature != null) {
                        	this.map.olMap.getLayersByName(this.dataLayers[0].name + "_0")[0].removeFeatures([this.temporaryFeature]);
                        }
            			this.map.olMap.zoomToExtent(newPoly.bounds);
                        this.map_controls.polygone.deactivate();
                        this.temporaryFeature = feature;
                    }
        		}
    		}));
    		dom.byId("valideModif").style.display = "inline-block";
        },


        /*
         * Supprime la feature temporaire créée pendant la prévisualisation lors de la recherche par adresse
         * 
         * @param void
         */
        removeTemporaryFeature: function() {
        	if (this.temporaryFeature != null) {
        		this.map.olMap.getLayersByName(this.dataLayers[0].name + "_0")[0].removeFeatures([this.temporaryFeature]);
        	}
        },


        /*
         * Récupère une adresse sur Nominatim à partir de coordoonnées x et y, et remplit les champs d'adresse et les champs cachés
         * 
         * @param goodCoords (Array) : Tableau associatif contenant les coordonnées x et y d'un point
         */
        fillAddressDataHiddenField: function(goodCoords) {
        	if (goodCoords['x'] === undefined && goodCoords['y'] === undefined) {
        		return;
        	}
        	var lonlat = new OpenLayers.LonLat(goodCoords['x'], goodCoords['y']);
        	lon = lonlat['lon'];
        	lat = lonlat['lat'];
        	var deferred = new Deferred();
        	request("https://nominatim.openstreetmap.org/reverse?format=json&lat="+lat+"&lon="+lon, {
                headers: {
                    'X-Requested-With': ''
                }
            }).then(function(data) {
				if (data) {
					deferred.resolve(data);
				}
				deferred.reject();
			});
        	deferred.then(lang.hitch(this, function(data) {
        		if (data) {
        			data = JSON.parse(data);
					this.editedFeature['attributes']['data-address'] = data['address'];
					this.editedFeature['attributes']['data-display_address'] = data['display_name'];
					this.setHiddenField(this.editedFeature);
        		}
        	}));
        },
    }); 
 });