<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lettre_reader_relance_adhesion_PDF.class.php,v 1.7.2.1 2024/06/06 13:51:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/pdf/reader/lettre_reader_PDF.class.php");
require_once($class_path."/emprunteur.class.php");

class lettre_reader_relance_adhesion_PDF extends lettre_reader_PDF {
	
    protected static function get_parameter_prefix() {
        return "pdflettreadhesion";
    }
		
    protected function _init_default_parameters() {
        $this->_init_parameter_value('list_order', 'empr_nom, empr_prenom');
    }
    
	protected function _init_default_positions() {
	    $this->_init_position_values('date_jour', array($this->w/2,98,0,0,10));
		$this->_init_position_values('biblio_info', array($this->get_parameter_value('marge_page_gauche'),10));
		$this->_init_position_values('lecteur_adresse', array($this->get_parameter_value('marge_page_gauche'),45));
		$this->_init_position_values('madame_monsieur', array($this->get_parameter_value('marge_page_gauche'),125,0,0,12));
	}
	
	protected function display_texte($id_empr) {
		$empr_temp = new emprunteur($id_empr, '', FALSE, 0);
		$texte_relance = $this->get_parameter_value('texte');
		$texte_relance = str_replace("!!date_fin_adhesion!!", $empr_temp->aff_date_expiration, $texte_relance);
		$this->PDF->multiCell($this->w, 8, $texte_relance, 0, 'J', 0);
	}
			
	public function doLettre($id_empr) {
		global $action, $selected_objects;
		global $empr_relance_adhesion, $pmb_afficher_numero_lecteur_lettres;
		global $list_ui_objects_type;
		
		//Génération de la lettre dans la langue du lecteur
		$this->set_language(emprunteur::get_lang_empr($id_empr));
		if ($action=="print_all" || $action=="print") {
		    if($action=="print_all" && !empty($list_ui_objects_type)) {
		        $objects = array();
		        $selected_objects = array();
                switch ($list_ui_objects_type) {
                    case 'readers_edition_ui':
	                    $list_ui_objects_type_json_filters = $list_ui_objects_type."_json_filters";
	                    global ${$list_ui_objects_type_json_filters};
	                    $filters = encoding_normalize::json_decode(stripslashes(${$list_ui_objects_type_json_filters}), 1);
	                    $pager = array('all_on_page' => true);
	                    $list_readers_edition_ui = new list_readers_edition_ui($filters, $pager);
	                    $objects = $list_readers_edition_ui->get_objects();
	                    break;
	            }
	            if(!empty($objects)) {
	                foreach ($objects as $object) {
	                    $selected_objects[] = $object->get_id();
	                }
	            }
		    }
			if(!empty($selected_objects)) {
				if(!is_array($selected_objects)) {
					$selected_objects = explode(',', $selected_objects);
				}
				foreach ($selected_objects as $selected_id_empr) {
					if(empty($empr_relance_adhesion) || ($empr_relance_adhesion==1 && !emprunteur::get_mail_empr($selected_id_empr))) {
						$this->PDF->addPage();
				
						$this->display_date_jour();
						$this->display_biblio_info() ;
						$this->display_lecteur_adresse($selected_id_empr, 90, 0, !$pmb_afficher_numero_lecteur_lettres);
				
						$this->display_madame_monsieur($selected_id_empr);
				
						$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),135);
						// mettre ici le texte
						$this->display_texte($selected_id_empr);
						
						$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('fdp'), 0, 'R', 0);
						
						//Ré-initialisation des positions pour les autres lettres
						$this->_init_default_positions();
					}
				}
			}
		} else {
			$this->PDF->addPage();
			$this->PDF->SetMargins($this->get_parameter_value('marge_page_gauche'),$this->get_parameter_value('marge_page_gauche'));
		
			$this->display_date_jour();
			$this->display_biblio_info() ;
			$this->display_lecteur_adresse($id_empr, 90, 0, !$pmb_afficher_numero_lecteur_lettres);
		
			$this->display_madame_monsieur($id_empr);
		
			$this->PDF->SetXY ($this->get_parameter_value('marge_page_gauche'),135);
			// mettre ici le texte
			$this->display_texte($id_empr);
		
			$this->PDF->multiCell($this->w, 8, $this->get_parameter_value('fdp'), 0, 'R', 0);
		}
		//Restauration de la langue de l'interface
		$this->restaure_language();
	}
	
}