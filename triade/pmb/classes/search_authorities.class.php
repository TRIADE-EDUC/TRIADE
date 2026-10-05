<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_authorities.class.php,v 1.43.4.2 2025/01/24 16:34:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion des recherches avancees des autorités
global $class_path;
require_once($class_path."/search.class.php");

class search_authorities extends search {

	protected $hidden_form_name;

	protected $sort_entity_type;

	protected $sort_index;

	public function filter_searchtable_from_accessrights($table) {
		global $gestion_acces_active,$gestion_acces_user_authority;

		if($gestion_acces_active && $gestion_acces_user_authority){
			//En vue de droits d'accès
		}
	}

	public function filter_searchtable_without_no_display($table) {
		global $no_display;
		global $what;

		if($no_display) {
			switch($what) {
				case 'auteur':
					$authority_type = AUT_TABLE_AUTHORS;
					break;
				case 'editeur':
					$authority_type = AUT_TABLE_PUBLISHERS;
					break;
				case 'collection':
					$authority_type = AUT_TABLE_COLLECTIONS;
					break;
				case 'subcollection':
					$authority_type = AUT_TABLE_SUB_COLLECTIONS;
					break;
				case 'categorie':
					$authority_type = AUT_TABLE_CATEG;
					break;
				case 'serie':
					$authority_type = AUT_TABLE_SERIES;
					break;
				case 'indexint':
					$authority_type = AUT_TABLE_INDEXINT;
					break;
				case 'titre_uniforme':
					$authority_type = AUT_TABLE_TITRES_UNIFORMES;
					break;
				case 'authperso':
					$authority_type = AUT_TABLE_AUTHPERSO;
					break;
			}
			$authority = new authority(0, $no_display, $authority_type);
			if(is_object($authority)) {
				$requete = "delete from ".$table." using ".$table." ";
				$requete.= "where ";
				$requete.= $table.".".$this->keyName." = ".$authority->get_id()." ";
				pmb_mysql_query($requete);
			}
		}
	}

	protected function sort_results($table) {
	    global $nb_per_page_search;
	    global $page;

	    $page = intval($page);
	    if($page) {
	        $start_page = $nb_per_page_search * ($page-1);
	    } else {
	        $start_page = 0;
	    }

	    $sort = new sort($this->sort_entity_type, 'base');
	    $sort->appliquer_tri($_SESSION[$this->sort_index], "SELECT * FROM " . $table, "id_authority", $start_page, $nb_per_page_search);

	    return $sort->table_tri_tempo;
	}

	protected function get_display_nb_results($nb_results) {
		global $msg;

		return " => ".$nb_results." ".$msg["search_extended_authorities_found"]."<br />\n";
	}

	protected function show_objects_results($table, $has_sort) {
		global $search;
		global $nb_per_page_search;
		global $page;

		$page = intval($page);
		if($page) {
		    $start_page = $nb_per_page_search * ($page-1);
		} else {
		    $start_page = 0;
		}

		$query = "select ".$table.".*,authorities.num_object,authorities.type_object from ".$table.",authorities where authorities.id_authority=".$table.".id_authority";
		if(count($search) > 1 && !$has_sort) {
			//Tri à appliquer par défaut
		}
		$query .= " limit ".$start_page.",".$nb_per_page_search;

		$result = pmb_mysql_query($query);
		$objects_ids = array();
		while ($row=pmb_mysql_fetch_object($result)) {
			$objects_ids[] = $row->id_authority;
		}
		if(count($objects_ids)) {
			$elements_class_name = $this->get_elements_list_ui_class_name();
			$elements_instance_list_ui = new $elements_class_name($objects_ids, count($objects_ids), 1);
			$elements_instance_list_ui->add_context_parameter('in_search', true);
			$elements = $elements_instance_list_ui->get_elements_list();
			print $elements;
		}
	}

	protected function get_display_actions() {
		return "";
	}

	protected function get_display_icons($nb_results, $recherche_externe = false) {
		return "";
	}

	protected static function init_session_facets($table) {
	    $objects = "";
	    $result = pmb_mysql_query('SELECT * FROM '.$table);
	    while($row = pmb_mysql_fetch_object($result)){
	        if($objects){
	            $objects .= ",";
	        }
	        $objects .= $row->id_authority;
	    }
	    session::set_value('search', ['authorities' => ['extended_search' => $objects]]);
	    facettes::set_facet_type('authorities');
	}

