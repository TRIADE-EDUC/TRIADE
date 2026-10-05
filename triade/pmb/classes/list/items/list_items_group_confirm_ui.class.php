<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_items_group_confirm_ui.class.php,v 1.2 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_items_group_confirm_ui extends list_items_group_ui {
	
	protected function init_default_columns() {
		global $pmb_sur_location_activate;
		
		$this->add_column('expl_cb', 'groupexpl_form_cb');
		$this->add_column('record_header', 'groupexpl_form_notice');
		if($pmb_sur_location_activate){
			$this->add_column('sur_loc_libelle');
		}
		$this->add_column('location_libelle');
		$this->add_column('section_libelle');
		$this->add_column('expl_cote');
		$this->add_column('statut_libelle');
		$this->add_column('pointed');
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$attributes = array();
		if(exemplaire::is_currently_borrowed($object->expl_id)) {
			$attributes['style'] = 'color:#FF0000;';
		}
		return $attributes;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		$content = '';
		switch ($property) {
			case 'statut_libelle':
				$content .= $object->statut_libelle;
				if(exemplaire::is_currently_borrowed($object->expl_id)) {
					$req_pret="select * from pret, empr where id_empr=pret_idempr and pret_idexpl=".$object->expl_id;
					$res_pret=pmb_mysql_query($req_pret);
					if (pmb_mysql_num_rows($res_pret)) {
						$r_pret=pmb_mysql_fetch_object($res_pret);
						$content .= "<br />".$msg["groupexpl_confirm_emprunteur"]." ";
						$content .= "<a href='./circ.php?categ=pret&form_cb=".$r_pret->empr_cb."'>".$r_pret->empr_nom." ".$r_pret->empr_prenom."</a>";
					}
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
}