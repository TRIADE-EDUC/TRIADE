<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_loans_PDF.class.php,v 1.3 2019-05-24 15:47:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/pdf/reader/lettre_reader_PDF.class.php");

class lettre_reader_loans_PDF extends lettre_reader_PDF {
	
	protected function get_parameter_prefix() {
		return "pdflettreloans";
	}
	
	protected function _init_default_parameters() {
		$this->_init_parameter_value('nb_par_page', 21);
		$this->_init_parameter_value('nb_1ere_page', 19);
		$this->_init_parameter_value('taille_bloc_expl', 12);
		$this->_init_parameter_value('debut_expl_1er_page', 35);
		$this->_init_parameter_value('debut_expl_page', 10);
		$this->_init_parameter_value('limite_after_list', 260);
	}
	
	protected function _init_default_positions() {
		$this->_init_position_values('biblio_info', array(10,10));
		$this->_init_position_values('lecteur_info', array(90,10,0,0,12));
		$this->_init_position_values('date_edition', array(10,15,0,0,12));
		$this->_init_position_values('expl_info', array(10));
	}
	
	protected function get_pos_page($nb_page, $indice_page) {
		if ($nb_page==0) {
			return $this->get_parameter_value('debut_expl_1er_page')+$this->get_parameter_value('taille_bloc_expl')*$indice_page;
		} else {
			return $this->get_parameter_value('debut_expl_page')+$this->get_parameter_value('taille_bloc_expl')*$indice_page;
		}
	}
	
	public function doLettre($id_empr) {
		global $biblio_name;
		global $msg;
		
		//requete par rapport à un emprunteur
		$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;
		$req = pmb_mysql_query($rqt);
		$count = pmb_mysql_num_rows($req);
		
		$this->PDF->addPage();
		
		// paramétrage spécifique à ce document :
		$offsety = 0;
		
		$this->display_biblio_info(0, 0, 1) ;
		$offsety=(ceil($this->PDF->GetStringWidth($biblio_name)/90)-1)*10; //90=largeur de la cell, 10=hauteur d'une ligne
		$this->display_lecteur_info($id_empr, 0, $offsety, 1,1);
		$this->display_date_edition(0,$offsety);
		
		$this->PDF->SetXY (10,22+$offsety);
		$this->PDF->setFont($this->font, 'BI', 14);
		$this->PDF->multiCell(190, 20, $msg["prets_en_cours"]." (".($count).")", 0, 'L', 0);
		
		$this->set_parameter_value('debut_expl_1er_page', $this->get_parameter_value('debut_expl_1er_page')+$offsety);
		$indice_page = 0 ;
		$nb_page=0;
		while ($data = pmb_mysql_fetch_array($req)) {
			if ($nb_page==0 && $indice_page==$this->get_parameter_value('nb_1ere_page')) {
				$this->PDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			} elseif ((($nb_page>=1) && (($indice_page % $this->get_parameter_value('nb_par_page'))==0)) || ($this->PDF->GetY()>$this->get_parameter_value('limite_after_list'))) {
				$this->PDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			}
			$pos_page = $this->get_pos_page($nb_page, $indice_page);
			$this->display_expl_info($data['expl_cb'],0,$pos_page, 1, 80);
			$indice_page++;
		}
		pmb_mysql_free_result($req);
	}
	
	
}