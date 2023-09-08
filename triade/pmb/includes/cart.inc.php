<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.inc.php,v 1.106 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/caddie.class.php");
require_once($class_path."/sort.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/connecteurs.class.php");
require_once ($class_path."/elements_list/elements_records_caddie_list_ui.class.php");

function aff_paniers($item=0, $object_type="NOTI", $lien_origine="./cart.php?", $action_click = "add_item", $titre="Cliquez sur le nom d'un panier pour y déposer la notice", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1, $nocheck=false, $lien_pointage=0) {
	global $msg;
	global $PMBuserid;
	global $charset;
	global $myCart;
	global $action;
	global $baselink;
	global $deflt_catalog_expanded_caddies;
	global $base_path;
	global $idcaddie_new;
	
	
	if ($lien_edition) $lien_edition_panier_cst = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='$lien_origine&action=edit_cart&idcaddie=!!idcaddie!!';\" />";
	else $lien_edition_panier_cst = "";

	$liste = caddie::get_cart_list($restriction_panier);
	print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
	if(($item)&&($nocheck)) {
		print "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&item=$item' method='post'>";
		print "<input type='hidden' id='idcaddie' name='idcaddie' >";
		if ($lien_pointage) {
			print "<input type='hidden' id='idcaddie_selected' name='idcaddie_selected' >";
		}
	}	
	if(($item)&&(!$nocheck)) {
		print "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&item=$item' method='post'>";
		if($action!="save_cart")print "<input type='checkbox' name='include_child' >&nbsp;".$msg["cart_include_child"];
	}
	print "<hr />";
	$boutons_select='';
	if ($lien_creation) {
		print "<div class='row'>
		$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
		</div><br>";
	}
	$script_submit = '';
	if(sizeof($liste)) {
		print "<div class='row'><a href='javascript:expandAll()'><img src='".get_url_icon('expand_all.gif')."' id='expandall' border='0'></a>
		<a href='javascript:collapseAll()'><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' border='0'></a>$titre</div>";
		print confirmation_delete("$lien_origine&action=del_cart&object_type=".$object_type."&item=$item&idcaddie=");
		$parity=array();
		
		foreach ($liste as $cle => $valeur) {
		    if (!empty($idcaddie_new) && ($idcaddie_new == $valeur['idcaddie'])) {
    		    $script_submit =  "<script>document.getElementById('id_" . $valeur['idcaddie'] . "').checked=true;document.forms['print_options'].submit()</script>";
		    }
		    if (!empty($idcaddie_new) && ($idcaddie_new == $valeur['idcaddie'])) {
		        $script_submit =  "<script>document.getElementById('id_" . $valeur['idcaddie'] . "').checked=true;document.forms['print_options'].submit()</script>";
		    }
			$rqt_autorisation=explode(" ",$valeur['autorisations']);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
				$aff_lien=str_replace('!!idcaddie!!', $valeur['idcaddie'], $lien_edition_panier_cst);
		        if(!$myCart)$myCart = new caddie(0);
		        $myCart->nb_item=$valeur['nb_item'];
		        $myCart->nb_item_pointe=$valeur['nb_item_pointe'];
		        $myCart->type=$valeur['type'];
		        $print_cart[$myCart->type]["titre"]="<b>".$msg["caddie_de_".$myCart->type]."</b><br />";
		        if(!trim($valeur["caddie_classement"])){
		        	$valeur["caddie_classement"]=classementGen::getDefaultLibelle();
		        }
		        if(!isset($parity[$myCart->type][$valeur["caddie_classement"]])) $parity[$myCart->type][$valeur["caddie_classement"]] = 0;
		        $parity[$myCart->type][$valeur["caddie_classement"]]=1-$parity[$myCart->type][$valeur["caddie_classement"]];
				if ($parity[$myCart->type][$valeur["caddie_classement"]]) $pair_impair = "even"; 
				else $pair_impair = "odd";	        
		        $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
				
				if($item && $action!="save_cart" && $action!="del_cart") {
					$rowPrint = "<tr class='$pair_impair' $tr_javascript ><td class='classement60'>".(!$nocheck?"<input type='checkbox' id='id_".$valeur['idcaddie']."' name='caddie[".$valeur['idcaddie']."]' value='".$valeur['idcaddie']."'>":"")."&nbsp;"; 
					$link = "$lien_origine&action=$action_click&object_type=".$object_type."&idcaddie=".$valeur['idcaddie']."&item=$item";	
            		if(!$nocheck){
            			$rowPrint.= "<a href='#' onclick='javascript:document.getElementById(\"id_".$valeur['idcaddie']."\").checked=true;document.forms[\"print_options\"].submit();' />";
            		} else {
            			if ($lien_pointage) {
            				$rowPrint.= "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$item.";document.getElementById(\"idcaddie_selected\").value=".$valeur['idcaddie'].";document.forms[\"print_options\"].submit();' />";
            			} else {
            				$rowPrint.= "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$valeur['idcaddie'].";document.forms[\"print_options\"].submit();' />";
            			}
            		}
            		$rowPrint .= "<span ".($valeur['favorite_color'] != '#000000' ? "style='color:".$valeur['favorite_color']."'" : "").">";
					$rowPrint .= "<strong>".$valeur['name']."</strong>";
					if ($valeur['comment']){
						$rowPrint.= "<br /><small>(".$valeur['comment'].")</small>";
					}
					$rowPrint .= "</span>";
	            	$rowPrint.=  "</td>
	            		".$myCart->aff_nb_items_reduit()."
	            		<td class='classement20'>$aff_lien</td>
						</tr>";
				} else {
					$link = "$lien_origine&action=$action_click&object_type=".$object_type."&idcaddie=".$valeur['idcaddie']."&item=$item";
	            	$rowPrint= "<tr class='$pair_impair' $tr_javascript >";
	                $rowPrint.= "<td class='classement60'><a href='$link' />
	                	<span ".($valeur['favorite_color'] != '#000000' ? "style='color:".$valeur['favorite_color']."'" : "").">
	                	<strong>".$valeur['name']."</strong>";	
	                if ($valeur['comment']){
	                	$rowPrint.= "<br /><small>(".$valeur['comment'].")</small>";
	                }
	                $rowPrint.= "</span></a></td>";
	            	$rowPrint.= $myCart->aff_nb_items_reduit();
	            	if ($lien_creation) {
	            		$classementGen = new classementGen('caddie', $valeur['idcaddie']);
	            		$rowPrint.= "<td class='classement15'>".$aff_lien."&nbsp;".caddie::show_actions($valeur['idcaddie'],$valeur['type']).($valeur['acces_rapide']?" <img src='".get_url_icon('chrono.png')."' title='".$msg['caddie_fast_access']."'>":"")."</td>";
	            		$rowPrint.= "<td class='classement5'>".$classementGen->show_selector($lien_origine,$PMBuserid)."</td>";
	            	} else {
	            		$rowPrint.= "<td class='classement20'>$aff_lien</td>";
	            	}
					$rowPrint.= "</tr>";
				}
				$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["titre"] = stripslashes($valeur["caddie_classement"]);
				if(!isset($print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"])) {
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] = '';
				}
				$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] .= $rowPrint;
			}
		}
		if ($lien_creation) {
			print "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
		}
		//Tri des classements
		foreach($print_cart as $key => $cart_type) {
			ksort($print_cart[$key]["classement_list"]);
		}
		// affichage des paniers par type
		foreach($print_cart as $key => $cart_type) {
			//on remplace les clés à cause des accents
			$cart_type["classement_list"]=array_values($cart_type["classement_list"]);
			$contenu="";
			foreach($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
				$contenu.=gen_plus($key.$keyBis,$cart_typeBis["titre"],"<table border='0' cellspacing='0' width='100%' class='classementGen_tableau'>".$cart_typeBis["cart_list"]."</table>",$deflt_catalog_expanded_caddies);
			}
			print gen_plus($key,$cart_type["titre"],$contenu,$deflt_catalog_expanded_caddies);
		}		
	} else {
		print $msg[398];
	}

	if (!$nocheck) {
		if($item && $action!="save_cart") {
			$boutons_select="<input type='submit' value='".$msg["print_cart_add"]."' class='bouton'/>&nbsp;<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/>&nbsp;";
		}	
		if ($lien_creation) {
			print "<div class='row'><hr />
				$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
				</div>"; 
		} else {
			print "<div class='row'><hr />
				$boutons_select
				</div>"; 		
		}
	} else 	if ($lien_creation) {
		print "<div class='row'><hr />
			$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
			</div>"; 
	}				
	//if(($item)&&(!$nocheck)) print"</form>";
	if(($item)) print"</form>";		
	print $script_submit;
}

