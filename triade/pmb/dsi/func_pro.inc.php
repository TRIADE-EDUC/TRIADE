<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_pro.inc.php,v 1.57 2018-12-28 09:16:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function get_bannette_pro($title_form, $message, $form_action, $form_cb="") {
	global $dsi_search_bannette_tmpl;
	global $id_classement, $nb_per_page;
	
	if (!isset($nb_per_page) || !$nb_per_page) {
		$nb_per_page = 10;
	}
	
	$dsi_search_tmpl = $dsi_search_bannette_tmpl;
	$dsi_search_tmpl = str_replace("!!titre_formulaire!!", $title_form, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!form_action!!", $form_action, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!message!!", $message, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!cb_initial!!", $form_cb, $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!classement!!", gen_liste_classement("BAN", $id_classement, "this.form.submit();")  , $dsi_search_tmpl);
	$dsi_search_tmpl = str_replace("!!nb_per_page!!", $nb_per_page  , $dsi_search_tmpl);
	return $dsi_search_tmpl;
}

function dsi_list_bannettes($form_cb="", $id_bannette=0, $id_classement="") {
	global $dbh, $msg, $charset;
	global $page, $nbr_lignes;
	global $dsi_list_tmpl, $base_path;

	// nombre de références par pages
	$nb_per_page = 10;
	
	if ($form_cb) {
		$form_cb = str_replace("*", "%", $form_cb) ;
		$clause = "WHERE nom_bannette like '%$form_cb%' and proprio_bannette=0" ;
	} else $clause = "WHERE proprio_bannette=0" ;
	if ($id_classement===0) $clause.= " and num_classement=0 "; 
	elseif ($id_classement>0) $clause.= " and num_classement='$id_classement' " ;

	if(!$nbr_lignes) {
		$requete = "SELECT COUNT(1) FROM bannettes $clause ";
		$res = pmb_mysql_query($requete, $dbh);
		$nbr_lignes = @pmb_mysql_result($res, 0, 0);
	}

	if (!$page) $page=1;
	$debut = ($page-1)*$nb_per_page;

	if($nbr_lignes) {

		// on lance la vraie requête
		$requete = "SELECT id_bannette, nom_bannette, comment_gestion FROM bannettes $clause ORDER BY nom_bannette, id_bannette LIMIT $debut,$nb_per_page ";
		$res = @pmb_mysql_query($requete, $dbh);

		$parity = 0;
		$bann_list = "";
		$ban_trouvees =  pmb_mysql_num_rows($res) ;
		while(($bann=pmb_mysql_fetch_object($res))) {
			if ($parity % 2) $pair_impair = "even";
				else $pair_impair = "odd";
			$td_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./dsi.php?categ=bannettes&sub=pro&id_bannette=$bann->id_bannette&suite=acces&id_classement=$id_classement';\" ";
			$bann_list .= "<tr class='$pair_impair' $td_javascript style='cursor: pointer' >";
			$bann_list .= "
				<td width='70%'>
					<strong>".htmlentities($bann->nom_bannette,ENT_QUOTES, $charset)."</strong>
					<br />(".htmlentities($bann->comment_gestion,ENT_QUOTES, $charset).")
					</td>";
			$bann_list .= "
				<td width='30%'>
					<a href='./dsi.php?categ=bannettes&sub=pro&suite=affect_equation&id_bannette=".$bann->id_bannette."'>".$msg['dsi_ban_affect_equation']."</a>
					<br />
					<a href='./dsi.php?categ=bannettes&sub=pro&suite=affect_lecteurs&id_bannette=".$bann->id_bannette."'>".$msg['dsi_ban_affect_lecteurs']."</a>
					</td>";
			$bann_list .= "</tr>";
			$parity += 1;
		}
		pmb_mysql_free_result($res);

		// affichage de la barre de navig
		$url_base = $base_path."/dsi.php?categ=bannettes&sub=pro&form_cb=".rawurlencode($form_cb)."&id_classement=$id_classement" ;
		$nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		
		$dsi_list_tmpl = str_replace("!!cle!!", $form_cb, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!list!!", $bann_list, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!nav_bar!!", $nav_bar, $dsi_list_tmpl);
		$dsi_list_tmpl = str_replace("!!message_trouve!!", $msg['dsi_ban_trouvees'], $dsi_list_tmpl);
		
		return $dsi_list_tmpl;
	} else return $msg['dsi_no_ban_found'] ;
}

