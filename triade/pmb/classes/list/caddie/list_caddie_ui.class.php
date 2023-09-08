<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_caddie_ui.class.php,v 1.7 2019-05-17 10:59:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/caddie/list_caddie_root_ui.class.php");

class list_caddie_ui extends list_caddie_root_ui {
		
	protected $instance_notice_tpl_gen;
	
	protected $flag_notice_id;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_caddie_content() {
		$query = "SELECT caddie_content.object_id FROM caddie_content";
		switch (static::$object_type) {
			case 'NOTI' :
				$query .= " left join notices on object_id=notice_id " ;
				break;
			case 'EXPL' :
				$query .= " left join exemplaires on object_id=expl_id " ;
				break;
			case 'BULL' :
				$query .= " left join bulletins on object_id=bulletin_id " ;
				break;
		}
		$query .= $this->_get_query_filters_caddie_content();
		$query .= " AND caddie_id='".static::$id_caddie."'";
		return $query;
	}
	
	protected function _get_query_base() {
		switch (static::$object_type) {
			case 'NOTI':
				$query = "SELECT n1.notice_id as id, n1.*, series.*, p1.*, p2.*, collections.*, sub_collections.*, indexint.*
					FROM notices n1 
					left join series on serie_id=n1.tparent_id
					left join publishers p1 on p1.ed_id=n1.ed1_id
					left join publishers p2 on p2.ed_id=n1.ed2_id
					left join collections on n1.coll_id=collection_id
					left join sub_collections on n1.subcoll_id=sub_coll_id
					left join indexint on n1.indexint=indexint_id 
					WHERE n1.notice_id IN (".$this->_get_query_caddie_content().")";
				break;
			case 'EXPL':
				$query = "SELECT e.expl_id as id, e.*, t.*, s.*, st.*, l.*, stat.*, n.*, series.*, p1.*, collections.*, sub_collections.*, p2.*, indexint.*, b.*
					FROM exemplaires e
					, docs_type t
					, docs_section s
					, docs_statut st
					, docs_location l
					, docs_codestat stat
					, notices n left join series on serie_id=n.tparent_id
					left join publishers p1 on p1.ed_id=n.ed1_id
					left join publishers p2 on p2.ed_id=n.ed2_id
					left join collections on n.coll_id=collection_id
					left join sub_collections on n.subcoll_id=sub_coll_id
					left join indexint on n.indexint=indexint_id
					left join bulletins as b on b.bulletin_notice=.n.notice_id 
					WHERE e.expl_id IN (".$this->_get_query_caddie_content().")
					AND e.expl_typdoc=t.idtyp_doc
					AND e.expl_section=s.idsection
					AND e.expl_statut=st.idstatut
					AND e.expl_location=l.idlocation
					AND e.expl_codestat=stat.idcode
					AND ((e.expl_notice=n.notice_id AND e.expl_notice <> 0) )";
				// OR (e.expl_bulletin=b.bulletin_id AND e.expl_bulletin <> 0)
				break;
			case 'BULL':
				$query = "select bulletins.bulletin_id as id, bulletins.* from bulletins where bulletin_id IN (".$this->_get_query_caddie_content().") ";
				break;
		}
		return $query;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
	
		$notice_tpl = $this->objects_type.'_notice_tpl';
		global ${$notice_tpl};
		if(isset(${$notice_tpl})) {
			$this->filters['notice_tpl'] = ${$notice_tpl};
		}
		parent::set_filters_from_form();
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $msg;
	
		$search_filters_form = parent::get_search_filters_form();
		if(!isset($this->filters['notice_tpl'])) $this->filters['notice_tpl'] = 0;
		$sel_notice_tpl=notice_tpl_gen::gen_tpl_select($this->objects_type."_notice_tpl",$this->filters['notice_tpl'],'',1,1);
		$suppl = "";
		if($sel_notice_tpl) {
			$sel_notice_tpl= "
				<div class='row'>
					<div class='colonne3'>
						<div class='row'>
							<label>".$msg['caddie_select_notice_tpl']."</label>
						</div>
						<div class='row'>
							".$sel_notice_tpl."
						</div>
					</div>
				</div>";
		}
		$search_filters_form .= $sel_notice_tpl;
		return $search_filters_form;
	}
	
