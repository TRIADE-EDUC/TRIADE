<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bulletin_affichage.inc.php,v 1.31 2023/12/13 14:45:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path;
require_once($base_path.'/includes/explnum.inc.php');
require_once($base_path.'/classes/notice_affichage.class.php');
require_once($base_path.'/classes/notice.class.php');

function bulletin_affichage_reduit($id, $no_link=0) {
	global $msg, $css, $charset, $opac_show_exemplaires;

	$requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, bulletin_titre, bulletin_cb, date_format(date_date, '".$msg["format_date_sql"]."') as aff_date_date FROM bulletins WHERE bulletin_id='$id'";
	$res3 = pmb_mysql_query($requete);
	$obj = pmb_mysql_fetch_object($res3) ;
	$notice3 = new notice($obj->bulletin_notice);

	$res_print = "<h3><img src='".get_url_icon('icon_per.gif')."' alt=''>" ;
	if ($no_link) $res_print .= $notice3->tit1 ;
		else $res_print .= $notice3->print_resume(1,$css) ;
	$res_print .= ". <b>".$obj->bulletin_numero."</b></h3>\n";

	if ($obj->bulletin_titre) {
		$res_print .=  htmlentities($obj->bulletin_titre,ENT_QUOTES, $charset)."\n";
		$suite_aff = " : " ;
	} else $suite_aff = "" ;
	if ($obj->mention_date) $res_print .=  $suite_aff.$msg['bull_mention_date'].$obj->mention_date."\n";
	if ($obj->date_date) $res_print .= "<br />".$msg['bull_date_date']." ".$obj->aff_date_date." \n";
	$code_cb_bulletin = '';
	if ($obj->bulletin_cb) {
		$res_print .= "<br />".$msg['code_start']." ".htmlentities($obj->bulletin_cb,ENT_QUOTES, $charset)."\n";
		$code_cb_bulletin = $obj->bulletin_cb;
	}

	do_image($res_print, $code_cb_bulletin, 0 ) ;

	if ($opac_show_exemplaires) $res_print .= pmb_bidi(notice_affichage::expl_list("m",0,$obj->bulletin_id));

	return $res_print ;

}

