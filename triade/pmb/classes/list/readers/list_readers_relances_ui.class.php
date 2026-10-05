<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_relances_ui.class.php,v 1.38.2.1.2.1 2025/05/02 07:34:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/emprunteur.class.php");

class list_readers_relances_ui extends list_readers_ui {
	
    protected $amendes;
    protected $levels;
    
    protected $list_dates_sort;
    protected $list_dates_relance;
    protected $list_ids = [];
    
	protected function _get_query_base() {
	    $query = 'SELECT id_empr, expl_id, CONCAT(empr_nom, " ", empr_prenom) AS empr_nom_prenom FROM empr
				JOIN pret ON pret_idempr=id_empr AND pret_retour<CURDATE()
                JOIN exemplaires ON pret_idexpl=expl_id
				JOIN empr_categ ON empr.empr_categ=empr_categ.id_categ_empr
				JOIN empr_codestat ON empr.empr_codestat = empr_codestat.idcode
                JOIN docs_location ON empr.empr_location=docs_location.idlocation';
	    return $query;
	}
	
	protected function get_object_instance($row) {
		$emprunteur = new emprunteur($row->id_empr);
		$emprunteur->set_pret_idexpl($row->expl_id);
		$this->list_ids[] = $row->id_empr;
		return $emprunteur;
	}
	
	protected function add_object($row) {
		global $all_level;
	
		if(is_array($all_level) && count($all_level)){
			if (empty($all_level[$row->id_empr])) {
				$this->pager['nb_results']--;
				return;
			}
		}
	    if (!empty($all_level[$row->id_empr])) {
	        $this->levels[$row->id_empr] = $all_level[$row->id_empr];
	    } else {
	        $amende=$this->get_amende($row->id_empr);
	        $this->levels[$row->id_empr] = $amende->get_max_level();
	    }
	    if (($this->levels[$row->id_empr]["level_normal"])||($this->levels[$row->id_empr]["level_min"])) {
	        $this->objects[] = $this->get_object_instance($row);
	    } else {
	    	$this->pager['nb_results']--;
	    }
	}
	
	public function init_object_id() {
		global $id, $relance_solo;
		
		//Récupérons l'identifiant si on est sur une action (validation / impression / mail)
		if(empty($id) && !empty($relance_solo)) {
			$this->object_id = intval($relance_solo);
		} else {
			parent::init_object_id();
		}
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = pmb_mysql_result(pmb_mysql_query("SELECT count(*) FROM empr"), 0, 0); //Illimité
		$this->set_pager_in_session();
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
	    parent::init_available_filters();
	    $this->available_filters['main_fields']['last_dates'] = 'relance_last_dates';
	    $this->add_custom_fields_available_filters('pret', 'pret_idexpl');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
	    parent::init_available_columns();
	    $this->add_custom_fields_available_columns('pret', 'pret_idexpl');
	    //Ajout de colonnes HORS XML
	    $this->available_columns['main_fields']['number_late'] = 'relance_nb_retard';
	    $this->available_columns['main_fields']['last_level'] = 'relance_dernier_niveau';
	    $this->available_columns['main_fields']['last_date'] = 'relance_date_derniere';
	    $this->available_columns['main_fields']['printed'] = 'relance_imprime';
	    $this->available_columns['main_fields']['supposed_level'] = 'relance_niveau_suppose';
	}
	
	protected function init_no_sortable_columns() {
		parent::init_no_sortable_columns();
		$this->no_sortable_columns[] = 'next_levels';
		$this->no_sortable_columns[] = 'list_actions';
	}
	
	protected function add_column_next_levels() {
		global $msg;
		
		$this->columns[] = array(
				'property' => 'next_levels',
				'label' => $msg['relance_action_prochaine'],
				'html' => "",
                'exportable' => false
		);
	}
	
	protected function add_column_list_actions() {
	    global $msg;
	    
	    $this->columns[] = array(
	        'property' => 'list_actions',
	        'label' => $msg['relance_actions'],
	        'html' => "",
	        'exportable' => false
	    );
	}
	
