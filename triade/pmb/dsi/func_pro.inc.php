<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func_pro.inc.php,v 1.59 2021/12/01 13:09:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function bannette_equation ($nom="", $id_bannette=0) {
	global $dsi_bannette_equation_assoce, $msg, $id_classement ;
	global $charset ;
	global $faire;
	global $page, $nbr_lignes, $nb_per_page;
	
	if (!$id_classement) $id_classement=0;
	$link_pagination = "";
	if($page > 1) {
		$link_pagination .= "&page=".$page."&nbr_lignes=".$nbr_lignes."&nb_per_page=".$nb_per_page;
	}
	$url_base = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=affect_equation"; 
	//$url_modif = "./dsi.php?categ=bannettes&sub=pro&id_bannette=$id_bannette&suite=acces"; 
	// $detail_bannette = "<h3>$nom &nbsp;<input type='button' class='bouton' value=\"$msg[dsi_bt_modifier_ban]\" onclick=\"document.location='$url_modif';\" /></h3>";
	if ($id_classement>0) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations left join bannette_equation on num_equation=id_equation where proprio_equation=0 and num_classement='$id_classement' order by nom_equation " ;
	elseif ($id_classement==0) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations left join bannette_equation on num_equation=id_equation where proprio_equation=0 order by nom_equation " ;
	elseif ($id_classement==-1) $requete = "select distinct id_equation, num_classement, nom_equation, comment_equation, proprio_equation from equations, bannette_equation where num_bannette=$id_bannette and num_equation=id_equation and proprio_equation=0 order by nom_equation " ;
	$res = pmb_mysql_query($requete);
	$equations = '';
	if(pmb_mysql_num_rows($res)) {
		while ($equa=pmb_mysql_fetch_object($res)) {
			$equations .= "<input type='checkbox' name='bannette_equation[]' value='$equa->id_equation' ";
			$requete_affect = "SELECT 1 FROM bannette_equation where num_equation='$equa->id_equation' and num_bannette='$id_bannette' ";
			$res_affect = pmb_mysql_query($requete_affect);
			if (pmb_mysql_num_rows($res_affect)) $equations .= "checked" ;
			$equations .= " /> $equa->nom_equation<br />";
		}
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