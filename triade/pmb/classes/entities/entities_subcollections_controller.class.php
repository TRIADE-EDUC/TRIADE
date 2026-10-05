<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_subcollections_controller.class.php,v 1.6.8.1 2024/06/11 08:23:56 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/entities/entities_authorities_controller.class.php");

// templates pour les fonctions de gestion des collections
include($include_path.'/templates/collections.tpl.php');

// classe gestion des collections et des éditeurs
require_once($class_path.'/editor.class.php');
require_once($class_path.'/collection.class.php');
require_once($class_path.'/subcollection.class.php');

class entities_subcollections_controller extends entities_authorities_controller {

	protected $model_class_name = 'subcollection';

	public function proceed_update() {
		global $msg;
		global $collection_nom;
		global $coll_id;
		global $issn, $subcollection_web, $comment;
		global $authority_statut, $authority_thumbnail_url;
		global $forcing, $forcing_values;

		// mise à jour d'une sous collection
		$object_instance = $this->get_object_instance();
		if (empty($forcing_values)) {
			$coll = array(
					'name' => $collection_nom,
					'parent' => $coll_id,
					'issn' => $issn,
					'subcollection_web' => $subcollection_web,
					'comment' => $comment,
					'statut'=> $authority_statut,
					'thumbnail_url' => $authority_thumbnail_url
			);
		} else {
			$coll = encoding_normalize::json_decode(stripslashes($forcing_values), true);
		}
		$updated = $object_instance->update($coll, $forcing);
		if($object_instance->get_cp_error_message()){//Traitement des messages d'erreurs champs persos
			error_message($msg['177'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
		}elseif($updated) {
			return $object_instance->id;
		}
		return 0;
	}

	public function get_searcher_instance() {
		return searcher_factory::get_searcher('subcollections', '', $this->user_input);
	}

	protected function get_display_header_list() {
		global $msg;

		$this->num_auth_present = searcher_authorities_subcollections::has_authorities_sources('subcollection');

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

		$display = $this->get_display_label_column($object_instance->name."&nbsp;($object_instance->parent_libelle.&nbsp;$object_instance->editeur_libelle)");
		$display .= "<td>".htmlentities($object_instance->issn,ENT_QUOTES, $charset)."</td>";

 		//Numéros d'autorite
		if($this->num_auth_present){
			$display .= "<td>".searcher_authorities_subcollections::get_display_authorities_sources($object_instance->id, 'subcollection')."</td>";
		}
		return $display;
	}
	protected function get_query_notice_count() {
		return "SELECT count(*) FROM notices WHERE subcoll_id = ".$this->authority->get_num_object();
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=subcollection&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=collection_form&id=".$id;
	}

	protected function get_results_title() {
		global $msg;

		return $msg[183];
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg[184],
			str_replace('!!cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg[181]),
			0,
			$this->url_base.'&sub=&id='
		);
	}

	protected function get_search_mode() {
		return 2;
	}

	protected function get_aut_type() {
		return "subcoll";
	}

	protected function get_last_order() {
		return 'order by sub_coll_id desc ';
	}

	protected function get_aut_const(){
	    return TYPE_SUBCOLLECTION;
	}
}
