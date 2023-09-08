<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_charte.class.php,v 1.4 2019-05-28 08:29:38 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_charte extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = '
<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
    <div class="uk-modal-header">
		<h2 class="uk-modal-title">{{title}}</h2>
    </div>
    {% if resume %}<div class="uk-block uk-alert">{{resume}}</div>{% endif %}
	<div class="uk-overflow-container">{{content}}</div>
	<div class="uk-modal-footer">
		<div class="uk-text-right">
        	<button class="uk-button uk-modal-close" type="button" disabled>{{msg.cms_module_charte_accept_button}}</button>
		</div>
	</div>
</div>';
	}
	
	public function get_form() {
		if(!isset($this->parameters['css']) || !$this->parameters['css']){
			$this->parameters['css'] = (isset($this->managed_datas['css']) ? $this->managed_datas['css'] : '');
		}
		$form = parent::get_form()."
			<div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name('charte_css')."'>".$this->format_text($this->msg['cms_module_common_view_charte_css'])."</label>
				</div>
				<div class='colonne-suite'>
					<textarea name='".$this->get_form_value_name('charte_css')."'>".$this->format_text($this->parameters['css'])."</textarea>
				</div>
			</div>
		";
		return $form;
	}
	
	public function save_form() {
		$this->parameters['css'] = $this->get_value_from_form('charte_css');
		
		return parent::save_form();
	}
	
	public function render($datas){
		if (empty($_SESSION["id_empr_session"])) {
			return '';
		}
		
		
		$datas['css'] = $this->parameters['css'];
		$datas['script_close_modal'] = '
			var modal_'.$this->get_module_dom_id().' = UIkit.modal("#'.$this->get_module_dom_id().'", {
				keyboard: false,
				bgclose: false,
				modal: false
			});
					
			modal_'.$this->get_module_dom_id().'.show();
			if (document.querySelector("#'.$this->get_module_dom_id().' .uk-overflow-container").clientHeight !=
				document.querySelector("#'.$this->get_module_dom_id().' .uk-overflow-container").scrollHeight) {
				document.querySelector("#'.$this->get_module_dom_id().' .uk-overflow-container").addEventListener("scroll",
					handleScroll_'.$this->get_module_dom_id().');
			} else {
				document.querySelector("#'.$this->get_module_dom_id().' .uk-modal-close").removeAttribute("disabled");
			}
						
			document.querySelector("#'.$this->get_module_dom_id().' .uk-modal-close").addEventListener("click", function() {
				fetch("'.$this->get_ajax_link().'")
				.then(function(res) {
					if (res.ok)	modal_'.$this->get_module_dom_id().'.hide();
				})
			})
							
			function handleScroll_'.$this->get_module_dom_id().'() {
				checkScrollPosition("'.$this->get_module_dom_id().'");
			}
		';
		
		if (!isset($_SESSION['empr_accepted_conditions'])) {
			$_SESSION['empr_accepted_conditions'] = 0;
		}
		
		if (!$_SESSION['empr_accepted_conditions']) {
			$html = parent::render($datas);
			if (!empty($datas['script_close_modal'])) $html.= '<script>'.$datas['script_close_modal'].'</script>';
			return $html;
		}
		return '';
	}
	
	public function execute_ajax() {
		$_SESSION['empr_accepted_conditions'] = 1;
		$response = array(
				'content' => "",
				'content-type' => "text/html"
		);
		return $response;
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_common_datasource_charte();
		return $datasource->get_format_data_structure();
	}
}