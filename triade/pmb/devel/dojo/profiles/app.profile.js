// +-------------------------------------------------+
// © 2002-2015 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: app.profile.js,v 1.2 2015/12/21 11:20:05 vtouchard Exp $


/**
 * This is the default application build profile used by the boilerplate. While it looks similar, this build profile
 * is different from the package build profile at `app/package.js` in the following ways:
 *
 * 1. you can have multiple application build profiles (e.g. one for desktop, one for tablet, etc.), but only one
 *    package build profile;
 * 2. the package build profile only configures the `resourceTags` for the files in the package, whereas the
 *    application build profile tells the build system how to build the entire application.
 *
 * Look to `util/build/buildControlDefault.js` for more information on available options and their default values.
 */

var profile = {
	// `basePath` is relative to the directory containing this profile file; in this case, it is being set to the
	// src/ directory, which is the same place as the `baseUrl` directory in the loader configuration. (If you change
	// this, you will also need to update run.js.)
	basePath: "../src/",

	// This is the directory within the release directory where built packages will be placed. The release directory
	// itself is defined by `build.sh`. You should probably not use this; it is a legacy option dating back to Dojo
	// 0.4.
	// If you do use this, you will need to update build.sh, too.
	// releaseName: "",

	// Builds a new release.
	action: "release",

	// Strips all comments and whitespace from CSS files and inlines @imports where possible.
	cssOptimize: "comments",

	// Excludes tests, demos, and original template files from being included in the built version.
	mini: true,

	// Uses Closure Compiler as the JavaScript minifier. This can also be set to "shrinksafe" to use ShrinkSafe,
	// though ShrinkSafe is deprecated and not recommended.
	// This option defaults to "" (no compression) if not provided.
	optimize: "closure",

	// We"re building layers, so we need to set the minifier to use for those, too.
	// This defaults to "shrinksafe" if not provided.
	layerOptimize: "closure",

	// A list of packages that will be built. The same packages defined in the loader should be defined here in the
	// build profile.
	packages: [
		// Using a string as a package is shorthand for `{ name: "app", location: "app" }`
		"app",
		"dgrid",
		"dijit",
		"dojo",
		"dojox",
		"put-selector",
		"xstyle"
	],

	// Strips all calls to console functions within the code. You can also set this to "warn" to strip everything
	// but console.error, and any other truthy value to strip everything but console.warn and console.error.
	// This defaults to "normal" (strip all but warn and error) if not provided.
	stripConsole: "normal",

	// The default selector engine is not included by default in a dojo.js build in order to make mobile builds
	// smaller. We add it back here to avoid that extra HTTP request. There is also an "acme" selector available; if
	// you use that, you will need to set the `selectorEngine` property in index.html, too.
	selectorEngine: "lite",

	// Any module in an application can be converted into a "layer" module, which consists of the original module +
	// additional dependencies built into the same file. Using layers allows applications to reduce the number of HTTP
	// requests by combining all JavaScript into a single file.
	layers: {
		// This is the main loader module. It is a little special because it is treated like an AMD module even though
		// it is actually just plain JavaScript. There is some extra magic in the build system specifically for this
		// module ID.
	  
	  
		"dojo/dojo": {
			// By default, the build system will try to include `dojo/main` in the built `dojo/dojo` layer, which adds
			// a bunch of stuff we do not want or need. We want the initial script load to be as small and quick to
			// load as possible, so we configure it as a custom, bootable base.
			boot: true,
			customBase: true,
			include: ["dojo/domReady", "dojo/dom", "dojo/parser", "dojo/_base/declare", "dojo/require", "dijit/registry", "dojo/pmbdojo"],
		},

		// In this demo application, we load `app/main` on the client-side, so here we build a separate layer containing
		// that code. (Practically speaking, you would probably just want to roll everything into the `dojo/dojo` layer,
		// but this helps provide a basic illustration of how multi-layer builds work.) Note that when you create a new
		// layer, the module referenced by the layer is always included in the layer (in this case, `app/main`), so it
		// does not need to be explicitly defined in the `include` array.
		"dojo/pmbdojo": {
			include: [
				  //dijit form
				  "dijit/form/TextBox",
				  "dijit/form/NumberSpinner",
				  "dijit/form/Button",
				  "dijit/form/DropDownButton",
				  "dijit/form/Button",
				  "dijit/form/ComboButton",
				  "dijit/form/ComboBox",
				  "dijit/form/CheckBox",
				  "dijit/form/Form",
				  "dijit/form/RadioButton",
				  "dijit/form/_RadioButtonMixin",
				  "dijit/form/Textarea",
				  //fin dijit form
				  
				  //datas
				  "dijit/tree/ForestStoreModel",
				  "dijit/Tree",
				  "dijit/tree/dndSource",
				  "dojo/data/ItemFileWriteStore",
				  "dojo/store/Memory",
				  "dojo/store/Observable",
				  "dojo/store/JsonRest",
				  "dojo/data/ObjectStore",
				  "dijit/tree/ObjectStoreModel",
				  'dojo/store/util/QueryResults', 
				  'dojo/store/util/SimpleQueryEngine',
				  //fin datas
				  
				  //dijit layout
				  "dijit/layout/ContentPane",
				  "dojox/layout/ContentPane",
				  "dijit/layout/BorderContainer",
				  "dijit/layout/TabContainer",
				  "dijit/layout/AccordionContainer",
				  "dijit/layout/AccordionPane",
				  //fin dijit layout
				  
				  //dojox widget
				  "dojox/widget/Dialog",
				  "dojox/widget/DialogSimple",
				  "dojox/widget/Standby",
				  //fin dojox widget
				  
				  //dijit widget
				  "dijit/PopupMenuItem",
				  "dijit/CheckedMenuItem",
				  "dijit/MenuSeparator",
				  "dijit/Editor",
				  "dijit/Dialog",
				  "dijit/TooltipDialog",
				  "dijit/ProgressBar",	
				  "dijit/_TemplatedMixin",
				  "dijit/Toolbar",
				  "dijit/ConfirmDialog",
				  "dijit/_ConfirmDialogMixin",
				  //fin dijit widget
				  
				  //editeur
				  "dijit/_editor/RichText",
				  "dijit/_editor/_Plugin",
				  "dijit/_editor/plugins/EnterKeyHandling",
				  "dijit/_editor/html",
				  "dijit/_editor/range",
				  "dijit/_editor/plugins/LinkDialog",
				  "dijit/_editor/plugins/FontChoice",
				  "dijit/_editor/plugins/TextColor",
				  "dijit/_editor/plugins/FullScreen",
				  "dijit/_editor/plugins/ViewSource",
				  "dojox/editor/plugins/InsertEntity",
				  "dojox/editor/plugins/TablePlugins",
				  "dojox/editor/plugins/ResizeTableColumn",
				  "dojox/editor/plugins/PasteFromWord",
				  "dojox/editor/plugins/InsertAnchor",
				  "dojox/editor/plugins/Blockquote",
				  "dojox/editor/plugins/LocalImage",
				  //fin editeur
				  
				  //dojo _base
				  "dojo/_base/connect",
				  "dojo/_base/lang",
				  "dojo/_base/Deferred",
				  "dojo/_base/array",
				  "dojo/_base/xhr",
				  "dojo/_base/event",
				  "dojo/_base/fx",
				  "dojo/_base/kernel",
				  "dojo/_base/sniff",
				  "dojo/_base/json",
				  "dojo/_base/Color",
				  "dojo/_base/browser",
				  "dojo/_base/unload",
				  "dojo/_base/html",
				  "dojo/_base/NodeList",
				  //fin dojo _base
				  
				  //dijit _base
				  "dijit/_base",
				  //fin dijit _base				  
				  
				  //dojo dnd
				  "dojo/dnd/Source",
				  "dojo/dnd/Selector",
				  "dojo/dnd/Container",
				  "dojo/dnd/AutoSource",
				  "dojo/dnd/Target",
				  //fin dojo dnd  
				  
				  //dojo dom
				  "dojo/dom-construct",
				  "dojo/dom-class",
				  "dojo/dom-style",
				  "dojo/dom-attr",
				  "dojo/dom-geometry",
				  "dojo/dom-prop",
				  "dojo/dom-form",
				  //fin dojo dom
				  
				  //dojo request
				  "dojo/request",
				  //fin dojo request
				  
				  //dojox grid
				  "dojox/grid/DataGrid",
				  "dojox/grid/util",
				  "dojox/grid/Selection",
				  "dojox/grid/DataSelection",
				  "dojox/grid/cells",
				  "dojox/grid/cells/_base",
				  "dojox/grid/_Grid",
				  "dojox/grid/_Events",
				  "dojox/grid/_Scroller",
				  "dojox/grid/_Layout",
				  "dojox/grid/_RowSelector",
				  "dojox/grid/_View",
				  "dojox/grid/_Builder",
				  "dojox/grid/_ViewManager",
				  "dojox/grid/_RowManager",
				  "dojox/grid/_FocusManager",
				  "dojox/grid/_EditManager",
				  "dojox/grid/_SelectionPreserver",
				  //fin dojoxgrid
				  
				  //dojo misc
				  "dojo/query",
				  "dojo/has",
				  "dojo/on",
				  "dojo/aspect",
				  "dojo/fx/Toggler",
				  "dojo/topic",
				  "dojo/mouse",
				  "dojo/keys",
				  "dojo/NodeList-dom",
				  "dojo/date",
				  "dojo/date/locale",
				  'dojo/uacss',
				  //fin dojo misc
				  
				  //dojox dtl
				  'dojox/dtl/_Templated',
				  'dojox/dtl/_base',
				  'dojox/dtl/tag/logic',
				  'dojox/dtl/tag/loop',
				  'dojox/dtl/filter/strings',
				  'dojox/dtl/filter/htmlstrings',
				  //fin dojox dtl
				  
				  //dijit misc
				  "dijit/CalendarLite",
				  "dijit/Calendar",
				  "dijit/dijit",
				  //fin dijit misc
				  
				  //dojox misc
				  "dojox/html/metrics",
				  'dojox/string/tokenize',
				  'dojox/string/Builder',
				  'dojox/string/sprintf',
				  //fin dojox misc.

				 ],
		},
		/*"dojo/pmbmaps": {
			// By default, the build system will try to include `dojo/main` in the built `dojo/dojo` layer, which adds
			// a bunch of stuff we do not want or need. We want the initial script load to be as small and quick to
			// load as possible, so we configure it as a custom, bootable base.
			include: [
				  "dojox/geo/openlayers/widget/Map", 
				  "dojox/geo/openlayers/_base", 
				  "dojox/geo/openlayers/Collection",
				  "dojox/geo/openlayers/Feature", 
				  "dojox/geo/openlayers/Geometry", 
				  "dojox/geo/openlayers/GeometryFeature", 
				  "dojox/geo/openlayers/GfxLayer", 
				  "dojox/geo/openlayers/JsonImport", 
				  "dojox/geo/openlayers/Layer", 
				  "dojox/geo/openlayers/LineString", 
				  "dojox/geo/openlayers/Map",
				  "dojox/geo/openlayers/Patch", 
				  "dojox/geo/openlayers/TouchInteractionSupport",
				  "dojox/geo/openlayers/WidgetFeature", 
				  "dojox/gfx/svg",
				 ],
		},*/
	},

	// Providing hints to the build system allows code to be conditionally removed on a more granular level than simple
	// module dependencies can allow. This is especially useful for creating tiny mobile builds. Keep in mind that dead
	// code removal only happens in minifiers that support it! Currently, only Closure Compiler to the Dojo build system
	// with dead code removal. A documented list of has-flags in use within the toolkit can be found at
	// <http://dojotoolkit.org/reference-guide/dojo/has.html>.
	staticHasFeatures: {
	    "dojo-debug-messages": 0,
	    "dojo-dom-ready-api": 1,
	    "dojo-has-api": 1,
	    "dojo-inject-api": 1,
	    "dojo-loader": 1,
	    "dojo-log-api": 0,
	    "dojo-trace-api": 0,
	    "dojo-v1x-i18n-Api": 1,
	    "dom": 1,
	    "host-browser": 1,
	    "extend-dojo": 1,
	},
};
