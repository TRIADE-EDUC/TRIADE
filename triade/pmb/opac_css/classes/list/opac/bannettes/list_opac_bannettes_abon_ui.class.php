<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_bannettes_abon_ui.class.php,v 1.2 2024/04/23 12:24:01 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_bannettes_abon_ui extends list_opac_bannettes_ui {
	
	protected static $id_empr;
	
	protected static $empr_cb;
	
	protected $search;
		
	protected function get_empr_categ() {
		if(!empty(static::$id_empr)) {
			$query = "select empr_categ from empr join empr_categ on empr.empr_categ = empr_categ.id_categ_empr where id_empr =".static::$id_empr;
			$result = pmb_mysql_query($query);
			return pmb_mysql_result($result, 0, 'empr_categ');
		}
		return 0;
	}
	
	protected function get_empr_cat_l() {
		if(!empty(static::$id_empr)) {
			$query = "select libelle from empr join empr_categ on empr.empr_categ = empr_categ.id_categ_empr where id_empr =".static::$id_empr;
			$result = pmb_mysql_query($query);
			return pmb_mysql_result($result, 0, 'libelle');
		}
		return '';
	}
	
	protected function get_access_liste_id() {
		$access_liste_id = array();
		if(!empty(static::$id_empr)) {
			$query = "SELECT empr_categ_num_bannette FROM bannette_empr_categs WHERE empr_categ_num_categ=".$this->get_empr_categ();
			$result = pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_object($result)) {
				$access_liste_id[] = $row->empr_categ_num_bannette;
			}
			$query = "select groupe_id from empr_groupe where empr_id=".static::$id_empr." AND groupe_id != 0";//En création de lecteur une entrée avec groupe_id = 0 est créée ...
			$result = pmb_mysql_query($query);
			$groups = array();
			while ($row=pmb_mysql_fetch_object($result)) {
				$groups[] = $row->groupe_id;
			}
			if (count($groups)) {
				$query = "SELECT empr_groupe_num_bannette FROM bannette_empr_groupes WHERE empr_groupe_num_groupe IN (".implode(",",$groups).")";
				$result = pmb_mysql_query($query);
				while ($row = pmb_mysql_fetch_object($result)) {
					$access_liste_id[] = $row->empr_groupe_num_bannette;
				}
			}
		}
		if (count($access_liste_id)) {
			$access_liste_id = array_unique($access_liste_id);
				
		} else {
			$access_liste_id[] = 0;
		}
		return $access_liste_id;
	}
	
	protected function get_form_title() {
		return '';
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields']['subscribed'] = 'dsi_bannette_gerer_abonn';
	}
	
	protected function init_default_columns() {
	    global $opac_private_bannette_date_used_to_calc;
	    
	    $this->add_column('subscribed');
	    $this->add_column('name');
	    $this->add_column('send_last_date');
	    $this->add_column('number_records');
	    $this->add_column('periodicity');
	    if($opac_private_bannette_date_used_to_calc == 2) {
	        $this->add_column('date_used_to_calc');
	    }
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
	}
	
	protected function init_default_applied_group() {
	    $this->applied_group = array(0 => 'nom_classement_opac');
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	public function get_display_search_form() {
	    return '';
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;

		$content = '';
		switch($property) {
			case 'name':
			    $link_to_bannette = "./empr.php?lvl=bannette&id_bannette=!!id_bannette!!";
		        // Construction de l'affichage de l'info bulle de la requette
		        $recherche = (!empty($object->equation_name) ? $object->equation_name : get_bannette_human_query($object->id_bannette));
		        if ($recherche) {
		            $zoom_comment = "<div id='zoom_comment".$object->id_bannette."' role='tooltip' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
					$zoom_comment .= htmlentities($msg['list_ui_bannette_title_tooltip'],ENT_QUOTES,$charset) . $recherche;
		            $zoom_comment .= "</div>";
		            $java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$object->id_bannette."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$object->id_bannette."'); z.style.display='none'; \"" ;
					$zoom_tooltip = " aria-describedby='zoom_comment".$object->id_bannette ."'" ;
				} else {
		            $zoom_comment = "";
		            $java_comment = "";
		        }
		        $content .= "<a href=\"".str_replace("!!id_bannette!!", $object->id_bannette, $link_to_bannette)."\" $java_comment $zoom_tooltip >";
			    $content .= "<span class='visually-hidden'>". htmlentities($msg['link_to_bannette_title'],ENT_QUOTES,$charset). "</span>" . $object->get_render_comment_public();
		        $content .= "</a>";
		        $content .= $zoom_comment;
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
	        case 'name':
	            $class .= ' bannette_nom_liste';
	            break;
	        case 'send_last_date':
	            $class .= ' bannette_date';
	            break;
	        case 'number_records':
	            $class .= ' bannette_nb_notices';
	            break;
	        case 'periodicity':
	            $class .= ' bannette_periodicite';
	            break;
	        case 'date_used_to_calc':
	            $class .= ' bannette_date_used_to_calc';
	            break;
	        case 'actions':
	            $class .= ' bannette_edit';
	            break;
	    }
	    return $class;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		global $opac_rgaa_active;

	    if(!empty($this->selected_columns[$property])) {
			if($opac_rgaa_active){
				$attributes = array(
					'data-column-name' => $this->_get_label_cell_header($this->selected_columns[$property])
				);
			}else{
				$attributes = array(
					'column_name' => $this->_get_label_cell_header($this->selected_columns[$property])
				);
			}
	       
	    } else {
	        $attributes = array();
	    }
	    $attributes['style'] = 'vertical-align:top;';
	    $class="";
	    switch($property) {
	        case 'name':
	            $class = 'bannette_nom_liste';
	            break;
	        case 'send_last_date':
	            $class = 'bannette_date';
	            break;
	        case 'number_records':
	            $class = 'bannette_nb_notices';
	            break;
	        case 'periodicity':
	            $class = 'bannette_periodicite';
	            break;
	        case 'date_used_to_calc':
	            $class = 'bannette_date_used_to_calc';
	            break;
	        case 'actions':
	            $class = 'bannette_edit';
	            break;
	    }
	    $attributes['class'] = $class;
	    return $attributes;
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
	
	public static function set_id_empr($id_empr) {
		static::$id_empr = intval($id_empr);
	}
	
	public static function set_empr_cb($empr_cb) {
		static::$empr_cb = $empr_cb;
	}
	
	public static function get_controller_url_base() {
	    global $base_path;
	    
	    return $base_path.'/empr.php?lvl=bannette';
	}
}