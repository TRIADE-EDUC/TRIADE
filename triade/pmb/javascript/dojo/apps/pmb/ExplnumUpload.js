// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExplnumUpload.js,v 1.8 2021/11/25 12:54:54 dgoron Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/ready",
        "snet/fileUploader/Uploader",
        "dojo/dom-construct",
        "apps/pmb/PMBDialog",
        "dojo/io-query",
    	"apps/pmb/contextUtil",
    	"dojo/_base/xhr", 
    	"dojo/dom-form",
    	"dojo/request/xhr",
    	"dojo/topic",
    	"dojo/parser",
    	"dijit/registry",
], function(declare, lang, request, query, on, domAttr, dom, ready, Uploader, domConstruct, PMBDialog, ioQuery, contextUtil, xhr, domForm, reqxhr, topic, parser, registry){
	return declare(null, {
		entityId: null,
		entityType: null,
		bulId: null,
		upl:null,
		constructor: function(entityId, entityType, bulId) {
			this.entityId = entityId;
			this.entityType = entityType;
			this.bulId = bulId;
			this.initUploader();
		},
		initUploader: function(){
			/**
			 * TODO: switch sur l'url selon le type d'entité
			 */
			
			this.upl = new Uploader({
				
				url: './ajax.php?module=catalog&categ=explnum&quoifaire=upload_docnum&record_id='+this.entityId+(this.bulId ? '&bul_id='+this.bulId : ''),
				dropTarget: 'dropTarget_'+this.entityId,
				maxKBytes: pmbDojo.uploadMaxFileSize,
				maxNumFiles: 10,
				append_div: 'document_list_'+this.entityId,
				requestCallback: lang.hitch(this, this.uploadCallback)
			});
		},
		uploadCallback: function(data){
			if(data.response){
				/**
				 * TODO: Switch sur le type d'entité pour récupérer l'endroit où placer l'élément
				 */
				if(this.entityType == 'article'){
                	var divDepouille = query('div[class="depouillements-perio"]');
                	var divContainer = query('div[id="bulletin_analysis_list"]', divDepouille[0]);
                	var widgets = registry.toArray();
                	widgets.forEach(function(widget){
                		if(widget.id.indexOf('commande') != -1){
                			widget.destroy();
                		}
                	});
                	domConstruct.empty(divContainer[0]);
                	domConstruct.place(data.bull_display, divContainer[0], 'last');
                	var divContainerBack = query('div[id^="el'+this.entityId+'_"][id$="Child"]')[0];
                	var id = divContainerBack.id.replace('Child','');
                	parser.parse(divContainer[0]);
    				query('script', divContainer[0]).forEach(function(node) {
    					domConstruct.create('script', {
    						innerHTML: node.innerHTML,
    						type: 'text/javascript'
    					}, node, 'replace');
    				});
                	expandBase(id, true);
                	topic.publish('ExplnumUpload', 'docnumUploaded', 'article');
                }else{
                	var tableContainer = this.getTableContainer();
                	var explnumContainer = query('div[id^=\"explnum_list_container_\"]', tableContainer);
                	
                	if(!explnumContainer.length){
                		var explnumContainer = domConstruct.create('div', {id:'explnum_list_container_record_'+this.entityId}, tableContainer);
                	}else{
                		explnumContainer = explnumContainer[0]; 
                	}
                	
                	var docnum = query('table[class="docnum"]', explnumContainer);
                	if(docnum.length){
                		domConstruct.destroy(docnum[0]);
                	}
    	            domConstruct.empty(explnumContainer);
                    domConstruct.place(data.title, explnumContainer, 'last');
                    domConstruct.place(data.response, explnumContainer, 'last');	
                    topic.publish('ExplnumUpload', 'docnumUploaded');
                }
                if(dijit.byId(this.upl.idDropTarget+'snetUploader')) {
                	dijit.byId(this.upl.idDropTarget+'snetUploader').hide();
                }
            }
			if(data.has_doublons) {
				if(this.upl.progressBars.length) {
					var progressBar = this.upl.progressBars[0];
					progressBar.abort();
					progressBar.setState('error');
					var error = {
							'statusCode' : '200', 
							'statusText' : 'Conflict',
							'responseText' : pmbDojo.messages.getMessage('docnum', 'explnum_has_doublons')
					};
					progressBar.error(error);
				}
            }
		},
		getTableContainer: function(){
			var queryString = ""
			switch(this.entityType){
				case 'record':
					queryString = 'div[id^=\"expl_area_\"]';
					break;
				case 'article':
					queryString = 'div[id=\"expl_area_'+this.entityId+'\"]'
					break;
			}
            var element = query(queryString);
            if(element && element.length){
            	return element[0];
            }
            return null;
		},
	});
});