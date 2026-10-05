<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_relance_adhesion.class.php,v 1.10.2.2 2024/09/24 13:17:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");

class mail_reader_relance_adhesion extends mail_reader {
	
    protected static function get_parameter_prefix() {
        return "mailrelanceadhesion";
    }
	
    protected function _init_default_parameters() {
    	$this->_init_parameter_value('list_order', 'empr_nom, empr_prenom');
        parent::_init_default_parameters();
    }
	
	protected function get_mail_object() {
		return $this->get_parameter_value('objet');
	}
	
	protected function get_mail_content() {
		$mail_content = '';
		if($this->get_parameter_value('madame_monsieur')) {
			$mail_content .= $this->get_parameter_value('madame_monsieur')."\r\n\r\n";
		}
		$mail_content .= $this->get_parameter_value('texte')."\r\n";
		if($this->get_parameter_value('fdp')) {
			$mail_content .= $this->get_parameter_value('fdp')."\r\n\r\n";
		}
		$mail_content .= $this->get_mail_bloc_adresse();
		
		$coords = $this->get_empr_coords();
		$mail_content = str_replace("!!date_fin_adhesion!!", $coords->aff_date_expiration, $mail_content);
		//remplacement nom et prenom
		$mail_content=str_replace("!!empr_name!!", $coords->empr_nom,$mail_content);
		$mail_content=str_replace("!!empr_first_name!!", $coords->empr_prenom,$mail_content);
		
		return $mail_content;
	}
	
	protected function get_mail_headers() {
		global $charset;
		
		return "Content-type: text/plain; charset=".$charset."\n";
	}
	
	public function send_mail() {
		global $action, $selected_objects;
		global $list_ui_objects_type;
		
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
					if(emprunteur::get_mail_empr($selected_id_empr)) {
						$this->mail_to_id = $selected_id_empr;
						$coords = $this->get_empr_coords();
						if($this->get_mail_to_mail()) {
						    $this->set_language($coords->empr_lang);
							$res_envoi=$this->mailpmb();
							$this->restaure_language();
							if ($res_envoi) echo $this->get_display_sent_succeed();
							else echo $this->get_display_sent_failed();
						} else {
							echo $this->get_display_unknown_mail();
						}
					}
				}
			}
		} else {
			$coords = $this->get_empr_coords();
			if($coords->empr_mail) {
			    $this->set_language($coords->empr_lang);
				$res_envoi=$this->mailpmb();
				$this->restaure_language();
				if ($res_envoi) {
				    echo $this->get_display_sent_succeed();
				    return true;
				} else {
				    echo $this->get_display_sent_failed();
				    return false;
				}
			} else {
				echo $this->get_display_unknown_mail();
				return false;
			}
		}
	}
}