	/**
	 * Fonction de callback
	 * {@inheritDoc}
	 * @see list_readers_ui::_compare_objects()
	 */
	protected function _compare_objects($a, $b, $index=0) {
	    if($this->applied_sort[$index]['by']) {
	        $sort_by = $this->applied_sort[$index]['by'];
			switch($sort_by) {
				case 'last_date':
					return $this->strcmp($this->levels[$a->id]["level_min_date_relance"], $this->levels[$b->id]["level_min_date_relance"]);
					break;
				case 'printed':
					return $this->intcmp($this->levels[$a->id]["printed"], $this->levels[$b->id]["printed"]);
					break;
				default :
				    return parent::_compare_objects($a, $b, $index);
					break;
			}
		}
	}
	
	protected function get_selection_query($type) {
	    $query = '';
	    switch ($type) {
	        case 'last_dates':
	            $query = 'SELECT DISTINCT date_relance as id, date_relance as label FROM pret WHERE date_relance <> "0000-00-00" ORDER BY label DESC';
	            break;
	        default:
	            $query = parent::get_selection_query($type);
	            break;
	    }
	    return $query;
	}
	
	protected function get_search_filter_last_level_validated() {
	    global $msg;
	    
	    $options = relance::get_filtered_levels($this->list_ids, 'level_min', $this->filters['last_level_validated']);
	    return $this->get_search_filter_multiple_selection('', 'last_level_validated', $msg['all_level_relances'], $options);
	}
	
	protected function get_search_filter_supposed_level() {
	    global $msg;
	    
	    $options = relance::get_filtered_levels($this->list_ids, 'level_normal', $this->filters['supposed_level']);
	    return $this->get_search_filter_multiple_selection('', 'supposed_level', $msg['all_level_relances'], $options);
	}
	
	protected function get_search_filter_last_dates() {
	    global $msg;
	    
	    return $this->get_search_filter_multiple_selection($this->get_selection_query('last_dates'), 'last_dates', $msg['print_relance_clear_all']);
	}
	
	protected function _get_object_property_number_late($object) {
		$query = "select count(pret_idexpl) as empr_nb from empr,pret where id_empr = ".$object->id." and pret_retour<CURDATE() and pret_idempr=id_empr group by empr.id_empr";
		$result = pmb_mysql_query($query);
		return pmb_mysql_result($result, 0, 0);
	}
	
	protected function _get_object_property_last_level($object) {
		return $this->levels[$object->id]["level_min"];
	}
	
