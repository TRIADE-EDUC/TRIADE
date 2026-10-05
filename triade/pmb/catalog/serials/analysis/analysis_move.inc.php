<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analysis_move.inc.php,v 1.6 2022/01/07 11:40:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset, $bul_id, $analysis_id, $to_bul;
global $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid;
global $serial_header;

//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_j = $dom_1->getJoin($PMBuserid,8,'bulletin_notice');
	$q = "select count(1) from bulletins $acces_j where bulletin_id=".$bul_id;
	$r = pmb_mysql_query($q);
	if ($r) {
		if(pmb_mysql_result($r,0,0)==0) {
			$acces_m=0;
		}
	} else {
		$acces_m=0;
	}
}

if ($acces_m==0) {

	if (!$analysis_id) {
		error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
	} else {
		error_message('', htmlentities($dom_1->getComment('mod_depo_error'), ENT_QUOTES, $charset), 1, '');
	}

} else {
	
	if(!$to_bul) {
		// affichage d'un form pour déplacer un article de périodique
		echo str_replace('!!page_title!!', $msg['4000'].$msg['1003'].$msg['analysis_move'], $serial_header);
		
		// on instancie le truc
		$myAnalysis = new analysis($analysis_id, $bul_id);
	
		$myBul = new bulletinage($bul_id);
		// lien vers la notice chapeau
		$link_parent = "<a href=\"./catalog.php?categ=serials\">";
		$link_parent .= $msg[4010]."</a>";
		$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		$link_parent .= "<a href=\"".serial::get_permalink($myBul->bulletin_notice)."\">".$myBul->get_serial()->tit1.'</a>';
		$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		$link_parent .= "<a href=\"".bulletinage::get_permalink($bul_id)."\">";
		if ($myBul->bulletin_numero) $link_parent .= $myBul->bulletin_numero." ";
		if ($myBul->mention_date) $link_parent .= " (".$myBul->mention_date.") "; 
		$link_parent .= "[".$myBul->aff_date_date."]";  
		$link_parent .= "</a> <img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		$link_parent .= "<h3>".$myAnalysis->tit1."</h3>";
		
		print pmb_bidi("<div class='row'><div class='perio-barre'>".$link_parent."</div></div><br />");
		
		print "<div class='row'>".$myAnalysis->move_form()."</div>";
	} else {

		// routine de déplacmeent
		$myAnalysis = new analysis($analysis_id, $bul_id);
		$myAnalysis->move($to_bul);
		
		print pmb_bidi("<script type=\"text/javascript\">document.location='".bulletinage::get_permalink($to_bul)."'</script>");
		//Redirection
	}

	
	
}
?>