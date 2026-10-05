// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AceManager.js,v 1.7 2022/03/07 14:27:58 qvarin Exp $

define([
	"dojo/_base/declare", 
	"dojo/_base/lang",
	"dojo/dom-construct",
    "dojo/request/xhr",
	"ace/ace",
	"ace/ext-language_tools", ], function(declare, lang, domConstruct, xhr) {
	return declare(null, {
		constructor : function() {
			this.registry = {};
			ace.config.set('basePath', 'javascript/ace')
		},
		initEditor : function(id, mode) { // Cette méthode n'est à utiliser
											// qu'avec des textarea ou des
											// inputs
			if (!mode) {
				mode = 'twig';
			}
			var node = document.getElementById(id)
			if (node) { // Un noeud porte l'identifiant
				var nodeName = node.getAttribute('name');
				var createdNode = domConstruct.create('input', {
					type : 'hidden',
					id : id,
					value : node.value,
					name : nodeName
				}, node, "after");
				var editor = ace.edit(id);
				editor.getSession().on(
						"change",
						function() {
							createdNode.setAttribute('value', editor
									.getSession().getValue());
						});

				editor.setTheme('ace/theme/eclipse');
				editor.getSession().setMode('ace/mode/' + mode);
				editor.setOptions({
					maxLines : Infinity,
					minLines : 5,
					enableBasicAutocompletion: true
					
				});
				editor.getSession().setUseWorker(true);
				editor.getSession().setUseWrapMode(true);
				this.registry[id] = editor;
				var pmbCompleter = {
					getCompletions : function(editor, session, pos, prefix, callback) {
						xhr.get("./ajax.php?module=ajax&categ=aceEditorCompletion&word=" + prefix,{
							handleAs : 'json'
						}).then(function(wordList) {
							callback(null, wordList);
						});
					}
				};
				var langTools = ace.require("ace/ext/language_tools");
				langTools.addCompleter(pmbCompleter);
			}
		},
		getEditor : function(id) {
			if (this.registry) {
				if (typeof this.registry[id] != "undefined") {
					return this.registry[id];
				}
			}
		}
	});
});