// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchControllerStatic.js,v 1.2 2021/10/25 09:38:33 tsamson Exp $

define(['apps/search/SearchController',
], function(SearchController) {
	var SearchControllerStatic = {
			instance : new SearchController(),
			
			getInstance : function() {
				if (!SearchControllerStatic.instance) {
					SearchControllerStatic.instance = new SearchController();
				}
				return SearchControllerStatic.instance;
			},
	};
	return SearchControllerStatic;
});