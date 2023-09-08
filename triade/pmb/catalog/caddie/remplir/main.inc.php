<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2019-06-05 09:04:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $type, $msg, $catalog_layout, $charset, $PMBuserid, $elements, $search_xml_file, $search_xml_file_full_path, $callback;

if (empty($type)) $type = "NOTI";
$table_key = "notice_id";
if ($type == "EXPL") $table_key = "expl_id";

$catalog_layout = str_replace('<!--!!sous_menu_choisi!! -->', $msg["caddie_menu_remplir"], $catalog_layout);
print $catalog_layout ;
print '<div class="row"><div class="msg-perio">'.$msg['caddie_creation_in_progress'].'</div></div>';
$caddie = new caddie();
$caddie->type = $type;
$caddie->name = date($msg['1005']." H:i:s - ").html_entity_decode(strip_tags($_SESSION['session_history'][$_SESSION['CURRENT']]['QUERY']['HUMAN_QUERY']), ENT_COMPAT | ENT_HTML401, $charset);
$caddie->autorisations = $PMBuserid;
$caddie->classementGen = $msg['caddie_classement_created_from_search'];
$id_caddie = $caddie->create_cart();

$values = array();
if (!empty($elements)) {
	$elements = explode(",", $elements);
	foreach ($elements as $element) {
		$values[] = "('$id_caddie', '$element', '')";
	}
} else {
	$requete = $_SESSION['session_history'][$_SESSION['CURRENT']][$type]['TEXT_QUERY'];
	if (!empty($requete)) {
		if (!empty($_SESSION['session_history'][$_SESSION['CURRENT']][$type]["TEXT_LIST_QUERY"])) {
			foreach($_SESSION['session_history'][$_SESSION['CURRENT']][$type]["TEXT_LIST_QUERY"] as $query) {
				pmb_mysql_query($query);
			}
		}
		$p = stripos($requete, "limit");
		if ($p) {
			$requete = substr($requete, 0, $p);
		}
	} else {
	    if(!empty($_SESSION['session_history'][$_SESSION['CURRENT']][$type]['FORM_VALUES'])){
	        $sh = new searcher_records_tab($_SESSION['session_history'][$_SESSION['CURRENT']][$type]['FORM_VALUES']);
	        $notices = $sh->get_result();
	        $requete = "select $table_key from notices where $table_key in ($notices)";
	    }else{
    		foreach ($_SESSION['session_history'][$_SESSION['CURRENT']]['QUERY']['POST'] as $varname => $value) {
    			global ${$varname};
    			${$varname} = $value;
    		}
    		$sh = new search(false, $search_xml_file, $search_xml_file_full_path);
    		$table = $sh->make_search();
    		$requete = "select * from $table";
	    }
	}
	$result = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($result)) {
		while ($row = pmb_mysql_fetch_assoc($result)) {
			$values[] = "('$id_caddie', '".$row[$table_key]."', '')";
		}
	}
}
if (!empty($values)) {
	pmb_mysql_query("insert into caddie_content (caddie_id, object_id, content) VALUES ".implode(",", $values));
}

print '<script>document.location = "'.str_replace("!!id_caddie!!", $id_caddie, $callback).'";</script>';
