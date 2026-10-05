<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_abon_ui.class.php,v 1.22 2023/03/24 08:22:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/bannette_abon.class.php");
require_once($class_path."/expl.class.php");

class list_bannettes_abon_ui extends list_bannettes_ui {
	
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
		return 0;
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
		$this->available_columns['main_fields']['name'] = 'dsi_bannette_gerer_nom_liste';
		$this->available_columns['main_fields']['send_last_date'] = 'dsi_bannette_gerer_date';
		$this->available_columns['main_fields']['number_records'] = 'dsi_bannette_gerer_nb_notices';
		$this->available_columns['main_fields']['periodicite'] = 'dsi_bannette_gerer_periodicite';
	}
	
	protected function init_default_columns() {
		$this->add_column('subscribed');
		$this->add_column('name');
		$this->add_column('send_last_date');
		$this->add_column('number_records');
		$this->add_column('periodicite');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('query', 'human', false);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = pmb_mysql_result(pmb_mysql_query("SELECT count(*) FROM bannettes"), 0, 0); //Illimité;
		$this->set_pager_in_session();
	}
	
	/**
	 * Construction dynamique des cellules du header
	 * @param string $name
	 */
	protected function _get_cell_header($name, $label = '') {
		global $msg, $charset;
		
		switch ($name) {
			case 'name':
				return "<th class='align_left' style='vertical-align:middle'>".$this->_get_label_cell_header($label)."</th>";
			case 'subscribed':
				return "<th class='center' style='vertical-align:middle'>
							<i class='fa fa-plus-square' id='".$this->objects_type."_list_cell_header_square_plus' onclick='".$this->objects_type."_selection_all(document.".$this->get_form_name().", this);' style='cursor:pointer;' title='".htmlentities($msg['tout_cocher_checkbox'], ENT_QUOTES, $charset)."'></i>
							&nbsp;
							<i class='fa fa-minus-square' id='".$this->objects_type."_list_cell_header_square_minus' onclick='".$this->objects_type."_unselection_all(document.".$this->get_form_name().", this);' style='cursor:pointer;' title='".htmlentities($msg['tout_decocher_checkbox'], ENT_QUOTES, $charset)."'></i>
						</th>";
			default:
				return "<th class='center' style='vertical-align:middle'>".$this->_get_label_cell_header($label)."</th>";
		}
	}
	
	protected function get_link_to_bannette($id_bannette, $proprio_bannette) {
		if($proprio_bannette) {
			return "./dsi.php?categ=bannettes&sub=abo&id_bannette=".$id_bannette."&suite=modif&id_empr=".$proprio_bannette;
		} else {
			return "./dsi.php?categ=bannettes&sub=pro&id_bannette=".$id_bannette."&suite=acces";
		}
	}
	
	protected function get_cell_content($object, $property) {
	    global $charset;
		
		$content = '';
		switch($property) {
			case 'subscribed':
				$content .= "<input class='".$this->objects_type."_selection' id_bannette='".$object->id_bannette."' type='checkbox' id='".$this->objects_type."_selection_".$object->id_bannette."' name='".$this->objects_type."_selection[".$object->id_bannette."]' value='".$object->id_bannette."' " .(!$this->filters['proprio_bannette'] && $object->is_subscribed(static::$id_empr) ? "checked='checked'" : ""). " />";
				break;
			case 'name':
				// Construction de l'affichage de l'info bulle de la requette
			    $java_comment = '';
			    $zoom_comment = '';
				$requete = "select * from bannette_equation, equations where num_equation = id_equation and num_bannette = ".$object->id_bannette;
				$resultat = pmb_mysql_query($requete);
				if (($r = pmb_mysql_fetch_object($resultat))) {
					$recherche = $r->requete;
					$equ = new equation ($r->num_equation);
					if (!isset($this->search) || !is_object($this->search)) $this->search = new search();
					$this->search->unserialize_search($equ->requete);
					$recherche = $this->search->make_human_query();
					$zoom_comment = "<div id='zoom_comment" . $object->id_bannette . "' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
					$zoom_comment.= $recherche;
					$zoom_comment.= "</div>";
					$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment" . $object->id_bannette . "'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment" . $object->id_bannette . "'); z.style.display='none'; \"";
				}
				$content .= "<a href = \"" . str_replace("!!id_bannette!!", $object->id_bannette, $this->get_link_to_bannette($object->id_bannette, $object->proprio_bannette)) . "\" $java_comment >";
				if($object->comment_public) {
				    $content .= htmlentities($object->comment_public, ENT_QUOTES, $charset);
				} else {
				    $content .= htmlentities($object->nom_bannette, ENT_QUOTES, $charset);
				}
				$content .= "</a>";
				$content .= $zoom_comment;
				if (in_array($this->get_empr_categ(), $object->categorie_lecteurs)) {
					$content.= " / ".$this->get_empr_cat_l();
				}
				$bannette_abon = new bannette_abon($object->id_bannette, static::$id_empr);
				foreach($bannette_abon->get_groups() as $groupe_id=>$group_label) {
					if (in_array($groupe_id, $object->groupe_lecteurs)) {
						$content.= " / ".$group_label;
					}
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array();	
	}
	
	public static function set_id_empr($id_empr) {
		static::$id_empr = intval($id_empr);
	}
	
	public static function set_empr_cb($empr_cb) {
		static::$empr_cb = $empr_cb;
	}
	
	public static function get_controller_url_base() {
	    global $base_path, $categ, $form_cb;
	    
	    return $base_path.'/circ.php?categ='.$categ.'&form_cb='.$form_cb;
	}
}