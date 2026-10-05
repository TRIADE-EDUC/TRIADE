<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_controller.class.php,v 1.17.4.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Digitalsignature\Models\DocnumCertifier;

global $class_path;
require_once($class_path."/acces.class.php");
require_once($class_path."/onto/onto_pmb_entities_mapping.class.php");
require_once($class_path."/frbr/cataloging/frbr_cataloging_scheme.class.php");

class entities_controller {

	protected $id;

	protected $model_class_name = '';

	protected $url_base = '';

	protected $action = '';

	protected $dom_1;

	protected $error_message = '';

	protected $back_url = '';

	protected $delete_url = '';

	public function __construct($id=0) {
	    $this->id = intval($id);
	}

	public function get_model_class_name() {
		return $this->model_class_name;
	}

	public function get_object_instance() {
		$model_class_name = $this->get_model_class_name();
		$object_instance = new $model_class_name($this->id);
		if(method_exists($model_class_name, 'set_controller')) {
			$model_class_name::set_controller($this);
		}
		return $object_instance;
	}

	public function get_url_base() {
		return $this->url_base;
	}

	public function set_url_base($url_base) {
		$this->url_base = $url_base;
	}

	public function set_action($action) {
		$this->action = $action;
	}

	protected function display_error_message() {
		global $charset;

		error_message('', htmlentities($this->dom_1->getComment($this->error_message), ENT_QUOTES, $charset), 1, '');
	}