// affichage d'un unique objet de caddie
function aff_cart_unique_object ($item, $caddie_type, $url_base="./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=0" ) {
	global $msg;
	global $dbh;
	global $begin_result_liste;
	global $end_result_list;
	global $page, $nbr_lignes, $nb_per_page, $nb_per_page_search;
	
	// nombre de références par pages
	if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
	else $nb_per_page = 10;
	
	$cb_display = "
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			";
	
	$liste[] = array('object_id' => $item, 'content' => "", 'blob_type' => "", 'flag' => "") ;  
	
	$aff_retour = "" ;
	
	//Calcul des variables pour la suppression d'items
	$modulo = $nbr_lignes%$nb_per_page;
	if($modulo == 1){
		$page_suppr = (!$page ? 1 : $page-1);
	} else {
		$page_suppr = $page;
	}	
	$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);	
	
	if(!sizeof($liste) || !is_array($liste)) {
		return $msg[399];
	} else {
		// en fonction du type de caddie on affiche ce qu'il faut
		if ($caddie_type=="NOTI") {
			$elements_records_caddie_list_ui = new elements_records_caddie_list_ui($liste, count($liste), false);
			$elements_records_caddie_list_ui->set_show_resa(0);
			$elements_records_caddie_list_ui->set_show_resa_planning(0);
			$elements_records_caddie_list_ui->set_draggable(0);
			elements_records_caddie_list_ui::set_url_base($url_base);
			print $elements_records_caddie_list_ui->get_elements_list();
			
			print $end_result_list;
		} // fin si NOTI
		// si EXPL
		if ($caddie_type=="EXPL") {
			// boucle de parcours des exemplaires trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
		    foreach ($liste as $cle => $expl) {
				if (!$expl['content'])
					if($stuff = get_expl_info($expl['object_id'])) {
						$stuff->lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=EXPL&item=$expl&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='".get_url_icon('basket_empty_20x20.gif')."' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a>";
						$stuff = check_pret($stuff);
						$aff_retour .= print_info($stuff,0,1);
					} else {
						$aff_retour .= "<strong>ID : ".$expl['object_id']."&nbsp;: ${msg[395]}</strong>";
					}
				else {
					$cb_display = "
						<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				    		<span class=\"notice-heada\"><strong>Code-barre : $expl[content]&nbsp;: ${msg[395]}</strong></span>
				    		<br />
						</div>
						";
					$aff_retour .= $cb_display;
				}
			} // fin de liste
			print $end_result_list;
		} // fin si EXPL
		if ($caddie_type=="BULL") {
			// boucle de parcours des bulletins trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
		    foreach ($liste as $cle => $expl) {
				global $url_base_suppr_cart; 
				$url_base_suppr_cart = $url_base ;
				if ($bull_aff = show_bulletinage_info($expl["object_id"],0,1)) {
					$aff_retour .= $bull_aff;
				} else {
					$aff_retour .= "<strong>$form_cb_expl&nbsp;: ${msg[395]}</strong><br />";
				}
			} // fin de liste
			print $end_result_list;
		} // fin si BULL
	}
	return $aff_retour ;
}
