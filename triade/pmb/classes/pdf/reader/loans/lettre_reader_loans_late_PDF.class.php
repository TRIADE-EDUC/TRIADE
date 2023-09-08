<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_loans_late_PDF.class.php,v 1.2 2019-04-26 15:59:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/pdf/reader/loans/lettre_reader_loans_PDF.class.php");

class lettre_reader_loans_late_PDF extends lettre_reader_loans_PDF {
	
	protected static $niveau_relance;
	
	protected function get_parameter_prefix() {
		return "pdflettreretard";
	}
	
	protected function get_parameter_value($name) {
		if(isset(static::$niveau_relance)) {
			$parameter_name = $this->get_parameter_prefix().'_'.static::$niveau_relance.$name;
			$parameter_value = $this->get_evaluated_parameter($parameter_name);
			if(!empty($parameter_value)) {
				return $parameter_value;
			}
		}
		$parameter_name = $this->get_parameter_prefix().'_1'.$name;
		return $this->get_evaluated_parameter($parameter_name);
	}
	
	protected function _init_parameter_value($name, $value) {
		$parameter_name = $this->get_parameter_prefix().'_'.static::$niveau_relance.$name;
		global $$parameter_name;
		if(empty(${$parameter_name})) {
			${$parameter_name} = $value;
		}
	}
	
	protected function _init_default_positions() {
		$this->_init_position_values('date_jour', array($this->w/2,98,0,0,10));
		$this->_init_position_values('biblio_info', array($this->get_parameter_value('marge_page_gauche'),15));
		$this->_init_position_values('lecteur_adresse', array($this->get_parameter_value('marge_page_gauche'),45));
		$this->_init_position_values('madame_monsieur', array($this->get_parameter_value('marge_page_gauche'),105,0,0,10));
	}
	
