// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordLine.js,v 1.21 2020/05/11 08:55:45 dgoron Exp $


define(["dojo/_base/declare", "dijit/_WidgetBase", "dojo/request/xhr", "dojo/_base/lang", "dojo/topic", "dojo/dom-construct", "dojo/dom-attr", "dijit/registry", "dojo/on", "apps/scan_request/ExplnumList", "dojo/dom", "snet/fileUploader/Uploader", "dojo/dom-style", "dojo/dom-class"], function(declare, WidgetBase, xhr, lang, topic, domConstruct, domAttr, registry, on, ExplnumList, dom, Uploader, domStyle, domClass){

	  return declare([WidgetBase], {
		  class: "recordLine",
		  labelInput: null,
		  popupButton: null,
		  explnumList: null,
		  recordSelector: null,
		  plusImg: null,
		  purgeButton: null,
		  parentDiv: null, 
		  childDiv: null,
		  lineData: null,
		  commentTextarea: null,
		  uploader: null,
		  id: 0,
		  requestId: null,
		  codeInput:null,
		  recordLink: null,
		  preventKeyPress: null, //Signal
		  state: 0, // �tat de la ligne -> 0 pas de documents num�rique, toute compl�tion active ; 1 -> au moins un docnum, pas de completion
		  constructor:function(params){
			  if(document.getElementsByName('id')[0].value != 0 && document.getElementsByName('id')[0].value != ''){
				  this.requestId = parseInt(document.getElementsByName('id')[0].value);
			  }else{
				  this.requestId = 0;
			  }
			  this.state = 0;
			  this.own(topic.subscribe('ElementsContainer', lang.hitch(this, this.handleEvents)));
			  this.own(topic.subscribe('ExplnumList', lang.hitch(this, this.handleEvents)));
			  this.recordLink = params.data.permalink;
		  },
		  handleEvents: function(evtType, evtArgs){
			  switch(evtType){
				case 'explnumUpdated':
					if(this.explnumList && (domAttr.get(this.explnumList.domNode, 'id')) == evtArgs.widgetId){
						this.updateState();
					}
					break;
			}  
		  },
		  postCreate:function(){
			  this.inherited(arguments);
			  this.parentDiv = domConstruct.create('div', {id:'scan_request_record_'+this.index+'_Parent', 'class':'notice-parent'}, this.domNode);
			  this.plusImg = domConstruct.create('img', {id:'scan_request_record_'+this.index+'_Img','class':'img_plus', style:{'vertical-align':'middle'}, border:0, hspace:3, src: pmbDojo.images.getImage('plus.gif'),name:'imEx',title:''}, this.parentDiv);
			  
			  on(this.plusImg, 'click', lang.hitch(this, function(){expandBase('scan_request_record_'+this.index+'_', true); return false;}));
			  
			  domConstruct.place(domConstruct.toDom(' '), this.parentDiv);
			  var spanContainer = domConstruct.create('span', {width:'368px', 'class':'notice-heada'}, this.parentDiv);
			  if(!this.params.readOnly && this.editable){ 
				  var readonly = false;
				  this.labelInput = domConstruct.create('input', {id:'scan_request_record_'+this.index,	value:this.data.label,'class':'saisie-30emr',	type:'text', autocomplete:'off', name:'scan_request_record_label[]', completion:'notice', autfield: 'scan_request_record_code_'+this.index}, spanContainer);
				  ajax_pack_element(this.labelInput);
				  this.recordSelector = domConstruct.create('input', {id:'scan_request_record_'+this.index+'_selector', 'class':'bouton', type:'button', value:'...'}, this.parentDiv);
				  on(this.recordSelector, 'click', lang.hitch(this, function(e){
					  openPopUp('./select.php?what=notice&caller=scan_request_form&param1=scan_request_record_code_'+this.index+'&param2=scan_request_record_'+this.index+'&dyn=1&no_display=0', 'selector_notice');
				  }));
				  this.purgeButton = domConstruct.create('input', {value:'X', type:'button', 'class':'bouton'}, this.parentDiv);
				  on(this.purgeButton, 'click', lang.hitch(this, this.purgeList));
				  if(this.data.id){
					  this.seeRecord = domConstruct.create('input', {type:'button', class:'bouton', value:pmbDojo.messages.getMessage('scan_request', 'scan_request_see_record')},this.parentDiv);
					  on(this.seeRecord, 'click', lang.hitch(this, this.seeRecordCallback));
				  }
		  	  }	else{
				  var readonly=true;
		  		  //this.labelInput = domConstruct.create('input', {id:'scan_request_record_'+this.index,	value:this.data.label,'class':'saisie-30emr',	type:'text', autocomplete:'off', name:'scan_request_record_label[]', autfield: 'scan_request_record_code_'+this.index , readonly:readonly}, spanContainer);
				  this.labelInput = domConstruct.create('a', {id:'scan_request_record_'+this.index,	href:this.recordLink, innerHTML:this.data.label}, spanContainer);
				  if(this.data.id){
					  domConstruct.place(domConstruct.toDom(' '), spanContainer);
					  this.copyRecordTitle = domConstruct.create('input', {type:'button', class:'bouton', value:pmbDojo.messages.getMessage('scan_request', 'scan_request_copy_record_title')},spanContainer);
					  on(this.copyRecordTitle, 'click', lang.hitch(this, function() {record_title_copy(this.data.label);}));
				  }
		  	  }		
			  
		      this.codeInput = domConstruct.create('input', {type:'hidden', id:'scan_request_record_code_'+this.index, name:'scan_request_record_code[]', value:this.data.id}, this.parentDiv);
			  //Div enfant
			  this.childDiv = domConstruct.create('div', {id:'scan_request_record_'+this.index+'_Child', 'class':'notice-child', style:{display:'none'} }, this.domNode);
			  var col1 = domConstruct.create('div', {'class':'colonne3'}, this.childDiv);
			  var col2 = domConstruct.create('div', {'class':'colonne3'}, this.childDiv);
			  var col3 = domConstruct.create('div', {'class':'colonne3'}, this.childDiv);
			  domConstruct.create('div', {'class':'row'}, this.childDiv);
			  
			  if(readonly){
				  this.commentTextarea = domConstruct.create('p', {id:'scan_request_record_comment_'+this.index, innerHTML: this.data.comment}, col1);
				  var commentHidden = domConstruct.create('input', {type: 'hidden', name:'scan_request_record_comment[]', value: this.data.comment}, col1);
			  }else{
				  this.commentTextarea = domConstruct.create('textarea', {id:'scan_request_record_comment_'+this.index, name:'scan_request_record_comment[]', innerHTML: this.data.comment, readonly:readonly}, col1);  
			  }  
			  if(this.requestId && this.data.id){
				  var url = './ajax.php?module=circ&categ=scan_request&sub=upload&num_request='+this.requestId+'&num_record='+this.data.id;
				  if(dom.byId('scan_request_concept_uri_value')) {
					  url += '&concept_uri='+encodeURIComponent(dom.byId('scan_request_concept_uri_value').value);
				  }
				  var dropZone = domConstruct.create('div', {id:'scan_request_record_file_drop_'+this.index, 'class':'dropTarget document_item', 'data-num_record':this.data.id, style:{width:'50%'}}, col2);
				  domConstruct.create('p', {style:{'pointer-events': 'none'}, innerHTML: pmbDojo.messages.getMessage('scan_request', 'scan_request_drag_files_here')},dropZone)
				  var expllistDiv = domConstruct.create('div', {}, col3);
				  this.explnumList = new ExplnumList(((this.data.explnums)?{explnumObjects:this.data.explnums}:{}),expllistDiv);	
				  this.uploader = new Uploader({
						url: url,
						dropTarget: dropZone,
						maxKBytes: pmbDojo.uploadMaxFileSize,
						maxNumFiles: 10,
						requestCallback: lang.hitch(this.explnumList, this.explnumList.addLine),
						onDropCallback: lang.hitch(this, this.onDropCallback)
				  });
				  this.updateState();
			  }			  
		  },
		  updateState: function(){
			  //console.log('update state called', this.explnumList.getNbExpl(), this.state);
			  var nbExpl = this.explnumList.getNbExpl();
			  if(!this.params.readOnly && this.editable){
				  if(this.state == 0 && nbExpl){ //Nous n'avions pas de document num�rique ; maintenant nous en avons
					  this.state = 1;
					  this.disallowEdit();
				  }
				  if(this.state == 1 && !nbExpl){ //Nous avions des documents num�rique et nous n'en avons plus
					  this.state = 0;
					  this.allowEdit();
				  }
			  }
		  },
		  allowEdit: function(){
			  domStyle.set(this.recordSelector, 'display', '');
			  domStyle.set(this.purgeButton, 'display', '');
			  this.preventKeyPress.remove();
			  this.preventKeyPress = null;
		  },
		  disallowEdit: function(){
			  domStyle.set(this.recordSelector, 'display', 'none');
			  domStyle.set(this.purgeButton, 'display', 'none');
			  this.preventKeyPress = on(this.labelInput, 'keypress', function(e){e.preventDefault()});
		  },
		  purgeList: function(){
			  this.commentTextarea.innerHTML = '';
			  this.labelInput.value = '';
			  this.codeInput.value = '';
			  if(this.uploader){
				  domStyle.set(this.uploader.dropTarget, 'display', 'none');
				  domStyle.set(this.explnumList.domNode, 'display', 'none');
			  }
			  if(this.seeRecord){
			  	domStyle.set(this.seeRecord, 'display', 'none');
			  }
		  },
		  onDropCallback: function(evt){
			  var url = './ajax.php?module=circ&categ=scan_request&sub=upload&num_request='+this.requestId+'&num_record='+this.codeInput.value;
			  if(dom.byId('scan_request_concept_uri_value')) {
				  url += '&concept_uri='+encodeURIComponent(dom.byId('scan_request_concept_uri_value').value);
			  }
			  this.uploader.changeUrl(url);	
			  var files = evt.dataTransfer.files;
			  this.uploader.reset();
			  this.uploader.addFiles(files);
			  domClass.remove(this.uploader.dropTarget, 'targetActive');
		  },
		  seeRecordCallback: function(){
		  	window.open(this.recordLink, '_blank');
		  }
	  });
});