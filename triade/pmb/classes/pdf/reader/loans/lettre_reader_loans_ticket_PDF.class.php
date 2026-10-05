<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_loans_ticket_PDF.class.php,v 1.3.10.1 2024/06/06 13:51:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/pdf/reader/lettre_reader_PDF.class.php");

class lettre_reader_loans_ticket_PDF extends lettre_reader_PDF {
	
    protected static function get_parameter_prefix() {
		return "pdflettreticket";
	}
	
	protected function _init_default_parameters() {
		$this->_init_parameter_value('nb_par_page', 14);
		$this->_init_parameter_value('nb_1ere_page', 7);
		$this->_init_parameter_value('taille_bloc_expl', 18);
	}
	
	protected function _init_default_positions() {
		$this->_init_position_values('biblio_info', array(10,10));
		$this->_init_position_values('lecteur_info', array(90,10,0,0,12));
		$this->_init_position_values('date_edition', array(10,70,0,0,12));
		$this->_init_position_values('not_bull_info_resa', array(20,0));
		$this->_init_position_values('expl_info', array(20));
	}
	
	public function doLettre($id_empr) {
		global $cb_doc;
		global $msg;
		
		//Génération de la lettre dans la langue du lecteur
		$this->set_language(emprunteur::get_lang_empr($id_empr));
		
		$this->PDF->addPage();
		
		$offsety = 40;
		
		$this->display_biblio_info() ;
		$this->display_lecteur_info($id_empr, 0, $offsety);
		$this->display_date_edition(0,$offsety);
		
		if (!isset($cb_doc) || $cb_doc == "") {
			$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;
			$req = pmb_mysql_query($rqt); 
			
			$this->PDF->SetXY (10,80+$offsety);
			$this->PDF->setFont($this->font, 'BI', 20);
			$this->PDF->multiCell(190, 20, $msg["prets_en_cours"], 0, 'L', 0);
			$i=0;
			$nb_page=0;
			$nb_par_page = $this->get_parameter_value('nb_par_page');
			$nb_1ere_page = $this->get_parameter_value('nb_1ere_page');
			$taille_bloc_expl = $this->get_parameter_value('taille_bloc_expl');
			while ($data = pmb_mysql_fetch_array($req)) {
				if ($nb_page==0 && $i<$nb_1ere_page) {
					$pos_page = 100+$offsety+$taille_bloc_expl*$i;
				}
				if (($nb_page==0 && $i==$nb_1ere_page) || ((($i-$nb_1ere_page) % $nb_par_page)==0)) {
					$this->PDF->addPage();
					$nb_page++;
				}
				if ($nb_page>=1) {
					$pos_page = 10+($taille_bloc_expl*($i-$nb_1ere_page-($nb_page-1)*$nb_par_page));
				}
				$this->display_expl_info($data['expl_cb'],0,$pos_page,0,70);
				$i++;
			}
		
			// Impression des réservations en cours
			$rqt = "select resa_idnotice, resa_idbulletin from resa where resa_idempr='".$id_empr."' " ;
			$req = pmb_mysql_query($rqt); 
			if (pmb_mysql_num_rows($req) > 0) {
				if ($nb_page==0 && $i<$nb_1ere_page) {
					$pos_page = 100+$offsety+$taille_bloc_expl*$i;
				}
				if (($nb_page==0 && $i==$nb_1ere_page) || ((($i-$nb_1ere_page) % $nb_par_page)==0)) {
					$this->PDF->addPage();
					$nb_page++;
				}
				if ($nb_page>=1) {
					$pos_page = 10+($taille_bloc_expl*($i-$nb_1ere_page-($nb_page-1)*$nb_par_page));
				}
				$i++;
				$this->PDF->SetXY (10,$pos_page+7);
				$this->PDF->setFont($this->font, 'BI', 20);
				$this->PDF->multiCell(190, 20, $msg["documents_reserves"], 0, 'L', 0);
				
				while ($data = pmb_mysql_fetch_array($req)) {
					if ($nb_page==0 && $i<$nb_1ere_page) {
						$pos_page = 100+$offsety+$taille_bloc_expl*$i;
					}
					if (($nb_page==0 && $i==$nb_1ere_page) || ((($i-$nb_1ere_page) % $nb_par_page)==0)) {
						$this->PDF->addPage();
						$nb_page++;
					}
					if ($nb_page>=1) {
						$pos_page = 10+($taille_bloc_expl*($i-$nb_1ere_page-($nb_page-1)*$nb_par_page));
					}
					$this->display_not_bull_info_resa($id_empr, $data['resa_idnotice'],$data['resa_idbulletin'],0,$pos_page, 65);
					$i++;
				}
			} // fin if résas
		
		} else {
			$this->PDF->SetXY (10,80+$offsety);
			$this->PDF->setFont($this->font, 'BI', 20);
			$this->PDF->multiCell(190, 20, $msg["ticket_de_pret"], 0, 'L', 0);
		
			$this->display_expl_info($cb_doc,0,100+$offsety,0,65);
		}
		//Restauration de la langue de l'interface
		$this->restaure_language();
	}
}