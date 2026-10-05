// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PMBContextDialog.js,v 1.2 2018/06/22 15:33:13 vtouchard Exp $


define(["dojo/_base/declare", 
	"dijit/Dialog", 
	"dojo/_base/lang", 
	"dojo/dom-class", 
	"apps/pmb/PMBMainDialog", 
	"apps/pmb/contextUtil",
	"dojo/_base/xhr",
	"dojo/dom-construct"], function(declare, Dialog, lang, domClass, PMBMainDialog, contextUtil, xhr, domConstruct){

	  return declare([Dialog, PMBMainDialog], {
		  onloadCallback:null,
		  _load: function(){
				// summary:
				// Load/reload the href specified in this.href

				// display loading message
				this._setContent(this.onDownloadStart(), true);

				var self = this;
				var getArgs = {
					preventCache: (this.preventCache || this.refreshOnShow),
					url: this.href,
					handleAs: "text"
				};
				if(lang.isObject(this.ioArgs)){
					lang.mixin(getArgs, this.ioArgs);
				}

				var hand = (this._xhrDfd = (this.ioMethod || xhr.get)(getArgs)),
					returnedHtml;

				hand.then(
					function(html){
						returnedHtml = html;
						html = contextUtil.buildCustomContext(html);
						
						try{
							self._isDownloaded = true;
							var ret = self._setContent(html.content, false);
							this.scriptNode = document.createElement('script');
							this.scriptNode.type = "text/javascript";
							document.body.appendChild(this.scriptNode);
							this.scriptNode.text = html.scripts;
							return ret;
						}catch(err){
							self._onError('Content', err); // onContentError
						}
					},
					function(err){
						if(!hand.canceled){
							// show error message in the pane
							self._onError('Download', err); // onDownloadError
						}
						delete self._xhrDfd;
						return err;
					}
				).then(function(){
						self.onDownloadEnd();
						delete self._xhrDfd;
						return returnedHtml;
					});
				// Remove flag saying that a load is needed
				delete this._hrefChanged;
		  },
		  onLoad: function(){
			  this.inherited(arguments);
			  if(this.onloadCallback){
				  lang.hitch(this, this.onloadCallback)();
			  }
		  },
		  onHide: function(){
			  this.inherited(arguments);
			  domConstruct.destroy(this.scriptNode);
			  this.destroyRecursive();
		  }
	  });
});