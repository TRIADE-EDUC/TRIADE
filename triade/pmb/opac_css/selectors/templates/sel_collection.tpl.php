<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_collection.tpl.php,v 1.6 2023/08/17 09:47:53 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;
global $selfrom, $p1, $p2, $p3, $p4, $p5, $p6;

if($selfrom=="rmc") {
	$jscript = $jscript_common_selector;
} else {
	if ($dyn==3) {
		$jscript = $jscript_common_authorities_unique;
	}elseif ($dyn==2) { // Pour les liens entre autorités
		$jscript = $jscript_common_authorities_link;
	}else {
		$jscript = "
		<script>
		<!--
		function set_parent(f_caller, id_coll, libelle_coll, callback, id_ed, libelle_ed){
			set_parent_value(f_caller, '".$p1."', id_ed);
			set_parent_value(f_caller, '".$p2."', libelle_ed ? reverse_html_entities(libelle_ed) : '');
			set_parent_value(f_caller, '".$p3."', id_coll);
			set_parent_value(f_caller, '".$p4."', libelle_coll ? reverse_html_entities(libelle_coll) : '');
			set_parent_value(f_caller, '".$p5."', '');
			set_parent_value(f_caller, '".$p6."', '');
		
			closeCurrentEnv();
		}
		-->
		</script>
		";
	}
}