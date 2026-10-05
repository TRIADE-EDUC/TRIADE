<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_authors_controller.class.php,v 1.1.14.1 2024/06/11 08:23:55 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/entities/entities_authorities_controller.class.php");
require_once($class_path.'/author.class.php');
include($include_path.'/templates/authors.tpl.php');

class entities_authors_controller extends entities_authorities_controller {

	protected $model_class_name = 'auteur';

	public function proceed_duplicate() {
		$object_instance = $this->get_object_instance();
		$id = 0;
		$object_instance->show_form($object_instance->type,true);
	}

	public function proceed_update() {
		global $msg;
		global $author_type, $author_nom, $author_rejete;
		global $date, $author_web, $author_comment;
		global $voir_id;
		global $lieu, $ville, $pays;
		global $subdivision, $numero;
		global $author_import_denied;
		global $authority_statut, $authority_thumbnail_url;
		global $forcing_values;
		global $forcing;

		if (empty($forcing_values)) {

    		// mise à jour d'un auteur
    		$author = array(
    				'type' 			=> $author_type,
    				'name' 			=> $author_nom,
    				'rejete' 		=> $author_rejete,
    				'date' 			=> $date,
    				'author_web'	=> $author_web,
    				'author_comment'=> $author_comment,
    				'voir_id' 		=> $voir_id,
    				'lieu'			=> $lieu,
    				'ville'			=> $ville,
    				'pays'			=> $pays,
    				'subdivision'	=> $subdivision,
    				'numero'		=> $numero,
    				'import_denied'	=> (isset($author_import_denied) ? $author_import_denied : 0),
    				'statut'		=> $authority_statut,
    				'thumbnail_url' => $authority_thumbnail_url
    		);
		} else {
		    $author = encoding_normalize::json_decode(stripslashes($forcing_values), true);
		}

		$object_instance = $this->get_object_instance();
		if (!isset($forcing)) {
		    $forcing = false;
		}
		$object_instance->update($author, $forcing);
		if($object_instance->get_cp_error_message()){
			error_message($msg['200'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
			return 0;
		}else{
			global $type_autorite;
			$type_autorite=$author_type;
			return $object_instance->id;
		}
	}

	public function proceed_form() {
		global $type_autorite, $cataloging_scheme_id, $id;

		$unlock_unload_script = "";
		if($this->id){
		    $entity_locking = new entity_locking($id, $this->get_type_const());
		    $entity_locking->lock_entity();
		    $unlock_unload_script = $entity_locking->get_polling_script();
		}

		$object_instance = $this->get_object_instance();
		ob_start();
		$object_instance->show_form($type_autorite);
		$entity_form = ob_get_contents();
		ob_end_clean();
		$entity_form = str_replace('<form', '<form data-advanced-form="true"', $entity_form);
		if ($cataloging_scheme_id) {
			$entity_form.= $this->get_cataloging_scheme_link_script("author");
		}
		print $entity_form;
		print $this->get_selector_js_script();
		print $unlock_unload_script;
	}

	public function get_searcher_instance()	{
		return searcher_factory::get_searcher('authors', '', $this->user_input);
	}

	protected function search_form() {
		global $type_autorite;

		$model_class_name = $this->get_model_class_name();
		$model_class_name::search_form($type_autorite);
	}

	protected function get_display_header_list() {
		global $msg;

		$this->num_auth_present = searcher_authorities_authors::has_authorities_sources('author');

		$display = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			".($this->num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
			<th>".$msg["count_notices_assoc"]."</th>
            <th></th>
		</tr>";
		return $display;
	}

	protected function get_display_columns() {
		global $msg;

		$object_instance = $this->authority->get_object_instance(array('recursif' => 1));
		$author_entry=$object_instance->get_isbd();
		if($object_instance->see) {
			// auteur avec renvoi
			// récupération des données de l'auteur cible
			$see = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $object_instance->see, array('recursif' => 1));
			$author_voir=$see->get_isbd();

			$author_voir = "<a href='./autorites.php?categ=see&sub=author&id=".$object_instance->see."'>".$author_voir."</a>";
			$author_entry .= ".&nbsp;-&nbsp;<u>$msg[210]</u>&nbsp;:&nbsp;".$author_voir;
		}

		$display = $this->get_display_label_column($author_entry, $object_instance->info_bulle);

		//Numéros d'autorite
		if($this->num_auth_present){
			$display .= "<td>".searcher_authorities_authors::get_display_authorities_sources($this->authority->get_num_object(), 'author')."</td>";
		}

		return $display;
	}
	protected function get_query_notice_count() {
		return "SELECT count(distinct responsability_notice) FROM responsability WHERE responsability_author = ".$this->authority->get_num_object();
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=author&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=author_form&id=".$id;
	}

	protected function get_results_title() {
		global $msg;
		global $type_autorite;

		switch($type_autorite){
			case 70 :
				//personne physique
				return $msg[209];
				break;
			case 71 :
				//collectivité
				return $msg["aut_resul_collectivite"];
				break;
			case 72 :
				//congrès
				return $msg["aut_resul_congres"];
				break;
			default:
				return $msg[209];
				break;
		}
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg[211],
			str_replace('!!author_cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg[212]),
			0,
			$this->url_base.'&sub=&id='
		);
	}

	protected function get_search_mode() {
		return 0;
	}

	protected function get_aut_type() {
		return "";
	}

	protected function get_last_order() {
		return 'order by author_id desc ';
	}

	public function get_back_url() {
		global $type_autorite;

		$this->back_url = parent::get_back_url();
		if($type_autorite) $this->back_url .= "&type_autorite=".$type_autorite;
		return $this->back_url;
	}

	public function get_delete_url() {
		global $type_autorite;

		$this->delete_url = parent::get_delete_url();
		if($type_autorite) $this->delete_url .= "&type_autorite=".$type_autorite;
		return $this->delete_url;
	}

	protected function get_pagination_link() {
		global $type_autorite;

		return parent::get_pagination_link().($type_autorite ? "&type_autorite=".$type_autorite : '');
	}

	protected function get_aut_const(){
	    return TYPE_AUTHOR;
	}
}
