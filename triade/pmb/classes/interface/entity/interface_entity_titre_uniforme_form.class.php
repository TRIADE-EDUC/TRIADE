<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: interface_entity_titre_uniforme_form.class.php,v 1.1.12.1 2025/02/27 10:04:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/interface/entity/interface_entity_authority_form.class.php');

class interface_entity_titre_uniforme_form extends interface_entity_authority_form {
	
    /**
     * Permet de savoir si on est en duplication d'oeuvre
     */
    protected $is_duplication = 0;
    
	protected function get_function_name_check_perso() {
		return 'check_perso_tu_form';
	}
	
	protected function get_js_gridform() {
		$mapping_dojo_inclusion_tu = '';
		if(form_mapper::isMapped('tu')){
			$mapping_dojo_inclusion_tu.= '
	     	var formMapper = new FormMapper("tu", "saisie_titre_uniforme");
	     	window["formMapperCallback"] = lang.hitch(formMapper, formMapper.selectorCallback, "tu");';
		}
		
		return "
		<script type='text/javascript'>
			require(['dojo/ready', 'apps/pmb/gridform/FormEdit','dojo/dom-attr','dojo/dom','apps/form_mapper/FormMapper', 'dojo/_base/lang'], function(ready, FormEdit, domAttr, dom, FormMapper, lang){
			     ready(function(){
			     	domAttr.set(dom.byId('oeuvre_type'),'backbone','yes');
			     	domAttr.set(dom.byId('oeuvre_nature'),'backbone','yes');
			     	new FormEdit();
			     	".$mapping_dojo_inclusion_tu."
			     });
			});
		</script>";
	}
	
	protected function get_js_script_error_label() {
		global $msg;
		return $msg['tu_form_submit_error'];
	}
	
	protected function get_js_script() {
		global $base_path;
		$js_script = parent::get_js_script();
		$js_script .= "<script type='text/javascript' src='".$base_path."/javascript/oeuvre_link_drop.js'></script>";
		return $js_script;
	}
	
	protected function get_statuses_selector() {
		return authorities_statuts::get_form_for(AUT_TABLE_TITRES_UNIFORMES, $this->num_statut);
	}
	
	protected function get_display_audit_action() {
	    if (!$this->is_duplication) {
		  return audit::get_dialog_button($this->object_id, AUDIT_TITRE_UNIFORME);
	    }
	    return '';
	}
	
	protected function get_display_hidden_fields() {
	    $display = parent::get_display_hidden_fields();
	    $display .= "<input type='hidden' name='is_duplication' value='".$this->is_duplication."' />";
	    return $display;
	}
	
	public function set_is_duplication($is_duplication) {
	    $this->is_duplication = $is_duplication;
	    return $this;
	}
}