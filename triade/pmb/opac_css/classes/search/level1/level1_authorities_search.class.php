<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level1_authorities_search.class.php,v 1.8.2.2 2024/06/12 07:10:51 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/level1_search.class.php");

class level1_authorities_search extends level1_search {

	protected $clause;

	protected $tri;

	protected $pert;

	protected $members;

	protected function get_hidden_search_form_name() {
    	$form_name = '';
    	switch ($this->type) {
    		case 'categories':
    			$form_name .= "search_categorie";
    			break;
    		case 'collections':
    			$form_name .= "search_collection";
    			break;
    		case 'subcollections':
    			$form_name .= "search_sub_collection";
    			break;
    		default:
    			$form_name .= parent::get_hidden_search_form_name();
    			break;

    	}
    	return $form_name;
    }

    protected function get_hidden_search_content_form() {
    	$content_form = parent::get_hidden_search_content_form();
    	switch ($this->type) {
    		case 'categories':
    			global $opac_thesaurus, $opac_thesaurus_default;
    			$content_form .= "<input type=\"hidden\" name=\"id_thes\" value=\"".($opac_thesaurus ? -1 : $opac_thesaurus_default)."\">";
    			break;
    	}
    	return $content_form;
    }

    protected function get_tri() {
    	if(!isset($this->tri)) {
    		$this->tri = 'order by pert desc';
    		switch ($this->type) {
    			case 'authors':
    				$this->tri .= ', index_author';
    				break;
    			case 'publishers':
    				$this->tri .= ', index_publisher';
    				break;
    			case 'collections':
    				$this->tri .= ', index_coll';
    				break;
				case 'subcollections':
					$this->tri .= ', index_sub_coll';
    				break;
				case 'indexint':
					$this->tri .= ', index_indexint';
					break;
				case 'titres_uniformes':
					$this->tri .= ', index_tu';
					break;
				case 'authperso':
					$this->tri .= ', authperso_index_infos_global';
					break;
    		}
    	}
    	return $this->tri;
    }

    public function get_nb_results() {
    	if(!isset($this->nb_results)) {

    	    $searcher = $this->get_searcher_instance();
    	    if(is_object($searcher)){
    	        $this->nb_results = $searcher->get_nb_results();
    	    }
	    	if($this->nb_results) {
	    		$this->add_in_session();
	    	}
    	}
    	return $this->nb_results;
    }

    protected function get_mode() {
    	switch ($this->type) {
    		case 'authors':
    			return "auteur";
    			break;
    		case 'categories':
    			return "categorie";
    			break;
    		case 'publishers':
    			return "editeur";
    			break;
    		case 'collections':
    			return "collection";
    			break;
			case 'subcollections':
				return 'souscollection';
				break;
			case 'indexint':
				return 'indexint';
				break;
			case 'titres_uniformes':
				return 'titre_uniforme';
				break;
			case 'concepts':
				return 'concept';
				break;
			case 'authperso':
				return 'authperso';
				break;
    	}
    }

    protected function get_affiliate_mode() {
    	switch ($this->type) {
    		case 'authors':
    			return "auteur";
    			break;
    		case 'categories':
    			return "category";
    			break;
    		case 'publishers':
    			return "publisher";
    			break;
    		case 'collections':
    			return "collection";
    			break;
    		case 'subcollections':
    			return 'subcollection';
    			break;
    		case 'indexint':
    			return 'indexint';
    			break;
    		case 'titres_uniformes':
    			return 'titre_uniforme';
    			break;
			case 'concepts':
				return 'concept';
				break;
    	}
    }

    protected function get_session_key() {
    	switch ($this->type) {
    		case 'authors':
    			return 'author';
    			break;
    		case 'categories':
    			return 'category';
    			break;
    		case 'publishers':
    			return 'publisher';
    			break;
    		case 'collections':
    			return 'collection';
    			break;
			case 'subcollections':
				return 'subcollection';
				break;
			case 'indexint':
				return 'indexint';
				break;
			case 'titres_uniformes':
				return 'titre_uniforme';
				break;
			case 'concepts':
				return 'concept';
				break;
    	}
    }

    protected function add_in_session() {
        if(!is_array($_SESSION["level1"][$this->get_session_key()])) {
            $_SESSION["level1"][$this->get_session_key()] = [];
        }
    	$_SESSION["level1"][$this->get_session_key()]["form"] = $this->get_hidden_search_form();;
    	$_SESSION["level1"][$this->get_session_key()]["count"] = $this->get_nb_results();
    }

    protected function get_search_type() {
    	return 'authorities';
    }

    public function get_searcher_instance() {
        if($this->type == 'concepts'){
            return new opac_searcher_autorities_skos_concepts($this->user_query);
        }
        return searcher_factory::get_searcher($this->type, '', $this->user_query);
    }

    protected function get_authority_type_const(){
        switch($this->type){
            case "authors" :
                return AUT_TABLE_AUTHORS;
            case "publishers" :
                return AUT_TABLE_PUBLISHERS;
            case "collections" :
                return AUT_TABLE_COLLECTIONS;
            case "subcollections" :
                return AUT_TABLE_SUB_COLLECTIONS;
            case "series" :
                return AUT_TABLE_SERIES;
            case "indexints" :
                return AUT_TABLE_INDEXINT;
            case "titres_uniformes" :
                return AUT_TABLE_TITRES_UNIFORMES;
            case "categories" :
                return AUT_TABLE_CATEG;
            case "concepts" :
                return AUT_TABLE_CONCEPT;
            case "authpersos" :
                return AUT_TABLE_AUTHPERSO;
        }
    }

    protected function get_display_link_affiliate_js(string $search_form)
    {
        global $opac_rgaa_active, $msg;

        if ($opac_rgaa_active) {
            return "
                let button = document.createElement('button');
            	button.setAttribute('type', 'button');
            	button.classList.add('search_result');
        		button.innerHTML = \"".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />\";

        		button.addEventListener('click',function(){
        			document.$search_form.action='".$this->get_form_action()."&tab=affiliate';
        			document.$search_form.submit();
        			return false;
        		},true);

        		div.appendChild(button);
		    ";
        }
        return "
            var a = document.createElement('a');
			a.setAttribute('href','#');
			a.innerHTML = \"".$msg['suite']."&nbsp;<img src='".get_url_icon('search.gif')."' style='border:0px' />\";

			a.addEventListener('click',function(){
				document.$search_form.action='".$this->get_form_action()."&tab=affiliate';
				document.$search_form.submit();
				return false;
			},true);

			div.appendChild(a);
        ";
    }
}
