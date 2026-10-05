<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: module_pdf.class.php,v 1.2 2021/12/10 09:36:12 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/modules/module.class.php");

class module_pdf extends module{
	
	public function proceed_liste_pret() {
		global $id_empr;
		
		// popup d'impression PDF pour fiche lecteur
		$this->load_class("/pdf/reader/loans/lettre_reader_loans_PDF.class.php");
		header("Content-Type: application/pdf");
		$lettre_reader_loans_PDF = lettre_reader_loans_PDF::get_instance('reader/loans');
		$lettre_reader_loans_PDF->doLettre($id_empr);
		$ourPDF = $lettre_reader_loans_PDF->PDF;
		$ourPDF->OutPut();
	}
	
	public function proceed_lettre_retard() {
		global $id_empr, $empr_print, $printall;
		global $pmb_lecteurs_localises, $empr_location_id, $deflt2docs_location, $pdflettreretard_impression_tri;
		global $mailretard_priorite_email;
		global $pmb_gestion_financiere, $pmb_gestion_amende, $empr_sms_msg_retard;
		global $ourPDF, $niveau, $relance;
		
		// la marge gauche des pages
		$var = "pdflettreretard_".$relance."marge_page_gauche";
		global ${$var};
		$marge_page_gauche = ${$var};
		
		if (!$id_empr) {
			$empr=$empr_print;
			$print_all = isset($printall) ? $printall : 0;
			
			$restrict_localisation="";
			if ($empr) {
				$restrict_localisation = " id_empr in (".implode(",",$empr).") and ";
			} elseif ($pmb_lecteurs_localises) {
				if ($empr_location_id=="") $empr_location_id = $deflt2docs_location ;
				if ($empr_location_id!=0) $restrict_localisation = " empr_location='$empr_location_id' AND ";
			}
			
			// parametre listant les champs de la table empr pour effectuer le tri d'impression des lettres
			if($pdflettreretard_impression_tri) $order_by= " ORDER BY $pdflettreretard_impression_tri";
			else $order_by= "";
			
			$rqt="select id_empr, concat(empr_nom,' ',empr_prenom) as  empr_name, empr_cb, empr_mail, empr_tel1, empr_sms, count(pret_idexpl) as empr_nb, $pdflettreretard_impression_tri from empr, pret, exemplaires where $restrict_localisation pret_retour<curdate() and pret_idempr=id_empr  and pret_idexpl=expl_id group by id_empr $order_by";
			$req=pmb_mysql_query($rqt) or die('Erreur SQL !<br />'.$rqt.'<br />'.pmb_mysql_error());
			while ($r = pmb_mysql_fetch_object($req)) {
				if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
					$amende=new amende($r->id_empr);
					$level=$amende->get_max_level();
					$niveau_min=$level["level_min"];
					$printed=$level["printed"];
					if (($printed==2) || (($mailretard_priorite_email==2) && ($niveau_min<3))) $printed=0;
					pmb_mysql_query("update pret set printed=1 where printed=2 and pret_idempr=".$r->id_empr);
					if (($print_all || !$printed)&&($niveau_min)) {
						$niveau=$niveau_min;
						// 						get_texts($niveau);
						lettre_retard_par_lecteur($r->id_empr, $niveau) ;
						$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
					}
				} else {
					if (!$niveau) $niveau=1;
					// 					get_texts($niveau);
					lettre_retard_par_lecteur($r->id_empr, $niveau) ;
					$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
				}
				if($r->empr_tel1 && $r->empr_sms && $empr_sms_msg_retard){
					send_sms(0, $niveau, $r->empr_tel1, $empr_sms_msg_retard);
				}
			} // fin while
		} else {
			if (!$niveau) $niveau=1;
			// 			get_texts($niveau);
			lettre_retard_par_lecteur($id_empr, $niveau) ;
			$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche);
			if($empr_sms_msg_retard) {
				$rqt="select concat(empr_nom,' ',empr_prenom) as  empr_name, empr_mail, empr_tel1, empr_sms from empr where id_empr='".$id_empr."' and empr_tel1!='' and empr_sms=1";
				$req=pmb_mysql_query($rqt) or die('Erreur SQL !<br />'.$rqt.'<br />'.pmb_mysql_error()); ;
				if ($r = pmb_mysql_fetch_object($req)) {
					if ($r->empr_tel1 && $r->empr_sms) {
						send_sms(0, $niveau, $r->empr_tel1, $empr_sms_msg_retard);
					}
				}
			}
		}
	}
	
	public function proceed_lettre_resa() {
		global $pdflettreresa_marge_page_gauche, $pdflettreresa_marge_page_droite, $pdflettreresa_largeur_page, $pdflettreresa_hauteur_page;
		global $pdflettreresa_format_page, $fpdf;
		global $id_resa, $probleme;
		global $ourPDF;
		global $id_empr_tmp;
		
		// la marge gauche des pages
		$marge_page_gauche = $pdflettreresa_marge_page_gauche;
		
		// la marge droite des pages
		$marge_page_droite = $pdflettreresa_marge_page_droite;
		
		// la largeur des pages
		$largeur_page = $pdflettreresa_largeur_page;
		
		// la hauteur des pages
		$hauteur_page = $pdflettreresa_hauteur_page;
		
		// le format des pages
		$format_page = $pdflettreresa_format_page;
		
		$taille_doc=array($largeur_page,$hauteur_page);
		
		$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
		$ourPDF->Open();
		
		if(!isset($id_empr_tmp)) $id_empr_tmp = 0;
		// chercher id_empr validé
		$rqt = "select resa_idempr from resa where id_resa in ($id_resa) ";
		$res = pmb_mysql_query($rqt) ;
		while ($resa_validee=pmb_mysql_fetch_object($res)){
			if($resa_validee->resa_idempr != $id_empr_tmp){
				lettre_resa_par_lecteur($resa_validee->resa_idempr) ;
				$id_empr_tmp=$resa_validee->resa_idempr;
			}
		}
		$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche,$marge_page_droite);
		
		if (isset($probleme) && $probleme) echo "<script> self.close(); </script>" ;
		else $ourPDF->OutPut();
	}
	
	public function proceed_lettre_resa_planning() {
		global $pdflettreresa_marge_page_gauche, $pdflettreresa_marge_page_droite, $pdflettreresa_largeur_page, $pdflettreresa_hauteur_page;
		global $pdflettreresa_format_page, $fpdf;
		global $id_resa, $probleme;
		global $ourPDF;
		
		// la marge gauche des pages
		$marge_page_gauche = $pdflettreresa_marge_page_gauche;
		
		// la marge droite des pages
		$marge_page_droite = $pdflettreresa_marge_page_droite;
		
		// la largeur des pages
		$largeur_page = $pdflettreresa_largeur_page;
		
		// la hauteur des pages
		$hauteur_page = $pdflettreresa_hauteur_page;
		
		// le format des pages
		$format_page = $pdflettreresa_format_page;
		
		$taille_doc=array($largeur_page,$hauteur_page);
		
		$ourPDF = new $fpdf($format_page, 'mm', $taille_doc);
		$ourPDF->Open();
		
		// chercher id_empr validé
		$q = "select distinct (resa_idempr) from resa_planning where id_resa in (".addslashes($id_resa).") and resa_validee=1 ";
		$r = pmb_mysql_query($q) ;
		while($o=pmb_mysql_fetch_object($r)) {
			lettre_resa_planning_par_lecteur($o->resa_idempr) ;
		}
		$ourPDF->SetMargins($marge_page_gauche,$marge_page_gauche,$marge_page_droite);
		
		if ($probleme) {
			echo "<script type='text/javascript'> self.close(); </script>" ;
		}else {
			$ourPDF->OutPut();
		}
	}
	
	public function proceed_lettre_retard_groupe() {
		global $coch_groupe, $selected_objects, $id_groupe, $relance;
		
		if (isset($id_groupe) && $id_groupe) {
			lettre_retard_par_groupe($id_groupe, array(), $relance) ;
		} else {
			$j=0;
			//Via la nouvelle mécanique de listes
			if(empty($coch_groupe) && !empty($selected_objects)) {
				$coch_groupe = explode(',', $selected_objects);
			}
			while (!empty($coch_groupe[$j])) {
				$id_groupe=$coch_groupe[$j];
				$query = "select distinct groupe_id from pret, empr_groupe where pret_retour < curdate() and empr_id=pret_idempr and groupe_id=$id_groupe" ;
				$result = pmb_mysql_query($query);
				while ($data = pmb_mysql_fetch_object($result)) {
					lettre_retard_par_groupe($data->groupe_id, array(), $relance) ;
				}
				$j++;
			}
		}
	}
	
	public function proceed_liste_pret_groupe() {
		global $coch_groupe, $selected_objects, $id_groupe;
		
		// popup d'impression PDF pour lettres de retard par groupe
		$this->load_class("/pdf/reader/loans/lettre_reader_loans_group_PDF.class.php");
		// reçoit : liste des groupes cochés $coch_groupe
		//Via la nouvelle mécanique de listes
		if(empty($coch_groupe) && !empty($selected_objects)) {
			$coch_groupe = explode(',', $selected_objects);
		}
		
		header("Content-Type: application/pdf");
		$lettre_reader_loans_group_PDF = lettre_reader_loans_group_PDF::get_instance('reader/loans');
		$lettre_reader_loans_group_PDF->doLettre($id_groupe);
		$ourPDF = $lettre_reader_loans_group_PDF->PDF;
		$ourPDF->OutPut();
	}
	
	public function proceed_lettre_relance_adhesion() {
		global $id_empr;
		
		// popup d'impression PDF pour lettre de relance d'abonnement
		$this->load_class("/pdf/reader/lettre_reader_relance_adhesion_PDF.class.php");
		$lettre_reader_relance_adhesion_PDF = lettre_reader_relance_adhesion_PDF::get_instance('reader');
		$lettre_reader_relance_adhesion_PDF->doLettre($id_empr);
		$ourPDF = $lettre_reader_relance_adhesion_PDF->PDF;
		$ourPDF->OutPut();
	}
	
	public function proceed_carte_lecteur() {
		global $idemprcaddie, $id_empr;
		
		$this->load_class("/pdf/reader/lettre_reader_card_PDF.class.php");
		$this->load_class("/caddie/empr_caddie_controller.class.php");
		if(!empty($idemprcaddie)) {
			empr_caddie_controller::proceed_pdf_carte($idemprcaddie);
		} else {
			$lettre_reader_card_PDF = lettre_reader_card_PDF::get_instance('reader');
			$lettre_reader_card_PDF->doLettre($id_empr);
			$ourPDF = $lettre_reader_card_PDF->PDF;
			$ourPDF->OutPut();
		}
	}
}