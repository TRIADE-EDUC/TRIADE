<?php
use Pmb\Common\Library\Navbar\Navbar;

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visu_ex.inc.php,v 1.39.4.2 2025/01/24 16:34:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $pmb_book_pics_show, $pmb_book_pics_url, $PMBuserid;
global $gestion_acces_active, $gestion_acces_user_notice, $nb_per_page_search;
global $form_cb_expl, $nb_results, $ex_query;
global $begin_result_liste, $end_result_liste;
global $typdoc_query, $page, $link, $link_expl, $link_explnum, $print_mode;

$prefix_url_image="./";
if (!isset($back_to_visu)){
	get_cb_expl($msg[375], $msg[661], $msg['circ_tit_form_cb_expl'], './circ.php?categ=visu_ex');
	if($form_cb_expl){
		$query = "select expl_id, expl_notice, pret_flag, pret_idempr from docs_statut, exemplaires left join pret on pret_idexpl=expl_id where expl_cb='$form_cb_expl' and expl_statut=idstatut ";
		$result = pmb_mysql_query($query);
		if(!pmb_mysql_num_rows($result)) {
			// exemplaire inconnu
			$alert_sound_list[]="critique";
			print "<strong>$form_cb_expl&nbsp;: {$msg[367]}</strong>";
		} else {
			$expl_lu = pmb_mysql_fetch_object($result) ;
			if ($stuff = get_expl_info($expl_lu->expl_id, 1)) {
				$stuff = check_pret($stuff);
				// print $begin_result_liste;
				print print_info($stuff,1,1);
				// pour affichage de l'image de couverture
				if ($pmb_book_pics_show == '1' && (($pmb_book_pics_url && !empty($stuff->code)) || (!empty($stuff->thumbnail_url))))
					print "<script type='text/javascript'>
						<!--
						var img = document.getElementById('PMBimagecover".$expl_lu->expl_notice."');
						isbn=img.getAttribute('isbn');
						url_image=img.getAttribute('url_image');
						if (isbn) {
							if (img.src.substring(img.src.length-8,img.src.length)=='vide.png') {
								img.src=url_image.replace(/!!noticecode!!/,isbn);
								}
							}		
						//-->
						</script>
						";
			} else {
				// exemplaire inconnu
				$alert_sound_list[]="critique";
				print "<strong>$form_cb_expl&nbsp;: {$msg[367]}</strong>";
			}
		}
	}
	
}else{
	//droits d'acces lecture notice
	$acces_j='';
	if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
		require_once("$class_path/acces.class.php");
		$ac= new acces();
		$dom_1= $ac->setDomain(1);
		$acces_j = $dom_1->getJoin($PMBuserid,4,'notice_id');
	} 
	
	// on commence par voir ce que la saisie utilisateur est ($ex_query)
	$ex_query = clean_string($ex_query);
	
	$EAN = '';
	$isbn = '';
	$code = '';
	
	$where_typedoc = "";
	if(isEAN($ex_query)) {
		// la saisie est un EAN -> on tente de le formater en ISBN
		$EAN=$ex_query;
		$isbn = EANtoISBN($ex_query);
		// si échec, on prend l'EAN comme il vient
		if(!$isbn) 
			$code = str_replace("*","%",$ex_query);
		else {
			$code=$isbn;
			$code10=formatISBN($code,10);
		}
	} else {
		if(isISBN($ex_query)) {
			// si la saisie est un ISBN
			$isbn = formatISBN($ex_query);
			// si échec, ISBN erroné on le prend sous cette forme
			if(!$isbn) 
				$code = str_replace("*","%",$ex_query);
			else {
				$code10=$isbn ;
				$code=formatISBN($code10,13);
			}
		} else {
			// ce n'est rien de tout ça, on prend la saisie telle quelle
			$code = str_replace("*","%",$ex_query);
			// filtrer par typdoc_query si selectionné
			if(!empty($typdoc_query) && !empty($typdoc_query[0])) $where_typedoc=" and typdoc in ('".implode("','", $typdoc_query)."') ";
		}
	}
	$page = intval($page);
	if(!empty($nb_results)){
	    if ($page) {
	        $limit_page= " limit ".($page-1)*$nb_per_page_search.", $nb_per_page_search "; 
	    } else {
	        $limit_page= " limit 0, $nb_per_page_search ";
	    }
	}else{
		$limit_page= " "; 
	}	
	$rqt_bulletin = 0;
	// on compte
	if ($EAN && $isbn) {
		
		// cas des EAN purs : constitution de la requête
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code','$EAN'".($code10?",'$code10'":"").")) ";
		$requete.= $limit_page;
		$myQuery = pmb_mysql_query($requete);
		
	} elseif ($isbn) {
		
		// recherche d'un isbn
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= " WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR exemplaires.expl_cb='$ex_query' OR notices.code in ('$code'".($code10?",'$code10'":"").")) ";
		$requete.= $limit_page;
		$myQuery = pmb_mysql_query($requete);
		
	} elseif ($code) {
		
		// recherche d'un exemplaire
		// note : le code est recherché aussi dans le champ code des notices
		// (cas des code-barres disques qui échappent à l'EAN)
		//
		$requete = "SELECT distinct notices.* FROM notices ";
		$requete.= $acces_j;
		$requete.= "left join exemplaires on notices.notice_id=exemplaires.expl_notice ";
		$requete.= "WHERE niveau_biblio='m' AND (exemplaires.expl_cb like '$code' OR notices.code like '$code') $where_typedoc ";
		$requete.= $limit_page;		
		$myQuery = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($myQuery)==0) {
			// rien trouvé en monographie
			$requete = "SELECT distinct notices.*, bulletin_id FROM notices ";
			$requete.= $acces_j;
			$requete.= "left join bulletins on bulletin_notice=notice_id left join exemplaires on (bulletin_id=expl_bulletin and expl_notice=0) ";
			$requete.= "WHERE niveau_biblio='s' AND (exemplaires.expl_cb like '$code' OR bulletin_numero like '$code' OR bulletin_cb like '$code' OR notices.code like '$code')  $where_typedoc ";
			$requete.= "GROUP BY bulletin_id ";
			$requete.= $limit_page;
			$myQuery = pmb_mysql_query($requete);
			$rqt_bulletin=1;
		}
		
	} else {
		// Pas de résultat
		error_message($msg[235], $msg[307]." $ex_query", 1, "./circ.php?categ=visu_rech");
		die();
	}
	
	if(empty($nb_results)){
		$nb_results= pmb_mysql_num_rows($myQuery);
	}
					
	if ($rqt_bulletin!=1) {
		if(pmb_mysql_num_rows($myQuery)) {
			// la recherche fournit plusieurs résultats !!!
			// boucle de parcours des notices trouvées
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			print sprintf("<div class='othersearchinfo'><b>".$msg[940]."</b>&nbsp;$ex_query =&gt; ".$msg["searcher_results"]."</div>",$nb_results);			
			print $begin_result_liste;
			$nb=0;
			$recherche_ajax_mode=0;
			while($notice = pmb_mysql_fetch_object($myQuery)) {
				if($notice->niveau_biblio != 's' && $notice->niveau_biblio != 'a') {
					// notice de monographie (les autres n'ont pas de code ni d'exemplaire !!! ;-)
					//Access au cataloguage
					if($nb>5) $recherche_ajax_mode=1;
					/*echo "<pre>";
					print_r($notice);
					echo "</pre>";*/
					//Les liens sont défini dans le fichier visu_rech.inc.php
					if (empty($link_explnum)) $link_explnum = "";
					$print_mode = intval($print_mode);
					$display = new mono_display($notice, 6, $link, 1, $link_expl, '', $link_explnum,1, $print_mode,1,1,'',0,false,true,$recherche_ajax_mode);
					//mono_display($id, $level=1, $action='', $expl=1, $expl_link='', $lien_suppr_cart="", $explnum_link='', $show_resa=0, $print=0, $show_explnum=1, $show_statut=0, $anti_loop='', $draggable=0, $no_link=false, $show_opac_hidden_fields=true,$ajax_mode=0)
					print pmb_bidi($display->result);
				}
				if (++$nb >= $nb_per_page_search) break;
			}
			if (!isset($end_result_liste)) $end_result_liste = "";
			print $end_result_liste;
		} else {
			// exemplaire inconnu
			error_message($msg[235], $msg[307]." $ex_query", 1, "./circ.php?categ=visu_rech");
			die();
		}
	} else {
		if (pmb_mysql_num_rows($myQuery)) {
			print sprintf("<div class='othersearchinfo'><b>".$msg[940]."</b>&nbsp;$ex_query =&gt; ".$msg["searcher_results"]."</div>",$nb_results);
			print $begin_result_liste;
			$nb=0;
			while(($n=pmb_mysql_fetch_object($myQuery))) {

				//Access au cataloguage
				$cart_link_non = false;

				require_once ("$include_path/bull_info.inc.php") ;
				require_once ("$class_path/serials.class.php") ;
				$n->isbd = show_bulletinage_info($n->bulletin_id);
				print pmb_bidi($n->isbd) ;
				if (++$nb >= $nb_per_page_search) break;
			}	
			print $end_result_liste;
		} else {
			// Pas de résultat
			error_message($msg[235], $msg[307]." $ex_query", 1, "./circ.php?categ=visu_rech");
			die();
		}
	}
	
	//Gestion de la pagination
	if ($nb_results) {
		print "
		<form name='search_form' action='./circ.php?categ=visu_rech' method='post' style='display:none'>
			<input type='hidden' name='page' value='$page'/>
			<input type='hidden' name='nb_results' value='$nb_results'/>
			<input type='hidden' name='ex_query' value='$ex_query'/>
			<input type='hidden' name='typdoc_query' value=''/>
			<input type='hidden' name='statut_query' value=''/>
		</form>";
		
		if (!$page) {
		    $current_page = 1 ;
		} else {
		    $current_page = intval($page);
		}
		$navbar = new Navbar($current_page, $nb_results, $nb_per_page_search);
		$navbar->setHiddenFormName('search_form');
		print "<div class='center'>".$navbar->render()."</div>";
	}  
}

