<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_schemes_controler.class.php,v 1.3.16.1 2025/02/12 12:34:07 dbellamy Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once($class_path.'/frbr/cataloging/frbr_cataloging_schemes.class.php');
require_once($class_path.'/frbr/cataloging/frbr_cataloging_scheme.class.php');

class frbr_cataloging_schemes_controler {

	/**
	 *
	 * @return string contenu html
	 */
	public function proceed() {
		global $pmb_url_base, $action, $scheme_id;

		$scheme_id = intval($scheme_id);

		switch ($action) {
			case 'edit' :
				$return = $this->proceed_edit($scheme_id);
				break;
			case 'save' :
				$this->proceed_save($scheme_id);
				$return = '<script type="text/javascript">document.location="'.$pmb_url_base.'/modelling.php?categ=frbr&sub=cataloging_schemes&action=list"</script>';
				break;
			case 'delete' :
				$this->proceed_delete($scheme_id);
				$return = '<script type="text/javascript">document.location="'.$pmb_url_base.'/modelling.php?categ=frbr&sub=cataloging_schemes&action=list"</script>';
				break;
			case 'select' :
				$return = $this->proceed_select();
				break;
			case 'list' :
			default :
				$return = $this->proceed_list();
				break;
		}
		return $return;
	}

	/**
	 *
	 * @return string liste des schemes de catalogage
	 */
	protected function proceed_list() {
		$schemes = new frbr_cataloging_schemes();
		return $schemes->get_schemes_list();
	}

	/**
	 *
	 * @param integer identifiant du formulaire a editer
	 */
	protected function proceed_edit($id) {
		$scheme = new frbr_cataloging_scheme($id);
		return $scheme->get_form();
	}

	/**
	 *
	 * @param integer identifiant du formulaire a sauvegarder
	 */
	protected function proceed_save($id) {
		$scheme = new frbr_cataloging_scheme($id);
		$scheme->set_values_from_form();
		return $scheme->save();
	}

	/**
	 *
	 * @param integer identifiant du formulaire a supprimer
	 */
	protected function proceed_delete($id) {
		$scheme = new frbr_cataloging_scheme($id);
		$scheme->delete();
	}

	/**
	 *
	 * @param integer identifiant du formulaire a sauvegarder
	 */
	protected function proceed_select() {
		$schemes = new frbr_cataloging_schemes();
		return $schemes->get_schemes_select();
	}

	/**
	 * fonction qui retourne le message associé au type d'entite
	 * @param string $type
	 * @return string message de l'entite
	 */
	public static function get_msg_from_type($type){
		global $msg;
		switch($type){
			case 'author':
			case 'authors':
				return $msg['isbd_author'];
			case 'authperso':
				return $msg['search_by_authperso_title'];
			case 'category':
				return $msg['isbd_categories'];
			case 'collection':
				return $msg['isbd_collection'];
			case 'concept':
			case 'concepts':
			case 'indexed_concept':
				return $msg['search_concept_title'];
			case 'indexint':
				return $msg['isbd_indexint'];
			case 'publisher':
				return $msg['isbd_editeur'];
			case 'record':
			case 'records':
				return $msg['288'];
			case 'serie':
				return $msg['isbd_serie'];
			case 'subcollection':
				return $msg['isbd_collection'];
			case 'titre_uniforme':
			case 'work':
			case 'works':
				return $msg['isbd_titre_uniforme'];
			case 'author_author':
			case 'authorities_author':
			case 'authorities_common_linked_work':
			case 'authorities_performer':
			case 'authperso_event':
			case 'event':
			case 'indexed_entities':
			case 'oeuvre_records':
			case 'records_author_records':
			case 'records_authperso_records':
			case 'records_oeuvre_records':
			case 'titre_uniforme_author':
			default :
				return '';
		}
	}
}