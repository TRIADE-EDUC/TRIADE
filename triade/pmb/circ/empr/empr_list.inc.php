<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_list.inc.php,v 1.79 2018-04-19 08:30:22 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($id_notice)) $id_notice = 0;
if(!isset($id_bulletin)) $id_bulletin = 0;

require_once ("$class_path/emprunteur.class.php");
require_once ("$class_path/docs_location.class.php");
require_once("$class_path/empr_caddie.class.php");
require_once("$class_path/search.class.php");
require_once($class_path.'/event/events/event_query_overload.class.php');

function iconepanier($id_emprunteur) {
	global $empr_show_caddie;
	global $msg;
	$img_ajout_empr_caddie="";
	if ($empr_show_caddie) {
		$img_ajout_empr_caddie = "\n<td><img src='".get_url_icon('basket_empr.gif')."' class='align_middle' alt='basket' title=\"${msg[400]}\" ";
		$img_ajout_empr_caddie .= "onmousedown=\"if (event) e=event; else e=window.event; if (e.target) elt=e.target; else elt=e.srcElement; e.cancelBubble = true; if (e.stopPropagation) e.stopPropagation();\" onmouseup=\"if (event) e=event; else e=window.event; if (e.target) elt=e.target; else elt=e.srcElement; if (elt.nodeName=='IMG') openPopUp('./cart.php?object_type=EMPR&item=".$id_emprunteur."', 'cart'); return false;\" ";
		$img_ajout_empr_caddie .= "onMouseOver=\"show_div_access_carts(event,".$id_emprunteur.",'EMPR');\" onMouseOut=\"set_flag_info_div(false);\" ";
		$img_ajout_empr_caddie .= "style=\"cursor: pointer\"></td>\n";
	}
	return $img_ajout_empr_caddie;
}

function get_nbpret($id_emprunteur){
	global $dbh, $msg;

	$rqt = "select count(pret_idexpl) as prets from empr left join pret on pret_idempr=id_empr where id_empr='".$id_emprunteur."' group by id_empr";
	$res = pmb_mysql_query($rqt,$dbh);
	$nb = pmb_mysql_fetch_object($res);

	return "<td>".$msg['empr_nb_pret']." : ".$nb->prets."</td>";
}

// nombre de références par pages
if ($nb_per_page_empr != "")
	$nb_per_page = $nb_per_page_empr ;
else
	$nb_per_page = 10;

$clause = '';
if(!isset($empr_location_id)) $empr_location_id = '';

switch ($sub) {
	case "launch":
		$sc=new search(true,"search_fields_empr");

		if ((string)$page=="") {
			$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["URI"]="./circ.php?categ=search";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["GET"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["POST"]["sub"]="";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_QUERY"]=$sc->make_human_query();
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["QUERY"]["HUMAN_TITLE"]= "[".$msg["param_empr"]."] ".$msg["search_emprunteur"];
			$_POST["page"]=0;
			$page=0;
		}
		if ($_SESSION["CURRENT"]!==false) {
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["URI"]="./circ.php?categ=search&sub=launch";
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["POST"]=$_POST;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["GET"]=$_GET;
			$_SESSION["session_history"][$_SESSION["CURRENT"]]["EMPR"]["PAGE"]=$page+1;
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

		if (!$page) $page=1;
		$debut =($page-1)*$nb_per_page;

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

		    $res=pmb_mysql_query($requete,$dbh);
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
						$sql_elts[] = "(empr_nom like '".$elt."%' OR empr_nom like '% ".$elt."%' OR empr_prenom like '".$elt."%' OR empr_prenom like '% ".$elt."%')";
					}
				}
				if(count($sql_elts)) {
					$clause = "WHERE ((".implode(' AND ',$sql_elts).") OR empr_cb like '".str_replace("*", "%", $form_cb)."%')" ;
				}
			}
			if(!$clause) {
				$clause = "WHERE (empr_nom like '".str_replace("*", "%", $form_cb)."%' OR empr_prenom like '".str_replace("*", "%", $form_cb)."%' OR empr_cb like '".str_replace("*", "%", $form_cb)."%')" ;
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
			$res = pmb_mysql_query($requete, $dbh);
			$nbr_lignes = @pmb_mysql_result($res, 0, 0);
		}
		break;
}

