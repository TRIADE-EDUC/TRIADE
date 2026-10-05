<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_opac_items_record_ui.class.php,v 1.1 2023/12/14 15:30:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_opac_items_record_ui extends list_opac_items_ui {
	
// 	protected function _get_query_base() {
// 		global $msg;
		
// 		$query = "SELECT exemplaires.expl_id as id, exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, docs_codestat.*, lenders.*, tdoc_libelle, ";
// 		if(array_key_exists("surloc_libelle", $this->available_columns['main_fields']) !== false){
// 			$query .= "sur_location.*, ";
// 		}
// 		$query .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
// 		$query .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
// 		$query .= " IF(pret_retour>sysdate(),0,1) as retard " ;
// 		$query .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
// 		$query .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
// 		if(array_key_exists("surloc_libelle", $this->available_columns['main_fields']) !== false){
// 			$query .= " left join sur_location on docs_location.surloc_num=sur_location.surloc_id ";
// 		}
// 		$query .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
// 		$query .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
// 		$query .= " left join docs_codestat on exemplaires.expl_codestat=docs_codestat.idcode ";
// 		$query .= " left join lenders on exemplaires.expl_owner=lenders.idlender ";
// 		$query .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
// 		return $query;
// 	}
	
	protected function init_default_applied_sort() {
		global $opac_expl_order;
		if($opac_expl_order) {
		    $cols = explode(',', $opac_expl_order);
			foreach ($cols as $col) {
				$col_asc_desc = explode(' ', $col);
				$by = $col_asc_desc[0];
				$asc_desc = (!empty($col_asc_desc[1]) ? $col_asc_desc[1] : 'asc');
				if(array_key_exists($by, $this->available_columns['main_fields']) !== false){
					$this->add_applied_sort($by, $asc_desc);
				}
			}
		}elseif(array_key_exists("surloc_libelle", $this->available_columns['main_fields']) !== false){
			$this->add_applied_sort('surloc_libelle');
			$this->add_applied_sort('location_libelle');
			$this->add_applied_sort('section_libelle');
			$this->add_applied_sort('expl_cote');
			$this->add_applied_sort('expl_cb');
		}else{
			$this->add_applied_sort('location_libelle');
			$this->add_applied_sort('section_libelle');
			$this->add_applied_sort('expl_cote');
			$this->add_applied_sort('expl_cb');
		}
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		global $pmb_expl_order;
		
		if(empty($this->applied_sort[0]['by']) && $pmb_expl_order) {
			return $this->_get_query_order_sql_build($pmb_expl_order);
		} else {
			return parent::_get_query_order();
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('expl_cote', 'text', array('bold' => true));
	}
	
	protected function init_default_columns() {
	    global $opac_sur_location_activate, $opac_expl_data;
		
		if($opac_sur_location_activate) $surloc_field="surloc_libelle,";
		else $surloc_field="";
		if (!$opac_expl_data) $opac_expl_data="tdoc_libelle,".$surloc_field."location_libelle,section_libelle,expl_cote";
		$colonnesarray=explode(",",$opac_expl_data);
		
		if (!in_array("expl_cb", $colonnesarray)) array_unshift($colonnesarray, "expl_cb");
		
		//Présence de champs personnalisés
		$this->displayed_cp = array();
		if (strstr($opac_expl_data, "#")) {
			$this->cp=new parametres_perso("expl");
		}
		for ($i=0; $i<count($colonnesarray); $i++) {
			if (substr($colonnesarray[$i],0,1)=="#") {
				//champ personnalisé
				if (!$this->cp->no_special_fields) {
					$id=substr($colonnesarray[$i],1);
					$this->add_column($this->cp->t_fields[$id]['NAME'], $this->cp->t_fields[$id]['TITRE']);
					$this->displayed_cp[$id] = $this->cp->t_fields[$id]['NAME'];
				}
			} else {
				$this->add_column($colonnesarray[$i]);
			}
		}
		if (!in_array("statut_libelle", $colonnesarray)) {
		    $this->add_column('statut_libelle');
		}
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
	    global $pmb_transferts_actif, $transferts_statut_transferts;
	    
	    if(!empty($this->selected_columns[$property])) {
	        $attributes = array(
	            'class' => $this->_get_label_cell_header($this->selected_columns[$property])
	        );
	    } else {
	        $attributes = array();
	    }
	    switch($property) {
	        case 'statut_libelle':
	            $class_statut = '';
	            $expl_data = $this->get_expl_data($object->expl_id);
	            if ($expl_data['flag_resa']) {
                    $class_statut = "expl_reserve";
                } else {
                    if ($object->pret_flag) {
                        if($expl_data['pret_retour']) { // exemplaire sorti
                            $class_statut = "expl_out";
                        } else { // pas sorti
                            $class_statut = "expl_available";
                        }
                    } else { // pas prêtable
                        // exemplaire pas prêtable, on affiche juste "exclu du pret"
                        if (($pmb_transferts_actif=="1") && ("".$object->idstatut.""== $transferts_statut_transferts)) {
                            $class_statut = "expl_transfert";
                        } else {
                            $class_statut = "expl_unavailable";
                        }
                    }
                }
	            $attributes['class'] .= ' '.$class_statut;
	            break;
	        default:
	            break;
	    }
	    return $attributes;
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg, $charset, $opac_url_base;
		
		$content = '';
		switch($property) {
		    case 'location_libelle':
    		    if($object->num_infopage) {
    		        if ($object->surloc_num) {
    		            $param_surloc="&surloc=".$object->surloc_num;
    		        } else {
    		            $param_surloc="";
    		        }
    		        $content .="<a href=\"".$opac_url_base."index.php?lvl=infopages&pagesid=".$object->num_infopage."&location=".$object->idlocation.$param_surloc."\" title=\"".htmlentities($msg['location_more_info'], ENT_QUOTES, $charset)."\">".htmlentities($object->location_libelle, ENT_QUOTES, $charset)."</a>";
    		    } else {
    		        $content .= $object->location_libelle;
    		    }
	            break;
// 			case 'expl_cote':
// 				if ($pmb_html_allow_expl_cote) {
// 					$content.=$object->expl_cote;
// 				} else {
// 					$content.=htmlentities($object->expl_cote,ENT_QUOTES, $charset);
// 				}
// 				break;
			case 'statut_libelle':
			    $expl_data = $this->get_expl_data($object->expl_id);
			    $content .= record_display::get_display_situation($expl_data);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	public function get_display_search_form() {
		//Ne pas retourner le formulaire car déjà inclu dans un autre
		return '';
	}
	
	protected function get_prefix() {
		if($this->filters['expl_bulletin']){
			return "bull_".$this->filters['expl_bulletin'];
		}else{
			return "noti_".$this->filters['expl_notice'];
		}
	}

	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module, $sub;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=expl&sub='.$sub;
	}
}