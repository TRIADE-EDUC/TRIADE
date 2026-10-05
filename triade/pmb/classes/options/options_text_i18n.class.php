<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_text_i18n.class.php,v 1.1 2021/05/10 07:03:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options_text.class.php");

class options_text_i18n extends options_text {
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['DEFAULT_LANG'][0]['value'] = '';
	}
	
    public function get_content_form() {
        $content_form = parent::get_content_form();
        return $content_form;
    }
    
    protected function get_additional_content_form() {
    	global $msg, $charset, $base_path, $langue_doc;
    	
    	if (!isset($langue_doc) || !count($langue_doc)) {
    		$langue_doc = new marc_list('lang');
    		$langue_doc = $langue_doc->table;
    	}
    	
    	$content_form = "
		<h3>".$msg["procs_options_lang_options"]."</h3>
		<table class='table-no-border' width='100%'>
			<tr>
				<td>".$msg["proc_options_default_value"]."</td>
				<td>
					<input type='hidden' id='DEFAULT_LANG' name='DEFAULT_LANG' value='".htmlentities($this->parameters['DEFAULT_LANG'][0]['value'],ENT_QUOTES,$charset)."' />
					<input type='text' id='DEFAULT_LANG_LABEL' name='DEFAULT_LANG_LABEL' class='saisie-20emr' value='".(!empty($langue_doc[$this->parameters['DEFAULT_LANG'][0]['value']]) ? htmlentities($langue_doc[$this->parameters['DEFAULT_LANG'][0]['value']],ENT_QUOTES,$charset) : '')."' />
					<input type='button' class='bouton' value='...' onclick=\"openPopUp('".$base_path."/select.php?what=lang&caller=formulaire&p1=DEFAULT_LANG&p2=DEFAULT_LANG_LABEL', 'selector')\" />
					<input type='button' class='bouton' value='X' onclick=\"this.form.DEFAULT_LANG.value='';this.form.DEFAULT_LANG_LABEL.value='';\" />
				</td>
			</tr>
		</table>";
    	return $content_form;
    }
    
    public function set_parameters_from_form() {
    	global $DEFAULT_LANG;
        
    	parent::set_parameters_from_form();
		$this->parameters['DEFAULT_LANG'][0]['value']=stripslashes($DEFAULT_LANG);
	}
}
?>