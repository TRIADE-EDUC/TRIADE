<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reception.inc.php,v 1.30 2022/10/04 09:20:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $sub, $msg, $charset, $PMBuserid, $database_window_title;
global $liste_transfert,$statut_reception,$section_reception,$info;
global $form_cb_expl, $transferts_reception_OK, $transferts_reception_erreur;
global $site_origine, $nb_per_page, $page;

$page = intval($page);

require_once($class_path."/resa.class.php");
require_once ($class_path.'/ajax_pret.class.php');
require_once($class_path."/mono_display_expl.class.php");
require_once($class_path."/event/events/event_recept_transfert_resa.class.php");

// Titre de la fenêtre
print window_title($database_window_title.$msg['transferts_circ_menu_reception'].$msg[1003].$msg[1001]);

//creation de l'objet transfert
$obj_transfert = new transfert();

switch ($action) {
	
	case "aff_recep":
		$list_transferts_reception_ui = new list_transferts_reception_ui(array('etat_demande' => 2));
		print $list_transferts_reception_ui->get_display_valid_list();
		break;
	
	case "recep":
		//on valide les receptions
		$obj_transfert->enregistre_reception($liste_transfert,$statut_reception,$section_reception,$info);
		$motif=$info[0]["motif"];
		//on affiche l'ecran principal
		$action = "";
		break;
}

if ($action=="") {
	$tmpString = do_cb_expl('',	$msg[661], $msg['transferts_circ_reception_exemplaire'], "./circ.php?categ=trans&sub=".$sub."&site_origine=".$site_origine."&nb_per_page=".$nb_per_page, "recep");

	//on récupere l'id du statut par défaut du site de l'utilisateur
	$rqt = "SELECT transfert_statut_defaut FROM docs_location " .
			"INNER JOIN users ON idlocation=deflt_docs_location " .
			"WHERE userid=".$PMBuserid;
	$res = pmb_mysql_query($rqt);
	$statut_defaut = pmb_mysql_result($res,0);
	
	//on remplit le select avec la liste des statuts
	$tmpString = str_replace("!!liste_statuts!!", do_liste_statut($statut_defaut), $tmpString);
	
	$liste_sel = "<option value=0>" . $msg["transferts_circ_reception_meme_section"] . "</option>" . do_liste_section(0);
	//on remplit le select avec la liste des sections
	$tmpString = str_replace("!!liste_sections!!", $liste_sel, $tmpString);
	
	echo $tmpString;

	if ($form_cb_expl != "") {
		//enregistrement de la reception
		$res_rcp = $obj_transfert->enregistre_reception_cb($form_cb_expl, $statut_reception, $section_reception,$info);
		$motif=$info[0]["motif"];
		if ($res_rcp==false) {
			// reception pas valide
			echo $transferts_reception_erreur;
		} else {
			// reception est faite
			$expl = new mono_display_expl($form_cb_expl,0 ,0);			
			echo str_replace("!!cb_expl!!", $expl->header,$transferts_reception_OK);
			$resa=new reservation(0,0,0,$form_cb_expl);
			if(($empr_resa=$resa->get_empr_info_cb())){			
				// On déclenche un événement sur la réservation à la réception d'un transfert !
				$evt = new event_recept_transfert_resa('transfert', 'recept_resa');
				$evt->set_resa($resa);
				$evth = events_handler::get_instance();
				$evth->send($evt);
				$transfert = new transfert($res_rcp);
				$motif=$transfert->get_motif();		
				echo str_replace("!!empr_link!!", $empr_resa,"<div class='row center'><span class='erreur'>".$msg["transferts_circ_reception_accepte_resa"]."</span><br /><b>".$msg["transferts_circ_reception_resa_par"]." : !!empr_link!!</b></div>");
				if($evt->get_result()){
					echo $evt->get_result();
				}
			}
			if(!empty($expl->expl_data->expl_note)) {
				echo "
				<div class='row center transferts_reception_message'>
					<span class='transferts_reception_message_title'>
						<img src='".get_url_icon('notification_new.png')."' title='".htmlentities($msg["expl_message"], ENT_QUOTES, $charset)."' alt='".htmlentities($msg["expl_message"], ENT_QUOTES, $charset)."' />
					</span>
					<span class='transferts_reception_message_content'>
						<b>".nl2br($expl->expl_data->expl_note)."</b>
					</span>
				</div>";
			}
			if($motif)echo "<div class='row center'><b>".$motif."</b></div>";
		}
	}

	$list_transferts_reception_ui = new list_transferts_reception_ui(array('etat_transfert' => 0, 'etat_demande' => 2));
	print $list_transferts_reception_ui->get_display_list();
}


?>