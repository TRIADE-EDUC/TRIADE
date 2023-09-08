// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AceManager.js,v 1.4 2019-02-18 16:48:33 apetithomme Exp $

define([
     "dojo/_base/declare",
     "dojo/_base/lang",
     "dojo/dom-construct",
], function(declare, lang, domConstruct){
	return declare(null, {
	  constructor:function(){
			this.registry = {};
		  },
	  initEditor: function(id, mode){ //Cette méthode n'est à utiliser qu'avec des textarea ou des inputs
		  if (!mode) {
			  mode = 'twig';
		  }
		  var node = document.getElementById(id)
		  if(node){ //Un noeud porte l'identifiant
			  var nodeName = node.getAttribute('name');
			  var createdNode = domConstruct.create('input', {type: 'hidden', id:id, value : node.value, name:nodeName}, node, "after");
			  var editor = ace.edit(id);
			  editor.getSession().on("change", function () {
				  createdNode.setAttribute('value',editor.getSession().getValue());
		  	  });
			  
			  editor.setTheme('ace/theme/eclipse');
			  editor.getSession().setMode('ace/mode/'+mode);
			  editor.setOptions({
				  maxLines: Infinity,
				  minLines: 5
			  });
			  editor.getSession().setUseWorker(true);
			  this.registry[id] = editor;
		  }
	  },
	  getEditor: function(id){
		  if(this.registry){
			  if(typeof this.registry[id] != "undefined"){
				  return this.registry[id];
			  }
		  }
	  }
	});
});