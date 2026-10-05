<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rubriques.inc.php,v 1.20 2019/08/20 09:18:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $caller, $param1, $param2, $id_bibli, $id_exer, $no_display, $bt_ajouter, $class_path, $sel_header, $elt_query, $charset;
global $sel_search, $jscript, $nbr_lignes, $page, $sel_footer;

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=rubriques&caller=$caller&param1=$param1&param2=$param2&id_bibli=$id_bibli&id_exer=$id_exer&no_display=$no_display&bt_ajouter=$bt_ajouter";

// contenu popup sélection fournisseur
require_once('./selectors/templates/sel_rubriques.tpl.php');
require_once($class_path.'/entites.class.php');
require_once($class_path.'/budgets.class.php');
require_once($class_path.'/rubriques.class.php');


// affichage du header
print $sel_header;

//Recherche
$sel_search = str_replace("!!elt_query!!", htmlentities(stripslashes($elt_query),ENT_QUOTES, $charset), $sel_search);
$sel_search = str_replace("!!action_url!!", $base_url, $sel_search);
print $sel_search;

print $jscript;
show_results($nbr_lignes, $page);

//fonction de tri des rubriques par libellé
function sort_array_rub($a,$b) {
	return $a['lib_rub_no_html']>$b['lib_rub_no_html'];
}

// affichage des membres de la page
function show_results($nbr_lignes=0, $page=0) {
	
	global $nb_per_page;
	global $base_url;
	global $caller;
 	global $charset;
	global $msg;
	global $id_bibli, $id_exer;
	global $acquisition_budget_show_all;
	global $elt_query;

	// on récupére le nombre de lignes qui vont bien
	$nbr_lignes = entites::countRubriquesFinales($id_bibli, $id_exer, true, $elt_query);

	if (!$page) $page=1;
	$debut = ($page-1)*$nb_per_page;

	if($nbr_lignes) {
		// on lance la vraie requête
		if(!$acquisition_budget_show_all){
			$res = entites::listRubriquesFinales($id_bibli, $id_exer, true, $debut, $nb_per_page, $elt_query);
		}else{
			$res = entites::listRubriquesFinales($id_bibli, $id_exer, true, 0, 0, $elt_query);
		}
		$id_bud = 0;

		print "<div class=\"row\"><table><tr><th>".htmlentities($msg['acquisition_rub'], ENT_QUOTES, $charset)."</th><th>".htmlentities($msg['acquisition_rub_sol'], ENT_QUOTES, $charset)."</th></tr>";
		$arrayRub=array();
		while($row = pmb_mysql_fetch_object($res)) {
							
			$new_id_bud = $row->num_budget;
			if ($new_id_bud != $id_bud) {
				//Affichage des rubriques précédentes
				if (count($arrayRub)) {
					//tri des rubriques par ordre alphabétique
					usort($arrayRub, "sort_array_rub");
					foreach ($arrayRub as $rub) {
						print "<tr><td><div class='child_tab'>";
						print pmb_bidi("
								<a href='#' onclick=\"set_parent('$caller', '".$rub["id_rubrique"]."', '".htmlentities(addslashes($rub["lib_bud"].":".$rub["lib_rub_no_html"]),ENT_QUOTES, $charset)."' )\" ><span ".$rub["cl"].">".$rub["lib_rub"]."</span></a>
					</div></td><td style='text-align:right;'><span ".$rub["cl"].">".$rub["sol"]."</span></td></tr>");
					}
				}
				//Affichage budget
				$id_bud = $new_id_bud;
				print pmb_bidi("<tr><td>".htmlentities($row->lib_bud, ENT_QUOTES, $charset)."</td>");
				if($row->type_budget) {
					$aff_glo = true;
					$mnt = $row->montant_global;	
					$cal = budgets::calcEngagement($id_bud);
					if($cal > $mnt) $sol=0; else $sol = $mnt-$cal;
					$sol = number_format($sol, 2,'.','' );
					if($cal > $mnt*($row->seuil_alerte/100)) $alert = true; else $alert = false;					
				} else {
					$aff_glo = false;
				}
				print "<td></td></tr>";
				//Rubriques
				$arrayRub=array();
			}
			$tab_rub = rubriques::listAncetres($row->id_rubrique, true);
			
			$lib_rub = '';
			$lib_rub_no_html = "" ;
			foreach ($tab_rub as $value) {
				$lib_rub.= htmlentities($value[1], ENT_QUOTES, $charset);
				$lib_rub_no_html.= $value[1];
				if($value[0] != $row->id_rubrique) $lib_rub.= ":";
			}
			if(!$aff_glo) {
				$mnt = $row->montant;
				$cal = rubriques::calcEngagement($row->id_rubrique);
				if($cal > $mnt) $sol=0; else $sol = $mnt-$cal;
				$sol = number_format($sol, 2,'.','' );
				if($cal >= $mnt*($row->seuil_alerte/100)) $alert = true; else $alert = false;
			}				
			if ($alert) $cl = "class='erreur' "; else $cl= '';
			
			$arrayRub[] = array(
					"id_rubrique"=>$row->id_rubrique,
					"lib_bud"=>$row->lib_bud,
					"lib_rub_no_html"=>$lib_rub_no_html,
					"cl"=>$cl,
					"lib_rub"=>$lib_rub,
					"sol"=>$sol
			);			
		}

		//Affichage des rubriques restantes
		if (count($arrayRub)) {
			//tri des rubriques par ordre alphabétique
			usort($arrayRub, "sort_array_rub");
			foreach ($arrayRub as $rub) {
				print "<tr><td><div class='child_tab'>";
				print pmb_bidi("
						<a href='#' onclick=\"set_parent('$caller', '".$rub["id_rubrique"]."', '".htmlentities(addslashes($rub["lib_bud"].":".$rub["lib_rub_no_html"]),ENT_QUOTES, $charset)."' )\" ><span ".$rub["cl"].">".$rub["lib_rub"]."</span></a>
					</div></td><td style='text-align:right;'><span ".$rub["cl"].">".$rub["sol"]."</span></td></tr>");
			}
		}		
		
		print "</table>";
		pmb_mysql_free_result($res);

		// affichage pagination
		print "<hr /><div class='center'>";
		if(!$acquisition_budget_show_all){
			$base_url.="&elt_query=".$elt_query;
			$nav_bar = aff_pagination ($base_url, $nbr_lignes, $nb_per_page, $page, 10, true, true) ;
			print $nav_bar;
		}
		print "</div></div>";
	}
}

print $sel_footer;