	public function doLettre($id_empr) {
		global $msg, $pmb_gestion_financiere, $pmb_gestion_amende, $niveau;
		global $pmb_afficher_numero_lecteur_lettres;
		global $mailretard_hide_fine;
	
		//Pour les amendes
		$valeur=0;
		$this->PDF->addPage();
	
		$this->display_date_jour();
		$this->display_biblio_info() ;
		$this->display_lecteur_adresse($id_empr, 90, 0, !$pmb_afficher_numero_lecteur_lettres, true,true);
	
		$this->display_madame_monsieur($id_empr);
		
		$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),$this->PDF->GetY()+4);
		$this->PDF->multiCell($this->w, 5, $this->get_parameter_value('before_list'), 0, 'J', 0);
	
		//Calcul des frais de relance
		$frais_relance = 0;
		if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
			$id_compte=comptes::get_compte_id_from_empr($id_empr,2);
			if ($id_compte) {
				$cpte=new comptes($id_compte);
				$frais_relance=$cpte->summarize_transactions("","",0,$realisee=-1);
				if ($frais_relance<0) $frais_relance=-$frais_relance; else $frais_relance=0;
			}
		}
	
		if($niveau!=3) {
			$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_retour < curdate() and pret_idexpl=expl_id order by pret_date " ;
			$req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
	
			while ($data = pmb_mysql_fetch_array($req)) {
				if (($pos_page=$this->PDF->GetY())>260) {
					$this->PDF->addPage();
					$pos_page=$this->get_parameter_value('debut_expl_page');
				}
				$valeur+=$this->display_expl_retard($data['expl_cb'],$this->get_parameter_value('marge_page_gauche'),$pos_page,$this->w, 10);
			}
			if (($valeur || $frais_relance) && (!$mailretard_hide_fine)) {
				$this->print_amendes($valeur,$frais_relance);
			}
	
			$this->PDF->SetX ($this->get_parameter_value('marge_page_gauche'));
			$this->PDF->setFont($this->font, '', 10);
		} else {
			$requete="select expl_cb from exemplaires, pret where pret_idempr=$id_empr and pret_idexpl=expl_id and niveau_relance=3";
			$res_recouvre=pmb_mysql_query($requete);
			while ($rrc=pmb_mysql_fetch_object($res_recouvre)) {
				$liste_r3[]=$rrc->expl_cb;
			}
			$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_retour < curdate() and pret_idexpl=expl_id order by pret_date " ;
			$req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
			while ($data = pmb_mysql_fetch_object($req)) {
				// Pas répéter les retard si déjà en niveau 3
				if(isset($liste_r3)){
					if(in_array($data->expl_cb,$liste_r3)===false){
						$liste_r[] = $data->expl_cb;
					}
				}
			}
			if($liste_r) {
				// Il y a des retard simple: on affiche d'abord les retards simples
				foreach($liste_r as $cb_expl) {
					if (($pos_page=$this->PDF->GetY())>260) {
						$this->PDF->addPage();
						$pos_page=$this->get_parameter_value('debut_expl_page');
					}
					$valeur+=$this->display_expl_retard($cb_expl,$this->get_parameter_value('marge_page_gauche'),$pos_page,$this->w, 10);
				}
				$this->PDF->setFont($this->font, '', 10);
				$this->PDF->multiCell($this->w, 5, $this->get_parameter_value('before_recouvrement'), 0, 'J', 0);
				// affiche retards niveau 3
				if(isset($liste_r3)){
					foreach($liste_r3 as $cb_expl) {
						if (($pos_page=$this->PDF->GetY())>260) {
							$this->PDF->addPage();
							$pos_page=$this->get_parameter_value('debut_expl_page');
						}
						$valeur+=$this->display_expl_retard($cb_expl,$this->get_parameter_value('marge_page_gauche'),$pos_page,$this->w, 10);
					}
				}
				if (($valeur || $frais_relance) && (!$mailretard_hide_fine)) {
					$this->print_amendes($valeur,$frais_relance);
				}
			} else {
				// il n'y a que des retards niveau 3
				if(isset($liste_r3)){
					foreach($liste_r3 as $cb_expl) {
						if (($pos_page=$this->PDF->GetY())>260) {
							$this->PDF->addPage();
							$pos_page=$this->get_parameter_value('debut_expl_page');
						}
						$valeur+=$this->display_expl_retard($cb_expl,$this->get_parameter_value('marge_page_gauche'),$pos_page,$this->w, 10);
					}
				}
				if (($valeur || $frais_relance) && (!$mailretard_hide_fine)) {
					$this->print_amendes($valeur,$frais_relance);
				}
				$this->PDF->setFont($this->font, '', 10);
				$this->PDF->multiCell($this->w, 5, $this->get_parameter_value('after_recouvrement'), 0, 'J', 0);
			}
			//if (($niveau==3)&&(($pmb_gestion_financiere)&&($pmb_gestion_amende))) {
		}
		$pos_page=$this->PDF->GetY();//Récupère la position dans la page pour prendre en compte l'ajout ou non des informations d'amendes et éviter la superposition d'informations
		if (($pos_page+$this->get_parameter_value('taille_bloc_expl'))>$this->get_parameter_value('limite_after_list')) {
			$this->PDF->addPage();
			$pos_after_list = $this->get_parameter_value('debut_expl_page');
		} else {
			$pos_after_list = $pos_page+$this->get_parameter_value('taille_bloc_expl');
		}
		$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),$pos_after_list);
	
		$this->PDF->setFont($this->font, '', 10);
		$this->PDF->multiCell($this->w, 5, $this->get_parameter_value('after_list'), 0, 'J', 0);
	
		$this->PDF->setFont($this->font, 'I', 10);
		$this->PDF->multiCell($this->w, 5, $this->get_parameter_value('fdp'), 0, 'R', 0);
		return $valeur;
	}
	
	protected function display_expl_retard($cb_doc, $x, $y, $largeur, $retrait) {
		global $msg;
		global $pmb_gestion_financiere, $pmb_gestion_amende;
		global $mailretard_hide_fine;
	
		$valeur=0;
		$dates_resa_sql = " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour " ;
		$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, pret_idempr, expl_id, expl_cb,expl_cote, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date!='', concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql.", " ;
		$requete.= " notices_m.tparent_id, notices_m.tnvol " ;
		$requete.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
		$requete.= " WHERE expl_cb='".addslashes($cb_doc)."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";
	
		$res = pmb_mysql_query($requete);
		$expl = pmb_mysql_fetch_object($res);
	
		// récupération du titre de série
		if ($expl->tparent_id && $expl->m_id) {
			$parent = new serie($expl->tparent_id);
			$tit_serie = $parent->name;
			if($expl->tnvol) {
				$tit_serie .= ', '.$expl->tnvol;
			}
			$expl->tit = $tit_serie.'. '.$expl->tit;
		}
		$libelle=$expl->tdoc_libelle;
		$responsabilites=get_notice_authors($expl->m_id) ;
	
		$as = array_keys ($responsabilites["responsabilites"], "0" ) ;
		$aut1_libelle = array();
		for ($i = 0 ; $i < count($as) ; $i++) {
			$indice = $as[$i] ;
			$auteur_1 = $responsabilites["auteurs"][$indice] ;
			$auteur = new auteur($auteur_1["id"]);
			$aut1_libelle[]= $auteur->get_isbd();
	
		}
		if (count($aut1_libelle)) {
			$auteurs_liste = implode ("; ",$aut1_libelle) ;
			if ($auteurs_liste) $libelle .= ' / '. $auteurs_liste;
	
		}
		$libelle=$expl->tit." (".$libelle.")" ;
	
		$this->PDF->SetXY ($x,$y);
		$this->PDF->setFont($this->font, 'BU', 10);
	
		while( $this->PDF->GetStringWidth($libelle) > 178) {
			$libelle=substr($libelle,0,count($libelle)-2);
		}
	
		$this->PDF->multiCell($largeur, 8, $libelle, 0, 'L', 0);
	
		$this->PDF->SetXY ($x+$retrait,$y+4);
		$this->PDF->setFont($this->font, '', 10);
		$this->PDF->multiCell(($largeur - $retrait), 8, $msg['fpdf_date_pret']." ".$expl->aff_pret_date, 0, 'L', 0);
		if (ceil($this->PDF->GetStringWidth($msg['fpdf_date_pret']." ".$expl->aff_pret_date)) > 52) {
			$w_string =	ceil($this->PDF->GetStringWidth($msg['fpdf_date_pret']." ".$expl->aff_pret_date));
		} else {
			$w_string = 52;
		}
		$this->PDF->SetXY (($x+$retrait+$w_string),$y+4);
		$this->PDF->setFont($this->font, 'B', 10);
		$this->PDF->multiCell(($largeur - $retrait - 52), 8, $msg['fpdf_retour_prevu']." ".$expl->aff_pret_retour, 0, 'L', 0);
	
		$this->PDF->SetXY ($x+$retrait,$y+8);
		$this->PDF->setFont($this->font, 'I', 8);
		$this->PDF->multiCell(($largeur - $retrait), 8, strip_tags($expl->location_libelle.": ".$expl->section_libelle.", ".$expl->expl_cote." (".$expl->expl_cb.")"), 0, 'L', 0);
	
		if (($pmb_gestion_financiere)&&($pmb_gestion_amende)) {
			$amende=new amende($expl->pret_idempr);
			$amd=$amende->get_amende($expl->expl_id);
			if ($amd["valeur"] && !$mailretard_hide_fine) {
				$this->PDF->SetXY (($x+$retrait+120),$y+8);
				$this->PDF->multiCell(($largeur - $retrait - 120), 8, sprintf($msg["relance_lettre_retard_amende"],comptes::format_simple($amd["valeur"])), 0, 'R', 0);
				$valeur=$amd["valeur"];
			}
		}
		return $valeur;
	}
	
	protected function display_expl_retard_empr($id_empr, $cb_doc, $x, $y, $largeur, $retrait) {
		global $msg;
	
		$requete = "SELECT id_empr, empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2  FROM empr WHERE id_empr='$id_empr' LIMIT 1 ";
		$res = pmb_mysql_query($requete);
		$empr = pmb_mysql_fetch_object($res);
		$this->PDF->SetXY ($x,$y);
		$this->PDF->setFont($this->font, '', 12);
		$this->PDF->multiCell(100, 8, $empr->empr_prenom." ".$empr->empr_nom, 0, 'L', 0);
		$y=$y+4;
		$this->display_expl_retard($cb_doc, $x, $y, $largeur, $retrait+10) ;
	}
	
	protected function print_amendes($valeur,$frais_relance) {
		global $msg;
		//Si il y a des amendes
		$this->PDF->SetY ($this->PDF->GetY()+2);
		$this->PDF->setFont($this->font, '', 10);
		$this->PDF->SetWidths(array(70,30));
	
		if ($this->PDF->GetY()>260) {
			$this->PDF->addPage();
			$this->PDF->SetY($this->get_parameter_value('debut_expl_page'));
		}
		if ($valeur) {
			$this->PDF->SetX ($this->get_parameter_value('marge_page_gauche')+40);
			$this->PDF->Row(array($msg["relance_lettre_retard_total_amendes"], comptes::format_simple($valeur) ));
		}
		if ($frais_relance) {
			$this->PDF->SetX ($this->get_parameter_value('marge_page_gauche')+40);
			$this->PDF->Row(array($msg["relance_lettre_retard_frais_relance"], comptes::format_simple($frais_relance) ));
		}
		if (($frais_relance)&&($valeur)) {
			$this->PDF->SetX ($this->get_parameter_value('marge_page_gauche')+40);
			$this->PDF->Row(array($msg["relance_lettre_retard_total_du"], comptes::format_simple($valeur+$frais_relance) ));
		}
		$this->PDF->SetY ($this->PDF->GetY()+4);
	}
	
	public static function set_niveau_relance($niveau_relance) {
		static::$niveau_relance = $niveau_relance;
	}
}