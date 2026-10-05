<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_indexint_controller.class.php,v 1.14.2.1 2024/06/11 08:23:56 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;

require_once ($class_path."/entities/entities_authorities_controller.class.php");

// on a besoin des templates indexation interne
include($include_path.'/templates/indexint.tpl.php');

// la classe de gestion des indexation interne
require_once($class_path.'/indexint.class.php');
require_once($class_path.'/pclassement.class.php');
require_once($class_path."/configuration/configuration_controller.class.php");

class entities_indexint_controller extends entities_authorities_controller {

	protected $model_class_name = 'indexint';

	protected $id_pclass;

	public function get_object_instance() {
		$model_class_name = $this->get_model_class_name();
		$object_instance = new $model_class_name($this->id, $this->id_pclass);
		if(method_exists($model_class_name, 'set_controller')) {
			$model_class_name::set_controller($this);
		}
		return $object_instance;
	}

	public function set_id_pclass($id_pclass=0) {
	    $this->id_pclass = (int) $id_pclass;
	}

	public function proceed() {
		global $sub, $action;
		global $id_pclass;

		switch($sub) {
			case 'pclass':
				if($action == 'add') {
					$id_pclass = 0;
				}
				configuration_controller::set_model_class_name('pclassement');
				configuration_controller::set_list_ui_class_name('list_configuration_indexint_pclass_ui');
				configuration_controller::proceed($id_pclass);
				break;
			default:
				parent::proceed();
				break;
		}
	}

	public function proceed_replace() {
		global $msg;
		global $n_indexint_id, $aut_link_save;

		$object_instance = $this->get_object_instance();
		if(!$n_indexint_id) {
			$object_instance->replace_form();
		}else {
			// routine de remplacement
			$rep_result = $object_instance->replace($n_indexint_id,$aut_link_save);
			if(!$rep_result) {
				print $this->get_display_list();
			}else {
				error_message($msg[132], $rep_result, 1, $this->get_edit_link());
			}
		}
	}

	public function proceed_update() {
		global $msg;
		global $indexint_nom, $indexint_comment, $indexint_pclassement;
		global $authority_statut, $authority_thumbnail_url;

		// mettre à jour
		$object_instance = $this->get_object_instance();
		$updated = $object_instance->update($indexint_nom, $indexint_comment, $indexint_pclassement, $authority_statut, $authority_thumbnail_url);
		if($object_instance->get_cp_error_message()){
			error_message($msg['indexint_create'], $object_instance->get_cp_error_message(), 1, $this->get_edit_link());
		}elseif($updated) {
			return $object_instance->indexint_id;
		}
		return 0;
	}

	public function get_searcher_instance() {
		global $exact;

		$exact = intval($exact);
		$indexint_searcher = searcher_factory::get_searcher('indexint', '', $this->user_input);
		$fields_restrict = array();
		if (!$exact) {
			$fields_restrict[]= array(
					'field' => "code_champ",
					'values' => array(8002),
					'op' => "and",
					'not' => false
			);
		} else {
			$fields_restrict[]= array(
					'field' => "code_champ",
					'values' => array(8001),
					'op' => "and",
					'not' => false
			);
		}
		$indexint_searcher->add_fields_restrict($fields_restrict);
		return $indexint_searcher;
	}

	protected function search_form() {
		global $id_pclass;

		$id_pclass = intval($id_pclass);
		$model_class_name = $this->get_model_class_name();
		$model_class_name::search_form($id_pclass);
	}

	protected function get_pagination_link() {
		global $thesaurus_classement_mode_pmb;
		global $thesaurus_classement_defaut;
		global $id_pclass;
		global $exact;

		$link = parent::get_pagination_link();
		if ($thesaurus_classement_mode_pmb != 0) {
			if($id_pclass!=0) {
				$link .= "&id_pclass=$id_pclass";
			}
		} else {
			$link .= "&id_pclass=$thesaurus_classement_defaut";
		}
		$link .= "&exact=".$exact;
		return $link;
	}

	protected function get_display_header_list() {
		global $msg;

		$display = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			<th>".$msg[707]."</th>
			<th>".$msg["count_notices_assoc"]."</th>
			<th></th>
		</tr>";
		return $display;
	}

	protected function get_display_columns() {
		global $thesaurus_classement_mode_pmb;
		global $charset;

		$object_instance = $this->authority->get_object_instance();

		if($thesaurus_classement_mode_pmb!=0){
			$pclass_name="[".$object_instance->name_pclass."] ";
		} else {
			$pclass_name="";
		}
		$display = $this->get_display_label_column($pclass_name.htmlentities($object_instance->name,ENT_QUOTES, $charset));
		$display .= "
			<td style='vertical-align:top' onmousedown=\"document.location='".$this->get_edit_link($this->authority->get_num_object())."&user_input=".rawurlencode($this->user_input)."&nbr_lignes=".$this->nbr_lignes."&page=".$this->page."';\">
				".htmlentities($object_instance->comment,ENT_QUOTES, $charset)."
			</td>";
		return $display;
	}
	protected function get_query_notice_count() {
		return "SELECT count(*) FROM notices WHERE indexint = ".$this->authority->get_num_object();
	}

	protected function get_permalink($id=0) {
		if(!$id) $id = $this->id;
		return "./autorites.php?categ=see&sub=indexint&id=".$id;
	}

	protected function get_edit_link($id=0) {
		global $thesaurus_classement_mode_pmb;
		global $thesaurus_classement_defaut;
		global $id_pclass;
		global $exact;

		if(!$id) $id = $this->id;
		$link = '';
		if ($thesaurus_classement_mode_pmb != 0) {
			if($id_pclass!=0) {
				$link .= "&id_pclass=$id_pclass";
			}
		} else {
			$link .= "&id_pclass=$thesaurus_classement_defaut";
		}
		return $this->url_base."&sub=indexint_form&id=".$id."&exact=".$exact.$link;
	}

	protected function get_results_title() {
		global $msg;
		global $exact;

		if ($this->user_input) {
			if ($exact)
				$c_user_input= $msg["rech_exacte"];
			else
				$c_user_input=$msg["rech_commentaire"];
		} else {
			$c_user_input = '';
		}
		return $msg['indexint_found']." ".$c_user_input;
	}

	protected function display_no_results() {
		global $msg, $charset;

		error_message(
			$msg['indexint_search'],
			str_replace('!!titre_cle!!', htmlentities($this->user_input, ENT_QUOTES, $charset), $msg['indexint_noresult']),
			0,
			$this->url_base.'&sub=&id='
		);
	}

	protected function get_search_mode() {
		return 1;
	}

	protected function get_aut_type() {
		return "indexint";
	}

	protected function get_last_order() {
		return 'order by indexint_id desc ';
	}

	public function get_back_url() {
		global $exact;

		$this->back_url = parent::get_back_url();
		if($this->id_pclass) $this->back_url .= "&id_pclass=".$this->id_pclass;
		if($exact) $this->back_url .= "&exact=".$exact;
		return $this->back_url;
	}

	public function get_delete_url() {
		global $exact;

		$this->delete_url = parent::get_delete_url();
		if($this->id_pclass) $this->delete_url .= "&id_pclass=".$this->id_pclass;
		if($exact) $this->delete_url .= "&exact=".$exact;
		return $this->delete_url;
	}

	protected function get_aut_const(){
	    return TYPE_INDEXINT;
	}
}
