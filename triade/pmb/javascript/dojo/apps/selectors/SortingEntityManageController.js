// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SortingEntityManageController.js,v 1.2 2020/03/06 15:50:34 tsamson Exp $

define(['dojo/_base/declare',
        'apps/frbr/EntityManageController'
], function(declare, EntityManageController) {
	//on derive de la classe utilisee dans les pages frbr pour eviter le doublon de code
	return declare([EntityManageController], {
		
		getRequestUrl: function() {
			return "ajax.php?module=selectors&what=sort&action=get_already_selected_sorting";
		},
	});
});