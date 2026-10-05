// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authForm.js,v 1.7 2021/09/06 13:32:22 dgoron Exp $


define(["dojo/_base/declare", "dojo/dom", "dojo/on", "dojo/topic", "dojo/_base/lang","dijit/form/TextBox", "dijit/form/Button", "dojo/dom-construct"], function(declare, dom, on, topic, lang, TextBox, Button, domConstruct){
	
	return declare([TextBox], {
		
		templateString:'<div class="dijit dijitReset dijitInline dijitLeft" id="widget_${id}" role="presentation" style="width:auto !important;"><div class="dijitReset dijitInputField dijitInputContainer">	<form name="form_${id}" ><input class="dijitReset dijitInputInner" data-dojo-attach-point="textbox,focusNode" autocomplete="off" ${!nameAttrSetting} type="${type}"  style="display:none;width:20em !important;"/>        <input id="f_${id}0" type="text" name="f_${id}0" style="width:20em" completion="${completion}" autfield="f_${id}_id0">   <input id="f_${id}_id0" type="hidden" value="" name="f_${id}_id0"><input id="max_${id}" type="hidden" value="1" name="max_${id}"><div id="button_open_popup_${id}"></div> <div id="button_delete_${id}0"></div> <div id="button_add_${id}"></div> <div id="add_part_${id}"></div></form> </div></div>',
		constructor:function(){
			
		},
		postCreate: function(){
			this.inherited(arguments);
			
			this.own( new Button({
				label: "...",
			}, "button_open_popup_"+this.id).on('click', lang.hitch(this,this.selector)));

			this.own( new Button({
				label: "X",
			}, "button_delete_"+this.id+"0").on('click', lang.hitch(this,this.raz,"0")));
			
			this.own( new Button({
				label: "+",
			}, "button_add_"+this.id).on('click', lang.hitch(this,this.add,"")));

			if(this.data.length) {
				for(var i=0; i<this.data.length; i++){
					if(i==0){
						dojo.byId("f_"+this.id+"0").value=this.data[0].label;
						dojo.byId("f_"+this.id+"_id0").value=this.data[0].id;
					} else {
						this.add(this.data[i]);
					}
				}
			} else {
				if(dojo.byId("f_"+this.id+"0")) {
					ajax_pack_element(dojo.byId("f_"+this.id+"0"));
				}
			}
			
			// this.add_function ...
			if(!window.add_categ){
				window.add_categ=lang.hitch(this,this.add,"");
			}
			//function callback
			if(!window.callback_categ){
				window.callback_categ=lang.hitch(this,this.authChange,"");
			}
		},	
		/*
		id: "categ",
		what_sel: "select_categ",
		completion: "categories_mul",
		selectUrl: "./select.php?what=categorie&dyn=1",
		inputIdUrl: "p1",
		inputNameUrl: "p2",
		data: data.item.categs
		*/
		selector:function(){	
			var name_id="xx";
			var name="xx";
			var select_prop= 'scrollbars=yes, toolbar=no, dependent=yes, resizable=yes';
			openPopUp(this.selectUrl+'&caller=form_'+this.id+'&'+this.inputIdUrl+'='+name_id+'&'+this.inputNameUrl+'='+name+'&callback='+this.callback, 'selector');		 
		},
		
		raz: function (index) {			
			dojo.byId("f_"+this.id+index).value="";
			dojo.byId("f_"+this.id+"_id"+index).value="";
			topic.publish('autForm',"autFormChange",{action:"raz"});
		},
		
		add: function (data) {		
			var suffixe=dojo.byId("max_"+this.id).value;
			var val_id="";
			var val_label="";
			
			if(data){
				val_id=data.id;
				val_label=data.label;	
			}
			if(data==""){ // appel issu du selecteur et bouton +
				topic.publish('autForm',"autFormChange",{action:"add"});				
			}
			domConstruct.place('<div class="row" id="row_'+this.id+suffixe+'"> </div>', "add_part_"+this.id);			
			var input_name =domConstruct.create("input",{
				id:"f_"+this.id+suffixe,
				name:"f_"+this.id+suffixe,
				value:val_label,
				type:"text",
				style:"width:20em",
				completion:this.completion,
				autfield:"f_"+this.id+"_id"+suffixe			
			});
			var input_name_id =domConstruct.create("input",{
				id:"f_"+this.id+"_id"+suffixe,
				name:"f_"+this.id+"_id"+suffixe,
				value:val_id,
				type:"hidden",
				onChange:lang.hitch(this,this.authChange)
			});
			
			domConstruct.place(input_name, "row_"+this.id+suffixe);
			domConstruct.place(input_name_id, "row_"+this.id+suffixe);
			domConstruct.place("<div id='button_delete_"+this.id+suffixe+"'></div>", "row_"+this.id+suffixe);
			
			this.own( new Button({
				label: "X",
			}, "button_delete_"+this.id+suffixe).on('click', lang.hitch(this,this.raz,suffixe)));		
			
			dojo.byId("max_"+this.id).value=suffixe*1+1*1;
			
			ajax_pack_element(dojo.byId("f_"+this.id+suffixe));
	    },
	    authChange: function (){
			topic.publish('autForm',"autFormChange",{action:"authChange"});
	    },
	    get_data: function (){
			var index=0;
			var data= new Array();
			if(dojo.byId("max_"+this.id)) {
				var nb=dojo.byId("max_"+this.id).value;
				for(var i=0; i<nb; i++){				
					//var label=dojo.byId("f_"+this.id+i).value;
					var id=dojo.byId("f_"+this.id+"_id"+i).value;
					if(id){					
						data[index]=id;
						index++;
					}
				}
			}
			return (data);
	    }
	});
});