	/**
	 * Objet de la liste du document bibliographique
	 */
	protected function get_display_export_noti_content_object_list($object, $line) {
		$display = "";
		$myCart = caddie_root::get_instance_from_object_type(static::$object_type, static::$id_caddie);
		if ($myCart->type=="EXPL"){
			$rqt_test = "select expl_notice as id from exemplaires where expl_id='".$object->id."' ";
			$res_notice = pmb_mysql_query($rqt_test);
			$obj_notice = pmb_mysql_fetch_object($res_notice) ;
			if (!$obj_notice->id) {
				$rqt_test = "select num_notice as id from bulletins join exemplaires on bulletin_id=expl_bulletin where expl_id='".$object->id."' ";
				$res_notice = pmb_mysql_query($rqt_test);
				$obj_notice = pmb_mysql_fetch_object($res_notice) ;
			}
			if((!isset($this->flag_notice_id[$obj_notice->id]) || !$this->flag_notice_id[$obj_notice->id]) && $obj_notice->id){
				$this->flag_notice_id[$obj_notice->id]=1;
				$display .= $this->instance_notice_tpl_gen->build_notice($obj_notice->id);
			}
		} elseif ($myCart->type=="NOTI") $display .= $this->instance_notice_tpl_gen->build_notice($object->id);
		if ($myCart->type=="BULL"){
			$rqt_test = $rqt_tout = "select num_notice as id from bulletins where bulletin_id = '".$object->id."' ";
			$res_notice = pmb_mysql_query($rqt_test);
			$obj_notice = pmb_mysql_fetch_object($res_notice);
			if((!isset($this->flag_notice_id[$obj_notice->id]) || !$this->flag_notice_id[$obj_notice->id]) && $obj_notice->id){
				$this->flag_notice_id[$obj_notice->id]=1;
				$display .= $this->instance_notice_tpl_gen->build_notice($obj_notice->id);
			}
		}
		return $display;
	}
	
	/**
	 * Liste des objets du document bibliographique
	 */
	public function get_display_export_noti_content_list() {
		$display = '';
		if(isset($this->applied_group[0]) && $this->applied_group[0]) {
			$grouped_objects = $this->get_grouped_objects();
			foreach($grouped_objects as $group_label=>$objects) {
				$display .= "
					<div class='list_ui_content_list_group ".$this->objects_type."_content_list_group' colspan='".count($this->columns)."'>
						".$group_label."
					</div>";
				foreach ($objects as $i=>$object) {
					$display .= $this->get_display_export_noti_content_object_list($object, $i);
				}
			}
		} else {
			foreach ($this->objects as $i=>$object) {
					$display .= $this->get_display_export_noti_content_object_list($object, $i);
			}
		}
		return $display;
	}
	
	public function get_display_export_noti_list() {
		global $charset;
		
		$display = "";
		
		$notice_tpl = $this->objects_type."_notice_tpl";
		global ${$notice_tpl};
		$this->instance_notice_tpl_gen=new notice_tpl_gen(${$notice_tpl});
		if(count($this->objects)) {
			$display .= $this->get_display_export_noti_content_list();
		}
		return "<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>".$display."</body></html>";
	}
	
	protected function get_exclude_fields() {
		switch (static::$object_type) {
			case 'NOTI':
				return array(
						'tparent_id',
						'ed1_id',
						'ed2_id',
						'coll_id',
						'subcoll_id',
						'indexint',
						'statut',
						'signature',
						'opac_visible_bulletinage',
						'map_echelle_num',
						'map_projection_num',
						'map_ref_num',
						'map_equinoxe'
				);
				break;
			case 'EXPL':
				return array(
						'expl_notice',
						'expl_bulletin',
						'expl_typdoc',
						'expl_section',
						'expl_statut',
						'expl_location',
						'expl_codestat',
						'expl_owner',
						'transfert_location_origine',
						'transfert_statut_origine',
						'transfert_section_origine',
						'idtyp_doc',
						'tdoc_owner'
				);
				break;
			case 'BULL':
				return array(
						'index_titre',
						'num_notice'
				);
				break;
		}
	}
	
	protected function get_main_fields() {
		switch (static::$object_type) {
			case 'NOTI':
				return array_merge(
						$this->get_describe_fields('notices', 'notices', 'notices'),
						array('serie_name' => $this->get_describe_field('titrserie', 'notices', 'notices')),
						array('collection_name' => $this->get_describe_field('coll', 'notices', 'notices')),
						array('subcollection_name' => $this->get_describe_field('subcoll', 'notices', 'notices')),
						array('publisher_name' => $this->get_describe_field('editeur', 'notices', 'notices')),
						array('indexint_name' => $this->get_describe_field('indexint', 'notices', 'notices')),
						array('statut_name' => $this->get_describe_field('statut', 'notices', 'notices'))
				);
				break;
			case 'EXPL':
				return array_merge(
						$this->get_describe_fields('exemplaires', 'items', 'exemplaires'),
						$this->get_describe_fields('notices', 'notices', 'notices')
				);
				break;
			case 'BULL':
				return array_merge(
						array('bulletin_numero' => 'bulletin_numero', 'mention_date' => 'mention_date', 'date_date' => 'date_date', 'bulletin_titre' => 'bulletin_titre', 'bulletin_cb' => 'bulletin_cb')
				);
				break;
		}
		
	}
	
