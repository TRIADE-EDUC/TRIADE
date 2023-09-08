<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_history.inc.php,v 1.5 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $idcaddie, $action, $msg;

if($idcaddie) {
	$myCart = new caddie($idcaddie);
	print pmb_bidi($myCart->aff_cart_titre());
	switch ($action) {
		case 'pointe_item':
			print $myCart->aff_cart_nb_items();
			switch ($myCart->type) {
				case "EXPL" :
					$sc=new search(true,"search_fields_expl");
					break;
				case "NOTI" :
					$sc=new search(true,"search_fields");
					break;
					
			}
			if(is_object($sc)) {
				$table=$sc->get_results("./catalog.php?categ=caddie&sub=pointage&moyen=search_history","",true);
				$requete="select count(1) from $table"; 
	   			$res = pmb_mysql_query($requete);
	    		if($res) $nb_results=pmb_mysql_result(pmb_mysql_query($requete),0,0);
	    		else $nb_results=0;
	    		if ($nb_results) {
		    		switch ($myCart->type) {
						case "EXPL" :
		    				$requete="select $table.* from ".$table.", exemplaires where exemplaires.expl_id=$table.expl_id";
	    			 		$resultat=pmb_mysql_query($requete);
	    			 		while ($row = pmb_mysql_fetch_object($resultat)) {
	    						$res_ajout = $myCart->pointe_item($row->expl_id,$myCart->type);
	    			 		}
							break;
						case "NOTI" :
		    				$requete="select $table.* from ".$table;
	    			 		$resultat=pmb_mysql_query($requete);
	    			 		while ($row = pmb_mysql_fetch_object($resultat)) {
	    						$res_ajout = $myCart->pointe_item($row->notice_id,$myCart->type);
	    			 		}
							break;
					}
	    		}
	    		print "<h3>".$msg["caddie_menu_pointage_apres_pointage"]."</h3>";
				print pmb_bidi($myCart->aff_cart_nb_items());
				print $sc->show_search_history($idcaddie, $myCart->type, "./catalog.php?categ=caddie&sub=pointage&moyen=search_history", "pointe_item");
			}
			break;
		default:
			print pmb_bidi($myCart->aff_cart_nb_items());
			switch ($myCart->type) {
				case "EXPL" :
					$sc=new search(true,"search_fields_expl");
					break;
				case "NOTI" :
					$sc=new search(true,"search_fields");
					break;
					
			}
			if(is_object($sc)) {
				print $sc->show_search_history($idcaddie, $myCart->type, "./catalog.php?categ=caddie&sub=pointage&moyen=search_history", "pointe_item");
			}
			break;
	}

} else aff_paniers($idcaddie, "NOTI", "./catalog.php?categ=caddie&sub=pointage&moyen=search_history", "", $msg["caddie_select_pointe"], "", 0, 0, 0);