<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_acces_profiles_ui.class.php,v 1.1.8.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/acces.class.php');

class list_acces_profiles_ui extends list_ui {
	
	protected static $domain;
	
	/**
	 * Instance de la classe domain
	 * @var domain
	 */
	protected $dom;
	
	protected $profile_type;
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'domain' => static::$domain,
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'prf_name' => 'dom_prf_name',
					'prf_use' => 'dom_prf_use',
					'prf_rule' => 'dom_prf_rule',
			)
		);
	}
	
	/**
	 * Initialisation des colonnes éditables disponibles
	 */
	protected function init_available_editable_columns() {
		$this->available_editable_columns = array(
				'prf_use',
		);
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'prf_name', 'prf_use', 'prf_rule'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('prf_name');
		$this->add_column('prf_use');
		$this->add_column('prf_rule');
	}
	
	public function get_display_search_form() {
		return '';
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('prf_name', 'display_mode', 'edition');
		$this->set_setting_column('prf_use', 'display_mode', 'edition');
		$this->set_setting_column('prf_use', 'edition_type', 'select');
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'prf_rule':
				$content.= nl2br(htmlentities($object->prf_hrule,ENT_QUOTES, $charset));
				$content.= "<input type=hidden id='prf_hrule[".$object->prf_id."]' name='prf_hrule[".$object->prf_id."]' value='".$object->prf_hrule."' />";
				$content.= "<input type='hidden' id='prf_id[".$object->prf_id."]' name='prf_id[".$object->prf_id."]' value='".$object->prf_id."' />";
				$content.= "<input type='hidden' id='prf_rule[".$object->prf_id."]' name='prf_rule[".$object->prf_id."]' value='".$object->prf_rule."' />";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_selector_profiles_use() {
		global $charset;
		
		//generation selecteur
		$selector = "<select name='!!sel_name!!' id='!!sel_name!!'>";
		$selector.= "<option value=\"0\" >".htmlentities($this->get_dom()->getComment($this->profile_type.'_prf_def_lib'), ENT_QUOTES, $charset)."</option>";
		foreach ($this->objects as $object) {
			$selector .= "<option value=\"".$object->prf_id."\" >";
			$selector .= htmlentities($object->prf_name, ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		$selector .= "<script type=\"text/javascript\">!!sel_script!!</script>";
		return $selector;
	}
	
	protected function get_cell_edition_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'prf_name':
				$content .= "<input type='text' class='in_cell' id='prf_lib[".$object->prf_id."]' name='prf_lib[".$object->prf_id."]' value='".htmlentities($object->prf_name, ENT_QUOTES, $charset)."' />";
				break;
			case 'prf_use':
				$content .= $this->get_selector_profiles_use();
				$content = str_replace('!!sel_name!!', "prf_used[".$object->prf_id."]", $content);
				$content = str_replace('!!sel_script!!', "document.getElementById(\"prf_used[".$object->prf_id."]\").value=\"".$object->prf_used."\";" ,$content);
				break;
			default :
				$content .= parent::get_cell_edition_content($object, $property);
				break;
		}
		return $content;
	}
	
	/**
	 * Liste des objets
	 */
	public function get_display_content_list() {
		global $charset;
		
		$display = '';
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='odd'\" ";
		$display.= "<tr class='odd' ".$tr_javascript.">";
		$display.= "<td colspan='".count($this->columns)."'>".htmlentities($this->get_dom()->getComment($this->profile_type.'_prf_def_lib'), ENT_QUOTES, $charset)."</td>";
		$display.= "</tr>";
		$display .= parent::get_display_content_list();
		return $display;
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$edit_link = array(
				'showConfiguration' => static::get_controller_url_base()."&action=list_save"
		);
		$this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);
	}
	
	protected function get_options_editable_column($object, $property) {
		switch ($property) {
			case 'prf_use':
				$options = array();
				foreach ($this->objects as $object) {
					$options[] = array('value' => $object->prf_id, 'label' => $object->prf_name);
				}
				return $options;
			default:
				return parent::get_options_editable_column($object, $property);
		}
	}
	
	public function get_dom() {
		global $ac, $dom;
		
		if(empty($this->dom)) {
			if(empty($dom)) {
				if (!$ac) {
					$ac= new acces();
				}
				if (empty($dom)) {
					$dom=$ac->setDomain($this->filters['domain']);
				}
			}
			$this->dom = $dom;
		}
		return $this->dom;
	}
	
	public function set_dom($dom) {
		$this->dom = $dom;
	}
	
	protected function get_display_left_actions() {
		global $base_path, $msg;
		
		return $this->get_interface_button($msg['654'], ['location' => $base_path."/admin.php?categ=acces&sub=domain&action=view&id=".static::$domain])."
			<input type='button' onclick=\"this.form.action='".static::get_controller_url_base()."&action=update'; this.form.submit();return false;\" value=\"".addslashes($msg['77'])."\" class='bouton' />";
	}
	
	protected function get_button_delete() {
		global $msg;
		
		return $this->get_interface_button($msg['63'], ['location' => static::get_controller_url_base()."&action=delete"]);
	}
	
	protected function get_display_block_actions($left_actions) {
		return "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				".$left_actions."
			</div>
			<div class='right'>
				".$this->get_button_delete()."
			</div>
		</div>";
	}
	
	public static function get_controller_url_base() {
		return parent::get_controller_url_base().'&id='.static::$domain;
	}
	
	public static function set_domain($domain) {
		static::$domain = $domain;
	}
}