<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_list.inc.php,v 1.87.2.1.2.1 2025/01/24 16:34:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $sub;
global $id_notice, $id_bulletin, $groupID;
global $form_cb, $empr_location_id, $pmb_lecteurs_localises, $type_resa, $page;
global $empr_sort_rows, $empr_show_rows, $empr_filter_rows;
global $force_resa, $pmb_resa_records_no_expl;

$id_notice = intval($id_notice);
$id_bulletin = intval($id_bulletin);
$groupID = intval($groupID);

require_once ("$class_path/emprunteur.class.php");
require_once ("$class_path/docs_location.class.php");
require_once("$class_path/empr_caddie.class.php");
require_once("$class_path/search.class.php");
require_once($class_path.'/event/events/event_query_overload.class.php');

function iconepanier($id_emprunteur) {
	global $msg, $empr_show_caddie;
	$img_ajout_empr_caddie="";
	if ($empr_show_caddie) {
		$img_ajout_empr_caddie = "\n<td><img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" ";
		$img_ajout_empr_caddie .= "onmousedown=\"if (event) e=event; else e=window.event; if (e.target) elt=e.target; else elt=e.srcElement; e.cancelBubble = true; if (e.stopPropagation) e.stopPropagation();\" onmouseup=\"if (event) e=event; else e=window.event; if (e.target) elt=e.target; else elt=e.srcElement; if (elt.nodeName=='IMG') openPopUp('./cart.php?object_type=EMPR&item=".$id_emprunteur."', 'cart'); return false;\" ";
		$img_ajout_empr_caddie .= "onMouseOver=\"show_div_access_carts(event,".$id_emprunteur.",'EMPR');\" onMouseOut=\"set_flag_info_div(false);\" ";
		$img_ajout_empr_caddie .= "style=\"cursor: pointer\"></td>\n";
	}
	return $img_ajout_empr_caddie;
}

function get_nbpret($id_emprunteur){
	global $msg;

	$rqt = "select count(pret_idexpl) as prets from empr left join pret on pret_idempr=id_empr where id_empr='".$id_emprunteur."' group by id_empr";
	$res = pmb_mysql_query($rqt);
	$nb = pmb_mysql_fetch_object($res);

	return "<td>".$msg['empr_nb_pret']." : ".$nb->prets."</td>";
}

$clause = '';
if(!isset($empr_location_id)) $empr_location_id = '';

switch ($sub) {
	case "launch":
		$sc=new search(true,"search_fields_empr");

		if ((string)$page=="" || $page==0) {
		    $_SESSION["CURRENT"] = (isset($_SESSION["session_history"]) && is_countable($_SESSION["session_history"])) ? count($_SESSION["session_history"]) : 0;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]="./circ.php?categ=search";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$sc->make_human_query();
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]= "[".$msg["param_empr"]."] ".$msg["search_emprunteur"];
			$_POST["page"]=1;
			$page=1;
		}
		if ($_SESSION["CURRENT"]!==false) {
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["URI"]="./circ.php?categ=search&sub=launch";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["PAGE"]=$page;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["HUMAN_QUERY"]=$sc->make_human_query();
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["SEARCH_TYPE"]="empr";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]['TEXT_LIST_QUERY']='';
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["TEXT_QUERY"]="";
		}

		$table=$sc->get_results("./circ.php?categ=search&sub=launch","./circ.php?categ=search",true);

		$sc->link ='./circ.php?categ=empr_saisie&id=!!id!!';
		$url = "./circ.php?categ=search&sub=launch";
		$url_to_search_form = "./circ.php?categ=search";
		$search_target="";

	    $requete="select count(1) from $table";
	    $res = 	pmb_mysql_query($requete);
	    if($res)
	    	$nbr_lignes=pmb_mysql_result($res,0,0);
	    else $nbr_lignes=0;

	    if ($nbr_lignes) {
    		$requete="select $table.* from ".$table.", empr where empr.id_empr=$table.id_empr";

			//Y-a-t-il une erreur lors de la recherche ?
		    if ($sc->error_message) {
		    	error_message_history("", $sc->error_message, 1);
		    	exit();
		    }

		    print $sc->make_hidden_search_form($url,"form_filters_extended");

		    $res=pmb_mysql_query($requete);
		    $human_requete = $sc->make_human_query();

		    print "<strong>".$msg["search_search_emprunteur"]."</strong> : ".$human_requete ;

			if ($nbr_lignes) {
				print " => ".$nbr_lignes." ".$msg["search_empr_nb_result"]."<br />\n";
				$tab_id_empr=array();
				while ($row = pmb_mysql_fetch_object($res)) {
					$tab_id_empr[] = $row->id_empr;
				}
				$clause = "WHERE id_empr in('".implode("','",$tab_id_empr)."')";
			} else print "<br />".$msg["1915"]." ";
	    }
		break;
	default :
		if ($form_cb) {
			$elts = explode(' ', $form_cb);
			if(count($elts)>1) {
				$sql_elts = array();
				foreach ($elts as $elt) {
					$elt = str_replace("*", "%", trim($elt));
					if($elt) {
						$sql_elts[] = "(empr_nom like '".$elt."%' OR empr_nom like '% ".$elt."%' OR empr_nom like '%-".$elt."%' OR empr_prenom like '".$elt."%' OR empr_prenom like '% ".$elt."%' OR empr_prenom like '%-".$elt."%')";
					}
				}
				if(count($sql_elts)) {
					$clause = "WHERE ((".implode(' AND ',$sql_elts).") OR empr_cb like '".str_replace("*", "%", $form_cb)."%')" ;
				}
			}
			if(!$clause) {
				$elt = str_replace("*", "%", $form_cb);
				$clause = "WHERE (empr_nom like '".$elt."%' OR empr_nom like '%-".$elt."%' OR empr_prenom like '".$elt."%' OR empr_prenom like '%-".$elt."%' OR empr_cb like '".$elt."%')" ;
			}
			/**
			 * Publication d'un évenement à l'affichage d'un lecteur
			 */
			$evt_handler = events_handler::get_instance();
			$event = new event_query_overload("empr", "search_query_overload");
			$evt_handler->send($event);

			if($event->get_query_overload()){
				$clause.= $event->get_query_overload();
			}
		}
		if ($empr_location_id && $pmb_lecteurs_localises)
			$clause .= " and empr_location='$empr_location_id'" ;

		// on récupére le nombre de lignes qui vont bien
		if (!isset($nbr_lignes)) {
			$requete = "SELECT COUNT(1) FROM empr $clause ";
			$res = pmb_mysql_query($requete);
			$nbr_lignes = pmb_mysql_result($res, 0, 0);
		}
		break;
}

