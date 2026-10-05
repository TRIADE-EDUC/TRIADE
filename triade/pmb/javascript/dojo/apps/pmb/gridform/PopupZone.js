// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PopupZone.js,v 1.2 2017/09/05 12:42:33 vtouchard Exp $


define(['dojo/_base/declare', 
        'dojo/_base/lang', 
        'dojo/topic', 
        'dojo/dom-style', 
        'apps/pmb/PMBConfirmDialog', 
        'dojo/aspect'], 
        function(declare, lang, topic, domStyle, ConfirmDialog, aspect){
	  return declare([ConfirmDialog], {
		  label:null,
		  isExpandable:null,
		  showLabel:null,
		  mode:null,
		  zoneId: null,
		  constructor:function(params){
			  //console.log('popup', this);
			  this.draggable = true;
			  this.closable = false;
			  this.zoneId = params.zoneId;
			  this.label = (params.label ? params.label : '');
			  this.isExpandable = (params.isExpandable ? params.isExpandable : false);
			  this.showLabel = (params.showLabel == false ? false : true);
			  
			  this.mode = (params.mode ? params.mode : 'create');
			  this.title = pmbDojo.messages.getMessage('grid', 'grid_js_move_popup_zone_create');
			  this.content = 
		            '<label for="label">'+pmbDojo.messages.getMessage('grid', 'grid_js_move_popup_zone_label')+'</label>&nbsp;<input data-dojo-type="dijit/form/ValidationTextBox" required="true" id="label" name="label" value="'+this.label+'" placeholder="'+pmbDojo.messages.getMessage('grid', 'grid_js_move_popup_zone_label_placeholder')+'"><br><br>' +
		            '<label for="isExpandable">'+pmbDojo.messages.getMessage('grid', 'grid_js_move_popup_zone_is_expandable')+'</label>&nbsp;<input id="isExpandable" name="isExpandable" value="1" '+(this.isExpandable ? 'checked' : '')+' data-dojo-type="dijit/form/CheckBox" onChange=\'if(this.checked) dijit.byId("showLabel").setChecked(true);\'/><br/><br>'+
		            '<label for="showLabel">'+pmbDojo.messages.getMessage('grid', 'grid_js_move_popup_zone_show_label')+'</label>&nbsp;<input id="showLabel" name="showLabel" value="1" '+(this.showLabel ? 'checked' : '')+' data-dojo-type="dijit/form/CheckBox" onChange=\'if(dijit.byId("isExpandable").checked) this.setChecked(true);\'/><br><br>'
            this.style = 'width:400px';
			  
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
			  if(!this.value.label || this.value.label == ''){
				  return false;
			  } 
			  var parameters = this.value;
			  topic.publish('PopupZone', this.mode+'Zone', {
				  label:(parameters.label?parameters.label:''), 
				  isExpandable:(parameters.isExpandable.length?true:false),
				  showLabel:(parameters.showLabel.length?true:false),
				  zoneId: this.zoneId
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