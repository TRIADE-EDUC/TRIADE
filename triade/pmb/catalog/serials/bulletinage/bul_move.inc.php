<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_move.inc.php,v 1.2 2021/03/18 08:32:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $bul_id, $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid;
global $to_serial, $serial_header;

//verification des droits de modification notice
$acces_m=1;
if ($bul_id && $gestion_acces_active==1 && $gestion_acces_user_notice==1) {
    require_once("$class_path/acces.class.php");
    $ac= new acces();
    $dom_1= $ac->setDomain(1);
    $acces_j = $dom_1->getJoin($PMBuserid,8,'bulletin_notice');
    $q = "select count(1) from bulletins $acces_j where bulletin_id = $bul_id ";
    $r = pmb_mysql_query($q);
    if(pmb_mysql_result($r,0,0)==0) {
        $acces_m=0;
    }
}

if ($acces_m==0) {
    error_message('', htmlentities($dom_1->getComment('mod_bull_error'), ENT_QUOTES, $charset), 1, '');
} else {
	if(!$to_serial) {
		// affichage d'un form pour déplacer un bulletin de périodique
		echo str_replace('!!page_title!!', $msg['4000'].$msg['1003'].$msg['bulletin_move'], $serial_header);
		
// 		// on instancie le truc
		$myBulletinage = new bulletinage($bul_id);
	
		// lien vers la notice chapeau
		$link_parent = "<a href=\"./catalog.php?categ=serials\">";
		$link_parent .= $msg[4010]."</a>";
		$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		$link_parent .= "<a href=\"".serial::get_permalink($myBulletinage->bulletin_notice)."\">".$myBulletinage->get_serial()->tit1.'</a>';
		$link_parent .= "<img src='".get_url_icon('d.gif')."' class='align_middle' hspace=\"5\">";
		$link_parent .= "<a href=\"".bulletinage::get_permalink($bul_id)."\">";
		if ($myBulletinage->bulletin_numero) $link_parent .= $myBulletinage->bulletin_numero." ";
		if ($myBulletinage->mention_date) $link_parent .= " (".$myBulletinage->mention_date.") "; 
		$link_parent .= "[".$myBulletinage->aff_date_date."]";  
		$link_parent .= "</a>";
		
		print pmb_bidi("<div class='row'><div class='perio-barre'>".$link_parent."</div></div><br />");
		
		print "<div class='row'>".$myBulletinage->move_form()."</div>";
	} else {

		// routine de déplacmeent
	    $myBulletinage = new bulletinage($bul_id);
	    $myBulletinage->move($to_serial);
		
	    //Redirection
	    print pmb_bidi("<script type=\"text/javascript\">document.location='".bulletinage::get_permalink($bul_id)."'</script>");
	}
}
?>