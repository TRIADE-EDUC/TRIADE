<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_env_var.class.php,v 1.12 2022/07/13 08:47:09 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_env_var extends cms_module_common_selector {
	
	/**
	 * 
	 * @var string
	 */
	public const EMPTY_PAGE = "";

	/**
	 * 
	 * @var string
	 */
	public const EMPTY_VAR = "";
	
	/**
	 * 
	 * @param number $id
	 */
	public function __construct($id = 0) {
		parent::__construct($id);
		$this->check_parameters();
	}

	/**
	 * Vérification des paramètres
	 */
	public function check_parameters() {
		$cms_page_id = $this->get_selected_page();
		if (is_array($this->parameters) && !empty($cms_page_id) && !$this->validate_page_id($cms_page_id)) {
			// la page a été supprimer on reset la valeur
			$this->parameters['page'] = self::EMPTY_PAGE;
		}
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see cms_module_common_selector::get_form()
	 */
	public function get_form() {
		global $cms_active;
		
		$form = "";
		if ($cms_active == 2) {
			$form = $this->get_new_form();
		} else {
			$form = $this->get_default_form();
		}
		
		return $form . parent::get_form();
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see cms_module_common_selector::save_form()
	 */
	public function save_form() {
		$cms_page_id = $this->get_value_from_form("env_page");
		$this->parameters = array(
			'var' => $this->get_value_from_form("env_var") ?? self::EMPTY_VAR,
			'page' => $this->validate_page_id($cms_page_id) ? $cms_page_id : self::EMPTY_PAGE
		);
		return parent::save_form();
	}
	
	/**
	 * Retourne la valeur de la variable d'environement sélectionnée
	 * @return mixed
	 */
	public function get_value() {
		if(isset($this->value)) {
			return $this->value;
		}
		
		$var = $this->get_selected_var();
		if (!empty($var)) {			
			global ${$var};
			$this->value = ${$var};
		}
		return $this->value ?? null;
	}
	
	/**
	 * Retourne la variable d'environement sélectionnée
	 * @return string|null
	 */
	public function get_selected_var() {
		if (!empty($this->parameters) && is_array($this->parameters)) {
			return $this->parameters['var'] ?? self::EMPTY_VAR;
		}
		return $this->parameters ?? self::EMPTY_VAR;
	}
	
	/**
	 * Retourne la page d'environement sélectionnée
	 * @return string|int|null
	 */
	public function get_selected_page() {
		if (!empty($this->parameters) && is_array($this->parameters)) {
			return $this->parameters['page'] ?? self::EMPTY_PAGE;
		}
		return self::EMPTY_PAGE;
	}

	/**
	 * Validation de l'identifiant de la page portail
	 * @param number $id
	 * @return boolean
	 */
	public function validate_page_id($id = 0) {
		if (0 == $id || empty($id)) {
			return false;
		}
		
		$pages_found = array_filter($this->get_pages(), function (\cms_page $cms_page) use ($id) {
			return $cms_page->get_id() == $id;
		});
		return !empty($pages_found);
	}
	
	/**
	 * Retourne la liste des pages portail
	 * @return cms_page[]
	 */
	public function get_pages() {
		if (!isset($this->pages)) {
			$this->pages = array();
			$cms_pages = new cms_pages();
			if (!empty($cms_pages->list)) {
				foreach ($cms_pages->list as $id) {
					$this->pages[] = new cms_page($id);
				}
			}
		}
		return $this->pages;
	}
	
	/**
	 * Formulaire pour l'ancien portail
	 * @return string
	 */
	public function get_default_form() {
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_env_var'>".$this->format_text($this->msg['cms_module_common_selector_env_var'])."</label>
				</div>
				<div class='colonne-suite'>";
		if (! empty($this->cms_build_env['page'])) {
			$form .= "
					<select name='".$this->get_form_value_name("env_var")."'>";
			foreach ($this->cms_build_env['page'] as $var) {
				$form .= "
						<option value='".$var['name']."' ".($var['name'] == $this->get_selected_var() ? "selected='selected'": "").">".$this->format_text(($var['comment']!=""? $var['comment'] : $var['name']))."</option>";
			}
			$form .= "
					</select>";
		} else {
			$form .= $this->format_text($this->msg['cms_module_common_selector_env_var_no_vars']);
		}
		$form .= "
				</div>
			</div>";
		return $form;
	}
	
	/**
	 * Formulaire pour le nouveau portail
	 * @return string
	 */
	public function get_new_form() {
		$pages_list = $this->format_text($this->msg['cms_module_common_selector_env_no_page']);
		$var_list = $this->format_text($this->msg['cms_module_common_selector_env_var_no_vars']);
		
		if (!empty($this->get_pages())) {
			$var_list = "<select  name='{$this->get_form_value_name("env_var")}'>";
			$pages_list = "<select  name='{$this->get_form_value_name("env_page")}'  onchange='filtredVars(event)'>";
			$pages_list .= "<option value='' data-cms_page_id='' " . (empty($this->get_selected_page()) ? "selected='selected'": "") . ">
								{$this->format_text($this->msg['cms_module_common_selector_env_var_no_page_selected'])}
							</option>";
								
			$first = false;
			foreach ($this->get_pages() as $cms_page) {
				foreach ($cms_page->vars as $var) {
					$label = empty($var['comment']) ? $var['name'] : $var['comment'];
					
					$var_selected  = "";
					if ($this->get_selected_var() == $var['name'] && $this->get_selected_page() == $cms_page->get_id()) {
						// On a la page et la variable qui match
						$var_selected = "selected='selected'";
					} elseif ($this->get_selected_var() == $var['name'] && empty($this->get_selected_page()) && !$first) {
						// On a la variable qui match mais aucune page donc on sélectionne le premier
						$var_selected = "selected='selected'";
						$first = true;
					}
					
					$var_list .= sprintf("<option value='%s' data-cms_page_id='%s' {$var_selected}>%s</option>", $var['name'], $cms_page->get_id(), $this->format_text($label));
				}
				
				$page_selected = "";
				if (!empty($this->get_selected_page()) && $this->get_selected_page() == $cms_page->get_id()) {
					$page_selected = "selected='selected'";
				}
				$pages_list .= sprintf("<option value='%s' {$page_selected}>%s</option>", $cms_page->get_id(), $this->format_text($cms_page->name));
			}
			$var_list .= "</select>";
			$pages_list .= "</select>";
		}
		
		$form = "
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_env_var'>".$this->format_text($this->msg['cms_module_common_selector_env_page'])."</label>
				</div>
				<div class='colonne-suite'>
					$pages_list
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='cms_module_common_selector_env_var'>".$this->format_text($this->msg['cms_module_common_selector_env_var'])."</label>
				</div>
				<div class='colonne-suite'>
					$var_list
					<span id='{$this->get_form_value_name("env_no_vars")}' style='display:none;'>{$this->format_text($this->msg['cms_module_common_selector_env_var_no_vars'])}</span>
				</div>
			</div>
			<script>
				function filtredVars(e) {
					if (e && e.target) {
						var selectPages = e.target;
					} else {
						var selectPages = document.querySelector('select[name=\"{$this->get_form_value_name("env_page")}\"]');
					}
					
					if (selectPages) {
						var selectVars = document.querySelector('select[name=\"{$this->get_form_value_name("env_var")}\"]');
						if (!selectVars) return false;
						
						var countHidden = 0;
						for (var i = 0; i < selectVars.options.length; i++) {
							var option = selectVars.options.item(i);
							if (selectPages.value != '' && option.getAttribute('data-cms_page_id') != selectPages.value) {
								selectVars.options.item(i).style.display = 'none';
								countHidden++;
							} else {
								selectVars.options.item(i).style.display = '';
							}
						}
						
						var noVars = document.getElementById('{$this->get_form_value_name("env_no_vars")}');
						if (countHidden == selectVars.options.length) {
							selectVars.style.display = 'none';
							if (noVars) noVars.style.display = '';
						} else {
							selectVars.style.display = '';
							if (noVars) noVars.style.display = 'none';
						}
						
						var selectedOptions = selectVars.selectedOptions.item(0);
						if (selectedOptions && selectedOptions.style.display == 'none') {
							selectVars.value = '';
						}
					}
				}
				
				filtredVars();
			</script>";
					
		return $form;
	}
}