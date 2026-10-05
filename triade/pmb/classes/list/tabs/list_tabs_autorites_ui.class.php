<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_tabs_autorites_ui.class.php,v 1.4 2023/12/27 08:04:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/authperso.class.php");

class list_tabs_autorites_ui extends list_tabs_ui {
	
	protected function _init_tabs() {
		global $pmb_use_uniform_title, $thesaurus_concepts_active;
		
		//Recherche
		$this->add_tab('search', 'search', 'search_authorities');
		$this->add_tab('search', 'search_perso', 'search_perso_menu');
		
		//Autorites
		$this->add_tab('132', 'auteurs', '133', 'author');
		if (SESSrights & THESAURUS_AUTH) {
			$this->add_tab('132', 'categories', '134', 'category', '&parent=0&id=0');
		}
		$this->add_tab('132', 'editeurs', '135', 'publisher');
		$this->add_tab('132', 'collections', '136', 'collection');
		$this->add_tab('132', 'souscollections', '137', 'subcollection');
		$this->add_tab('132', 'series', '333', 'serie');
		if ($pmb_use_uniform_title) {
			$this->add_tab('132', 'titres_uniformes', 'aut_menu_titre_uniforme', 'titre_uniforme');
		}
		$this->add_tab('132', 'indexint', 'indexint_menu', 'indexint');
		if ($thesaurus_concepts_active==true && (SESSrights & CONCEPTS_AUTH)) {
			$this->add_tab('132', 'concepts', 'ontology_skos_menu', 'concept');
		}
		$authpersos = new authpersos();
		if(count($authpersos->info)) {
			foreach($authpersos->info as $elt){
			    $this->add_tab('132', 'authperso', $elt['name'], 'authperso', '&id_authperso='.$elt['id'], $elt['id']);
			}
		}
		
		//Paniers
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_gestion');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_collecte', 'collecte');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_pointage', 'pointage');
		$this->add_tab('caddie_menu', 'caddie', 'caddie_menu_action', 'action');
		
		//Semantique
		$this->add_tab('semantique', 'semantique', 'word_syn_menu', 'synonyms');
		$this->add_tab('semantique', 'semantique', 'empty_words_libelle', 'empty_words');
	
		//Import
		$this->add_tab('authorities_gest', 'import', 'authorities_import');
	}
	
	protected function is_active_tab($label_code, $categ, $sub='') {
		switch (true) {
		    case ($categ == "authperso") : 
		        if ((isset($_GET['sub']) && $sub == $_GET['sub'])) {
		            if ((func_get_arg(4) !== false) && (!empty($_GET['id']))) {
		                $authperso_num = func_get_arg(4);
	                    $authperso_auth = new authperso_authority($_GET['id']);
	                    if ($authperso_num == $authperso_auth->get_authperso_num()) {
	                        return true;
	                    }
		            }
		        } else {
    		        if (func_get_arg(3) !== false) {
    		            $url_extra = func_get_arg(3);
    		            $params = explode("&", $url_extra);
    		            foreach ($params as $param) {
    		                $sub_param = explode("=", $param);
    		                if ($sub_param[0] == "id_authperso") {
    		                    if ($this->is_equal_var_get("id_authperso", $sub_param[1])) {
    		                        return true;
    		                    }
    		                }
    		            }
    		        }
		        }
		        break;
		    case ($label_code == "ontology_skos_menu") : 
		        if($this->is_equal_var_get('categ', ['concepts', 'see']) && $this->is_equal_var_get('sub', array("concept", "conceptscheme", "collection", "orderedcollection"))) {
		            return true;
		        }
		        break;
		    case (isset($_GET['sub']) && $sub == $_GET['sub']) :
		        return true;
		    case (isset($_GET['categ']) && $categ == $_GET['categ']) :
		        return true;
		}
		return false;
	}
	
	public function get_display_tab($object) {
	    return "<li id='".static::$module_name."_menu_".$object->get_label_code()."' ".($this->is_active_tab($object->get_label_code(), $object->get_categ(), $object->get_sub(), $object->get_url_extra(), $object->get_number()) ? "class='active'" : "" ).">
			<a href='".$object->get_destination_link()."'>
				".$object->get_label()."
			</a>
		</li>";
	}
}