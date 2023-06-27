// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsEditorialTypeContentForm.js,v 1.5 2019-05-17 12:39:05 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojox/layout/ContentPane',
        'apps/pmb/gridform/cms/FormCMSEdit',
        ], function(declare, ContentPane, FormCMSEdit){
		return declare([ContentPane], {
			type:null,
			activated_grid:null,
			activated_tinymce:null,
			formCMSEdit:null,
			constructor: function(data) {
				this.type = data.type;
				this.activated_grid = data.activated_grid;
				this.activated_tinymce = data.activated_tinymce;
			},
			onLoad: function(){
				if(this.activated_grid && !this.formCMSEdit) {
					this.formCMSEdit = new FormCMSEdit('cms', this.type);
					if(this.activated_tinymce) {
						this.formCMSEdit.destroyTinymceElements();
						this.formCMSEdit.loadTinymceElements();
					}
				}
				ajax_parse_dom();
				init_drag();
			},
		})
});