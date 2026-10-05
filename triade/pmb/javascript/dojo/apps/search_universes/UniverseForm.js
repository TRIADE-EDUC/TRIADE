// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UniverseForm.js,v 1.5 2020/10/16 13:02:33 qvarin Exp $


define([
        "dojo/_base/declare",
        "apps/search_universes/EntityForm",
        "dojo/request",
        "dojo/_base/lang",
        "dojo/topic",
        "dojo/query",
        "dojo/on",
        "dojo/dom-attr"
        ], 
		function(declare, EntityForm, request, lang, topic, query, on, domAttr){
	return declare(EntityForm, {
		addSegment : function(params) {
			var url = 'ajax.php?module=admin&categ=search_universes&sub=segment&action=edit&universe_id='+params.entity_id+'&id=0';
			request.get(url, {
				handleAs : 'html'
			}).then(lang.hitch(this, function(html) {
				var data = {};
				data.html = html;
				data.addNewEntity = true;
				topic.publish('formButton', 'loadNewContent', data);
			}),function(err){
				alert(pmbDojo.messages.getMessage('search_universes', 'search_segment_set_not_save'));
			});
		},
		init: function(){
			this.inherited(arguments);
			query('table.universe_segments_table td').forEach((node)=>{
				if (domAttr.get(node, 'onClick')) {
					domAttr.remove(node, 'onClick');
				}
				
				var segementId = domAttr.get(node, 'segmentId');
				if (!segementId) {
					segementId = domAttr.get(node.parentNode, 'segmentId');
				}
				on(node, "click", lang.hitch(this, function(e) {
					request.get('ajax.php?module=admin&categ=search_universes&sub=segment&action=edit&id='+segementId, {
						handleAs : 'html'
					}).then(lang.hitch(this, function(html) {
						var data = {};
						data.html = html;
						data.addNewEntity = true;
						topic.publish('formButton', 'loadNewContent', data);
						topic.publish('formButton', 'selectSegementNode', {'universeId': this.id, 'segementId': segementId});
					}));
				}))
			});
		}
	});
});