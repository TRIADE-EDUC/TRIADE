// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PopupPond.js,v 1.1 2023/07/26 06:23:12 dgoron Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dom-style', 
        'apps/pmb/PMBConfirmDialog', 
        'dojo/aspect'], 
        function(declare, lang, topic, domStyle, ConfirmDialog, aspect){
	  return declare([ConfirmDialog], {
		  code:null,
		  label:null,
		  pond:null,
		  constructor:function(params){
			  //console.log('popup', this);
			  this.draggable = true;
			  this.code = (params.code ? params.code : '');
			  this.label = (params.label ? params.label : '');
			  this.pond = (params.pond ? params.pond : '');
			  this.title = pmbDojo.messages.getMessage('misc', 'misc_file_edit_pond');
			  this.content = 
		            '<label for="code"><strong>'+pmbDojo.messages.getMessage('misc', 'misc_file_code')+' :</strong></label>&nbsp;'+this.code+'<br><br>' +
		            '<label for="label"><strong>'+pmbDojo.messages.getMessage('misc', 'misc_file_label')+' :</strong></label>&nbsp;'+this.label+'<br><br>' +
		            '<label for="pond"><strong>'+pmbDojo.messages.getMessage('misc', 'misc_file_pond')+' :</strong></label>&nbsp;<input data-dojo-type="dijit/form/NumberTextBox" required="true" id="pond_'+this.code+'" name="pond" value="'+this.pond+'"><br><br>';
            this.style = 'width:200px';
			  
		  },
		  handleEvents: function(evtClass, evtType, evtArgs){
			  switch(evtClass){
			  }
		  },
		  createNodes: function(){
			  
		  },
		  onHide: function(evt){
			  this.inherited(arguments);
			  this.destroyDescendants();
			  this.destroy();
			  delete this;
		  },
		  onExecute: function(){
			  var callback = lang.hitch(this, this.show);
			  if(!this.value.pond || this.value.pond == ''){
				  return false;
			  } 
			  var parameters = this.value;
			  topic.publish('PopupPond', 'editPond', {
				  code:(parameters.code?parameters.code:''), 
				  pond:(parameters.pond?parameters.pond:'')
			  });
			  this.hide();
		  },
		  postCreate: function(){
				domStyle.set(this.domNode, {
					display: "none",
					position: "absolute"
				});
				this.ownerDocumentBody.appendChild(this.domNode);
				aspect.after(this, "onCancel", lang.hitch(this, "hide"), true);

				this._modalconnects = [];
		  }
	  });
});