function bannette_equation ($nom="", $id_bannette=0) {
	global $dsi_bannette_equation_assoce, $msg, $dbh, $id_classement ;
	global $charset ;
	global $faire;
	global $page, $nbr_lignes, $nb_per_page;
	
	if (!$id_classement) $id_classement=0;
	$link_pagination = "";
	if($page > 1) {
		$link_pagination .= "&page=".$page."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page;
	}
	$url_base = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=affect_equation"; 
	$url_modif = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=acces"; 
	// $detail_bannette = "<h3>$nom &nbsp;<input type='button' class='bouton' value=\"$msg[dsi_bt_modifier_ban]\" onclick=\"document.location='$url_modif';\" /></h3>";
	if ($id_classement>0) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations left join bannette_equation on num_equation=id_equation where proprio_equation=0 and num_classement='$id_classement' order by nom_equation " ;
	elseif ($id_classement==0) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations left join bannette_equation on num_equation=id_equation where proprio_equation=0 order by nom_equation " ;
	elseif ($id_classement==-1) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations, bannette_equation where num_bannette=$id_bannette and num_equation=id_equation and proprio_equation=0 order by nom_equation " ;
	$res = pmb_mysql_query($requete, $dbh) or die ($requete) ;
	$parity = 0;
	$equ_trouvees =  pmb_mysql_num_rows($res) ;
	$equations = '';
	while ($equa=pmb_mysql_fetch_object($res)) {
		$equations .= "<input type='checkbox' name='bannette_equation[]' value='$equa->id_equation' ";
		$requete_affect = "SELECT 1 FROM bannette_equation where num_equation='$equa->id_equation' and num_bannette='$id_bannette' ";
		$res_affect = pmb_mysql_query($requete_affect, $dbh);
		if (pmb_mysql_num_rows($res_affect)) $equations .= "checked" ;
		$equations .= " /> $equa->nom_equation<br />";
	}
	$dsi_bannette_equation_assoce = str_replace("!!form_action!!", $url_base."&faire=enregistrer".$link_pagination, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!nom_bannette!!", $nom, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!equations!!", $equations, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!id_classement_anc!!", $id_classement, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!id_bannette!!", $id_bannette, $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace("!!classement!!", 
		gen_liste ("select id_classement, nom_classement from classements where id_classement=1 union select 0 as id_classement, '".$msg['dsi_all_classements']."' as nom_classement UNION select id_classement, nom_classement from classements where type_classement='EQU' order by nom_classement", "id_classement", "nom_classement", "id_classement", "this.form.faire.value=''; this.form.submit();", $id_classement, "", "",-1,$msg['dsi_ban_equation_affectees'],0)
		, $dsi_bannette_equation_assoce);
	if($faire == "enregistrer") {
		$dsi_bannette_equation_assoce = str_replace("!!bannette_equations_saved!!", "<div class='erreur'>".$msg["dsi_bannette_equations_update"]."</div><br />", $dsi_bannette_equation_assoce);
	} else {
		$dsi_bannette_equation_assoce = str_replace("!!bannette_equations_saved!!", "", $dsi_bannette_equation_assoce);
	}
	// afin de revenir où on était : $form_cb, le critère de recherche
	global $form_cb ;
	$dsi_bannette_equation_assoce = str_replace('!!form_cb!!', urlencode($form_cb),  $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace('!!form_cb_hidden!!', htmlentities($form_cb,ENT_QUOTES, $charset),  $dsi_bannette_equation_assoce);
	$dsi_bannette_equation_assoce = str_replace('!!link_pagination!!', $link_pagination,  $dsi_bannette_equation_assoce);
	
	return $dsi_bannette_equation_assoce ;
}	