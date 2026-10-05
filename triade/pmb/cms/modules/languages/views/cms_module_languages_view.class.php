<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_languages_view.class.php,v 1.2 2022/01/05 09:03:43 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_languages_view extends cms_module_common_view{
	
	public function __construct($id=0){
		parent::__construct($id);
	}
	
	public function get_form(){
	    $form = parent::get_form()
	    ."
        <div class='row'>
    		<div class='colonne5'>
    			<label for='cms_module_lang_abr'>".$this->msg['cms_module_languages_view_text_type']."</label>
            </div>
    		<div class='colonne-suite'>
    			<input type='radio' id='cms_module_lang_abr' name='cms_module_lang_display_type' value='0' ". ($this->parameters['lang_display_type']==0?"checked='checked'":"")."/>
    			<label for='cms_module_lang_abr'>".$this->msg['cms_module_languages_view_lang_abr']."</label>
                <input type='radio' id='cms_module_lang_name' name='cms_module_lang_display_type' value='1'". ($this->parameters['lang_display_type']==1?"checked='checked'":"")."/>
    			<label for='cms_module_lang_name'>".$this->msg['cms_module_languages_view_lang_complet']."</label>
            </div>
        </div>
    
        <div class='row'>
    		<div class='colonne5'>
    			<label for='cms_module_lang_list'>".$this->msg['cms_module_languages_view_display_type']."</label>
    		</div>
    		<div class='colonne-suite'>
    			<input type='radio' id='cms_module_lang_list' name='cms_module_lang_input_type' value='0' ". ($this->parameters['lang_input_type']==0?"checked='checked'":"")."/>
    			<label for='cms_module_lang_list'>".$this->msg['cms_module_languages_view_list']."</label>
    			<input type='radio' id='cms_module_lang_selector' name='cms_module_lang_input_type' value='1'". ($this->parameters['lang_input_type']==1?"checked='checked'":"")."/>
    			<label for='cms_module_lang_selector'>".$this->msg['cms_module_languages_view_selector']."</label>
    		</div>
        </div>";
	    return $form;
	}
	
	public function save_form(){
	    global $cms_module_lang_display_type, $cms_module_lang_input_type;
	    
	    $this->parameters['lang_display_type'] = (int) $cms_module_lang_display_type;
	    $this->parameters['lang_input_type'] = (int) $cms_module_lang_input_type;
	    return parent::save_form();
	}
	
	public function render($datas){
	    return parent::render($datas);
	}
}