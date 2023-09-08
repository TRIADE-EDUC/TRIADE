<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_equ.inc.php,v 1.20 2018-06-18 14:08:27 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function get_equation($title_form, $message, $form_action, $form_cb="") {
	global $dsi_search_equation_tmpl;
	global $id_classement ;
	
	$dsi_search_tmpl = $dsi_search_equation_tmpl;
	$dsi_search_tmpl = str_replace("!!titre_formulaire!!", $title_form, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!form_action!!", $form_action, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!message!!", $message, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!cb_initial!!", $form_cb, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!classement!!", gen_liste_classement("EQU", $id_classement, "this.form.submit();")  , $dsi_search_tmpl);
	return $dsi_search_tmpl;
}

function dsi_list_equations($form_cb="") {
	global $dbh, $msg;
	global $page, $nbr_lignes;
	global $dsi_list_tmpl;
	global $id_classement, $base_path;
	global $nb_per_page;
	
	// nombre de références par pages
	if(!$nb_per_page) {
		$nb_per_page = 10;
	}
	
	if ($form_cb) {
		$form_cb = str_replace("*", "%", $form_cb) ;
		$clause = "WHERE nom_equation like '%$form_cb%' and proprio_equation=0" ;
	} else $clause = "WHERE proprio_equation=0" ;
	if ($id_classement===0) $clause.= " and num_classement=0 "; 
	elseif ($id_classement>0) $clause.= " and num_classement='$id_classement' " ;

	if(!$nbr_lignes) {
		$requete = "SELECT COUNT(1) FROM equations $clause ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = @pmb_mysql_result($res, 0, 0);
	}

	if (!$page) $page=1;
	$debut = ($page-1)*$nb_per_page;

	if($nbr_lignes) {
		// on lance la vraie requête
		$requete = "SELECT id_equation, nom_equation, comment_equation, num_classement FROM equations $clause ORDER BY nom_equation, id_equation LIMIT $debut,$nb_per_page ";
		$res = @pmb_mysql_query($requete, $dbh);

		$equation_list = '';
		$equation_list .= "
		    <tr>
		      <th>".$msg['66']."</th>
		      <th>".$msg['67']."</th>
		      <th></th>
		    </tr>
		";
		$parity = 0;
		while(($equa=pmb_mysql_fetch_object($res))) {
			if ($parity % 2) $pair_impair = "even";
			else $pair_impair = "odd";
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./dsi.php?categ=equations&sub=gestion&id_equation=$equa->id_equation&suite=acces';\" ";
			$requete_cla = "SELECT id_classement, nom_classement FROM classements where id_classement='$equa->num_classement' and type_classement='EQU' ";
			$res_cla = pmb_mysql_query($requete_cla, $dbh);
			if (pmb_mysql_num_rows($res_cla)) {
				$cla=pmb_mysql_fetch_object($res_cla) ;
				$lib = $cla->nom_classement ;
			} else $lib=""; 
			$equation_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
			$equation_list .= "
				<td>
					<strong>".$equa->id_equation."</strong>
				</td>
				<td>
					<strong>$equa->nom_equation</strong><br />
					".($equa->comment_equation ? "($equa->comment_equation)" : "&nbsp;")."
				</td>
				<td>
        			$lib
				</td>
			</tr>";
			$parity += 1;
		}
		pmb_mysql_free_result($res);

		// affichage de la barre de navig
		$url_base = $base_path."/dsi.php?categ=equations&suite=search&form_cb=".rawurlencode($form_cb)."&id_classement=$id_classement" ;
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		
		$dsi_list_tmpl = str_replace("!!cle!!", $form_cb, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!list!!", $equation_list, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!message_trouve!!", $msg['dsi_equ_trouvees'], $dsi_list_tmpl);
		
		return $dsi_list_tmpl;
	} else {
		return $msg['dsi_no_equation'];
	}
}
	