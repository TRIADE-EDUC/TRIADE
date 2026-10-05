<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_demandes_ui.class.php,v 1.2 2021/12/23 15:48:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path.'/transfert_demande.class.php');

class list_transferts_demandes_ui extends list_transferts_ui {
	
	protected function _get_query_base() {
		$query = 'select id_transfert_demande from transferts_demande
			INNER JOIN transferts ON id_transfert=num_transfert
			INNER JOIN exemplaires ON num_expl=expl_id
			INNER JOIN docs_section ON expl_section=idsection
			INNER JOIN docs_location AS locd ON num_location_dest=locd.idlocation
			INNER JOIN docs_location AS loco ON num_location_source=loco.idlocation
			INNER JOIN lenders ON expl_owner=idlender
			INNER JOIN docs_statut ON expl_statut=idstatut
            LEFT JOIN resa ON resa_trans=id_resa
			LEFT JOIN empr ON resa_idempr=id_empr
			LEFT JOIN pret ON pret_idexpl=num_expl';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new transfert_demande($row->id_transfert_demande);
	}
	
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'site_origine' => 'transferts_edition_filtre_origine',
						'site_destination' => 'transferts_edition_filtre_destination',
						'f_etat_date' => 'transferts_circ_retour_filtre_etat',
						'cb' => '232',
						'sens_transfert' => 'transfert_sens',
						'etat_demande' => 'editions_datasource_expl_etat_demande_transfert',
						'etat_transfert' => 'editions_datasource_expl_etat_transfert',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('site_origine');
		$this->add_selected_filter('site_destination');
		$this->add_selected_filter('cb');
		$this->add_selected_filter('sens_transfert');
		$this->add_selected_filter('etat_demande');
		$this->add_selected_filter('etat_transfert');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields']['num_transfert'] = 'editions_datasource_expl_id_transfert';
		$this->available_columns['main_fields']['sens_transfert'] = 'transfert_sens';
		$this->available_columns['main_fields']['etat_demande'] = 'editions_datasource_expl_etat_demande_transfert';
		$this->available_columns['main_fields']['etat_transfert'] = 'editions_datasource_expl_etat_transfert';
		$this->available_columns['main_fields']['resa_trans'] = 'editions_datasource_expl_etat_transfert';
	}
	
	protected function init_default_columns() {
		$this->add_column('num_transfert');
		$this->add_column('record');
		$this->add_column('cb');
		$this->add_column('cote');
		$this->add_column('location');
		$this->add_column('formatted_date_creation');
		$this->add_column('source');
		$this->add_column('destination');
		$this->add_column('sens_transfert');
		$this->add_column('etat_demande');
		$this->add_column('etat_transfert');
		$this->add_column('formatted_date_envoyee');
		$this->add_column('formatted_date_reception');
		$this->add_column('motif');
		$this->add_column('motif_refus');
		$this->add_column('empr');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('sens_transfert', 'integer');
		$this->set_filter_from_form('etat_demande', 'integer');
		$this->set_filter_from_form('etat_transfert', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_sens_transfert() {
		global $msg;
		
		$options = array(
				-1 => $msg['all'],
				0 => "Envoi",
				1 => "Retour",
		);
		return $this->get_search_filter_simple_selection('', 'sens_transfert', '', $options);
	}
	
	protected function get_search_filter_etat_demande() {
		global $msg;
		
		$options = array(
				-1 => $msg['all'],
				0 => $msg["editions_datasource_expl_etat_demande_0"],
				1 => $msg["editions_datasource_expl_etat_demande_1"],
				2 => $msg["editions_datasource_expl_etat_demande_2"],
				3 => $msg["editions_datasource_expl_etat_demande_3"],
				4 => $msg["editions_datasource_expl_etat_demande_4"],
				5 => $msg["editions_datasource_expl_etat_demande_5"],
				6 => $msg["editions_datasource_expl_etat_demande_6"],
		);
		return $this->get_search_filter_simple_selection('', 'etat_demande', '', $options);
	}
	
	protected function get_search_filter_etat_transfert() {
		global $msg;
		
		$options = array(
				-1 => $msg['all'],
				0 => $msg["editions_datasource_expl_etat_transfert_0"],
				1 => $msg["editions_datasource_expl_etat_transfert_1"],
		);
		return $this->get_search_filter_simple_selection('', 'etat_transfert', '', $options);
	}
	
	protected function _get_object_property_record($object) {
		if($object->get_transfert()->get_num_notice()) {
			return aff_titre($object->get_transfert()->get_num_notice(), 0);
		} else {
			return aff_titre(0, $object->get_transfert()->get_num_bulletin());
		}
	}
	
	protected function _get_object_property_empr($object) {
		$id_resa = $object->get_resa_trans();
		if($id_resa) {
			$query = "select id_empr, empr_cb from empr join resa on id_empr = resa_idempr where id_resa = ".$id_resa;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) == 1) {
				$row = pmb_mysql_fetch_object($result);
				return emprunteur::get_name($row->id_empr);
			}
		}
		return '';
	}
	
	protected function _get_object_property_source($object) {
		$docs_location = new docs_location($object->get_num_location_source());
		return $docs_location->libelle;
	}
	
	protected function _get_object_property_destination($object) {
		$docs_location = new docs_location($object->get_num_location_dest());
		return $docs_location->libelle;
	}
	
	protected function _get_object_property_motif_refus($object) {
		return $object->get_motif_refus();
	}
	
	protected function _get_object_property_sens_transfert($object) {
		if($object->get_sens_transfert()) {
			return "Retour";
		} else {
			return "Envoi";
		}
	}
	
	protected function _get_object_property_etat_demande($object) {
		global $msg;
		return $msg["editions_datasource_expl_etat_demande_".$object->get_etat_demande()];
	}
	
	protected function _get_object_property_etat_transfert($object) {
		global $msg;
		
		return $msg["editions_datasource_expl_etat_transfert_".$object->get_transfert()->get_etat_transfert()];
	}
	
	protected function _get_object_property_motif($object) {
		return $object->get_transfert()->get_motif();
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'empr':
				//TODO
				$id_resa = $object->get_resa_trans();
				if($id_resa) {
					$query = "select id_empr, empr_cb from empr join resa on id_empr = resa_idempr where id_resa = ".$id_resa;
					$result = pmb_mysql_query($query);
					if(pmb_mysql_num_rows($result) == 1) {
						$row = pmb_mysql_fetch_object($result);
						if (SESSrights & CIRCULATION_AUTH) {
							$content = "<a href='./circ.php?categ=pret&form_cb=".$row->empr_cb."'>";
							$content .= emprunteur::get_name($row->id_empr);
							$content .= "</a>";
						} else {
							$content .= emprunteur::get_name($row->id_empr);
						}
					}
				}
				break;
			case 'formatted_date_reception':
				$content .= $object->get_formatted_date_reception();
				break;
			case 'formatted_date_envoyee':
				$content .= $object->get_formatted_date_envoyee();
				break;
			case 'formatted_date_refus':
				$content .= $object->get_formatted_date_visualisee();
				break;
			case 'transfert_ask_user_num':
			case 'transfert_send_user_num':
				$content .= user::get_param(call_user_func_array(array($object->get_transfert(), "get_".$property), array()), 'username');
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function _get_query_human_sens_transfert() {
		if($this->filters['sens_transfert'] !== '' && $this->filters['sens_transfert'] !== -1) {
			if($this->filters['sens_transfert'] == 1) {
				return "Retour";
			} else {
				return "Envoi";
			}
		}
		return '';
	}
	
	protected function _get_query_human_etat_demande() {
		global $msg;
		
		if($this->filters['etat_demande'] !== '' && $this->filters['etat_demande'] !== -1) {
			return $msg["editions_datasource_expl_etat_demande_".$this->filters['etat_demande']];
		}
		return '';
	}
	
	protected function _get_query_human_etat_transfert() {
		global $msg;
		
		if($this->filters['etat_transfert'] !== '' && $this->filters['etat_transfert'] !== -1) {
			return $msg["editions_datasource_expl_etat_transfert_".$this->filters['etat_transfert']];
		}
		return '';
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=transferts_demandes';
	}
}