	public function has_rights() {
		global $gestion_acces_active, $gestion_acces_user_notice;

		$acces_m=1;
		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
			$ac= new acces();
			$this->dom_1= $ac->setDomain(1);
			$acces_m = $this->get_acces_m();
		}
		if ($acces_m==0) {
			return false;
		}
		return true;
	}

	protected function get_redirection_form() {
		global $current_module;

		return
			"<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"".$this->get_permalink()."\" style=\"display:none\">
				<input type=\"hidden\" name=\"id_form\" value=\"".md5(microtime())."\">
			</form>
			<script type=\"text/javascript\">document.dummy.submit();</script>";
	}

	public function proceed_explnum_form() {
	}

	public function proceed_explnum_update() {
		global $msg;
		global $f_notice, $f_bulletin, $f_nom, $f_url;
		global $conservervignette, $f_statut_chk, $f_explnum_statut;
		global $pmb_digital_signature_activate;

		//Vérification des champs personalisés
		$p_perso=new parametres_perso("explnum");
		$nberrors=$p_perso->check_submited_fields();
		if ($nberrors) {
			error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
			exit();
		}

		$explnum = new explnum($this->id);

		if ($pmb_digital_signature_activate) {
		    $docNumCertifier = new DocnumCertifier($explnum);
		    $check = $docNumCertifier->checkSignExists();
		    if ($check) {
		        return print return_error_message($msg["540"], $msg["digital_signature_already_signed_docnum_del"], 1, "./catalog.php?categ=isbd&id=".$explnum->explnum_notice);
		    }
		}

		$explnum->set_p_perso($p_perso);
		$explnum->mise_a_jour($f_notice, $f_bulletin, $f_nom, $f_url, $this->get_permalink(), $conservervignette, $f_statut_chk, $f_explnum_statut);
	}

	public function proceed_explnum_delete() {
	    global $msg;

		$explnum = new explnum($this->id);
		print "<div class=\"row\"><div class=\"msg-perio\">".$msg['catalog_notices_suppression']."</div></div>";
		$explnum->delete();

		print $this->get_redirection_form();
	}

	public function get_id() {
		return $this->id;
	}

	public function get_back_url() {
		global $base_path;
		global $user_input;
		global $page, $nbr_lignes;

		$short_referer = $base_path."/".substr($_SERVER["HTTP_REFERER"], strrpos($_SERVER["HTTP_REFERER"], "/")+1);
		if(strpos($short_referer, '&sub=replace')) {
			if(isset($_SESSION['PMB_STAKE_SHORT_REFERER']) && $_SESSION['PMB_STAKE_SHORT_REFERER']) {
				$this->back_url = $_SESSION['PMB_STAKE_SHORT_REFERER'];
			}
		}
		if(!$this->back_url) {
			$_SESSION['PMB_STAKE_SHORT_REFERER'] = $base_path."/".substr($_SERVER["HTTP_REFERER"], strrpos($_SERVER["HTTP_REFERER"], "/")+1);

			//Par défaut on imagine qu'on vient de la recherche
			$this->back_url = $this->get_url_base()."&sub=reach";
			if($user_input) $this->back_url .= "&user_input=".rawurlencode(stripslashes($user_input));
			if($page) $this->back_url .= "&page=".$page;
			if($nbr_lignes) $this->back_url .= "&nbr_lignes=".$nbr_lignes;

			//sinon on vient de la fiche de l'autorité
			//Dans le cas des autorités perso le get_permalink ne correspond pas à la page d'affichage dela notice de l'autorité
			if (strrpos($_SERVER["HTTP_REFERER"], "authperso") !== FALSE) {
			    if ($this->id && $_SESSION['PMB_STAKE_SHORT_REFERER'] == $this->get_display_permalink()) {
    			    $this->back_url = $this->get_display_permalink();
			    }
			} else if($this->id && $_SESSION['PMB_STAKE_SHORT_REFERER'] == $this->get_permalink()) {
				$this->back_url = $this->get_permalink();
			}
		}
		return $this->back_url;
	}

	public function get_delete_url() {
		global $user_input;
		global $page, $nbr_lignes;

		$this->delete_url = $this->get_url_base()."&sub=delete&id=".$this->id;
		if($user_input) $this->delete_url .= "&user_input=".rawurlencode(stripslashes($user_input));
		if($page) $this->delete_url .= "&page=".$page;
		if($nbr_lignes) $this->delete_url .= "&nbr_lignes=".$nbr_lignes;
		return $this->delete_url;
	}

	protected function get_cataloging_scheme_link_script($entity) {
		global $cataloging_scheme_id, $cataloging_scheme_level;

		if (!isset($cataloging_scheme_level)) {
			$cataloging_scheme_level = 0;
		}

		$rdf_dom_mapping_links = onto_pmb_entities_mapping::get_entity_rdf_dom_mapping_links($entity);
		$rdf_dom_mapping_link_types = onto_pmb_entities_mapping::get_entity_rdf_dom_mapping_link_types($entity);

		$scheme = new frbr_cataloging_scheme($cataloging_scheme_id);
		$entity_links = $scheme->get_links();
		if (!isset($entity_links[$cataloging_scheme_level])) {
			return '';
		}

		$entity_links_types = $scheme->get_links_types();

		$link = "";
		if (isset($rdf_dom_mapping_links[$entity_links[$cataloging_scheme_level]])) {
			$link = $rdf_dom_mapping_links[$entity_links[$cataloging_scheme_level]];
		}
		$link_type = "";
		$link_type_value = "";

		if (isset($rdf_dom_mapping_link_types[$entity_links[$cataloging_scheme_level]]) && !empty($entity_links_types[$cataloging_scheme_level])) {
			$link_type = $rdf_dom_mapping_link_types[$entity_links[$cataloging_scheme_level]];
			$link_type_value = $entity_links_types[$cataloging_scheme_level];
		}

		return '
			<script type="text/javascript">
				window.addEventListener("load", ()=> {
					var linkType = "'.$link_type.'";
					var linkFormName = "'.$link.'";
					if (linkFormName) {
						var nodes = document.querySelectorAll("*[data-form-name="+linkFormName+"]");
						if (nodes.length) {
							let node = nodes[0];
							while ((node.parentNode) &&
								(node.parentNode.getAttribute("movable") != "yes") &&
								(node.parentNode.getAttribute("title") == null)) {
								node = node.parentNode;
							}
							let nodeButton = node.parentNode.querySelectorAll("input[type=button][value=\'...\']");
							if (nodeButton.length) {
								let parentNodeId = node.parentNode.id;
								let idExpand = 0;
								if (parentNodeId.indexOf("Child") !== -1) {
									idExpand = parentNodeId.slice(0,parentNodeId.indexOf("Child"));
								}
								if (idExpand) {
									expandBase(idExpand);
								}
								let nodeLinkType = document.getElementById(linkType);
								if (nodeLinkType && nodeLinkType.tagName === "SELECT") {
									nodeLinkType.value = "'.$link_type_value.'";
								}
								window.catalogingSchemeId = '.$cataloging_scheme_id.';
								window.catalogingSchemeLevel = '.$cataloging_scheme_level.';
								nodeButton[0].focus(true);
								nodeButton[0].click();
							}
						}
					}
				});
			</script>
		';

	}

	public function get_document_title() {
		return '';
	}

	public function set_document_title() {
		// Titre de la page
		$title = $this->get_document_title();
		if($title) {
			print '<script type="text/javascript">document.title = "'.addslashes(strip_tags(pmb_bidi($title))).'";</script>';
		}
	}

	/**
	 *
	 * @return int (8 = droits de modification)
	 */
	protected function get_acces_m()
	{
		return 1;
	}

	/**
	 *
	 * @param integer $id
	 * @return string
	 */
	protected function get_permalink($id=0) {
		return '';
	}

	/**
	 *
	 * @param integer $id
	 * @return string
	 */
	protected function get_display_permalink($id=0) {
		return '';
	}

	/**
	 *
	 * @param integer $id
	 * @return string
	 */
	protected function get_edit_link($id=0) {
		return '';
	}
}
