<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_loans_group_PDF.class.php,v 1.8.2.1 2024/06/10 12:20:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/pdf/reader/loans/lettre_reader_loans_PDF.class.php");

class lettre_reader_loans_group_PDF extends lettre_reader_loans_PDF {
	
    protected static function get_parameter_prefix() {
		return "pdflettreloansgroup";
	}
	
	protected function _init_default_parameters() {
		$this->_init_parameter_value('nb_par_page', 21);
		$this->_init_parameter_value('nb_1ere_page', 19);
		$this->_init_parameter_value('taille_bloc_expl', 12);
		$this->_init_parameter_value('debut_expl_1er_page', 35);
		$this->_init_parameter_value('debut_expl_page', 10);
		$this->_init_parameter_value('limite_after_list', 250);
		$this->_init_parameter_value('list_order', 'empr_nom, empr_prenom');
		$this->_init_parameter_value('list_order_from_empr', 'pret_date');
	}
	
	protected function _init_default_positions() {
		$this->_init_position_values('biblio_info', array(10,10));
		$this->_init_position_values('groupe_adresse', array(130,10,0,0,12));
		$this->_init_position_values('lecteur_adresse', array(130,10,0,0,12));
		$this->_init_position_values('date_edition', array(10,15,0,0,12));
		$this->_init_position_values('lecteur_info', array(10,0,0,0,12));
		$this->_init_position_values('expl_info', array(10));
	}
	
	protected function get_query_list_order() {
	    return "order by ".$this->get_parameter_value('list_order');
	}
	
	protected function get_query_list($id) {
		$id = intval($id);
	    return "select empr_id from empr_groupe, empr, pret where groupe_id='".$id."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id ".$this->get_query_list_order();
	}
	
	protected function get_query_list_base_from_empr() {
	    return "
            SELECT pret_idempr, expl_id, expl_cb, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit
            FROM pret
            join exemplaires ON pret_idexpl=expl_id
            LEFT JOIN notices as notices_m ON notices_m.notice_id = exemplaires.expl_notice and expl_notice <> 0
            LEFT JOIN bulletins ON bulletins.bulletin_id = exemplaires.expl_bulletin
            LEFT JOIN notices AS notices_s ON bulletins.bulletin_notice = notices_s.notice_id
        ";
	}
	
	protected function get_query_list_order_from_empr() {
	    return "order by ".$this->get_parameter_value('list_order_from_empr');
	}
	
	protected function get_query_list_from_empr($id_empr) {
	    return $this->get_query_list_base_from_empr()." where pret_idempr='".$id_empr."' ".$this->get_query_list_order_from_empr();
	}
	
	public function doLettre($id_groupe) {
		global $biblio_name;
		global $msg;
		global $coch_groupe;
		
		$j=0;
		if(!is_array($coch_groupe)) $coch_groupe = array();
		if ($id_groupe) $coch_groupe[0]=$id_groupe;
		while (!empty($coch_groupe[$j])) {
			$id_groupe=$coch_groupe[$j];
		
			//Génération de la lettre dans la langue du lecteur
			$group = new group($id_groupe);
			if($group->id_resp) {
			    $this->set_language(emprunteur::get_lang_empr($group->id_resp));
            }
			
			$this->PDF->addPage();
			
			// paramétrage spécifique à ce document :
			$offsety = 0;
			$this->display_biblio_info(0, 0, 1) ;
			$offsety=(ceil($this->PDF->GetStringWidth($biblio_name)/90)-1)*10; //90=largeur de la cell, 10=hauteur d'une ligne
			$this->display_groupe_adresse($id_groupe, 0, $offsety,true);
			$this->display_date_edition(0,$offsety);
		
			$this->PDF->SetXY (10,15+$offsety);
			$this->PDF->setFont($this->font, 'BI', 14);
			$this->PDF->multiCell(190, 20, $msg["prets_en_cours"], 0, 'L', 0);
			$i=0;
			$this->set_parameter_value('debut_expl_1er_page', $this->get_parameter_value('debut_expl_1er_page')+$offsety);
			$nb_page=0;
			$indice_page=0;
			
			//requete par rapport à un groupe d'emprunteurs
			$rqt1 = $this->get_query_list($id_groupe);
			$req1 = pmb_mysql_query($rqt1);
		
			while ($data1=pmb_mysql_fetch_array($req1)) {
				$id_empr=$data1['empr_id'];
				if ($nb_page==0 && $indice_page==$this->get_parameter_value('nb_1ere_page')) {
					$this->PDF->addPage();
					$nb_page++;
					$indice_page = 0 ;
				} elseif (($nb_page>=1) && (($indice_page % $this->get_parameter_value('nb_par_page'))==0) || ($this->PDF->GetY()>$this->get_parameter_value('limite_after_list'))) {
					$this->PDF->addPage();
					$nb_page++;
					$indice_page = 0 ;
				}
				$pos_y = $this->get_pos_page($nb_page, $indice_page);
				//Ajustement réalisé dans display_lecteur_info
				if(!empty($this->y_lecteur_info)) {
					$pos_y -= $this->y_lecteur_info;
				}
				$this->display_lecteur_info($id_empr,0,$pos_y, 1, 0);
				$indice_page++;
		
				//requete par rapport à un emprunteur
				$rqt = $this->get_query_list_from_empr($id_empr);
				$req = pmb_mysql_query($rqt);
				while ($data = pmb_mysql_fetch_array($req)) {
					if ($nb_page==0 && $indice_page==$this->get_parameter_value('nb_1ere_page')) {
						$this->PDF->addPage();
						$nb_page++;
						$indice_page = 0 ;
					} elseif (($nb_page>=1) && (($indice_page % $this->get_parameter_value('nb_par_page'))==0) || ($this->PDF->GetY()>$this->get_parameter_value('limite_after_list'))) {
						$this->PDF->addPage();
						$nb_page++;
						$indice_page = 0 ;
					}
					$pos_page = $this->get_pos_page($nb_page, $indice_page);
					$this->display_expl_info($data['expl_cb'],0,$pos_page-5, 1, 80);
					$indice_page++;
				}
			}
			$j++;
			
			//Restauration de la langue de l'interface
			if($group->id_resp) {
                $this->restaure_language();
			}
		}
	}
}