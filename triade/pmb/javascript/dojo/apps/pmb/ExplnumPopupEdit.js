// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExplnumPopupEdit.js,v 1.4 2018/08/07 16:04:05 vtouchard Exp $

define([
        "dojo/_base/declare",
        "dojo/_base/lang",
        "dojo/request",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr",
        "dojo/dom",
        "dojo/ready",
        "dojo/dom-construct",
        "dojo/io-query",
    	"dojo/_base/xhr", 
    	"dojo/dom-form",
    	"dojo/request/xhr",
    	"apps/pmb/PMBContextDialog",
    	"dojo/topic",
], function(declare, lang, request, query, on, domAttr, dom, ready, domConstruct, ioQuery, xhr, domForm, reqxhr, PMBContextDialog, topic){
	return declare(null, {
		signals: [],
		constructor: function(recordId) {
			this.recordId = recordId;
			ready(this, lang.hitch(this, this.init));
			var signal = topic.subscribe('ExplnumUpload', lang.hitch(this, this.handleEvents));
			this.signals.push(signal);
		},
		init: function(){
			this.addExplLinksEvent();
		},
		removeSignals: function(){
			this.signals.forEach(function(signal){
				signal.remove();
			});
		},
		linkClicked: function(e){
			e.preventDefault();
			//ajax.php?module=catalog&categ=explnum&quoifaire=get_form&record_id=
			var objectQuery = ioQuery.queryToObject(e.target.href.split('?')[1]);
			var params = {};
			var elt = ['explnum_id', 'id', 'bulletin_id', 'analysis_id', 'bul_id'];
			elt.forEach(function(elt){
				if(objectQuery[elt]){
					params[elt] = objectQuery[elt];
				}
			});
			var dialog = new PMBContextDialog({
				popupEditController: this,
				title: pmbDojo.messages.getMessage('docnum', 'docnum_edit_title'),
				href: './ajax.php?module=catalog&categ=explnum&quoifaire=get_form&'+ioQuery.objectToQuery(params),
				onloadCallback: function(){
					var form = query('form[name="explnum"]', this.containerNode)[0];
					var cancelButton = query('input[id="cancel_button"]', this.containerNode)[0];
					domConstruct.destroy(cancelButton);
					on(form, 'submit', lang.hitch(this, function(form, e){
						e.preventDefault();
						this.makePost(form).then(lang.hitch(this, function(data){
							var data = JSON.parse(data); 
							if(data.status == "1"){
								dialog.hide();
					            var tableContainer = query('div[id=\"expl_area_'+data.record_id+'\"]');
					            domConstruct.empty(tableContainer[0]);
				                domConstruct.place(data.title, tableContainer[0], 'last');
				                domConstruct.place(data.response, tableContainer[0], 'last');
				                this.popupEditController.addExplLinksEvent();
							}else{
								alert(data.response);
							}
						}));
						return false;
					},form));
				},
				makePost: function(form){
					var formData = new FormData(form);
					var queryObj = ioQuery.queryToObject(form.action.split('?')[1]);
					return new Promise((resolve, reject) => {
						var xhr = new XMLHttpRequest();
						xhr.open("POST", './ajax.php?module=catalog&categ=explnum&quoifaire=update&id='+queryObj.id+'&iframe=1');
//						xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
						xhr.onload = () => resolve(xhr.responseText);
						xhr.onerror = () => reject(xhr.statusText);
						xhr.send(formData);
					});
				}
			});
			
			dialog.show();
			return false;
		},
		addExplLinksEvent: function(evtArgs){
			if (evtArgs === 'article') {
				this.removeSignals();
			}
			var links = query('a[class="docnum_name_link"]');
			links.forEach(lang.hitch(this, function(link){
				this.signals.push(on(link, 'click', lang.hitch(this, this.linkClicked)));
			}));
		},
		handleEvents: function(evtType, evtArgs = ''){
			// On récupère de ExplnumUpload.js evtArgs qui vaut 'article' si c'est un article et qui vaudra '' si ce n'est pas précisé
			switch(evtType){
				case 'docnumUploaded':
					this.addExplLinksEvent(evtArgs);
					break;
				case 'destroy':
					this.destroy();
					break;
			}
		},
		destroy: function(){
			this.removeSignals();
			for(var key in this){
				this[key] = null;
			}
		}
	});
});