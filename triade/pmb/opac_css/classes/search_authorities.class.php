<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_authorities.class.php,v 1.14 2022/01/07 11:40:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion des recherches avancees des autorités
global $class_path;
require_once($class_path."/search.class.php");
require_once($class_path."/searcher/searcher_authorities_authors.class.php");
require_once($class_path."/searcher/searcher_authorities_authpersos.class.php");
require_once($class_path."/searcher/searcher_authorities_categories.class.php");
require_once($class_path."/searcher/searcher_authorities_collections.class.php");
require_once($class_path."/searcher/searcher_authorities_concepts.class.php");
require_once($class_path."/searcher/searcher_authorities_indexint.class.php");
require_once($class_path."/searcher/searcher_authorities_publishers.class.php");
require_once($class_path."/searcher/searcher_authorities_series.class.php");
require_once($class_path."/searcher/searcher_authorities_subcollections.class.php");
require_once($class_path."/searcher/searcher_authorities_tab.class.php");
require_once($class_path."/searcher/searcher_authorities_titres_uniformes.class.php");
require_once($class_path."/searcher/searcher_autorities_skos_concepts.class.php");

class search_authorities extends search {
    
    protected $hidden_form_name;    
	
	public function filter_searchtable_from_accessrights($table) {
		
	}
	
	protected function sort_results($table) {
		return $table;
	}
	
	protected function get_display_nb_results($nb_results) {
		global $msg;
		 
		return " => ".$nb_results." ".$msg["search_extended_authorities_found"]."<br />\n";
	}
	
	protected function show_objects_results($table, $has_sort) {
		global $search;
		global $nb_per_page;
		global $page;
		$start_page=$nb_per_page*$page;
		
		$query = "select ".$table.".*,authorities.num_object,authorities.type_object from ".$table.",authorities where authorities.id_authority=".$table.".id_authority";
		if(count($search) > 1 && !$has_sort) {
			//Tri à appliquer par défaut
		}		
		if (!empty($nb_per_page)) {
		    $query .= " limit ".$start_page.",".$nb_per_page;
		}
	
		$result=pmb_mysql_query($query);
		$objects_ids = array();
		while ($row=pmb_mysql_fetch_object($result)) {
			$objects_ids[] = $row->id_authority;
		}
		if(count($objects_ids)) {
		    $elements_class_name = $this->get_elements_list_ui_class_name();
		    $elements_instance_list_ui = new $elements_class_name($objects_ids, count($objects_ids), 1);
		    
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

	public static function get_join_and_clause_from_equation($type = AUT_TABLE_AUTHORS, $equation='') {
		
		$authority_join = '';
		$authority_clause = '';
		$authority_ids = array();
		if($equation) {
			$my_search = new search_authorities('search_fields_authorities_gestion');
			$my_search->unserialize_search(stripslashes($equation));
			$res = $my_search->make_search();
			$req="select * from ".$res ;
			$resultat=pmb_mysql_query($req);
			while($r=pmb_mysql_fetch_object($resultat)) {
				$authority_ids[]=$r->id_authority;
			}
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
			$authority_join =' JOIN authorities on num_object = '.$aut_id_name.' and type_object = '.$type.' ';
			if (count($authority_ids)) {
				$authority_clause = ' and authorities.id_authority IN ('.implode(',',$authority_ids).') ';
			}else {
				$authority_clause = ' and authorities.id_authority IN (0) ';
			}
		}
		return array(
			'join' => $authority_join,
			'clause' => $authority_clause
		);
	}
	
	public function show_results($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
	    global $begin_result_liste;
	    global $search;
	    global $msg;
	    global $opac_nb_max_tri;
	    global $debug;
	    //Y-a-t-il des champs ?
	    if (count($search)==0) {
	        array_pop($_SESSION["session_history"]);
	        error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
	        exit();
	    }
	    $recherche_externe=true;//Savoir si l'on peut faire une recherche externe à partir des critères choisis
	    //Verification des champs vides
	    for ($i=0; $i<count($search); $i++) {
	        $op=$this->get_global_value("op_".$i."_".$search[$i]);
	        
	        $field=$this->get_global_value("field_".$i."_".$search[$i]);
	        
	        $field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");
	        
	        $s=explode("_",$search[$i]);
	        $bool=false;
	        if ($s[0]=="f") {
	            $champ=$this->fixedfields[$s[1]]["TITLE"];
	            if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
	                $bool=true;
	            }
	        } elseif(array_key_exists($s[0],$this->pp)) {
	            $champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
	            if ($this->is_empty($field, "field_".$i."_".$search[$i]) && $this->is_empty($field1, "field_".$i."_".$search[$i]."_1")) {
	                $bool=true;
	            }
	        } elseif($s[0]=="s") {
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
	    $table=$this->make_search();
	    
	    if ($acces==true) {
	        $this->filter_searchtable_from_accessrights($table);
	    }
	    
	    $requete="select count(1) from $table";
	    if($res=pmb_mysql_query($requete)){
	        $nb_results=pmb_mysql_result($res,0,0);
	    }else{
	        $query_data = array_pop($_SESSION["session_history"]);
	        error_message_history("",$msg["search_impossible"], 1);
	        print $this->get_back_button($query_data);
	        exit();
	    }
	    
	    //gestion du tri
	    $has_sort = false;
	    if ($nb_results <= $opac_nb_max_tri) {
	        if (isset($_SESSION["tri"])) {
	            $table = $this->sort_results($table);
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
	    
	    $human_requete = $this->make_human_query();
	    print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
	    if ($debug) print "<br />".$this->serialize_search();
	    if ($nb_results) {
	        print $this->get_display_nb_results($nb_results);
	        print $begin_result_liste;
	        print $this->get_display_icons($nb_results, $recherche_externe);
	    } else print "<br />".$msg["no_result"]." ";
	    
	    self::get_caddie_link();
	    
	    print "<input type='button' class='bouton' onClick=\"document.".$this->get_hidden_form_name().".action='".$url_to_search_form."'; document.".$this->get_hidden_form_name().".target='".$search_target."'; document.".$this->get_hidden_form_name().".submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
	    print $this->get_display_actions();
	    
	    print $this->get_current_search_map();
	    
	    $this->show_objects_results($table, $has_sort);
	    
	    $this->get_navbar($nb_results, $hidden_form);
	}
	
	protected function get_hidden_form_name(){
	    if(!isset($this->hidden_form_name)){
	        $this->hidden_form_name = 'search_form_'.md5(microtime());
	    }
	    return $this->hidden_form_name;
	}
		
	public static function get_caddie_link() {
	    //global $msg;
	    //print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=".$_SESSION['CURRENT']."&action=print_prepare&object_type=".self::get_type_from_mode()."&authorities_caddie=1','print_cart'); return false;\"><img src='".get_url_icon('basket_small_20x20.gif')."' style='border:0px' class='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;";
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
	 * @return string
	 */
	public static function get_sort_link($nb_results, $entity_type) {
	    print entities_authorities_controller::get_sort_link($nb_results, $entity_type);
	}
}
?>