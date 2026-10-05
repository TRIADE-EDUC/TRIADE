<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_loans_late_group_PDF.class.php,v 1.11.2.1 2024/06/10 12:20:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/pdf/reader/loans/lettre_reader_loans_late_PDF.class.php");

class lettre_reader_loans_late_group_PDF extends lettre_reader_loans_late_PDF {
	
	protected $lecteurs_ids;
	
	protected function get_parameter_value($name) {
	    $parameter_name = static::get_parameter_prefix().'_'.static::$niveau_relance.$name.'_group';
	    if($this->is_exist_parameter($parameter_name)) {
	    	return $this->get_evaluated_parameter($parameter_name);
	    }
		return parent::get_parameter_value($name);
	}
		
	protected function _init_default_parameters() {
		$this->_init_parameter_value('list_order', 'empr_nom, empr_prenom, pret_date');
	    parent::_init_default_parameters();
	}
	
	protected function _init_default_positions() {
		$this->_init_position_values('date_jour', array($this->w/2,98,0,0,10));
		$this->_init_position_values('biblio_info', array($this->get_parameter_value('marge_page_gauche'),15));
		$this->_init_position_values('groupe_adresse', array($this->get_parameter_value('marge_page_gauche'),45,0,0,12));
		$this->_init_position_values('lecteur_adresse', array($this->get_parameter_value('marge_page_gauche'),45,0,0,12));
		if(!empty($this->get_parameter_value('objet'))) {
			$this->_init_position_values('objet', array($this->get_parameter_value('marge_page_gauche'),125,0,0,10));
			$this->_init_position_values('madame_monsieur', array($this->get_parameter_value('marge_page_gauche'),132,0,0,10));
		} else {
			$this->_init_position_values('madame_monsieur', array($this->get_parameter_value('marge_page_gauche'),125,0,0,10));
		}
	}
	
	protected function get_query_list_order() {
	    return "order by ".$this->get_parameter_value('list_order');
	}
	
	protected function get_query_list($id) {
		$id = intval($id);
	    if ($this->lecteurs_ids) {
	        $lecteur_ids_text = " AND id_empr in (".implode(",",$this->lecteurs_ids).")";
	    } else {
	        $lecteur_ids_text = "";
	    }
	    return "select  empr_id, expl_cb from pret, exemplaires, empr_groupe, empr where groupe_id='".$id."' and pret_retour < curdate() and pret_idexpl=expl_id and empr_id=pret_idempr and empr_id=id_empr $lecteur_ids_text ".$this->get_query_list_order();
	}
	
	protected function display_parameter_multiCell($name) {
		switch ($name) {
			case 'before_list_group':
				$this->display_multiCell($this->w, 8, $this->get_parameter_value($name));
				break;
			case 'after_list':
				$this->display_multiCell($this->w, 8, $this->get_parameter_value($name)."\n\n");
				break;
			case 'fdp':
				$this->display_multiCell($this->w, 8, $this->get_parameter_value($name), 0, 'R');
				break;
			default:
				parent::display_parameter_multiCell($name);
				break;
		}
	}
	