if ($nbr_lignes == 1) {
	// on lance la vraie requête
	$requete = "SELECT id_empr as id FROM empr $clause ";
	$res = pmb_mysql_query($requete);

	$id = pmb_mysql_result($res, '0', 'id');
	if ($id) {
		$erreur_affichage="<table style='border:0px; padding:1px' role='presentation'>
		<tr><td style='width:33%'>&nbsp;<span>&nbsp;</span></td>
				<td style='width:100%'>";
		$erreur_affichage.="&nbsp;<span>&nbsp;</span>";
		$erreur_affichage.="</td></tr></table>";
		if ($id_notice || $id_bulletin) {
			//type_resa : on est en prévision
			if ($type_resa) {
				echo "<script type='text/javascript'> parent.location.href='./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$id&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin'; </script>";
			} else {
				echo "<script type='text/javascript'> parent.location.href='./circ.php?categ=resa&id_empr=$id&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin".($force_resa && $pmb_resa_records_no_expl ? '&force_resa=1' : '')."'; </script>";
			}
		} else {
			$empr = new emprunteur($id, $erreur_affichage, FALSE, 1);
			$affichage = $empr->fiche;
		}
	}
} else if($nbr_lignes) {
    $aff_search_back="";
	if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
		$filter = emprunteur::get_instance_filter_list($clause);
		list_readers_circ_ui::set_used_filter_list_mode(true);
		list_readers_circ_ui::set_filter_list($filter);
		
		// ER : trouver ici nbr_lignes
		$nbr_lignes = $filter->nb_lines_query();
		if (!$filter->error) {
			switch ($sub) {
				case "launch":
					$aff_search_back.="<input type='button' class='bouton' onClick=\"document.form_filters_extended.action='$url_to_search_form'; document.form_filters_extended.target='$search_target'; document.form_filters_extended.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
					break;
				default:
					if ($empr_location_id == -1) $empr_location_id = 0;
					break;
			}
		}
	} else {
		switch ($sub) {
			case "launch":
				$aff_search_back.="<input type='button' class='bouton' onClick=\"document.form_filters.action='$url_to_search_form'; document.form_filters.target='$search_target'; document.form_filters.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
				break;
			default:
				break;
		}
	}
	// affichage du résultat
	$filters = array();
	$filters['simple_search'] = (!empty($form_cb) ? $form_cb : '');
	if ($empr_location_id && $pmb_lecteurs_localises) {
		$filters['empr_location_id'] = $empr_location_id;
	}
	if(!empty($tab_id_empr)) {
		$filters['empr_ids'] = $tab_id_empr;
	} elseif(!empty($_POST['form_cb'])) {
		// en provenance de la recherche simple (POST de la variable), on élimine l'éventuel filtre sur une précédente recherche avancée
		$filters['empr_ids'] = array();
	}
	$list_readers_circ_ui = new list_readers_circ_ui($filters);
	print $aff_search_back;
	print $list_readers_circ_ui->get_display_list();
} else {
	switch($sub) {
		case "launch":
			$human_requete = $sc->make_human_query();
		    print "<strong>".$msg["search_search_emprunteur"]."</strong> : ".$human_requete ;
		    print $sc->make_hidden_search_form($url,"form_filters");
			print "<br />".$msg[1915]."<input type='button' class='bouton' onClick=\"document.form_filters.action='$url_to_search_form'; document.form_filters.target='$search_target'; document.form_filters.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
			break;
		default:
			// la requête de recherche d'emprunteur n'a produit aucun résultat
			// si on est en résa on a un id de notice ou de bulletin
			if ($id_notice || $id_bulletin) {
				//type_resa : on est en prévision
				if ($type_resa) {
					get_cb( $msg['prevision_doc'], $msg[34], $msg['circ_tit_form_cb_empr'], "./circ.php?categ=pret&id_notice=$id_notice&id_bulletin=$id_bulletin&type_resa=1", 0);
				} else {
					get_cb( $msg['reserv_doc'], $msg[34], $msg['circ_tit_form_cb_empr'], "./circ.php?categ=pret&id_notice=$id_notice&id_bulletin=$id_bulletin", 0);
				}
			} else {
				get_cb(	$msg[13], $msg[34], $msg['circ_tit_form_cb_empr'], "./circ.php?categ=pret", 0, 0);
			}
			error_message($msg[46], str_replace('!!form_cb!!', $form_cb, $msg[47]), 0, './circ.php');
			break;
	}
}
