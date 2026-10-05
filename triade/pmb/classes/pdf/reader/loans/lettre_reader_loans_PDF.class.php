<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_loans_PDF.class.php,v 1.7.2.2 2024/10/01 15:38:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/pdf/reader/lettre_reader_PDF.class.php");

class lettre_reader_loans_PDF extends lettre_reader_PDF {
	
    protected static function get_parameter_prefix() {
		return "pdflettreloans";
	}
	
	protected function _init_default_parameters() {
		$this->_init_parameter_value('nb_par_page', 21);
		$this->_init_parameter_value('nb_1ere_page', 19);
		$this->_init_parameter_value('taille_bloc_expl', 12);
		$this->_init_parameter_value('debut_expl_1er_page', 35);
		$this->_init_parameter_value('debut_expl_page', 10);
		$this->_init_parameter_value('limite_after_list', 260);
		$this->_init_parameter_value('list_order', 'pret_date');
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
	
	protected function get_query_list_base() {
	    return "
            SELECT pret_idempr, expl_id, expl_cb, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, niveau_relance
            FROM pret
            join exemplaires ON pret_idexpl=expl_id
            LEFT JOIN notices as notices_m ON notices_m.notice_id = exemplaires.expl_notice and expl_notice <> 0
            LEFT JOIN bulletins ON bulletins.bulletin_id = exemplaires.expl_bulletin
            LEFT JOIN notices AS notices_s ON bulletins.bulletin_notice = notices_s.notice_id
        ";
	}
	
	protected function get_query_list_order() {
	    return "order by ".$this->get_parameter_value('list_order');
	}
	
	protected function get_query_list($id) {
		$id = intval($id);
	    return $this->get_query_list_base()." where pret_idempr='".$id."' ".$this->get_query_list_order();
	}
	
	public function doLettre($id_empr) {
		global $biblio_name;
		global $msg;
		
		//Génération de la lettre dans la langue du lecteur
		$this->set_language(emprunteur::get_lang_empr($id_empr));
		//requete par rapport à un emprunteur
		$rqt = $this->get_query_list($id_empr);
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
		//Restauration de la langue de l'interface
		$this->restaure_language();
	}
	
	protected function get_default_parameters() {
		$default_parameters = array();
		$default_parameters['nb_par_page'] = $this->get_parameter_value('nb_par_page');
		$default_parameters['nb_1ere_page'] = $this->get_parameter_value('nb_1ere_page');
		$default_parameters['taille_bloc_expl'] = $this->get_parameter_value('taille_bloc_expl');
		$default_parameters['debut_expl_1er_page'] = $this->get_parameter_value('debut_expl_1er_page');
		$default_parameters['debut_expl_page'] = $this->get_parameter_value('debut_expl_page');
		$default_parameters['limite_after_list'] = $this->get_parameter_value('limite_after_list');
		$default_parameters['list_order'] = $this->get_parameter_value('list_order');
		return $default_parameters;
	}
	
	protected function get_default_positions() {
		$default_positions = array();
		$default_positions['biblio_info'] = $this->get_position_values('biblio_info');
		$default_positions['lecteur_info'] = $this->get_position_values('lecteur_info');
		$default_positions['date_edition'] = $this->get_position_values('date_edition');
		$default_positions['expl_info'] = $this->get_position_values('expl_info');
		return $default_positions;
	}
	
}