<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: level2_authorities_search.class.php,v 1.14.2.1 2024/06/12 07:10:52 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/level2_search.class.php");
require_once($class_path."/elements_list/elements_authorities_list_ui.class.php");
require_once($class_path."/searcher/opac_searcher_autorities_skos_concepts.class.php");
require_once($class_path."/thesaurus.class.php");

class level2_authorities_search extends level2_search {

	protected $query;

	protected function get_title() {
    	global $msg;

    	$title = '';
    	switch($this->type) {
    		case 'authors':
    			global $author_type;
				switch($author_type) {
					case '71':
						$title .= $msg["collectivites_found"];
						break;
					case '72':
						$title .= $msg["congres_found"];
						break;
					case '70':
					default:
						$title .= $msg["authors_found"];
						break;
				}
    			break;
    		case 'subcollections':
    			$title .= $msg['subcolls_found'];
    			break;
    		case 'categories':
    			$title .= $msg['categs_found'];
    			break;
    		case 'authperso':
    			global $name;
    			$title .= $name;
    			break;
    		case 'extended' :
    			$es = new search_authorities("search_fields_authorities");
    			$title = $es->make_human_query();
    			break;
    		default:
    			$title .= parent::get_title();
    			break;
    	}
    	return $title;
    }

    protected function get_categories_query() {
    	global $opac_stemming_active;
    	global $id_thes;
    	global $lang, $default_tmp_storage_engine;

    	$first_clause = "catdef.libelle_categorie not like '~%' ";

    	$aq=new analyse_query($this->user_query,0,0,1,0,$opac_stemming_active);
    	$members_catdef = $aq->get_query_members('catdef','catdef.libelle_categorie','catdef.index_categorie','catdef.num_noeud');

    	$list_thes = array();
    	if ($id_thes == -1) {
    		//recherche dans tous les thesaurus
    		$list_thes = thesaurus::getThesaurusList();
    	} else {
    		//recherche dans le thesaurus transmis
    		$thes = new thesaurus($id_thes);
    		$list_thes[$id_thes]=$thes->libelle_thesaurus;
    	}

    	$q = "drop table if exists catjoin ";
    	$r = pmb_mysql_query($q);

    	$query = "create temporary table catjoin ENGINE={$default_tmp_storage_engine} as select
    			";
    	foreach ($list_thes as $id_thesaurus=>$libelle_thesaurus) {
    		$thes = new thesaurus($id_thesaurus);
    		$query .= "
    			noeuds.id_noeud as num_noeud,
    			noeuds.num_thesaurus,
    			".$members_catdef['select']." as pert
    			from noeuds
    		";
    		$query .= "join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";

    		$query .= "where noeuds.num_thesaurus = '".$thes->id_thesaurus."' ";
    		$query .= "and ".$first_clause." ";
    		$query .= "and ".$members_catdef['where']." ";
    		pmb_mysql_query($query);
    		$query = "INSERT INTO catjoin SELECT ";
    	}
    	return "select id_authority as id, catjoin.pert from catjoin JOIN authorities ON catjoin.num_noeud = authorities.num_object AND type_object = ".AUT_TABLE_CATEG;
    }

    protected function get_query() {
    	global $pert, $clause, $tri, $limiter;
    	if(!isset($this->query)){
    	$query = "select id_authority as id, ".stripslashes($pert)." from authorities ";
    	switch($this->type) {
    		case 'authors':
    			$query .= "JOIN authors ON author_id = authorities.num_object AND type_object = ".AUT_TABLE_AUTHORS;
    			global $type;
    			if($type) {
    				$query .= " AND author_type='$type'";
    			}
    			break;
    		case 'categories':
    			$query = ""; //Spécifique aux catégories
    			$query .= $this->get_categories_query();
    			break;
    		case 'publishers':
    			$query .= "JOIN publishers ON ed_id = authorities.num_object AND type_object = ".AUT_TABLE_PUBLISHERS;
    			break;
    		case 'collections':
    			$query .= "JOIN collections ON coll_id = authorities.num_object AND type_object = ".AUT_TABLE_COLLECTIONS;
    			break;
    		case 'subcollections':
    			$query .= "JOIN sub_collections ON sub_coll_id = authorities.num_object AND type_object = ".AUT_TABLE_SUB_COLLECTIONS;
    			break;
    		case 'indexint':
    			$query .= "JOIN indexint ON indexint_id = authorities.num_object AND type_object = ".AUT_TABLE_INDEXINT;
    			break;
    		case 'titres_uniformes':
    			$query .= "JOIN titres_uniformes ON tu_id = authorities.num_object AND type_object = ".AUT_TABLE_TITRES_UNIFORMES;
    			break;
    		case 'authperso':
    			$query .= "JOIN authperso_authorities ON id_authperso_authority = authorities.num_object AND type_object = ".AUT_TABLE_AUTHPERSO;
				break;
    		case 'extended' :
    			$searcher = new searcher_authorities_extended();
    			$searcher->get_result();
    			$query = "select id_authority as id from ".$searcher->table;
    			break;
    	}
    	$query .= " ".stripslashes($clause)." group by id_authority ".$tri." ".$limiter;
	    	$this->query = $query;
    	}
    	return $this->query;
    }

