<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id$

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $opac_fonction_affichage_liste_bull;
if($opac_fonction_affichage_liste_bull && file_exists($base_path."/includes/".$opac_fonction_affichage_liste_bull.".inc.php")){
	require_once($base_path."/includes/".$opac_fonction_affichage_liste_bull.".inc.php");
}

function affichage_liste_bulletins_normale($res, $print = true) {
	global $charset;

	$return = '';

	while(($tableau=pmb_mysql_fetch_array($res))) {
		$return.= "<span class='liste_bulletins'>";
		if ($tableau['nbexplnum']>0){
			$padding = "";
			$return.= '<img src="'.get_url_icon('attachment.png').'" alt="">';
		}
		else
			$padding = "style=\"padding-left:11px;\"";

		$return.= "<a href=./index.php?lvl=bulletin_display&id=".$tableau['bulletin_id']." ".$padding.">".$tableau['bulletin_numero'];
		if ($tableau['mention_date']) $return.= pmb_bidi(" (".$tableau['mention_date'].")\n");
			elseif ($tableau['date_date']) $return.= pmb_bidi(" (".formatdate($tableau['date_date']).")\n");
		if ($tableau['bulletin_titre']) $return.= pmb_bidi(" : ".htmlentities($tableau['bulletin_titre'],ENT_QUOTES, $charset)."\n");
		$return.= "</a> ;";
		$return.= "</span>\n";
	}
	if (!$print) {
		return $return;
	}
	print $return;
}

function affichage_liste_bulletins_tableau($res, $print = true) {
    //list_opac_bulletins_ui utilisé à la place
	return '';
}


function affichage_liste_bulletins_depliable($res, $print = true) {
	global $charset;
	$resultat_aff="";
	while(($tableau=pmb_mysql_fetch_array($res))) {

		$sql = "SELECT COUNT(1) FROM explnum WHERE explnum_bulletin='".$tableau["bulletin_id"]."'";
		$result = pmb_mysql_query($sql);
		$count=pmb_mysql_result($result, 0, 0);

		$titre="";
		if($count)$titre.= '<img src="'.get_url_icon('attachment.png').'" alt="">';

		$titre.= $tableau['bulletin_numero'];
		if ($tableau['mention_date']) $titre.= pmb_bidi(" (".$tableau['mention_date'].")\n");
		elseif ($tableau['date_date']) $titre.= pmb_bidi(" (".formatdate($tableau['date_date']).")\n");
		if ($tableau['bulletin_titre']) $titre.= pmb_bidi(" : ".htmlentities($tableau['bulletin_titre'],ENT_QUOTES, $charset)."\n");

		//	($id,$titre,$contenu,$maximise=0) {
		$resultat_aff.=gen_plus("bull_id_".$tableau['bulletin_id'],$titre, get_bulletin_list_func($tableau['bulletin_id']) );
	}
	if (!$print) {
		return $resultat_aff;
	}
	print $resultat_aff;
}