	protected function add_authors_available_columns() {
		global $msg;
		
		return array(
				'author_main' => $msg['244'],
// 				'authors_others' => $msg['246'],
				'authors_secondary' => $msg['247']
		);
	}
	
	protected function add_categories_available_columns() {
		global $msg;
	
		return array(
				'categories' => $msg['134']
		);
	}
	
	protected function add_languages_available_columns() {
		global $msg;
		
		return array(
				'langues' => $msg['710'],
				'languesorg' => $msg['711']
		);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		switch (static::$object_type) {
			case 'NOTI':
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_authors_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_categories_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_languages_available_columns());
				$this->add_custom_fields_available_columns('notices', 'notice_id');
				break;
			case 'EXPL':
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_authors_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_categories_available_columns());
				$this->available_columns['main_fields'] = array_merge($this->available_columns['main_fields'], $this->add_languages_available_columns());
				$this->add_custom_fields_available_columns('expl', 'expl_id');
				break;
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'tit1',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_cell_categories_content($object) {
		global $opac_thesaurus;
		global $opac_categories_categ_in_line;
		global $pmb_keyword_sep;
		
		$content = '';
		$record_datas = record_display::get_record_datas($object->id);
		$categories = $record_datas->get_categories();
		foreach($categories as $id_thes => $thesaurus) {
			if($opac_thesaurus) {
				foreach ($thesaurus as $i=>$categorie) {
					if($opac_categories_categ_in_line) {
						if(!$i) {
							$content .= "<p><strong>".$categorie['object']->thes->libelle_thesaurus."</strong></p>";
						} else {
							$content .= $pmb_keyword_sep;
						}
						$content .= "<span>".$categorie['format_label']."</span>";
					} else {
						$content .= "<p>[".$categorie['object']->thes->libelle_thesaurus."] ".$categorie['object']->libelle."</p>";
					}
				}
			} else {
				foreach ($thesaurus as $i=>$categorie) {
					if($opac_categories_categ_in_line) {
						if($i) {
							$content .= $pmb_keyword_sep;
						}
						$content .= "<span>".$categorie['object']->libelle."</span>";
					} else {
						$content .= "<p>".$categorie['object']->libelle."</p>";
					}
				}
			}
		}
		return $content;
	}
	
	protected function get_cell_group_label($group_label, $indice=0) {
		$content = '';
		switch($this->applied_group[$indice]) {
			case 'typdoc':
				$marc_list_instance = marc_list_collection::get_instance('doctype');
				$content .= $marc_list_instance->table[$group_label];
				break;
			default :
				$content .= parent::get_cell_group_label($group_label, $indice);
				break;
		}
		return $content;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'author_main':
				$record_datas = record_display::get_record_datas($object->id);
				$content .= $record_datas->get_auteurs_principaux();
				break;
			case 'authors_others':
				//TODO
				break;
			case 'authors_secondary':
				$record_datas = record_display::get_record_datas($object->id);
				$content .= $record_datas->get_auteurs_secondaires();
				break;
			case 'categories':
				$content .= $this->get_cell_categories_content($object);
				break;
			case 'langues':
			case 'languesorg':
				$record_datas = record_display::get_record_datas($object->id);
				$langues = $record_datas->get_langues();
				$content .= record_display::get_lang_list($langues[$property]); 
				break;
			case 'typdoc':
				$marc_list_instance = marc_list_collection::get_instance('doctype');
				$content .= $marc_list_instance->table[$object->{$property}];
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_export_icons() {
		global $msg;
		
		$export_icons = "<img  src='".get_url_icon('texte_ico.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('EXPORT_NOTI');\" alt='".$msg['etatperso_export_notice']."' title='".$msg['etatperso_export_notice']."'/>&nbsp;&nbsp;";
		$export_icons .= parent::get_export_icons();
		return $export_icons;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/catalog.php?categ=caddie&sub=action&quelle=edition&action=choix_quoi&object_type='.static::$object_type.'&idcaddie='.static::$id_caddie.'&item=0';
	}
}