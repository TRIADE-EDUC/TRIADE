<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_loans_late_group_PDF.class.php,v 1.2 2019-04-26 15:59:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/pdf/reader/loans/lettre_reader_loans_late_PDF.class.php");

class lettre_reader_loans_late_group_PDF extends lettre_reader_loans_late_PDF {
	
	protected $lecteurs_ids;
	
	protected function get_parameter_value($name) {
		$parameter_name = $this->get_parameter_prefix().'_'.static::$niveau_relance.$name.'_group';
		$parameter_value = $this->get_evaluated_parameter($parameter_name);
		if($parameter_value) {
			return $parameter_value;
		} else {
			return parent::get_parameter_value($name);
		}
	}
		
	protected function _init_default_positions() {
		$this->_init_position_values('date_jour', array($this->w/2,98,0,0,10));
		$this->_init_position_values('biblio_info', array($this->get_parameter_value('marge_page_gauche'),15));
		$this->_init_position_values('groupe_adresse', array($this->get_parameter_value('marge_page_gauche'),45,0,0,12));
		$this->_init_position_values('madame_monsieur', array($this->get_parameter_value('marge_page_gauche'),125,0,0,10));
	}
	
	public function doLettre($id_groupe) {
		global $msg;
		
		$this->PDF->addPage();
		$this->display_date_jour();
		$this->display_biblio_info() ;
		$this->display_groupe_adresse($id_groupe, 90);
		
		$this->display_madame_monsieur($id_groupe);
		
		$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('before_list_group'), 0, 'J', 0);
		
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
		
		if ($this->lecteurs_ids) {
			$lecteur_ids_text = " AND id_empr in (".implode(",",$this->lecteurs_ids).")";
		} else {
			$lecteur_ids_text = "";
		}
		$rqt = "select  empr_id, expl_cb from pret, exemplaires, empr_groupe, empr where groupe_id='".$id_groupe."' and pret_retour < curdate() and pret_idexpl=expl_id and empr_id=pret_idempr and empr_id=id_empr $lecteur_ids_text order by empr_nom, empr_prenom, pret_date " ;
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
			$this->display_expl_retard_empr($data['empr_id'], $data['expl_cb'], $this->get_parameter_value('marge_page_gauche'),$pos_page,$this->w, 10);
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
		$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('after_list')."\n\n", 0, 'J', 0);
		$this->PDF->setFont($this->font, 'I', 10);
		$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('fdp'), 0, 'R', 0);
	}
	
	public function set_lecteurs_ids($lecteurs_ids) {
		$this->lecteurs_ids = $lecteurs_ids;
	}
	
	protected function get_text_madame_monsieur($id_groupe) {
		$query = "select empr_nom, empr_prenom from empr join groupe on id_empr=resp_groupe where id_groupe='".$id_groupe."'";
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
	
}