function get_bulletin_list_func($id){
	global $charset,$msg,$css;
	global $opac_visionneuse_allow,$opac_cart_allow,$opac_max_resa;
	global $begin_result_liste,$notice_header,$opac_resa_planning;
	global $opac_show_exemplaires,$fonction,$opac_resa_popup,$opac_resa,$popup_resa,$allow_book;
	global $opac_perio_a2z_show_bulletin_notice;
	global $opac_show_links_invisible_docnums;

	$resultat_aff="";
	$libelle = $msg[270];
	$largeur = 500;
	$requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, bulletin_titre, bulletin_cb, date_format(date_date, '".$msg["format_date_sql"]."') as aff_date_date,num_notice FROM bulletins WHERE bulletin_id='$id'";

	$res = pmb_mysql_query($requete);
	while(($obj=pmb_mysql_fetch_array($res))) {
		//on cherches des documents numériques
		$req = "select explnum_id from explnum where explnum_bulletin = ".$obj["bulletin_id"];
		$resultat = pmb_mysql_query($req) or die ($req." ".pmb_mysql_error());
		$nb_ex = pmb_mysql_num_rows($resultat);
		//on met le nécessaire pour la visionneuse
		if($opac_visionneuse_allow && $nb_ex){
			$resultat_aff.= "
			<script>
				function sendToVisionneuse(explnum_id){
					document.getElementById('visionneuseIframe').src = 'visionneuse.php?mode=perio_bulletin&idperio=".$obj['bulletin_notice']."'+(typeof(explnum_id) != 'undefined' ? '&explnum_id='+explnum_id+\"\" : '\'');
				}
			</script>";
		}
		$typdocchapeau="a";
		$icon="";
		$requete3 = "SELECT notice_id,typdoc FROM notices WHERE notice_id='".$obj["bulletin_notice"]."' ";
		$res3 = pmb_mysql_query($requete3);
		while(($obj3=pmb_mysql_fetch_object($res3))) {
			$notice3 = new notice($obj3->notice_id);
			$typdocchapeau=$obj3->typdoc;
		}
		$notice3->fetch_visibilite();
		if (!$icon) $icon="icon_per.gif";
		$icon_doc = marc_list_collection::get_instance('icondoc');
		$icon = $icon_doc->table["b".$typdocchapeau];

		$res_print .= "<h3><img src='".get_url_icon($icon)."' alt=''/> ".$notice3->print_resume(1,$css)."."." <b>".$obj["bulletin_numero"]."</b></h3>\n";
		$num_notice=$obj['num_notice'];

		if ($obj['date_date']) $res_print .= $msg['bull_date_date']." &nbsp;".$obj['aff_date_date']." \n";
		if ($obj['bulletin_cb']) {
			$res_print .= "<br />".$msg["code_start"]." ".htmlentities($obj['bulletin_cb'],ENT_QUOTES, $charset)."\n";
			$code_cb_bulletin = $obj['bulletin_cb'];
		}
	}

	do_image($res_print, $code_cb_bulletin, 0 ) ;
	if ($opac_perio_a2z_show_bulletin_notice && $num_notice) {
		// Il y a une notice de bulletin
		$resultat_aff.= $res_print ;
		global $opac_notices_depliable;
		global $seule;
		$memo_opac_notices_depliable=$opac_notices_depliable;
		$memo_seule=$seule;
		$opac_notices_depliable = 0;
		$seule=1;
		$resultat_aff.= pmb_bidi(aff_notice($num_notice,0,0)) ;
		$opac_notices_depliable=$memo_opac_notices_depliable;
		$seule=$memo_seule;
	} else {
		// construction des dépouillements
		$requete = "SELECT * FROM analysis, notices, notice_statut WHERE analysis_bulletin='$id' AND notice_id = analysis_notice AND statut = id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").") ";
		$res = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			$depouill= "<h3>".$msg['bull_dep']."</h3>";
			if ($opac_notices_depliable) $depouill .= $begin_result_liste;
			if ($opac_cart_allow) $depouill.="<a href=\"cart_info.php?id=".$id."&lvl=analysis&header=".rawurlencode(strip_tags($notice_header))."\" target=\"cart_info\" class=\"img_basket\" title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a>";
			$depouill.= "<blockquote role='presentation'>";
			while(($obj=pmb_mysql_fetch_array($res))) {
				$depouill.= pmb_bidi(aff_notice($obj["analysis_notice"]));
			}
			$depouill.= "</blockquote>";
		}

		$resultat_aff.= $res_print ;
		$resultat_aff.= $depouill ;
		if ($notice3->visu_expl && (!$notice3->visu_expl_abon || ($notice3->visu_expl_abon && $_SESSION["user_code"])))	{
			if (!$opac_resa_planning) {
				$resa_check=check_statut(0,$id) ;
				if ($resa_check) {
					$requete_resa = "SELECT count(1) FROM resa WHERE resa_idbulletin='$id'";
					$nb_resa_encours = pmb_mysql_result(pmb_mysql_query($requete_resa), 0, 0) ;
					if ($nb_resa_encours) $message_nbresa = str_replace("!!nbresa!!", $nb_resa_encours, $msg["resa_nb_deja_resa"]) ;

					if (($_SESSION["user_code"] && $allow_book) && $opac_resa && !$popup_resa) {
						$ret_resa .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
						if ($opac_max_resa==0 || $opac_max_resa>$nb_resa_encours) {
						    $ret_resa .= record_display::get_display_action_resa_expl(0, $id);
							$ret_resa .= $message_nbresa ;
						} else {
						    $ret_resa .= str_replace("!!nb_max_resa!!", $opac_max_resa, $msg["resa_nb_max_resa"]) ;
						}
						$ret_resa.= "<br />";
					} elseif (!($_SESSION["user_code"]) && $opac_resa && !$popup_resa) {
						// utilisateur pas connecté
						// préparation lien réservation sans être connecté
						$ret_resa .= "<h3>".$msg["bulletin_display_resa"]."</h3>";
						$ret_resa .= record_display::get_display_action_resa_expl(0, $id);
						$ret_resa .= $message_nbresa ;
						$ret_resa .= "<br />";
					} else {
					    $ret_resa = "";
					}
					$resultat_aff.= pmb_bidi($ret_resa) ;
				}
			}
			if ($opac_show_exemplaires) {
				if($fonction=='notice_affichage_custom_bretagne')	$resultat_aff.= pmb_bidi(notice_affichage_custom_bretagne::expl_list("m",0,$id));
				else $resultat_aff.= pmb_bidi(notice_affichage::expl_list("m",0,$id,0));
			}
		}
		if ($opac_show_links_invisible_docnums || (is_null($notice3->dom_2) && $notice3->visu_explnum && (!$notice3->visu_explnum_abon || ($notice3->visu_explnum_abon && $_SESSION["user_code"])))  || ($notice3->rights & 16) ) {
			if (($explnum = show_explnum_per_notice(0, $id, ''))) $resultat_aff.= pmb_bidi("<a name='docnum'><h3>".$msg["explnum"]."</h3></a>".$explnum);
		}
	}
	pmb_mysql_free_result($res);

	$resultat_aff.= notice_affichage::autres_lectures (0,$id);
	return($resultat_aff);
}
?>