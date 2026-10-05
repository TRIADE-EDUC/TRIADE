<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_collections_controller.class.php,v 1.7.2.1 2024/06/11 08:23:56 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/entities/entities_authorities_controller.class.php");

// templates pour les fonctions de gestion des collections
require_once($include_path.'/templates/collections.tpl.php');

// classe gestion des collections et des éditeurs
require_once($class_path.'/collection.class.php');
require_once($class_path.'/editor.class.php');

class entities_collections_controller extends entities_authorities_controller {

	protected $model_class_name = 'collection';
	public $num_auth_present;

	public function proceed_update() {
		global $msg;
		global $collection_nom;
		global $ed_id;
		global $collection_web, $issn, $comment;
		global $authority_statut, $authority_thumbnail_url;
		global $forcing, $forcing_values;

		// mise à jour d'une collection
		$collection_nom = clean_string($collection_nom);
		if ((!$collection_nom) || (!$ed_id)) {
			error_message($msg[132], $msg['erreur_creation_collection'], 1, '');
		}else {
			if (empty($forcing_values)) {
				$coll = array(
						'name' => $collection_nom,
						'parent' => $ed_id,
						'collection_web' => $collection_web,
						'issn' => $issn,
						'comment' => $comment,
						'statut'=> $authority_statut,
						'thumbnail_url' => $authority_thumbnail_url
				);
			} else {
				$coll = encoding_normalize::json_decode(stripslashes($forcing_values), true);
			}
			$object_instance = $this->get_object_instance();
			$updated = $object_instance->update($coll,$forcing);
			if($object_instance->get_cp_error_message()){//Traitement des messages d'erreurs champs persos
				error_message($msg['167'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
			}elseif($updated) {
				return $object_instance->id;
			}
		}
		return 0;
	}

	public function get_searcher_instance() {
		return searcher_factory::get_searcher('collections', '', $this->user_input);
	}

	protected function get_display_header_list() {
		global $msg;

		$this->num_auth_present = searcher_authorities_collections::has_authorities_sources('collection');

		$display = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			<th>".$msg[165]."</th>
			".($this->num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
			<th>".$msg["count_notices_assoc"]."</th>
            <th></th>
		</tr>";
		return $display;
	}

	protected function get_display_columns() {
		global $charset;

		$object_instance = $this->authority->get_object_instance();

		$display = $this->get_display_label_column(htmlentities($object_instance->display, ENT_QUOTES, $charset));
		$display .= "<td>".htmlentities($object_instance->issn,ENT_QUOTES, $charset)."</td>";

		//Numéros d'autorite
		if($this->num_auth_present){
			$display .= "<td>".searcher_authorities_collections::get_display_authorities_sources($this->authority->get_num_object(), 'collection')."</td>";
		}
		return $display;
	}

	protected function get_query_notice_count() {
		return "SELECT count(*) FROM notices WHERE coll_id = ".$this->authority->get_num_object();
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=collection&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=collection_form&id=".$id;
	}

	protected function get_results_title() {
		global $msg;

		return $msg[173];
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg[175],
			str_replace('!!cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg[174]),
			0,
			$this->url_base.'&sub=&id='
		);
	}

	protected function get_search_mode() {
		return 2;
	}

	protected function get_aut_type() {
		return "collection";
	}

	protected function get_last_order() {
		return 'order by collection_id desc ';
	}

	protected function get_aut_const(){
	    return TYPE_COLLECTION;
	}
}
