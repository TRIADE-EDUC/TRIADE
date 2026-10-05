// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsExpand.js,v 1.7 2018/08/24 12:24:31 tsamson Exp $


define([ 
        "dojo/_base/declare", 
        "dojo/_base/lang",
        "dojo/dom",
        "dojo/dom-construct", 
        "dojo/on", 
        "dojo/request/xhr",
        "dojo/query",
    ], function(declare,lang, dom, domConstruct, on, xhr, query){
	return declare(null, {
		module : 'cms',
		expandAllButton : null,
		collapseAllButton : null,
		context : null,
		imgPatience : pmbDojo.images.getImage('patience.gif'),
		imgOpened : pmbDojo.images.getImage('minus.gif'),
		imgClosed : pmbDojo.images.getImage('plus.gif'),
		contentAlreadyLoaded : new Array(),
		
		constructor: function(data){
			this.expandAllButton = dom.byId(data.expand_all_id);
			this.collapseAllButton = dom.byId(data.collapse_all_id);
			if (data.context) {
				this.context = dom.byId(data.context);
			} else {
				this.context = document;
			}
			this.init();
			this.initExpandBase();
		},
		
		init : function() {
			if (this.expandAllButton) {
				on(this.expandAllButton, 'click', lang.hitch(this, this.expandAll))
			}
			if (this.collapseAllButton) {
				on(this.collapseAllButton, 'click', lang.hitch(this, this.collapseAll))
			}
		},
		
		initExpandBase : function() {
			query('.img_plus', this.context).forEach(
				lang.hitch(this,function(node) {
					if (node.getAttribute("data")) {
						var data = JSON.parse(node.getAttribute("data"));
						on(node,'click', lang.hitch(this, this.expandBase, node, data));
					}
				}
			));
		},
		
		getAjaxContent : function(data) {
			var nodeChild = dom.byId(data.domId + "Child");
			var divPatience = domConstruct.create("div", {"style" : {"width":"100%", "height" : "30px", "text-align" : "center"}}, nodeChild);
			var imgPatience = domConstruct.create("img", {"src" : this.imgPatience, "style" : "padding 0 auto;", "border" : "0"}, divPatience);
			xhr.post('./ajax.php?module='+this.module+'&categ='+data.categ+'&action='+data.action+'&id='+data.id, {
				data: {
					expand_params : JSON.stringify(data.params),
				},
				handleAs: 'text'
			}).then(lang.hitch(this, function(content){
				var content_dom = domConstruct.toDom(content);
				domConstruct.place(content_dom, nodeChild, "only");
				preLoadScripts(nodeChild);
			}));
		},
		
		expandAll : function() {
			query('.img_plus', this.context).forEach(
				function (node) {
					var nodeChild = dom.byId(node.id.replace('Img','')+'Child');
					if (nodeChild && nodeChild.style.display == 'none') {
						node.click();
					}
				}
			);
		},
		
		collapseAll : function() {
			query('div[class~="notice-child"]', this.context).forEach(
				function(node) {
					node.style.display = 'none';
				}
			);
			query('div[class~="child"]', this.context).forEach(
				function(node) {
					node.style.display = 'none';
				}
			);
			query('.img_plus', this.context).forEach(
				lang.hitch(this,function(node) {
					node.src = this.imgClosed;
				})
			);
		},
		
		expandBase : function (node, data) {
			var nodeChild = dom.byId(data.domId + "Child");
			if (nodeChild.style.display == "none") {
				nodeChild.style.display = "block";
				node.src = this.imgOpened;
				this.getAjaxContent(data);
				if (dom.byId('documents_selected_'+data.id)) {
					domConstruct.destroy('documents_selected_'+data.id);
				}
			} else {
				nodeChild.style.display = "none";
				node.src = this.imgClosed;
			}
		}
		
	});
});