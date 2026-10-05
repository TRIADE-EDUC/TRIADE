<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: simple_search.inc.php,v 1.144 2019/01/11 09:03:16 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// recherche simple
require_once($base_path."/classes/marc_table.class.php");
require_once($base_path."/includes/javascript/form.inc.php");
require_once($base_path."/includes/empr.inc.php");
require_once($class_path."/search.class.php");
require_once($class_path."/thesaurus.class.php");
require_once($class_path."/search_persopac.class.php");
require_once($base_path."/classes/perio_a2z.class.php");
require_once($base_path."/classes/authperso.class.php");
require_once($class_path."/skos/skos_concept.class.php");
require_once($class_path."/search_view.class.php");

function get_sources() {
	global $msg;

	$sources = array();
	$sources_no_category = array();
	//Recherche des sources
	$query = "SELECT connectors_categ_sources.num_categ, connectors_sources.source_id, connectors_categ.connectors_categ_name as categ_name, connectors_categ.opac_expanded, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed,connectors_sources.opac_selected, source_sync.cancel FROM connectors_sources LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2) WHERE connectors_sources.opac_allowed=1 ORDER BY connectors_categ.connectors_categ_name, connectors_sources.name ";
	$result = pmb_mysql_query($query);
	while ($srce=pmb_mysql_fetch_object($result)) {
		if ($srce->categ_name) {
			$sources[$srce->categ_name][] = $srce;
		} else {
			$sources_no_category[] = $srce;
		}
	}
	if(count($sources_no_category)) {
		$sources[$msg["source_no_category"]] =$sources_no_category;
	}
	return $sources;
}

function do_sources() {
	global $charset,$source, $dbh, $msg;
	$r="";
	if (!$source) $source=array();
	if (count($source)) $_SESSION["checked_sources"]=$source;
	if (!isset($_SESSION["checked_sources"])) $_SESSION["checked_sources"] = '';
	if ($_SESSION["checked_sources"]&&(!$source)) $source=$_SESSION["checked_sources"];
	
	$count = 0;
	$paquets_de_sources = array();
	
	//Recherche des sources
	$sources = get_sources();
	foreach ($sources as $category_name=>$category) {
		if (isset($paquets_de_source) && $paquets_de_source) $paquets_de_sources[] = $paquets_de_source;
		$paquets_de_source = array();
		$paquets_de_source["name"] = $category_name;
		$paquets_de_source["content"] = '';
		
		 
		// gen_plus_form("zsources".$count, $srce->categ_name ,"sdfsdfsdfsdf",true);
		$count++;
		foreach ($category as $srce) {
		    $paquets_de_source["id"] = $srce->num_categ;
		    $paquets_de_source["opac_expanded"] = $srce->opac_expanded ? true : false;
		    
			if(!isset($_SESSION["source_".$srce->source_id."_cancel"])) $_SESSION["source_".$srce->source_id."_cancel"] = 0;
			$paquets_de_source["content"] .="<div style='width:30%; float:left'>
				<input type='checkbox' ".($_SESSION["source_".$srce->source_id."_cancel"]==2 ? 'DISABLED' : "")." name='source[]' value='".$srce->source_id."' id='source_".$srce->source_id."_".$count."' onclick='change_source_checkbox(source_".$srce->source_id."_".$count.", ".$srce->source_id.");'";
			if (array_search($srce->source_id,$source)!==false) {
				$paquets_de_source["content"] .= " checked";
			} else if (!count($source) && $srce->opac_selected) {
				$paquets_de_source["content"] .= " checked";
			}
			$paquets_de_source["content"] .= "/>".($_SESSION["source_".$srce->source_id."_cancel"]==2 ? "<s>" : "")."<label for='source_".$srce->source_id."_".$count."'><img src='".($srce->repository==1?get_url_icon("entrepot.png"):get_url_icon("globe.gif"))."'/>&nbsp;".htmlentities($srce->name.($srce->comment?" : ".$srce->comment:""),ENT_QUOTES,$charset).($_SESSION["source_".$srce->source_id."_cancel"]==2 ? "</s> <i>(".$msg["source_blocked"].")</i>" : "")."</label>
			</div><div class='row'></div>";
		}
	}
	if (isset($paquets_de_source) && $paquets_de_source) $paquets_de_sources[] = $paquets_de_source; 
    foreach($paquets_de_sources as $paquets_de_source) {
    	$r .= gen_plus_form("zsources".$paquets_de_source["id"], $paquets_de_source["name"], $paquets_de_source["content"], $paquets_de_source["opac_expanded"])."\n\n";
    }
   	
   	return $r;
}

function decale($var,$var1) {
	global ${$var};
	global ${$var1};
	${$var1}=${$var};
}