if (!isset($page)) $page=1;
$debut =($page-1)*$nb_per_page;

if ($nbr_lignes == 1) {
	// on lance la vraie requête
	$requete = "SELECT id_empr as id FROM empr $clause ";
	$res = @pmb_mysql_query($requete, $dbh);

	$id = @pmb_mysql_result($res, '0', 'id');
	if ($id) {
		$erreur_affichage="<table style='border:0px' cellpadding='1' >
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
	if ($empr_location_id && $pmb_lecteurs_localises) {
		$docs_location=new docs_location($empr_location_id);
		$where_intitule=$msg["empr_location_intitule"]. " \"".$docs_location->libelle."\"";
	} else
		$where_intitule="";

	if ($empr_show_caddie) {
		$script_filters = "<script type='text/javascript'>
			function popCaddie(the_form) {
	   			my_form = eval(the_form);
	   			window.open('./cart.php?object_type=EMPR&action=add_result', 'popup', 'height=700,width=600,menubar=no,toolbar=no,dependent=yes,resizable=yes,location=no,status=no,scrollbars=yes');
	   			my_form.target = 'popup';
	   			my_form.action='./cart.php?object_type=EMPR&action=add_result';
	   			my_form.submit();
			}
		</script>
		<form name='AddToCaddie' method='post' style='display:none'>
			<input type='hidden' name='clause' value=\"".htmlentities($clause, ENT_QUOTES, $charset)."\">
	 		!!filtered_query_hidden!!
		</form>";
	} else {
		$script_filters = "";
	}
	$aff_filters="";
	if (($empr_sort_rows)||($empr_show_rows)||($empr_filter_rows)) {
		require_once($class_path."/filter_list.class.php");
		$filter=new filter_list("empr","empr_list",$empr_show_rows,$empr_filter_rows,$empr_sort_rows);
		if (!$empr_location_id) $empr_location_id=-1;
		if (array_search("l",explode(",",$empr_filter_rows))!==FALSE) {
			$lo="f".$filter->fixedfields["l"]["ID"];
			global ${$lo};
			if (!${$lo}) {
				$tableau=array();
				$tableau[0]=$empr_location_id;
				${$lo}=$tableau;
			}
		}
		$requete = "SELECT id_empr,empr_cb,empr_nom,empr_prenom,empr_adr1,empr_ville,empr_year FROM empr $clause group by id_empr ORDER BY empr_nom, empr_prenom ";
		$filter->original_query=$requete;
		$filter->page=$page;
		$filter->nb_per_page=$nb_per_page;
		$filter->multiple=1;
		$t=array();
		$t["row_even"]["style"]="cursor: pointer";
		$t["row_even"]["class"]="even";
		$t["row_odd"]["class"]="odd";
		$t["row_odd"]["style"]="cursor: pointer";
		$t["cols"][0]["style"]="font-weight: bold";
		$filter->css=$t;
		$t=array();
		// si on est en résa on a un id de notice ou de bulletin
		if ($id_notice || $id_bulletin) {
			//type_resa : on est en prévision
			if ($type_resa) {
				$t["row"]["onmousedown"]="document.location=\"./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=!!id_empr!!&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin\";";
			} else {
				$t["row"]["onmousedown"]="document.location=\"./circ.php?categ=resa&id_empr=!!id_empr!!&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin\";";
			}
		} else {
			$t["row"]["onmousedown"]="if(event.ctrlKey || event.metaKey) { window.open(\"./circ.php?categ=pret&form_cb=!!b!!\",\"_blank\"); } else { document.location=\"./circ.php?categ=pret&form_cb=!!b!!\"; }";
		}
		$t["row"]["onmouseout"]="this.className='!!parity!!'";
		$filter->scripts=$t;
		$filter->activate_filters();
		$filtered_query = $filter->filtered_query;
		if ($empr_show_caddie) $script_filters = str_replace("!!filtered_query_hidden!!","<input type='hidden' name='filtered_query' value=\"".htmlentities($filtered_query, ENT_QUOTES, $charset)."\">", $script_filters);
		// ER : trouver ici nbr_lignes
		$nbr_lignes = $filter->nb_lines_query();
		if (!$filter->error) {
			$aff_filters.="<script type='text/javascript' src='./javascript/tablist.js'></script>";
			switch ($sub) {
				case "launch":
					$url_base = "./circ.php?categ=search&sub=launch";
					$aff_filters.="<input type='button' class='bouton' onClick=\"document.form_filters_extended.action='$url_to_search_form'; document.form_filters_extended.target='$search_target'; document.form_filters_extended.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
					if ($empr_show_caddie)
						 $aff_filters.="&nbsp;&nbsp;<input type='button' class='bouton' value='".$msg["add_empr_cart"]."' onClick=\"popCaddie(document.forms['AddToCaddie']); return false;\">";
					break;
				default:
					if ($empr_location_id == -1) $empr_location_id = 0;
					$url_base = "./circ.php?categ=pret&form_cb=".rawurlencode($form_cb)."&id_notice=$id_notice"."&id_bulletin=$id_bulletin&empr_location_id=$empr_location_id";
					break;
			}
			$aff_filters.="<form class='form-$current_module' id='form_filters' name='form_filters' method='post' action='".$url_base."&nb_per_page=$nb_per_page' onSubmit='this.page.value=\"1\";'><h3>".$msg["filters_tris"]."</h3>";
			if ($sub=="launch") {
				$aff_filters.=$sc->make_hidden_form_content();
			}
			$aff_filters.="<div class='form-contenu'>";
			if ($sub!="launch") {
				$aff_filters.="<input type='hidden' name='page' value='$page'>";
			}
			$aff_filters.="<div id=\"el1Parent\" class=\"notice-parent\"><img src=\"".get_url_icon('plus.gif')."\" class=\"img_plus\" name=\"imEx\" id=\"el1Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el1', true); return false;\">
		   							<b>".$msg["filters"]."</b></div>
									<div id=\"el1Child\" style=\"margin-left:7px;display:none;\">";
			$aff_filters.=$filter->display_filters();
			$aff_filters.="</div><div class='row'></div><div id=\"el2Parent\" class=\"notice-parent\"><img src=\"".get_url_icon('plus.gif')."\" class=\"img_plus\" name=\"imEx\" id=\"el2Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el2', true); return false;\">
								<b>".$msg["tris_dispos"]."</b></div>
						<div id=\"el2Child\" style=\"margin-left:7px;display:none;\">";
			$aff_filters.=$filter->display_sort();
			$aff_filters.="</div></div><div class='row'></div><div class='row'><input type='submit' class='bouton' value='".$msg["empr_sort_filter_button"]."'>";
			if ($empr_show_caddie && $sub!="launch")
				$aff_filters.="&nbsp;&nbsp;<input type='button' class='bouton' value='".$msg["add_empr_cart"]."' onClick=\"popCaddie(document.forms['AddToCaddie']); return false;\">";
			$aff_filters.="</div></form>";
			$aff_filters.="<br />".$filter->make_human_filters();
			$aff_filters.=$script_filters;
			$empr_list_tmpl=str_replace("!!filters_list!!",$aff_filters,$empr_list_tmpl);
			$nav_bar=$filter->display_pager();
			$empr_list=$filter->display_result();
		} else $empr_list_tmpl=str_replace("!!filters_list!!",$filter->error_message,$empr_list_tmpl);
	} else {
		// on lance la vraie requête
		$requete = "SELECT *, count(pret_idexpl) as nb_pret FROM empr left join pret on id_empr=pret_idempr $clause group by id_empr ORDER BY empr_nom, empr_prenom LIMIT $debut,$nb_per_page ";
		$res = @pmb_mysql_query($requete, $dbh);
//		$nbr_lignes = pmb_mysql_num_rows($res);
		$parity = 0;
		switch ($sub) {
			case "launch":
				$url_base = "./circ.php?categ=search&sub=launch";
				$aff_filters.="<input type='button' class='bouton' onClick=\"document.form_filters.action='$url_to_search_form'; document.form_filters.target='$search_target'; document.form_filters.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
				if ($empr_show_caddie) {
					$script_filters = str_replace("!!filtered_query_hidden!!","", $script_filters);
					$aff_filters.="&nbsp;&nbsp;<input type='button' class='bouton' value='".$msg["add_empr_cart"]."' onClick=\"popCaddie(document.forms['AddToCaddie']); return false;\">";
				}
				$empr_list_tmpl=str_replace("!!filters_list!!",$aff_filters.$script_filters,$empr_list_tmpl);
				break;
			default:
				if ($empr_show_caddie) {
					$script_filters = str_replace("!!filtered_query_hidden!!","", $script_filters);
					$aff_filters.="&nbsp;&nbsp;<input type='button' class='bouton' value='".$msg["add_empr_cart"]."' onClick=\"popCaddie(document.forms['AddToCaddie']); return false;\">";
				}
				$empr_list_tmpl=str_replace("!!filters_list!!",$aff_filters.$script_filters,$empr_list_tmpl);
				break;
		}
		$empr_list .= "<table style='border:0px; width:100%'>";
		while(($empr=pmb_mysql_fetch_object($res))) {
			$recherche_groupe=@pmb_mysql_query("SELECT libelle_groupe FROM empr_groupe, groupe WHERE empr_id=".$empr->id_empr." AND groupe_id=id_groupe ORDER BY libelle_groupe");
			$grp=array();
			while ($gr=pmb_mysql_fetch_object($recherche_groupe)) {
				$grp[]=$gr->libelle_groupe;
			}
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			// si on est en résa on a un id de notice ou de bulletin
			if ($id_notice || $id_bulletin) {
				//type_resa : on est en prévision
				if ($type_resa) {
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$empr->id_empr&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin';\" ";
				} else {
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./circ.php?categ=resa&id_empr=$empr->id_empr&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin';\" ";
				}
			} else {
				$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./circ.php?categ=pret&form_cb=".rawurlencode($empr->empr_cb)."';\" ";
			}
			// **************** ajout icône ajout panier
			$img_ajout_empr_caddie = iconepanier($empr->id_empr);

			$empr_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
			$empr_list .= "
				<td>
					<strong>$empr->empr_cb</strong>
				</td>
				<td>
       				$empr->empr_nom&nbsp;$empr->empr_prenom
				</td>
				<td>
					<b>".implode("/",$grp)."</b>
				</td>
				<td>
					$empr->empr_adr1
				</td>
				<td>
					$empr->empr_ville
				</td>
				<td>
					$empr->empr_year
				</td>
				<td> $msg[empr_nb_pret]"." : "."
					$empr->nb_pret
				</td>
				$img_ajout_empr_caddie
				</tr>";
			$parity += 1;
			}
		pmb_mysql_free_result($res);
		$empr_list .= "</table>";
		// si on est en résa on a un id de notice ou de bulletin
		if ($id_notice || $id_bulletin) {
			//type_resa : on est en prévision
			if ($type_resa) {
				$url_base = "./circ.php?categ=resa_planning&resa_action=add_resa&id_empr=$id&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin";
			} else {
				$url_base = "./circ.php?categ=resa&id_empr=$id&groupID=$groupID&id_notice=$id_notice&id_bulletin=$id_bulletin";
			}
		} else {
			$url_base = "./circ.php?categ=pret&nbr_lignes=$nbr_lignes&form_cb=".rawurlencode($form_cb)."&empr_location_id=$empr_location_id";
		}
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
	}
	// affichage du résultat
	list_empr($form_cb, $empr_list, $nav_bar, $nbr_lignes, $where_intitule);

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
