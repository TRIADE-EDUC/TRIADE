<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_link.class.php,v 1.10 2022/09/22 13:52:04 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if(!defined('TYPE_CONCEPT_PREFLABEL')){
	define('TYPE_CONCEPT_PREFLABEL', 1);
}
if(!defined('TYPE_TU_RESPONSABILITY')){
	define('TYPE_TU_RESPONSABILITY', 2);
}
if(!defined('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL')){
	define('TYPE_NOTICE_RESPONSABILITY_PRINCIPAL', 3);
}
if(!defined('TYPE_NOTICE_RESPONSABILITY_AUTRE')){
	define('TYPE_NOTICE_RESPONSABILITY_AUTRE', 4);
}
if(!defined('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE')){
	define('TYPE_NOTICE_RESPONSABILITY_SECONDAIRE', 5);
}
if(!defined('TYPE_TU_RESPONSABILITY_INTERPRETER')){
	define('TYPE_TU_RESPONSABILITY_INTERPRETER', 6);
}

global $class_path;
require_once($class_path."/concept.class.php");

class vedette_link {

	/**
	 * Met à jour les objets liés à la vedette
	 * 
	 * @param vedette_composee $vedette Vedette liée
	 */
	static public function update_objects_linked_with_vedette(vedette_composee $vedette) {
		$query = "select num_object, type_object from vedette_link where num_vedette = ".$vedette->get_id();
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			while ($object = pmb_mysql_fetch_object($result)) {
				// On appelle les fonctions de mise à jour des différents objets
				switch ($object->type_object) {
					case TYPE_CONCEPT_PREFLABEL :
						$concept = new concept($object->num_object);
						$concept->update_display_label($vedette->get_label());
						break;
				}
			}
		}
	}
	
	/**
	 * Sauvegarde en base le lien entre vedette et objet
	 * @param vedette_composee $vedette Vedette liée
	 * @param int $object_id Identifiant en base de l'objet
	 * @param int $object_type Type de l'objet
	 */
	static public function save_vedette_link(vedette_composee $vedette, $object_id, $object_type) {
		$query = "replace into vedette_link (num_vedette, num_object, type_object) values (".$vedette->get_id().", ".$object_id.", ".$object_type.")";
		pmb_mysql_query($query);
	}
	
	/**
	 * Supprime tous les liens en base entre cet objet et des vedettes
	 * @param vedette_composee $vedette Vedette liée
	 * @param int $object_id Identifiant en base de l'objet
	 * @param int $object_type Type de l'objet
	 * @return int Identifiant de la vedette liée
	 */
	static public function delete_vedette_link_from_object(vedette_composee $vedette, $object_id, $object_type) {
		$id_vedette=self::get_vedette_id_from_object($object_id, $object_type);
		
		$query = "delete from vedette_link where num_object = ".$object_id." and type_object = ".$object_type;
		pmb_mysql_query($query);
		return $id_vedette;
	}
	
	/**
	 * Retourne l'identifiant de la vedette liée à un objet
	 * @param int $object_id Identifiant de l'objet
	 * @param int $object_type Type de l'objet
	 * @return int Identifiant de la vedette liée
	 */
	static public function get_vedette_id_from_object($object_id, $object_type) {
		if ($object_id) {
			$query = "select num_vedette from vedette_link where num_object = ".$object_id." and type_object = ".$object_type;
			$result = pmb_mysql_query($query);
			if ($result && pmb_mysql_num_rows($result)) {
				if ($row = pmb_mysql_fetch_object($result)) {
					return $row->num_vedette;
				}
			}
		}
		return 0;
	}
}