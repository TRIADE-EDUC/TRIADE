<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_cart.inc.php,v 1.40 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// ********************************************************************************
// affichage des paniers existants
function aff_paniers_empr($item=0, $lien_origine="./circ.php?", $action_click = "add_item", $titre="", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1,$post_param_serialized="") {
	global $msg;
	global $PMBuserid;
	global $charset;
	global $myCart;
	global $sub,$quoi;
	global $action;
	global $baselink;
	global $deflt_catalog_expanded_caddies;
	global $idcaddie_new;
	
	if ($lien_edition) $lien_edition_panier_cst = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='$lien_origine&action=edit_cart&idemprcaddie=!!idemprcaddie!!';\" />";
		else $lien_edition_panier_cst = "";
	 if($sub!='gestion' && $sub!='action') {
		print "<form name='print_options' action='$lien_origine&action=$action_click&item=$item' method='post'>";
	}
	$liste = empr_caddie::get_cart_list($restriction_panier);
	print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
	print "<hr />";
	$boutons_select = '';
	if ($lien_creation) {
		print "<div class='row'>";
		if($sub!='gestion')  print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"this.form.action='$lien_origine&action=new_cart&item=$item'; this.form.submit();\" />";
		else print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&item=$item'\" />";
		print "</div><br>";
	}
	$script_submit = '';
	if(sizeof($liste)) {
		print pmb_bidi("<div class='row'><a href='javascript:expandAll()'><img src='".get_url_icon('expand_all.gif')."' id='expandall' border='0'></a>
				<a href='javascript:collapseAll()'><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' border='0'></a>$titre</div>");
		print confirmation_delete("$lien_origine&action=del_cart&item=$item&idemprcaddie=");
		print "<script type='text/javascript'>
			function add_to_cart(form) {
        		var inputs = form.getElementsByTagName('input');
        		var count=0;
        		for(i=0;i<inputs.length;i++){
					if(inputs[i].type=='checkbox' && inputs[i].checked==true)
        				count ++;
				}
				if(count == 0){
					alert(\"$msg[no_emprcart_selected]\");
					return false;
				}
				return true;
   			}
   		</script>";
		if($sub=="gestion" && $quoi=="panier"){
			print "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
		}
		$parity=0;
		foreach ($liste as $cle => $valeur) {
		    if (!empty($idcaddie_new) && ($idcaddie_new != $valeur['idemprcaddie'])) continue;		    
		    if (!empty($idcaddie_new) && ($idcaddie_new == $valeur['idemprcaddie'])) {
		        $script_submit =  "<script>document.getElementById('id_" . $valeur['idemprcaddie'] . "').checked=true;document.forms['print_options'].submit()</script>";
		    }		    
			$rqt_autorisation=explode(" ",$valeur['autorisations']);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
				$classementRow = $valeur['empr_caddie_classement'];
				if(!trim($classementRow)){
					$classementRow=classementGen::getDefaultLibelle();
				}
				$link = "$lien_origine&action=$action_click&idemprcaddie=".$valeur['idemprcaddie']."&item=$item";
				
				if (($parity=1-$parity)) $pair_impair = "even"; else $pair_impair = "odd";
	
				$lien_edition_panier = str_replace('!!idemprcaddie!!', $valeur['idemprcaddie'], $lien_edition_panier_cst);
		        $aff_lien = $lien_edition_panier;
		        $myCart = new empr_caddie(0);
		        $myCart->nb_item=$valeur['nb_item'];
		        $myCart->nb_item_pointe=$valeur['nb_item_pointe'];
		        $myCart->type='EMPR';
		        $print_cart[$classementRow]["titre"]=stripslashes($classementRow);
		        
		        if(!isset($print_cart[$classementRow]["cart_list"])) $print_cart[$classementRow]["cart_list"] = '';
		        $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
				if($item && $action!="save_cart" && $action!="del_cart") {
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<tr class='$pair_impair' $tr_javascript ><td class='classement60'>");
		            if($action != "transfert" && $action != "del_cart" && $action!="save_cart") {
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<input type='checkbox' id='id_".$valeur['idemprcaddie']."' name='caddie[".$valeur['idemprcaddie']."]' value='".$valeur['idemprcaddie']."'>&nbsp;");
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='#' onClick='javascript:document.getElementById(\"id_".$valeur['idemprcaddie']."\").checked=true; document.forms[\"print_options\"].submit();' />");
		            } else {		            
						$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='$link' />");
		            }
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<span ".($valeur['favorite_color'] != '#000000' ? "style='color:".$valeur['favorite_color']."'" : "").">");
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<strong>".$valeur['name']."</strong>");
	                if ($valeur['comment']) $print_cart[$classementRow]["cart_list"].=  pmb_bidi("<br /><small>(".$valeur['comment'].")</small>");
	                $print_cart[$classementRow]["cart_list"].= pmb_bidi("</span>");
	            	$print_cart[$classementRow]["cart_list"].=  pmb_bidi("</td>
	            		".$myCart->aff_nb_items_reduit()."
	            		<td class='classement20'>$aff_lien</td>
						</tr>");						
				} else {		        
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<tr class='$pair_impair' $tr_javascript >");
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<td class='classement60'>");
		            if($sub!='gestion' && $sub!='action'  && $action!="save_cart") {
						$print_cart[$classementRow]["cart_list"].= pmb_bidi("<input type='checkbox' id='id_".$valeur['idemprcaddie']."' name='caddie[".$valeur['idemprcaddie']."]' value='".$valeur['idemprcaddie']."'>&nbsp;");		            	
						$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='#' onClick='javascript:document.getElementById(\"id_".$valeur['idemprcaddie']."\").checked=true; document.forms[\"print_options\"].submit();' />");
		            } else {
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<a href='$link' />");
		            }
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<span ".($valeur['favorite_color'] != '#000000' ? "style='color:".$valeur['favorite_color']."'" : "").">");
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("<strong>".$valeur['name']."</strong>");
		            if ($valeur['comment']){
		            	$print_cart[$classementRow]["cart_list"].= pmb_bidi("<br /><small>(".$valeur['comment'].")</small>");
		            }
		            $print_cart[$classementRow]["cart_list"].= pmb_bidi("</span>");
		            $print_cart[$classementRow]["cart_list"].=pmb_bidi("</a></td>");
		            $print_cart[$classementRow]["cart_list"].=pmb_bidi($myCart->aff_nb_items_reduit());
		            if(($sub=="gestion" && $quoi=="panier") || ($action=="del_cart")){
		            	$print_cart[$classementRow]["cart_list"].=pmb_bidi("<td class='classement15'>".$aff_lien."&nbsp;".empr_caddie::show_actions($valeur['idemprcaddie'])."</td>");
		            	$classementGen = new classementGen('empr_caddie', $valeur['idemprcaddie']);
		            	$print_cart[$classementRow]["cart_list"].=pmb_bidi("<td class='classement5'>".$classementGen->show_selector($lien_origine,$PMBuserid)."</td>");
		            }else{
		            	$print_cart[$classementRow]["cart_list"].=pmb_bidi("<td class='classement20'>$aff_lien</td>");
		            }
					$print_cart[$classementRow]["cart_list"].=pmb_bidi("</tr>");
				}		
			}
		}
		//on trie
		ksort($print_cart);
		//on remplace les clés à cause des accents
		$print_cart=array_values($print_cart);
		foreach($print_cart as $key => $type) {
			print gen_plus($key,$type["titre"],"<table class='classementGen_tableau'>".$type["cart_list"]."</table>",$deflt_catalog_expanded_caddies);
		}
		
	} else {
		print $msg[398];
	}
	
	 if($sub!='gestion' && $sub!='action'&& $action != "del_cart") {
		$boutons_select="<input type='submit' value='".$msg["print_cart_add"]."' class='bouton' onclick=\"return add_to_cart(this.form);\"/>&nbsp;<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/>&nbsp;";
	}	
	if ($lien_creation) {
		print "<div class='row'><hr />";
			if($sub!='gestion')  print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"this.form.action='$lien_origine&action=new_cart&item=$item'; this.form.submit();\" />";
			else print $boutons_select."<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&item=$item'\" />";
		print "</div>"; 
	} else {
		print "<div class='row'><hr />
			$boutons_select
			</div>"; 		
	}
	if ($post_param_serialized != "") {
		print unserialize($post_param_serialized);
	}			
	 if($sub!='gestion')  print"</form>";
	 print $script_submit;

}
