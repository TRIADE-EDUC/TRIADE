<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: tri.inc.php,v 1.10 2024/03/05 10:23:36 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $quoifaire, $notices_relations_ids;

require_once($class_path."/notice_relations.class.php");

switch($quoifaire){	
	case 'up_order' :
		update_order($notices_relations_ids);	
		break;	
	case 'up_order_avis' :
		update_order_avis();	
		break;
	case 'up_order_search_perso' :
		update_order_search_perso();
		break;
}

function update_order_avis(){	
	global $tablo_avis;

	$liste_avis = explode(",",$tablo_avis);
	for($i=0;$i<count($liste_avis);$i++){
		$rqt = "update avis set avis_rank='".$i."' where id_avis='".$liste_avis[$i]."' ";
		pmb_mysql_query($rqt);
	}
}

function update_order($notices_relations_ids){
	$list = explode(",",$notices_relations_ids);
	for($i=0;$i<count($list);$i++){
		notice_relations::update_ranking($list[$i], $i);
	}
}

function update_order_search_perso(){
	global $tab_search_perso;

	$liste_search_perso = explode(",",$tab_search_perso);
	for($i=0;$i<count($liste_search_perso);$i++){
		$query = "update search_perso set search_order='".$i."' where search_id='".$liste_search_perso[$i]."' ";
		pmb_mysql_query($query);
	}
}
?>