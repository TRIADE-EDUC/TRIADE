<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_bulletins_ui.class.php,v 1.2.2.1.2.1 2025/04/25 08:32:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_bulletins_ui extends list_opac_ui {
		
	protected $bulletins_data;
	
	protected function _get_query_base() {
		$query = 'SELECT bulletin_id as id, bulletins.* FROM bulletins';
		return $query;
	}
	
	protected function _get_query() {
	    $query_filters = $this->_get_query_filters();
	    
	    global $gestion_acces_active, $gestion_acces_empr_notice, $gestion_acces_empr_docnum, $opac_show_links_invisible_docnums;
	    $join_docnum_noti = $join_docnum_bull = "";
	    if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
	        $ac = new acces();
	        $dom_2= $ac->setDomain(2);
	        $join_noti = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.num_notice");
	        $join_bull = $dom_2->getJoin($_SESSION["id_empr_session"],4,"bulletins.bulletin_notice");
	        if(!$opac_show_links_invisible_docnums){
	            $join_docnum_noti = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.num_notice");
	            $join_docnum_bull = $dom_2->getJoin($_SESSION["id_empr_session"],16,"bulletins.bulletin_notice");
	        }
	    }else{
	        $join_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	        $join_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((notice_visible_opac=1 and notice_visible_opac_abon=0)".($_SESSION["user_code"]?" or (notice_visible_opac_abon=1 and notice_visible_opac=1)":"").")";
	        if(!$opac_show_links_invisible_docnums){
	            $join_docnum_noti = "join notices on bulletins.num_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	            $join_docnum_bull = "join notices on bulletins.bulletin_notice = notices.notice_id join notice_statut on notices.statut = notice_statut.id_notice_statut AND ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	        }
	    }
	    $join_docnum_explnum = "";
	    if(!$opac_show_links_invisible_docnums) {
	        if ($gestion_acces_active==1 && $gestion_acces_empr_docnum==1) {
	            $ac = new acces();
	            $dom_3= $ac->setDomain(3);
	            $join_docnum_explnum = $dom_3->getJoin($_SESSION["id_empr_session"],16,"explnum_id");
	        }else{
	            $join_docnum_explnum = "join explnum_statut on explnum_docnum_statut=id_explnum_statut and ((explnum_visible_opac=1 and explnum_visible_opac_abon=0)".($_SESSION["user_code"]?" or (explnum_visible_opac_abon=1 and explnum_visible_opac=1)":"").")";
	        }
	    }
	    $requete_docnum_noti = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where bulletin_notice = ".$this->filters['serial_id']." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_noti." where bulletin_notice = ".$this->filters['serial_id'].") group by bulletin_id";
	    $requete_docnum_bull = "select bulletin_id, count(explnum_id) as nbexplnum from explnum join bulletins on explnum_bulletin = bulletin_id and explnum_notice = 0 ".$join_docnum_explnum." where bulletin_notice = ".$this->filters['serial_id']." and explnum_bulletin in (select bulletin_id from bulletins ".$join_docnum_bull." where bulletin_notice = ".$this->filters['serial_id'].") group by bulletin_id";
	    $requete_noti = "select bulletins.bulletin_id as id, bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_noti." left join ($requete_docnum_noti) as docnum_noti on bulletins.bulletin_id = docnum_noti.bulletin_id ".$query_filters." and bulletins.num_notice != 0 GROUP BY bulletins.bulletin_id";
	    $requete_bull = "select bulletins.bulletin_id as id, bulletins.*,ifnull(nbexplnum,0) as nbexplnum from bulletins ".$join_bull." left join ($requete_docnum_bull) as docnum_bull on bulletins.bulletin_id = docnum_bull.bulletin_id ".$query_filters." and bulletins.num_notice = 0 GROUP BY bulletins.bulletin_id";
	    
	    $query = "select * from (".$requete_noti." union ".$requete_bull.") as uni ".$query_filters;
	    
// 	    $query = $this->_get_query_base();
// 	    $query .= $this->_get_query_filters();
	    $query .= $this->_get_query_order();
	    if($this->applied_sort_type == "SQL"){
	        $this->pager['nb_results'] = pmb_mysql_num_rows(pmb_mysql_query($query));
	        $query .= $this->_get_query_pager();
	    }
	    return $query;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'location' => '',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'serial_id' => 0,
				'docs_location_id' => 0,
				'bulletin_numero' => '',
				'date_date_start' => '',
				'date_date_end' => '',
				'mention_date' => ''
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
	    global $opac_bull_results_per_page, $nb_per_page_custom;

	    parent::init_default_pager();
	    // nombre de références par pages (12 par défaut)
	    $this->pager['nb_per_page'] = ($opac_bull_results_per_page ? $opac_bull_results_per_page : 12);
	    // Il y le paginateur a prendre en compte
	    if (isset($nb_per_page_custom)) {
	        $this->pager['nb_per_page'] = intval($nb_per_page_custom);
	    }
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    //si on recherche par date ou par numéro, le résultat sera trié par ordre croissant
	    if ($this->filters['bulletin_numero'] || $this->filters['mention_date'] || $this->filters['date_date_start'] || $this->filters['date_date_end']) {
	        $this->add_applied_sort('date_date');
	        $this->add_applied_sort('bulletin_numero');
	    } else {
	        $this->add_applied_sort('date_date', 'desc');
	        $this->add_applied_sort('bulletin_numero', 'desc');
	    }
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		global $pmb_collstate_advanced;
		
		$this->available_columns =
		array('main_fields' =>
				array(
						'bulletin_numero' => 'bull_numero',
				        'date_date' => 'date_parution',
						'mention_date' => 'bull_mention_date',
				        'bulletin_titre' => 'etat_collection_title',
    				    'nb_analysis' => 'bulletin_nb_articles',
    				    'nb_explnum' => 'bulletin_nb_explnum',
    				    'nb_expl' => 'bulletin_nb_expl'
				)
		);
		if ($pmb_collstate_advanced) {
			$this->available_columns['main_fields']['collstates'] = 'bul_collstate';
		}
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		global $pmb_collstate_advanced;
		global $opac_show_nb_analysis;
		
		$this->add_column('bulletin_numero');
		$this->add_column('mention_date');
		$this->add_column('bulletin_titre');
		if ($pmb_collstate_advanced) {
		    $this->add_column('collstates');
		}
		if ($opac_show_nb_analysis) {
		    $this->add_column('nb_analysis');
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'sorts', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('nb_analysis', 'align', 'center');
		$this->set_setting_column('nb_explnum', 'align', 'center');
		$this->set_setting_column('nb_expl', 'align', 'center');
		$this->set_setting_column('date_date', 'datatype', 'date');
		$this->set_setting_column('nb_analysis', 'datatype', 'integer');
		$this->set_setting_column('nb_explnum', 'datatype', 'integer');
		$this->set_setting_column('nb_expl', 'datatype', 'integer');
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('bulletin_numero');
		$this->set_filter_from_form('date_date_start');
		$this->set_filter_from_form('date_date_end');
		$this->set_filter_from_form('mention_date');
		parent::set_filters_from_form();
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_simple_restriction('serial_id', 'bulletin_notice', 'integer');
		if($this->filters['bulletin_numero']) {
			$this->query_filters [] = 'bulletin_numero like "%'.str_replace('*','%', $this->filters['bulletin_numero']).'%"';
		}
		$this->_add_query_filter_interval_restriction('date_date', 'date_date', 'date');
		if($this->filters['mention_date']) {
			$this->query_filters [] = 'mention_date like "%'.str_replace('*','%', $this->filters['mention_date']).'%"';
		}
		if($this->filters['ids']) {
			$this->query_filters [] = 'bulletin_id IN ('.$this->filters['ids'].')';
		}
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 * @param number $index
	 * @return number
	 */
	protected function _compare_objects($a, $b, $index=0) {
		if($this->applied_sort[$index]['by']) {
			$sort_by = $this->applied_sort[$index]['by'];
			switch($sort_by) {
				case 'bulletin_numero':
					$matches_a = array();
					$matches_b = array();
					$bulletin_numero_a = 0;
					preg_match_all('!\d+!', $a->bulletin_numero, $matches_a);
					if(!empty($matches_a[0][0])) {
						$bulletin_numero_a = $matches_a[0][0];
						if(!empty($matches_a[0][1])) {
							$bulletin_numero_a .= ".".$matches_a[0][1];
						}
						if(!empty($matches_a[0][2])) {
							$bulletin_numero_a .= $matches_a[0][2];
						}
					}
					
					$bulletin_numero_b = 0;
					preg_match_all('!\d+!', $b->bulletin_numero, $matches_b);
					if(!empty($matches_b[0][0])) {
						$bulletin_numero_b = $matches_b[0][0];
						if(!empty($matches_b[0][1])) {
							$bulletin_numero_b .= ".".$matches_b[0][1];
						}
						if(!empty($matches_b[0][2])) {
							$bulletin_numero_b .= $matches_b[0][2];
						}
					}
					return $this->floatcmp($bulletin_numero_a, $bulletin_numero_b);
					break;
				case 'date_date':
					return strcmp($a->date_date, $b->date_date);
				default :
					return parent::_compare_objects($a, $b, $index);
			}
		}
	}
	
	protected function _get_object_property_mention_date($object) {
	    if ($object->mention_date) {
	        return $object->mention_date;
	    } elseif ($object->date_date) {
	        return formatdate($object->date_date);
	    }
	}
	
	protected function _get_object_property_nb_analysis($object) {
		if(!isset($this->bulletins_data[$object->bulletin_id]['nb_analysis'])) {
		    $record_datas = NEW record_datas($this->filters['serial_id']);
		    $this->bulletins_data[$object->bulletin_id]['nb_analysis'] = $record_datas->get_nb_articles($object->id);
		}
		return $this->bulletins_data[$object->bulletin_id]['nb_analysis'];
	}
	
	protected function _get_object_property_nb_expl($object) {
		if(!isset($this->bulletins_data[$object->bulletin_id]['nbexpl'])) {
		    // A revoir plus tard si necessaire
			$this->bulletins_data[$object->bulletin_id]['nbexpl'] = 0;
		}
		return $this->bulletins_data[$object->bulletin_id]['nbexpl'];
	}
	
	protected function _get_object_property_nb_explnum($object) {
		if(!isset($this->bulletins_data[$object->bulletin_id]['nbexplnum'])) {
// 			$query = "SELECT count(1) FROM explnum WHERE explnum_bulletin='".$object->bulletin_id."' ";
// 			$result = pmb_mysql_query($query);
// 			$this->bulletins_data[$object->bulletin_id]['nbexplnum'] = pmb_mysql_result($result, 0, 0);
			$this->bulletins_data[$object->bulletin_id]['nbexplnum'] = $object->nbexplnum;
		}
		return $this->bulletins_data[$object->bulletin_id]['nbexplnum'];
	}
	
	protected function get_collstates($object) {
		global $pmb_collstate_advanced;
		
		$collstates = array();
		if ($pmb_collstate_advanced) {
		    $query = "SELECT collstate_bulletins_num_collstate, state_collections FROM collstate_bulletins JOIN collections_state ON collections_state.collstate_id = collstate_bulletins.collstate_bulletins_num_collstate WHERE collstate_bulletins_num_bulletin = '".$object->bulletin_id."'";
		    $result = pmb_mysql_query($query);
		    if (pmb_mysql_num_rows($result)) {
		        while ($row = pmb_mysql_fetch_object($result)) {
		            $collstates[$row->collstate_bulletins_num_collstate] = $row->state_collections;
		        }
		    }
		}
		return $collstates;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
	
		$content = '';
		switch($property) {
		    case 'bulletin_numero':
		        $nb_explnum = $this->_get_object_property_nb_explnum($object);
		        $content .= ($nb_explnum > 1 ? "<img src='".get_url_icon("globe_rouge.png")."' alt=''/>" : "<img src='".get_url_icon("globe_orange.png")."' alt=''/>");
		        $content .= htmlentities($object->bulletin_numero, ENT_QUOTES, $charset);
		        break;
			case 'nb_analysis':
			    $record_datas = new record_datas($this->filters['serial_id']);
			    $nb_analysis = $record_datas->get_nb_articles($object->id);
			    if ($nb_analysis) {
			        $content .= $nb_analysis." ".( $nb_analysis == 1 ? $msg['article'] : $msg['articles'] );
			    }
				break;
			case 'collstates':
				$collstates = $this->get_collstates($object);
				foreach($collstates as $id => $collstate) {
					if($content) {
						$content.= "<br/>";
					}
					$content .="<a href='./index.php?lvl=collstate_bulletins_display&id=".$id."&serial_id=".$this->filters['serial_id']."'>".$collstate."</a>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_class_objects_list() {
	    return parent::get_class_objects_list()." exemplaires";
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
	    $attributes['href'] = "./index.php?lvl=bulletin_display&id=".$object->bulletin_id;
		return $attributes;
	}
	
	public function get_error_message_empty_list() {
		global $msg;
		
	    return "<strong>".$msg["bull_no_found"]."</strong>";
	}
	
	protected function get_display_pager() {
	    global $nb_per_page_custom;
	    // constitution des liens
	    if (isset($nb_per_page_custom)) {
	        $this->pager['nb_per_page'] = intval($nb_per_page_custom);
	    }
	    $url_page = "javascript:if (document.getElementById(\"onglet_isbd".$this->filters['serial_id']."\")) if (document.getElementById(\"onglet_isbd".$this->filters['serial_id']."\").className==\"isbd_public_active\") document.form_values.premier.value=\"ISBD\"; else document.form_values.premier.value=\"PUBLIC\"; document.form_values.page.value=!!page!!; document.form_values.submit()";
	    $nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
	    $action = "javascript:if (document.getElementById(\"onglet_isbd".$this->filters['serial_id']."\")) if (document.getElementById(\"onglet_isbd".$this->filters['serial_id']."\").className==\"isbd_public_active\") document.form_values.premier.value=\"ISBD\"; else document.form_values.premier.value=\"PUBLIC\"; document.form_values.page.value=document.form.page.value; document.form_values.submit()";
	    return "<div class='row'></div><div id='navbar'><br />\n<div style='text-align:center'>".printnavbar($this->pager['page'], $this->pager['nb_results'], $this->pager['nb_per_page'], $url_page, $nb_per_page_custom_url, $action)."</div></div>";
	}
	
	protected function pager_custom() {
	    return '';
	}
	
	protected function _cell_is_sortable($name) {
	    return false;
	}
}