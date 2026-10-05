<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// © 2006 mental works / www.mental-works.com contact@mental-works.com
// 	complètement repris et corrigé par PMB Services
// +-------------------------------------------------+
// $Id: addtags.php,v 1.38.2.1.2.1 2025/02/07 13:49:04 qvarin Exp $

use Pmb\Common\Library\CSRF\ParserCSRF;
use Pmb\Common\Library\RGAA\RGAABuilder;

$base_path = ".";
require_once($base_path."/includes/init.inc.php");

global $opac_allow_add_tag, $msg, $charset, $opac_url_base, $noticeid, $ChpTag;
global $opac_rgaa_active;

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

if (!$opac_allow_add_tag) {
    http_response_code(403);
    die();
}

require_once($base_path.'/includes/templates/common.tpl.php');

// classe de gestion des catégories
require_once($base_path.'/classes/categorie.class.php');
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/notice_display.class.php');

// classe indexation interne
require_once($base_path.'/classes/indexint.class.php');

// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

// classe de gestion des réservations
require_once($base_path.'/classes/resa.class.php');

// pour l'affichage correct des notices
require_once($base_path."/includes/templates/notice.tpl.php");
require_once($base_path."/includes/navbar.inc.php");
require_once($base_path."/includes/explnum.inc.php");
require_once($base_path."/includes/notice_affichage.inc.php");
require_once($base_path."/includes/bulletin_affichage.inc.php");

require_once($base_path."/includes/connexion_empr.inc.php");

// autenticazione LDAP - by MaxMan
require_once($base_path."/includes/ldap_auth.inc.php");

// RSS
require_once($base_path."/includes/includes_rss.inc.php");

// pour fonction de formulaire de connexion
require_once($base_path."/includes/empr.inc.php");
// pour fonction de vérification de connexion
require_once($base_path.'/includes/empr_func.inc.php');

if ($opac_allow_add_tag == 0) {
    http_response_code(403);
    die();
}

$noticeid = intval($noticeid);
if (!isset($ChpTag)) {
    $ChpTag = '';
}
$ChpTag = strip_tags($ChpTag);

// par défaut, on suppose que le droit donné par le statut est Ok
$allow_avis = 1 ;
$allow_tag = 1 ;

if ($opac_allow_add_tag == 1) {
    //ajout possible sans authentification
    $log_ok = 1;
} else {
    //Vérification de la session
    $empty_pwd = true;
    $ext_auth = false;
    // si paramétrage authentification particulière et pour la re-authentification ntlm
    if (file_exists($base_path.'/includes/ext_auth.inc.php')) {
        require_once($base_path.'/includes/ext_auth.inc.php');
    }
    $log_ok = connexion_empr();
}

ob_start();

print $popup_header;
print "
	<script>
		// Fonction a utilisier pour l'encodage des URLs en javascript
		function encode_URL(data){
			var docCharSet = document.characterSet ? document.characterSet : document.charset;
			if(docCharSet == \"UTF-8\"){
				return encodeURIComponent(data);
			}else{
				return escape(data);
			}
		}
	</script>
	<div id='att' style='z-Index:1000'></div>";

if ($opac_allow_add_tag == 2 && !$allow_tag) {
    die($popup_footer);
}

print "<div id='titre-popup'>".common::format_title($msg['notice_title_tag'])."</div>";

// Le lecteur a ajouté un mot-clé
if (($ChpTag) && ($log_ok)) {
    $sql = "select index_l from notices where notice_id='".$noticeid."'";
    $r = pmb_mysql_query($sql);
    $row = pmb_mysql_fetch_assoc($r);
    $tags = explode(';', $row['index_l']);
    $tags = array_map('trim', $tags);
    if (in_array(trim($ChpTag), $tags)) {
        echo "<br /><br />".$msg['addtag_exist'];
    } else {
        $sql = "insert into tags (libelle, num_notice,user_code,dateajout) values ('$ChpTag',$noticeid,'". $_SESSION["user_code"] ."',CURRENT_TIMESTAMP())";
        if (pmb_mysql_query($sql)) {
            echo "<div align='center'><br /><br />".$msg['addtag_enregistre']."<br /><br /><a href='#' onclick='window.close()'>".$msg['addtag_fermer']."</a></div>";
        } else {
            echo "<div align='center'><br /><br />".$msg['addtag_pb_enr']."<br /><br /><a href='#' onclick='window.close()'>".$msg['addtag_fermer']."</a></div>";
        }
    }
} else {
    echo "
		<form id='f' name='f' method='post' action='".$opac_url_base."addtags.php'>
			<input type='hidden' name='noticeid' value='$noticeid' />
			$msg[addtag_choisissez]<br />
			<input type='text' id='select' name='select' class='saisie-20emr' completion='keywords' autfield='ChpTag' value='' autocomplete='off' />
			<input class='bouton' value='...' id='select_selection_selector' title='".htmlentities($msg['parcourir'], ENT_QUOTES, $charset)."' onclick=\"openPopUp('".$base_path."/select.php?what=keyword&caller=f&p1=select&p2=ChpTag&deb_rech=', 'selector')\" type='button' />
			<input type='button' class='bouton' value='X' onclick=\"document.getElementById('select').value=''; document.getElementById('ChpTag').value='';\">
			<br /><br />
			$msg[addtag_nouveau]<br />
			<input type='text' id='ChpTag' name='ChpTag' style='width:200px'/>
		    <input type='submit' class='bouton' name='submit' value='".$msg['addtag_bt_ajouter']."' />
		</form>
		<script src='".$base_path."/includes/javascript/popup.js'></script>
		<script src='".$base_path."/includes/javascript/ajax.js'></script>
		<script>
			ajax_parse_dom();
		</script>
			";
}

if (!$log_ok && $opac_allow_add_tag == 2) {
    $lvl = 'tags';
    print do_formulaire_connexion();
    //print $erreur_session ;
}


//Enregistrement du log
$record_log = generate_log('addtag');
if ($record_log) {
    print $record_log->validation_script();
}

print $popup_footer;

$htmltoparse = ob_get_contents();
ob_end_clean();

if ($opac_rgaa_active) {
    $htmltoparse = RGAABuilder::transform($htmltoparse);
}

$parserCSRF = new ParserCSRF();
print $parserCSRF->parseHTML($htmltoparse);

/* Fermeture de la connexion */
pmb_mysql_close();
