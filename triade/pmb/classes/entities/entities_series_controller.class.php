<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_series_controller.class.php,v 1.6.8.1 2024/06/11 08:23:56 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/entities/entities_authorities_controller.class.php");

// on a besoin des templates séries
include($include_path.'/templates/series.tpl.php');

// la classe de gestion des séries
require_once($class_path.'/serie.class.php');

class entities_series_controller extends entities_authorities_controller {

	protected $model_class_name = 'serie';

	public function proceed_replace() {
		global $msg;
		global $n_serie_id, $aut_link_save;

		$object_instance = $this->get_object_instance();
		if(!$n_serie_id) {
			$object_instance->replace_form();
		}else {
			// routine de remplacement
			$rep_result = $object_instance->replace($n_serie_id,$aut_link_save);
			if(!$rep_result) {
				print $this->get_display_list();
			}else {
				error_message($msg[132], $rep_result, 1, $this->get_edit_link());
			}
		}
	}

	public function proceed_update() {
		global $msg;
		global $serie_nom;

		// mettre à jour titre de série id
		$object_instance = $this->get_object_instance();
		$updated = $object_instance->update($serie_nom);
		if($object_instance->get_cp_error_message()){
			error_message($msg['336'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
		}elseif($updated) {
			return $object_instance->s_id;
		}
		return 0;
	}

	public function get_searcher_instance() {
		return searcher_factory::get_searcher('series', '', $this->user_input);
	}

	protected function get_display_header_list() {
		global $msg;

		$this->num_auth_present = searcher_authorities_series::has_authorities_sources('serie');

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
		$object_instance = $this->authority->get_object_instance();

		$display = $this->get_display_label_column($object_instance->name);
		//Numéros d'autorite
		if($this->num_auth_present){
			$display .= "<td>".searcher_authorities_series::get_display_authorities_sources($this->authority->get_num_object(), 'serie')."</td>";
		}
		return $display;
	}

	protected function get_query_notice_count() {
		return "SELECT count(*) FROM notices WHERE tparent_id = ".$this->authority->get_num_object();
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=serie&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=serie_form&id=".$id;
	}

	protected function get_results_title() {
		global $msg;

		return $msg[334];
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg[152],
			str_replace('!!titre_cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg[335]),
			0,
			$this->url_base.'&sub=&id='
		);
	}

	protected function get_search_mode() {
		return 10;
	}

	protected function get_aut_type() {
		return "tit_serie";
	}

	protected function get_last_order() {
		return 'order by serie_id desc ';
	}

	protected function get_aut_const(){
	    return TYPE_SERIE;
	}
}
