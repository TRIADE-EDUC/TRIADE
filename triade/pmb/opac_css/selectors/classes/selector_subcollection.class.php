<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_subcollection.class.php,v 1.11.4.1 2025/01/16 11:24:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_sub_collection.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path.'/subcollection.class.php');
require_once($class_path."/authority.class.php");

class selector_subcollection extends selector_authorities {

	public function __construct($user_input=''){
		parent::__construct($user_input);
		$this->objects_type = 'subcollections';
	}

	protected function get_form() {
		global $charset;
		global $selector_sub_collection_form;

		$form = $selector_sub_collection_form;
		$form = str_replace("!!deb_saisie!!", htmlentities($this->user_input,ENT_QUOTES,$charset), $form);
		$form = str_replace("!!base_url!!",static::get_base_url(),$form);
		return $form;
	}

	protected function save() {
		global $collection_nom;
		global $coll_id;
		global $issn;

		$value = array();
		$value['name']		=	$collection_nom;
		$value['parent']	=	$coll_id;
		$value['issn'] = $issn;
		$collection = new subcollection();
		$collection->update($value);
		return $collection->id;
	}

	protected function get_authority_instance($authority_id=0, $object_id=0) {
		//return new authority($authority_id, $object_id, AUT_TABLE_SUB_COLLECTIONS);
		return authorities_collection::get_authority('authority', $authority_id, ['num_object' => $object_id, 'type_object' => AUT_TABLE_SUB_COLLECTIONS]);
	}

	protected function get_display_object($id=0, $object_id=0) {
		global $charset;
		global $caller;
		global $callback;

		$display = '';
		$authority = $this->get_authority_instance($id, $object_id);
		$subcollection = $authority->get_object_instance();

		$libellesubcoll = htmlentities(addslashes($subcollection->name),ENT_QUOTES,$charset);
		$idparentcoll = $subcollection->parent;
		$idparentlibelle = htmlentities(addslashes($subcollection->parent_libelle),ENT_QUOTES,$charset);
		$idediteur = $subcollection->publisher;
		$libelleediteur = htmlentities(addslashes($subcollection->publisher_libelle),ENT_QUOTES,$charset);

		$display .= pmb_bidi($authority->get_display_statut_class_html()."
		<a href='#' onclick=\"set_parent('$caller', '".$authority->get_num_object()."', '".$libellesubcoll."','$callback', $idparentcoll, '".$idparentlibelle."', $idediteur, '".$libelleediteur."')\">
			".$subcollection->name."</a>");
		$display .= pmb_bidi("&nbsp;(".$subcollection->parent_libelle.".&nbsp;".$subcollection->publisher_libelle.")<br />");
		return $display;
	}

	public static function get_params_url() {
		global $p3, $p4, $p5, $p6, $mode;

		$params_url = parent::get_params_url();
		$params_url .= ($p3 ? "&p3=".$p3 : "").($p4 ? "&p4=".$p4 : "").($p5 ? "&p5=".$p5 : "").($p6 ? "&p6=".$p6 : "").($mode ? "&mode=".$mode : "");
		return $params_url;
	}
}
?>