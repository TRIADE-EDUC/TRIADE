// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityForm.js,v 1.4 2023/03/22 15:34:34 qvarin Exp $


define([
        "dojo/_base/declare", 
        "dojo/_base/lang",
        "dojo/on",
        "dojo/dom",
        "dijit/registry",
        "apps/pmb/PMBDojoxDialogSimple",
        "apps/pmb/pmbEventsHandler",
        ], 
		function(declare, lang, on, dom, registry, DialogSimple, pmbEventsHandler){
	return declare(null, {
		className: null,
		indexation: null,
		signals: null,
		dijits:null,
		formName:null,
		constructor: function(params){
			lang.mixin(this, params);

            this.signals = [];
            this.dijits = {};

			this.init();
		},

		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'leafRootClicked':
				case 'leafClicked':
					this.destroy();
					break;
				default:
					if (typeof this[evtType] == 'function') {
						this[evtType](evtArgs);
					}
					break;
			}
		},

		init: function(){
			pmbEventsHandler.initEvents(this);
		},

		destroy:function(){
			this.signals.forEach(function(signal) {
				signal.listner.remove();
			});
			this.dijits.forEach(function(dijit) {
				dijit.destroyRecursive();
				dijit.destroy();
			});
		},

		loadDialog : function(params, evt, path) {
			let dijitId = params.entity_type + "_dialog";

			if(!this.dijits[dijitId]) {
                // Suppression + creation de la dialog
				this.destroyDialog(dijitId);
				this.dijits[dijitId] = new DialogSimple({
					title: pmbDojo.messages.getMessage('dsi', 'search_rmc_title'),
					executeScripts: true,
					id: dijitId,
					style: {
						width:'85%'
					}
				});

				this.dijits[dijitId].attr('href', path);
				this.dijits[dijitId].startup();

				// Gestion des event de la modal dojo
				this.addSignal(dijitId, on(this.dijits[dijitId], "load", lang.hitch(this, function() {
					pmbEventsHandler.initEvents(this, dom.byId(dijitId));
					pmbEventsHandler.formToAjax(dom.byId(dijitId));
				})));
				this.addSignal(dijitId, on(this.dijits[dijitId], "hide", lang.hitch(this, function() {
					this.destroyDialog(dijitId);
				})));
			}

            // On calcule sa taille et on l'affiche
			this.dijits[dijitId].resize();
			this.dijits[dijitId].show();

			return this.dijits[dijitId];
		},

		hideDialog : function(params) {
			if (!params.className) {
				params.className = this.className;
			}

			let dijitId = params.entity_type + "_dialog";
			if (this.dijits[dijitId]) {
				this.dijits[dijitId].hide();
			} else {
				this.destroyDialog(params.entity_type + "_dialog");
			}
		},

		removeDialog : function(params) {
			this.destroyDialog(params.entity_type + "_dialog");
		},

		destroyDialog : function(dijitId) {
			let myDijit = registry.byId(dijitId);
            if (myDijit) {
				myDijit.destroyDescendants();
                myDijit.destroy();
                myDijit.destroyRendering();
            }

			if (this.dijits[dijitId]) {
				delete this.dijits[dijitId];
			}

			let signalRemoved = [];
            for (let index in this.signals) {
                let signal = this.signals[index];
                if (signal && signal.dialoagId == dijitId) {
                    signal.listner.remove();
                    signalRemoved.push(index);
                }
            }
            for (let index of signalRemoved) {
                this.signals.splice(index, 1);
            }
		},

		addSignal: function(dijitId, signal) {
			this.signals.push({
				dialoagId: dijitId,
				listner: signal
			});
		}
	});
});