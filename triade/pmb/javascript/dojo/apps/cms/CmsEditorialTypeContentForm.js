// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsEditorialTypeContentForm.js,v 1.6 2019/08/08 08:22:37 dgoron Exp $


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
				document.body.dispatchEvent(new Event('movestart'));
				if(this.activated_grid && !this.formCMSEdit) {
					this.formCMSEdit = new FormCMSEdit('cms', this.type);
					if(this.activated_tinymce) {
						this.formCMSEdit.destroyTinymceElements();
						this.formCMSEdit.loadTinymceElements();
					}
				}
				ajax_parse_dom();
				init_drag();
				document.body.dispatchEvent(new Event('moveend'));
			},
		})
});