function bulletin_affichage($id,$type=""){
	global $msg, $begin_result_liste;
	global $opac_show_exemplaires, $css, $charset, $opac_resa_planning, $opac_resa, $opac_resa_popup, $opac_max_resa, $popup_resa, $opac_cart_allow, $allow_book;

	$display ="";
	$code_cb_bulletin = '';
	$ret_resa = '';
	$message_nbresa = '';
	$fonction = '';
	$requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, bulletin_titre, bulletin_cb, date_format(date_date, '".$msg["format_date_sql"]."') as aff_date_date,num_notice FROM bulletins WHERE bulletin_id='$id'";
	$res = pmb_mysql_query($requete);
	while(($obj=pmb_mysql_fetch_array($res))) {
		$requete3 = "SELECT notice_id FROM notices WHERE notice_id='".$obj["bulletin_notice"]."' ";
		$res3 = pmb_mysql_query($requete3);
		while(($obj3=pmb_mysql_fetch_object($res3))) {
			$notice3 = new notice($obj3->notice_id);
		}
		$notice3->fetch_visibilite();
		//on vient poser l'ancre des docnums...
		$req = "select explnum_id from explnum where explnum_bulletin = ".$obj["bulletin_id"];
		$resultat = pmb_mysql_query($req) or die ($req." ".pmb_mysql_error());
		$nb_ex = pmb_mysql_num_rows($resultat);
		$res_print = "<h3><img src='".get_url_icon('icon_per.gif')."' alt=''> ".$notice3->print_resume(1,$css)."."." <b>".$obj["bulletin_numero"]."</b>".($nb_ex ? "&nbsp;<a href='#docnum'>".($nb_ex > 1 ? "<img src='".get_url_icon("globe_rouge.png")."' />" : "<img src='".get_url_icon("globe_orange.png")."' />")."</a>" : "")."</h3>\n";
		$num_notice=$obj['num_notice'];
		if ($obj['bulletin_titre']) {
			$res_print .=  htmlentities($obj['bulletin_titre'],ENT_QUOTES, $charset)."<br />";
		}
		if ($obj['mention_date']) $res_print .= $msg['bull_mention_date']." &nbsp;".$obj['mention_date']."\n";
		if ($obj['date_date']) $res_print .= "<br />".$msg['bull_date_date']." &nbsp;".$obj['aff_date_date']." \n";
		if($type != "visionneuse" && $nb_ex) $res_print .= "<br /><a href='#docnum'>".($nb_ex > 1 ? "<img src='".get_url_icon("globe_rouge.png")."' alt=''/>" : "<img src='".get_url_icon("globe_orange.png")."' alt=''/>")."</a>";
		if ($obj['bulletin_cb']) {
			$res_print .= "<br />".$msg["code_start"]." ".htmlentities($obj['bulletin_cb'],ENT_QUOTES, $charset)."\n";
			$code_cb_bulletin = $obj['bulletin_cb'];
		}
	}

	do_image($res_print, $code_cb_bulletin, 0 ) ;
	if ($num_notice) {
		// Il y a une notice de bulletin
		$display .= $res_print ;
		$opac_notices_depliable = 0;
		$seule=1;
		//$display .= pmb_bidi(aff_notice($num_notice,0,0)) ;
		if($type == "visionneuse") $display .= pmb_bidi(aff_notice($num_notice,1,1,0,"",0,1));
		else $display .= pmb_bidi(aff_notice($num_notice,0,1,0,"",0));
	} else {
		// construction des dépouillements
		$depouill= "<br /><h3>".$msg['bull_dep']."</h3>";
		$requete = "SELECT * FROM analysis, notices, notice_statut WHERE analysis_bulletin='$id' AND notice_id = analysis_notice AND statut = id_notice_statut and ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").") ";
		$res = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res)) {
			if ($opac_notices_depliable) $depouill .= $begin_result_liste;
			if ($opac_cart_allow) $depouill.="<a href=\"cart_info.php?id=".$id."&lvl=analysis&header=".rawurlencode(strip_tags($notice_header))."\" target=\"cart_info\" class=\"img_basket\" title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a>";
			$depouill.= "<blockquote role='presentation'>";
			while(($obj=pmb_mysql_fetch_array($res))) {
				$depouill.= pmb_bidi(aff_notice($obj["analysis_notice"]));
			}
			$depouill.= "</blockquote>";
		} else $depouill = $msg["no_analysis"];

		$display .= $res_print ;
		$display .= $depouill ;
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
					} elseif ($fonction=='notice_affichage_custom_bretagne') {
					    
					} else $ret_resa = "";
					$display .= pmb_bidi($ret_resa) ;
				}
			}

			if ($opac_show_exemplaires) {
				if($fonction=='notice_affichage_custom_bretagne')
					$display .= pmb_bidi(notice_affichage_custom_bretagne::expl_list("m",0,$id));
				else
					$display .= pmb_bidi(notice_affichage::expl_list("m",0,$id));
			}
		}
		if ($type != "visionneuse" && $notice3->visu_explnum && (!$notice3->visu_explnum_abon || ($notice3->visu_explnum_abon && $_SESSION["user_code"]))) {
			if (($explnum = show_explnum_per_notice(0, $id, '')))
				$display .=  pmb_bidi("<a name='docnum'><h3>".$msg["explnum"]."</h3></a>".$explnum);
		}
	}
	pmb_mysql_free_result($res);

	$display .=  notice_affichage::autres_lectures (0,$id) ;

	return $display;
}

function bulletin_header($id){
	global $msg;

	$header ="";
	$query = "select tit1,bulletin_titre,bulletin_numero,mention_date, date_format(date_date, '".$msg["format_date_sql"]."') as aff_date_date from bulletins join notices on bulletin_notice = notice_id where bulletin_id = ".$id;
	$result = pmb_mysql_query($query);
	if(pmb_mysql_num_rows($result)){
		$row = pmb_mysql_fetch_object($result);
		$header = $row->tit1.".";
		if($row->bulletin_numero!= ""){
			$header.=" ".$row->bulletin_numero;
		}
		if($row->mention_date!=""){
			$header.=" ".$row->mention_date;
		}else $header.=" ".$row->aff_date_date;
		if($row->bulletin_titre!=""){
			$header.=" - ".$row->bulletin_titre;
		}
	}
	return $header;
}