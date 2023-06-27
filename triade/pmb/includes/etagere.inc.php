<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: etagere.inc.php,v 1.22 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage des etagères existantes
function aff_etagere($action, $bouton_ajout=1) {
	global $msg;
	global $PMBuserid;
	global $charset, $opac_url_base;
	global $deflt_catalog_expanded_caddies;
	
	$liste = etagere::get_etagere_list();
	if(sizeof($liste)) {
		if($action=="edit_etagere"){
			print "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
			print "<div class='hmenu'>
						<span><a href='catalog.php?categ=etagere&sub=classementGen'>".$msg["classementGen_list_libelle"]."</a></span>
					</div><hr>";
			if ($bouton_ajout) {
				print "<div class='row'>
					<input class='bouton' type='button' value=' ".$msg["etagere_new_etagere"]." ' onClick=\"document.location='./catalog.php?categ=etagere&sub=gestion&action=new_etagere'\" />
					</div><br>";
			}
		}
		print pmb_bidi("<div class='row'><a href='javascript:expandAll()'><img src='".get_url_icon('expand_all.gif')."' id='expandall' border='0'></a>
				<a href='javascript:collapseAll()'><img src='".get_url_icon('collapse_all.gif')."' id='collapseall' border='0'></a></div>");
		$parity=1;
		$arrayRows=array();
		foreach ($liste as $cle => $valeur) {
			$rqt_autorisation=explode(" ",$valeur['autorisations']);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
				$classementRow = $valeur['etagere_classement'];
				if(!trim($classementRow)){
					$classementRow=classementGen::getDefaultLibelle();
				}
				$baselink = "./catalog.php?categ=etagere";
				$link = $baselink."&sub=$action&action=edit_etagere&idetagere=".$valeur['idetagere'];
				if ($parity % 2) {
					$pair_impair = "even";
				} else {
					$pair_impair = "odd";
				}
				$parity += 1;
	
	        	$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
	        	$td_javascript_click=" onmousedown=\"document.location='$link';\" ";
	
	        	$rowPrint=pmb_bidi("<tr class='$pair_impair' $tr_javascript >");
	        	$rowPrint.=pmb_bidi("<td $td_javascript_click style='cursor: pointer'><strong>".$valeur['name']."</strong>".($valeur['comment']?" (".$valeur['comment'].")":"")."</td>");
	        	$rowPrint.=pmb_bidi("<td $td_javascript_click style='cursor: pointer'>".$valeur['comment_gestion']."</td>");
	           	$rowPrint.=pmb_bidi("<td $td_javascript_click style='cursor: pointer'>".$valeur['nb_paniers']."</td>");
	           	$rowPrint.=pmb_bidi("<td $td_javascript_click style='cursor: pointer'>".($valeur['validite']?$msg['etagere_visible_date_all']:$msg['etagere_visible_date_du']." ".$valeur['validite_date_deb_f']." ".$msg['etagere_visible_date_fin']." ".$valeur['validite_date_fin_f'])."</td>");
	           	$rowPrint.=pmb_bidi("<td>".($valeur['visible_accueil']?"X":"")."<br /><a href='".$opac_url_base."index.php?lvl=etagere_see&id=".$valeur['idetagere']."' target=_blank>".$opac_url_base."index.php?lvl=etagere_see&id=".$valeur['idetagere']."</a></td>");
	           	if($action=="edit_etagere"){
	           		$classementGen = new classementGen('etagere', $valeur['idetagere']);
	           		$rowPrint.=pmb_bidi("<td>".$classementGen->show_selector($baselink,$PMBuserid)."</td>");
	           	}
				$rowPrint.=pmb_bidi("</tr>");
	
	           	$arrayRows[$classementRow]["title"]=stripslashes($classementRow);
	           	if(!isset($arrayRows[$classementRow]["etagere_list"])) {
	           		$arrayRows[$classementRow]["etagere_list"] = '';
	           	}
	           	$arrayRows[$classementRow]["etagere_list"].=$rowPrint;
			}
		}
		//on trie
		ksort($arrayRows);
		//on remplace les clés à cause des accents
		$arrayRows=array_values($arrayRows);
		foreach($arrayRows as $key => $type) {
			if($action=="edit_etagere"){
				print gen_plus($key,$type["title"],"<table class='classementGen_tableau'><tr><th class='classement40'>".$msg['etagere_name']."</th><th class='classement10'>".$msg['etagere_comment_gestion']."</th><th class='classement10'>".$msg["etagere_cart_count"]."</th><th class='classement10'>".$msg['etagere_visible_date']."</th><th class='classement35'>".$msg['etagere_visible_accueil']."</th><th class='classement5'>&nbsp;</th></tr>".$type["etagere_list"]."</table>",$deflt_catalog_expanded_caddies);
			}else{
				print gen_plus($key,$type["title"],"<table class='classementGen_tableau'><tr><th class='classement40'>".$msg['etagere_name']."</th><th class='classement10'>".$msg['etagere_comment_gestion']."</th><th class='classement10'>".$msg["etagere_cart_count"]."</th><th class='classement10'>".$msg['etagere_visible_date']."</th><th class='classement40'>".$msg['etagere_visible_accueil']."</th></tr>".$type["etagere_list"]."</table>",$deflt_catalog_expanded_caddies);
			}
		}
	
	} else {
		print $msg['etagere_no_etagere'];
	}
	if ($bouton_ajout) print "<div class='row'>
		<input class='bouton' type='button' value=' ".$msg["etagere_new_etagere"]." ' onClick=\"document.location='./catalog.php?categ=etagere&sub=gestion&action=new_etagere'\" />
		</div>";

}
