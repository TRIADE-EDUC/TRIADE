<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_demandes_ui.class.php,v 1.2.4.1 2025/04/16 08:15:54 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/demandes_types.class.php");

class list_opac_demandes_ui extends list_opac_ui {
	
	protected $demandes;
	
	protected $themes;
	
	protected $types;
	
	protected function _get_query_base() {
		$query = 'select id_demande
				from demandes d 
				join demandes_type dy on d.type_demande=dy.id_type
				join demandes_theme dt on d.theme_demande=dt.id_theme				
				left join demandes_users du on du.num_demande=d.id_demande
				left join users on (du.num_user=userid) 	
				';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new demandes($row->id_demande);
	}
		
// 	protected function get_form_title() {
// 		global $msg;
		
// 		return $msg['demandes_liste'];
// 	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		global $pmb_lecteurs_localises;
		
		$this->available_filters =
		array('main_fields' =>
				array(
						'user_input' => 'demandes_titre',
						'demandeur' => 'demandes_user_filtre',
						'state' => 'demandes_etat_filtre',
						'date' => 'demandes_periode_filtre',
						'affectation' => 'demandes_affectation_filtre',
						'theme' => 'demandes_theme_filtre',
						'type' => 'demandes_type_filtre'
				)
		);
		if($pmb_lecteurs_localises) {
			$this->available_filters['main_fields']['location'] = 'demandes_localisation_filtre';
		}
		$this->available_filters['custom_fields'] = array();
		$this->add_custom_fields_available_filters('demandes', 'id_demande');
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $pmb_lecteurs_localises;
		
		$this->filters = array(
				'user_input' => '',
				'demandeur' => '',
				'state' => 0,
				'date_start' => '',
				'date_end' => '',
				'affectation' => 0,
                'theme' => 0,
                'type' => 0,
				'type_action' => 0,
				'statut_action' => array(),
				'id_demande' => 0
		);
		
		if($pmb_lecteurs_localises || array_key_exists('location', $this->selected_filters)) {
		    $this->filters['location'] = 0;
		}
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('state');
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date_demande', 'desc');
	    $this->add_applied_sort('titre_demande');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $demandes_notice_auto;
		