    protected function get_permalink($id) {

    	$permalink = 'index.php?lvl=';
    	switch($this->type) {
    		case 'authors':
    			$permalink .= "author_see";
    			break;
    		case 'categories':
    			$permalink .= "categ_see";
    			break;
    		case 'publishers':
    			$permalink .= "publisher_see";
    			break;
    		case 'collections':
    			$permalink .= "coll_see";
    			break;
    		case 'subcollections':
    			$permalink .= "subcoll_see";
    			break;
    		case 'indexint':
    			$permalink .= "indexint_see";
    			break;
    		case 'titres_uniformes':
    		    $permalink .= "titre_uniforme_see";
    		    break;
    		case 'concepts':
    		    $permalink .= "concept_see";
    			break;
			case 'authperso':
				$permalink .= "authperso_see";
				break;
    	}
    	$permalink .= "&id=".$id."&from=search";
    	return $permalink;
    }

    protected function get_display_element($element) {
    	$display = '';
    	switch($this->type) {
    		case 'indexint':
    			$display .= "<a href='".$this->get_permalink($element->indexint_id)."'><img src='".get_url_icon('folder.gif')."' style='border:0px'/> ".$element->indexint_name." ".$element->indexint_comment."</a>";
    			break;
    		default:
    			$display .= "<li class='categ_colonne'><span class='notice_fort'><a href='".$this->get_permalink($element->id)."'>".$element->name."</a></span></li>";
    			break;
    	}
    	return $display;
    }

    protected function get_display_elements_list() {
        global $page,$opac_search_results_per_page;
        global $count;

        $this->elements_ids = array();
        $searcher = $this->get_searcher_instance();
        if(is_object($searcher)){

		    if(!$page) {
		        $debut = 0;
		    } else {
		        $debut = ($page-1)*$opac_search_results_per_page;
		    }
		    $this->elements_ids = $searcher->get_sorted_result("default",$debut,$opac_search_results_per_page);
		    $count = count(array_filter(explode(',', $searcher->get_objects_ids()))); // pour affichage du nombre de résultats
		}
    	$elements_authorities_list_ui = new elements_authorities_list_ui($this->elements_ids, count($this->elements_ids), false);
    	return $elements_authorities_list_ui->get_elements_list();
    }

    protected function search_affiliate() {
    	global $tab;
    	global $pmb_logs_activate;

    	if($tab == "affiliate"){
    		//l'onglet source affiliées est actif, il faut son contenu...
    		switch($this->type) {
    			case 'authors':
    				$as=new affiliate_search_author($this->user_query,"authorities");
    				global $author_type;
    				$as->filter = $author_type;
    				$affiliate_indice = 'author_affiliate';
    				break;
    			case 'categories':
    				$as=new affiliate_search_category($this->user_query,"authorities");
    				$affiliate_indice = 'category_affiliate';
    				break;
    			case 'publishers':
    				$as=new affiliate_search_publisher($this->user_query,"authorities");
    				$affiliate_indice = 'publisher_affiliate';
    				break;
    			case 'collections':
    				$as=new affiliate_search_collection($this->user_query,"authorities");
    				$affiliate_indice = 'collection_affiliate';
    				break;
				case 'subcollections':
					$as=new affiliate_search_subcollection($this->user_query,"authorities");
					$affiliate_indice = 'subcollection_affiliate';
					break;
				case 'indexint':
					$as=new affiliate_search_indexint($this->user_query,"authorities");
					$affiliate_indice = 'indexint_affiliate';
					break;
				case 'titres_uniformes':
					$as=new affiliate_search_titre_uniforme($this->user_query,"authorities");
					$affiliate_indice = 'titres_uniformes_affiliate';
					break;
				case 'concepts':
					$as=new affiliate_search_concept($this->user_query,"authorities");
					$affiliate_indice = 'concept_affiliate';
					break;
    		}
    		print $as->getResults();
    	}
    	print "
			</div>
			<div class='row'>&nbsp;</div>";
    	//Enregistrement des stats
    	if($pmb_logs_activate){
    		global $nb_results_tab;
    		if($this->type == 'authors') {
    			foreach($as->getNbResults() as $type => $nb){
    				switch($type){
    					case "authors":
    						$nb_results_tab['physiques'] = $nb;
    						break;
    					case "coll":
    						$nb_results_tab['collectivites'] = $nb;
    						break;
    					case "congres":
    						$nb_results_tab['congres'] = $nb;
    						break;
    				}
    			}
    		} elseif($this->type == 'authperso') {
    			global $mode;
    			$nb_results_tab[$mode] = $nb;
    		} else {
    			$nb_results_tab[$affiliate_indice] = $as->getTotalNbResults();
    		}
    	}
    }

    /**
     * Enregistrement des stats
     */
    protected function search_log($count) {
    	global $nb_results_tab;

    	if($this->type == 'authors') {
			global $author_type;
			switch($author_type) {
				case '71':
					$nb_results_tab['collectivites'] = $count;
					break;
				case '72':
					$nb_results_tab['congres'] = $count;
					break;
				case '70':
				default:
					$nb_results_tab['physiques'] = $count;
					break;
			}
		} elseif($this->type == 'authperso') {
			global $mode;
			$nb_results_tab[$mode] = $count;
		} else {
			parent::search_log($count);
		}
    }

    public function get_search_title(){
        global $msg, $count;
        if($this->type == "extended"){
            return pmb_bidi("<h3 class='searchResult-search' id='searchResult-search' ><span class='searchResult-equation'><span class='search-found'>".$count." ".$msg["authorities_found"]."</span> '".$this->get_title()."'</span></h3>");
        }
        return parent::get_search_title();
    }

    public function set_query($query){
        $this->query = $query;
    }

    public function get_searcher_instance() {
         if($this->type == 'concepts'){
             $obj = new searcher_autorities_skos_concepts($this->user_query);
             return $obj;
         }
        $obj = searcher_factory::get_searcher($this->type, '', $this->user_query);
        return $obj;
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
}
?>