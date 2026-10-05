<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_group_ui.class.php,v 1.6 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/list/readers/list_readers_bannette_ui.tpl.php");
require_once($class_path."/bannette.class.php");

class list_readers_group_ui extends list_readers_ui {
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_column('default', 'align', 'left');
	}
	
	protected function init_no_sortable_columns() {
		parent::init_no_sortable_columns();
		$this->no_sortable_columns[] = 'delmember';
	}
	
	protected function add_column_delmember() {
		global $msg, $charset;
		
		$this->columns[] = array(
				'property' => 'delmember',
				'label' => "",
				'html' => "
					<a href=\"./circ.php?categ=groups&action=delmember&groupID=".$this->filters['group']."&memberID=!!id!!\">
						<img src='".get_url_icon('trash.gif')."' title=\"".htmlentities($msg['928'], ENT_QUOTES, $charset)."\" border=\"0\" />
					</a>",
                'exportable' => false
		);
	}
	
	public function get_display_search_form() {
	    global $empr_allow_prolong_members_group;
	    //formulaire au dessus de la liste
	    if ($empr_allow_prolong_members_group) {
	        return '';
        } else {
	        return parent::get_display_search_form();
        }
	}
	
	protected function init_default_pager() {
	    parent::init_default_pager();
	    //formulaire au dessus de la liste
	    if ($this->filters['group']) {
	        $this->pager['all_on_page'] = true;
	    }
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch ($property) {
			case 'aff_date_prolong':
				return array();
			default:
				return array(
						'onclick' => "document.location=\"".$this->get_edition_link($object)."\";"
				);
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $empr_prolong_calc_date_adhes_depassee;
	
		$content = '';
		switch($property) {
			case 'empr_name':
				$content .= "<a href='".$this->get_edition_link($object)."'>";
				$content .= $object->nom;
				if($object->prenom) {
					$content .= ", ".$object->prenom;
				}
				$content .= "</a>";
				break;
			case 'aff_date_prolong':
				if ($object->adhesion_renouv_proche() || $object->adhesion_depassee()) {
					$rqt="select duree_adhesion from empr_categ where id_categ_empr='".$object->categ."'";
					$res_dur_adhesion = pmb_mysql_query($rqt);
					$row = pmb_mysql_fetch_row($res_dur_adhesion);
					$nb_jour_adhesion_categ = $row[0];
					
					if ($empr_prolong_calc_date_adhes_depassee && $object->adhesion_depassee()) {
						$rqt_date = "select date_add(curdate(),INTERVAL 1 DAY) as nouv_date_debut,
							date_add(curdate(),INTERVAL $nb_jour_adhesion_categ DAY) as nouv_date_fin ";
					} else {
						$rqt_date = "select date_add('".$object->date_expiration."',INTERVAL 1 DAY) as nouv_date_debut,
							date_add('".$object->date_expiration."',INTERVAL $nb_jour_adhesion_categ DAY) as nouv_date_fin ";
					}
					$resultatdate=pmb_mysql_query($rqt_date) or die ("<br /> $rqt_date ".pmb_mysql_error());
					$resdate=pmb_mysql_fetch_object($resultatdate);
					$content .= get_input_date("form_expiration_".$object->id, "form_expiration_".$object->id, $resdate->nouv_date_fin, false);
				}
				break;
			default:
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function init_default_columns() {
		global $empr_allow_prolong_members_group;
		
		$this->add_column('empr_name');
		$this->add_column('cb');
		$this->add_column('nb_loans');
		$this->add_column('nb_resas_and_validated');
		if ($empr_allow_prolong_members_group) {
			$this->add_column('aff_date_adhesion', 'group_empr_date_adhesion');
			$this->add_column('aff_date_expiration', 'group_empr_date_expiration');
			$this->add_column('aff_date_prolong', 'group_empr_date_prolong');
		}
		$this->add_column_delmember();
	}
	
	protected function get_selection_actions() {
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
		}
		return $this->selection_actions;
	}
	
	protected function get_edition_link($object) {
		global $base_path;
		return $base_path.'/circ.php?categ=pret&form_cb='.rawurlencode($object->cb)."&groupID=".$this->filters['group'];
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $categ, $sub, $groupID;
	
		return $base_path.'/circ.php?categ='.$categ.'&sub='.$sub.'&groupID='.$groupID;
	}
}