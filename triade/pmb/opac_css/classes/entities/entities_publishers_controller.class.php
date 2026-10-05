<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_publishers_controller.class.php,v 1.1.14.1 2024/06/11 08:23:55 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/entities/entities_authorities_controller.class.php");

// on a besoin des templates éditeurs
include($include_path.'/templates/editeurs.tpl.php');

// la classe de gestion des éditeurs
require_once($class_path.'/editor.class.php');

class entities_publishers_controller extends entities_authorities_controller {

	protected $model_class_name = 'editeur';

	public function proceed_replace() {
		global $msg;
		global $ed_id, $aut_link_save;

		$object_instance = $this->get_object_instance();
		if(!$ed_id) {
			$object_instance->replace_form();
		}else {
			// routine de remplacement
			$rep_result = $object_instance->replace($ed_id,$aut_link_save);
			if(!$rep_result) {
				print $this->get_display_list();
			}else {
				error_message($msg[132], $rep_result, 1, $this->get_edit_link());
			}
		}
	}

	public function proceed_update() {
		global $msg;
		global $ed_nom;
		global $ed_adr1, $ed_adr2, $ed_cp, $ed_ville, $ed_pays;
		global $ed_comment, $ed_web, $id_fou;
		global $authority_statut, $authority_thumbnail_url;

		// mise à jour d'un éditeur
		$ed = array(
				'name' => $ed_nom,
				'adr1' => $ed_adr1,
				'adr2' => $ed_adr2,
				'cp' => $ed_cp,
				'ville' => $ed_ville,
				'pays' => $ed_pays,
				'ed_comment'	=> $ed_comment,
				'statut'	=> $authority_statut,
				'web' => $ed_web,
				'id_fou' => $id_fou,
				'thumbnail_url' => $authority_thumbnail_url
		);
		$object_instance = $this->get_object_instance();
		$object_instance->update($ed);
		if($object_instance->get_cp_error_message()){//Traitement des messages d'erreurs champs persos
			error_message($msg['145'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
			return 0;
		}else{
			return $object_instance->id;
		}
	}

	public function get_searcher_instance() {
		return searcher_factory::get_searcher('publishers', '', $this->user_input);
	}

	protected function get_display_header_list() {
		global $msg;

		$this->num_auth_present = searcher_authorities_publishers::has_authorities_sources('publisher');

		$display = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			<th>".$msg[72]."</th>
			<th>".$msg[147]."</th>
			<th>".$msg["count_notices_assoc"]."</th>
            <th></th>
		</tr>";
		return $display;
	}

	protected function get_display_columns() {
		global $charset;

		$object_instance = $this->authority->get_object_instance();

		$display = $this->get_display_label_column(htmlentities($object_instance->name,ENT_QUOTES,$charset));
		$display.= "<td style='vertical-align:top' onmousedown=\"document.location='".$this->get_permalink($this->authority->get_num_object())."';\">";
		$affcall='';
		if ($object_instance->ville || $object_instance->pays) {
			if ($object_instance->ville) {
				$affcall.=$object_instance->ville;
				if($object_instance->pays) $affcall.=' - ';
			}
			$affcall.=$object_instance->pays;
		}
		$display.= htmlentities($affcall,ENT_QUOTES,$charset);

		$display.= "</td>
					<td class='align_right'>";

		if($object_instance->web) {
			$display .= "<a href='$object_instance->web' target='_new'>".htmlentities($object_instance->web,ENT_QUOTES,$charset)."</a>";
		}else {
			$display .= '&nbsp;';
		}
		$display .= "</td>";
		return $display;
	}

	protected function get_query_notice_count() {
		return "SELECT count(*) FROM notices WHERE ed1_id = ".$this->authority->get_num_object()." or ed2_id = ".$this->authority->get_num_object();
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=publisher&id=".$id;
	}

	protected function get_edit_link($id=0) {
		if(!$id) $id = $this->id;
		return $this->url_base."&sub=editeur_form&id=".$id;
	}

	protected function get_results_title() {
		global $msg;

		return $msg[154];
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg[152],
			str_replace('!!ed_cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg[153]),
			0,
			$this->url_base.'&sub=&id='
		);
	}

	protected function get_search_mode() {
		return 2;
	}

	protected function get_aut_type() {
		return "publisher";
	}

	protected function get_last_order() {
		return 'order by ed_id desc ';
	}

	protected function get_aut_const(){
	    return TYPE_PUBLISHER;
	}
}
