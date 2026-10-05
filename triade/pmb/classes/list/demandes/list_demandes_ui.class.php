<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_demandes_ui.class.php,v 1.26.2.2.2.5 2025/05/22 12:29:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/demandes_types.class.php");

class list_demandes_ui extends list_ui {
	
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
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'theme_demande':
	            return 'libelle_theme';
	        case 'type_demande':
	            return 'libelle_type';
	        case 'titre_demande':
	        case 'date_demande':
	        case 'date_prevue':
	        case 'deadline_demande':
	        case 'progression':
	            return $sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	protected function _get_query_order() {
	    if (static::class == 'list_demandes_ui') {
	        return ' GROUP BY id_demande '.parent::_get_query_order();
	    }
	    return parent::_get_query_order();
	}
	
	protected function get_object_instance($row) {
		return new demandes($row->id_demande);
	}
		
	protected function get_form_title() {
		global $msg;
		
		return $msg['demandes_search_filtre_form'];
	}
	
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
		global $pmb_lecteurs_localises;
		
		$this->add_selected_filter('user_input');
		$this->add_empty_selected_filter();
		$this->add_empty_selected_filter();
		$this->add_selected_filter('demandeur');
		$this->add_selected_filter('state');
		$this->add_selected_filter('date');
		$this->add_selected_filter('affectation');
		$this->add_selected_filter('theme');
		$this->add_selected_filter('type');
		if($pmb_lecteurs_localises) {
			$this->add_selected_filter('location');
		}
		if(count($this->available_filters['custom_fields'])) {
			foreach ($this->available_filters['custom_fields'] as $property=>$label) {
				$this->add_selected_filter($property, $label);
			}
		}
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date_demande', 'desc');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'see' => '210',
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
						'record' => 'demandes_notice'
				)
		);
		
		$this->available_columns['custom_fields'] = array();
		$this->add_custom_fields_available_columns('demandes', 'id_demande');
	}
	
	protected function add_column_more_details() {
	    global $msg, $charset;
	    
		$this->columns[] = array(
				'property' => '',
				'label' => "",
                'html' => "<img onclick=\"expand_action('action!!id!!','!!id!!', true); return false;\" title='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['plus_detail'], ENT_QUOTES, $charset)."' id='action!!id!!Img' name='imEx' class='img_plus' src='".get_url_icon('plus.gif')."'>",
				'exportable' => false
		);
	}
	
	protected function get_name_selection_objects() {
	    return "chk";
	}
	
	protected function init_default_columns() {
		$this->add_column_more_details();
		$this->add_column('see');
		$this->add_column('theme_demande');
		$this->add_column('type_demande');
		$this->add_column('titre_demande');
		$this->add_column('etat_demande');
		$this->add_column('date_demande');
		$this->add_column('date_prevue');
		$this->add_column('deadline_demande');
		$this->add_column('demandeur');
		$this->add_column('attribution');
		$this->add_column('progression');
		if(count($this->available_columns['custom_fields'])) {
			foreach ($this->available_columns['custom_fields'] as $property=>$label) {
				$this->add_column($property, $label);
			}
		}
		$this->add_column('record');
		$this->add_column_selection();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'unfolded_filters', true);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('progression', 'align', 'left');
		$this->set_setting_column('date_demande', 'datatype', 'date');
		$this->set_setting_column('date_prevue', 'datatype', 'date');
		$this->set_setting_column('deadline_demande', 'datatype', 'date');
	}
	
	public function get_display_list() {
	    global $javascript_path, $msg;
		
		$display = parent::get_display_list();
		$display .= "
			<script src='".$javascript_path."/dynamic_element.js' type='text/javascript'></script>
			<script src='".$javascript_path."/demandes_form.js' type='text/javascript'></script>
			<script type='text/javascript'>
				var msg_demandes_note_confirm_demande_end='".addslashes($msg['demandes_note_confirm_demande_end'])."'; 
				var msg_demandes_actions_nocheck='".addslashes($msg['demandes_actions_nocheck'])."'; 
				var msg_demandes_confirm_suppr = '".addslashes($msg['demandes_confirm_suppr'])."';
				var msg_demandes_note_confirm_suppr = '".addslashes($msg['demandes_note_confirm_suppr'])."';
			</script>
			<script type='text/javascript'>
				function alert_progressiondemande(){
					alert(\"".$msg['demandes_progres_ko']."\");
				}
			</script>
			<script>parse_dynamic_elts();</script>
		";
		return $display;	
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["demandes_liste_vide"], ENT_QUOTES, $charset);
	}
	
	protected function init_default_selection_actions() {
	    global $msg, $idetat, $iduser;
	    
	    parent::init_default_selection_actions();
	    //A-t-on choisi une valeur en provenance du formulaire
	    if(isset($idetat)) {
	        $this->filters['state'] = intval($idetat);
	    }
	    //afficher la liste des boutons de changement d'état
	    if($this->filters['state']){
	        $states = $this->get_demandes()->workflow->getStateList($this->filters['state']);
	        for($i=0;$i<count($states);$i++){
	            $state_link = array(
	                'href' => static::get_controller_url_base()."&act=change_state&state=".$states[$i]['id'],
	                'confirm' => $msg['demandes_change_checked_states']." ?"
	            );
	            $this->selection_actions[] = $this->get_selection_action('state_'.$states[$i]['id'], $states[$i]['comment'], '', $state_link);
	        }
	        
	    }
	    // A-t-on choisi une valeur en provenance du formulaire
	    if(isset($iduser)) {
	        if (is_array($iduser) && count($iduser)) {
	            if (count($iduser) == 1) {
	                $this->filters['affectation'] = intval($iduser[0]);
	            } else {
	                $this->filters['affectation'] = 0;
	            }
	        } else {
	            $this->filters['affectation'] = intval($iduser);
	        }
	    }
	    if($this->filters['affectation'] == -1){
	        $affectation_link = array(
	            'href' => static::get_controller_url_base()."&act=affecter",
	        );
	        // 				$affectation_btn = "<input type='submit' class='bouton' name='affect_btn' id='affect_btn' onclick='this.form.act.value=\"affecter\";return verifChk();' value='".htmlentities($msg['demandes_attribution_checked'],ENT_QUOTES,$charset)."' />&nbsp;".$this->getUsersSelector();
	        $this->selection_actions[] = $this->get_selection_action('affectation', $msg['demandes_attribution_checked'], '', $affectation_link);
	    }
	    
	    $delete_link = array(
    	     'href' => static::get_controller_url_base()."&act=suppr_noti",
    	     'confirm' => $msg['demandes_confirm_suppr']
	     );
	     $this->selection_actions[] = $this->get_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link);
	}
	
	protected function get_display_selection_action($action) {
		$display = parent::get_display_selection_action($action);
		if($action['name'] == 'affectation') {
			$display .= "&nbsp;<span style='margin-right:25px'>".$this->get_demandes()->getUsersSelector()."</span>";
		}
		return $display;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function get_name_selected_objects() {
		return "chk";
	}
	
	protected function get_inheritance_nodes_selected_objects_form($action=array()) {
		return "
            var iduser = 0;
            if(dom.byId('iduser')) {
                iduser = dom.byId('iduser').value;
            }
			var affectation_iduser_hidden = domConstruct.create('input', {
				type : 'hidden',
				name : 'iduser',
				value : iduser
			});
			domConstruct.place(affectation_iduser_hidden, selected_objects_form);
		";
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
		} else {
		    $this->set_filter_from_form('user_input');
		}
		if(isset($idetat)) {
			$this->filters['state'] = intval($idetat);
		} else {
// 		    $this->set_filter_from_form('state', 'integer');
		}
		if(isset($idempr)) {
		    $idempr = intval($idempr);
			$this->filters['demandeur'] = $idempr;
			if ($idempr) {
			    $this->filters['location'] = emprunteur::get_location($idempr)->id;
			}
		}
		if(isset($date_debut) || isset($date_fin)) {
    		if(isset($date_debut)) {
    			$this->filters['date_start'] = $date_debut;
    		}
    		if(isset($date_fin)) {
    			$this->filters['date_end'] = $date_fin;
    		}
		} else {
		    $this->set_filter_from_form('date_start');
		    $this->set_filter_from_form('date_end');
		}
		if(isset($iduser)) {
		    if (is_array($iduser)) {
		        if (count($iduser) == 1) {
                    $this->filters['affectation'] = intval($iduser[0]);
		        } else {
		            $this->filters['affectation'] = 0;
		        }
		    } else {
		        $this->filters['affectation'] = intval($iduser);
		    }
		}
		if(isset($id_type)) {
			$this->filters['type'] = intval($id_type);
		} else {
		    $this->set_filter_from_form('type', 'integer');
		}
		if(isset($id_theme)) {
			$this->filters['theme'] = intval($id_theme);
		} else {
		    $this->set_filter_from_form('theme', 'integer');
		}
		if(isset($dmde_loc)) {
			$this->filters['location'] = intval($dmde_loc);
		} else {
		    $this->set_filter_from_form('location', 'integer');
		}
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
	    $query = '';
	    switch ($type) {
	        case 'theme':
	            $query = 'select id_theme as id, libelle_theme as label from demandes_theme order by label';
	            break;
	        case 'type':
	            $query = 'select id_type as id, libelle_type as label from demandes_type order by label';
	            break;
	        case 'location':
	            $query = 'select idlocation as id, location_libelle as label from docs_location order by label';
	            break;
	    }
	    return $query;
	}
	
	protected function get_search_filter_user_input() {
		return $this->get_search_filter_simple_text('user_input');
	}
	
	protected function get_search_filter_demandeur() {
		global $charset;
		global $pmb_lecteurs_localises;
		
		return "
			<input type='hidden' id='idempr' name='idempr' value='".$this->filters['demandeur']."' />
			<input type='text' id='empr_txt' name='empr_txt' class='saisie-20emr' value='".htmlentities(emprunteur::get_name($this->filters['demandeur'], 1), ENT_QUOTES, $charset)."' completion='empr' autfield='idempr' autocomplete='off' tabindex='1'/>
			<input type='button' class='bouton_small' value='...' onclick=\"openPopUp('./select.php?what=origine&caller=".$this->get_form_name()."&param1=idempr&param2=empr_txt&deb_rech='+".pmb_escape()."(this.form.empr_txt.value)+'&filtre=ONLY_EMPR&callback=filtrer_user".($pmb_lecteurs_localises ? "&empr_loca='+this.form.dmde_loc.value": "'").", 'selector')\" />
			<input type='button' class='bouton_small' value='X' onclick=\"document.getElementById('idempr').value=0;document.getElementById('empr_txt').value='';\" />
		";
	}
	
	protected function get_search_filter_state() {
		return $this->get_demandes()->getStateSelector($this->filters['state'],'',true);
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function get_search_filter_affectation() {
		return $this->get_demandes()->getUsersSelector('',true,false,true);
	}
	
	protected function get_search_filter_theme() {
	    global $msg;
	    return $this->get_search_filter_simple_selection($this->get_selection_query('theme'), 'theme', $msg['list_simple_all']);
	}
	
	protected function get_search_filter_type() {
        global $msg;
	    return $this->get_search_filter_simple_selection($this->get_selection_query('type'), 'type', $msg['list_simple_all']);
	}
	
	protected function get_search_filter_location() {
		global $msg;
		
		return $this->get_search_filter_simple_selection($this->get_selection_query('location'), 'location', $msg['demandes_localisation_all']);
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_display_search_form() {
		$this->is_displayed_add_filters_block = false;
		$display_search_form = parent::get_display_search_form();
		return $display_search_form;
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
				if($object->dmde_read_gestion == 1){
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par onclick=\"change_read_dmde('dmde".$dmde->id_demande."','$dmde->id_demande', true); return false;\"
					$content .= "<img title=\"\" onclick=\"document.location='".$object->get_gestion_link()."'\" id=\"dmde".$object->id_demande."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' style='display:none' />
								<img title=\"" . $msg['demandes_new']. "\" onclick=\"document.location='".$object->get_gestion_link()."'\" id=\"dmde".$object->id_demande."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."' />";
				} else {
					// remplacer $action le jour où on décide d'activer la modif d'état manuellement par onclick=\"change_read_dmde('dmde".$dmde->id_demande."','$dmde->id_demande', true); return false;\"
					$content .= "<img title=\"\" onclick=\"document.location='".$object->get_gestion_link()."'\" id=\"dmde".$object->id_demande."Img1\" class=\"img_plus\" src='".get_url_icon('notification_empty.png')."' />
								<img title=\"" . $msg['demandes_new']. "\" onclick=\"document.location='".$object->get_gestion_link()."'\" id=\"dmde".$object->id_demande."Img2\" class=\"img_plus\" src='".get_url_icon('notification_new.png')."' style='display:none' />";
				}
				break;
			case 'progression':
				$content .= "
					<span id='progressiondemande_".$object->id_demande."'  dynamics='demandes,progressiondemande' dynamics_params='img/img' >
						<img src='".get_url_icon('jauge.png')."' height='15px' width=\"".$object->progression."%\" title='".$object->progression."%' alt='".$object->progression."%' />
					</span>";
				break;
			case 'record':
				if($object->get_num_linked_notice()) {
					$content .= "<a href='".notice::get_permalink($object->get_num_linked_notice())."'><img style='border:0px' class='align_middle' src='".get_url_icon('notice.gif')."' alt='".htmlentities($msg['demandes_see_notice'],ENT_QUOTES,$charset)."' title='".htmlentities($msg['demandes_see_notice'],ENT_QUOTES,$charset)."'></a>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "window.location=\"".$object->get_gestion_link()."\""
		);
	}
	
	protected function get_display_content_object_list($object, $indice) {
		if(static::class == 'list_demandes_ui') {
			// affichage en gras si nouveauté du côté des notes ou des actions
			$object->dmde_read_gestion = demandes::dmde_majRead($object->id_demande,"_gestion");
			if($object->dmde_read_gestion == 1){
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
	
	protected function _get_query_human() {
		global $msg;
		
		return "<h3>".$msg['demandes_liste']." (".$this->pager['nb_results'].")</h3>";
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
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg['demandes_new'], ['location' => static::get_controller_url_base()."&act=new"], ['id' => 'new_dmd', 'name' => 'new_dmd']);
	}
	
	protected function at_least_one_action() {
	    return true;
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=dmde';
	}
}