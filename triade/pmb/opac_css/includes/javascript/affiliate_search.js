// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: affiliate_search.js,v 1.1 2011/05/18 14:42:58 arenou Exp $

function showSearchTab(tab,extended){
	if(extended){
		document.form_values.action = "./index.php?lvl=more_results&mode=extended&tab="+tab;
		document.form_values.submit();
	}else{	
		document.form_values.action = "./index.php?lvl=more_results&tab="+tab;
		document.form_values.submit();
	}
}