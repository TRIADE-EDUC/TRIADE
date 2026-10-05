<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_admin_planificateur_form.class.php,v 1.2 2024/03/12 13:14:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class interface_admin_planificateur_form extends interface_admin_form {
	
	protected $id_type;
	
    protected function get_submit_action() {
        switch ($this->table_name) {
            case 'taches_type':
                return $this->get_url_base()."&action=type_update".(!empty($this->object_id) ? "&id=".$this->object_id : "");
            default:
                return $this->get_url_base()."&action=edit".(!empty($this->object_id) ? "&id=".$this->object_id : "");
        }
    }
    
	protected function get_action_duplicate_label() {
		global $msg;
		return $msg['tache_duplicate_bouton'];
	}
	
	protected function get_delete_action() {
	    return $this->get_url_base()."&action=delete&id=".$this->object_id;
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['planificateur_alert_name'];
	}
	
	protected function get_js_script() {
	    global $msg;
	    
	    if(isset($this->field_focus) && $this->field_focus) {
	        return "
			<script type='text/javascript'>
				if(typeof test_form == 'undefined') {
					function test_form(form) {
						document.getElementById('subaction').value='save';
                		var heure = document.getElementById('task_perio_heure').value;
                		var min = document.getElementById('task_perio_min').value;
                		var reg_horaire_fixe = new RegExp('^[0-9]{1,2}$');
                		var reg_horaire_intervalle = new RegExp('^[0-9]{1,2}[-]{1}[0-9]{1,2}$');
                		var reg_horaire_intervalle_repeat = new RegExp('^[0-9]{1,2}[-]{1}[0-9]{1,2}[{]{1}[0-9]{1,2}[}]{1}');
                		var reg_number = new RegExp('^[0-9]{1,6}$');
		
                		if (document.getElementById('task_name').value == '') {
                			alert(\"$msg[planificateur_alert_name]\");
                			return false;
                		}
                		if (form.form_users.value == '') {
                			alert(\"$msg[planificateur_alert_user]\");
                			return false;
                		}
                		if (document.getElementById('path')) {
                			if (document.getElementById('path').value == '') {
                				alert(\"$msg[planificateur_alert_upload]\");
                				return false;
                			}
                		}
                        var checkboxes_quotidien = Array.prototype.slice.call(document.querySelectorAll('input[type=\"checkbox\"][name=\"chkbx_task_quotidien[]\"]:checked:enabled'));
                        var checkboxes_hebdo = Array.prototype.slice.call(document.querySelectorAll('input[type=\"checkbox\"][name=\"chkbx_task_hebdo[]\"]:checked:enabled'));
                        var checkboxes_mensuel = Array.prototype.slice.call(document.querySelectorAll('input[type=\"checkbox\"][name=\"chkbx_task_mensuel[]\"]:checked:enabled'));
                        if((checkboxes_quotidien && !checkboxes_quotidien.length) 
                            || (checkboxes_hebdo && !checkboxes_hebdo.length) 
                            || (checkboxes_mensuel && !checkboxes_mensuel.length)
                        ) {
                            alert(\"$msg[planificateur_alert_perio]\");
                            return false;
                        }
                		if ((heure != '') && (heure != '*')) {
                			if (reg_horaire_fixe.test(heure)) {
                				if ((heure < 0) || (heure > 23)) {
                					alert(\"$msg[planificateur_alert_heure]\");
                					return false;
                				}
                			} else if (reg_horaire_intervalle.test(heure)) {
                				var heure_exp = heure.split('-'); 
                				if ((heure_exp[0] < 0) || (heure_exp[0] > 23) || (heure_exp[1] < 0) || (heure_exp[1] > 23)) {
                					alert(\"$msg[planificateur_alert_heure]\");
                					return false;
                				}
                			} else if (reg_horaire_intervalle_repeat.test(heure)) {
                				var reg_h=new RegExp('[-{}]+');
                				var heure_exp = heure.split(reg_h);
                				if ((heure_exp[0] < 0) || (heure_exp[0] > 23) || (heure_exp[1] < 0) || (heure_exp[1] > 23)) {
                					alert(\"$msg[planificateur_alert_heure]\");
                					return false;
                				}
                			} else {
                				alert(\"$msg[planificateur_alert_heure]\");
                				return false;
                			}
                		}
                		if ((min != '') && (min != '*')) {
                			if (reg_horaire_fixe.test(min)) {
                				if ((min < 0) || (min > 59)) {
                					alert(\"$msg[planificateur_alert_min]\");
                					return false;
                				}
                			} else if (reg_horaire_intervalle.test(min)) {
                				var min_exp = min.split('-'); 
                				if ((min_exp[0] < 0) || (min_exp[0] > 59) || (min_exp[1] < 0) || (min_exp[1] > 59)) {
                					alert(\"$msg[planificateur_alert_min]\");
                					return false;
                				}
                			} else if (reg_horaire_intervalle_repeat.test(min)) {
                				var reg_m=new RegExp('[-{}]+');
                				var min_exp = min.split(reg_m);
                				if ((min_exp[0] < 0) || (min_exp[0] > 59) || (min_exp[1] < 0) || (min_exp[1] > 59)) {
                					alert(\"$msg[planificateur_alert_min]\");
                					return false;
                				}
                			} else {
                				alert(\"$msg[planificateur_alert_min]\");
                				return false;
                			}
                		}
                		if (document.getElementById('timeout').value != '') {
                			if (reg_number.test(document.getElementById('timeout').value) == false) {
                				alert(\"$msg[planificateur_alert_timeout]\");
                				return false;
                			}
                		}
                		if (document.getElementById('histo_day')) {
                			if (document.getElementById('histo_day').value != '') {
                				if (reg_number.test(document.getElementById('histo_day').value) == false) {
                					alert(\"$msg[planificateur_alert_histoday]\");
                					return false;
                				}
                			}
                		}
                		if (document.getElementById('histo_number')) {
                			if (document.getElementById('histo_number').value != '') {
                				if (reg_number.test(document.getElementById('histo_number').value) == false) {
                					alert(\"$msg[planificateur_alert_histonumber]\");
                					return false;
                				}
                			}
                		}
					}
				}
				</script>
			";
	    }
	    return "";
	}
	
    public function set_id_type($id_type) {
        $this->id_type = $id_type;
		return $this;
	}
	
	public function get_url_base() {
	    return parent::get_url_base().(!empty($this->id_type) ? "&type_id=".$this->id_type : "");
	}
}