		$this->available_columns =
		array('main_fields' =>
				array(
						'see' => 'demandes_see',
						'theme_demande' => 'demandes_theme',
						'type_demande' => 'demandes_type',
						'titre_demande' => 'demandes_titre',
						'etat_demande' => 'demandes_etat',
						'date_demande' => 'demandes_date_dmde',
						'date_prevue' => 'demandes_date_prevue',
						'deadline_demande' => 'demandes_date_butoir',
						'demandeur' => 'demandes_demandeur',
						'attribution' => 'demandes_attribution',
				        'progression' => 'demandes_progression',
						'linked_record' => 'demandes_linked_record'
				)
		);
		if($demandes_notice_auto) {
			$this->available_columns['main_fields']['record'] = 'demandes_notice';
		}
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('demandes', 'id_demande');
	}
	
	protected function add_column_more_details() {
		$this->columns[] = array(
				'property' => '',
				'label' => "plus_detail",
				'html' => "<img hspace=\"3\" border=\"0\" onclick=\"expand_action('action!!id!!','!!id!!', true); return false;\" title=\"\" id=\"action!!id!!Img\" name=\"imEx\" class=\"img_plus\" src=\"".get_url_icon('plus.gif')."\">",
				'exportable' => false
		);
	}
	
	protected function get_display_html_content_selection() {
		return "<div class='center'><input type='checkbox' id='chk_!!id!!' name='chk[!!id!!]' class='".$this->objects_type."_selection' value='!!id!!'></div>";
	}
	
	protected function init_default_columns() {
	    global $opac_demandes_affichage_simplifie;
		global $demandes_notice_auto;
		
		$this->add_column_more_details();
		$this->add_column('see');
		$this->add_column('titre_demande');
		if(empty($this->filters['state'])) {
		    $this->add_column('etat_demande');
		}
		$this->add_column('date_demande');
		if(!$opac_demandes_affichage_simplifie) {
		    $this->add_column('deadline_demande');
		    $this->add_column('attribution');
		    $this->add_column('progression');
		}
		if(count($this->available_columns['custom_fields'])) {
			foreach ($this->available_columns['custom_fields'] as $property=>$label) {
				$this->add_column($property, $label);
			}
		}
		$this->add_column('linked_record');
		if($demandes_notice_auto) {
		    $this->add_column('record');
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('date_demande', 'datatype', 'date');
		$this->set_setting_column('date_prevue', 'datatype', 'date');
		$this->set_setting_column('deadline_demande', 'datatype', 'date');
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    $this->pager['all_on_page'] = true;
	}
	
	public function get_display_list() {
	    global $javascript_path, $msg;
		
		$display = parent::get_display_list();
		$display .= "
			<script src='".$javascript_path."/demandes_form.js' type='text/javascript'></script>
			<script type='text/javascript'>
				var msg_demandes_actions_nocheck='".addslashes($msg['demandes_actions_nocheck'])."'; 
				var msg_demandes_confirm_suppr = '".addslashes($msg['demandes_confirm_suppr'])."';
				var msg_demandes_note_confirm_suppr = '".addslashes($msg['demandes_note_confirm_suppr'])."';
			</script>
			<script type='text/javascript'>
				function alert_progressiondemande(){
					alert(\"".$msg['demandes_progres_ko']."\");
				}
			</script>
		";
		return $display;	
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["demandes_liste_vide"], ENT_QUOTES, $charset);
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	protected function init_no_sortable_columns() {
	    $this->no_sortable_columns = array(
	        'see'
	    );
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $user_input;
		global $idetat, $idempr, $date_debut,$date_fin;
		global $iduser, $id_type, $id_theme;
		global $dmde_loc;
		
		if(isset($user_input)) {
			$this->filters['user_input'] = stripslashes($user_input);
		}
		if(isset($idetat)) {
			$this->filters['state'] = intval($idetat);
		}
		if(isset($date_debut)) {
			$this->filters['date_start'] = $date_debut;
		}
		if(isset($date_fin)) {
			$this->filters['date_end'] = $date_fin;
		}
		if(isset($iduser)) {
			$this->filters['affectation'] = intval($iduser);
		}
		if(isset($id_type)) {
			$this->filters['type'] = intval($id_type);
		}
		if(isset($id_theme)) {
			$this->filters['theme'] = intval($id_theme);
		}
		if(isset($dmde_loc)) {
			$this->filters['location'] = intval($dmde_loc);
		}
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_user_input() {
		global $charset;
		
		return "<input type='text' class='saisie-30em' name='user_input' id='user_input' value='".htmlentities($this->filters['user_input'], ENT_QUOTES, $charset)."'/>";
	}
	
	protected function get_search_filter_state() {
		return $this->get_demandes()->getStateSelector($this->filters['state'],'',true);
	}
	
	protected function get_search_filter_date() {
		global $msg;
		
		//Formulaire des filtres
		$date_deb="<input type='date' id='date_debut' name='date_debut' value='".$this->filters['date_start']."' />";
		$date_but="<input type='date' id='date_fin' name='date_fin' value='".$this->filters['date_end']."' />";
		return sprintf($msg['demandes_filtre_periode_lib'],$date_deb,$date_but);
	}
	
	protected function get_search_filter_affectation() {
		return $this->get_demandes()->getUsersSelector('',true,false,true);
	}
	
	protected function get_search_filter_theme() {
		$themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$this->filters['theme']);
		return $themes->getListSelector($this->filters['theme'],'',true);
	}
	
	protected function get_search_filter_type() {
		$types = new demandes_types('demandes_type','id_type','libelle_type',$this->filters['type']);
		return $types->getListSelector($this->filters['type'],'',true);
	}
	
	protected function get_search_filter_location() {
// 		global $msg, $charset;
		
// 		$req_loc = "select idlocation, location_libelle from docs_location";
// 		$res_loc = pmb_mysql_query($req_loc);
// 		$sel_loc = "<select id='dmde_loc' name='dmde_loc' onchange='this.form.act.value=\"search\";submit()' >";
// 		$sel_loc .= "<option value='0' ".(!$this->filters['location'] ? 'selected' : '').">".htmlentities($msg['demandes_localisation_all'],ENT_QUOTES,$charset)."</option>";
// 		while($loc = pmb_mysql_fetch_object($res_loc)){
// 			$sel_loc .= "<option value='".$loc->idlocation."' ".(($this->filters['location']==$loc->idlocation) ? 'selected' : '').">".htmlentities($loc->location_libelle,ENT_QUOTES,$charset)."</option>";
// 		}
// 		$sel_loc.= "</select>";
// 		return $sel_loc;
	}
		
	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
	    $filter_join_query = '';
	    if(array_key_exists("location", $this->filters) && $this->filters['location']) {
	        $filter_join_query .= " left join empr on (num_demandeur=id_empr) ";
	    }
	    return $filter_join_query;
	}
	
	protected function _add_query_filters() {
		if($this->filters['user_input']) {
			$user_input = str_replace('*','%',$this->filters['user_input']);
			$this->query_filters [] = "titre_demande like '%".addslashes($user_input)."%'";
		}
		$this->_add_query_filter_simple_restriction('demandeur', 'num_demandeur', 'integer');
		$this->_add_query_filter_simple_restriction('state', 'etat_demande');
		//Filtre date
		if($this->filters['date_start']<$this->filters['date_end']){
			$this->query_filters [] = "(date_demande >= '".$this->filters['date_start']."' and deadline_demande <= '".$this->filters['date_end']."' )";
		}
		
		$this->_add_query_filter_simple_restriction('theme', 'theme_demande');
		$this->_add_query_filter_simple_restriction('type', 'type_demande');
		$this->_add_query_filter_simple_restriction('location', 'empr_location', 'integer');
		$this->_add_query_filter_simple_restriction('type_action', 'type_action');
		$this->_add_query_filter_multiple_restriction('statut_action', 'statut_action');
		if($this->filters['affectation']) {
			if($this->filters['affectation'] == -1){
				$this->query_filters [] = 'num_user IS NULL';
			} else {
				$this->query_filters [] = 'num_user = "'.$this->filters['affectation'].'"';
			}
		}
		$this->_add_query_filter_simple_restriction('id_demande', 'id_demande', 'integer');
	}
	
	protected function _get_object_property_theme_demande($object) {
		return $this->get_themes()->getLabel($object->theme_demande);
	}
	
	protected function _get_object_property_type_demande($object) {
		return $this->get_types()->getLabel($object->type_demande);
	}
	
	protected function _get_object_property_etat_demande($object) {
		return $object->workflow->getStateCommentById($object->etat_demande);
	}
	
	protected function _get_object_property_demandeur($object) {
		return emprunteur::get_name($object->num_demandeur, 1);
	}
	
	protected function _get_object_property_attribution($object) {
	    $attribution = '';
	    if (!empty($object->users)) {
	        foreach ($object->users as $user) {
	            if ($user['statut'] == 1) {
	                if (!empty($attribution)) {
	                    $attribution .= "/ ";
	                }
	                $attribution .= $user['nom'];
	            }
	        }
	    }
	    return $attribution;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'see':
			    if($object->dmde_read_opac == 1){
			        $content .= "<img onclick=\"change_read('read".$object->id_demande."','$object->id_demande', true); return false;\" title=\"\" id=\"read".$object->id_demande."Img1\" class=\"img_plus\" src=\"".get_url_icon('notification_empty.png')."\" style='display:none'>
								<img onclick=\"change_read('read".$object->id_demande."','$object->id_demande', true); return false;\" title=\"\" id=\"read".$object->id_demande."Img2\" class=\"img_plus\" src=\"".get_url_icon('notification_new.png')."\">";
			    } else {
			        $content .= "<img onclick=\"change_read('read".$object->id_demande."','$object->id_demande', true); return false;\" title=\"\" id=\"read".$object->id_demande."Img1\" class=\"img_plus\" src=\"".get_url_icon('notification_empty.png')."\" >
								<img onclick=\"change_read('read".$object->id_demande."','$object->id_demande', true); return false;\" title=\"\" id=\"read".$object->id_demande."Img2\" class=\"img_plus\" src=\"".get_url_icon('notification_new.png')."\" style='display:none'>";
			    }
				break;
			case 'progression':
			    $content .= "<img src='".get_url_icon('jauge.png')."' height='15px' width=\"".$object->progression."%\" alt='".$object->progression."%' />";
				break;
			case 'linked_record':
			    if($object->get_num_linked_notice()) {
			        $record_datas = record_display::get_record_datas($object->get_num_linked_notice());
			        $content .= "<a href='".htmlentities($record_datas->get_permalink(), ENT_QUOTES, $charset)."' title='".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."' >".htmlentities($record_datas->get_tit1(), ENT_QUOTES, $charset)."</a>";
			    }
			    break;
			case 'record':
			    if(demandes::is_notice_visible($object)){
			        $content = "<a href='".notice::get_permalink($object->num_notice)."' alt='".$msg['demandes_see_notice']."' title='".$msg['demandes_see_notice']."'><img src='".get_url_icon('mois.gif')."' /></a>";
			    }
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_class_cell_header($name) {
	    $class = parent::_get_class_cell_header($name);
	    switch($name) {
	        case 'see':
	            $class .= ' empr_demandes_col2';
	            break;
	        case 'titre_demande':
	            $class .= ' empr_demandes_col_titre';
	            break;
	        case 'etat_demande':
	            $class .= ' empr_demandes_col_etat';
	            break;
	        case 'date_demande':
	            $class .= ' empr_demandes_col_date_dmde';
	            break;
	        case 'date_prevue':
	            $class .= ' empr_demandes_col_date_butoir';
	            break;
	        case 'attribution':
	            $class .= ' empr_demandes_col_user';
	            break;
	        case 'progression':
	            $class .= ' empr_demandes_col_progression';
	            break;
	        case 'linked_record':
	            $class .= ' empr_demandes_col_linked';
	            break;
	        case 'record':
	            break;
	        default:
	            if (!empty($name)) {
                    $custom_field_id = $this->get_custom_parameters_instance('demandes')->get_field_id_from_name($name);
    	            if (!empty($custom_field_id)) {
    	                $class .= ' empr_demandes_col_'.$name;
    	            }
	            }
	            break;
	    }
	    return $class;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
	    $class = "";
	    $onclick = "document.location=\"".static::get_controller_url_base()."&iddemande=".$object->id_demande."\"";
	    switch($property) {
	        case 'see':
	            $class .= 'empr_demandes_col2';
	            break;
	        case 'titre_demande':
	            $class .= 'empr_demandes_col_titre';
	            break;
	        case 'etat_demande':
	            $class .= 'empr_demandes_col_etat';
	            break;
	        case 'date_demande':
	            $class .= 'empr_demandes_col_date_dmde';
	            break;
	        case 'date_prevue':
	            $class .= 'empr_demandes_col_date_butoir';
	            break;
	        case 'attribution':
	            $class .= 'empr_demandes_col_user';
	            break;
	        case 'progression':
	            $class .= 'empr_demandes_col_progression';
	            $onclick = "";
	            break;
	        case 'linked_record':
	            $class .= 'empr_demandes_col_linked';
	            $onclick = "";
	            break;
	        case 'record':
	            $onclick = "";
	            break;
	        default:
	            if (!empty($property)) {
	                $custom_field_id = $this->get_custom_parameters_instance('demandes')->get_field_id_from_name($property);
    	            if (!empty($custom_field_id)) {
    	                $class .= 'empr_demandes_col_'.$property;
    	            }
	            }
	            break;
	    }
	    return array(
	        'class' => $class,
	        'onclick' => $onclick
	    );
	}
	
	protected function get_display_content_object_list($object, $indice) {
		if(static::class == 'list_opac_demandes_ui') {
		    // affichage en gras si nouveauté du côté des notes ou des actions
		    $object->dmde_read_opac = demandes::dmde_majRead($object->id_demande,"_opac");
		    if($object->dmde_read_opac == 1){
		        $style=" style='cursor: pointer; font-weight:bold'";
		    } else {
		        $style=" style='cursor: pointer'";
		    }
			$display = "
						<tr id='dmde".$object->id_demande."' class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\" ".$style.">";
			foreach ($this->columns as $column) {
				if($column['html']) {
					$display .= $this->get_display_cell_html_value($object, $column['html']);
				} else {
					$display .= $this->get_display_cell($object, $column['property']);
				}
			}
			$display .= "</tr>";
			//Le détail de l'action, contient les notes
			$display .="<tr id=\"action".$object->id_demande."Child\" style=\"display:none\">
					<td></td>
					<td colspan=\"".(count($this->columns))."\" id=\"action".$object->id_demande."ChildTd\">";
			
			$display .="</td>
					</tr>";
			return $display;
		} else {
			return parent::get_display_content_object_list($object, $indice);
		}
	}	
	
	public function get_demandes() {
		if(!isset($this->demandes)) {
			$this->demandes = new demandes();
		}
		return $this->demandes;
	}
	
	public function get_themes() {
		if(!isset($this->themes)) {
			$this->themes = new demandes_themes('demandes_theme','id_theme','libelle_theme',$this->filters['theme']);
		}
		return $this->themes;
	}
	
	public function get_types() {
		if(!isset($this->types)) {
			$this->types = new demandes_types('demandes_type','id_type','libelle_type',$this->filters['type']);
		}
		return $this->types;
	}
	
	public static function get_controller_url_base() {
	    global $base_path;
	    
	    return $base_path.'/empr.php?tab=request&lvl=list_dmde&sub=open_demande';
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=dmde';
	}
}