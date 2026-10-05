// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ExplnumList.js,v 1.7 2018/03/29 14:46:17 tsamson Exp $


define(["dojo/_base/declare", "dijit/_WidgetBase", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic", "dojo/dom-construct", "dojo/dom-attr", "dijit/registry", "dojo/on"], function(declare, WidgetBase, xhr, lang, topic, domConstruct, domAttr, registry, on){

	  return declare([WidgetBase], {
		  explnumList: null,
		  tableNode: null,
		  deleteUrl: "./catalog.php?categ=del_explnum&explnum_id=",
		  seeUrl: "./doc_num.php?explnum_id=",
		  editUrlRecord: "./catalog.php?categ=edit_explnum&id=",
		  editUrlBulletin: "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=",
		  constructor:function(params){
			 //console.log(arguments, params, 'params explnumlist');
		  },
		  postCreate:function(){
			  this.inherited(arguments);
			  this.explnumList = {};
			  this.tableNode = domConstruct.create('table', {style: 'width:100%'}, this.domNode);
			  var thContainerNode = domConstruct.create('tr', null, this.tableNode);
			  var titleTh = domConstruct.create('th', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_name')}, thContainerNode);
			  var typeTh = domConstruct.create('th', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_type')}, thContainerNode);
			  var seeTh =  domConstruct.create('th', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_see')}, thContainerNode);
			  var deleteTh = domConstruct.create('th', {innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_delete')}, thContainerNode);
			  if(this.params.explnumObjects && this.params.explnumObjects.length){
				  for(var i=0; i<this.params.explnumObjects.length ; i++){
					  this.addLine(this.params.explnumObjects[i]);
				  }
			  }
		  },
		  addLine: function(jsonData){
			  this.explnumList[jsonData.id] = {};
			  this.explnumList[jsonData.id].data = jsonData;
			  this.explnumList[jsonData.id].tr = domConstruct.create('tr', null, this.tableNode);
			  
			  if(parseInt(jsonData.record_id)){
				  domConstruct.create('td', {innerHTML: '<a title="'+pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_edit_label')+'" target="_blank" href="'+this.editUrlRecord+jsonData.record_id+'&explnum_id='+jsonData.id+'">'+jsonData.title+'</a>'}, this.explnumList[jsonData.id].tr);
			  }else if(parseInt(jsonData.bulletin_id)){
				  domConstruct.create('td', {innerHTML: '<a title="'+pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_edit_label')+'" target="_blank" href="'+this.editUrlBulletin+jsonData.bulletin_id+'&explnum_id='+jsonData.id+'">'+jsonData.title+'</a>'}, this.explnumList[jsonData.id].tr); 
			  }
			 
			  domConstruct.create('td', {innerHTML: jsonData.type}, this.explnumList[jsonData.id].tr);
			  var tdSee = domConstruct.create('td', {style: {textAlign:'center', cursor:'pointer'},innerHTML: '<i class="fa fa-eye fa-1"></i>'}, this.explnumList[jsonData.id].tr);
			  var tdDelete = domConstruct.create('td', {style: {textAlign:'center', cursor:'pointer'}, innerHTML: '<i class="fa fa-trash fa-1"></i>'}, this.explnumList[jsonData.id].tr);
			  on(tdDelete, "click", lang.hitch(this, this.deleteCallback, jsonData.id, jsonData.record_id));
			  on(tdSee, "click", lang.hitch(this, this.seeCallback, jsonData.id));
			  topic.publish('ExplnumList', 'explnumUpdated', {widgetId: domAttr.get(this.domNode, 'id'), elementAdded: jsonData});
			  return this.explnumList[jsonData.id];
		  },
		  removeLine: function(explnumId){
			  if(this.explnumList[explnumId]){
				  domConstruct.destroy(this.explnumList[explnumId].tr);
			  }
			  delete this.explnumList[explnumId];
			  topic.publish('ExplnumList', 'explnumUpdated', {widgetId: domAttr.get(this.domNode, 'id'), elementRemoved: explnumId});
		  },
		  seeCallback: function(explnumId){
			window.open(this.seeUrl+explnumId, '_blank');
		  },
		  deleteCallback: function(explnumId, record_id){
			if(confirm(pmbDojo.messages.getMessage('scan_request', 'scan_request_explnum_delete_alert').replace('!!docnum_title!!', this.explnumList[explnumId].data.title))){
				 xhr(this.deleteUrl+explnumId+"&id="+record_id, {
						sync:true,
				  }).then(lang.hitch(this, function(){
					  this.removeLine(explnumId);	  
				  }));  
			}  
		  },
		  getNbExpl: function(){
			  var i = 0;
			  for(var key in this.explnumList){
				  i++;
			  }
			  return i;
		  }
	  });
});