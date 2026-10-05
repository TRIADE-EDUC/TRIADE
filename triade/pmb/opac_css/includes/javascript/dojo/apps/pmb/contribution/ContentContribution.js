// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContentContribution.js,v 1.2 2021/04/01 08:59:48 qvarin Exp $

define([
        'dojo/_base/declare',
        'dijit/layout/ContentPane',
        'dojo/_base/lang',
        'dojo/ready',
        'dojo/topic',
], function(declare, ContentPane, lang, ready, topic){
	return declare(ContentPane, {
		fieldsBackupValues: {},
		
		
		constructor: function() {
			topic.subscribe('Contribution', lang.hitch(this, this.handleEvents))
		},
		
		handleEvents: function(evtType, evtArgs) {
			switch(evtType){
				case 'savedSubDraftForm':
					if (evtArgs.widgetId == this.id) {
						// Sauvegarde brouillon
						this.resetbackupField();
					}
					break;
			}
		},
		
		onClose: function () {
			this.inherited(arguments);
			this.resetFields();
			topic.publish('ContentContribution', 'closeForm', {widgetId: this.id});
		},
		
		postCreate: function() {
			this.inherited(arguments);
			ready(lang.hitch(this, this.initBackup));
		},
		
		initBackup: function() {
			
			// init de la backup
			var backup = {};
			
			// On recupere les champs
			var nodesHidden = document.querySelectorAll('input[id^="' + this.nodeClickedId + '"][type="hidden"]');
			var nodesText = document.querySelectorAll('input[id^="' + this.nodeClickedId + '"][type="text"]');
			var nodesSelect = document.querySelectorAll('select[id^="' + this.nodeClickedId + '"]');
			var nodes = [...nodesText, ...nodesHidden, ...nodesSelect];
			
			// On recupere les valeurs pour les mettre dans la backup
			for (var i = 0; i < nodes.length; i++) {
				var key = nodes[i].id.replace(this.nodeClickedId+"_", '');
				backup[key] = nodes[i].value;
			}
			
			// On stock la backup
			this.fieldsBackupValues = backup;
		},
		
		getbackupField : function () {
			if (!this.fieldsBackupValues) {
				return {};
			}
			
			return this.fieldsBackupValues;
		},
		
		resetbackupField : function () {
			this.fieldsBackupValues = {};
			this.initBackup();
		},
		
		resetFields : function () {
			for (let index in this.fieldsBackupValues) {
				var node = document.getElementById(this.nodeClickedId+"_"+index);
				if (node && node.value != this.fieldsBackupValues[index]) {
					node.value = this.fieldsBackupValues[index];
				}
			}
		},
	})
});