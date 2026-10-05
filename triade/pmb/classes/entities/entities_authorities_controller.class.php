<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: entities_authorities_controller.class.php,v 1.25.4.1 2025/04/24 09:50:00 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($class_path."/entities/entities_controller.class.php");
global $pmb_indexation_lang;
include($include_path."/marc_tables/".$pmb_indexation_lang."/empty_words");
require_once($class_path."/analyse_query.class.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($include_path.'/user_error.inc.php');
require_once($class_path.'/entity_locking.class.php');
require_once($class_path.'/authority.class.php');

class entities_authorities_controller extends entities_controller {

	protected $user_input;

	protected $authority;

	protected $searcher_instance;

	protected $nbr_lignes;

	protected $page;

	protected $parity;
	protected $num_auth_present;

	public function __construct($id) {
		global $user_input;
		if($user_input) {
			$this->user_input = stripslashes($user_input);
		} else {
			$this->user_input = '';
		}
		parent::__construct($id);
	}

	protected function get_display_label_column($label='', $infobulle='') {
		$display = "
			<td style='vertical-align:top' onmousedown=\"document.location='".$this->get_edit_link($this->authority->get_num_object())."&user_input=".rawurlencode($this->user_input)."&nbr_lignes=".$this->nbr_lignes."&page=".$this->page."';\" title='".$infobulle."'>
				".$this->authority->get_display_statut_class_html().$label."
			</td>";
		return $display;
	}

	protected function get_display_line($authority_id=0) {
		global $msg;

		$display = '';

		// On va chercher les infos spécifique à l'autorité
		$this->authority = new authority($authority_id);

		if ($this->parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$this->parity += 1;

		if(static::class == 'entities_categories_controller') {
			$notice_count = $this->get_query_notice_count();
		} else {
			$notice_count_sql = $this->get_query_notice_count();
			$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
		}

		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
		$display.= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
		$display.= "<td style='text-align:center; width:25px;'>
        				<a title='".$msg['authority_list_see_label']."' href='".$this->get_permalink($this->authority->get_num_object())."'>
        					<i class='fa fa-eye'></i>
        				</a>
        			</td>";
		$display .= $this->get_display_columns();
		if($notice_count) {
			$display .=  "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=".$this->get_search_mode()."&etat=aut_search&aut_type=".$this->get_aut_type()."&aut_id=".$this->authority->get_num_object()."'\">".$notice_count."</td>";
		} else {
			$display .= "<td>&nbsp;</td>";
		}
		$display.= '<td>'.$this->authority->get_caddie().'</td>';
		$display .=  "</tr>";
		return $display;
	}

	protected function search_form() {
		$model_class_name = $this->get_model_class_name();
		$model_class_name::search_form();
	}

	protected function get_pagination_link() {
		global $authority_statut;

		return $this->url_base."&sub=reach&user_input=".rawurlencode($this->user_input).'&authority_statut='.$authority_statut;
	}

	public function get_display_list() {
		global $page, $nb_per_page_gestion, $categ;
		global $last_param;

		$display = '';

		if(!$this->user_input) $this->user_input = '*';

		$this->search_form();

		$this->searcher_instance = $this->get_searcher_instance();
		$this->nbr_lignes = $this->searcher_instance->get_nb_results();

		if(!$page) {
			$page=1;
			$this->page = $page;
		} else {
		    $this->page = (int) $page;
		}
		$debut =($this->page-1)*$nb_per_page_gestion;

		if($this->nbr_lignes) {
			$display .= $this->get_display_header_list();

			$this->parity=1;
			$sorted_objects = $this->searcher_instance->get_sorted_result('default', $debut, $nb_per_page_gestion);
			$this->set_session_history($this->searcher_instance->get_human_query(), $categ, 'QUERY', 'classic');
			$this->set_session_history($this->searcher_instance->get_human_query(), $categ, 'AUT', 'classic');
			if (is_array($sorted_objects)) {
    			foreach ($sorted_objects as $authority_id) {
    				$display .= $this->get_display_line($authority_id);
    			} // fin while
			}
			if (!$last_param) $nav_bar = aff_pagination ($this->get_pagination_link(), $this->nbr_lignes, $nb_per_page_gestion, $this->page, 10, false, true) ;
			else $nav_bar="";

			// affichage du résultat
			print $this->searcher_instance->get_results_list_from_search($this->get_results_title(), $this->user_input, $display, $nav_bar);
		} else {
			// la requête n'a produit aucun résultat
			$this->display_no_results();
		}
	}

	public function proceed() {
		global $sub;
		global $force_unlock;
		global $PMBuserid, $save_and_continue;
		//parade pour la facto
		$formatted_sub = $sub;
		if($sub) {
			$exploded_sub = explode('_', $sub);
			if(isset($exploded_sub[1])) {
				if($exploded_sub[1] == 'form') $formatted_sub = 'form';
				if($exploded_sub[1] == 'last') $formatted_sub = 'last';
			}
		}
		switch($formatted_sub) {
			case 'reach':
				print $this->get_display_list();
				break;
			case 'delete':
			    $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
			    // On déclenche un événement sur la supression
			    $evt_handler = events_handler::get_instance();
			    $event = new event_entity("entity", "has_deletion_rights");
			    $event->set_entity_id($this->id);
			    $event->set_entity_type($this->get_aut_const());
			    $event->set_user_id($PMBuserid);
			    $evt_handler->send($event);
			    if($event->get_error_message()){
			    	information_message('', $event->get_error_message(), 1, $this->get_permalink());
			    } else {
			    	$this->proceed_delete();
			    }
				break;
			case 'replace':
			    $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			    if($entity_locking->is_locked()){
			        print $entity_locking->get_locked_form();
			        break;
			    }
		        $this->proceed_replace();
				break;
			case 'duplicate' :
				$this->proceed_duplicate();
				break;
			case 'update':
				$entity_locking = new entity_locking($this->id, $this->get_aut_const());
				if ($this->id && $entity_locking->is_locked()) {
				    if($PMBuserid == $entity_locking->get_locked_user_id()){
				        $updated_id = $this->proceed_update();
				        $entity_locking->unlock_entity();
				    }else{
				        print $entity_locking->get_save_error_message();
				        break;
				    }
				} else{
				    $updated_id = $this->proceed_update();
				}
				if($updated_id) {
				    if ($save_and_continue) {
				        $this->id = 0;
				        $this->proceed_form();
				    } else {
				        print $this->get_display_view($updated_id);
				    }
				}
				break;
			case 'form':
			    if($this->id){
			        $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			        if($entity_locking->is_locked()){
			            print $entity_locking->get_locked_form();
			            break;
			        }
			    }
		        $this->proceed_form();
				break;
			case 'last':
				$this->proceed_last();
				break;
			case 'unlock':
			    $entity_locking = new entity_locking($this->id, $this->get_aut_const());
			    $entity_locking->unlock_entity();
			    break;
			default:
				$this->proceed_default();
				break;
		}
	}

	public function proceed_delete() {
	    global $msg;

		$object_instance = $this->get_object_instance();
		$sup_result = $object_instance->delete();
		if(!$sup_result) {
			print $this->get_display_list();
		}else {
			error_message($msg[132], $sup_result, 1, $this->get_edit_link());
		}
	}

	public function proceed_replace() {
		global $msg;
		global $by, $aut_link_save;

		$object_instance = $this->get_object_instance();
		if(!$by) {
			$object_instance->replace_form();
		}else {
			// routine de remplacement
			$rep_result = $object_instance->replace($by,$aut_link_save);
			if(!$rep_result) {
				print $this->get_display_list();
			}else {
				error_message($msg[132], $rep_result, 1, $this->get_edit_link());
			}
		}
	}

	public function proceed_duplicate() {
		$object_instance = $this->get_object_instance();
		$object_instance->show_form(true);
	}

	public function proceed_update() {
	}

	public function proceed_form() {
	    global $cataloging_scheme_id, $id;
	    $unlock_unload_script = "";
	    if($this->id){
	        $entity_locking = new entity_locking($id, $this->get_type_const());
	        $entity_locking->lock_entity();
	        $unlock_unload_script = $entity_locking->get_polling_script();
	    }
		$object_instance = $this->get_object_instance();
		ob_start();
		$object_instance->show_form();
		$entity_form = ob_get_contents();
		ob_end_clean();
		$entity_form = str_replace('<form', '<form data-advanced-form="true"', $entity_form);

		if ($cataloging_scheme_id) {
			$entity_form.= $this->get_cataloging_scheme_link_script($this->get_model_class_name());
		}
		print $entity_form;
		print $this->get_selector_js_script();
		print $unlock_unload_script;
	}

	public function proceed_last() {
		global $last_param;
		global $tri_param, $limit_param;
		global $pmb_nb_lastautorities;
		global $clef, $nbr_lignes;

		$last_param=1;
		$tri_param = $this->get_last_order();
		$limit_param = 'limit 0, '.$pmb_nb_lastautorities;
		$clef = '';
		$nbr_lignes = 0 ;
		print $this->get_display_list();
	}

	public function proceed_default() {
		global $pmb_allow_authorities_first_page;

		if(!$pmb_allow_authorities_first_page && (!isset($this->user_input) || $this->user_input == '')){
			$this->search_form();
		}else {
			// affichage du début de la liste
			print $this->get_display_list();
		}
	}

	public function get_display_view($id=0) {
		print "<script type='text/javascript'>
			document.location = '".$this->get_permalink($id)."';
			</script>";
	}
	/**
	 * Fourni le javascript permettant d'instancier le systeme d'onglet
	 */
	protected function get_selector_js_script(){
		return "<script type='text/javascript'>
					require(['dojo/ready', 'apps/pmb/form/FormController'], function(ready, FormController){
					     ready(function(){
					     	new FormController();
					     });
					});
				</script>";
	}

	public static function get_caddie_link() {
		global $msg, $categ, $user_input;
		//Pas de lien vers le panier si pas de recherche
		if (!isset($user_input) || $user_input==='') {
		    return '';
		}
		return "<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=".$_SESSION['CURRENT']."&action=print_prepare&object_type=".self::get_type_from_categ($categ)."&authorities_caddie=1','print_cart'); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>";
	}

	/**
	 * Retourne le template pour appliquer un tri
	 * @param int|string $nb_results nombre de résultat de la recherce
	 * @param string $entity_type type de l'entité
	 * @param boolean $popup utilisation d'une popup
	 * @return string
	 */
	public static function get_sort_link($nb_results, $entity_type, $popup = false) {
	    global $pmb_nb_max_tri, $msg;
	    global $affich_authorities_tris_result_liste, $affich_authorities_popup_tris_result_liste;

	    $display_icons = "";
	    if ($nb_results <= $pmb_nb_max_tri) {
	        $sort_index = "tri_".$entity_type;
	        if ($popup) {
	            $display_icons .= $affich_authorities_popup_tris_result_liste;
	        } else {
    	        $display_icons .= $affich_authorities_tris_result_liste;
	        }
	        $display_icons = str_replace('!!entity_type!!', $entity_type, $display_icons);
	        $display_icons = str_replace('!!sort_params!!', static::get_sort_params_link(), $display_icons);
	        if (!empty($_SESSION[$sort_index])) {
	            $sort = new sort($entity_type,"base");
	            $display_icons .= $msg['tri_par']." ".$sort->descriptionTriParId($_SESSION[$sort_index]);
	        }

	    }

	    return $display_icons;
	}

	/**
	 * parametres a ajouter sur l'url de tri
	 * @return string
	 */
	protected static function get_sort_params_link() {
	    return "";
	}

	/**
	 *
	 * @param string $human_query
	 * @param string $categ
	 * @param string $type
	 * @param string $search_type
	 */
	protected function set_session_history($human_query, $categ, $type, $search_type = "extended") {
		global $page, $msg, $id_authperso;

		if(!isset($_SESSION["session_history"])) $_SESSION["session_history"] = array();
		switch ($type) {
			case 'QUERY' :
				$_SESSION["CURRENT"]=count($_SESSION["session_history"]);
				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["URI"] = './'.$this->url_base;
				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"] = $_POST;
				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"] = $_GET;
				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"]["sub"] = "";
				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"]["sub"] = "";
				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_QUERY"] = $human_query;
				$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_TITLE"] = "[".$msg["132"]."] ".$this->get_msg_from_categ($categ, (isset($id_authperso) ? $id_authperso : 0));
				break;
			case 'AUT' :
				if ($_SESSION["CURRENT"] !== false) {
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["URI"] = './'.$this->url_base;
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["PAGE"] = $page;
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["POST"] = $_POST;
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["GET"] = $_GET;
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_QUERY"] = $human_query;
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["SEARCH_TYPE"] = $search_type;
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["SEARCH_OBJECTS_TYPE"] = $this->get_type_from_categ($categ);
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["HUMAN_TITLE"] = "[".$msg["132"]."] ".$this->get_msg_from_categ($categ, (isset($id_authperso) ? $id_authperso : 0));
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]['TEXT_LIST_QUERY']='';
					$_SESSION["session_history"][$_SESSION["CURRENT"]][$type]["TEXT_QUERY"] = "";
				}
				break;
		}
	}

	public function get_msg_from_categ($categ, $id_authperso = 0) {
		global $msg;

		switch ($categ) {
			case 'auteurs' :
				return $msg['133'];
			case 'categories' :
				return $msg['134'];
			case 'editeurs' :
				return $msg['135'];
			case 'collections' :
				return $msg['136'];
			case 'souscollections' :
				return $msg['137'];
			case 'series' :
				return $msg['333'];
			case 'titres_uniformes' :
				return $msg['aut_menu_titre_uniforme'];
			case 'indexint' :
				return $msg['indexint_menu'];
			case 'concepts' :
				return $msg['ontology_skos_menu'];
			case 'authperso' :
				return authpersos::get_name($id_authperso);
		}
		return '';
	}

	public static function get_type_from_categ($categ) {

		$type = "MIXED";
		switch ($categ) {
			case 'auteurs' :
				$type = "AUTHORS";
				break;
			case 'categories' :
				$type = "CATEGORIES";
				break;
			case 'editeurs' :
				$type = "PUBLISHERS";
				break;
			case 'collections' :
				$type = "COLLECTIONS";
				break;
			case 'souscollections' :
				$type = "SUBCOLLECTIONS";
				break;
			case 'series' :
				$type = "SERIES";
				break;
			case 'titres_uniformes' :
				$type = "TITRES_UNIFORMES";
				break;
			case 'indexint' :
				$type = "INDEXINT";
				break;
			case 'concepts' :
				$type = "CONCEPTS";
				break;
			case 'authperso' :
				$type = "AUTHPERSO";
				break;
		}
		return $type;
	}

	//A dériver dans les enfants
	protected function get_aut_const(){
	    return '';
	}

	public function get_type_const() {
	    switch($this->get_model_class_name()) {
	        case 'auteur':
	            return TYPE_AUTHOR;
	        case 'collection':
	            return TYPE_COLLECTION;
	        case 'authperso':
	            return TYPE_AUTHPERSO;
	        case 'category':
	            return TYPE_CATEGORY;
	        case 'indexint':
	            return TYPE_INDEXINT;
	        case 'concept':
	            return TYPE_CONCEPT;
	        case 'editeur':
	            return TYPE_PUBLISHER;
	        case 'serie':
	            return TYPE_SERIE;
	        case 'subcollection':
	            return TYPE_SUBCOLLECTION;
	        case 'titre_uniforme':
	            return TYPE_TITRE_UNIFORME;
	    }
	}

	/**
	 *
	 * @return string
	 */
	protected function get_query_notice_count() {
		return '';
	}

	/**
	 *
	 * @return string
	 */
	protected function get_display_columns() {
		return '';
	}

	/**
	 *
	 * @return int
	 */
	protected function get_search_mode() {
		return 0;
	}

	/**
	 *
	 * @return string
	 */
	protected function get_aut_type() {
		return "";
	}

	/**
	 *
	 * @return searcher_autorities|searcher_sphinx_authorities
	 */
	public function get_searcher_instance()	{
		return searcher_factory::get_searcher('authorities', '', $this->user_input);
	}

	/**
	 *
	 * @return string
	 */
	protected function get_display_header_list() {
		return '';
	}

	/**
	 *
	 * @return string
	 */
	protected function get_results_title() {
		return '';
	}

	/**
	 *
	 * @return void
	 */
	protected function display_no_results() {}

	/**
	 *
	 * @return string
	 */
	protected function get_last_order() {
		return '';
	}
}