	protected function _get_object_property_supposed_level($object) {
		return $this->levels[$object->id]["level_normal"];
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		global $mailretard_priorite_email;
		global $mailretard_priorite_email_2;
		global $mailretard_priorite_email_3;
		
		$content = '';
		switch($property) {
		    case 'empr_name':
		        $content .= "<a href='./circ.php?categ=pret&id_empr=".$object->id."'>".$object->nom." ".$object->prenom."</a>";
		        break;
		    case 'last_date':
		        $date_relance=$this->levels[$object->id]["level_min_date_relance"];
		        $list_dates = array();
		        if(!isset($this->list_dates_sort)) $this->list_dates_sort = array();
		        if(!isset($this->list_dates_relance)) $this->list_dates_relance = array();
		        $list_dates[$date_relance]=format_date($date_relance);
		        if ($this->levels[$object->id]["printed"]) {
		            $this->list_dates_relance[$date_relance]=$list_dates[$date_relance];
		            $dr=explode("-",$date_relance);
		            $this->list_dates_sort[$date_relance]=mktime(0,0,0,$dr[1],$dr[2],$dr[0]);
		        }
		        //Tri des dates
		        if (count($this->list_dates_sort)) {
		            arsort($this->list_dates_sort);
		        }
		        $content .= $list_dates[$date_relance];
		        break;
		    case 'printed':
		        $content .= ($this->levels[$object->id]["printed"] ? "X" : "");
		        break;
		    case 'next_levels':
		        $niveau_min = $this->levels[$object->id]["level_min"];
		        $niveau_normal = $this->levels[$object->id]["level_normal"];
		        $content .= relance::get_action($object->id,$niveau_min,$niveau_normal);
		        break;
		    case 'list_actions':
// 		        $script="envoi();";
// 		        $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_valid"]."' onClick=\"this.form.action = this.form.action + '#relance_empr_".$object->id."'; this.form.act.value='solo'; this.form.relance_solo.value='".$object->id."'; $script\"/>&nbsp;";
		        $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_valid"]."' onClick=\"document.forms['".$this->objects_type."_search_form'].action = document.forms['".$this->objects_type."_search_form'].action + '&act=solo&relance_solo=".$object->id."&action_".$object->id."='+document.getElementsByName('action_".$object->id."')[0].value+'#relance_empr_".$object->id."'; document.forms['".$this->objects_type."_search_form'].submit();\"/>&nbsp;";
		        
		        //Si mail de rappel affecté au responsable du groupe
		        $requete="select id_groupe,resp_groupe from groupe,empr_groupe where id_groupe=groupe_id and empr_id=".$object->id." and resp_groupe and mail_rappel limit 1";
		        $res=pmb_mysql_query($requete);
		        if(pmb_mysql_num_rows($res) > 0) {
		            $requete="select id_empr, empr_mail from empr where id_empr='".pmb_mysql_result($res, 0,1)."'";
		            $result=pmb_mysql_query($requete);
		            $has_mail = (pmb_mysql_result($result, 0,1) ? 1 : 0);
		        } else {
		            $has_mail = ($object->mail ? 1 : 0);
		        }
		        $niveau_min = $this->levels[$object->id]["level_min"];
		        if ($niveau_min) {
// 		            $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_print"]."' onClick=\"openPopUp('pdf.php?pdfdoc=lettre_retard&id_empr=".$object->id."&niveau=".$niveau_min."','lettre'); this.form.act.value='solo_print'; this.form.relance_solo.value='".$object->id."'; $script\"/>";
		            $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_print"]."' onClick=\"openPopUp('pdf.php?pdfdoc=lettre_retard&id_empr=".$object->id."&niveau=".$niveau_min."','lettre'); document.forms['".$this->objects_type."_search_form'].action = document.forms['".$this->objects_type."_search_form'].action + '&act=solo_print&relance_solo=".$object->id."#relance_empr_".$object->id."'; document.forms['".$this->objects_type."_search_form'].submit();\"/>";
		            $flag_mail=false;
		            if (((($mailretard_priorite_email==1)||($mailretard_priorite_email==2))&&($has_mail))&&(($niveau_min<3)||(($mailretard_priorite_email_3==1 || $mailretard_priorite_email_3==2) && $niveau_min>=3))) {
		                $flag_mail=true;
		                if (($niveau_min==2) && ($mailretard_priorite_email==1) && ($mailretard_priorite_email_2==1)) {
		                    //On force en lettre
		                    $flag_mail=false;
		                }
		            }
		            if ($flag_mail) {
// 		                $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_mail"]."' onClick=\"this.form.action = this.form.action + 'act=solo_mail&relance_solo=".$object->id."#relance_empr_".$object->id."'; this.form.act.value='solo_mail'; this.form.relance_solo.value='".$object->id."'; $script\"/>";
		                $content .= "<input type='button' class='bouton_small' value='".$msg["relance_row_mail"]."' onClick=\"document.forms['".$this->objects_type."_search_form'].action = document.forms['".$this->objects_type."_search_form'].action + '&act=solo_mail&relance_solo=".$object->id."#relance_empr_".$object->id."'; document.forms['".$this->objects_type."_search_form'].submit();\"/>";
		            }
		        }
		      break;
			default:
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		switch($property) {
			case 'last_date':
				$date_relance=$this->levels[$object->id]["level_min_date_relance"];
				$list_dates = array();
				if(!isset($this->list_dates_sort)) $this->list_dates_sort = array();
				if(!isset($this->list_dates_relance)) $this->list_dates_relance = array();
				$list_dates[$date_relance]=format_date($date_relance);
				if ($this->levels[$object->id]["printed"]) {
					$this->list_dates_relance[$date_relance]=$list_dates[$date_relance];
					$dr=explode("-",$date_relance);
					$this->list_dates_sort[$date_relance]=mktime(0,0,0,$dr[1],$dr[2],$dr[0]);
				}
				//Tri des dates
				if (count($this->list_dates_sort)) {
					arsort($this->list_dates_sort);
				}
				$grouped_label = $list_dates[$date_relance];
				break;
			case 'printed':
				$grouped_label = ($this->levels[$object->id]["printed"] ? $msg['40'] : $msg['39']);
				break;
			case 'next_levels':
				$grouped_label = $this->levels[$object->id]["level_normal"];
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	protected function get_display_html_content_selection() {
	    global $msg, $charset;
		return "<div class='center'><input type='checkbox' id='".$this->get_name_selection_objects()."_!!id!!' name='".$this->get_name_selection_objects()."[!!id!!]' class='list_ui_selection ".$this->objects_type."_selection' value='!!id!!' checked='checked' title='".htmlentities($msg['list_ui_selection_checkbox'], ENT_QUOTES, $charset)."'></div>";
	}
	
	protected function init_default_columns() {
	
	    $this->add_column_selection();
	    if(!empty(static::$used_filter_list_mode)) {
	        $displaycolumns=explode(",",static::$filter_list->displaycolumns);
	        //parcours des champs
	        foreach ($displaycolumns as $displaycolumn) {
	            if(substr($displaycolumn,0,2) == "#e") {
	                $parametres_perso = $this->get_custom_parameters_instance('empr');
	                $custom_name = $parametres_perso->get_field_name_from_id(substr($displaycolumn,2));
	                $label = $this->get_label_available_column($custom_name, 'custom_fields');
	                $this->add_column($custom_name, $label);
	            } else {
	                $this->add_column(static::$correspondence_columns_fields['main_fields'][$displaycolumn]);
	            }
	        }
	    } else {
	        $this->add_column('cb');
	        $this->add_column('empr_name');
	        $this->add_column('categ_libelle');
	        $this->add_column('codestat_libelle');
	        $this->add_column('groups');
	    }
		//Afficher les CP de prêt
	    if(!empty($this->custom_fields_available_columns)) {
			foreach ($this->custom_fields_available_columns as $name=>$column) {
			    if($column['type'] == 'pret') {
			        $parametres_perso = $this->get_custom_parameters_instance('pret');
			        $custom_id = $parametres_perso->get_field_id_from_name($name);
			        $this->add_column($name, $parametres_perso->t_fields[$custom_id]['TITRE']);
			    }
			}
	    }
		
		$this->add_column('number_late');
		$this->add_column('last_level');
		$this->add_column('last_date');
		$this->add_column('printed');
		$this->add_column('supposed_level');
		
		$this->add_column_next_levels();
		$this->add_column_list_actions();
	}
	
	protected function init_columns($columns=array()) {
	    $this->columns = array();
		if(count($this->selected_columns)) {
			parent::init_columns($columns);
			$this->add_column_next_levels();
			$this->add_column_list_actions();
		} else {
		    $this->init_default_columns();
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('empr_name', 'align', 'left');
		$this->set_setting_column('categ_libelle', 'align', 'left');
		$this->set_setting_column('codestat_libelle', 'align', 'left');
		$this->set_setting_column('groups', 'align', 'left');
		$this->set_setting_column('list_actions', 'align', 'left');
		$this->set_setting_column('last_level', 'datatype', 'integer');
		$this->set_setting_column('supposed_level', 'datatype', 'integer');
		$this->set_setting_column('number_late', 'datatype', 'integer');
	}
	
	protected function _get_query_order() {
		$this->applied_sort_type = 'SQL';
	    return " group by id_empr ".parent::_get_query_order();
	}
	
	protected function get_display_query_human($humans) {
		global $msg;
		
		return "<div class='align_left'><br />".implode(' '.$msg['search_and'].' ', $humans)."<br /><br /></div>";
	}
	
	protected function get_selection_mode() {
	    return 'button';
	}
	
	protected function get_link_action($action, $msg_confirm='') {
	    return array(
	        'href' => static::get_controller_url_base()."&act=".$action,
	        'confirm' => $msg_confirm
	    );
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		$this->add_selection_action('valid_all', $msg['relance_valid_all'], 'tick.gif', $this->get_link_action('valid'));
		$this->add_selection_action('print_nonprinted', $msg['relance_print_nonprinted'], 'print.gif', $this->get_link_action('print'));
		$this->add_selection_action('reprint', $msg['relance_reprint'], 'print.gif', $this->get_link_action('reprint'));
// 		$this->add_selection_action('print_letters', $msg['relance_print_letters'], 'print.gif', $this->get_link_action('print_letters'));
// 		$this->add_selection_action('print_letters_mails', $msg['relance_print_letters_mails'], 'print.gif', $this->get_link_action('print_letters_mails'));
// 		$this->add_selection_action('export', $msg['relance_export'], 'tableur.gif', $this->get_link_action('export'));
		$this->add_selection_action('export_csv', $msg['relance_export_csv'], 'tableur.gif', $this->get_link_action('export_csv'));
	}
	
	protected function get_name_selected_objects() {
	    return "empr";
	}
	
	protected function get_inheritance_nodes_selected_objects_form($action=array()) {
		return "
			selection.forEach(function(selected_option) {
				var next_actions_hidden = domConstruct.create('input', {
					type : 'hidden',
					name : 'action_'+selected_option,
					value : dom.byId('action_'+selected_option).value
				});
				domConstruct.place(next_actions_hidden, selected_objects_form);
			});
		";
	}
	
	protected function get_display_others_actions() {
	    global $msg;
	    
	    $display = '';
	    if (isset($this->list_dates_relance) && count($this->list_dates_relance)) {
	        $display .= "
		<div id='list_ui_others_actions' class='list_ui_others_actions ".$this->objects_type."_others_actions'>
		<span class='right list_ui_other_action_relance_clear ".$this->objects_type."_other_action_relance_clear'>
			<input type='button' id='".$this->objects_type."_other_action_relance_clear_link' value='".addslashes($msg["print_relance_clear"])."' class='bouton'/>&nbsp;
            <select id='clear_date' name='clear_date'>
                <option value=''>".$msg["print_relance_clear_all"]."</option>";
	        foreach ($this->list_dates_sort as $val=>$stamp) {
	            $lib=$this->list_dates_relance[$val];
	            $display .= "<option value='$val'>".$lib."</option>\n";
	        }
	        $display .= "
            </select>
        </span>
		<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){
			on(dom.byId('".$this->objects_type."_other_action_relance_clear_link'), 'click', function() {
				var selected_clear_date = dom.byId('clear_date').options[dom.byId('clear_date').selectedIndex].value;
				var selected_clear_formatted_date = dom.byId('clear_date').options[dom.byId('clear_date').selectedIndex].text;
                if (confirm('".sprintf(addslashes($msg["confirm_print_relance_clear"]),"'+selected_clear_formatted_date+' ?'").")) {
                    var input_act = domConstruct.create('input', {
    					type : 'hidden',
    					id : 'act',
    					name : 'act',
    					value : 'raz_printed'
    				});
                    domConstruct.place(input_act, dom.byId('".$this->objects_type."_search_form'));
                    var input_printed_cd = domConstruct.create('input', {
    					type : 'hidden',
    					id : 'printed_cd',
    					name : 'printed_cd',
    					value : selected_clear_date
    				});
    				domConstruct.place(input_printed_cd, dom.byId('".$this->objects_type."_search_form'));
                    var input_relance_solo = domConstruct.create('input', {
    					type : 'hidden',
    					id : 'relance_solo',
    					name : 'relance_solo',
    					value : ''
    				});
    				domConstruct.place(input_relance_solo, dom.byId('".$this->objects_type."_search_form'));
                }
				dom.byId('".$this->objects_type."_search_form').submit();
			});
		});
		</script>";
	    }
	    return $display;
	}
		
	protected static function init_correspondence_filters_fields() {
	    parent::init_correspondence_filters_fields();
	    static::$correspondence_filters_fields['main_fields']['2'] = 'last_level_validated';
	    static::$correspondence_filters_fields['main_fields']['3'] = 'supposed_level';
	    static::$correspondence_filters_fields['main_fields']['17'] = 'last_dates';
	}
	
	protected static function init_correspondence_columns_fields() {
	    parent::init_correspondence_columns_fields();
	    static::$correspondence_columns_fields['main_fields']['2'] = 'last_level';
	    static::$correspondence_columns_fields['main_fields']['3'] = 'supposed_level';
	}
	
	public function get_amende($id_empr) {
	    if(!isset($this->amendes[$id_empr])) {
	        $this->amendes[$id_empr] = new amende($id_empr);
	    }
	    return $this->amendes[$id_empr];
	}
}