	public function doLettre($id_groupe) {
		global $msg;
		
		//Génération de la lettre dans la langue du lecteur
		$group = new group($id_groupe);
		if($group->id_resp) {
		    $this->set_language(emprunteur::get_lang_empr($group->id_resp));
        }
	
		$this->PDF->addPage();
		$this->display_date_jour();
		$this->display_biblio_info() ;
		$this->display_groupe_adresse($id_groupe, 90);
		
		$this->display_objet();
		$this->display_madame_monsieur($id_groupe);
		
		$this->display_parameter_multiCell('before_list_group');
		
		// compter les totaux pour ce groupe et les retards
		$sqlcount = "SELECT count(pret_idexpl) as combien , IF(pret_retour>=curdate(),0,1) as retard ";
		$sqlcount .= "FROM exemplaires, empr, pret, empr_groupe, groupe ";
		$sqlcount .= "WHERE pret.pret_idempr = empr.id_empr AND pret.pret_idexpl = exemplaires.expl_id AND empr_groupe.empr_id = empr.id_empr AND groupe.id_groupe = empr_groupe.groupe_id and id_groupe=$id_groupe group by retard order by retard ";
		$reqcount = pmb_mysql_query($sqlcount);
		$nbok=0;
		$nbretard=0;
		while ($datacount = pmb_mysql_fetch_object($reqcount)) {
			if ($datacount->retard==0) $nbok=$datacount->combien;
			if ($datacount->retard==1) $nbretard=$datacount->combien;
		}
		$retard_sur_total = str_replace ("!!nb_retards!!",$nbretard*1,$msg['n_retards_sur_total_de']);
		$retard_sur_total = str_replace ("!!nb_total!!",($nbretard+$nbok)*1,$retard_sur_total);
		$this->PDF->multiCell($this->w, 8, $retard_sur_total, 0, 'L', 0);
		
		$rqt = $this->get_query_list($id_groupe);
		$req = pmb_mysql_query($rqt);
		$i=0;
		$nb_page=0;
		$indice_page = 0 ;
		while ($data = pmb_mysql_fetch_array($req)) {
			if ($nb_page==0 && $i==$this->get_parameter_value('nb_1ere_page')) {
				$this->PDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			} elseif ((($nb_page>=1) && ((($i-$this->get_parameter_value('nb_1ere_page')) % $this->get_parameter_value('nb_par_page'))==0)) || ($this->PDF->GetY()>$this->get_parameter_value('limite_after_list'))) {
				$this->PDF->addPage();
				$nb_page++;
				$indice_page = 0 ;
			}
			$pos_page = $this->get_pos_page($nb_page, $indice_page);
			$this->display_expl_retard_empr($data['empr_id'], $data['expl_cb'],$pos_page, 10);
			$i++;
			$indice_page++;
		}
		$this->PDF->setFont($this->font, '', 10);
		if (($pos_page+$this->get_parameter_value('taille_bloc_expl'))>$this->get_parameter_value('limite_after_list')) {
			$this->PDF->addPage();
			$pos_after_list = $this->get_parameter_value('debut_expl_page');
		} else {
			$pos_after_list = $pos_page+$this->get_parameter_value('taille_bloc_expl');
		}
		$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),($pos_after_list));
		$this->display_parameter_multiCell('after_list');
		$this->PDF->setFont($this->font, 'I', 10);
		$this->display_parameter_multiCell('fdp');
		
		$this->display_after_sign();
		
		//Restauration de la langue de l'interface
		if($group->id_resp) {
		  $this->restaure_language();
		}
	}
	
	public function set_lecteurs_ids($lecteurs_ids) {
		$this->lecteurs_ids = $lecteurs_ids;
	}
	
	protected function get_text_madame_monsieur($id) {
		$id = intval($id);
		$query = "select empr_nom, empr_prenom from empr join groupe on id_empr=resp_groupe where id_groupe='".$id."'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result) == 1) {
			$row = pmb_mysql_fetch_object($result);
			$text_madame_monsieur=str_replace("!!empr_name!!", $row->empr_nom,$this->get_parameter_value('madame_monsieur'));
			$text_madame_monsieur=str_replace("!!empr_first_name!!", $row->empr_prenom,$text_madame_monsieur);
		} else {
			$row = pmb_mysql_fetch_object($result);
			$text_madame_monsieur=str_replace("!!empr_name!!", "",$this->get_parameter_value('madame_monsieur'));
			$text_madame_monsieur=str_replace("!!empr_first_name!!", "",$text_madame_monsieur);
		}
		return $text_madame_monsieur;
	}
	
	protected static function get_parameter_name($name) {
		return static::get_parameter_prefix().'_'.$name.'_group';
	}
	
}