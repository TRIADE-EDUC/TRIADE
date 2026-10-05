<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_bannette_ui.class.php,v 1.17.4.1 2025/02/20 09:18:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/list/readers/list_readers_bannette_ui.tpl.php");
require_once($class_path."/bannette.class.php");

class list_readers_bannette_ui extends list_readers_ui {
	
	protected $id_bannette;
	
	protected $bannette;
	
	protected function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg['dsi_ban_lec_assoce'], ENT_QUOTES, $charset)." : ".$this->get_bannette()->nom_bannette;
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_filter('codestat_one', 'visible', 0);
	}
	
	protected function init_default_selected_filters() {
		global $pmb_lecteurs_localises;
		
		$this->add_selected_filter('categories');
		$this->add_selected_filter('groups');
		if($pmb_lecteurs_localises) {
			$this->add_selected_filter('locations');
		} else {
			$this->add_empty_selected_filter();
		}
		$this->add_selected_filter('name');
		$this->add_selected_filter('has_mail');
		$this->add_selected_filter('has_affected');
		$this->add_selected_filter('mail');
	}
	
	protected function get_search_filter_has_affected() {
		global $msg;
	
		return "
			<input type='radio' id='".$this->objects_type."_has_affected_no' name='".$this->objects_type."_has_affected' value='0' ".(!$this->filters['has_affected'] ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_has_affected_no'>".$msg['39']."</label>		
			<input type='radio' id='".$this->objects_type."_has_affected_yes' name='".$this->objects_type."_has_affected' value='1' ".($this->filters['has_affected'] ? "checked='checked'" : "")." />
			<label for='".$this->objects_type."_has_affected_yes'>".$msg['40']."</label>";
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $msg;
		global $faire;
		
		$search_filters_form = '';
		if($faire == "enregistrer") {
			$search_filters_form .= "<div class='erreur'>".$msg["dsi_bannette_lecteurs_update"]."</div><br />";
		}
		$search_filters_form .= parent::get_search_filters_form();
		return $search_filters_form;
	}
	
	/**
	 * Jointure externes SQL pour les lecteurs affectés
	 */
	protected function _get_query_join_filter_affected() {
		
		return " JOIN bannette_abon ON bannette_abon.num_empr = empr.id_empr";
	}
	
	/**
	 * Filtre SQL pour les lecteurs affectés
	 */
	protected function _get_query_filter_affected() {
		global $id_bannette;
		
		$filter_query_affected = '';
		if($this->id_bannette || $id_bannette) {
			$filter_query_affected = 'bannette_abon.num_bannette = "'.($this->id_bannette ? $this->id_bannette : $id_bannette).'"';
		}
		return $filter_query_affected;
	}
	
	protected function get_search_buttons_extension() {
		global $base_path;
		global $msg;
		global $form_cb;
		
		return $this->get_interface_button($msg['bt_retour'], ['location' => $base_path."/dsi.php?categ=bannettes&sub=pro&id_bannette=&suite=search&form_cb=".$form_cb]);
	}
	
	protected function add_column_mails_selection() {
		$this->columns[] = array(
				'property' => 'mails_selection',
				'label' => "",
				'html' => "",
                'exportable' => false
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'mails_selection':
				$mails_selection = '';
				if ($object->mail){
					$requete_affect = "SELECT * FROM bannette_abon where num_empr='".$object->id."' and num_bannette='".$this->id_bannette."' ";
					$res_affect = pmb_mysql_query($requete_affect);
					if (pmb_mysql_num_rows($res_affect)){
						$abon=pmb_mysql_fetch_object($res_affect);
					}
					$destinataires = explode(";",$object->mail) ;
					if(count($destinataires)>1){
						$mails_selection="<select name='".$this->objects_type."_mails_selection[".$object->id."]' data-empr-id='".$object->id."' class='".$this->objects_type."_mails_selection'>";
						if(!isset($abon->bannette_mail) || !$abon->bannette_mail) $selected=" selected='selected' ";
						$mails_selection.="<option value ='' $selected>".$msg["dsi_ban_form_mail_all"]."</option>";
						foreach($destinataires as $mail){
							$selected="";
							if(isset($abon) && $mail == $abon->bannette_mail)  $selected=" selected='selected' ";
							$mails_selection.="<option value ='$mail' $selected >$mail</option>";
						}
						$mails_selection.="</select>";
					}
				}
				$content .= $mails_selection;
				break;
			default:
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_html_content_selection() {
	    global $msg, $charset;
		return "<div class='center'><input type='checkbox' id='".$this->get_name_selection_objects()."_!!id!!' name='".$this->get_name_selection_objects()."[!!id!!]' class='list_ui_selection ".$this->objects_type."_selection' value='!!id!!' title='".htmlentities($msg['list_ui_selection_checkbox'], ENT_QUOTES, $charset)."' !!subscribed!!></div>";
	}
	
	protected function get_display_cell_html_value($object, $value) {
		if(method_exists($object, 'get_id')) {
			$value = str_replace('!!subscribed!!', ($this->get_bannette()->is_subscribed($object->get_id()) ? "checked='checked'" : ""), $value);
		} else {
			$value = str_replace('!!subscribed!!', ($this->get_bannette()->is_subscribed($object->id) ? "checked='checked'" : ""), $value);
		}
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function init_default_columns() {
	
		$this->add_column_selection();
		$this->add_column('empr_name');
		$this->add_column('mail');
		$this->add_column_mails_selection();
	}
	
	protected function init_columns($columns=array()) {
		parent::init_columns($columns);
		if(count($this->selected_columns)) {
			$this->add_column_mails_selection();
		}
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		global $base_path;
		global $id_bannette;
		
		$this->selection_actions = array();
		if($this->id_bannette || $id_bannette) {
			$link = array(
					'href' => $base_path."/dsi.php?categ=bannettes&sub=pro&id_bannette=".($this->id_bannette ? $this->id_bannette : $id_bannette)."&suite=affect_lecteurs&faire=enregistrer"
			);
			$this->add_selection_action('save', $msg['77'], 'sauv.gif', $link);
		}
	}

	protected function add_events_on_selection_actions() {
		$display = "<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){";
		foreach($this->get_selection_actions() as $action) {
			$display .= "
				on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
					var selection = new Array();
					query('.".$this->objects_type."_selection:checked').forEach(function(node) {
						selection.push(node.value);
					});
					var mails_selection = new Array();
					query('.".$this->objects_type."_mails_selection').forEach(function(node) {
						mails_selection.push(node);
					});
					var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
					if(!confirm_msg || confirm(confirm_msg)) {
						".(isset($action['link']['href']) && $action['link']['href'] ? "
							var selected_objects_form = domConstruct.create('form', {
								action : '".$action['link']['href']."',
								name : '".$this->objects_type."_selected_objects_form',
								id : '".$this->objects_type."_selected_objects_form',
								method : 'POST'
							});
							selection.forEach(function(selected_option) {
								var selected_objects_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->objects_type."_selected_objects[]',
									value : selected_option
								});
								domConstruct.place(selected_objects_hidden, selected_objects_form);
							});
							mails_selection.forEach(function(selector_node) {
								var empr_id = selector_node.getAttribute('data-empr-id');
								var selected_mails_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->objects_type."_mails_selection['+empr_id+']',
									value : selector_node.value
								});
								domConstruct.place(selected_mails_hidden, selected_objects_form);
							});
							var page_hidden = domConstruct.create('input', {
								type : 'hidden',
								id : '".$this->objects_type."_page',
								name : '".$this->objects_type."_page',
								value : document.getElementById('".$this->objects_type."_page').value
							});
							domConstruct.place(page_hidden, selected_objects_form);
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							"
									: "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
					}
				});";
		}
		$display .= "});
		</script>";
		return $display;
	}
	
	public function get_export_icons() {
		global $msg, $base_path, $form_cb;
		
		if($this->get_setting('display', 'search_form', 'export_icons')) {
			return $this->get_interface_button($msg['dsi_ban_affect_equation'], ['location' => $base_path."/dsi.php?categ=bannettes&sub=pro&suite=affect_equation&id_bannette=".$this->id_bannette."&form_cb=".$form_cb]);
		}
		return "";
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $sub, $id_bannette, $suite;
	
		return $base_path.'/dsi.php?categ='.$categ.'&sub='.$sub.'&id_bannette='.$id_bannette.'&suite='.$suite;
	}
	
	public function run_action_affect_lecteurs() {
	    $selected_objects = static::get_selected_objects();
		if(is_array($selected_objects)) {
			$name = $this->objects_type."_mails_selection";
			global ${$name};
			$sel_mail = ${$name};
			$objects = $this->objects;
			foreach ($objects as $indice=>$object) {
				$query = "select count(*) from bannette_abon where num_empr='".$object->id."' and num_bannette='".$this->id_bannette."'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_result($result, 0, 0)) {
					if(!in_array($object->id, $selected_objects)) {
						pmb_mysql_query("delete from bannette_abon where num_empr='".$object->id."' and num_bannette='".$this->id_bannette."'");
						if($this->filters['has_affected']) {
							unset($this->objects[$indice]);
							$this->pager['nb_results']--;
						}
					}
					
				} else {
					if(in_array($object->id, $selected_objects)) {
						pmb_mysql_query("insert into bannette_abon set num_empr='".$object->id."', num_bannette='".$this->id_bannette."', bannette_mail='".$sel_mail[$object->id]."'");
					}
				}
			}
		}
	}
	
	public function set_id_bannette($id_bannette) {
		$this->id_bannette = intval($id_bannette);
	}
	
	public function get_bannette() {
		if(!isset($this->bannette)) {
			$this->bannette = new bannette($this->id_bannette);
		}
		return $this->bannette;
	}
}