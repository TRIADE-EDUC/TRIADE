<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.24.4.1 2025/01/24 16:34:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $page, $msg, $pmb_map_activate, $pmb_extended_search_dnd_interface;
global $reinit_facettes, $param_delete_facette, $check_facette;
global $reinit_compare;
global $num_facettes_set;

require_once($class_path."/search.class.php");
require_once($class_path."/search_perso.class.php");

switch ($sub) {
    case "launch":
        $num_facettes_set = intval($num_facettes_set);
        if($num_facettes_set) {
            facettes::set_session_facettes_set($num_facettes_set);
        }
        $reinit_facettes = intval($reinit_facettes);
        if($reinit_facettes) {
            facettes::destroy_global_env();
        }
        if((isset($param_delete_facette)) || (isset($check_facette) && is_array($check_facette))) {
            facettes::checked_facette_search();
        }
        break;
    default:
        //Réinitialisation des facettes
        facettes::destroy_global_env();
        break;
}

$sc=new search(true);
$sc->init_links();

switch ($sub) {
	case "launch":
	    if ((string)$page=="" || $page==0) {
		    if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
			$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]="./catalog.php?categ=search&mode=6";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$sc->make_human_query();
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]= "[".$msg["130"]."] ".$msg["search_extended"];
			$_POST["page"]=1;
			$page=1;
		}
		$sc->show_results("./catalog.php?categ=search&mode=6&sub=launch","./catalog.php?categ=search&mode=6", true, '', true );
		if ($_SESSION["CURRENT"]!==false) {
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["URI"]="./catalog.php?categ=search&mode=6&sub=launch";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["PAGE"]=$page;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["HUMAN_QUERY"]=$sc->make_human_query();
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["SEARCH_TYPE"]="extended";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]['TEXT_LIST_QUERY']='';
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["TEXT_QUERY"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["NOTI"]["EXTENDED_SEARCH"]=$sc->json_encode_search();
		}
		if($pmb_map_activate){
			$sc->check_emprises();
		}		
		break;
	default:
		print $sc->show_form("./catalog.php?categ=search&mode=6","./catalog.php?categ=search&mode=6&sub=launch");
		if ($pmb_extended_search_dnd_interface){
			$search_perso= new search_perso();
			print '<div id="search_perso" style="display:none">'.$search_perso->get_forms_list().'</div>';
		}	
		break;		
}
