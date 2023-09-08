<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_diffusion_ui.class.php,v 1.3 2019-06-05 14:19:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/bannettes/list_bannettes_ui.class.php");

class list_bannettes_diffusion_ui extends list_bannettes_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_form_title() {
		global $msg;
		return $msg['dsi_ban_search'];
	}
	
	protected function init_default_columns() {
		$this->columns = array();
	}
		
	protected function init_available_filters() {
		parent::init_available_filters();
		$this->available_filters['main_fields']['type'] = 'dsi_bannette_type';
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		global $msg, $charset;

		$display = "<tr >
					<th width='1%' class='sorttable_nosort'>
					</th>
					<th width='60%'>
						<strong>".htmlentities($msg['dsi_ban_form_nom'],ENT_QUOTES, $charset)."</strong>
						<br />(".htmlentities($msg['dsi_ban_form_com_public'],ENT_QUOTES, $charset).")
					</th>
					<th width='20%'>
						<strong>".htmlentities($msg['dsi_ban_date_last_envoi'],ENT_QUOTES, $charset)."</strong>
						<br />(".htmlentities($msg['dsi_ban_date_last_remp'],ENT_QUOTES, $charset).")
					</th>
					<th width='10%'>
						<strong>".htmlentities($msg['dsi_ban_nb_notices'],ENT_QUOTES, $charset)."</strong>
					</th>
				</tr>";
		return $display;
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		global $charset;
		global $sub;
		
		if ($object->proprio_bannette) {
			$nom_bannette = "<span style='color:red'>".htmlentities($object->nom_bannette,ENT_QUOTES, $charset)."</span>" ;
		} else {
			$nom_bannette = htmlentities($object->nom_bannette,ENT_QUOTES, $charset) ;
		}
		$td_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\" ";
		$display = "
		<tr class='".($indice % 2 ? 'odd' : 'even')."' $td_javascript >
			<td width='1%' class='center'>
				<input type='checkbox' name='liste_bannette[]' id='auto_".$object->id_bannette."' value='$object->id_bannette' ".($sub == 'lancer' ? "checked='checked'" : '')."/>
			</td>
			<td width='60%'>
				<strong>".$nom_bannette."</strong>
				<br />(".htmlentities($object->comment_public,ENT_QUOTES, $charset).")
			</td>
			<td sorttable_customkey='".$object->date_last_envoi."'>
				<strong>".htmlentities($object->aff_date_last_envoi,ENT_QUOTES, $charset)."</strong>";
			if ($object->alert_diff) {
				$display .= "<br /><span style='color:red'>(".htmlentities($object->aff_date_last_remplissage,ENT_QUOTES, $charset).")</span>";
			} else {
				$display .= "<br />(".htmlentities($object->aff_date_last_remplissage,ENT_QUOTES, $charset).")" ;
			}
			$display .= "
			<td width='10%'>
				<strong>".htmlentities($object->nb_notices,ENT_QUOTES, $charset)."</strong>
			</td>
		</tr>";
		return $display;
	}
	
	/**
	 * Liste des objets
	 */
	public function get_display_content_list() {
		global $msg, $charset;
		global $sub;
		
		$display = '';
		$id_check_list = '';
		if(isset($this->applied_group[0]) && $this->applied_group[0]) {
			$grouped_objects = $this->get_grouped_objects();
			foreach($grouped_objects as $group_label=>$objects) {
				$display .= "
					<tr>
						<td class='list_ui_content_list_group ".$this->objects_type."_content_list_group' colspan='".count($this->columns)."'>
							".$group_label."
						</th>
					</tr>";
				foreach ($objects as $i=>$object) {
					$id_check="auto_".$object->id_bannette;
					if($id_check_list)$id_check_list.='|';
					$id_check_list.=$id_check;
					$display .= $this->get_display_content_object_list($object, $i);
				}
			}
		} else {
			foreach ($this->objects as $i=>$object) {
				$id_check="auto_".$object->id_bannette;
				if($id_check_list)$id_check_list.='|';
				$id_check_list.=$id_check;
				$display .= $this->get_display_content_object_list($object, $i);
			}
		}
		$display.="<input type='hidden' id='auto_id_list' name='auto_id_list' value='".$id_check_list."' >";
		return $display;
	}
	
	protected function get_title() {
		global $msg;
		return "<h1>".($this->filters['auto'] ?$msg['dsi_diff_ban_auto_found'] : $msg['dsi_diff_ban_manu_found'])."</h1>";
	}
	
	public function get_display_list() {
		global $msg, $charset;
		global $base_path;
		global $current_module;
		global $sub;
		
		$display = $this->get_title();
		
// 		// Affichage du formulaire de recherche
		if($sub == 'auto' || $sub == 'manu') {
			$display .= $this->get_search_form();
		} else {
			$display .= $this->get_search_hidden_form();
		}
		
		// Affichage de la human_query
// 		$display .= $this->_get_query_human();
	
		//Récupération du script JS de tris
// 		$display .= $this->get_js_sort_script_sort();
		
		$display .= "
			<form class='form-$current_module' id='bannette_lecteurs_assoce' name='bannette_lecteurs_assoce' method='post' action='".static::get_controller_url_base()."' >
				<h3>$msg[dsi_dif_act_ban_contenu]
						<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,1);'>
						<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(document.getElementById(\"auto_id_list\").value,0);'>
				</h3>
				<div class='form-contenu'>
					<script type='text/javascript' src='./javascript/sorttable.js'></script>
					<script>	
						function confirm_dsi_ban_diffuser() {
				       		result = confirm(\"".$msg['confirm_dsi_ban_diffuser']."\");
				       		if(result) {
				       			return true;
							} else
				           		return false;
				    	}
				    	function confirm_dsi_dif_full_auto() {
				       		result = confirm(\"".$msg['confirm_dsi_dif_full_auto']."\");
				       		if(result) {
				       			return true;
							} else
				           		return false;
				    	}
					</script>";
		//Affichage de la liste des objets
		$display .= "<table id='".$this->objects_type."_list' width='100%' class='sortable'>";
		$display .= $this->get_display_header_list();
		if(count($this->objects)) {
			$display .= $this->get_display_content_list();
		} else {
			switch($sub) {
				case 'auto':
					$display .= $msg['dsi_no_ban_found'];
					break;
				case 'lancer':
					$display .= $msg['dsi_no_automatic_ban_found_ech'];
					break;
			}
		}
		$display .= "</table>";
		$display .= $this->pager();
		$display .= "
					<div class='row'>&nbsp;</div>
					<div class='row'>
						<div class='left'>
							<input type='button' class='bouton' name='bt_vider' value=\"".$msg['dsi_ban_vider']."\" onclick=\"this.form.suite.value='vider'; this.form.submit();\" />
							<input type='button' class='bouton' name='bt_remplir' value=\"".$msg['dsi_ban_remplir']."\" onclick=\"this.form.suite.value='remplir'; this.form.submit();\" />
							<input type='button' class='bouton' name='bt_voircontenu' value=\"".$msg['dsi_ban_visualiser']."\" onclick=\"this.form.suite.value='visualiser'; this.form.submit();\" />
							<input type='button' class='bouton' name='bt_diffuser' value=\"".$msg['dsi_ban_diffuser']."\" onclick=\"if(confirm_dsi_ban_diffuser()){this.form.suite.value='diffuser'; this.form.submit();}\" />
							<input type='button' class='bouton' name='bt_diffuser' value=\"".$msg['dsi_dif_full_auto']."\" onclick=\"if(confirm_dsi_dif_full_auto()){this.form.suite.value='full_auto'; this.form.submit();}\" />
							<input type='hidden' name='suite' value='' />
							<input type='hidden' name='id_classement' value='".$this->filters['id_classement']."' />
							<input type='hidden' name='".$this->objects_type."_name' value='".$this->filters['name']."' />
						</div>
						<div class='right'>
							<input type='button' class='bouton' name='gen_document' value=\"".$msg["dsi_ban_gen_document"]."\" onclick=\"this.form.suite.value='gen_document'; this.form.submit();\" />	
							<input type='button' class='bouton' name='bt_exporter' value=\"".$msg['dsi_ban_exporter_diff']."\" onclick=\"this.form.suite.value='exporter'; this.form.submit();\" />
						</div>
					</div>
				</div>
			</form>";
		return $display;
	}
	
	public static function get_controller_url_base() {
		global $base_path, $sub;
	
		return $base_path.'/dsi.php?categ=diffuser&sub='. $sub;
	}
}