	public function show_results($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
		global $begin_result_liste;
		global $search;
		global $msg;
		global $pmb_nb_max_tri;
		global $debug;
		global $nb_per_page_search;

		//Y-a-t-il des champs ?
		if (count($search)==0) {
			array_pop($_SESSION["session_history"]);
			error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
			exit();
		}

		//Savoir si l'on peut faire une recherche externe à partir des critères choisis
		$recherche_externe=true;

		// Permet de savoir sur quelle entités on vas appliquer un tri
		$apply_sort_entities = array();

		//Verification des champs vides
		for ($i=0; $i < count($search); $i++) {

			$op=$this->get_global_value("op_".$i."_".$search[$i]);
			$field=$this->get_global_value("field_".$i."_".$search[$i]);
			$field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");

			$s=explode("_",$search[$i]);
			$bool=false;

			// Recherche tous les champs sur une autorité perso
			if ($s[0] == "authperso" && !in_array("authperso", $apply_sort_entities)) {
			    $apply_sort_entities[] = "authperso";
			}

			if ($s[0]=="f") {

			    if (!empty($this->fixedfields[$s[1]]['GROUP']) && !in_array($this->groups[$this->fixedfields[$s[1]]['GROUP']]['objects_type'], $apply_sort_entities)) {
			        $apply_sort_entities[] = $this->groups[$this->fixedfields[$s[1]]['GROUP']]['objects_type'];
			    } elseif (empty($this->fixedfields[$s[1]]['GROUP'])) {
        			if (!in_array("authperso", $apply_sort_entities)) {
        			    $apply_sort_entities[] = "authperso";
        			}
			    }

				$champ=$this->fixedfields[$s[1]]["TITLE"];
				if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
					$bool=true;
				}

			} elseif (array_key_exists($s[0],$this->pp)) {

			    $sort_type = $this->pp[$s[0]]->get_sort_type();
			    if (!in_array($sort_type, $apply_sort_entities)) {
			        $apply_sort_entities[] = $sort_type;
			    }

			    $champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
				if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
					$bool=true;
				}
			} elseif($s[0]=="s") {

			    if (!empty($this->specialfields[$s[1]]['GROUP']) && !in_array($this->groups[$this->specialfields[$s[1]]['GROUP']]['objects_type'], $apply_sort_entities)) {
			        $apply_sort_entities[] = $this->groups[$this->specialfields[$s[1]]['GROUP']]['objects_type'];
			    }

				$recherche_externe=false;
				$champ=$this->specialfields[$s[1]]["TITLE"];
				$type=$this->specialfields[$s[1]]["TYPE"];
				for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
			}//elseif (substr($s,0,9)=="authperso") {}
			if (($bool)&&(!$this->op_empty[$op])) {
				$query_data = array_pop($_SESSION["session_history"]);
				error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
				print $this->get_back_button($query_data);
				exit();
			}
		}

		$table = $this->make_search();

		if ($acces == true) {
			$this->filter_searchtable_from_accessrights($table);
		}

		if(!empty($this->context_parameters['in_selector'])) {
			$this->filter_searchtable_without_no_display($table);
		}

		$res = pmb_mysql_query("select count(1) from $table");
		if($res) {
			$nb_results = pmb_mysql_result($res,0,0);
		} else {
			$query_data = array_pop($_SESSION["session_history"]);
			error_message_history("", $msg["search_impossible"], 1);
			print $this->get_back_button($query_data);
			exit();
		}

		// gestion du tri
		$entity_type = "mixed";
		if (count($apply_sort_entities) == 1) {
		    $entity_type = $apply_sort_entities[0];
		    // on est sur une recherche d'autorités mixtes
		    if($entity_type == 'authorities') {
		    	$entity_type = "mixed";
		    }
		}
		$this->sort_entity_type = $entity_type;

		$has_sort = false;
		if ($nb_results <= $pmb_nb_max_tri) {
		    $this->sort_index = "tri_" . $entity_type;
		    if (array_key_exists($this->sort_index, $_SESSION)) {
		        $table = $this->sort_results($table, $entity_type);
				$has_sort = true;
			}
		}
		// fin gestion tri


		//Y-a-t-il une erreur lors de la recherche ?
		if ($this->error_message) {
			$query_data = array_pop($_SESSION["session_history"]);
			error_message_history("", $this->error_message, 1);
			print $this->get_back_button($query_data);
			exit();
		}

		if ($hidden_form) {
			print $this->make_hidden_search_form($url,$this->get_hidden_form_name(),"",false);
			print facette_search_compare::form_write_facette_compare();
			print "</form>";
		}

		static::init_session_facets($table);

		$human_requete = $this->make_human_query();
		print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
		if ($debug) print "<br />".$this->serialize_search();

		if ($nb_results) {
			print $this->get_display_nb_results($nb_results);
			print $begin_result_liste;
			print $this->get_display_icons($nb_results, $recherche_externe);
		} else {
		    print "<br />".$msg["1915"]." ";
		}

	    // Template de panier
		self::get_caddie_link();
		// Template de tri
		self::get_sort_link($nb_results, $entity_type);

		if(empty($this->context_parameters['in_selector'])) {
    		print searcher::get_quick_actions('AUT');
    		print "<input type='button' class='bouton' onClick=\"document.".$this->get_hidden_form_name().".action='".$url_to_search_form."'; document.".$this->get_hidden_form_name().".target='".$search_target."'; document.".$this->get_hidden_form_name().".submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
    		print $this->get_display_actions();
		}

		print searcher::get_check_uncheck_all_buttons();
		facettes::set_hidden_form_name($this->get_hidden_form_name());
		facettes::set_elements_list_nb_per_page($nb_per_page_search);
		print "
        <div class='row'>"
		    .$this->get_current_search_map()."
        </div>
        <div class='content_details'>
            <div id='facettes_list' class='facettes_list'>".facettes::call_ajax_facettes()."</div>
                <div id='results_list' class='results_list'>";
		    $this->show_objects_results($table, $has_sort);
		    print "</div>
        </div>";

		$this->get_navbar($nb_results, $hidden_form);
	}

	public static function get_caddie_link() {
		global $msg;
		print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=".$_SESSION['CURRENT']."&action=print_prepare&object_type=".self::get_type_from_mode()."&authorities_caddie=1','print_cart'); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;";
	}

	public static function get_type_from_mode() {
		global $mode;

		$type = "MIXED";
		switch ($mode) {
			case 1 :
				$type = "AUTHORS";
				break;
			case 2 :
				$type = "CATEGORIES";
				break;
			case 3 :
				$type = "PUBLISHERS";
				break;
			case 4 :
				$type = "COLLECTIONS";
				break;
			case 5 :
				$type = "SUBCOLLECTIONS";
				break;
			case 6 :
				$type = "SERIES";
				break;
			case 7 :
				$type = "TITRES_UNIFORMES";
				break;
			case 8 :
				$type = "INDEXINT";
				break;
			case 9 :
				$type = "CONCEPTS";
				break;
		}
		return $type;
	}

	public function get_elements_list_ui_class_name() {
		if(!isset($this->elements_list_ui_class_name)) {
			$this->elements_list_ui_class_name = "elements_authorities_list_ui";
		}
		return $this->elements_list_ui_class_name;
	}

	protected function get_hidden_form_name(){
		if(!isset($this->hidden_form_name)){
			$this->hidden_form_name = 'search_form_'.md5(microtime());
		}
		return $this->hidden_form_name;
	}

	public function generate_query_op_and($prefixe = "", $suffixe = "", $search_table = "") {
	    if ($prefixe) {
	        return "create temporary table ".$prefixe."and_result_".$suffixe." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select ".$prefixe."mf_".$suffixe.".* from ".$prefixe."mf_".$suffixe." where ".$search_table.".id_authority=".$prefixe."mf_".$suffixe.".id_authority)";
	    } else {
	        return "create temporary table and_result_".$suffixe." ENGINE=".$this->current_engine." select ".$search_table.".* from ".$search_table." where exists ( select mf_".$suffixe.".* from mf_".$suffixe." where ".$search_table.".id_authority=mf_".$suffixe.".id_authority)";
	    }
	}

	/**
	 * Retourne le template pour appliquer un tri
	 * @param int|string $nb_results nombre de résultat de la recherce
	 * @param string $entity_type type de l'entité
	 * @param boolean $popup utilisation d'une popup
	 * @return string
	 */
	public static function get_sort_link($nb_results, $entity_type, $popup = false) {
	    print entities_authorities_controller::get_sort_link($nb_results, $entity_type, $popup);
	}

	/**
	 * Retourne la clé primaire du type d'entité
	 *
	 * @param int $type
	 * @return string
	 */
	public static function get_aut_key_name($type) {
		switch($type) {
			case AUT_TABLE_AUTHORS :
				$aut_id_name = 'author_id';
				break;
			case AUT_TABLE_CATEG :
				$aut_id_name = 'id_noeud';
				break;
			case AUT_TABLE_PUBLISHERS :
				$aut_id_name = 'ed_id';
				break;
			case AUT_TABLE_COLLECTIONS :
				$aut_id_name = 'collection_id';
				break;
			case AUT_TABLE_SUB_COLLECTIONS :
				$aut_id_name = 'sub_coll_id';
				break;
			case AUT_TABLE_SERIES :
				$aut_id_name = 'serie_id';
				break;
			case AUT_TABLE_TITRES_UNIFORMES :
				$aut_id_name = 'tu_id';
				break;
			case AUT_TABLE_INDEXINT :
				$aut_id_name = 'indexint_id';
				break;
			case AUT_TABLE_CONCEPT :
				$aut_id_name = 'id_item';
				break;
			case AUT_TABLE_AUTHPERSO :
				// TODO
				break;
			default:
				$aut_id_name = 'author_id';
				break;
		}

		return $aut_id_name;
	}

	/**
	 * Retourne les requêtes 'join' pour l'équation
	 *
	 * @param int $type Type d'autorités
	 * @param string $equation Équation de recherche
	 * @return array [join, clause]
	 */
	public static function get_join_and_clause_from_equation($type = AUT_TABLE_AUTHORS, $equation='') {

	    $authority_join = '';
	    $authority_clause = '';
	    $authority_ids = array();

	    if ($equation) {
	        $my_search = new search_authorities(false, 'search_fields_authorities');
	        $my_search->unserialize_search(stripslashes($equation));
	        $tmp_table = $my_search->make_search();

	        $req="select id_authority from ".$tmp_table;
	        $resultat=pmb_mysql_query($req);
			if (pmb_mysql_num_rows($resultat)) {
				while($r = pmb_mysql_fetch_assoc($resultat)) {
					$authority_ids[] = $r['id_authority'];
				}
				pmb_mysql_free_result($resultat);
			}

	        $authority_join =' JOIN authorities on num_object = ' . self::get_aut_key_name($type) .' and type_object = '.intval($type).' ';
	        if (count($authority_ids)) {
	            $authority_clause = ' and authorities.id_authority IN ('.implode(',',$authority_ids).') ';
	        } else {
	            $authority_clause = ' and authorities.id_authority IN (0) ';
	        }
	    }

	    return array(
	        'join' => $authority_join,
	        'clause' => $authority_clause
	    );
	}

	/**
	 * Retourne les requêtes 'join' et 'clause' les statuts
	 *
	 * @param string $type
	 * @param boolean $with_equation
	 * @return array [join, clause]
	 */
	public static function get_join_and_clause_for_statuts($type = AUT_TABLE_AUTHORS, $with_equation = false)
	{
		global $module_from;

		$authority_join = "";
		$authority_clause = "";

		if (strpos($module_from, 'catalog') !== false) {
			$field = 'authorities_statuts_searcher_autority';
			if (strpos($module_from, 'autocomplete') !== false) {
				$field = 'authorities_statuts_autocomplete';
			}

			if (!$with_equation) {
				$authority_join .= ' JOIN authorities on num_object = '. self::get_aut_key_name($type) .' and type_object = '.intval($type).' ';
			}

			$authority_join .= ' JOIN authorities_statuts ON id_authorities_statut = num_statut ';
			$authority_clause .= ' AND ' . $field . ' = 1';
		}

	    return array(
	        'join' => $authority_join,
	        'clause' => $authority_clause
	    );
	}

	/**
	 * Retourne les requêtes 'join' et 'clause' pour l'équation et les statuts
	 *
	 * @param int $type
	 * @param string $equation
	 * @return array [join, clause]
	 */
	public static function get_join_and_clause($type = AUT_TABLE_AUTHORS, $equation='')
	{
		$result = self::get_join_and_clause_from_equation($type, $equation);
		$filter = self::get_join_and_clause_for_statuts($type, !empty($equation));

		$result['join'] .= $filter['join'];
		$result['clause'] .= $filter['clause'];

		return $result;
	}
}