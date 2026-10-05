<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_edition_ui.class.php,v 1.12.4.1 2025/02/20 09:19:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/list/loans/list_loans_edition_ui.tpl.php");
require_once($class_path."/emprunteur.class.php");

class list_loans_edition_ui extends list_loans_ui {
	
	protected function get_title() {
		return "<div class='row'><p class='message'>".$this->get_display_late()."</p></div>";
	}
	
	protected function get_form_title() {
		global $msg;
		global $sub;
		
		$form_title = '';
		switch($sub) {
			case "retard" :
				$form_title .= $msg[1112];
				break;
			case "retard_par_date" :
				$form_title .= $msg['edit_expl_retard_par_date'];
				break;
			case 'short_loans' :
				$form_title .= $msg['current_short_loans'];
				break;
			case 'unreturned_short_loans' :
				$form_title .= $msg['unreturned_short_loans'];
				break;
			case 'overdue_short_loans' :
				$form_title .= $msg['overdue_short_loans'];
				break;
			default :
			case "encours" :
				$form_title .= $msg[1111];
				break;
		}
		return $form_title;
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		global $base_path;
		global $pmb_gestion_amende, $pmb_gestion_financiere;
		global $sub;
		
		parent::init_default_selection_actions();
		if($pmb_gestion_amende==0 || $pmb_gestion_financiere==0) {
			if($sub == 'pargroupe') {
				$relance_link = array(
						'openPopUp' => $base_path."/pdf.php?pdfdoc=lettre_retard_groupe",
						'openPopUpTitle' => 'lettre'
				);
				$this->add_selection_action('relance_groupe', $msg['lettres_relance_groupe'], 'print.gif', $relance_link);
			}
			if($sub == 'retard' || $sub == 'retard_par_date') {
				$relance_link = array(
						'href' => static::get_controller_url_base()."&action=print",
				        'confirm' => $msg['loans_relances_print_mail_confirm']
				);
				$this->add_selection_action('relance', $msg['loans_relances_print_mail'], 'print.gif', $relance_link);
			}
		}
	}
	
	protected function init_default_columns() {
		global $pmb_gestion_amende, $pmb_gestion_financiere;
		global $sub;
	
		if ($pmb_gestion_amende==0 || $pmb_gestion_financiere==0) {
			if($sub == 'retard' || $sub == 'retard_par_date' || $sub == 'pargroupe') {
				$this->add_column_selection();
			}
		}
		$this->add_column('cb_expl');
		$this->add_column('cote');
		$this->add_column('typdoc');
		$this->add_column('record');
		$this->add_column('author');
		$this->add_column('empr');
		$this->add_column('pret_date');
		$this->add_column('pret_retour');
		$this->add_column('late_letter', '369', '', false);
	}
	
	protected function get_sub_title() {
		global $sub, $msg;
		switch($sub) {
			case 'retard':
				return $msg['1112'];
				break;
			case 'retard_par_date':
				return $msg['edit_expl_retard_par_date'];
				break;
			case 'short_loans':
				return $msg['current_short_loans'];
				break;
			case 'unreturned_short_loans':
				return $msg['unreturned_short_loans'];
				break;
			case 'overdue_short_loans':
				return $msg['overdue_short_loans'];
				break;
			case 'encours':
			default :
				return $msg['1111'];
				break;
		}
	}
	
	protected function get_display_spreadsheet_title() {
		global $sub, $msg;
		switch ($sub) {
			case 'short_loans' :
			case 'unreturned_short_loans' :
			case 'overdue_short_loans' :
				$this->spreadsheet->write_string(0,0,$this->get_sub_title());
				break;
			default :
				$this->spreadsheet->write_string(0,0,$msg[1110]." : ".$this->get_sub_title());
				break;
		}
	}
	
	protected function get_html_title() {
		global $sub, $msg;
		switch ($sub) {
			case 'short_loans' :
			case 'unreturned_short_loans' :
			case 'overdue_short_loans' :
				return "<h1>".$this->get_sub_title()."</h1>";
			default :
				return "<h1>".$msg[1110]." : ".$this->get_sub_title()."</h1>";
				break;
		}
	}
	
	protected function get_search_buttons_extension() {
	    global $msg;
	    
	    if(count($this->objects)) {
	        $action = array(
	            'name' => 'print_all_relances',
	            'link' => array(
	                'href' => static::get_controller_url_base()."&action=print_all",
	                'confirm' => $msg['loans_all_relances_print_mail_confirm']
	            )
	        );
	        return $this->get_interface_button($msg['lettres_relance'], [], ['id' => $this->objects_type."_global_action_print_all_relances_link"])
            .$this->add_event_on_global_action($action);
	    }
	    return "";
	}
	
	protected function asked_relance($object) {
	    global $action, $loans_edition_ui_selected_objects;
	    
	    if($action == "print_all") {
	        return true;
	    }
	    if(!empty($loans_edition_ui_selected_objects)) {
	        if($action == "print" && in_array($object->id_empr."_".$object->id_expl, $loans_edition_ui_selected_objects)) {
	            return true;
	        }
	    }
	    return false;
	}
	
	public function print_relances() {
		global $mailretard_priorite_email;
		global $relance;
		global $pmb_lecteurs_localises;
		
		$not_all_mail = array();
		$mail_sended_id_empr = array();
		foreach ($this->objects as $object) {
            if($this->asked_relance($object)) {
    			$mail_sended = 0;
    			if ((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&(emprunteur::get_mail_empr($object->id_empr))) {
    				if ((!count($mail_sended_id_empr)) || (!in_array($object->id_empr,$mail_sended_id_empr))) {
    					if (!$relance) $relance = 1;
    					
    					mail_reader_loans_late_relance::set_niveau_relance($relance);
    					mail_reader_loans_late_relance::set_hide_fines(true);
    					$mail_reader_loans_late_relance = mail_reader_loans_late_relance::get_instance();
    					$mail_reader_loans_late_relance->set_mail_to_id($object->id_empr);
    					$mail_sended = $mail_reader_loans_late_relance->send_mail();
    				} else {
    					$mail_sended = 1;
    				}
    			}
    			if (!$mail_sended) {
    				$not_all_mail[] = $object->id_empr;
    			} else {
    				$mail_sended_id_empr[] = $object->id_empr;
    			}
	        }
		}
		if (count($not_all_mail) > 0) {
			$restrict_localisation ="";
			if ($pmb_lecteurs_localises) {
				if ($this->filters['empr_location_id']!="") $restrict_localisation .= "&empr_location_id=".$this->filters['empr_location_id'];
				if ($this->filters['docs_location_id']!="") $restrict_localisation .= "&docs_location_id=".$this->filters['docs_location_id'];
			}
			print "<form name='print_empr_ids' action='pdf.php?pdfdoc=lettre_retard$restrict_localisation' target='lettre' method='post'>";
			for ($i=0; $i<count($not_all_mail); $i++) {
				print "<input type='hidden' name='empr_print[]' value='".$not_all_mail[$i]."'/>";
			}
			print "	<script>openPopUp('','lettre');
				document.print_empr_ids.submit();
				</script>
			</form>";
		}	
	}
}