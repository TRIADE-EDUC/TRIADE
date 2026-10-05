<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_htmlcode_view_htmlcode.class.php,v 1.7 2019/06/13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_htmlcode_view_htmlcode extends cms_module_common_view{
	protected $cadre_parent;
	
	public function __construct($id=0){
	    parent::__construct((int) $id);
	}
	
	public function get_form(){
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_view_htmlcode'>".$this->format_text($this->msg['cms_module_htmlcode_view_htmlcode'])."</label>
				</div>
				<div class='colonne-suite'>
					<textarea id='cms_module_common_view_htmlcode' name='cms_module_common_view_htmlcode'>".$this->format_text(stripslashes($this->parameters['htmlcode']))."</textarea>		
				</div>
			</div>".$this->get_ace_editor_script('cms_module_common_view_htmlcode');
		
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_htmlcode;
		
		$this->parameters['htmlcode'] = $cms_module_common_view_htmlcode;			
		return parent::save_form();	
	}	
	
	public function render($datas){
		return stripslashes($this->parameters['htmlcode']) ;
	}
}