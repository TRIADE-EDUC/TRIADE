require({cache:{
'dojox/geo/openlayers/widget/Map':function(){
define([
	"dojo/_base/lang",
	"dojo/_base/declare",
	"dojo/_base/array",
	"dojo/dom-geometry",
	"dojo/query",
	"dijit/_Widget",
	"../_base",
	"../Map",
	"../Layer",
	"../GfxLayer"
], function(lang, declare, array, domgeo, query, Widget, openlayers, Map, Layer, GfxLayer){

	return declare("dojox.geo.openlayers.widget.Map", Widget, {
		// summary:
		//		A widget version of the `dojox.geo.openlayers.Map` component.
		// description:
		//		The `dojox.geo.openlayers.widget.Map` widget is the widget 
		//		version of the `dojox.geo.openlayers.Map` component. 
		//		With this widget, user can specify some attributes in the markup such as
		//		
		//		- `baseLayerType`: The type of the base layer. Permitted values are
		//		- `initialLocation`: The initial location as for the dojox.geo.openlayers.Map.fitTo method
		//		- `touchHandler`: Tells if we attach touch handler or not.
		//
		// example:
		//	| <div id="map" dojoType="dojox.geo.openlayers.widget.Map" baseLayerType="Google" initialLocation="{
		//	|   position: [7.154126, 43.651748],
		//	|   extent: 0.2 }"
		//	| style="background-color: #b5d0d0; width: 100%; height: 100%;">
		//

		// baseLayerType: String
		//		Base layer type as defined in `dojox.geo.openlayer.BaseLayerType`. Can be one of:
		//
		//		- `OSM`
		//		- `WMS`
		//		- `Google`
		//		- `VirtualEarth`
		//		- `Yahoo`
		//		- `ArcGIS`
		baseLayerType: openlayers.BaseLayerType.OSM,

		// initialLocation: String
		//		The part of the map shown at startup time. It is the string description of the location shown at
		//		startup time. Format is the same as for the `dojox.geo.openlayers.widget.Map.fitTo`
		//		method.
		//	|	{
		//	|		bounds: [ulx, uly, lrx, lry]
		//	|	}
		//		The map is fit on the specified bounds expressed as decimal degrees latitude and longitude.
		//		The bounds are defined with their upper left and lower right corners coordinates.
		//
		//	|	{
		//	|		position: [longitude, latitude],
		//	|		extent: degrees
		//	|	}
		//		The map is fit on the specified position showing the extent `<extent>` around
		//		the specified center position.
		initialLocation: null,

		// touchHandler: Boolean
		//		Tells if the touch handler should be attached to the map or not.
		//		Touch handler handles touch events so that the widget can be used
		//		on mobile applications.
		touchHandler: false,

		// map: [readonly] Map
		//		The underlying `dojox/geo/openlayers/Map` object.
		map : null,

		startup: function(){
			// summary:
			//		Processing after the DOM fragment is added to the document
			this.inherited(arguments);
			this.map.initialFit({
				initialLocation: this.initialLocation
			});
		},

		buildRendering: function(){
			// summary:
			//		Construct the UI for this widget, creates the real dojox.geo.openlayers.Map object.		
			// tags:
			//		protected
			this.inherited(arguments);
			var div = this.domNode;
			var map = new Map(div, {
				baseLayerType: this.baseLayerType,
				touchHandler: this.touchHandler
			});
			this.map = map;

			this._makeLayers();
		},

		_makeLayers: function(){
			// summary:
			//		Creates layers defined as markup.
			// tags:
			//		private
			var n = this.domNode;
			var layers = /* ?? query. */query("> .layer", n);
			array.forEach(layers, function(l){
				var type = l.getAttribute("type");
				var name = l.getAttribute("name");
				var cls = "dojox.geo.openlayers." + type;
				var p = lang.getObject(cls);
				if(p){
					var layer = new p(name, {});
					if(layer){
						this.map.addLayer(layer);
					}
				}
			}, this);
		},

		resize : function(b,h){
			// summary:
			//		Resize the widget.
			// description:
			//		Resize the domNode and the widget to the dimensions of a box of the following form:
			//		`{ l: 50, t: 200, w: 300: h: 150 }`
			// b: Object|Number?
			//		If passed, denotes the new size of the widget.
			//		Can be either nothing (widget adapts to the div),
			//		an Object describing a box, or a Number representing the width.
			// h: Number?
			//		The new height. Requires that a width has been specified in the first parameter.

			var olm = this.map.getOLMap();

			var box;
			switch(arguments.length){
				case 0:
					// case 0, do not resize the div, just the surface
				break;
				case 1:
					// argument, override node box
					box = lang.mixin({}, b);
					domgeo.setMarginBox(olm.div, box);
				break;
				case 2:
					// two argument, width, height
					box = {
						w: arguments[0],
						h: arguments[1]
					};
					domgeo.setMarginBox(olm.div, box);
				break;
			}
			olm.updateSize();
		}
	});
});

},
'dojox/geo/openlayers/_base':function(){
define(["dojo/_base/lang"], function(lang){
	
	var openlayers = lang.getObject("dojox.geo.openlayers", true);
	/*===== openlayers = dojox.geo.openlayers; =====*/
	
	openlayers.BaseLayerType = {
		// summary:
		//		Defines the base layer types to be used at Map construction time or
		//		with the setBaseLayerType function.
		// description:
		//		This object defines the base layer types to be used at Map construction
		//		time or with the setBaseLayerType function.

		// OSM: String
		//		The Open Street Map base layer type selector.
		OSM: "OSM",

		// Transport: String
		//		The Open Cycle Map transport layer type selector.
		Transport: "OSM.Transport",

		// WMS: String
		//		The Web Map Server base layer type selector.
		WMS: "WMS",

		// GOOGLE: String
		//		The Google base layer type selector.
		GOOGLE: "Google",

		// VIRTUAL_EARTH: String
		//		The Virtual Earth base layer type selector.
		VIRTUAL_EARTH: "VirtualEarth",

		// BING: String
		//		Same as Virtual Earth
		BING: "VirtualEarth",

		// YAHOO: String
		//		The Yahoo base layer type selector.
		YAHOO: "Yahoo",

		// ARCGIS: String
		//		The ESRI ARCGis base layer selector.
		ARCGIS: "ArcGIS"
	};

	openlayers.EPSG4326 = new OpenLayers.Projection("EPSG:4326");

	var re = /^\s*(\d{1,3})[D°]\s*(\d{1,2})[M']\s*(\d{1,2}\.?\d*)\s*(S|"|'')\s*([NSEWnsew]{0,1})\s*$/i;
	openlayers.parseDMS = function(v, toDecimal){
		// summary:
		//		Parses the specified string and returns degree minute second or decimal degree.
		// description:
		//		Parses the specified string and returns degree minute second or decimal degree.
		// v: String
		//		The string to parse
		// toDecimal: Boolean
		//		Specifies if the result should be returned in decimal degrees or in an array
		//		containing the degrees, minutes, seconds values.
		// returns: Float|Array
		//		the parsed value in decimal degrees or an array containing the degrees, minutes, seconds values.

		var res = re.exec(v);
		if(res == null || res.length < 5){
			return parseFloat(v);
		}
		var d = parseFloat(res[1]);
		var m = parseFloat(res[2]);
		var s = parseFloat(res[3]);
		var nsew = res[5];
		if(toDecimal){
			var lc = nsew.toLowerCase();
			var dd = d + (m + s / 60.0) / 60.0;
			if(lc == "w" || lc == "s"){
				dd = -dd;
			}
			return dd;
		}
		return [d, m, s, nsew];
	};
	
	return openlayers;
});

},
'dojox/geo/openlayers/Map':function(){
define([
	"dojo/_base/kernel",
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/_base/array",
	"dojo/_base/json",
	"dojo/dom",
	"dojo/dom-style",
	"./_base",
	"./TouchInteractionSupport",
	"./Layer",
	"./Patch"
], function(kernel, declare, lang, array, json, dom, style, openlayers, TouchInteractionSupport, Layer, Patch){

	kernel.experimental("dojox.geo.openlayers.Map");


	Patch.patchGFX();

	/*=====
	dojox.geo.openlayers.__MapArgs = {
		// summary:
		//		The keyword arguments that can be passed in a Map constructor.
		// baseLayerType: String
		//		 type of the base layer. Can be any of
		//
		//		- `dojox.geo.openlayers.BaseLayerType.OSM`: Open Street Map base layer
		//		- `dojox.geo.openlayers.BaseLayerType.Transport`: Open Street Map Transport base layer (opencyclemap.org)
		//		- `dojox.geo.openlayers.BaseLayerType.WMS`: Web Map Service layer
		//		- `dojox.geo.openlayers.BaseLayerType.GOOGLE`: Google layer
		//		- `dojox.geo.openlayers.BaseLayerType.VIRTUAL_EARTH`: Virtual Earth layer
		//		- `dojox.geo.openlayers.BaseLayerType.BING`: Bing layer
		//		- `dojox.geo.openlayers.BaseLayerType.YAHOO`: Yahoo layer
		//		- `dojox.geo.openlayers.BaseLayerType.ARCGIS`: ESRI ArgGIS layer
		// baseLayerName: String
		//		The name of the base layer.
		// baseLayerUrl: String
		//		Some layer may need an url such as Web Map Server.
		// baseLayerOptions: String
		//		Additional specific options passed to OpensLayers layer, such as The list of layer to display, for Web Map Server layer.
	};
	=====*/

	return declare("dojox.geo.openlayers.Map", null, {
		// summary:
		//		A map viewer based on the OpenLayers library.
		//
		// description:
		//		The `dojox.geo.openlayers.Map` object allows to view maps from various map providers. 
		//		It encapsulates  an `OpenLayers.Map` object on which most operations are delegated.
		//		GFX layers can be added to display GFX georeferenced shapes as well as Dojo widgets.
		//		Parameters can be passed as argument at construction time to define the base layer
		//		type and the base layer parameters such as url or options depending on the type
		//		specified. These parameters can be any of:
		//
		//		_baseLayerType_: type of the base layer. Can be any of:
		//		
		//		- `dojox.geo.openlayers.BaseLayerType.OSM`: Open Street Map base layer
		//		- `dojox.geo.openlayers.BaseLayerType.Transport`: Open Street Map Transport base layer (opencyclemap.org)
		//		- `dojox.geo.openlayers.BaseLayerType.WMS`: Web Map Service layer
		//		- `dojox.geo.openlayers.BaseLayerType.GOOGLE`: Google layer
		//		- `dojox.geo.openlayers.BaseLayerType.VIRTUAL_EARTH`: Virtual Earth layer
		//		- `dojox.geo.openlayers.BaseLayerType.BING`: Bing layer
		//		- `dojox.geo.openlayers.BaseLayerType.YAHOO`: Yahoo layer
		//		- `dojox.geo.openlayers.BaseLayerType.ARCGIS`: ESRI ArgGIS layer
		//		
		//		Note that access to commercial server such as Google, Virtual Earth or Yahoo may need specific licencing.
		//		
		//		The parameters value also include:
		//		
		//		- `baseLayerName`: The name of the base layer.
		//		- `baseLayerUrl`: Some layer may need an url such as Web Map Server
		//		- `baseLayerOptions`: Additional specific options passed to OpensLayers layer,
		//		  such as The list of layer to display, for Web Map Server layer.
		//
		// example:
		//	|	var map = new dojox.geo.openlayers.widget.Map(div, {
		//	|		baseLayerType: dojox.geo.openlayers.BaseLayerType.OSM,
		//	|		baseLayerName: 'Open Street Map Layer'
		//	|	});

		// olMap: OpenLayers.Map
		//		The underlying OpenLayers.Map object.
		//		Should be accessed on read mode only.
		olMap: null,

		_tp: null,

		constructor: function(div, options){
			// summary:
			//		Constructs a new Map object
			if(!options){
				options = {};
			}

			div = dom.byId(div);

			this._tp = {
				x: 0,
				y: 0
			};

			var opts = options.openLayersMapOptions;

			if(!opts){
				opts = {
					controls: [new OpenLayers.Control.ScaleLine({
						maxWidth: 200
					}), new OpenLayers.Control.Navigation()]
				};
			}
			if(options.accessible){
				var kbd = new OpenLayers.Control.KeyboardDefaults();
				if(!opts.controls){
					opts.controls = [];
				}
				opts.controls.push(kbd);
			}
			var baseLayerType = options.baseLayerType;
			if(!baseLayerType){
				baseLayerType = openlayers.BaseLayerType.OSM;
			}
			var map = new OpenLayers.Map(div, opts);
			this.olMap = map;

			this._layerDictionary = {
				olLayers: [],
				layers: []
			};

			if(options.touchHandler){
				this._touchControl = new TouchInteractionSupport(map);
			}

			var base = this._createBaseLayer(options);
			this.addLayer(base);

			this.initialFit(options);
		},

		initialFit: function(params){
			// summary:
			//		Performs an initial fit to contents.
			// tags:
			//		protected
			var o = params.initialLocation;
			if(!o){
				o = [-160, 70, 160, -70];
			}
			this.fitTo(o);
		},

		setBaseLayerType: function(type){
			// summary:
			//		Set the base layer type, replacing the existing base layer
			// type: dojox/geo/openlayers.BaseLayerType
			//		base layer type
			// returns:
			//		The newly created layer.
			if(type == this.baseLayerType){
				return null; // Layer
			}

			var o = null;
			if(typeof type == "string"){
				o = {
					baseLayerName: type,
					baseLayerType: type
				};
				this.baseLayerType = type;
			}else if(typeof type == "object"){
				o = type;
				this.baseLayerType = o.baseLayerType;
			}
			var bl = null;
			if(o != null){
				bl = this._createBaseLayer(o);
				if(bl != null){
					var olm = this.olMap;
					var ob = olm.getZoom();
					var oc = olm.getCenter();
					var recenter = !!oc && !!olm.baseLayer && !!olm.baseLayer.map;

					if(recenter){
						var proj = olm.getProjectionObject();
						if(proj != null){
							oc = oc.transform(proj, openlayers.EPSG4326);
						}
					}
					var old = olm.baseLayer;
					if(old != null){
						var l = this._getLayer(old);
						this.removeLayer(l);
					}
					if(bl != null){
						this.addLayer(bl);
					}
					if(recenter){
						proj = olm.getProjectionObject();
						if(proj != null){
							oc = oc.transform(openlayers.EPSG4326, proj);
						}
						olm.setCenter(oc, ob);
					}
				}
			}
			return bl;
		},

		getBaseLayerType: function(){
			// summary:
			//		Returns the base layer type.
			// returns:
			//		The current base layer type.
			return this.baseLayerType; // openlayers.BaseLayerType
		},

		getScale: function(geodesic){
			// summary:
			//		Returns the current scale
			// geodesic: Boolean
			//		Tell if geodesic calculation should be performed. If set to
			//		true, the scale will be calculated based on the horizontal size of the
			//		pixel in the center of the map viewport.
			var scale = null;
			var om = this.olMap;
			if(geodesic){
				var units = om.getUnits();
				if(!units){
					return null;	// Number
				}
				var inches = OpenLayers.INCHES_PER_UNIT;
				scale = (om.getGeodesicPixelSize().w || 0.000001) * inches["km"] * OpenLayers.DOTS_PER_INCH;
			}else{
				scale = om.getScale();
			}
			return scale;	// Number
		},

		getOLMap: function(){
			// summary:
			//		gets the underlying OpenLayers map object.
			// returns:
			//		The underlying OpenLayers map object.
			return this.olMap;	// OpenLayers.Map
		},

		_createBaseLayer: function(params){
			// summary:
			//		Creates the base layer.
			// tags:
			//		private
			var base = null;
			var type = params.baseLayerType;
			var url = params.baseLayerUrl;
			var name = params.baseLayerName;
			var options = params.baseLayerOptions;

			if(!name){
				name = type;
			}
			if(!options){
				options = {};
			}
			switch(type){
				case openlayers.BaseLayerType.OSM:
					options.transitionEffect = "resize";
					//				base = new OpenLayers.Layer.OSM(name, url, options);
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.OSM(name, url, options)
					});
				break;
				case openlayers.BaseLayerType.Transport:
					options.transitionEffect = "resize";
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.OSM.TransportMap(name, url, options)
					});
				break;
				case openlayers.BaseLayerType.WMS:
					if(!url){
						url = "http://labs.metacarta.com/wms/vmap0";
						if(!options.layers){
							options.layers = "basic";
						}
					}
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.WMS(name, url, options, {
							transitionEffect: "resize"
						})
					});
				break;
				case openlayers.BaseLayerType.GOOGLE:
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.Google(name, options)
					});
				break;
				case openlayers.BaseLayerType.VIRTUAL_EARTH:
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.VirtualEarth(name, options)
					});
				break;
				case openlayers.BaseLayerType.YAHOO:
					//				base = new OpenLayers.Layer.Yahoo(name);
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.Yahoo(name, options)
					});
				break;
				case openlayers.BaseLayerType.ARCGIS:
					if(!url){
						url = "http://server.arcgisonline.com/ArcGIS/rest/services/ESRI_StreetMap_World_2D/MapServer/export";
					}
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.ArcGIS93Rest(name, url, options, {})
					});

				break;
			}

			if(base == null){
				if(type instanceof OpenLayers.Layer){
					base = type;
				}else{
					options.transitionEffect = "resize";
					base = new Layer(name, {
						olLayer: new OpenLayers.Layer.OSM(name, url, options)
					});
					this.baseLayerType = openlayers.BaseLayerType.OSM;
				}
			}

			return base;
		},

		removeLayer: function(layer){
			// summary:
			//		Remove the specified layer from the map.
			// layer: Layer
			//		The layer to remove from the map.
			var om = this.olMap;
			var i = array.indexOf(this._layerDictionary.layers, layer);
			if(i > 0){
				this._layerDictionary.layers.splice(i, 1);
			}
			var oll = layer.olLayer;
			var j = array.indexOf(this._layerDictionary.olLayers, oll);
			if(j > 0){
				this._layerDictionary.olLayers.splice(i, j);
			}
			om.removeLayer(oll, false);
		},

		layerIndex: function(layer, index){
			// summary:
			//		Set or retrieve the layer index.
			// description:
			//		Set or get the layer index, that is the z-order of the layer.
			//		if the index parameter is provided, the layer index is set to
			//		this value. If the index parameter is not provided, the index of 
			//		the layer is returned.
			// layer: Layer
			//		the layer to retrieve the index.
			// index: int?
			//		index of the layer
			// returns:
			//		the index of the layer.
			var olm = this.olMap;
			if(!index){
				return olm.getLayerIndex(layer.olLayer);
			}
			//olm.raiseLayer(layer.olLayer, index);
			olm.setLayerIndex(layer.olLayer, index);

			this._layerDictionary.layers.sort(function(l1, l2){
				return olm.getLayerIndex(l1.olLayer) - olm.getLayerIndex(l2.olLayer);
			});
			this._layerDictionary.olLayers.sort(function(l1, l2){
				return olm.getLayerIndex(l1) - olm.getLayerIndex(l2);
			});

			return index; // Number
		},

		addLayer: function(layer){
			// summary:
			//		Add the specified layer to the map.
			// layer: Layer
			//		The layer to add to the map.
			layer.dojoMap = this;
			var om = this.olMap;
			var ol = layer.olLayer;
			this._layerDictionary.olLayers.push(ol);
			this._layerDictionary.layers.push(layer);
			om.addLayer(ol);
			layer.added();
		},

		_getLayer: function(/*OpenLayer.Layer */ol){
			// summary:
			//		Retrieve the dojox.geo.openlayer.Layer from the OpenLayer.Layer
			// tags:
			//		private
			var i = array.indexOf(this._layerDictionary.olLayers, ol);
			if(i != -1){
				return this._layerDictionary.layers[i];
			}
			return null;
		},

		getLayer: function(property, value){
			// summary:
			//		Returns the layer whose property matches the value.
			// property: String
			//		The property to check
			// value: Object
			//		The value to match
			// returns:
			//		The layer(s) matching the property's value. Since multiple layers
			//		match the property's value the return value is an array. 
			// example:
			//		var layers = map.getLayer("name", "Layer Name");
			var om = this.olMap;
			var ols = om.getBy("layers", property, value);
			var ret = new Array(); //[];
			array.forEach(ols, function(ol){
				ret.push(this._getLayer(ol));
			}, this);
			return ret; // Layer[]
		},

		getLayerCount: function(){
			// summary:
			//		Returns the count of layers of this map.
			// returns:
			//		The number of layers of this map. 
			var om = this.olMap;
			if(om.layers == null){
				return 0;
			}
			return om.layers.length; // Number
		},

		fitTo: function(o){
			// summary:
			//		Fits the map on a point,or an area
			// description:
			//		Fits the map on the point or extent specified as parameter. 
			// o: Object
			//		Object with key values fit parameters or a JSON string.
			// example:
			//		Examples of arguments passed to the fitTo function:
			//	|	null
			//		The map is fit on full extent
			//
			//	|	{
			//	|		bounds: [ulx, uly, lrx, lry]
			//	|	}
			//		The map is fit on the specified bounds expressed as decimal degrees latitude and longitude.
			//		The bounds are defined with their upper left and lower right corners coordinates.
			// 
			//	|	{
			//	|		position: [longitude, latitude],
			//	|		extent: degrees
			//	|	}
			//		The map is fit on the specified position showing the extent `<extent>` around
			//		the specified center position.

			var map = this.olMap;
			var from = openlayers.EPSG4326;

			if(o == null){
				var c = this.transformXY(0, 0, from);
				map.setCenter(new OpenLayers.LonLat(c.x, c.y));
				return;
			}
			var b = null;
			if(typeof o == "string"){
				var j = json.fromJson(o);
			}else{
				j = o;
			}
			var ul;
			var lr;
			if(j.hasOwnProperty("bounds")){
				var a = j.bounds;
				b = new OpenLayers.Bounds();
				ul = this.transformXY(a[0], a[1], from);
				b.left = ul.x;
				b.top = ul.y;
				lr = this.transformXY(a[2], a[3], from);
				b.right = lr.x;
				b.bottom = lr.y;
			}
			if(b == null){
				if(j.hasOwnProperty("position")){
					var p = j.position;
					var e = j.hasOwnProperty("extent") ? j.extent : 1;
					if(typeof e == "string"){
						e = parseFloat(e);
					}
					b = new OpenLayers.Bounds();
					ul = this.transformXY(p[0] - e, p[1] + e, from);
					b.left = ul.x;
					b.top = ul.y;
					lr = this.transformXY(p[0] + e, p[1] - e, from);
					b.right = lr.x;
					b.bottom = lr.y;
				}
			}
			if(b == null){
				if(o.length == 4){
					b = new OpenLayers.Bounds();
					// TODO Choose the correct method
					if(false){
						b.left = o[0];
						b.top = o[1];

						b.right = o[2];
						b.bottom = o[3];
					}else{
						ul = this.transformXY(o[0], o[1], from);
						b.left = ul.x;
						b.top = ul.y;
						lr = this.transformXY(o[2], o[3], from);
						b.right = lr.x;
						b.bottom = lr.y;
					}
				}
			}
			if(b != null){
				map.zoomToExtent(b, true);
			}
		},

		transform: function(p, from, to){
			// summary:
			//		Transforms the point passed as argument, expressed in the <em>from</em> 
			//		coordinate system to the map coordinate system.
			// description:
			//		Transforms the point passed as argument without modifying it. The point is supposed to be expressed
			//		in the <em>from</em> coordinate system and is transformed to the map coordinate system.
			// p: Object {x, y}
			//		The point to transform
			// from: OpenLayers.Projection
			//		The projection in which the point is expressed.
			return this.transformXY(p.x, p.y, from, to);
		},

		transformXY: function(x, y, from, to){
			// summary:
			//		Transforms the coordinates passed as argument, expressed in the <em>from</em> 
			//		coordinate system to the map coordinate system.
			// description:
			//		Transforms the coordinates passed as argument. The coordinate are supposed to be expressed
			//		in the <em>from</em> coordinate system and are transformed to the map coordinate system.
			// x: Number
			//		The longitude coordinate to transform.
			// y: Number
			//		The latitude coordinate to transform.
			// from: OpenLayers.Projection?
			//		The projection in which the point is expressed, or EPSG4326 is not specified.
			// to: OpenLayers.Projection?
			//		The projection in which the point is converted to. In not specifed, the map projection is used.
			// returns:
			//		The transformed coordinate as an {x,y} Object.

			var tp = this._tp;
			tp.x = x;
			tp.y = y;
			if(!from){
				from = openlayers.EPSG4326;
			}
			if(!to){
				to = this.olMap.getProjectionObject();
			}
			tp = OpenLayers.Projection.transform(tp, from, to);
			return tp; // Object
		}

	});

});

},
'dojox/geo/openlayers/TouchInteractionSupport':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/connect",
	"dojo/_base/html",
	"dojo/_base/lang",
	"dojo/_base/event",
	"dojo/_base/window"
], function(declare, connect, html, lang, event, win){

	return declare("dojox.geo.openlayers.TouchInteractionSupport", null, {
		// summary:
		//		class to handle touch interactions on a OpenLayers.Map widget
		// tags:
		//		private

		_map: null,
		_centerTouchLocation: null,
		_touchMoveListener: null,
		_touchEndListener: null,
		_initialFingerSpacing: null,
		_initialScale: null,
		_tapCount: null,
		_tapThreshold: null,
		_lastTap: null,

		constructor: function(map){
			// summary:
			//		Constructs a new TouchInteractionSupport instance
			// map: OpenLayers.Map
			//		the Map widget this class provides touch navigation for.
			this._map = map;
			this._centerTouchLocation = new OpenLayers.LonLat(0, 0);

			var div = this._map.div;

			// install touch listeners
			connect.connect(div, "touchstart", this, this._touchStartHandler);
			connect.connect(div, "touchmove", this, this._touchMoveHandler);
			connect.connect(div, "touchend", this, this._touchEndHandler);

			this._tapCount = 0;
			this._lastTap = {
				x: 0,
				y: 0
			};
			this._tapThreshold = 100; // square distance in pixels

		},

		_getTouchBarycenter: function(touchEvent){
			// summary:
			//		returns the midpoint of the two first fingers (or the first finger location if only one)
			// touchEvent: TouchEvent
			//		a touch event
			// returns:
			//		the midpoint as an {x,y} object.
			// tags:
			//		private
			var touches = touchEvent.touches;
			var firstTouch = touches[0];
			var secondTouch = null;
			if(touches.length > 1){
				secondTouch = touches[1];
			}else{
				secondTouch = touches[0];
			}

			var marginBox = html.marginBox(this._map.div);

			var middleX = (firstTouch.pageX + secondTouch.pageX) / 2.0 - marginBox.l;
			var middleY = (firstTouch.pageY + secondTouch.pageY) / 2.0 - marginBox.t;

			return {
				x: middleX,
				y: middleY
			}; // Object

		},

		_getFingerSpacing: function(touchEvent){
			// summary:
			//		computes the distance between the first two fingers
			// touchEvent: Event
			//		a touch event
			// returns: float
			//		a distance. -1 if less that 2 fingers
			// tags:
			//		private
			var touches = touchEvent.touches;
			var spacing = -1;
			if(touches.length >= 2){
				var dx = (touches[1].pageX - touches[0].pageX);
				var dy = (touches[1].pageY - touches[0].pageY);
				spacing = Math.sqrt(dx * dx + dy * dy);
			}
			return spacing;
		},

		_isDoubleTap: function(touchEvent){
			// summary:
			//		checks whether the specified touchStart event is a double tap 
			//		(i.e. follows closely a previous touchStart at approximately the same location)
			// touchEvent: TouchEvent
			//		a touch event
			// returns: boolean
			//		true if this event is considered a double tap
			// tags:
			//		private
			var isDoubleTap = false;
			var touches = touchEvent.touches;
			if((this._tapCount > 0) && touches.length == 1){
				// test distance from last tap
				var dx = (touches[0].pageX - this._lastTap.x);
				var dy = (touches[0].pageY - this._lastTap.y);
				var distance = dx * dx + dy * dy;
				if(distance < this._tapThreshold){
					isDoubleTap = true;
				}else{
					this._tapCount = 0;
				}
			}
			this._tapCount++;
			this._lastTap.x = touches[0].pageX;
			this._lastTap.y = touches[0].pageY;
			setTimeout(lang.hitch(this, function(){
				this._tapCount = 0;
			}), 300);

			return isDoubleTap;
		},

		_doubleTapHandler: function(touchEvent){
			// summary:
			//		action performed on the map when a double tap was triggered 
			// touchEvent: TouchEvent
			//		a touch event
			// tags:
			//		private

			// perform a basic 2x zoom on touch
			var touches = touchEvent.touches;
			var marginBox = html.marginBox(this._map.div);
			var offX = touches[0].pageX - marginBox.l;
			var offY = touches[0].pageY - marginBox.t;
			// clicked map point before zooming
			var mapPoint = this._map.getLonLatFromPixel(new OpenLayers.Pixel(offX, offY));
			// zoom increment power
			this._map.setCenter(new OpenLayers.LonLat(mapPoint.lon, mapPoint.lat), this._map.getZoom() + 1);
		},

		_touchStartHandler: function(touchEvent){
			// summary:
			//		action performed on the map when a touch start was triggered 
			// touchEvent: Event
			//		a touch event
			// tags:
			//		private
			event.stop(touchEvent);

			// test double tap
			if(this._isDoubleTap(touchEvent)){
				this._doubleTapHandler(touchEvent);
				return;
			}

			// compute map midpoint between fingers		
			var middlePoint = this._getTouchBarycenter(touchEvent);

			this._centerTouchLocation = this._map.getLonLatFromPixel(new OpenLayers.Pixel(middlePoint.x, middlePoint.y));

			// store initial finger spacing to compute zoom later
			this._initialFingerSpacing = this._getFingerSpacing(touchEvent);

			// store initial map scale
			this._initialScale = this._map.getScale();

			// install touch move and up listeners (if not done by other fingers before)
			if(!this._touchMoveListener){
				this._touchMoveListener = connect.connect(win.global, "touchmove", this, this._touchMoveHandler);
			}
			if(!this._touchEndListener){
				this._touchEndListener = connect.connect(win.global, "touchend", this, this._touchEndHandler);
			}
		},

		_touchEndHandler: function(touchEvent){
			// summary:
			//		action performed on the map when a touch end was triggered 
			// touchEvent: Event
			//		a touch event
			// tags:
			//		private
			event.stop(touchEvent);

			var touches = touchEvent.touches;

			if(touches.length == 0){
				// disconnect listeners only when all fingers are up
				if(this._touchMoveListener){
					connect.disconnect(this._touchMoveListener);
					this._touchMoveListener = null;
				}
				if(this._touchEndListener){
					connect.disconnect(this._touchEndListener);
					this._touchEndListener = null;
				}
			}else{
				// recompute touch center
				var middlePoint = this._getTouchBarycenter(touchEvent);

				this._centerTouchLocation = this._map.getLonLatFromPixel(new OpenLayers.Pixel(middlePoint.x, middlePoint.y));
			}
		},

		_touchMoveHandler: function(touchEvent){
			// summary:
			//		action performed on the map when a touch move was triggered 
			// touchEvent: Event
			//		a touch event
			// tags:
			//		private

			// prevent browser interaction
			event.stop(touchEvent);

			var middlePoint = this._getTouchBarycenter(touchEvent);

			// compute map offset
			var mapPoint = this._map.getLonLatFromPixel(new OpenLayers.Pixel(middlePoint.x, middlePoint.y));
			var mapOffsetLon = mapPoint.lon - this._centerTouchLocation.lon;
			var mapOffsetLat = mapPoint.lat - this._centerTouchLocation.lat;

			// compute scale factor
			var scaleFactor = 1;
			var touches = touchEvent.touches;
			if(touches.length >= 2){
				var fingerSpacing = this._getFingerSpacing(touchEvent);
				scaleFactor = fingerSpacing / this._initialFingerSpacing;
				// weird openlayer bug: setting several times the same scale value lead to visual zoom...
				this._map.zoomToScale(this._initialScale / scaleFactor);
			}

			// adjust map center on barycenter
			var currentMapCenter = this._map.getCenter();
			this._map.setCenter(new OpenLayers.LonLat(currentMapCenter.lon - mapOffsetLon, currentMapCenter.lat
																																											- mapOffsetLat));

		}
	});
});

},
'dojox/geo/openlayers/Layer':function(){
define([
	"dojo/_base/declare", 
	"dojo/_base/lang", 
	"dojo/_base/array", 
	"dojo/_base/sniff",
	"./Feature"
], function(declare, lang, array, sniff, Feature){

		return declare("dojox.geo.openlayers.Layer", null, {
			// summary:
			//		Base layer class for dojox.geo.openlayers.Map specific layers extending OpenLayers.Layer class.
			//		This layer class accepts Features which encapsulates graphic objects to be added to the map.
			//		This layer class encapsulates an OpenLayers.Layer.
			//		This class provides Feature management such as add, remove and feature access.
			constructor: function(name, options){
				// summary:
				//		Constructs a new Layer.
				// name: String
				//		The name of the layer.
				// options: Object
				//		Options passed to the underlying OpenLayers.Layer object.

				var ol = options ? options.olLayer : null;

				if(!ol){
					ol = lang.delegate(new OpenLayers.Layer(name, options));
				}

				this.olLayer = ol;
				this._features = null;
				this.olLayer.events.register("moveend", this, lang.hitch(this, this.moveTo));
			},

			renderFeature: function(/* Feature */f){
				// summary:
				//		Called when rendering a feature is necessary.
				// f: Feature
				//		The feature to draw.
				f.render();
			},

			getDojoMap: function(){
				return this.dojoMap;
			},

			addFeature: function(f){
				// summary:
				//		Add a feature or an array of features to the layer.
				// f: Feature|Feature[]
				//		The Feature or array of features to add.
				if(lang.isArray(f)){
					array.forEach(f, function(item){
						this.addFeature(item);
					}, this);
					return;
				}
				if(this._features == null){
					this._features = [];
				}
				this._features.push(f);
				f._setLayer(this);
			},

			removeFeature: function(f){
				// summary:
				//		Removes a feature or an array of features from the layer.
				// f: Feature|Feature[]
				//		The Feature or array of features to remove.
				var ft = this._features;
				if(ft == null){
					return;
				}
				if(f instanceof Array){
					f = f.slice(0);
					array.forEach(f, function(item){
						this.removeFeature(item);
					}, this);
					return;
				}
				var i = array.indexOf(ft, f);
				if(i != -1){
					ft.splice(i, 1);
				}
				f._setLayer(null);
				f.remove();
			},

			removeFeatureAt: function(index){
				// summary:
				//		Remove the feature at the specified index.
				// index: int
				//		The index of the feature to remove.
				var ft = this._features;
				var f = ft[index];
				if(!f){
					return;
				}
				ft.splice(index, 1);
				f._setLayer(null);
				f.remove();
			},

			getFeatures: function(){
				// summary:
				//		Returns the feature hold by this layer.
				// returns:
				//		The untouched array of features hold by this layer.
				return this._features; // Feature[]
			},

			getFeatureAt: function(i){
				// summary:
				//		Returns the i-th feature of this layer.
				// i: Number
				//		The index of the feature to return.
				// returns:
				//		The i-th feature of this layer.
				if(this._features == null){
					return undefined;
				}
				return this._features[i]; // Feature
			},

			getFeatureCount: function(){
				// summary:
				//		Returns the number of the features contained by this layer.
				// returns:
				//		The number of the features contained by this layer.
				if(this._features == null){
					return 0;
				}
				return this._features.length; // Number
			},

			clear: function(){
				// summary:
				//		Removes all the features from this layer.
				var fa = this.getFeatures();
				this.removeFeature(fa);
			},

			moveTo: function(event){
				// summary:
				//		Called when the layer is panned or zoomed.
				// event: MouseEvent
				//		The event
				if(event.zoomChanged){
					if(this._features == null){
						return;
					}
					array.forEach(this._features, function(f){
						this.renderFeature(f);
					}, this);
				}
			},

			redraw: function(){
				// summary:
				//		Redraws this layer
				if(sniff.isIE){
					setTimeout(lang.hitch(this, function(){
						this.olLayer.redraw();
					}, 0));
				}else{
					this.olLayer.redraw();
				}
			},

			added: function(){
				// summary:
				//		Called when the layer is added to the map
			}

		});
	});

},
'dojox/geo/openlayers/Feature':function(){
define([
	"dojo/_base/kernel",
	"dojo/_base/declare",
	"./_base"
], function(dojo, declare, openlayers){

	return declare("dojox.geo.openlayers.Feature", null, {
		// summary:
		//		A Feature encapsulates an item so that it can be added to a Layer.
		//		This class is not attended to be used as it, but serve as a base class
		//		for specific features such as GeometryFeature which can display georeferenced 
		//		geometries and WidgetFeature which can display georeferenced widgets. 
		constructor: function(){
			// summary:
			//		Construct a new Feature
			this._layer = null;
			this._coordSys = openlayers.EPSG4326;
		},

		getCoordinateSystem: function(){
			// summary:
			//		Returns the coordinate system in which coordinates of this feature are expressed.
			// returns:
			//		The coordinate system in which coordinates of this feature are expressed.
			return this._coordSys; // OpenLayers.Projection
		},

		setCoordinateSystem: function(/* OpenLayers.Projection */cs){
			// summary:
			//		Set the coordinate system in which coordinates of this feature are expressed.
			// cs: OpenLayers.Projection
			//		The coordinate system in which coordinates of this feature are expressed.
			this._coordSys = cs;
		},

		getLayer: function(){
			// summary:
			//		Returns the Layer to which this feature belongs.
			// returns:
			//		The layer to which this feature belongs.
			return this._layer; // dojox/geo/openlayers/Layer
		},

		_setLayer: function(/* dojox/geo/openlayers/Layer */l){
			// summary:
			//		Sets the layer to which this Feature belongs
			// description:
			//		Called when the feature is added to the Layer.
			// tags:
			//		private
			this._layer = l;
		},

		render: function(){
		// summary:
		//		subclasses implements drawing specific behavior.
		},

		remove: function(){
		// summary:
		//		Subclasses implements specific behavior.
		//		Called when removed from the layer.
		},

		_getLocalXY: function(p){
			// summary:
			//		From projected coordinates to screen coordinates
			// p: Object
			//		Object with x and y fields
			// tags:
			//		private
			var x = p.x;
			var y = p.y;
			var layer = this.getLayer();
			var resolution = layer.olLayer.map.getResolution();
			var extent = layer.olLayer.getExtent();
			var rx = (x / resolution + (-extent.left / resolution));
			var ry = ((extent.top / resolution) - y / resolution);
			return [rx, ry];
		}
	});
});

},
'dojox/geo/openlayers/Patch':function(){
define([
	"dojo/_base/kernel",
	"dojo/_base/lang",	// dojo.extend getObject
	"dojo/_base/sniff",	// dojo.isIE
	"dojox/gfx",
	"dojox/gfx/shape"
], function(dojo, lang, sniff, gfx, shape){

	var dgo = lang.getObject("geo.openlayers", true, dojox);

	dgo.Patch = {

		patchMethod: function(/*Object*/type, /*String*/method, /*Function*/execBefore, /*Function*/
		execAfter){
			// summary:
			//		Patches the specified method of the given type so that the 'execBefore' (resp. 'execAfter') function is 
			//		called before (resp. after) invoking the legacy implementation.
			// description:
			//		The execBefore function is invoked with the following parameter:
			//		execBefore(method, arguments) where 'method' is the patched method name and 'arguments' the arguments received
			//		by the legacy implementation.
			//		The execAfter function is invoked with the following parameter:
			//		execBefore(method, returnValue, arguments) where 'method' is the patched method name, 'returnValue' the value
			//		returned by the legacy implementation and 'arguments' the arguments received by the legacy implementation.
			// type: Object
			//		the type to patch.
			// method: String
			//		the method name.
			// execBefore: Function
			//		the function to execute before the legacy implementation.
			// execAfter: Function
			//		the function to execute after the legacy implementation.
			// tags:
			//		private
			var old = type.prototype[method];
			type.prototype[method] = function(){
				var callee = method;
				if(execBefore){
					execBefore.call(this, callee, arguments);
				}
				var ret = old.apply(this, arguments);
				if(execAfter){
					ret = execAfter.call(this, callee, ret, arguments) || ret;
				}
				return ret;
			};
		},

		patchGFX: function(){

			var vmlFixRawNodePath = function(){
				if(!this.rawNode.path){
					this.rawNode.path = {};
				}
			};

			var vmlFixFillColors = function(){
				if(this.rawNode.fill && !this.rawNode.fill.colors){
					this.rawNode.fill.colors = {};
				}
			};
			
			if(sniff.isIE <= 8 && gfx.renderer === "vml"){
				this.patchMethod(gfx.Line, "setShape", vmlFixRawNodePath, null);
				this.patchMethod(gfx.Polyline, "setShape", vmlFixRawNodePath, null);
				this.patchMethod(gfx.Path, "setShape", vmlFixRawNodePath, null);
				
				this.patchMethod(shape.Shape, "setFill", vmlFixFillColors, null);
			}
		}
	};
	return dgo.Patch;
});

},
'dojox/gfx':function(){
define(["dojo/_base/lang", "./gfx/_base", "./gfx/renderer!"], 
  function(lang, gfxBase, renderer){
	// module:
	//		dojox/gfx
	// summary:
	//		This the root of the Dojo Graphics package
	gfxBase.switchTo(renderer);
	return gfxBase;
});

},
'dojox/gfx/_base':function(){
define(["dojo/_base/kernel", "dojo/_base/lang", "dojo/_base/Color", "dojo/_base/sniff", "dojo/_base/window",
	    "dojo/_base/array","dojo/dom", "dojo/dom-construct","dojo/dom-geometry"],
function(kernel, lang, Color, has, win, arr, dom, domConstruct, domGeom){
	// module:
	//		dojox/gfx
	// summary:
	//		This module contains common core Graphics API used by different graphics renderers.

	var g = lang.getObject("dojox.gfx", true),
		b = g._base = {};
	
	// candidates for dojox.style (work on VML and SVG nodes)
	g._hasClass = function(/*DomNode*/node, /*String*/classStr){
		// summary:
		//		Returns whether or not the specified classes are a portion of the
		//		class list currently applied to the node.
		
		// return (new RegExp('(^|\\s+)'+classStr+'(\\s+|$)')).test(node.className)	// Boolean
		var cls = node.getAttribute("className");
		return cls && (" " + cls + " ").indexOf(" " + classStr + " ") >= 0;  // Boolean
	};
	g._addClass = function(/*DomNode*/node, /*String*/classStr){
		// summary:
		//		Adds the specified classes to the end of the class list on the
		//		passed node.
		var cls = node.getAttribute("className") || "";
		if(!cls || (" " + cls + " ").indexOf(" " + classStr + " ") < 0){
			node.setAttribute("className", cls + (cls ? " " : "") + classStr);
		}
	};
	g._removeClass = function(/*DomNode*/node, /*String*/classStr){
		// summary:
		//		Removes classes from node.
		var cls = node.getAttribute("className");
		if(cls){
			node.setAttribute(
				"className",
				cls.replace(new RegExp('(^|\\s+)' + classStr + '(\\s+|$)'), "$1$2")
			);
		}
	};

	// candidate for dojox.html.metrics (dynamic font resize handler is not implemented here)

	//		derived from Morris John's emResized measurer
	b._getFontMeasurements = function(){
		// summary:
		//		Returns an object that has pixel equivilents of standard font
		//		size values.
		var heights = {
			'1em': 0, '1ex': 0, '100%': 0, '12pt': 0, '16px': 0, 'xx-small': 0,
			'x-small': 0, 'small': 0, 'medium': 0, 'large': 0, 'x-large': 0,
			'xx-large': 0
		};
		var p, oldStyle;	
		if(has("ie")){
			//	We do a font-size fix if and only if one isn't applied already.
			// NOTE: If someone set the fontSize on the HTML Element, this will kill it.
			oldStyle = win.doc.documentElement.style.fontSize || "";
			if(!oldStyle){
				win.doc.documentElement.style.fontSize="100%";
			}
		}

		//		set up the measuring node.
		var div = domConstruct.create("div", {style: {
				position: "absolute",
				left: "0",
				top: "-100px",
				width: "30px",
				height: "1000em",
				borderWidth: "0",
				margin: "0",
				padding: "0",
				outline: "none",
				lineHeight: "1",
				overflow: "hidden"
			}}, win.body());

		//		do the measurements.
		for(p in heights){
			div.style.fontSize = p;
			heights[p] = Math.round(div.offsetHeight * 12/16) * 16/12 / 1000;
		}

		if(has("ie")){
			// Restore the font to its old style.
			win.doc.documentElement.style.fontSize = oldStyle;
		}
		win.body().removeChild(div);
		return heights; //object
	};

	var fontMeasurements = null;

	b._getCachedFontMeasurements = function(recalculate){
		if(recalculate || !fontMeasurements){
			fontMeasurements = b._getFontMeasurements();
		}
		return fontMeasurements;
	};

	// candidate for dojox.html.metrics

	var measuringNode = null, empty = {};
	b._getTextBox = function(	/*String*/ text,
								/*Object*/ style,
								/*String?*/ className){
		var m, s, al = arguments.length;
		var i, box;
		if(!measuringNode){
			measuringNode = domConstruct.create("div", {style: {
				position: "absolute",
				top: "-10000px",
				left: "0",
				visibility: "hidden"
			}}, win.body());
		}
		m = measuringNode;
		// reset styles
		m.className = "";
		s = m.style;
		s.borderWidth = "0";
		s.margin = "0";
		s.padding = "0";
		s.outline = "0";
		// set new style
		if(al > 1 && style){
			for(i in style){
				if(i in empty){ continue; }
				s[i] = style[i];
			}
		}
		// set classes
		if(al > 2 && className){
			m.className = className;
		}
		// take a measure
		m.innerHTML = text;

		if(m.getBoundingClientRect){
			var bcr = m.getBoundingClientRect();
			box = {l: bcr.left, t: bcr.top, w: bcr.width || (bcr.right - bcr.left), h: bcr.height || (bcr.bottom - bcr.top)};
		}else{
			box = domGeom.getMarginBox(m);
		}
		m.innerHTML = "";
		return box;
	};

	b._computeTextLocation = function(/*g.defaultTextShape*/textShape, /*Number*/width, /*Number*/height, /*Boolean*/fixHeight) {
		var loc = {}, align = textShape.align;
		switch (align) {
			case 'end':
				loc.x = textShape.x - width;
				break;
			case 'middle':
				loc.x = textShape.x - width / 2;
				break;
			default:
				loc.x = textShape.x;
				break;
		}
		var c = fixHeight ? 0.75 : 1;
		loc.y = textShape.y - height*c; // **rough** approximation of the ascent...
		return loc;
	};
	b._computeTextBoundingBox = function(/*shape.Text*/s){
		// summary:
		//		Compute the bbox of the given shape.Text instance. Note that this method returns an
		//		approximation of the bbox, and should be used when the underlying renderer cannot provide precise metrics.
		if(!g._base._isRendered(s)){
			return {x:0, y:0, width:0, height:0};
		}
		var loc, textShape = s.getShape(),
			font = s.getFont() || g.defaultFont,
			w = s.getTextWidth(),
			h = g.normalizedLength(font.size);
		loc = b._computeTextLocation(textShape, w, h, true);
		return {
			x: loc.x,
			y: loc.y,
			width: w,
			height: h
		};
	};
	b._isRendered = function(/*Shape*/s){
		var p = s.parent;
		while(p && p.getParent){
			p = p.parent;
		}
		return p !== null;
	};

	// candidate for dojo.dom

	var uniqueId = 0;
	b._getUniqueId = function(){
		// summary:
		//		returns a unique string for use with any DOM element
		var id;
		do{
			id = kernel._scopeName + "xUnique" + (++uniqueId);
		}while(dom.byId(id));
		return id;
	};

	// IE10

	var touchActionProp = has("pointer-events") ? "touchAction" : has("MSPointer") ? "msTouchAction" : null;
	b._fixMsTouchAction = touchActionProp ? function(/*dojox/gfx/shape.Surface*/surface){
		surface.rawNode.style[touchActionProp] = "none";
	} : function() {};

	/*=====
	g.Stroke = {
		// summary:
		//		A stroke defines stylistic properties that are used when drawing a path.

		// color: String
		//		The color of the stroke, default value 'black'.
		color: "black",

		// style: String
		//		The style of the stroke, one of 'solid', ... . Default value 'solid'.
		style: "solid",

		// width: Number
		//		The width of a stroke, default value 1.
		width: 1,

		// cap: String
		//		The endcap style of the path. One of 'butt', 'round', ... . Default value 'butt'.
		cap: "butt",

		// join: Number
		//		The join style to use when combining path segments. Default value 4.
		join: 4
	};
	
	g.Fill = {
		// summary:
		//		Defines how to fill a shape. Four types of fills can be used: solid, linear gradient, radial gradient and pattern.
		//		See dojox/gfx.LinearGradient, dojox/gfx.RadialGradient and dojox/gfx.Pattern respectively for more information about the properties supported by each type.
		
		// type: String?
		//		The type of fill. One of 'linear', 'radial', 'pattern' or undefined. If not specified, a solid fill is assumed.
		type:"",
		
		// color: String|dojo/Color?
		//		The color of a solid fill type.
		color:null,
		
	};
	
	g.LinearGradient = {
		// summary:
		//		An object defining the default stylistic properties used for Linear Gradient fills.
		//		Linear gradients are drawn along a virtual line, which results in appearance of a rotated pattern in a given direction/orientation.

		// type: String
		//		Specifies this object is a Linear Gradient, value 'linear'
		type: "linear",

		// x1: Number
		//		The X coordinate of the start of the virtual line along which the gradient is drawn, default value 0.
		x1: 0,

		// y1: Number
		//		The Y coordinate of the start of the virtual line along which the gradient is drawn, default value 0.
		y1: 0,

		// x2: Number
		//		The X coordinate of the end of the virtual line along which the gradient is drawn, default value 100.
		x2: 100,

		// y2: Number
		//		The Y coordinate of the end of the virtual line along which the gradient is drawn, default value 100.
		y2: 100,

		// colors: Array
		//		An array of colors at given offsets (from the start of the line).  The start of the line is
		//		defined at offest 0 with the end of the line at offset 1.
		//		Default value, [{ offset: 0, color: 'black'},{offset: 1, color: 'white'}], is a gradient from black to white.
		colors: []
	};
	
	g.RadialGradient = {
		// summary:
		//		Specifies the properties for RadialGradients using in fills patterns.

		// type: String
		//		Specifies this is a RadialGradient, value 'radial'
		type: "radial",

		// cx: Number
		//		The X coordinate of the center of the radial gradient, default value 0.
		cx: 0,

		// cy: Number
		//		The Y coordinate of the center of the radial gradient, default value 0.
		cy: 0,

		// r: Number
		//		The radius to the end of the radial gradient, default value 100.
		r: 100,

		// colors: Array
		//		An array of colors at given offsets (from the center of the radial gradient).
		//		The center is defined at offest 0 with the outer edge of the gradient at offset 1.
		//		Default value, [{ offset: 0, color: 'black'},{offset: 1, color: 'white'}], is a gradient from black to white.
		colors: []
	};
	
	g.Pattern = {
		// summary:
		//		An object specifying the default properties for a Pattern using in fill operations.

		// type: String
		//		Specifies this object is a Pattern, value 'pattern'.
		type: "pattern",

		// x: Number
		//		The X coordinate of the position of the pattern, default value is 0.
		x: 0,

		// y: Number
		//		The Y coordinate of the position of the pattern, default value is 0.
		y: 0,

		// width: Number
		//		The width of the pattern image, default value is 0.
		width: 0,

		// height: Number
		//		The height of the pattern image, default value is 0.
		height: 0,

		// src: String
		//		A url specifying the image to use for the pattern.
		src: ""
	};

	g.Text = {
		//	summary:
		//		A keyword argument object defining both the text to be rendered in a VectorText shape,
		//		and specifying position, alignment, and fitting.
		//	text: String
		//		The text to be rendered.
		//	x: Number?
		//		The left coordinate for the text's bounding box.
		//	y: Number?
		//		The top coordinate for the text's bounding box.
		//	width: Number?
		//		The width of the text's bounding box.
		//	height: Number?
		//		The height of the text's bounding box.
		//	align: String?
		//		The alignment of the text, as defined in SVG. Can be "start", "end" or "middle".
		//	fitting: Number?
		//		How the text is to be fitted to the bounding box. Can be 0 (no fitting), 1 (fitting based on
		//		passed width of the bounding box and the size of the font), or 2 (fit text to the bounding box,
		//		and ignore any size parameters).
		//	leading: Number?
		//		The leading to be used between lines in the text.
		//	decoration: String?
		//		Any text decoration to be used.
	};

	g.Font = {
		// summary:
		//		An object specifying the properties for a Font used in text operations.
	
		// type: String
		//		Specifies this object is a Font, value 'font'.
		type: "font",
	
		// style: String
		//		The font style, one of 'normal', 'bold', default value 'normal'.
		style: "normal",
	
		// variant: String
		//		The font variant, one of 'normal', ... , default value 'normal'.
		variant: "normal",
	
		// weight: String
		//		The font weight, one of 'normal', ..., default value 'normal'.
		weight: "normal",
	
		// size: String
		//		The font size (including units), default value '10pt'.
		size: "10pt",
	
		// family: String
		//		The font family, one of 'serif', 'sanserif', ..., default value 'serif'.
		family: "serif"
	};

	=====*/

	lang.mixin(g, {
		// summary:
		//		defines constants, prototypes, and utility functions for the core Graphics API

		// default shapes, which are used to fill in missing parameters
		defaultPath: {
			// summary:
			//		Defines the default Path prototype object.

			// type: String
			//		Specifies this object is a Path, default value 'path'.
			type: "path", 

			// path: String
			//		The path commands. See W32C SVG 1.0 specification.
			//		Defaults to empty string value.
			path: ""
		},
		defaultPolyline: {
			// summary:
			//		Defines the default PolyLine prototype.

			// type: String
			//		Specifies this object is a PolyLine, default value 'polyline'.
			type: "polyline",

			// points: Array
			//		An array of point objects [{x:0,y:0},...] defining the default polyline's line segments. Value is an empty array [].
			points: []
		},
		defaultRect: {
			// summary:
			//		Defines the default Rect prototype.

			// type: String
			//		Specifies this default object is a type of Rect. Value is 'rect'
			type: "rect",

			// x: Number
			//		The X coordinate of the default rectangles position, value 0.
			x: 0,

			// y: Number
			//		The Y coordinate of the default rectangle's position, value 0.
			y: 0,

			// width: Number
			//		The width of the default rectangle, value 100.
			width: 100,

			// height: Number
			//		The height of the default rectangle, value 100.
			height: 100,

			// r: Number
			//		The corner radius for the default rectangle, value 0.
			r: 0
		},
		defaultEllipse: {
			// summary:
			//		Defines the default Ellipse prototype.

			// type: String
			//		Specifies that this object is a type of Ellipse, value is 'ellipse'
			type: "ellipse",

			// cx: Number
			//		The X coordinate of the center of the ellipse, default value 0.
			cx: 0,

			// cy: Number
			//		The Y coordinate of the center of the ellipse, default value 0.
			cy: 0,

			// rx: Number
			//		The radius of the ellipse in the X direction, default value 200.
			rx: 200,

			// ry: Number
			//		The radius of the ellipse in the Y direction, default value 200.
			ry: 100
		},
		defaultCircle: {
			// summary:
			//		An object defining the default Circle prototype.

			// type: String
			//		Specifies this object is a circle, value 'circle'
			type: "circle",

			// cx: Number
			//		The X coordinate of the center of the circle, default value 0.
			cx: 0,
			// cy: Number
			//		The Y coordinate of the center of the circle, default value 0.
			cy: 0,

			// r: Number
			//		The radius, default value 100.
			r: 100
		},
		defaultLine: {
			// summary:
			//		An object defining the default Line prototype.

			// type: String
			//		Specifies this is a Line, value 'line'
			type: "line",

			// x1: Number
			//		The X coordinate of the start of the line, default value 0.
			x1: 0,

			// y1: Number
			//		The Y coordinate of the start of the line, default value 0.
			y1: 0,

			// x2: Number
			//		The X coordinate of the end of the line, default value 100.
			x2: 100,

			// y2: Number
			//		The Y coordinate of the end of the line, default value 100.
			y2: 100
		},
		defaultImage: {
			// summary:
			//		Defines the default Image prototype.

			// type: String
			//		Specifies this object is an image, value 'image'.
			type: "image",

			// x: Number
			//		The X coordinate of the image's position, default value 0.
			x: 0,

			// y: Number
			//		The Y coordinate of the image's position, default value 0.
			y: 0,

			// width: Number
			//		The width of the image, default value 0.
			width: 0,

			// height: Number
			//		The height of the image, default value 0.
			height: 0,

			// src: String
			//		The src url of the image, defaults to empty string.
			src: ""
		},
		defaultText: {
			// summary:
			//		Defines the default Text prototype.

			// type: String
			//		Specifies this is a Text shape, value 'text'.
			type: "text",

			// x: Number
			//		The X coordinate of the text position, default value 0.
			x: 0,

			// y: Number
			//		The Y coordinate of the text position, default value 0.
			y: 0,

			// text: String
			//		The text to be displayed, default value empty string.
			text: "",

			// align:	String
			//		The horizontal text alignment, one of 'start', 'end', 'center'. Default value 'start'.
			align: "start",

			// decoration: String
			//		The text decoration , one of 'none', ... . Default value 'none'.
			decoration: "none",

			// rotated: Boolean
			//		Whether the text is rotated, boolean default value false.
			rotated: false,

			// kerning: Boolean
			//		Whether kerning is used on the text, boolean default value true.
			kerning: true
		},
		defaultTextPath: {
			// summary:
			//		Defines the default TextPath prototype.

			// type: String
			//		Specifies this is a TextPath, value 'textpath'.
			type: "textpath",

			// text: String
			//		The text to be displayed, default value empty string.
			text: "",

			// align: String
			//		The horizontal text alignment, one of 'start', 'end', 'center'. Default value 'start'.
			align: "start",

			// decoration: String
			//		The text decoration , one of 'none', ... . Default value 'none'.
			decoration: "none",

			// rotated: Boolean
			//		Whether the text is rotated, boolean default value false.
			rotated: false,

			// kerning: Boolean
			//		Whether kerning is used on the text, boolean default value true.
			kerning: true
		},

		// default stylistic attributes
		defaultStroke: {
			// summary:
			//		A stroke defines stylistic properties that are used when drawing a path.
			//		This object defines the default Stroke prototype.
			// type: String
			//		Specifies this object is a type of Stroke, value 'stroke'.
			type: "stroke",

			// color: String
			//		The color of the stroke, default value 'black'.
			color: "black",

			// style: String
			//		The style of the stroke, one of 'solid', ... . Default value 'solid'.
			style: "solid",

			// width: Number
			//		The width of a stroke, default value 1.
			width: 1,

			// cap: String
			//		The endcap style of the path. One of 'butt', 'round', ... . Default value 'butt'.
			cap: "butt",

			// join: Number
			//		The join style to use when combining path segments. Default value 4.
			join: 4
		},
		defaultLinearGradient: {
			// summary:
			//		An object defining the default stylistic properties used for Linear Gradient fills.
			//		Linear gradients are drawn along a virtual line, which results in appearance of a rotated pattern in a given direction/orientation.

			// type: String
			//		Specifies this object is a Linear Gradient, value 'linear'
			type: "linear",

			// x1: Number
			//		The X coordinate of the start of the virtual line along which the gradient is drawn, default value 0.
			x1: 0,

			// y1: Number
			//		The Y coordinate of the start of the virtual line along which the gradient is drawn, default value 0.
			y1: 0,

			// x2: Number
			//		The X coordinate of the end of the virtual line along which the gradient is drawn, default value 100.
			x2: 100,

			// y2: Number
			//		The Y coordinate of the end of the virtual line along which the gradient is drawn, default value 100.
			y2: 100,

			// colors: Array
			//		An array of colors at given offsets (from the start of the line).  The start of the line is
			//		defined at offest 0 with the end of the line at offset 1.
			//		Default value, [{ offset: 0, color: 'black'},{offset: 1, color: 'white'}], is a gradient from black to white.
			colors: [
				{ offset: 0, color: "black" }, { offset: 1, color: "white" }
			]
		},
		defaultRadialGradient: {
			// summary:
			//		An object specifying the default properties for RadialGradients using in fills patterns.

			// type: String
			//		Specifies this is a RadialGradient, value 'radial'
			type: "radial",

			// cx: Number
			//		The X coordinate of the center of the radial gradient, default value 0.
			cx: 0,

			// cy: Number
			//		The Y coordinate of the center of the radial gradient, default value 0.
			cy: 0,

			// r: Number
			//		The radius to the end of the radial gradient, default value 100.
			r: 100,

			// colors: Array
			//		An array of colors at given offsets (from the center of the radial gradient).
			//		The center is defined at offest 0 with the outer edge of the gradient at offset 1.
			//		Default value, [{ offset: 0, color: 'black'},{offset: 1, color: 'white'}], is a gradient from black to white.
			colors: [
				{ offset: 0, color: "black" }, { offset: 1, color: "white" }
			]
		},
		defaultPattern: {
			// summary:
			//		An object specifying the default properties for a Pattern using in fill operations.

			// type: String
			//		Specifies this object is a Pattern, value 'pattern'.
			type: "pattern",

			// x: Number
			//		The X coordinate of the position of the pattern, default value is 0.
			x: 0,

			// y: Number
			//		The Y coordinate of the position of the pattern, default value is 0.
			y: 0,

			// width: Number
			//		The width of the pattern image, default value is 0.
			width: 0,

			// height: Number
			//		The height of the pattern image, default value is 0.
			height: 0,

			// src: String
			//		A url specifying the image to use for the pattern.
			src: ""
		},
		defaultFont: {
			// summary:
			//		An object specifying the default properties for a Font used in text operations.

			// type: String
			//		Specifies this object is a Font, value 'font'.
			type: "font",

			// style: String
			//		The font style, one of 'normal', 'bold', default value 'normal'.
			style: "normal",

			// variant: String
			//		The font variant, one of 'normal', ... , default value 'normal'.
			variant: "normal",

			// weight: String
			//		The font weight, one of 'normal', ..., default value 'normal'.
			weight: "normal",

			// size: String
			//		The font size (including units), default value '10pt'.
			size: "10pt",

			// family: String
			//		The font family, one of 'serif', 'sanserif', ..., default value 'serif'.
			family: "serif"
		},

		getDefault: (function(){
			// summary:
			//		Returns a function used to access default memoized prototype objects (see them defined above).
			var typeCtorCache = {};
			// a memoized delegate()
			return function(/*String*/ type){
				var t = typeCtorCache[type];
				if(t){
					return new t();
				}
				t = typeCtorCache[type] = new Function();
				t.prototype = g[ "default" + type ];
				return new t();
			}
		})(),

		normalizeColor: function(/*dojo/Color|Array|string|Object*/ color){
			// summary:
			//		converts any legal color representation to normalized
			//		dojo/Color object
			// color:
			//		A color representation.
			return (color instanceof Color) ? color : new Color(color); // dojo/Color
		},
		normalizeParameters: function(existed, update){
			// summary:
			//		updates an existing object with properties from an 'update'
			//		object
			// existed: Object
			//		the target object to be updated
			// update: Object
			//		the 'update' object, whose properties will be used to update
			//		the existed object
			var x;
			if(update){
				var empty = {};
				for(x in existed){
					if(x in update && !(x in empty)){
						existed[x] = update[x];
					}
				}
			}
			return existed;	// Object
		},
		makeParameters: function(defaults, update){
			// summary:
			//		copies the original object, and all copied properties from the
			//		'update' object
			// defaults: Object
			//		the object to be cloned before updating
			// update: Object
			//		the object, which properties are to be cloned during updating
			// returns: Object
			//      new object with new and default properties
			var i = null;
			if(!update){
				// return dojo.clone(defaults);
				return lang.delegate(defaults);
			}
			var result = {};
			for(i in defaults){
				if(!(i in result)){
					result[i] = lang.clone((i in update) ? update[i] : defaults[i]);
				}
			}
			return result; // Object
		},
		formatNumber: function(x, addSpace){
			// summary:
			//		converts a number to a string using a fixed notation
			// x: Number
			//		number to be converted
			// addSpace: Boolean
			//		whether to add a space before a positive number
			// returns: String
			//      the formatted value
			var val = x.toString();
			if(val.indexOf("e") >= 0){
				val = x.toFixed(4);
			}else{
				var point = val.indexOf(".");
				if(point >= 0 && val.length - point > 5){
					val = x.toFixed(4);
				}
			}
			if(x < 0){
				return val; // String
			}
			return addSpace ? " " + val : val; // String
		},
		// font operations
		makeFontString: function(font){
			// summary:
			//		converts a font object to a CSS font string
			// font: Object
			//		font object (see dojox/gfx.defaultFont)
			return font.style + " " + font.variant + " " + font.weight + " " + font.size + " " + font.family; // Object
		},
		splitFontString: function(str){
			// summary:
			//		converts a CSS font string to a font object
			// description:
			//		Converts a CSS font string to a gfx font object. The CSS font
			//		string components should follow the W3C specified order
			//		(see http://www.w3.org/TR/CSS2/fonts.html#font-shorthand):
			//		style, variant, weight, size, optional line height (will be
			//		ignored), and family. Note that the Font.size attribute is limited to numeric CSS length.
			// str: String
			//		a CSS font string.
			// returns: Object
			//      object in dojox/gfx.defaultFont format
			var font = g.getDefault("Font");
			var t = str.split(/\s+/);
			do{
				if(t.length < 5){ break; }
				font.style   = t[0];
				font.variant = t[1];
				font.weight  = t[2];
				var i = t[3].indexOf("/");
				font.size = i < 0 ? t[3] : t[3].substring(0, i);
				var j = 4;
				if(i < 0){
					if(t[4] == "/"){
						j = 6;
					}else if(t[4].charAt(0) == "/"){
						j = 5;
					}
				}
				if(j < t.length){
					font.family = t.slice(j).join(" ");
				}
			}while(false);
			return font;	// Object
		},
		// length operations

		// cm_in_pt: Number
		//		points per centimeter (constant)
		cm_in_pt: 72 / 2.54,

		// mm_in_pt: Number
		//		points per millimeter (constant)
		mm_in_pt: 7.2 / 2.54,

		px_in_pt: function(){
			// summary:
			//		returns the current number of pixels per point.
			return g._base._getCachedFontMeasurements()["12pt"] / 12;	// Number
		},

		pt2px: function(len){
			// summary:
			//		converts points to pixels
			// len: Number
			//		a value in points
			return len * g.px_in_pt();	// Number
		},

		px2pt: function(len){
			// summary:
			//		converts pixels to points
			// len: Number
			//		a value in pixels
			return len / g.px_in_pt();	// Number
		},

		normalizedLength: function(len) {
			// summary:
			//		converts any length value to pixels
			// len: String
			//		a length, e.g., '12pc'
			// returns: Number
			//      pixels
			if(len.length === 0){ return 0; }
			if(len.length > 2){
				var px_in_pt = g.px_in_pt();
				var val = parseFloat(len);
				switch(len.slice(-2)){
					case "px": return val;
					case "pt": return val * px_in_pt;
					case "in": return val * 72 * px_in_pt;
					case "pc": return val * 12 * px_in_pt;
					case "mm": return val * g.mm_in_pt * px_in_pt;
					case "cm": return val * g.cm_in_pt * px_in_pt;
				}
			}
			return parseFloat(len);	// Number
		},

		// pathVmlRegExp: RegExp
		//		a constant regular expression used to split a SVG/VML path into primitive components
		// tags:
		//		private
		pathVmlRegExp: /([A-Za-z]+)|(\d+(\.\d+)?)|(\.\d+)|(-\d+(\.\d+)?)|(-\.\d+)/g,

		// pathVmlRegExp: RegExp
		//		a constant regular expression used to split a SVG/VML path into primitive components
		// tags:
		//		private
		pathSvgRegExp: /([A-DF-Za-df-z])|([-+]?\d*[.]?\d+(?:[eE][-+]?\d+)?)/g,

		equalSources: function(a, b){
			// summary:
			//		compares event sources, returns true if they are equal
			// a: Object
			//		first event source
			// b: Object
			//		event source to compare against a
			// returns: Boolean
			//      true, if objects are truthy and the same
			return a && b && a === b;
		},

		switchTo: function(/*String|Object*/ renderer){
			// summary:
			//		switch the graphics implementation to the specified renderer.
			// renderer:
			//		Either the string name of a renderer (eg. 'canvas', 'svg, ...) or the renderer
			//		object to switch to.
			var ns = typeof renderer == "string" ? g[renderer] : renderer;
			if(ns){
				// If more options are added, update the docblock at the end of shape.js!
				arr.forEach(["Group", "Rect", "Ellipse", "Circle", "Line",
						"Polyline", "Image", "Text", "Path", "TextPath",
						"Surface", "createSurface", "fixTarget"], function(name){
					g[name] = ns[name];
				});
				if(typeof renderer == "string"){
					g.renderer = renderer;
				}else{
					arr.some(["svg","vml","canvas","canvasWithEvents","silverlight"], function(r){
						return (g.renderer = g[r] && g[r].Surface === g.Surface ? r : null);
					});
				}
			}
		}
	});
	
	/*=====
		g.createSurface = function(parentNode, width, height){
			// summary:
			//		creates a surface
			// parentNode: Node
			//		a parent node
			// width: String|Number
			//		width of surface, e.g., "100px" or 100
			// height: String|Number
			//		height of surface, e.g., "100px" or 100
			// returns: dojox/gfx.Surface
			//     newly created surface
		};
		g.fixTarget = function(){
			// tags:
			//		private
		};
	=====*/
	
	return g; // defaults object api
});

},
'dojox/gfx/renderer':function(){
define(["./_base","dojo/_base/lang", "dojo/_base/sniff", "dojo/_base/window", "dojo/_base/config"],
  function(g, lang, has, win, config){
  //>> noBuildResolver
	var currentRenderer = null;

	has.add("vml", function(global, document, element){
		element.innerHTML = "<v:shape adj=\"1\"/>";
		var supported = ("adj" in element.firstChild);
		element.innerHTML = "";
		return supported;
	});

	return {
		// summary:
		//		This module is an AMD loader plugin that loads the appropriate graphics renderer
		//		implementation based on detected environment and current configuration settings.
		
		load: function(id, require, load){
			// tags:
			//      private
			if(currentRenderer && id != "force"){
				load(currentRenderer);
				return;
			}
			var renderer = config.forceGfxRenderer,
				renderers = !renderer && (lang.isString(config.gfxRenderer) ?
					config.gfxRenderer : "svg,vml,canvas,silverlight").split(","),
				silverlightObject, silverlightFlag;

			while(!renderer && renderers.length){
				switch(renderers.shift()){
					case "svg":
						// the next test is from https://github.com/phiggins42/has.js
						if("SVGAngle" in win.global){
							renderer = "svg";
						}
						break;
					case "vml":
						if(has("vml")){
							renderer = "vml";
						}
						break;
					case "silverlight":
						try{
							if(has("ie")){
								silverlightObject = new ActiveXObject("AgControl.AgControl");
								if(silverlightObject && silverlightObject.IsVersionSupported("1.0")){
									silverlightFlag = true;
								}
							}else{
								if(navigator.plugins["Silverlight Plug-In"]){
									silverlightFlag = true;
								}
							}
						}catch(e){
							silverlightFlag = false;
						}finally{
							silverlightObject = null;
						}
						if(silverlightFlag){
							renderer = "silverlight";
						}
						break;
					case "canvas":
						if(win.global.CanvasRenderingContext2D){
							renderer = "canvas";
						}
						break;
				}
			}

			if (renderer === 'canvas' && config.canvasEvents !== false) {
				renderer = "canvasWithEvents";
			}

			if(config.isDebug){
				console.log("gfx renderer = " + renderer);
			}

			function loadRenderer(){
				require(["dojox/gfx/" + renderer], function(module){
					g.renderer = renderer;
					// memorize the renderer module
					currentRenderer = module;
					// now load it
					load(module);
				});
			}
			if(renderer == "svg" && typeof window.svgweb != "undefined"){
				window.svgweb.addOnLoad(loadRenderer);
			}else{
				loadRenderer();
			}
		}
	};
});

},
'dojox/gfx/shape':function(){
define(["./_base", "dojo/_base/lang", "dojo/_base/declare", "dojo/_base/kernel", "dojo/_base/sniff",
	"dojo/on", "dojo/_base/array", "dojo/dom-construct", "dojo/_base/Color", "./matrix" ],
	function(g, lang, declare, kernel, has, on, arr, domConstruct, Color, matrixLib){

	var shape = g.shape = {
		// summary:
		//		This module contains the core graphics Shape API.
		//		Different graphics renderer implementation modules (svg, canvas, vml, silverlight, etc.) extend this
		//		basic api to provide renderer-specific implementations for each shape.
	};

	shape.Shape = declare("dojox.gfx.shape.Shape", null, {
		// summary:
		//		a Shape object, which knows how to apply
		//		graphical attributes and transformations
	
		constructor: function(){
			// rawNode: Node
			//		underlying graphics-renderer-specific implementation object (if applicable)
			this.rawNode = null;

			// shape: Object
			//		an abstract shape object
			//		(see dojox/gfx.defaultPath,
			//		dojox/gfx.defaultPolyline,
			//		dojox/gfx.defaultRect,
			//		dojox/gfx.defaultEllipse,
			//		dojox/gfx.defaultCircle,
			//		dojox/gfx.defaultLine,
			//		or dojox/gfx.defaultImage)
			this.shape = null;
	
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a transformation matrix
			this.matrix = null;
	
			// fillStyle: dojox/gfx.Fill
			//		a fill object
			//		(see dojox/gfx.defaultLinearGradient,
			//		dojox/gfx.defaultRadialGradient,
			//		dojox/gfx.defaultPattern,
			//		or dojo/Color)
			this.fillStyle = null;
	
			// strokeStyle: dojox/gfx.Stroke
			//		a stroke object
			//		(see dojox/gfx.defaultStroke)
			this.strokeStyle = null;
	
			// bbox: dojox/gfx.Rectangle
			//		a bounding box of this shape
			//		(see dojox/gfx.defaultRect)
			this.bbox = null;
	
			// virtual group structure
	
			// parent: Object
			//		a parent or null
			//		(see dojox/gfx/shape.Surface,
			//		or dojox/gfx.Group)
			this.parent = null;
	
			// parentMatrix: dojox/gfx/matrix.Matrix2D
			//		a transformation matrix inherited from the parent
			this.parentMatrix = null;

			if(has("gfxRegistry")){
				var uid = shape.register(this);
				this.getUID = function(){
					return uid;
				}
			}
		},
		
		destroy: function(){
			// summary:
			//		Releases all internal resources owned by this shape. Once this method has been called,
			//		the instance is considered destroyed and should not be used anymore.
			if(has("gfxRegistry")){
				shape.dispose(this);
			}
			if(this.rawNode && "__gfxObject__" in this.rawNode){
				this.rawNode.__gfxObject__ = null;
			}
			this.rawNode = null;
		},
	
		// trivial getters
	
		getNode: function(){
			// summary:
			//		Different graphics rendering subsystems implement shapes in different ways.  This
			//		method provides access to the underlying graphics subsystem object.  Clients calling this
			//		method and using the return value must be careful not to try sharing or using the underlying node
			//		in a general way across renderer implementation.
			//		Returns the underlying graphics Node, or null if no underlying graphics node is used by this shape.
			return this.rawNode; // Node
		},
		getShape: function(){
			// summary:
			//		returns the current Shape object or null
			//		(see dojox/gfx.defaultPath,
			//		dojox/gfx.defaultPolyline,
			//		dojox/gfx.defaultRect,
			//		dojox/gfx.defaultEllipse,
			//		dojox/gfx.defaultCircle,
			//		dojox/gfx.defaultLine,
			//		or dojox/gfx.defaultImage)
			return this.shape; // Object
		},
		getTransform: function(){
			// summary:
			//		Returns the current transformation matrix applied to this Shape or null
			return this.matrix;	// dojox/gfx/matrix.Matrix2D
		},
		getFill: function(){
			// summary:
			//		Returns the current fill object or null
			//		(see dojox/gfx.defaultLinearGradient,
			//		dojox/gfx.defaultRadialGradient,
			//		dojox/gfx.defaultPattern,
			//		or dojo/Color)
			return this.fillStyle;	// Object
		},
		getStroke: function(){
			// summary:
			//		Returns the current stroke object or null
			//		(see dojox/gfx.defaultStroke)
			return this.strokeStyle;	// Object
		},
		getParent: function(){
			// summary:
			//		Returns the parent Shape, Group or null if this Shape is unparented.
			//		(see dojox/gfx/shape.Surface,
			//		or dojox/gfx.Group)
			return this.parent;	// Object
		},
		getBoundingBox: function(){
			// summary:
			//		Returns the bounding box Rectangle for this shape or null if a BoundingBox cannot be
			//		calculated for the shape on the current renderer or for shapes with no geometric area (points).
			//		A bounding box is a rectangular geometric region
			//		defining the X and Y extent of the shape.
			//		(see dojox/gfx.defaultRect)
			//		Note that this method returns a direct reference to the attribute of this instance. Therefore you should
			//		not modify its value directly but clone it instead.
			return this.bbox;	// dojox/gfx.Rectangle
		},
		getTransformedBoundingBox: function(){
			// summary:
			//		returns an array of four points or null
			//		four points represent four corners of the untransformed bounding box
			var b = this.getBoundingBox();
			if(!b){
				return null;	// null
			}
			var m = this._getRealMatrix(),
				gm = matrixLib;
			return [	// Array
					gm.multiplyPoint(m, b.x, b.y),
					gm.multiplyPoint(m, b.x + b.width, b.y),
					gm.multiplyPoint(m, b.x + b.width, b.y + b.height),
					gm.multiplyPoint(m, b.x, b.y + b.height)
				];
		},
		getEventSource: function(){
			// summary:
			//		returns a Node, which is used as
			//		a source of events for this shape
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			return this.rawNode;	// Node
		},
	
		// empty settings
		
		setClip: function(clip){
			// summary:
			//		sets the clipping area of this shape.
			// description:
			//		The clipping area defines the shape area that will be effectively visible. Everything that
			//		would be drawn outside of the clipping area will not be rendered.
			//		The possible clipping area types are rectangle, ellipse, polyline and path, but all are not
			//		supported by all the renderers. vml only supports rectangle clipping, while the gfx silverlight renderer does not
			//		support path clipping.
			//		The clip parameter defines the clipping area geometry, and should be an object with the following properties:
			//
			//		- {x:Number, y:Number, width:Number, height:Number} for rectangular clip
			//		- {cx:Number, cy:Number, rx:Number, ry:Number} for ellipse clip
			//		- {points:Array} for polyline clip
			//		- {d:String} for a path clip.
			//
			//		The clip geometry coordinates are expressed in the coordinate system used to draw the shape. In other
			//		words, the clipping area is defined in the shape parent coordinate system and the shape transform is automatically applied.
			// example:
			//		The following example shows how to clip a gfx image with all the possible clip geometry: a rectangle,
			//		an ellipse, a circle (using the ellipse geometry), a polyline and a path:
			//
			//	|	surface.createImage({src:img, width:200,height:200}).setClip({x:10,y:10,width:50,height:50});
			//	|	surface.createImage({src:img, x:100,y:50,width:200,height:200}).setClip({cx:200,cy:100,rx:20,ry:30});
			//	|	surface.createImage({src:img, x:0,y:350,width:200,height:200}).setClip({cx:100,cy:425,rx:60,ry:60});
			//	|	surface.createImage({src:img, x:300,y:0,width:200,height:200}).setClip({points:[350,0,450,50,380,130,300,110]});
			//	|	surface.createImage({src:img, x:300,y:350,width:200,height:200}).setClip({d:"M 350,350 C314,414 317,557 373,450.0000 z"});

			// clip: Object
			//		an object that defines the clipping geometry, or null to remove clip.
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			this.clip = clip;
		},
		
		getClip: function(){
			return this.clip;
		},
	
		setShape: function(shape){
			// summary:
			//		sets a shape object
			//		(the default implementation simply ignores it)
			// shape: Object
			//		a shape object
			//		(see dojox/gfx.defaultPath,
			//		dojox/gfx.defaultPolyline,
			//		dojox/gfx.defaultRect,
			//		dojox/gfx.defaultEllipse,
			//		dojox/gfx.defaultCircle,
			//		dojox/gfx.defaultLine,
			//		or dojox/gfx.defaultImage)
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			this.shape = g.makeParameters(this.shape, shape);
			this.bbox = null;
			return this;	// self
		},
		setFill: function(fill){
			// summary:
			//		sets a fill object
			//		(the default implementation simply ignores it)
			// fill: Object
			//		a fill object
			//		(see dojox/gfx.defaultLinearGradient,
			//		dojox/gfx.defaultRadialGradient,
			//		dojox/gfx.defaultPattern,
			//		or dojo/_base/Color)
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			if(!fill){
				// don't fill
				this.fillStyle = null;
				return this;	// self
			}
			var f = null;
			if(typeof(fill) == "object" && "type" in fill){
				// gradient or pattern
				switch(fill.type){
					case "linear":
						f = g.makeParameters(g.defaultLinearGradient, fill);
						break;
					case "radial":
						f = g.makeParameters(g.defaultRadialGradient, fill);
						break;
					case "pattern":
						f = g.makeParameters(g.defaultPattern, fill);
						break;
				}
			}else{
				// color object
				f = g.normalizeColor(fill);
			}
			this.fillStyle = f;
			return this;	// self
		},
		setStroke: function(stroke){
			// summary:
			//		sets a stroke object
			//		(the default implementation simply ignores it)
			// stroke: Object
			//		a stroke object
			//		(see dojox/gfx.defaultStroke)
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			if(!stroke){
				// don't stroke
				this.strokeStyle = null;
				return this;	// self
			}
			// normalize the stroke
			if(typeof stroke == "string" || lang.isArray(stroke) || stroke instanceof Color){
				stroke = {color: stroke};
			}
			var s = this.strokeStyle = g.makeParameters(g.defaultStroke, stroke);
			s.color = g.normalizeColor(s.color);
			return this;	// self
		},
		setTransform: function(matrix){
			// summary:
			//		sets a transformation matrix
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a matrix or a matrix-like object
			//		(see an argument of dojox/gfx/matrix.Matrix2D
			//		constructor for a list of acceptable arguments)
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			this.matrix = matrixLib.clone(matrix ? matrixLib.normalize(matrix) : matrixLib.identity);
			return this._applyTransform();	// self
		},
	
		_applyTransform: function(){
			// summary:
			//		physically sets a matrix
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			return this;	// self
		},
	
		// z-index
	
		moveToFront: function(){
			// summary:
			//		moves a shape to front of its parent's list of shapes
			var p = this.getParent();
			if(p){
				p._moveChildToFront(this);
				this._moveToFront();	// execute renderer-specific action
			}
			return this;	// self
		},
		moveToBack: function(){
			// summary:
			//		moves a shape to back of its parent's list of shapes
			var p = this.getParent();
			if(p){
				p._moveChildToBack(this);
				this._moveToBack();	// execute renderer-specific action
			}
			return this;
		},
		_moveToFront: function(){
			// summary:
			//		renderer-specific hook, see dojox/gfx/shape.Shape.moveToFront()
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
		},
		_moveToBack: function(){
			// summary:
			//		renderer-specific hook, see dojox/gfx/shape.Shape.moveToFront()
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
		},
	
		// apply left & right transformation
	
		applyRightTransform: function(matrix){
			// summary:
			//		multiplies the existing matrix with an argument on right side
			//		(this.matrix * matrix)
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a matrix or a matrix-like object
			//		(see an argument of dojox/gfx/matrix.Matrix2D
			//		constructor for a list of acceptable arguments)
			return matrix ? this.setTransform([this.matrix, matrix]) : this;	// self
		},
		applyLeftTransform: function(matrix){
			// summary:
			//		multiplies the existing matrix with an argument on left side
			//		(matrix * this.matrix)
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a matrix or a matrix-like object
			//		(see an argument of dojox/gfx/matrix.Matrix2D
			//		constructor for a list of acceptable arguments)
			return matrix ? this.setTransform([matrix, this.matrix]) : this;	// self
		},
		applyTransform: function(matrix){
			// summary:
			//		a shortcut for dojox/gfx/shape.Shape.applyRightTransform
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a matrix or a matrix-like object
			//		(see an argument of dojox/gfx/matrix.Matrix2D
			//		constructor for a list of acceptable arguments)
			return matrix ? this.setTransform([this.matrix, matrix]) : this;	// self
		},
	
		// virtual group methods
	
		removeShape: function(silently){
			// summary:
			//		removes the shape from its parent's list of shapes
			// silently: Boolean
			//		if true, do not redraw a picture yet
			if(this.parent){
				this.parent.remove(this, silently);
			}
			return this;	// self
		},
		_setParent: function(parent, matrix){
			// summary:
			//		sets a parent
			// parent: Object
			//		a parent or null
			//		(see dojox/gfx/shape.Surface,
			//		or dojox/gfx.Group)
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix or a matrix-like object
			this.parent = parent;
			return this._updateParentMatrix(matrix);	// self
		},
		_updateParentMatrix: function(matrix){
			// summary:
			//		updates the parent matrix with new matrix
			// matrix: dojox/gfx/Matrix2D
			//		a 2D matrix or a matrix-like object
			this.parentMatrix = matrix ? matrixLib.clone(matrix) : null;
			return this._applyTransform();	// self
		},
		_getRealMatrix: function(){
			// summary:
			//		returns the cumulative ('real') transformation matrix
			//		by combining the shape's matrix with its parent's matrix
			var m = this.matrix;
			var p = this.parent;
			while(p){
				if(p.matrix){
					m = matrixLib.multiply(p.matrix, m);
				}
				p = p.parent;
			}
			return m;	// dojox/gfx/matrix.Matrix2D
		}
	});
	
	shape._eventsProcessing = {
		on: function(type, listener){
			//	summary:
			//		Connects an event to this shape.

			return on(this.getEventSource(), type, shape.fixCallback(this, g.fixTarget, listener));
		},

		connect: function(name, object, method){
			// summary:
			//		connects a handler to an event on this shape
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
			// redirect to fixCallback to normalize events and add the gfxTarget to the event. The latter
			// is done by dojox/gfx.fixTarget which is defined by each renderer
			if(name.substring(0, 2) == "on"){
				name = name.substring(2);
			}
			return this.on(name, method ? lang.hitch(object, method) : object);
		},

		disconnect: function(token){
			// summary:
			//		connects a handler by token from an event on this shape
			
			// COULD BE RE-IMPLEMENTED BY THE RENDERER!
	
			return token.remove();
		}
	};
	
	shape.fixCallback = function(gfxElement, fixFunction, scope, method){
		// summary:
		//		Wraps the callback to allow for tests and event normalization
		//		before it gets invoked. This is where 'fixTarget' is invoked.
		// tags:
		//      private
		// gfxElement: Object
		//		The GFX object that triggers the action (ex.:
		//		dojox/gfx.Surface and dojox/gfx/shape.Shape). A new event property
		//		'gfxTarget' is added to the event to reference this object.
		//		for easy manipulation of GFX objects by the event handlers.
		// fixFunction: Function
		//		The function that implements the logic to set the 'gfxTarget'
		//		property to the event. It should be 'dojox/gfx.fixTarget' for
		//		most of the cases
		// scope: Object
		//		Optional. The scope to be used when invoking 'method'. If
		//		omitted, a global scope is used.
		// method: Function|String
		//		The original callback to be invoked.
		if(!method){
			method = scope;
			scope = null;
		}
		if(lang.isString(method)){
			scope = scope || kernel.global;
			if(!scope[method]){ throw(['dojox.gfx.shape.fixCallback: scope["', method, '"] is null (scope="', scope, '")'].join('')); }
			return function(e){  
				return fixFunction(e,gfxElement) ? scope[method].apply(scope, arguments || []) : undefined; }; // Function
		}
		return !scope 
			? function(e){ 
				return fixFunction(e,gfxElement) ? method.apply(scope, arguments) : undefined; } 
			: function(e){ 
				return fixFunction(e,gfxElement) ? method.apply(scope, arguments || []) : undefined; }; // Function
	};
	lang.extend(shape.Shape, shape._eventsProcessing);
	
	shape.Container = {
		// summary:
		//		a container of shapes, which can be used
		//		as a foundation for renderer-specific groups, or as a way
		//		to logically group shapes (e.g, to propagate matricies)
	
		_init: function() {
			// children: Array
			//		a list of children
			this.children = [];
			this._batch = 0;
		},
	
		// group management
	
		openBatch: function() {
			// summary:
			//		starts a new batch, subsequent new child shapes will be held in
			//		the batch instead of appending to the container directly.
			// description:
			//		Because the canvas renderer has no DOM hierarchy, the canvas implementation differs
			//		such that it suspends the repaint requests for this container until the current batch is closed by a call to closeBatch().
			return this;
		},
		closeBatch: function() {
			// summary:
			//		submits the current batch, append all pending child shapes to DOM
			// description:
			//		On canvas, this method flushes the pending redraws queue.
			return this;
		},
		add: function(shape){
			// summary:
			//		adds a shape to the list
			// shape: dojox/gfx/shape.Shape
			//		the shape to add to the list
			var oldParent = shape.getParent();
			if(oldParent){
				oldParent.remove(shape, true);
			}
			this.children.push(shape);
			return shape._setParent(this, this._getRealMatrix());	// self
		},
		remove: function(shape, silently){
			// summary:
			//		removes a shape from the list
			// shape: dojox/gfx/shape.Shape
			//		the shape to remove
			// silently: Boolean
			//		if true, do not redraw a picture yet
			for(var i = 0; i < this.children.length; ++i){
				if(this.children[i] == shape){
					if(silently){
						// skip for now
					}else{
						shape.parent = null;
						shape.parentMatrix = null;
					}
					this.children.splice(i, 1);
					break;
				}
			}
			return this;	// self
		},
		clear: function(/*Boolean?*/ destroy){
			// summary:
			//		removes all shapes from a group/surface.
			// destroy: Boolean
			//		Indicates whether the children should be destroyed. Optional.
			var shape;
			for(var i = 0; i < this.children.length;++i){
				shape = this.children[i];
				shape.parent = null;
				shape.parentMatrix = null;
				if(destroy){
					shape.destroy();
				}
			}
			this.children = [];
			return this;	// self
		},
		getBoundingBox: function(){
			// summary:
			//		Returns the bounding box Rectangle for this shape.
			if(this.children){
				// if this is a composite shape, then sum up all the children
				var result = null;
				arr.forEach(this.children, function(shape){
					var bb = shape.getBoundingBox();
					if(bb){
						var ct = shape.getTransform();
						if(ct){
							bb = matrixLib.multiplyRectangle(ct, bb);
						}
						if(result){
							// merge two bbox 
							result.x = Math.min(result.x, bb.x);
							result.y = Math.min(result.y, bb.y);
							result.endX = Math.max(result.endX, bb.x + bb.width);
							result.endY = Math.max(result.endY, bb.y + bb.height);
						}else{
							// first bbox 
							result = {
								x: bb.x,
								y: bb.y,
								endX: bb.x + bb.width,
								endY: bb.y + bb.height
							};
						}
					}
				});
				if(result){
					result.width = result.endX - result.x;
					result.height = result.endY - result.y;
				}
				return result; // dojox/gfx.Rectangle
			}
			// unknown/empty bounding box, subclass shall override this impl 
			return null;
		},
		// moving child nodes
		_moveChildToFront: function(shape){
			// summary:
			//		moves a shape to front of the list of shapes
			// shape: dojox/gfx/shape.Shape
			//		one of the child shapes to move to the front
			for(var i = 0; i < this.children.length; ++i){
				if(this.children[i] == shape){
					this.children.splice(i, 1);
					this.children.push(shape);
					break;
				}
			}
			return this;	// self
		},
		_moveChildToBack: function(shape){
			// summary:
			//		moves a shape to back of the list of shapes
			// shape: dojox/gfx/shape.Shape
			//		one of the child shapes to move to the front
			for(var i = 0; i < this.children.length; ++i){
				if(this.children[i] == shape){
					this.children.splice(i, 1);
					this.children.unshift(shape);
					break;
				}
			}
			return this;	// self
		}
	};

	shape.Surface = declare("dojox.gfx.shape.Surface", null, {
		// summary:
		//		a surface object to be used for drawings
		constructor: function(){
			// underlying node
			this.rawNode = null;
			// the parent node
			this._parent = null;
			// the list of DOM nodes to be deleted in the case of destruction
			this._nodes = [];
			// the list of events to be detached in the case of destruction
			this._events = [];
		},
		destroy: function(){
			// summary:
			//		destroy all relevant external resources and release all
			//		external references to make this object garbage-collectible
			arr.forEach(this._nodes, domConstruct.destroy);
			this._nodes = [];
			arr.forEach(this._events, function(h){ if(h){ h.remove(); } });
			this._events = [];
			this.rawNode = null;	// recycle it in _nodes, if it needs to be recycled
			if(has("ie")){
				while(this._parent.lastChild){
					domConstruct.destroy(this._parent.lastChild);
				}
			}else{
				this._parent.innerHTML = "";
			}
			this._parent = null;
		},
		getEventSource: function(){
			// summary:
			//		returns a node, which can be used to attach event listeners
			return this.rawNode; // Node
		},
		_getRealMatrix: function(){
			// summary:
			//		always returns the identity matrix
			return null;	// dojox/gfx/Matrix2D
		},
		/*=====
		 setDimensions: function(width, height){
			 // summary:
			 //		sets the width and height of the rawNode
			 // width: String
			 //		width of surface, e.g., "100px"
			 // height: String
			 //		height of surface, e.g., "100px"
			 return this;	// self
		 },
		 getDimensions: function(){
			 // summary:
			 //     gets current width and height in pixels
			 // returns: Object
			 //     object with properties "width" and "height"
		 },
		 =====*/
		isLoaded: true,
		onLoad: function(/*dojox/gfx/shape.Surface*/ surface){
			// summary:
			//		local event, fired once when the surface is created
			//		asynchronously, used only when isLoaded is false, required
			//		only for Silverlight.
		},
		whenLoaded: function(/*Object|Null*/ context, /*Function|String*/ method){
			var f = lang.hitch(context, method);
			if(this.isLoaded){
				f(this);
			}else{
				on.once(this, "load", function(surface){
					f(surface);
				});
			}
		}
	});
	lang.extend(shape.Surface, shape._eventsProcessing);

	/*=====
	g.Point = declare("dojox/gfx.Point", null, {
		// summary:
		//		2D point for drawings - {x, y}
		// description:
		//		Do not use this object directly!
		//		Use the naked object instead: {x: 1, y: 2}.
	});

	g.Rectangle = declare("dojox.gfx.Rectangle", null, {
		// summary:
		//		rectangle - {x, y, width, height}
		// description:
		//		Do not use this object directly!
		//		Use the naked object instead: {x: 1, y: 2, width: 100, height: 200}.
	});
	 =====*/


	shape.Rect = declare("dojox.gfx.shape.Rect", shape.Shape, {
		// summary:
		//		a generic rectangle
		constructor: function(rawNode){
			// rawNode: Node
			//		The underlying graphics system object (typically a DOM Node)
			this.shape = g.getDefault("Rect");
			this.rawNode = rawNode;
		},
		getBoundingBox: function(){
			// summary:
			//		returns the bounding box (its shape in this case)
			return this.shape;	// dojox/gfx.Rectangle
		}
	});
	
	shape.Ellipse = declare("dojox.gfx.shape.Ellipse", shape.Shape, {
		// summary:
		//		a generic ellipse
		constructor: function(rawNode){
			// rawNode: Node
			//		a DOM Node
			this.shape = g.getDefault("Ellipse");
			this.rawNode = rawNode;
		},
		getBoundingBox: function(){
			// summary:
			//		returns the bounding box
			if(!this.bbox){
				var shape = this.shape;
				this.bbox = {x: shape.cx - shape.rx, y: shape.cy - shape.ry,
					width: 2 * shape.rx, height: 2 * shape.ry};
			}
			return this.bbox;	// dojox/gfx.Rectangle
		}
	});
	
	shape.Circle = declare("dojox.gfx.shape.Circle", shape.Shape, {
		// summary:
		//		a generic circle
		constructor: function(rawNode){
			// rawNode: Node
			//		a DOM Node
			this.shape = g.getDefault("Circle");
			this.rawNode = rawNode;
		},
		getBoundingBox: function(){
			// summary:
			//		returns the bounding box
			if(!this.bbox){
				var shape = this.shape;
				this.bbox = {x: shape.cx - shape.r, y: shape.cy - shape.r,
					width: 2 * shape.r, height: 2 * shape.r};
			}
			return this.bbox;	// dojox/gfx.Rectangle
		}
	});
	
	shape.Line = declare("dojox.gfx.shape.Line", shape.Shape, {
		// summary:
		//		a generic line (do not instantiate it directly)
		constructor: function(rawNode){
			// rawNode: Node
			//		a DOM Node
			this.shape = g.getDefault("Line");
			this.rawNode = rawNode;
		},
		getBoundingBox: function(){
			// summary:
			//		returns the bounding box
			if(!this.bbox){
				var shape = this.shape;
				this.bbox = {
					x:		Math.min(shape.x1, shape.x2),
					y:		Math.min(shape.y1, shape.y2),
					width:	Math.abs(shape.x2 - shape.x1),
					height:	Math.abs(shape.y2 - shape.y1)
				};
			}
			return this.bbox;	// dojox/gfx.Rectangle
		}
	});
	
	shape.Polyline = declare("dojox.gfx.shape.Polyline", shape.Shape, {
		// summary:
		//		a generic polyline/polygon (do not instantiate it directly)
		constructor: function(rawNode){
			// rawNode: Node
			//		a DOM Node
			this.shape = g.getDefault("Polyline");
			this.rawNode = rawNode;
		},
		setShape: function(points, closed){
			// summary:
			//		sets a polyline/polygon shape object
			// points: Object|Array
			//		a polyline/polygon shape object, or an array of points
			// closed: Boolean
			//		close the polyline to make a polygon
			if(points && points instanceof Array){
				this.inherited(arguments, [{points: points}]);
				if(closed && this.shape.points.length){
					this.shape.points.push(this.shape.points[0]);
				}
			}else{
				this.inherited(arguments, [points]);
			}
			return this;	// self
		},
		_normalizePoints: function(){
			// summary:
			//		normalize points to array of {x:number, y:number}
			var p = this.shape.points, l = p && p.length;
			if(l && typeof p[0] == "number"){
				var points = [];
				for(var i = 0; i < l; i += 2){
					points.push({x: p[i], y: p[i + 1]});
				}
				this.shape.points = points;
			}
		},
		getBoundingBox: function(){
			// summary:
			//		returns the bounding box
			if(!this.bbox && this.shape.points.length){
				var p = this.shape.points;
				var l = p.length;
				var t = p[0];
				var bbox = {l: t.x, t: t.y, r: t.x, b: t.y};
				for(var i = 1; i < l; ++i){
					t = p[i];
					if(bbox.l > t.x) bbox.l = t.x;
					if(bbox.r < t.x) bbox.r = t.x;
					if(bbox.t > t.y) bbox.t = t.y;
					if(bbox.b < t.y) bbox.b = t.y;
				}
				this.bbox = {
					x:		bbox.l,
					y:		bbox.t,
					width:	bbox.r - bbox.l,
					height:	bbox.b - bbox.t
				};
			}
			return this.bbox;	// dojox/gfx.Rectangle
		}
	});
	
	shape.Image = declare("dojox.gfx.shape.Image", shape.Shape, {
		// summary:
		//		a generic image (do not instantiate it directly)
		constructor: function(rawNode){
			// rawNode: Node
			//		a DOM Node
			this.shape = g.getDefault("Image");
			this.rawNode = rawNode;
		},
		getBoundingBox: function(){
			// summary:
			//		returns the bounding box (its shape in this case)
			return this.shape;	// dojox/gfx.Rectangle
		},
		setStroke: function(){
			// summary:
			//		ignore setting a stroke style
			return this;	// self
		},
		setFill: function(){
			// summary:
			//		ignore setting a fill style
			return this;	// self
		}
	});
	
	shape.Text = declare(shape.Shape, {
		// summary:
		//		a generic text (do not instantiate it directly)
		constructor: function(rawNode){
			// rawNode: Node
			//		a DOM Node
			this.fontStyle = null;
			this.shape = g.getDefault("Text");
			this.rawNode = rawNode;
		},
		getFont: function(){
			// summary:
			//		returns the current font object or null
			return this.fontStyle;	// Object
		},
		setFont: function(newFont){
			// summary:
			//		sets a font for text
			// newFont: Object
			//		a font object (see dojox/gfx.defaultFont) or a font string
			this.fontStyle = typeof newFont == "string" ? g.splitFontString(newFont) :
				g.makeParameters(g.defaultFont, newFont);
			this._setFont();
			return this;	// self
		},
		getBoundingBox: function(){
			var bbox = null, s = this.getShape();
			if(s.text){
				bbox = g._base._computeTextBoundingBox(this);
			}
			return bbox;
		}
	});
	
	shape.Creator = {
		// summary:
		//		shape creators
		createShape: function(shape){
			// summary:
			//		creates a shape object based on its type; it is meant to be used
			//		by group-like objects
			// shape: Object
			//		a shape descriptor object
			// returns: dojox/gfx/shape.Shape | Null
			//      a fully instantiated surface-specific Shape object
			switch(shape.type){
				case g.defaultPath.type:		return this.createPath(shape);
				case g.defaultRect.type:		return this.createRect(shape);
				case g.defaultCircle.type:	    return this.createCircle(shape);
				case g.defaultEllipse.type:	    return this.createEllipse(shape);
				case g.defaultLine.type:		return this.createLine(shape);
				case g.defaultPolyline.type:	return this.createPolyline(shape);
				case g.defaultImage.type:		return this.createImage(shape);
				case g.defaultText.type:		return this.createText(shape);
				case g.defaultTextPath.type:	return this.createTextPath(shape);
			}
			return null;
		},
		createGroup: function(){
			// summary:
			//		creates a group shape
			return this.createObject(g.Group);	// dojox/gfx/Group
		},
		createRect: function(rect){
			// summary:
			//		creates a rectangle shape
			// rect: Object
			//		a path object (see dojox/gfx.defaultRect)
			return this.createObject(g.Rect, rect);	// dojox/gfx/shape.Rect
		},
		createEllipse: function(ellipse){
			// summary:
			//		creates an ellipse shape
			// ellipse: Object
			//		an ellipse object (see dojox/gfx.defaultEllipse)
			return this.createObject(g.Ellipse, ellipse);	// dojox/gfx/shape.Ellipse
		},
		createCircle: function(circle){
			// summary:
			//		creates a circle shape
			// circle: Object
			//		a circle object (see dojox/gfx.defaultCircle)
			return this.createObject(g.Circle, circle);	// dojox/gfx/shape.Circle
		},
		createLine: function(line){
			// summary:
			//		creates a line shape
			// line: Object
			//		a line object (see dojox/gfx.defaultLine)
			return this.createObject(g.Line, line);	// dojox/gfx/shape.Line
		},
		createPolyline: function(points){
			// summary:
			//		creates a polyline/polygon shape
			// points: Object
			//		a points object (see dojox/gfx.defaultPolyline)
			//		or an Array of points
			return this.createObject(g.Polyline, points);	// dojox/gfx/shape.Polyline
		},
		createImage: function(image){
			// summary:
			//		creates a image shape
			// image: Object
			//		an image object (see dojox/gfx.defaultImage)
			return this.createObject(g.Image, image);	// dojox/gfx/shape.Image
		},
		createText: function(text){
			// summary:
			//		creates a text shape
			// text: Object
			//		a text object (see dojox/gfx.defaultText)
			return this.createObject(g.Text, text);	// dojox/gfx/shape.Text
		},
		createPath: function(path){
			// summary:
			//		creates a path shape
			// path: Object
			//		a path object (see dojox/gfx.defaultPath)
			return this.createObject(g.Path, path);	// dojox/gfx/shape.Path
		},
		createTextPath: function(text){
			// summary:
			//		creates a text shape
			// text: Object
			//		a textpath object (see dojox/gfx.defaultTextPath)
			return this.createObject(g.TextPath, {}).setText(text);	// dojox/gfx/shape.TextPath
		},
		createObject: function(shapeType, rawShape){
			// summary:
			//		creates an instance of the passed shapeType class
			// shapeType: Function
			//		a class constructor to create an instance of
			// rawShape: Object 
			//		properties to be passed in to the classes 'setShape' method
	
			// SHOULD BE RE-IMPLEMENTED BY THE RENDERER!
			return null;	// dojox/gfx/shape.Shape
		}
	};
	
	/*=====
	 lang.extend(shape.Surface, shape.Container);
	 lang.extend(shape.Surface, shape.Creator);

	 g.Group = declare(shape.Shape, {
		// summary:
		//		a group shape, which can be used
		//		to logically group shapes (e.g, to propagate matricies)
	});
	lang.extend(g.Group, shape.Container);
	lang.extend(g.Group, shape.Creator);

	g.Rect     = shape.Rect;
	g.Circle   = shape.Circle;
	g.Ellipse  = shape.Ellipse;
	g.Line     = shape.Line;
	g.Polyline = shape.Polyline;
	g.Text     = shape.Text;
	g.Surface  = shape.Surface;
	=====*/

	return shape;
});

},
'dojox/gfx/matrix':function(){
define(["./_base","dojo/_base/lang"], 
  function(g, lang){
	var m = g.matrix = {};

	// candidates for dojox.math:
	var _degToRadCache = {};
	m._degToRad = function(degree){
		return _degToRadCache[degree] || (_degToRadCache[degree] = (Math.PI * degree / 180));
	};
	m._radToDeg = function(radian){ return radian / Math.PI * 180; };

	m.Matrix2D = function(arg){
		// summary:
		//		a 2D matrix object
		// description:
		//		Normalizes a 2D matrix-like object. If arrays is passed,
		//		all objects of the array are normalized and multiplied sequentially.
		// arg: Object
		//		a 2D matrix-like object, a number, or an array of such objects
		if(arg){
			if(typeof arg == "number"){
				this.xx = this.yy = arg;
			}else if(arg instanceof Array){
				if(arg.length > 0){
					var matrix = m.normalize(arg[0]);
					// combine matrices
					for(var i = 1; i < arg.length; ++i){
						var l = matrix, r = m.normalize(arg[i]);
						matrix = new m.Matrix2D();
						matrix.xx = l.xx * r.xx + l.xy * r.yx;
						matrix.xy = l.xx * r.xy + l.xy * r.yy;
						matrix.yx = l.yx * r.xx + l.yy * r.yx;
						matrix.yy = l.yx * r.xy + l.yy * r.yy;
						matrix.dx = l.xx * r.dx + l.xy * r.dy + l.dx;
						matrix.dy = l.yx * r.dx + l.yy * r.dy + l.dy;
					}
					lang.mixin(this, matrix);
				}
			}else{
				lang.mixin(this, arg);
			}
		}
	};

	// the default (identity) matrix, which is used to fill in missing values
	lang.extend(m.Matrix2D, {xx: 1, xy: 0, yx: 0, yy: 1, dx: 0, dy: 0});

	lang.mixin(m, {
		// summary:
		//		class constants, and methods of dojox/gfx/matrix

		// matrix constants

		// identity: dojox/gfx/matrix.Matrix2D
		//		an identity matrix constant: identity * (x, y) == (x, y)
		identity: new m.Matrix2D(),

		// flipX: dojox/gfx/matrix.Matrix2D
		//		a matrix, which reflects points at x = 0 line: flipX * (x, y) == (-x, y)
		flipX:    new m.Matrix2D({xx: -1}),

		// flipY: dojox/gfx/matrix.Matrix2D
		//		a matrix, which reflects points at y = 0 line: flipY * (x, y) == (x, -y)
		flipY:    new m.Matrix2D({yy: -1}),

		// flipXY: dojox/gfx/matrix.Matrix2D
		//		a matrix, which reflects points at the origin of coordinates: flipXY * (x, y) == (-x, -y)
		flipXY:   new m.Matrix2D({xx: -1, yy: -1}),

		// matrix creators

		translate: function(a, b){
			// summary:
			//		forms a translation matrix
			// description:
			//		The resulting matrix is used to translate (move) points by specified offsets.
			// a: Number|dojox/gfx.Point
			//		an x coordinate value, or a point-like object, which specifies offsets for both dimensions
			// b: Number?
			//		a y coordinate value
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 1){
				return new m.Matrix2D({dx: a, dy: b}); // dojox/gfx/matrix.Matrix2D
			}
			// branch
			return new m.Matrix2D({dx: a.x, dy: a.y}); // dojox/gfx/matrix.Matrix2D
		},
		scale: function(a, b){
			// summary:
			//		forms a scaling matrix
			// description:
			//		The resulting matrix is used to scale (magnify) points by specified offsets.
			// a: Number|dojox/gfx.Point
			//		a scaling factor used for the x coordinate, or
			//		a uniform scaling factor used for the both coordinates, or
			//		a point-like object, which specifies scale factors for both dimensions
			// b: Number?
			//		a scaling factor used for the y coordinate
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 1){
				return new m.Matrix2D({xx: a, yy: b}); // dojox/gfx/matrix.Matrix2D
			}
			if(typeof a == "number"){
				return new m.Matrix2D({xx: a, yy: a}); // dojox/gfx/matrix.Matrix2D
			}
			return new m.Matrix2D({xx: a.x, yy: a.y}); // dojox/gfx/matrix.Matrix2D
		},
		rotate: function(angle){
			// summary:
			//		forms a rotating matrix
			// description:
			//		The resulting matrix is used to rotate points
			//		around the origin of coordinates (0, 0) by specified angle.
			// angle: Number
			//		an angle of rotation in radians (>0 for CW)
			// returns: dojox/gfx/matrix.Matrix2D
			var c = Math.cos(angle);
			var s = Math.sin(angle);
			return new m.Matrix2D({xx: c, xy: -s, yx: s, yy: c}); // dojox/gfx/matrix.Matrix2D
		},
		rotateg: function(degree){
			// summary:
			//		forms a rotating matrix
			// description:
			//		The resulting matrix is used to rotate points
			//		around the origin of coordinates (0, 0) by specified degree.
			//		See dojox/gfx/matrix.rotate() for comparison.
			// degree: Number
			//		an angle of rotation in degrees (>0 for CW)
			// returns: dojox/gfx/matrix.Matrix2D
			return m.rotate(m._degToRad(degree)); // dojox/gfx/matrix.Matrix2D
		},
		skewX: function(angle) {
			// summary:
			//		forms an x skewing matrix
			// description:
			//		The resulting matrix is used to skew points in the x dimension
			//		around the origin of coordinates (0, 0) by specified angle.
			// angle: Number
			//		a skewing angle in radians
			// returns: dojox/gfx/matrix.Matrix2D
			return new m.Matrix2D({xy: Math.tan(angle)}); // dojox/gfx/matrix.Matrix2D
		},
		skewXg: function(degree){
			// summary:
			//		forms an x skewing matrix
			// description:
			//		The resulting matrix is used to skew points in the x dimension
			//		around the origin of coordinates (0, 0) by specified degree.
			//		See dojox/gfx/matrix.skewX() for comparison.
			// degree: Number
			//		a skewing angle in degrees
			// returns: dojox/gfx/matrix.Matrix2D
			return m.skewX(m._degToRad(degree)); // dojox/gfx/matrix.Matrix2D
		},
		skewY: function(angle){
			// summary:
			//		forms a y skewing matrix
			// description:
			//		The resulting matrix is used to skew points in the y dimension
			//		around the origin of coordinates (0, 0) by specified angle.
			// angle: Number
			//		a skewing angle in radians
			// returns: dojox/gfx/matrix.Matrix2D
			return new m.Matrix2D({yx: Math.tan(angle)}); // dojox/gfx/matrix.Matrix2D
		},
		skewYg: function(degree){
			// summary:
			//		forms a y skewing matrix
			// description:
			//		The resulting matrix is used to skew points in the y dimension
			//		around the origin of coordinates (0, 0) by specified degree.
			//		See dojox/gfx/matrix.skewY() for comparison.
			// degree: Number
			//		a skewing angle in degrees
			// returns: dojox/gfx/matrix.Matrix2D
			return m.skewY(m._degToRad(degree)); // dojox/gfx/matrix.Matrix2D
		},
		reflect: function(a, b){
			// summary:
			//		forms a reflection matrix
			// description:
			//		The resulting matrix is used to reflect points around a vector,
			//		which goes through the origin.
			// a: dojox/gfx.Point|Number
			//		a point-like object, which specifies a vector of reflection, or an X value
			// b: Number?
			//		a Y value
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length == 1){
				b = a.y;
				a = a.x;
			}
			// make a unit vector
			var a2 = a * a, b2 = b * b, n2 = a2 + b2, xy = 2 * a * b / n2;
			return new m.Matrix2D({xx: 2 * a2 / n2 - 1, xy: xy, yx: xy, yy: 2 * b2 / n2 - 1}); // dojox/gfx/matrix.Matrix2D
		},
		project: function(a, b){
			// summary:
			//		forms an orthogonal projection matrix
			// description:
			//		The resulting matrix is used to project points orthogonally on a vector,
			//		which goes through the origin.
			// a: dojox/gfx.Point|Number
			//		a point-like object, which specifies a vector of projection, or
			//		an x coordinate value
			// b: Number?
			//		a y coordinate value
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length == 1){
				b = a.y;
				a = a.x;
			}
			// make a unit vector
			var a2 = a * a, b2 = b * b, n2 = a2 + b2, xy = a * b / n2;
			return new m.Matrix2D({xx: a2 / n2, xy: xy, yx: xy, yy: b2 / n2}); // dojox/gfx/matrix.Matrix2D
		},

		// ensure matrix 2D conformance
		normalize: function(matrix){
			// summary:
			//		converts an object to a matrix, if necessary
			// description:
			//		Converts any 2D matrix-like object or an array of
			//		such objects to a valid dojox/gfx/matrix.Matrix2D object.
			// matrix: Object
			//		an object, which is converted to a matrix, if necessary
			// returns: dojox/gfx/matrix.Matrix2D
			return (matrix instanceof m.Matrix2D) ? matrix : new m.Matrix2D(matrix); // dojox/gfx/matrix.Matrix2D
		},

		// common operations

		isIdentity: function(matrix){
			// summary:
			//		returns whether the specified matrix is the identity.
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix object to be tested
			// returns: Boolean
			return matrix.xx == 1 && matrix.xy == 0 && matrix.yx == 0 && matrix.yy == 1 && matrix.dx == 0 && matrix.dy == 0; // Boolean
		},
		clone: function(matrix){
			// summary:
			//		creates a copy of a 2D matrix
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix-like object to be cloned
			// returns: dojox/gfx/matrix.Matrix2D
			var obj = new m.Matrix2D();
			for(var i in matrix){
				if(typeof(matrix[i]) == "number" && typeof(obj[i]) == "number" && obj[i] != matrix[i]) obj[i] = matrix[i];
			}
			return obj; // dojox/gfx/matrix.Matrix2D
		},
		invert: function(matrix){
			// summary:
			//		inverts a 2D matrix
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix-like object to be inverted
			// returns: dojox/gfx/matrix.Matrix2D
			var M = m.normalize(matrix),
				D = M.xx * M.yy - M.xy * M.yx;
				M = new m.Matrix2D({
					xx: M.yy/D, xy: -M.xy/D,
					yx: -M.yx/D, yy: M.xx/D,
					dx: (M.xy * M.dy - M.yy * M.dx) / D,
					dy: (M.yx * M.dx - M.xx * M.dy) / D
				});
			return M; // dojox/gfx/matrix.Matrix2D
		},
		_multiplyPoint: function(matrix, x, y){
			// summary:
			//		applies a matrix to a point
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix object to be applied
			// x: Number
			//		an x coordinate of a point
			// y: Number
			//		a y coordinate of a point
			// returns: dojox/gfx.Point
			return {x: matrix.xx * x + matrix.xy * y + matrix.dx, y: matrix.yx * x + matrix.yy * y + matrix.dy}; // dojox/gfx.Point
		},
		multiplyPoint: function(matrix, /* Number||Point */ a, /* Number? */ b){
			// summary:
			//		applies a matrix to a point
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix object to be applied
			// a: Number|dojox/gfx.Point
			//		an x coordinate of a point, or a point
			// b: Number?
			//		a y coordinate of a point
			// returns: dojox/gfx.Point
			var M = m.normalize(matrix);
			if(typeof a == "number" && typeof b == "number"){
				return m._multiplyPoint(M, a, b); // dojox/gfx.Point
			}
			return m._multiplyPoint(M, a.x, a.y); // dojox/gfx.Point
		},
		multiplyRectangle: function(matrix, /*Rectangle*/ rect){
			// summary:
			//		Applies a matrix to a rectangle.
			// description:
			//		The method applies the transformation on all corners of the
			//		rectangle and returns the smallest rectangle enclosing the 4 transformed
			//		points.
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix object to be applied.
			// rect: Rectangle
			//		the rectangle to transform.
			// returns: dojox/gfx.Rectangle
			var M = m.normalize(matrix);
			rect = rect || {x:0, y:0, width:0, height:0}; 
			if(m.isIdentity(M))
				return {x: rect.x, y: rect.y, width: rect.width, height: rect.height}; // dojo/gfx.Rectangle
			var p0 = m.multiplyPoint(M, rect.x, rect.y),
				p1 = m.multiplyPoint(M, rect.x, rect.y + rect.height),
				p2 = m.multiplyPoint(M, rect.x + rect.width, rect.y),
				p3 = m.multiplyPoint(M, rect.x + rect.width, rect.y + rect.height),
				minx = Math.min(p0.x, p1.x, p2.x, p3.x),
				miny = Math.min(p0.y, p1.y, p2.y, p3.y),
				maxx = Math.max(p0.x, p1.x, p2.x, p3.x),
				maxy = Math.max(p0.y, p1.y, p2.y, p3.y);
			return{ // dojo/gfx.Rectangle
				x: minx,
				y: miny,
				width: maxx - minx,
				height: maxy - miny
			};
		},
		multiply: function(matrix){
			// summary:
			//		combines matrices by multiplying them sequentially in the given order
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix-like object,
			//		all subsequent arguments are matrix-like objects too
			var M = m.normalize(matrix);
			// combine matrices
			for(var i = 1; i < arguments.length; ++i){
				var l = M, r = m.normalize(arguments[i]);
				M = new m.Matrix2D();
				M.xx = l.xx * r.xx + l.xy * r.yx;
				M.xy = l.xx * r.xy + l.xy * r.yy;
				M.yx = l.yx * r.xx + l.yy * r.yx;
				M.yy = l.yx * r.xy + l.yy * r.yy;
				M.dx = l.xx * r.dx + l.xy * r.dy + l.dx;
				M.dy = l.yx * r.dx + l.yy * r.dy + l.dy;
			}
			return M; // dojox/gfx/matrix.Matrix2D
		},

		// high level operations

		_sandwich: function(matrix, x, y){
			// summary:
			//		applies a matrix at a central point
			// matrix: dojox/gfx/matrix.Matrix2D
			//		a 2D matrix-like object, which is applied at a central point
			// x: Number
			//		an x component of the central point
			// y: Number
			//		a y component of the central point
			return m.multiply(m.translate(x, y), matrix, m.translate(-x, -y)); // dojox/gfx/matrix.Matrix2D
		},
		scaleAt: function(a, b, c, d){
			// summary:
			//		scales a picture using a specified point as a center of scaling
			// description:
			//		Compare with dojox/gfx/matrix.scale().
			// a: Number
			//		a scaling factor used for the x coordinate, or a uniform scaling factor used for both coordinates
			// b: Number?
			//		a scaling factor used for the y coordinate
			// c: Number|Point
			//		an x component of a central point, or a central point
			// d: Number
			//		a y component of a central point
			// returns: dojox/gfx/matrix.Matrix2D
			switch(arguments.length){
				case 4:
					// a and b are scale factor components, c and d are components of a point
					return m._sandwich(m.scale(a, b), c, d); // dojox/gfx/matrix.Matrix2D
				case 3:
					if(typeof c == "number"){
						return m._sandwich(m.scale(a), b, c); // dojox/gfx/matrix.Matrix2D
					}
					return m._sandwich(m.scale(a, b), c.x, c.y); // dojox/gfx/matrix.Matrix2D
			}
			return m._sandwich(m.scale(a), b.x, b.y); // dojox/gfx/matrix.Matrix2D
		},
		rotateAt: function(angle, a, b){
			// summary:
			//		rotates a picture using a specified point as a center of rotation
			// description:
			//		Compare with dojox/gfx/matrix.rotate().
			// angle: Number
			//		an angle of rotation in radians (>0 for CW)
			// a: Number|dojox/gfx.Point
			//		an x component of a central point, or a central point
			// b: Number?
			//		a y component of a central point
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 2){
				return m._sandwich(m.rotate(angle), a, b); // dojox/gfx/matrix.Matrix2D
			}
			return m._sandwich(m.rotate(angle), a.x, a.y); // dojox/gfx/matrix.Matrix2D
		},
		rotategAt: function(degree, a, b){
			// summary:
			//		rotates a picture using a specified point as a center of rotation
			// description:
			//		Compare with dojox/gfx/matrix.rotateg().
			// degree: Number
			//		an angle of rotation in degrees (>0 for CW)
			// a: Number|dojox/gfx.Point
			//		an x component of a central point, or a central point
			// b: Number?
			//		a y component of a central point
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 2){
				return m._sandwich(m.rotateg(degree), a, b); // dojox/gfx/matrix.Matrix2D
			}
			return m._sandwich(m.rotateg(degree), a.x, a.y); // dojox/gfx/matrix.Matrix2D
		},
		skewXAt: function(angle, a, b){
			// summary:
			//		skews a picture along the x axis using a specified point as a center of skewing
			// description:
			//		Compare with dojox/gfx/matrix.skewX().
			// angle: Number
			//		a skewing angle in radians
			// a: Number|dojox/gfx.Point
			//		an x component of a central point, or a central point
			// b: Number?
			//		a y component of a central point
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 2){
				return m._sandwich(m.skewX(angle), a, b); // dojox/gfx/matrix.Matrix2D
			}
			return m._sandwich(m.skewX(angle), a.x, a.y); // dojox/gfx/matrix.Matrix2D
		},
		skewXgAt: function(degree, a, b){
			// summary:
			//		skews a picture along the x axis using a specified point as a center of skewing
			// description:
			//		Compare with dojox/gfx/matrix.skewXg().
			// degree: Number
			//		a skewing angle in degrees
			// a: Number|dojox/gfx.Point
			//		an x component of a central point, or a central point
			// b: Number?
			//		a y component of a central point
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 2){
				return m._sandwich(m.skewXg(degree), a, b); // dojox/gfx/matrix.Matrix2D
			}
			return m._sandwich(m.skewXg(degree), a.x, a.y); // dojox/gfx/matrix.Matrix2D
		},
		skewYAt: function(angle, a, b){
			// summary:
			//		skews a picture along the y axis using a specified point as a center of skewing
			// description:
			//		Compare with dojox/gfx/matrix.skewY().
			// angle: Number
			//		a skewing angle in radians
			// a: Number|dojox/gfx.Point
			//		an x component of a central point, or a central point
			// b: Number?
			//		a y component of a central point
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 2){
				return m._sandwich(m.skewY(angle), a, b); // dojox/gfx/matrix.Matrix2D
			}
			return m._sandwich(m.skewY(angle), a.x, a.y); // dojox/gfx/matrix.Matrix2D
		},
		skewYgAt: function(/* Number */ degree, /* Number||Point */ a, /* Number? */ b){
			// summary:
			//		skews a picture along the y axis using a specified point as a center of skewing
			// description:
			//		Compare with dojox/gfx/matrix.skewYg().
			// degree: Number
			//		a skewing angle in degrees
			// a: Number|dojox/gfx.Point
			//		an x component of a central point, or a central point
			// b: Number?
			//		a y component of a central point
			// returns: dojox/gfx/matrix.Matrix2D
			if(arguments.length > 2){
				return m._sandwich(m.skewYg(degree), a, b); // dojox/gfx/matrix.Matrix2D
			}
			return m._sandwich(m.skewYg(degree), a.x, a.y); // dojox/gfx/matrix.Matrix2D
		}

		//TODO: rect-to-rect mapping, scale-to-fit (isotropic and anisotropic versions)

	});
	// propagate Matrix2D up
	g.Matrix2D = m.Matrix2D;

	return m;
});



},
'dojox/geo/openlayers/GfxLayer':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/connect",
	"dojo/dom-style",
	"dojox/gfx",
	"dojox/gfx/matrix",
	"./Feature",
	"./Layer"
], function(declare, connect, style, gfx, matrix, Feature, Layer){

	return declare("dojox.geo.openlayers.GfxLayer", Layer, {
		// summary:
		//		A layer dedicated to render dojox.geo.openlayers.GeometryFeature
		// description:
		//		A layer class for rendering geometries as dojox.gfx.Shape objects.
		//		This layer class accepts Features which encapsulates graphic objects to be added to the map.
		//		All objects should be added to this group.
		// tags:
		//		private
		_viewport: null,

		constructor: function(name, options){
			// summary:
			//		Constructs a new GFX layer.
			var s = gfx.createSurface(this.olLayer.div, 100, 100);
			this._surface = s;
			var vp;
			if(options && options.viewport){
				vp = options.viewport;
			}else{
				vp = s.createGroup();
			}
			this.setViewport(vp);
			connect.connect(this.olLayer, "onMapResize", this, "onMapResize");
			this.olLayer.getDataExtent = this.getDataExtent;
		},

		getViewport: function(){
			// summary:
			//		Gets the viewport
			// tags:
			//		internal
			return this._viewport;
		},

		setViewport: function(g){
			// summary:
			//		Sets the viewport
			// g: dojox.gfx.Group
			// tags:
			//		internal
			if(this._viewport){
				this._viewport.removeShape();
			}
			this._viewport = g;
			this._surface.add(g);
		},

		onMapResize: function(){
			// summary:
			//		Called when map is resized.
			// tags:
			//		protected
			this._surfaceSize();
		},

		setMap: function(map){
			// summary:
			//		Sets the map for this layer.
			// tags:
			//		protected
			this.inherited(arguments);
			this._surfaceSize();
		},

		getDataExtent: function(){
			// summary:
			//		Get data extent
			// tags:
			//		private
			var ret = this._surface.getDimensions();
			return ret;
		},

		getSurface: function(){
			// summary:
			//		Get the underlying dojox.gfx.Surface
			// returns:
			//		The dojox.gfx.Surface this layer uses to draw its GFX rendering.
			return this._surface; // dojox.gfx.Surface
		},

		_surfaceSize: function(){
			// summary:
			//		Recomputes the surface size when being resized.
			// tags:
			//		private
			var s = this.olLayer.map.getSize();
			this._surface.setDimensions(s.w, s.h);
		},

		moveTo: function(event){
			// summary:
			//		Called when this layer is moved or zoomed.
			// event:
			//		The event
			var s = style.get(this.olLayer.map.layerContainerDiv);
			var left = parseInt(s.left);
			var top = parseInt(s.top);

			if(event.zoomChanged || left || top){
				var d = this.olLayer.div;

				style.set(d, {
					left: -left + "px",
					top: -top + "px"
				});

				if(this._features == null){
					return;
				}
				var vp = this.getViewport();

				vp.setTransform(matrix.translate(left, top));

				this.inherited(arguments);

			}
		},

		added: function(){
			// summary:
			//		Called when added to a map.
			this.inherited(arguments);
			this._surfaceSize();
		}

	});
});

},
'dojox/geo/openlayers/Collection':function(){
define([
	"dojo/_base/declare",
	"./Geometry"
], function(declare, Geometry){

	return declare("dojox.geo.openlayers.Collection", Geometry, {
		// summary:
		//		A collection of geometries. 

		// coordinates: Array
		//		An array of geometries.
		coordinates:null,

		setGeometries: function(g){
			// summary:
			//		Sets the geometries
			// g: Array
			//		The array of geometries.
			this.coordinates = g;
		},

		getGeometries: function(){
			// summary:
			//		Returns the geometries.
			// returns:
			//		The array of geometries defining this collection.
			return this.coordinates; // Array
		}
	});
});

},
'dojox/geo/openlayers/Geometry':function(){
define([
	"dojo/_base/declare"
], function(declare){

	return declare("dojox.geo.openlayers.Geometry", null, {
		// summary:
		//		A Geometry handles description of shapes to be rendered in a GfxLayer
		//		using a GeometryFeature feature.
		//		A Geometry can be:
		//
		//		- A point geometry of type dojox.geo.openlayers.Point. Coordinates are a an 
		//		Object {x, y}
		//		- A line string geometry of type dojox.geo.openlayers.LineString. Coordinates are
		//		an array of {x, y} objects
		//		- A collection geometry of type dojox.geo.openlayers.Collection. Coordinates are an array of geometries.

		// coordinates: Object|Array
		//		The coordinates of the geometry, Object like {x, y} or Array.
		coordinates : null,

		// shape: [private] dojox/gfx/shape.Shape
		//		The associated shape when rendered
		shape: null,

		constructor: function(coords){
			// summary:
			//		Constructs a new geometry
			// coords: Object
			//		Coordinates of the geometry. {x:``x``, y:``y``} object for a point geometry, array of {x:``x``, y:``y``}
			//		objects for line string geometry, array of geometries for collection geometry.
			this.coordinates = coords;
		}
	});
});

},
'dojox/geo/openlayers/GeometryFeature':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/array",
	"dojo/_base/lang",
	"dojox/gfx/matrix",
	"./Point",
	"./LineString",
	"./Collection",
	"./Feature"
], function(declare, array, lang, matrix, Point, LineString, Collection, Feature){

	return declare("dojox.geo.openlayers.GeometryFeature", Feature, {
		// summary:
		//		A Feature encapsulating a geometry.
		// description:
		//		This Feature renders a geometry such as a Point or LineString geometry. This Feature
		//		is responsible for reprojecting the geometry before creating a gfx shape to display it.
		//		By default the shape created is a circle for a Point geometry and a polyline for a 
		//		LineString geometry. User can change these behavior by overriding the createShape 
		//		method to create the desired shape.
		// example:
		//	|  var geom = new dojox.geo.openlayers.Point({x:0, y:0});
		//	|  var gf = new dojox.geo.openlayers.GeometryFeature(geom);

		constructor: function(geometry){
			// summary:
			//		Constructs a GeometryFeature for the specified geometry.
			// geometry: dojox/geo/openlayers/Geometry
			//		The geometry to render.
			this._geometry = geometry;
			this._shapeProperties = {};
			this._fill = null;
			this._stroke = null;
		},

		_createCollection: function(/* dojox/geo/openlayers/Geometry */g){
			// summary:
			//		Create collection shape and add it to the viewport.
			// tags:
			//		private
			var layer = this.getLayer();
			var s = layer.getSurface();
			var c = this.createShape(s, g);
			var vp = layer.getViewport();
			vp.add(c);
			return c;
		},

		_getCollectionShape: function(/* dojox/geo/openlayers/Geometry */g){
			// summary:
			//		Get the collection shape, create it if necessary
			// tags:
			//		private
			var s = g.shape;
			if(s == null){
				s = this._createCollection(g);
				g.shape = s;
			}
			return s;
		},

		renderCollection: function(g){
			// summary:
			//		Renders a geometry collection.
			// g: dojox.geo.openlayers.Geometry?
			//		The geometry to render.
			if(g == undefined){
				g = this._geometry;
			}

			s = this._getCollectionShape(g);
			var prop = this.getShapeProperties();
			s.setShape(prop);

			array.forEach(g.coordinates, function(item){
				if(item instanceof Point){
					this.renderPoint(item);
				}else if(item instanceof LineString){
					this.renderLineString(item);
				}else if(item instanceof Collection){
					this.renderCollection(item);
				}else{
					throw new Error();
				}
			}, this);
			this._applyStyle(g);
		},

		render: function(g){
			// summary:
			//		Render a geometry. 
			//		Called by the Layer on which the feature is added. 
			// g: dojox/geo/openlayer/Geometry?
			//		The geometry to draw
			if(g == undefined){
				g = this._geometry;
			}

			if(g instanceof Point){
				this.renderPoint(g);
			}else if(g instanceof LineString){
				this.renderLineString(g);
			}else if(g instanceof Collection){
				this.renderCollection(g);
			}else{
				throw new Error();
			}
		},

		getShapeProperties: function(){
			// summary:
			//		Returns the shape properties. 
			// returns: Object
			//		The shape properties.
			return this._shapeProperties;
		},

		setShapeProperties: function(s){
			// summary:
			//		Sets the shape properties. 
			// s: Object
			//		The shape properties to set.
			this._shapeProperties = s;
			return this;
		},

		createShape: function(s, g){
			// summary:
			//		Called when the shape rendering the geometry has to be created.
			//		This default implementation creates a circle for a point geometry, a polyline for
			//		a LineString geometry and is recursively called when creating a collection.
			//		User may replace this method to produce a custom shape.
			// s: dojox/gfx/Surface
			//		The surface on which the method create the shapes.
			// g: dojox/geo/openlayers/Geometry?
			//		The reference geometry 
			// returns:
			//		The resulting shape.
			if(!g){
				g = this._geometry;
			}

			var shape = null;
			if(g instanceof Point){
				shape = s.createCircle();
			}else if(g instanceof LineString){
				shape = s.createPolyline();
			}else if(g instanceof Collection){
				var grp = s.createGroup();
				array.forEach(g.coordinates, function(item){
					var shp = this.createShape(s, item);
					grp.add(shp);
				}, this);
				shape = grp;
			}else{
				throw new Error();
			}
			return shape;
		},

		getShape: function(){
			// summary:
			//		Returns the shape rendering the geometry
			// returns:
			//		The shape used to render the geometry.
			var g = this._geometry;
			if(!g){
				return null;
			}
			if(g.shape){
				return g.shape;
			}
			this.render();
			return g.shape; // dojox.gfx.shape.Shape
		},

		_createPoint: function(/* dojox/geo/openlayer/Geometry */g){
			// summary:
			//		Create a point shape
			// tags:
			//		private
			var layer = this.getLayer();
			var s = layer.getSurface();
			var c = this.createShape(s, g);
			var vp = layer.getViewport();
			vp.add(c);
			return c;
		},

		_getPointShape: function(/* dojox/geo/openlayers/Geometry */g){
			// summary:
			//		get the point geometry shape, create it if necessary
			// tags:
			//		private
			var s = g.shape;
			if(s == null){
				s = this._createPoint(g);
				g.shape = s;
			}
			return s;
		},

		renderPoint: function(g){
			// summary:
			//		Renders a point geometry.
			// g: dojox/geo/openlayers/Point?
			//		The geometry to render, or the current instance geometry if not specified.
			if(g == undefined){
				g = this._geometry;
			}
			var layer = this.getLayer();
			var map = layer.getDojoMap();

			s = this._getPointShape(g);
			var prop = lang.mixin({}, this._defaults.pointShape);
			prop = lang.mixin(prop, this.getShapeProperties());
			s.setShape(prop);

			var from = this.getCoordinateSystem();
			var p = map.transform(g.coordinates, from);

			var a = this._getLocalXY(p);
			var cx = a[0];
			var cy = a[1];
			var tr = layer.getViewport().getTransform();
			if(tr){
				s.setTransform(matrix.translate(cx - tr.dx, cy - tr.dy));
			}
			this._applyStyle(g);
		},

		_createLineString: function(/* dojox/geo/openlayers/Geometry */g){
			// summary:
			//		Create polyline shape and add it to the viewport.
			// tags:
			//		private
			var layer = this.getLayer();
			var s = layer._surface;
			var shape = this.createShape(s, g);
			var vp = layer.getViewport();
			vp.add(shape);
			g.shape = shape;
			return shape;
		},

		_getLineStringShape: function(/* dojox/geo/openlayers/Geometry */g){
			// summary:
			//		Get the line string geometry shape, create it if necessary
			// tags:
			//		private
			var s = g.shape;
			if(s == null){
				s = this._createLineString(g);
				g.shape = s;
			}
			return s;
		},

		renderLineString: function(g){
			// summary:
			//		Renders a line string geometry.
			// g: dojox/geo/openlayers/Geometry?
			//		The geometry to render.
			if(g == undefined){
				g = this._geometry;
			}
			var layer = this.getLayer();
			var map = layer.getDojoMap();
			var lss = this._getLineStringShape(g);
			var from = this.getCoordinateSystem();
			var points = new Array(g.coordinates.length); // ss.getShape().points;		
			var tr = layer.getViewport().getTransform();
			array.forEach(g.coordinates, function(c, i, array){
				var p = map.transform(c, from);
				var a = this._getLocalXY(p);
				if(tr){
					a[0] -= tr.dx;
					a[1] -= tr.dy;
				}
				points[i] = {
					x: a[0],
					y: a[1]
				};
			}, this);
			var prop = lang.mixin({}, this._defaults.lineStringShape);
			prop = lang.mixin(prop, this.getShapeProperties());
			prop = lang.mixin(prop, {
				points: points
			});
			lss.setShape(prop);
			this._applyStyle(g);
		},

		_applyStyle: function(g){
			// summary:
			//		Apply the style on the geometry's shape.
			// g: Geometry
			//		The geometry.
			// tags:
			//		private
			if(!g || !g.shape){
				return;
			}

			var f = this.getFill();

			var fill;
			if(!f || lang.isString(f) || lang.isArray(f)){
				fill = f;
			}else{
				fill = lang.mixin({}, this._defaults.fill);
				fill = lang.mixin(fill, f);
			}

			var s = this.getStroke();
			var stroke;
			if(!s || lang.isString(s) || lang.isArray(s)){
				stroke = s;
			}else{
				stroke = lang.mixin({}, this._defaults.stroke);
				stroke = lang.mixin(stroke, s);
			}

			this._applyRecusiveStyle(g, stroke, fill);
		},

		_applyRecusiveStyle: function(g, stroke, fill){
			// summary:
			//		Apply the style on the geometry's shape recursively.
			// g: dojox.geo.openlayers.Geometry
			//		The geometry.
			// stroke: Object
			//		The stroke
			// fill:Object
			//		The fill
			// tags:
			//		private
			var shp = g.shape;

			if(shp.setFill){
				shp.setFill(fill);
			}

			if(shp.setStroke){
				shp.setStroke(stroke);
			}

			if(g instanceof Collection){
				array.forEach(g.coordinates, function(i){
					this._applyRecusiveStyle(i, stroke, fill);
				}, this);
			}
		},

		setStroke: function(s){
			// summary:
			//		Set the stroke style to be applied on the rendered shape.
			// s: Object
			//		The stroke style
			this._stroke = s;
			return this;
		},

		getStroke: function(){
			// summary:
			//		Returns the stroke style
			// returns:
			//		The stroke style
			return this._stroke;
		},

		setFill: function(f){
			// summary:
			//		Set the fill style to be applied on the rendered shape.
			// f: Object
			//		The fill style
			this._fill = f;
			return this;
		},

		getFill: function(){
			// summary:
			//		Returns the fill style
			// returns:
			//		The fill style
			return this._fill;
		},

		remove: function(){
			// summary:
			//		Removes the shape from the Surface. 
			//		Called when the feature is removed from the layer.
			var g = this._geometry;
			var shp = g.shape;
			g.shape = null;
			if(shp){
				shp.removeShape();
			}
			if(g instanceof Collection){
				array.forEach(g.coordinates, function(i){
					this.remove(i);
				}, this);
			}
		},

		_defaults: {
			fill: null,
			stroke: null,
			pointShape: {
				r: 30
			},
			lineStringShape: null
		}

	});
});

},
'dojox/geo/openlayers/Point':function(){
define([
	"dojo/_base/declare",
	"./Geometry"
], function(declare, Geometry){

	return declare("dojox.geo.openlayers.Point", Geometry, {
		// summary:
		//		A Point geometry handles description of points to be rendered in a GfxLayer

		setPoint: function(p){
			// summary:
			//		Sets the point for this geometry.
			// p: Object
			//		The point geometry expressed as a {x, y} object.
			this.coordinates = p;
		},

		getPoint: function(){
			// summary:
			//		Gets the point defining this geometry.
			// returns:
			//		The point defining this geometry.
			return this.coordinates; // Object
		}
	});
});

},
'dojox/geo/openlayers/LineString':function(){
define([
	"dojo/_base/declare",
	"./Geometry"
], function(declare, Geometry){

	return declare("dojox.geo.openlayers.LineString", Geometry, {
		// summary:
		//		The `dojox.geo.openlayers.LineString` geometry. This geometry holds an array
		//		of coordinates.

		setPoints: function(p){
			// summary:
			//		Sets the points for this geometry.
			// p: Object[]
			//		An array of {x, y} objects
			this.coordinates = p;
		},

		getPoints: function(){
			// summary:
			//		Gets the points of this geometry.
			// returns:
			//		The points of this geometry.
			return this.coordinates; // Object[]
		}

	});
});

},
'dojox/geo/openlayers/JsonImport':function(){
define([
	"dojo/_base/declare",
	"dojo/_base/xhr",
	"dojo/_base/lang",
	"dojo/_base/array",
	"./LineString",
	"./Collection",
	"./GeometryFeature"
], function(declare, xhr, lang, array, LineString, Collection, GeometryFeature){

	/*=====
	dojox.geo.openlayers.__JsonImportArgs = {
		// summary:
		//		The keyword arguments that can be passed in a JsonImport constructor.
		// url: String
		//		The url pointing to the JSON file to load.
		// nextFeature: function
		//		The function called each time a feature is read. The function is called with a GeometryFeature as argument.
		// error: function
		//		Error callback called if something fails.
	};
	=====*/

	return declare("dojox.geo.openlayers.JsonImport", null, {
		// summary:
		//		Class to load JSON formated ShapeFile as output of the JSon Custom Map Converter.
		// description:
		//		This class loads JSON formated ShapeFile produced by the JSon Custom Map Converter.
		//		When loading the JSON file, it calls a iterator function each time a feature is read.
		//		This iterator function is provided as parameter to the constructor.
		//
		constructor : function(params){
			// summary:
			//		Construct a new JSON importer.
			// params: dojox.geo.openlayers.__JsonImportArgs
			//		The parameters to initialize this JsonImport instance.
			this._params = params;
		},

		loadData: function(){
			// summary:
			//		Triggers the loading.
			var p = this._params;
			xhr.get({
				url: p.url,
				handleAs: "json",
				sync: true,
				load: lang.hitch(this, this._gotData),
				error: lang.hitch(this, this._loadError)
			});
		},

		_gotData: function(/* Object */items){
			// summary:
			//		Called when loading is complete.
			// tags:
			//		private
			var nf = this._params.nextFeature;
			if(!lang.isFunction(nf)){
				return;
			}

			var extent = items.layerExtent;
			var ulx = extent[0];
			var uly = extent[1];
			var lrx = ulx + extent[2];
			var lry = uly + extent[3];

			var extentLL = items.layerExtentLL;
			var x1 = extentLL[0];
			var y1 = extentLL[1];
			var x2 = x1 + extentLL[2];
			var y2 = y1 + extentLL[3];

			var ulxLL = x1;
			var ulyLL = y2;
			var lrxLL = x2;
			var lryLL = y1;

			var features = items.features;

			for( var f in features){
				var o = features[f];
				var s = o["shape"];
				var gf = null;
				if(lang.isArray(s[0])){

					var a = new Array();
					array.forEach(s, function(item){
						var ls = this._makeGeometry(item, ulx, uly, lrx, lry, ulxLL, ulyLL, lrxLL, lryLL);
						a.push(ls);
					}, this);
					var g = new Collection(a);
					gf = new GeometryFeature(g);
					nf.call(this, gf);

				}else{
					gf = this._makeFeature(s, ulx, uly, lrx, lry, ulxLL, ulyLL, lrxLL, lryLL);
					nf.call(this, gf);
				}
			}
			var complete = this._params.complete;
			if(lang.isFunction(complete)){
				complete.call(this, complete);
			}
		},

		_makeGeometry: function(/* Array */s, /* Float */ulx, /* Float */uly, /* Float */lrx, /* Float */
		lry, /* Float */ulxLL, /* Float */ulyLL, /* Float */lrxLL, /* Float */lryLL){
			// summary:
			//		Make a geometry with the specified points.
			// tags:
			//		private
			var a = [];
			var k = 0.0;
			for( var i = 0; i < s.length - 1; i += 2){
				var x = s[i];
				var y = s[i + 1];

				k = (x - ulx) / (lrx - ulx);
				var px = k * (lrxLL - ulxLL) + ulxLL;

				k = (y - uly) / (lry - uly);
				var py = k * (lryLL - ulyLL) + ulyLL;

				a.push({
					x: px,
					y: py
				});

			}
			var ls = new LineString(a);
			return ls; // LineString
		},

		_makeFeature: function(/* Array */s, /* Float */ulx, /* Float */uly, /* Float */lrx, /* Float */
		lry, /* Float */ulxLL, /* Float */ulyLL, /* Float */lrxLL, /* Float */lryLL){
			// summary:
			//		Make a GeometryFeature with the specified points.
			// tags:
			//		private
			var ls = this._makeGeometry(s, ulx, uly, lrx, lry, ulxLL, ulyLL, lrxLL, lryLL);
			var gf = new GeometryFeature(ls);
			return gf;
		},

		_loadError: function(){
			// summary:
			//		Called when an error occurs. Calls the error function is provided in the parameters.
			// tags:
			//		private
			var f = this._params.error;
			if(lang.isFunction(f)){
				f.apply(this, parameters);
			}
		}
	});
});

},
'dojox/geo/openlayers/WidgetFeature':function(){
define([
	"dojo/_base/declare",
	"dojo/dom-style",
	"dojo/_base/lang",
	"dijit/registry",
	"./Feature"
], function(declare, style, lang, registry, Feature){
	/*=====
	dojox.geo.openlayers.__WidgetFeatureArgs = {
		// summary:
		//		The keyword arguments that can be passed in a WidgetFeature constructor.
		//		You must define a least one widget retrieval parameter and the geo-localization parameters.
		// createWidget: Function?
		//		Function for widget creation. Must return a `dijit._Widget.
		// dojoType: String?
		//		The class of a widget to create.
		// dijitId: String?
		//		The digitId of an existing widget.
		// widget: dijit._Widget?
		//		An already created widget.
		// width: Number?
		//		The width of the widget.
		// height: Number?
		//		The height of the widget.
		// longitude: Number
		//		The longitude, in decimal degrees where to place the widget.
		// latitude: Number
		//		The latitude, in decimal degrees where to place the widget.
	};
	=====*/

		return declare("dojox.geo.openlayers.WidgetFeature", Feature, {
			// summary:
			//		Wraps a Dojo widget, provide geolocalisation of the widget and interface
			//		to Layer class.
			// description:
			//		This class allows to add a widget in a `dojox.geo.openlayers.Layer`.

			_widget: null,
			_bbox: null,

			constructor: function(params){
				// summary:
				//		Constructs a new `dojox.geo.openlayers.WidgetFeature`
				// params: dojox.geo.openlayers.__WidgetFeatureArgs
				//		The parameters describing the widget.
				this._params = params;
			},

			setParameters: function(params){
				// summary:
				//		Sets the parameters describing the widget.
				// params: dojox.geo.openlayers.__WidgetFeatureArgs
				//		The parameters describing the widget.
				this._params = params;
			},

			getParameters: function(){
				// summary:
				//		Returns the parameters describing the widget.
				// returns: dojox.geo.openlayers.__WidgetFeatureArgs
				//		The parameters describing the widget.
				return this._params;
			},

			_getWidget: function(){
				// summary:
				//		Creates, if necessary the widget and returns it
				// tags:
				//		private
				var params = this._params;

				if((this._widget == null) && (params != null)){
					var w = null;

					if(typeof (params.createWidget) == "function"){
						w = params.createWidget.call(this);
					}else if(params.dojoType){
						dojo["require"](params.dojoType);
						var c = lang.getObject(params.dojoType);
						w = new c(params);
					}else if(params.dijitId){
						w = registry.byId(params.dijitId);
					}else if(params.widget){
						w = params.widget;
					}

					if(w != null){
						this._widget = w;
						if(typeof (w.startup) == "function"){
							w.startup();
						}
						var n = w.domNode;
						if(n != null){
							style.set(n, {
								position: "absolute"
							});
						}
					}
					this._widget = w;
				}
				return this._widget;
			},

			_getWidgetWidth: function(){
				// summary:
				//		gets the widget width
				// tags:
				//		private
				var p = this._params;
				if(p.width){
					return p.width;
				}
				var w = this._getWidget();
				if(w){
					return style.get(w.domNode, "width");
				}
				return 10;
			},

			_getWidgetHeight: function(){
				// summary:
				//		gets the widget height
				// tags:
				//		private
				var p = this._params;
				if(p.height){
					return p.height;
				}
				var w = this._getWidget();
				if(w){
					return style.get(w.domNode, "height");
				}
				return 10;
			},

			render: function(){
				// summary:
				//		renders the widget.
				// description:
				//		Places the widget accordingly to longitude and latitude defined in parameters.
				//		This function is called when the center of the maps or zoom factor changes.
				var layer = this.getLayer();

				var widget = this._getWidget();
				if(widget == null){
					return;
				}
				var params = this._params;
				var lon = params.longitude;
				var lat = params.latitude;
				var from = this.getCoordinateSystem();
				var map = layer.getDojoMap();
				var p = map.transformXY(lon, lat, from);
				var a = this._getLocalXY(p);

				var width = this._getWidgetWidth();
				var height = this._getWidgetHeight();

				var x = a[0] - width / 2;
				var y = a[1] - height / 2;
				var dom = widget.domNode;

				var pa = layer.olLayer.div;
				if(dom.parentNode != pa){
					if(dom.parentNode){
						dom.parentNode.removeChild(dom);
					}
					pa.appendChild(dom);
				}
				this._updateWidgetPosition({
					x: x,
					y: y,
					width: width,
					height: height
				});
			},

			_updateWidgetPosition: function(box){
				// summary:
				//		Places the widget with the computed x and y values
				// tags:
				//		private
				
				// var box = this._params;

				var w = this._widget;
				var dom = w.domNode;

				style.set(dom, {
					position: "absolute",
					left: box.x + "px",
					top: box.y + "px",
					width: box.width + "px",
					height: box.height + "px"
				});

				if(w.srcNodeRef){
					style.set(w.srcNodeRef, {
						position: "absolute",
						left: box.x + "px",
						top: box.y + "px",
						width: box.width + "px",
						height: box.height + "px"
					});
				}

				if(lang.isFunction(w.resize)){
					w.resize({
						w: box.width,
						h: box.height
					});
				}
			},

			remove: function(){
				// summary:
				//		removes this feature.
				// description:
				//		Remove this feature by disconnecting the widget from the dom.
				var w = this._getWidget();
				if(!w){
					return;
				}
				var dom = w.domNode;
				if(dom.parentNode){
					dom.parentNode.removeChild(dom);
				}
			}
		});
	});

},
'dojox/gfx/svg':function(){
define(["dojo/_base/lang", "dojo/_base/sniff", "dojo/_base/window", "dojo/dom", "dojo/_base/declare", "dojo/_base/array",
  "dojo/dom-geometry", "dojo/dom-attr", "dojo/_base/Color", "./_base", "./shape", "./path"],
function(lang, has, win, dom, declare, arr, domGeom, domAttr, Color, g, gs, pathLib){

	var svg = g.svg = {
		// summary:
		//		This the graphics rendering bridge for browsers compliant with W3C SVG1.0.
		//		This is the preferred renderer to use for interactive and accessible graphics.
	};
	svg.useSvgWeb = (typeof window.svgweb != "undefined");

	// Need to detect iOS in order to workaround bug when
	// touching nodes with text
	var uagent = navigator.userAgent,
		safMobile = has("ios"),
		android = has("android"),
		textRenderingFix = has("chrome") || (android && android>=4) ? "auto" : "optimizeLegibility";// #16099, #16461

	function _createElementNS(ns, nodeType){
		// summary:
		//		Internal helper to deal with creating elements that
		//		are namespaced.  Mainly to get SVG markup output
		//		working on IE.
		if(win.doc.createElementNS){
			return win.doc.createElementNS(ns,nodeType);
		}else{
			return win.doc.createElement(nodeType);
		}
	}
	
	function _setAttributeNS(node, ns, attr, value){
		if(node.setAttributeNS){
			return node.setAttributeNS(ns, attr, value);
		}else{
			return node.setAttribute(attr, value);
		}
	}

	function _createTextNode(text){
		if(svg.useSvgWeb){
			return win.doc.createTextNode(text, true);
		}else{
			return win.doc.createTextNode(text);
		}
	}

	function _createFragment(){
		if(svg.useSvgWeb){
			return win.doc.createDocumentFragment(true);
		}else{
			return win.doc.createDocumentFragment();
		}
	}

	svg.xmlns = {
		xlink: "http://www.w3.org/1999/xlink",
		svg:   "http://www.w3.org/2000/svg"
	};

	svg.getRef = function(name){
		// summary:
		//		looks up a node by its external name
		// name: String
		//		an SVG external reference
		// returns:
		//      returns a DOM Node specified by the name argument or null
		if(!name || name == "none") return null;
		if(name.match(/^url\(#.+\)$/)){
			return dom.byId(name.slice(5, -1));	// Node
		}
		// alternative representation of a reference
		if(name.match(/^#dojoUnique\d+$/)){
			// we assume here that a reference was generated by dojox/gfx
			return dom.byId(name.slice(1));	// Node
		}
		return null;	// Node
	};

	svg.dasharray = {
		solid:				"none",
		shortdash:			[4, 1],
		shortdot:			[1, 1],
		shortdashdot:		[4, 1, 1, 1],
		shortdashdotdot:	[4, 1, 1, 1, 1, 1],
		dot:				[1, 3],
		dash:				[4, 3],
		longdash:			[8, 3],
		dashdot:			[4, 3, 1, 3],
		longdashdot:		[8, 3, 1, 3],
		longdashdotdot:		[8, 3, 1, 3, 1, 3]
	};

	var clipCount = 0;

	svg.Shape = declare("dojox.gfx.svg.Shape", gs.Shape, {
		// summary:
		//		SVG-specific implementation of dojox/gfx/shape.Shape methods

		destroy: function(){
			if(this.fillStyle && "type" in this.fillStyle){
				var fill = this.rawNode.getAttribute("fill"),
					ref  = svg.getRef(fill);
				if(ref){
					ref.parentNode.removeChild(ref);
				}
			}
			if(this.clip){
				var clipPathProp = this.rawNode.getAttribute("clip-path");
				if(clipPathProp){
					var clipNode = dom.byId(clipPathProp.match(/gfx_clip[\d]+/)[0]);
					if(clipNode){ clipNode.parentNode.removeChild(clipNode); }
				}
			}
			gs.Shape.prototype.destroy.apply(this, arguments);
		},

		setFill: function(fill){
			// summary:
			//		sets a fill object (SVG)
			// fill: Object
			//		a fill object
			//		(see dojox/gfx.defaultLinearGradient,
			//		dojox/gfx.defaultRadialGradient,
			//		dojox/gfx.defaultPattern,
			//		or dojo/_base/Color)

			if(!fill){
				// don't fill
				this.fillStyle = null;
				this.rawNode.setAttribute("fill", "none");
				this.rawNode.setAttribute("fill-opacity", 0);
				return this;
			}
			var f;
			// FIXME: slightly magical. We're using the outer scope's "f", but setting it later
			var setter = function(x){
					// we assume that we're executing in the scope of the node to mutate
					this.setAttribute(x, f[x].toFixed(8));
				};
			if(typeof(fill) == "object" && "type" in fill){
				// gradient
				switch(fill.type){
					case "linear":
						f = g.makeParameters(g.defaultLinearGradient, fill);
						var gradient = this._setFillObject(f, "linearGradient");
						arr.forEach(["x1", "y1", "x2", "y2"], setter, gradient);
						break;
					case "radial":
						f = g.makeParameters(g.defaultRadialGradient, fill);
						var grad = this._setFillObject(f, "radialGradient");
						arr.forEach(["cx", "cy", "r"], setter, grad);
						break;
					case "pattern":
						f = g.makeParameters(g.defaultPattern, fill);
						var pattern = this._setFillObject(f, "pattern");
						arr.forEach(["x", "y", "width", "height"], setter, pattern);
						break;
				}
				this.fillStyle = f;
				return this;
			}
			// color object
			f = g.normalizeColor(fill);
			this.fillStyle = f;
			this.rawNode.setAttribute("fill", f.toCss());
			this.rawNode.setAttribute("fill-opacity", f.a);
			this.rawNode.setAttribute("fill-rule", "evenodd");
			return this;	// self
		},

		setStroke: function(stroke){
			// summary:
			//		sets a stroke object (SVG)
			// stroke: Object
			//		a stroke object (see dojox/gfx.defaultStroke)

			var rn = this.rawNode;
			if(!stroke){
				// don't stroke
				this.strokeStyle = null;
				rn.setAttribute("stroke", "none");
				rn.setAttribute("stroke-opacity", 0);
				return this;
			}
			// normalize the stroke
			if(typeof stroke == "string" || lang.isArray(stroke) || stroke instanceof Color){
				stroke = { color: stroke };
			}
			var s = this.strokeStyle = g.makeParameters(g.defaultStroke, stroke);
			s.color = g.normalizeColor(s.color);
			// generate attributes
			if(s){
				var w = s.width < 0 ? 0 : s.width;
				rn.setAttribute("stroke", s.color.toCss());
				rn.setAttribute("stroke-opacity", s.color.a);
				rn.setAttribute("stroke-width",   w);
				rn.setAttribute("stroke-linecap", s.cap);
				if(typeof s.join == "number"){
					rn.setAttribute("stroke-linejoin",   "miter");
					rn.setAttribute("stroke-miterlimit", s.join);
				}else{
					rn.setAttribute("stroke-linejoin",   s.join);
				}
				var da = s.style.toLowerCase();
				if(da in svg.dasharray){
					da = svg.dasharray[da];
				}
				if(da instanceof Array){
					da = lang._toArray(da);
					var i;
					for(i = 0; i < da.length; ++i){
						da[i] *= w;
					}
					if(s.cap != "butt"){
						for(i = 0; i < da.length; i += 2){
							da[i] -= w;
							if(da[i] < 1){ da[i] = 1; }
						}
						for(i = 1; i < da.length; i += 2){
							da[i] += w;
						}
					}
					da = da.join(",");
				}
				rn.setAttribute("stroke-dasharray", da);
				rn.setAttribute("dojoGfxStrokeStyle", s.style);
			}
			return this;	// self
		},

		_getParentSurface: function(){
			var surface = this.parent;
			for(; surface && !(surface instanceof g.Surface); surface = surface.parent);
			return surface;
		},

		_setFillObject: function(f, nodeType){
			var svgns = svg.xmlns.svg;
			this.fillStyle = f;
			var surface = this._getParentSurface(),
				defs = surface.defNode,
				fill = this.rawNode.getAttribute("fill"),
				ref  = svg.getRef(fill);
			if(ref){
				fill = ref;
				if(fill.tagName.toLowerCase() != nodeType.toLowerCase()){
					var id = fill.id;
					fill.parentNode.removeChild(fill);
					fill = _createElementNS(svgns, nodeType);
					fill.setAttribute("id", id);
					defs.appendChild(fill);
				}else{
					while(fill.childNodes.length){
						fill.removeChild(fill.lastChild);
					}
				}
			}else{
				fill = _createElementNS(svgns, nodeType);
				fill.setAttribute("id", g._base._getUniqueId());
				defs.appendChild(fill);
			}
			if(nodeType == "pattern"){
				fill.setAttribute("patternUnits", "userSpaceOnUse");
				var img = _createElementNS(svgns, "image");
				img.setAttribute("x", 0);
				img.setAttribute("y", 0);
				img.setAttribute("width",  (f.width < 0 ? 0 : f.width).toFixed(8));
				img.setAttribute("height", (f.height < 0 ? 0 : f.height).toFixed(8));
				_setAttributeNS(img, svg.xmlns.xlink, "xlink:href", f.src);
				fill.appendChild(img);
			}else{
				fill.setAttribute("gradientUnits", "userSpaceOnUse");
				for(var i = 0; i < f.colors.length; ++i){
					var c = f.colors[i], t = _createElementNS(svgns, "stop"),
						cc = c.color = g.normalizeColor(c.color);
					t.setAttribute("offset",       c.offset.toFixed(8));
					t.setAttribute("stop-color",   cc.toCss());
					t.setAttribute("stop-opacity", cc.a);
					fill.appendChild(t);
				}
			}
			this.rawNode.setAttribute("fill", "url(#" + fill.getAttribute("id") +")");
			this.rawNode.removeAttribute("fill-opacity");
			this.rawNode.setAttribute("fill-rule", "evenodd");
			return fill;
		},

		_applyTransform: function() {
			var matrix = this.matrix;
			if(matrix){
				var tm = this.matrix;
				this.rawNode.setAttribute("transform", "matrix(" +
					tm.xx.toFixed(8) + "," + tm.yx.toFixed(8) + "," +
					tm.xy.toFixed(8) + "," + tm.yy.toFixed(8) + "," +
					tm.dx.toFixed(8) + "," + tm.dy.toFixed(8) + ")");
			}else{
				this.rawNode.removeAttribute("transform");
			}
			return this;
		},

		setRawNode: function(rawNode){
			// summary:
			//		assigns and clears the underlying node that will represent this
			//		shape. Once set, transforms, gradients, etc, can be applied.
			//		(no fill & stroke by default)
			var r = this.rawNode = rawNode;
			if(this.shape.type!="image"){
				r.setAttribute("fill", "none");
			}
			r.setAttribute("fill-opacity", 0);
			r.setAttribute("stroke", "none");
			r.setAttribute("stroke-opacity", 0);
			r.setAttribute("stroke-width", 1);
			r.setAttribute("stroke-linecap", "butt");
			r.setAttribute("stroke-linejoin", "miter");
			r.setAttribute("stroke-miterlimit", 4);
			// Bind GFX object with SVG node for ease of retrieval - that is to
			// save code/performance to keep this association elsewhere
			r.__gfxObject__ = this;
		},

		setShape: function(newShape){
			// summary:
			//		sets a shape object (SVG)
			// newShape: Object
			//		a shape object
			//		(see dojox/gfx.defaultPath,
			//		dojox/gfx.defaultPolyline,
			//		dojox/gfx.defaultRect,
			//		dojox/gfx.defaultEllipse,
			//		dojox/gfx.defaultCircle,
			//		dojox/gfx.defaultLine,
			//		or dojox/gfx.defaultImage)
			this.shape = g.makeParameters(this.shape, newShape);
			for(var i in this.shape){
				if(i != "type"){
					var v = this.shape[i];
					if(i === "width" || i === "height"){
						v = v < 0 ? 0 : v;
					}
					this.rawNode.setAttribute(i, v);
				}
			}
			this.bbox = null;
			return this;	// self
		},

		// move family

		_moveToFront: function(){
			// summary:
			//		moves a shape to front of its parent's list of shapes (SVG)
			this.rawNode.parentNode.appendChild(this.rawNode);
			return this;	// self
		},
		_moveToBack: function(){
			// summary:
			//		moves a shape to back of its parent's list of shapes (SVG)
			this.rawNode.parentNode.insertBefore(this.rawNode, this.rawNode.parentNode.firstChild);
			return this;	// self
		},
		setClip: function(clip){
			// summary:
			//		sets the clipping area of this shape.
			// description:
			//		This method overrides the dojox/gfx/shape.Shape.setClip() method.
			// clip: Object
			//		an object that defines the clipping geometry, or null to remove clip.
			this.inherited(arguments);
			var clipType = clip ? "width" in clip ? "rect" : 
							"cx" in clip ? "ellipse" : 
							"points" in clip ? "polyline" : "d" in clip ? "path" : null : null;
			if(clip && !clipType){
				return this;
			}
			if(clipType === "polyline"){
				clip = lang.clone(clip);
				clip.points = clip.points.join(",");
			}
			var clipNode, clipShape,
				clipPathProp = domAttr.get(this.rawNode, "clip-path");
			if(clipPathProp){
				clipNode = dom.byId(clipPathProp.match(/gfx_clip[\d]+/)[0]);
				if(clipNode){ // may be null if not in the DOM anymore
					clipNode.removeChild(clipNode.childNodes[0]);
				}
			}
			if(clip){
				if(clipNode){
					clipShape = _createElementNS(svg.xmlns.svg, clipType);
					clipNode.appendChild(clipShape);
				}else{
					var idIndex = ++clipCount;
					var clipId = "gfx_clip" + idIndex;
					var clipUrl = "url(#" + clipId + ")";
					this.rawNode.setAttribute("clip-path", clipUrl);
					clipNode = _createElementNS(svg.xmlns.svg, "clipPath");
					clipShape = _createElementNS(svg.xmlns.svg, clipType);
					clipNode.appendChild(clipShape);
					this.rawNode.parentNode.insertBefore(clipNode, this.rawNode);
					domAttr.set(clipNode, "id", clipId);
				}
				domAttr.set(clipShape, clip);
			}else{
				//remove clip-path
				this.rawNode.removeAttribute("clip-path");
				if(clipNode){
					clipNode.parentNode.removeChild(clipNode);
				}
			}
			return this;
		},
		_removeClipNode: function(){
			var clipNode, clipPathProp = domAttr.get(this.rawNode, "clip-path");
			if(clipPathProp){
				clipNode = dom.byId(clipPathProp.match(/gfx_clip[\d]+/)[0]);
				if(clipNode){
					clipNode.parentNode.removeChild(clipNode);
				}
			}
			return clipNode;
		}
	});


	svg.Group = declare("dojox.gfx.svg.Group", svg.Shape, {
		// summary:
		//		a group shape (SVG), which can be used
		//		to logically group shapes (e.g, to propagate matricies)
		constructor: function(){
			gs.Container._init.call(this);
		},
		setRawNode: function(rawNode){
			// summary:
			//		sets a raw SVG node to be used by this shape
			// rawNode: Node
			//		an SVG node
			this.rawNode = rawNode;
			// Bind GFX object with SVG node for ease of retrieval - that is to
			// save code/performance to keep this association elsewhere
			this.rawNode.__gfxObject__ = this;
		},
		destroy: function(){
			// summary:
			//		Releases all internal resources owned by this shape. Once this method has been called,
			//		the instance is considered disposed and should not be used anymore.
			this.clear(true);
			// avoid this.inherited
			svg.Shape.prototype.destroy.apply(this, arguments);
		}
	});
	svg.Group.nodeType = "g";

	svg.Rect = declare("dojox.gfx.svg.Rect", [svg.Shape, gs.Rect], {
		// summary:
		//		a rectangle shape (SVG)
		setShape: function(newShape){
			// summary:
			//		sets a rectangle shape object (SVG)
			// newShape: Object
			//		a rectangle shape object
			this.shape = g.makeParameters(this.shape, newShape);
			this.bbox = null;
			for(var i in this.shape){
				if(i != "type" && i != "r"){
					var v = this.shape[i];
					if(i === "width" || i === "height"){
						v = v < 0 ? 0 : v;
					}
					this.rawNode.setAttribute(i, v);
				}
			}
			if(this.shape.r != null){
				this.rawNode.setAttribute("ry", this.shape.r);
				this.rawNode.setAttribute("rx", this.shape.r);
			}
			return this;	// self
		}
	});
	svg.Rect.nodeType = "rect";

	svg.Ellipse = declare("dojox.gfx.svg.Ellipse", [svg.Shape, gs.Ellipse], {});
	svg.Ellipse.nodeType = "ellipse";

	svg.Circle = declare("dojox.gfx.svg.Circle", [svg.Shape, gs.Circle], {});
	svg.Circle.nodeType = "circle";

	svg.Line = declare("dojox.gfx.svg.Line", [svg.Shape, gs.Line], {});
	svg.Line.nodeType = "line";

	svg.Polyline = declare("dojox.gfx.svg.Polyline", [svg.Shape, gs.Polyline], {
		// summary:
		//		a polyline/polygon shape (SVG)
		setShape: function(points, closed){
			// summary:
			//		sets a polyline/polygon shape object (SVG)
			// points: Object|Array
			//		a polyline/polygon shape object, or an array of points
			if(points && points instanceof Array){
				this.shape = g.makeParameters(this.shape, { points: points });
				if(closed && this.shape.points.length){
					this.shape.points.push(this.shape.points[0]);
				}
			}else{
				this.shape = g.makeParameters(this.shape, points);
			}
			this.bbox = null;
			this._normalizePoints();
			var attr = [], p = this.shape.points;
			for(var i = 0; i < p.length; ++i){
				attr.push(p[i].x.toFixed(8), p[i].y.toFixed(8));
			}
			this.rawNode.setAttribute("points", attr.join(" "));
			return this;	// self
		}
	});
	svg.Polyline.nodeType = "polyline";

	svg.Image = declare("dojox.gfx.svg.Image", [svg.Shape, gs.Image], {
		// summary:
		//		an image (SVG)
		setShape: function(newShape){
			// summary:
			//		sets an image shape object (SVG)
			// newShape: Object
			//		an image shape object
			this.shape = g.makeParameters(this.shape, newShape);
			this.bbox = null;
			var rawNode = this.rawNode;
			for(var i in this.shape){
				if(i != "type" && i != "src"){
					var v = this.shape[i];
					if(i === "width" || i === "height"){
						v = v < 0 ? 0 : v;
					}
					rawNode.setAttribute(i, v);
				}
			}
			rawNode.setAttribute("preserveAspectRatio", "none");
			_setAttributeNS(rawNode, svg.xmlns.xlink, "xlink:href", this.shape.src);
			// Bind GFX object with SVG node for ease of retrieval - that is to
			// save code/performance to keep this association elsewhere
			rawNode.__gfxObject__ = this;
			return this;	// self
		}
	});
	svg.Image.nodeType = "image";

	svg.Text = declare("dojox.gfx.svg.Text", [svg.Shape, gs.Text], {
		// summary:
		//		an anchored text (SVG)
		setShape: function(newShape){
			// summary:
			//		sets a text shape object (SVG)
			// newShape: Object
			//		a text shape object
			this.shape = g.makeParameters(this.shape, newShape);
			this.bbox = null;
			var r = this.rawNode, s = this.shape;
			r.setAttribute("x", s.x);
			r.setAttribute("y", s.y);
			r.setAttribute("text-anchor", s.align);
			r.setAttribute("text-decoration", s.decoration);
			r.setAttribute("rotate", s.rotated ? 90 : 0);
			r.setAttribute("kerning", s.kerning ? "auto" : 0);
			r.setAttribute("text-rendering", textRenderingFix);

			// update the text content
			if(r.firstChild){
				r.firstChild.nodeValue = s.text;
			}else{
				r.appendChild(_createTextNode(s.text));
			}
			return this;	// self
		},
		getTextWidth: function(){
			// summary:
			//		get the text width in pixels
			var rawNode = this.rawNode,
				oldParent = rawNode.parentNode,
				_measurementNode = rawNode.cloneNode(true);
			_measurementNode.style.visibility = "hidden";

			// solution to the "orphan issue" in FF
			var _width = 0, _text = _measurementNode.firstChild.nodeValue;
			oldParent.appendChild(_measurementNode);

			// solution to the "orphan issue" in Opera
			// (nodeValue == "" hangs firefox)
			if(_text!=""){
				while(!_width){
//Yang: work around svgweb bug 417 -- http://code.google.com/p/svgweb/issues/detail?id=417
if (_measurementNode.getBBox)
					_width = parseInt(_measurementNode.getBBox().width);
else
	_width = 68;
				}
			}
			oldParent.removeChild(_measurementNode);
			return _width;
		},
		getBoundingBox: function(){
			var s = this.getShape(), bbox = null;
			if(s.text){
				// try/catch the FF native getBBox error.
				try {
					bbox = this.rawNode.getBBox();
				} catch (e) {
					// under FF when the node is orphan (all other browsers return a 0ed bbox.
					bbox = {x:0, y:0, width:0, height:0};
				}
			}
			return bbox;
		}
	});
	svg.Text.nodeType = "text";

	svg.Path = declare("dojox.gfx.svg.Path", [svg.Shape, pathLib.Path], {
		// summary:
		//		a path shape (SVG)
		_updateWithSegment: function(segment){
			// summary:
			//		updates the bounding box of path with new segment
			// segment: Object
			//		a segment
			this.inherited(arguments);
			if(typeof(this.shape.path) == "string"){
				this.rawNode.setAttribute("d", this.shape.path);
			}
		},
		setShape: function(newShape){
			// summary:
			//		forms a path using a shape (SVG)
			// newShape: Object
			//		an SVG path string or a path object (see dojox/gfx.defaultPath)
			this.inherited(arguments);
			if(this.shape.path){
				this.rawNode.setAttribute("d", this.shape.path);
			}else{
				this.rawNode.removeAttribute("d");
			}
			return this;	// self
		}
	});
	svg.Path.nodeType = "path";

	svg.TextPath = declare("dojox.gfx.svg.TextPath", [svg.Shape, pathLib.TextPath], {
		// summary:
		//		a textpath shape (SVG)
		_updateWithSegment: function(segment){
			// summary:
			//		updates the bounding box of path with new segment
			// segment: Object
			//		a segment
			this.inherited(arguments);
			this._setTextPath();
		},
		setShape: function(newShape){
			// summary:
			//		forms a path using a shape (SVG)
			// newShape: Object
			//		an SVG path string or a path object (see dojox/gfx.defaultPath)
			this.inherited(arguments);
			this._setTextPath();
			return this;	// self
		},
		_setTextPath: function(){
			if(typeof this.shape.path != "string"){ return; }
			var r = this.rawNode;
			if(!r.firstChild){
				var tp = _createElementNS(svg.xmlns.svg, "textPath"),
					tx = _createTextNode("");
				tp.appendChild(tx);
				r.appendChild(tp);
			}
			var ref  = r.firstChild.getAttributeNS(svg.xmlns.xlink, "href"),
				path = ref && svg.getRef(ref);
			if(!path){
				var surface = this._getParentSurface();
				if(surface){
					var defs = surface.defNode;
					path = _createElementNS(svg.xmlns.svg, "path");
					var id = g._base._getUniqueId();
					path.setAttribute("id", id);
					defs.appendChild(path);
					_setAttributeNS(r.firstChild, svg.xmlns.xlink, "xlink:href", "#" + id);
				}
			}
			if(path){
				path.setAttribute("d", this.shape.path);
			}
		},
		_setText: function(){
			var r = this.rawNode;
			if(!r.firstChild){
				var tp = _createElementNS(svg.xmlns.svg, "textPath"),
					tx = _createTextNode("");
				tp.appendChild(tx);
				r.appendChild(tp);
			}
			r = r.firstChild;
			var t = this.text;
			r.setAttribute("alignment-baseline", "middle");
			switch(t.align){
				case "middle":
					r.setAttribute("text-anchor", "middle");
					r.setAttribute("startOffset", "50%");
					break;
				case "end":
					r.setAttribute("text-anchor", "end");
					r.setAttribute("startOffset", "100%");
					break;
				default:
					r.setAttribute("text-anchor", "start");
					r.setAttribute("startOffset", "0%");
					break;
			}
			//r.parentNode.setAttribute("alignment-baseline", "central");
			//r.setAttribute("dominant-baseline", "central");
			r.setAttribute("baseline-shift", "0.5ex");
			r.setAttribute("text-decoration", t.decoration);
			r.setAttribute("rotate", t.rotated ? 90 : 0);
			r.setAttribute("kerning", t.kerning ? "auto" : 0);
			r.firstChild.data = t.text;
		}
	});
	svg.TextPath.nodeType = "text";

	// Fix for setDimension bug:
	// http://bugs.dojotoolkit.org/ticket/16100
	// (https://code.google.com/p/chromium/issues/detail?id=162628)
	var hasSvgSetAttributeBug = (function(){ var matches = /WebKit\/(\d*)/.exec(uagent); return matches ? matches[1] : 0})() > 534;

	svg.Surface = declare("dojox.gfx.svg.Surface", gs.Surface, {
		// summary:
		//		a surface object to be used for drawings (SVG)
		constructor: function(){
			gs.Container._init.call(this);
		},
		destroy: function(){
			// no need to call svg.Container.clear to remove the children raw
			// nodes since the surface raw node will be removed. So, only dispose at gfx level	
			gs.Container.clear.call(this, true); 
			this.defNode = null;	// release the external reference
			this.inherited(arguments);
		},
		setDimensions: function(width, height){
			// summary:
			//		sets the width and height of the rawNode
			// width: String
			//		width of surface, e.g., "100px"
			// height: String
			//		height of surface, e.g., "100px"
			if(!this.rawNode){ return this; }
			var w = width < 0 ? 0 : width,
				h = height < 0 ? 0 : height;
			this.rawNode.setAttribute("width",  w);
			this.rawNode.setAttribute("height", h);
			if(hasSvgSetAttributeBug){
				this.rawNode.style.width =  w;
				this.rawNode.style.height =  h;
			}
			return this;	// self
		},
		getDimensions: function(){
			// summary:
			//		returns an object with properties "width" and "height"
			var t = this.rawNode ? {
				width:  g.normalizedLength(this.rawNode.getAttribute("width")),
				height: g.normalizedLength(this.rawNode.getAttribute("height"))} : null;
			return t;	// Object
		}
	});

	svg.createSurface = function(parentNode, width, height){
		// summary:
		//		creates a surface (SVG)
		// parentNode: Node
		//		a parent node
		// width: String|Number
		//		width of surface, e.g., "100px" or 100
		// height: String|Number
		//		height of surface, e.g., "100px" or 100
		// returns: dojox/gfx/shape.Surface
		//     newly created surface

		var s = new svg.Surface();
		s.rawNode = _createElementNS(svg.xmlns.svg, "svg");
		s.rawNode.setAttribute("overflow", "hidden");
		if(width){
			s.rawNode.setAttribute("width",  width < 0 ? 0 : width);
		}
		if(height){
			s.rawNode.setAttribute("height", height < 0 ? 0 : height);
		}

		var defNode = _createElementNS(svg.xmlns.svg, "defs");
		s.rawNode.appendChild(defNode);
		s.defNode = defNode;

		s._parent = dom.byId(parentNode);
		s._parent.appendChild(s.rawNode);

		g._base._fixMsTouchAction(s);

		return s;	// dojox/gfx.Surface
	};

	// Extenders

	var Font = {
		_setFont: function(){
			// summary:
			//		sets a font object (SVG)
			var f = this.fontStyle;
			// next line doesn't work in Firefox 2 or Opera 9
			//this.rawNode.setAttribute("font", dojox.gfx.makeFontString(this.fontStyle));
			this.rawNode.setAttribute("font-style", f.style);
			this.rawNode.setAttribute("font-variant", f.variant);
			this.rawNode.setAttribute("font-weight", f.weight);
			this.rawNode.setAttribute("font-size", f.size);
			this.rawNode.setAttribute("font-family", f.family);
		}
	};

	var C = gs.Container;
	var Container = svg.Container = {
		openBatch: function() {
			// summary:
			//		starts a new batch, subsequent new child shapes will be held in
			//		the batch instead of appending to the container directly
			if(!this._batch){
				this.fragment = _createFragment();
			}
			++this._batch;
			return this;
		},
		closeBatch: function() {
			// summary:
			//		submits the current batch, append all pending child shapes to DOM
			this._batch = this._batch > 0 ? --this._batch : 0;
			if (this.fragment && !this._batch) {
				this.rawNode.appendChild(this.fragment);
				delete this.fragment;
			}
			return this;
		},
		add: function(shape){
			// summary:
			//		adds a shape to a group/surface
			// shape: dojox/gfx/shape.Shape
			//		an VML shape object
			if(this != shape.getParent()){
				if (this.fragment) {
					this.fragment.appendChild(shape.rawNode);
				} else {
					this.rawNode.appendChild(shape.rawNode);
				}
				C.add.apply(this, arguments);
				// update clipnode with new parent
				shape.setClip(shape.clip);
			}
			return this;	// self
		},
		remove: function(shape, silently){
			// summary:
			//		remove a shape from a group/surface
			// shape: dojox/gfx/shape.Shape
			//		an VML shape object
			// silently: Boolean?
			//		if true, regenerate a picture
			if(this == shape.getParent()){
				if(this.rawNode == shape.rawNode.parentNode){
					this.rawNode.removeChild(shape.rawNode);
				}
				if(this.fragment && this.fragment == shape.rawNode.parentNode){
					this.fragment.removeChild(shape.rawNode);
				}
				// remove clip node from parent 
				shape._removeClipNode();
				C.remove.apply(this, arguments);
			}
			return this;	// self
		},
		clear: function(){
			// summary:
			//		removes all shapes from a group/surface
			var r = this.rawNode;
			while(r.lastChild){
				r.removeChild(r.lastChild);
			}
			var defNode = this.defNode;
			if(defNode){
				while(defNode.lastChild){
					defNode.removeChild(defNode.lastChild);
				}
				r.appendChild(defNode);
			}
			return C.clear.apply(this, arguments);
		},
		getBoundingBox: C.getBoundingBox,
		_moveChildToFront: C._moveChildToFront,
		_moveChildToBack:  C._moveChildToBack
	};

	var Creator = svg.Creator = {
		// summary:
		//		SVG shape creators
		createObject: function(shapeType, rawShape){
			// summary:
			//		creates an instance of the passed shapeType class
			// shapeType: Function
			//		a class constructor to create an instance of
			// rawShape: Object
			//		properties to be passed in to the classes "setShape" method
			if(!this.rawNode){ return null; }
			var shape = new shapeType(),
				node = _createElementNS(svg.xmlns.svg, shapeType.nodeType);

			shape.setRawNode(node);
			shape.setShape(rawShape);
			// rawNode.appendChild() will be done inside this.add(shape) below
			this.add(shape);
			return shape;	// dojox/gfx/shape.Shape
		}
	};

	lang.extend(svg.Text, Font);
	lang.extend(svg.TextPath, Font);

	lang.extend(svg.Group, Container);
	lang.extend(svg.Group, gs.Creator);
	lang.extend(svg.Group, Creator);

	lang.extend(svg.Surface, Container);
	lang.extend(svg.Surface, gs.Creator);
	lang.extend(svg.Surface, Creator);

	// Mouse/Touch event
	svg.fixTarget = function(event, gfxElement) {
		// summary:
		//		Adds the gfxElement to event.gfxTarget if none exists. This new
		//		property will carry the GFX element associated with this event.
		// event: Object
		//		The current input event (MouseEvent or TouchEvent)
		// gfxElement: Object
		//		The GFX target element
		if (!event.gfxTarget) {
			if (safMobile && event.target.wholeText) {
				// Workaround iOS bug when touching text nodes
				event.gfxTarget = event.target.parentElement.__gfxObject__;
			} else {
				event.gfxTarget = event.target.__gfxObject__;
			}
		}
		return true;
	};

	// some specific override for svgweb + flash
	if(svg.useSvgWeb){
		// override createSurface()
		svg.createSurface = function(parentNode, width, height){
			var s = new svg.Surface();
			
			width = width < 0 ? 0 : width;
			height = height < 0 ? 0 : height;

			// ensure width / height
			if(!width || !height){
				var pos = domGeom.position(parentNode);
				width  = width  || pos.w;
				height = height || pos.h;
			}

			// ensure id
			parentNode = dom.byId(parentNode);
			var id = parentNode.id ? parentNode.id+'_svgweb' : g._base._getUniqueId();

			// create dynamic svg root
			var mockSvg = _createElementNS(svg.xmlns.svg, 'svg');
			mockSvg.id = id;
			mockSvg.setAttribute('width', width);
			mockSvg.setAttribute('height', height);
			svgweb.appendChild(mockSvg, parentNode);

			// notice: any call to the raw node before flash init will fail.
			mockSvg.addEventListener('SVGLoad', function(){
				// become loaded
				s.rawNode = this;
				s.isLoaded = true;

				// init defs
				var defNode = _createElementNS(svg.xmlns.svg, "defs");
				s.rawNode.appendChild(defNode);
				s.defNode = defNode;

				// notify application
				if (s.onLoad)
					s.onLoad(s);
			}, false);

			// flash not loaded yet
			s.isLoaded = false;
			return s;
		};

		// override Surface.destroy()
		svg.Surface.extend({
			destroy: function(){
				var mockSvg = this.rawNode;
				svgweb.removeChild(mockSvg, mockSvg.parentNode);
			}
		});

		// override connect() & disconnect() for Shape & Surface event processing
		var _eventsProcessing = {
			connect: function(name, object, method){
				// connect events using the mock addEventListener() provided by svgweb
				if (name.substring(0, 2)==='on') { name = name.substring(2); }
				if (arguments.length == 2) {
					method = object;
				} else {
					method = lang.hitch(object, method);
				}
				this.getEventSource().addEventListener(name, method, false);
				return [this, name, method];
			},
			disconnect: function(token){
				// disconnect events using the mock removeEventListener() provided by svgweb
				this.getEventSource().removeEventListener(token[1], token[2], false);
				delete token[0];
			}
		};

		lang.extend(svg.Shape, _eventsProcessing);
		lang.extend(svg.Surface, _eventsProcessing);
	}

	return svg;
});

},
'dojox/gfx/path':function(){
define(["./_base", "dojo/_base/lang","dojo/_base/declare", "./matrix", "./shape"],
	function(g, lang, declare, matrix, shapeLib){

	// module:
	//		dojox/gfx/path

	var Path = declare("dojox.gfx.path.Path", shapeLib.Shape, {
		// summary:
		//		a generalized path shape

		constructor: function(rawNode){
			// summary:
			//		a path constructor
			// rawNode: Node
			//		a DOM node to be used by this path object
			this.shape = lang.clone(g.defaultPath);
			this.segments = [];
			this.tbbox = null;
			this.absolute = true;
			this.last = {};
			this.rawNode = rawNode;
			this.segmented = false;
		},

		// mode manipulations
		setAbsoluteMode: function(mode){
			// summary:
			//		sets an absolute or relative mode for path points
			// mode: Boolean
			//		true/false or "absolute"/"relative" to specify the mode
			this._confirmSegmented();
			this.absolute = typeof mode == "string" ? (mode == "absolute") : mode;
			return this; // self
		},
		getAbsoluteMode: function(){
			// summary:
			//		returns a current value of the absolute mode
			this._confirmSegmented();
			return this.absolute; // Boolean
		},

		getBoundingBox: function(){
			// summary:
			//		returns the bounding box {x, y, width, height} or null
			this._confirmSegmented();
			return (this.bbox && ("l" in this.bbox)) ? {x: this.bbox.l, y: this.bbox.t, width: this.bbox.r - this.bbox.l, height: this.bbox.b - this.bbox.t} : null; // dojox/gfx.Rectangle
		},

		_getRealBBox: function(){
			// summary:
			//		returns an array of four points or null
			//		four points represent four corners of the untransformed bounding box
			this._confirmSegmented();
			if(this.tbbox){
				return this.tbbox;	// Array
			}
			var bbox = this.bbox, matrix = this._getRealMatrix();
			this.bbox = null;
			for(var i = 0, len = this.segments.length; i < len; ++i){
				this._updateWithSegment(this.segments[i], matrix);
			}
			var t = this.bbox;
			this.bbox = bbox;
			this.tbbox = t ? [
				{x: t.l, y: t.t},
				{x: t.r, y: t.t},
				{x: t.r, y: t.b},
				{x: t.l, y: t.b}
			] : null;
			return this.tbbox;	// Array
		},

		getLastPosition: function(){
			// summary:
			//		returns the last point in the path, or null
			this._confirmSegmented();
			return "x" in this.last ? this.last : null; // Object
		},

		_applyTransform: function(){
			this.tbbox = null;
			return this.inherited(arguments);
		},

		// segment interpretation
		_updateBBox: function(x, y, m){
			// summary:
			//		updates the bounding box of path with new point
			// x: Number
			//		an x coordinate
			// y: Number
			//		a y coordinate

			if(m){
				var t = matrix.multiplyPoint(m, x, y);
				x = t.x;
				y = t.y;
			}

			// we use {l, b, r, t} representation of a bbox
			if(this.bbox && ("l" in this.bbox)){
				if(this.bbox.l > x) this.bbox.l = x;
				if(this.bbox.r < x) this.bbox.r = x;
				if(this.bbox.t > y) this.bbox.t = y;
				if(this.bbox.b < y) this.bbox.b = y;
			}else{
				this.bbox = {l: x, b: y, r: x, t: y};
			}
		},
		_updateWithSegment: function(segment, matrix){
			// summary:
			//		updates the bounding box of path with new segment
			// segment: Object
			//		a segment
			var n = segment.args, l = n.length, i;
			// update internal variables: bbox, absolute, last
			switch(segment.action){
				case "M":
				case "L":
				case "C":
				case "S":
				case "Q":
				case "T":
					for(i = 0; i < l; i += 2){
						this._updateBBox(n[i], n[i + 1], matrix);
					}
					this.last.x = n[l - 2];
					this.last.y = n[l - 1];
					this.absolute = true;
					break;
				case "H":
					for(i = 0; i < l; ++i){
						this._updateBBox(n[i], this.last.y, matrix);
					}
					this.last.x = n[l - 1];
					this.absolute = true;
					break;
				case "V":
					for(i = 0; i < l; ++i){
						this._updateBBox(this.last.x, n[i], matrix);
					}
					this.last.y = n[l - 1];
					this.absolute = true;
					break;
				case "m":
					var start = 0;
					if(!("x" in this.last)){
						this._updateBBox(this.last.x = n[0], this.last.y = n[1], matrix);
						start = 2;
					}
					for(i = start; i < l; i += 2){
						this._updateBBox(this.last.x += n[i], this.last.y += n[i + 1], matrix);
					}
					this.absolute = false;
					break;
				case "l":
				case "t":
					for(i = 0; i < l; i += 2){
						this._updateBBox(this.last.x += n[i], this.last.y += n[i + 1], matrix);
					}
					this.absolute = false;
					break;
				case "h":
					for(i = 0; i < l; ++i){
						this._updateBBox(this.last.x += n[i], this.last.y, matrix);
					}
					this.absolute = false;
					break;
				case "v":
					for(i = 0; i < l; ++i){
						this._updateBBox(this.last.x, this.last.y += n[i], matrix);
					}
					this.absolute = false;
					break;
				case "c":
					for(i = 0; i < l; i += 6){
						this._updateBBox(this.last.x + n[i], this.last.y + n[i + 1], matrix);
						this._updateBBox(this.last.x + n[i + 2], this.last.y + n[i + 3], matrix);
						this._updateBBox(this.last.x += n[i + 4], this.last.y += n[i + 5], matrix);
					}
					this.absolute = false;
					break;
				case "s":
				case "q":
					for(i = 0; i < l; i += 4){
						this._updateBBox(this.last.x + n[i], this.last.y + n[i + 1], matrix);
						this._updateBBox(this.last.x += n[i + 2], this.last.y += n[i + 3], matrix);
					}
					this.absolute = false;
					break;
				case "A":
					for(i = 0; i < l; i += 7){
						this._updateBBox(n[i + 5], n[i + 6], matrix);
					}
					this.last.x = n[l - 2];
					this.last.y = n[l - 1];
					this.absolute = true;
					break;
				case "a":
					for(i = 0; i < l; i += 7){
						this._updateBBox(this.last.x += n[i + 5], this.last.y += n[i + 6], matrix);
					}
					this.absolute = false;
					break;
			}
			// add an SVG path segment
			var path = [segment.action];
			for(i = 0; i < l; ++i){
				path.push(g.formatNumber(n[i], true));
			}
			if(typeof this.shape.path == "string"){
				this.shape.path += path.join("");
			}else{
				for(i = 0, l = path.length; i < l; ++i){
					this.shape.path.push(path[i]);
				}
			}
		},

		// a dictionary, which maps segment type codes to a number of their arguments
		_validSegments: {m: 2, l: 2, h: 1, v: 1, c: 6, s: 4, q: 4, t: 2, a: 7, z: 0},

		_pushSegment: function(action, args){
			// summary:
			//		adds a segment
			// action: String
			//		valid SVG code for a segment's type
			// args: Array
			//		a list of parameters for this segment
			this.tbbox = null;
			var group = this._validSegments[action.toLowerCase()], segment;
			if(typeof group == "number"){
				if(group){
					if(args.length >= group){
						segment = {action: action, args: args.slice(0, args.length - args.length % group)};
						this.segments.push(segment);
						this._updateWithSegment(segment);
					}
				}else{
					segment = {action: action, args: []};
					this.segments.push(segment);
					this._updateWithSegment(segment);
				}
			}
		},

		_collectArgs: function(array, args){
			// summary:
			//		converts an array of arguments to plain numeric values
			// array: Array
			//		an output argument (array of numbers)
			// args: Array
			//		an input argument (can be values of Boolean, Number, dojox/gfx.Point, or an embedded array of them)
			for(var i = 0; i < args.length; ++i){
				var t = args[i];
				if(typeof t == "boolean"){
					array.push(t ? 1 : 0);
				}else if(typeof t == "number"){
					array.push(t);
				}else if(t instanceof Array){
					this._collectArgs(array, t);
				}else if("x" in t && "y" in t){
					array.push(t.x, t.y);
				}
			}
		},

		// segments
		moveTo: function(){
			// summary:
			//		forms a move segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "M" : "m", args);
			return this; // self
		},
		lineTo: function(){
			// summary:
			//		forms a line segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "L" : "l", args);
			return this; // self
		},
		hLineTo: function(){
			// summary:
			//		forms a horizontal line segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "H" : "h", args);
			return this; // self
		},
		vLineTo: function(){
			// summary:
			//		forms a vertical line segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "V" : "v", args);
			return this; // self
		},
		curveTo: function(){
			// summary:
			//		forms a curve segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "C" : "c", args);
			return this; // self
		},
		smoothCurveTo: function(){
			// summary:
			//		forms a smooth curve segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "S" : "s", args);
			return this; // self
		},
		qCurveTo: function(){
			// summary:
			//		forms a quadratic curve segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "Q" : "q", args);
			return this; // self
		},
		qSmoothCurveTo: function(){
			// summary:
			//		forms a quadratic smooth curve segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "T" : "t", args);
			return this; // self
		},
		arcTo: function(){
			// summary:
			//		forms an elliptic arc segment
			this._confirmSegmented();
			var args = [];
			this._collectArgs(args, arguments);
			this._pushSegment(this.absolute ? "A" : "a", args);
			return this; // self
		},
		closePath: function(){
			// summary:
			//		closes a path
			this._confirmSegmented();
			this._pushSegment("Z", []);
			return this; // self
		},

		_confirmSegmented: function() {
			if (!this.segmented) {
				var path = this.shape.path;
				// switch to non-updating version of path building
				this.shape.path = [];
				this._setPath(path);
				// switch back to the string path
				this.shape.path = this.shape.path.join("");
				// become segmented
				this.segmented = true;
			}
		},

		// setShape
		_setPath: function(path){
			// summary:
			//		forms a path using an SVG path string
			// path: String
			//		an SVG path string
			var p = lang.isArray(path) ? path : path.match(g.pathSvgRegExp);
			this.segments = [];
			this.absolute = true;
			this.bbox = {};
			this.last = {};
			if(!p) return;
			// create segments
			var action = "",	// current action
				args = [],		// current arguments
				l = p.length;
			for(var i = 0; i < l; ++i){
				var t = p[i], x = parseFloat(t);
				if(isNaN(x)){
					if(action){
						this._pushSegment(action, args);
					}
					args = [];
					action = t;
				}else{
					args.push(x);
				}
			}
			this._pushSegment(action, args);
		},
		setShape: function(newShape){
			// summary:
			//		forms a path using a shape
			// newShape: Object
			//		an SVG path string or a path object (see dojox/gfx.defaultPath)
			this.inherited(arguments, [typeof newShape == "string" ? {path: newShape} : newShape]);

			this.segmented = false;
			this.segments = [];
			if(!g.lazyPathSegmentation){
				this._confirmSegmented();
			}
			return this; // self
		},

		// useful constant for descendants
		_2PI: Math.PI * 2
	});

	var TextPath = declare("dojox.gfx.path.TextPath", Path, {
		// summary:
		//		a generalized TextPath shape

		constructor: function(rawNode){
			// summary:
			//		a TextPath shape constructor
			// rawNode: Node
			//		a DOM node to be used by this TextPath object
			if(!("text" in this)){
				this.text = lang.clone(g.defaultTextPath);
			}
			if(!("fontStyle" in this)){
				this.fontStyle = lang.clone(g.defaultFont);
			}
		},
		getText: function(){
			// summary:
			//		returns the current text object or null
			return this.text;	// Object
		},
		setText: function(newText){
			// summary:
			//		sets a text to be drawn along the path
			this.text = g.makeParameters(this.text,
				typeof newText == "string" ? {text: newText} : newText);
			this._setText();
			return this;	// self
		},
		getFont: function(){
			// summary:
			//		returns the current font object or null
			return this.fontStyle;	// Object
		},
		setFont: function(newFont){
			// summary:
			//		sets a font for text
			this.fontStyle = typeof newFont == "string" ?
				g.splitFontString(newFont) :
				g.makeParameters(g.defaultFont, newFont);
			this._setFont();
			return this;	// self
		}
	});

	/*=====
	g.Path = Path;
	g.TextPath = TextPath;
	=====*/

	return g.path = {
		// summary:
		//		This module contains the core graphics Path API.
		//		Path command format follows the W3C SVG 1.0 Path api.

		Path: Path,
		TextPath: TextPath
	};
});

}}});
define("dojo/pmbmaps", [], 1);
