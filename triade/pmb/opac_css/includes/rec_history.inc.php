<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rec_history.inc.php,v 1.74.2.3.2.1 2025/03/04 11:24:07 dgoron Exp $

use Pmb\AI\Models\AiSessionSemanticModel;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path,$include_path,$msg;
require_once($base_path."/classes/search.class.php");
require_once($base_path."/classes/authperso.class.php");
require_once($base_path."/classes/search_universes/search_universes_history.class.php");

//Enregistrement de l'historique en fonction du type de recherche
function rec_history() {
	global $search_type;
	global $opac_search_other_function;

	switch ($search_type) {
		case "simple_search":
			global $user_query;
			global $map_emprises_query;
			global $look_TITLE,
	       		$look_AUTHOR,
	      	 	$look_PUBLISHER,
	      	 	$look_TITRE_UNIFORME,
	       		$look_COLLECTION,
	       		$look_SUBCOLLECTION,
	       		$look_CATEGORY,
	       		$look_INDEXINT,
	       		$look_KEYWORDS,
	       		$look_ABSTRACT,
	       		$look_ALL,
	       		$look_DOCNUM,
	       		$look_CONTENT,
				$look_CONCEPT;
	       	global $typdoc,$l_typdoc;

			$_SESSION["nb_queries"]=intval($_SESSION["nb_queries"])+1;
			$n=$_SESSION["nb_queries"];
			$_SESSION["user_query".$n]=$user_query;
			$_SESSION["map_emprises_query".$n]=$map_emprises_query;
			$_SESSION["typdoc".$n]=$typdoc;
			$_SESSION["look_TITLE".$n]=$look_TITLE;
	       	$_SESSION["look_AUTHOR".$n]=$look_AUTHOR;
	      	$_SESSION["look_PUBLISHER".$n]=$look_PUBLISHER;
	      	$_SESSION["look_TITRE_UNIFORME".$n]=$look_TITRE_UNIFORME;
	       	$_SESSION["look_COLLECTION".$n]=$look_COLLECTION;
	       	$_SESSION["look_SUBCOLLECTION".$n]=$look_SUBCOLLECTION;
	        $_SESSION["look_CATEGORY".$n]=$look_CATEGORY;
	       	$_SESSION["look_INDEXINT".$n]=$look_INDEXINT;
	       	$_SESSION["look_KEYWORDS".$n]=$look_KEYWORDS;
	       	$_SESSION["look_ABSTRACT".$n]=$look_ABSTRACT;
	       	$_SESSION["look_CONTENT".$n]=$look_CONTENT;
	       	$_SESSION["look_DOCNUM".$n]=$look_DOCNUM;
	       	$_SESSION["look_CONCEPT".$n]=$look_CONCEPT;
	       	$_SESSION["look_ALL".$n]=$look_ALL;
	       	$_SESSION["search_type".$n]=$search_type;
	       	$_SESSION["l_typdoc".$n]=$l_typdoc;
	       	$_SESSION["level1".$n]=$_SESSION["level1"] ?? null;

	       	$authpersos=authpersos::get_instance();
	       	$authpersos->rec_history($n);
	       	if ($opac_search_other_function) search_other_function_rec_history($n);

			break;
		case "extended_search":
		case "extended_search_authorities":
			global $es;
			global $search;
			//Inutile de setter la recherche en session si la globale search est vide
			if(empty($search)){
			    return;
			}
			$_SESSION["nb_queries"] = intval($_SESSION["nb_queries"]) + 1;
			$n=$_SESSION["nb_queries"];

			$_SESSION["human_query".$n]=$es->make_human_query();
			$_SESSION["nb_search".$n]=count($search);
			for ($i=0; $i<count($search); $i++) {
				$_SESSION["search_".$i."_".$n]=$search[$i];
				$inter="inter_".$i."_".$search[$i];
				global ${$inter};
				$_SESSION["inter_".$i."_".$search[$i]."_".$n]=${$inter};
				$op="op_".$i."_".$search[$i];
				global ${$op};
				$_SESSION["op_".$i."_".$search[$i]."_".$n]=${$op};
				$field_="field_".$i."_".$search[$i];
    			global ${$field_};
    			$field=${$field_};
    			if (is_countable($field)) {
        			$_SESSION["n_fields_".$i."_".$search[$i]."_".$n]=count($field);
        			for ($j=0; $j<count($field); $j++) {
        				$_SESSION["field_".$i."_".$search[$i]."_".$j."_".$n]=$field[$j];
        			}
    			} else {
    			    $_SESSION["n_fields_".$i."_".$search[$i]."_".$n]=0;
    			}
				$fieldlib_ = "field_".$i."_".$search[$i]."_lib";
				global ${$fieldlib_};
				$fieldlib=${$fieldlib_};
				$_SESSION["n_fields_".$i."_".$search[$i]."_".$n."_lib"]=(is_array($fieldlib) ? count($fieldlib) : 0);
				if(is_array($fieldlib)) {
					for ($j=0; $j<count($fieldlib); $j++) {
						$_SESSION["field_".$i."_".$search[$i]."_".$j."_".$n."_lib"]=$fieldlib[$j];
					}
				}
    			$field1_="field_".$i."_".$search[$i]."_1";
    			global ${$field1_};
    			$field1=${$field1_};
    			$_SESSION["n_fields_".$i."_".$search[$i]."_".$n."_1"]=(is_array($field1) ? count($field1) : 0);
    			if(is_array($field1)) {
    				for ($j=0; $j<count($field1); $j++) {
    					$_SESSION["field_".$i."_".$search[$i]."_".$j."_".$n."_1"]=$field1[$j];
    				}
    			}

				$fieldvar_="fieldvar_".$i."_".$search[$i];
    			global ${$fieldvar_};
    			$fieldvar=${$fieldvar_};
    			$_SESSION["fieldvar_".$i."_".$search[$i]."_".$n]=$fieldvar;
			}
			$_SESSION["search_type".$n]=$search_type;
			break;
		case "search_universes":
		    search_universes_history::rec_history();
			break;
		case "term_search":
			global $search_term;
			global $term_click;
			global $page_search;
			$_SESSION["nb_queries"]=intval($_SESSION["nb_queries"])+1;
			$n=$_SESSION["nb_queries"];
			$_SESSION["search_type".$n]=$search_type;
			$_SESSION["search_term".$n]=stripslashes($search_term);
			$_SESSION["term_click".$n]=stripslashes($term_click);
			$_SESSION["page_search".$n]=intval($page_search);
			$_SESSION["l_typdoc".$n]=$l_typdoc;
			break;
		case "tags_search":
			global $user_query;

			$_SESSION["nb_queries"]=intval($_SESSION["nb_queries"])+1;
			$n=$_SESSION["nb_queries"];
			$_SESSION["user_query".$n]=$user_query;
			$_SESSION["search_type".$n]="simple_search";
			break;

		case "ai_search":
			AiSessionSemanticModel::rec_history();
			break;
	}
}

function get_history($n) {
	global $search_type;
	global $opac_search_other_function;

	$search_type=$_SESSION["search_type".$n];

	switch ($search_type) {
		case "simple_search":
			global $user_query;
			global $map_emprises_query;
			global $look_TITLE,
	       		$look_AUTHOR,
	      	 	$look_PUBLISHER,
	      	 	$look_TITRE_UNIFORME,
	       		$look_COLLECTION,
	       		$look_SUBCOLLECTION,
	       		$look_CATEGORY,
	       		$look_INDEXINT,
	       		$look_KEYWORDS,
	       		$look_ABSTRACT,
	       		$look_DOCNUM,
	       		$look_ALL,
	       		$look_CONTENT,
				$look_CONCEPT;
	       	global $typdoc,$l_typdoc;

	       	$user_query=$_SESSION["user_query".$n];
			$map_emprises_query=$_SESSION["map_emprises_query".$n];
			$typdoc=$_SESSION["typdoc".$n];
			$look_TITLE=$_SESSION["look_TITLE".$n];
	       	$look_AUTHOR=$_SESSION["look_AUTHOR".$n];
	      	$look_PUBLISHER=$_SESSION["look_PUBLISHER".$n];
	      	$look_TITRE_UNIFORME=$_SESSION["look_TITRE_UNIFORME".$n];
	       	$look_COLLECTION=$_SESSION["look_COLLECTION".$n];
	       	$look_SUBCOLLECTION=$_SESSION["look_SUBCOLLECTION".$n];
	        $look_CATEGORY=$_SESSION["look_CATEGORY".$n];
	       	$look_INDEXINT=$_SESSION["look_INDEXINT".$n];
	       	$look_KEYWORDS=$_SESSION["look_KEYWORDS".$n];
	       	$look_ABSTRACT=$_SESSION["look_ABSTRACT".$n];
	       	$look_ALL=$_SESSION["look_ALL".$n];
	       	$look_DOCNUM=$_SESSION["look_DOCNUM".$n];
	       	$look_CONTENT=$_SESSION["look_CONTENT".$n];
	       	$look_CONCEPT=$_SESSION["look_CONCEPT".$n];
	       	$l_typdoc=$_SESSION["l_typdoc".$n];
	       	$_SESSION["level1"]=$_SESSION["level1".$n];

	       	$authpersos=authpersos::get_instance();
	       	$authpersos->get_history($n);

	       	if ($opac_search_other_function) search_other_function_get_history($n);

	       	break;
		case "extended_search_authorities":
		    if(is_object($es) && get_class($es) != "search_authorities"){
    		    $es = new search_authorities("search_fields_authorities");
    		}
		case "extended_search":
			global $search;
			for ($i=0; $i<$_SESSION["nb_search".$n]; $i++) {
				$search[$i]=$_SESSION["search_".$i."_".$n];
				$inter="inter_".$i."_".$search[$i];
				global ${$inter};
				${$inter}=$_SESSION["inter_".$i."_".$search[$i]."_".$n];
				$op="op_".$i."_".$search[$i];
				global ${$op};
				${$op}=$_SESSION["op_".$i."_".$search[$i]."_".$n];

    			$n_fields=$_SESSION["n_fields_".$i."_".$search[$i]."_".$n];
    			$field=array();
    			for ($j=0; $j<$n_fields; $j++) {
    				$field[$j]=$_SESSION["field_".$i."_".$search[$i]."_".$j."_".$n];
    			}
    			$field_="field_".$i."_".$search[$i];
    			global ${$field_};
    			${$field_}=$field;

				$n_fieldslib = $_SESSION["n_fields_".$i."_".$search[$i]."_".$n."_lib"];
				$fieldlib = array();
				for ($j=0; $j<$n_fieldslib; $j++) {
					$fieldlib[$j] = $_SESSION["field_".$i."_".$search[$i]."_".$j."_".$n."_lib"];
				}
				$fieldlib_ = "field_".$i."_".$search[$i]."_lib";
				global ${$fieldlib_};
				${$fieldlib_} = $fieldlib;

    			$n_fields1=$_SESSION["n_fields_".$i."_".$search[$i]."_".$n."_1"];
    			$field1=array();
    			for ($j=0; $j<$n_fields1; $j++) {
    				$field1[$j]=$_SESSION["field_".$i."_".$search[$i]."_".$j."_".$n."_1"];
    			}
    			$field1_="field_".$i."_".$search[$i]."_1";
    			global ${$field1_};
    			${$field1_}=$field1;

    			$fieldvar=$_SESSION["fieldvar_".$i."_".$search[$i]."_".$n];
    			$fieldvar_="fieldvar_".$i."_".$search[$i];
    			global ${$fieldvar_};
    			${$fieldvar_}=$fieldvar;
			}
			break;
		case "term_search":
			global $search_term;
			global $term_click;
			global $page_search;

			$search_term=$_SESSION["search_term".$n];
			$term_click=$_SESSION["term_click".$n];
			$page_search=$_SESSION["page_search".$n];
			break;
		case "search_universes" :
		    search_universes_history::get_history($n);
		    break;
		case "ai_search" :
		    AiSessionSemanticModel::get_history($n);
		    break;

	}
	$_SESSION["search_type"]=$search_type;
}

function get_human_query($n) {
	global $msg;
	global $opac_search_other_function, $opac_indexation_docnum_allfields;
	global $include_path, $charset;

	if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

	$r = '';
	switch ($_SESSION["search_type".$n]) {
		case "simple_search":
			$r1 = '';
			if ($_SESSION["look_TITLE".$n]) $r1.=$msg["titles"]." ";
			if ($_SESSION["look_AUTHOR".$n]) $r1.=$msg["authors"]." ";
			if ($_SESSION["look_PUBLISHER".$n]) $r1.=$msg["publishers"]." ";
			if ($_SESSION["look_TITRE_UNIFORME".$n]) $r1.=$msg["titres_uniformes"]." ";
			if ($_SESSION["look_COLLECTION".$n]) $r1.=$msg["collections"]." ";
			if ($_SESSION["look_SUBCOLLECTION".$n]) $r1.=$msg["subcollections"]." ";
			if ($_SESSION["look_CATEGORY".$n]) $r1.=$msg["categories"]." ";
			if ($_SESSION["look_INDEXINT".$n]) $r1.=$msg["indexint"]." ";
			if ($_SESSION["look_KEYWORDS".$n]) $r1.=$msg["keywords"]." ";
			if ($_SESSION["look_ABSTRACT".$n]) $r1.=$msg["abstract"]." ";
			if ($_SESSION["look_ALL".$n]) $r1.=$msg["tous"]." ".($opac_indexation_docnum_allfields ? "[".$msg['docnum_search_with']."] " : '');
			if ($_SESSION["look_DOCNUM".$n]) $r1.=$msg["docnum"]." ";
			if ($_SESSION["look_CONTENT".$n]) $r1.=" ";
			if ($_SESSION["look_CONCEPT".$n]) $r1.=$msg["skos_concept"]." ";
	       	$authpersos=authpersos::get_instance();
	        $r1.=$authpersos->get_human_query($n);

			if ($_SESSION["typdoc".$n]) {
				$doctype = new marc_list('doctype');
				$r2=sprintf($msg["simple_search_history_doc_type"],$doctype->table[$_SESSION["typdoc".$n]]);
			} else $r2=$msg["simple_search_history_all_doc_types"];
			if ($opac_search_other_function) {
				$r3=search_other_function_human_query($n);
				if ($r3) $r2.=", ".$r3;
			}
			$r=sprintf($msg["simple_search_history"],htmlentities(stripslashes($_SESSION["user_query".$n]),ENT_QUOTES,$charset),$r1,$r2);

			if($_SESSION["map_emprises_query".$n]){
				$r.=$msg["map_history_emprises"]. implode(" ", $_SESSION["map_emprises_query".$n]);
			}

			break;
		case "extended_search":
		case "extended_search_authorities":
			$r=sprintf($msg["extended_search_history"],(isset($_SESSION["human_query".$n]) ? stripslashes($_SESSION["human_query".$n]) : ''));
			break;
		case "term_search":
			if ($_SESSION["search_term".$n]=="") $r1="(tous les termes)"; else $r1=stripslashes($_SESSION["search_term".$n]);
			$r=sprintf($msg["term_search_history"],$r1,(intval($_SESSION["page_search".$n])+1),$_SESSION["term_click".$n]);
			break;
		case "module":
			$r=sprintf($msg["navigation_search_libelle"],stripslashes($_SESSION["human_query".$n]));
			break;
		case "search_universes":
		    //$r=sprintf($msg["search_universe_history"],stripslashes($_SESSION["search_universes".$n]["universe_query"]), search_universe::get_label_from_id($_SESSION["search_universes".$n]["universe_id"]));
		    $r=search_universes_history::get_human_query($n);
			break;
		case "ai_search":
			$r = AiSessionSemanticModel::get_human_query(intval($n));
			break;
	}
	return $r;
}

function get_human_query_level_two($n) {
	global $msg;
	global $opac_search_other_function, $opac_indexation_docnum_allfields;
	global $include_path, $charset;

	if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

	if ($_SESSION["search_type".$n]=="simple_search") {
		$valeur_champ="";
		switch ($_SESSION["notice_view".$n]["search_mod"]) {
			case 'abstract':
				$r1=$msg["abstract"]." ";
			break;
			case 'title':
				$r1=$msg["title_search"]." ";
			break;
			case 'all':
				$r1=$msg["global_search"]." ".($opac_indexation_docnum_allfields ? "[".$msg['docnum_search_with']."] " : '');
			break;
			case 'keyword':
				$r1=$msg["keyword_search"]." ";
			break;
			case 'categ_see':
				$categ_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select libelle_categorie from categories where num_noeud=".$categ_id;
				$r_cat=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_cat)) {
					$valeur_champ=pmb_mysql_result($r_cat,0,0);
				}
				$r1=$msg["category"]." ";
			break;
			case 'author_see':
				$author_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select concat(author_name,', ',author_rejete) from authors where author_id=".$author_id;
				$r_author=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_author)) {
					$valeur_champ=pmb_mysql_result($r_author,0,0);
				}
				$r1=$msg["author_search"]." ";
			break;
			case 'indexint_see':
				$indexint_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select indexint_name from indexint where indexint_id=".$indexint_id;
				$r_indexint=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_indexint)) {
					$valeur_champ=pmb_mysql_result($r_indexint,0,0);
				}
				$r1=$msg["indexint_search"]." ";
			break;
			case 'publisher_see':
				$publisher_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select ed_name from publishers where ed_id=".$publisher_id;
				$r_pub=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_pub)) {
					$valeur_champ=pmb_mysql_result($r_pub,0,0);
				}
				$r1=$msg["publisher_search"]." ";
			break;
			case 'titre_uniforme_see':
				$titre_uniforme_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select tu_name from publishers where tu_id=".$titre_uniforme_id;
				$r_tu=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_tu)) {
					$valeur_champ=pmb_mysql_result($r_tu,0,0);
				}
				$r1=$msg["titre_uniforme_search"]." ";
			break;
			case 'coll_see':
				$coll_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select collection_name from collections where collection_id=".$coll_id;
				$r_coll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_coll)) {
					$valeur_champ=pmb_mysql_result($r_coll,0,0);
				}
				$r1=$msg["coll_search"]." ";
			break;
			case 'subcoll_see':
				$subcoll_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select sub_coll_name from sub_collections where sub_coll_id=".$subcoll_id;
				$r_subcoll=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_subcoll)) {
					$valeur_champ=pmb_mysql_result($r_subcoll,0,0);
				}
				$r1=$msg["subcoll_search"]." ";
			break;
			case 'docnum':
				$r1=$msg["docnum"];
				break;
			case 'concept_see':
				$concept_id=$_SESSION["notice_view".$n]["search_id"];
				$requete="select value from skos_field_global_index where code_champ = 1 and code_ss_champ = 1 and id_item = ".$concept_id;
				$r_concept=pmb_mysql_query($requete);
				if (@pmb_mysql_num_rows($r_concept)) {
					$valeur_champ=pmb_mysql_result($r_concept,0,0);
				}
				$r1=$msg["skos_concept"]." ";
				break;
			case 'authperso_see':
				$auth_id=$_SESSION["notice_view".$n]["search_id"];
				$authperso = new authperso_data($auth_id);
				$r1 = $authperso->get_name()." ";
				$valeur_champ = $authperso->get_isbd();
				break;
		}
		if (isset($_SESSION["typdoc".$n]) && $_SESSION["typdoc".$n]) {
			$doctype = new marc_list('doctype');
			$r2 = sprintf($msg["simple_search_history_doc_type"],$doctype->table[$_SESSION["typdoc".$n]]);
		} else $r2 = $msg["simple_search_history_all_doc_types"];
		if ($opac_search_other_function) {
			$r3=search_other_function_human_query($n);
			if ($r3) $r2.=", ".$r3;
		}
		$r=sprintf($msg["simple_search_history"],(!$valeur_champ? (isset($_SESSION["user_query".$n]) ? htmlentities(stripslashes($_SESSION["user_query".$n]),ENT_QUOTES,$charset) : ''):$valeur_champ),$r1,$r2);
		if(isset($_SESSION["map_emprises_query".$n]) && $_SESSION["map_emprises_query".$n]){
			$r.=$msg["map_history_emprises"]. implode(" ", $_SESSION["map_emprises_query".$n]);
		}
	} else {
		$r= get_human_query($n);
	}
	return $r;
}

function rec_last_history() {
	global $page;
	global $msg;
	global $opac_search_other_function;
	global $facette_test;
	global $affiliate_page, $catalog_page;

	if ($page=="") $page_=1; else $page_=$page;

	if ($facette_test) $search_type=$_SESSION["search_type".$_SESSION["last_query"]]; else $search_type=$_SESSION["search_type"];

	$_SESSION["lq_facette_test"]=($facette_test?2:0);

	switch ($search_type) {
		case "simple_search":
			global $user_query, $mode, $count, $typdoc, $clause;
			global $clause_bull, $clause_bull_num_notice, $tri, $pert;
			global $page, $l_typdoc, $join, $id_thes, $map_emprises_query;

			if (!$facette_test) {
				$_SESSION["lq_user_query"]=$user_query;
				$_SESSION["lq_mode"]=$mode;
				$_SESSION["lq_count"]=$count;
				$_SESSION["lq_typdoc"]=$typdoc;
				$_SESSION["lq_clause"]=$clause;
				$_SESSION["lq_clause_bull"]=$clause_bull;
				$_SESSION["lq_clause_bull_num_notice"]=$clause_bull_num_notice;
				$_SESSION["lq_tri"]=$tri;
				$_SESSION["lq_pert"]=$pert;
				$_SESSION["lq_page"]=$page_;
				$_SESSION["lq_affiliate_page"]=$affiliate_page;
				$_SESSION["lq_catalog_page"]=$catalog_page;
				$_SESSION["lq_l_typdoc"]=$l_typdoc;
				$_SESSION["lq_join"]=$join;
				$_SESSION["lq_id_thes"]=$id_thes;
				$_SESSION["lq_level1"]=(isset($_SESSION["level1"]) ? $_SESSION["level1"] : '');

				if (isset($map_emprises_query)) {
					$_SESSION["lq_map_emprises_query"] = $map_emprises_query;
				} else {
					unset($_SESSION["lq_map_emprises_query"]);
				}

				unset($_SESSION["lq_facette"]);

				if ($opac_search_other_function) {
					search_other_function_rec_history($_SESSION["last_query"]);
				}
				switch ($mode) {
					case "tous" :
						$_SESSION["list_name"]=$msg["list_tous"];
						$_SESSION["list_name_msg"]="list_tous";
						break;
					case "auteur":
						$_SESSION["list_name"]=$msg["list_authors"];
						$_SESSION["list_name_msg"]="list_authors";
						break;
					case "titre":
						$_SESSION["list_name"]=$msg["list_titles"];
						$_SESSION["list_name_msg"]="list_titles";
						break;
					case "editeur":
						$_SESSION["list_name"]=$msg["list_publishers"];
						$_SESSION["list_name_msg"]="list_publishers";
						break;
					case "titre_uniforme":
						$_SESSION["list_name"]=$msg["list_titres_uniformes"];
						$_SESSION["list_name_msg"]="list_titres_uniformes";
						break;
					case "collection":
						$_SESSION["list_name"]=$msg["list_collections"];
						$_SESSION["list_name_msg"]="list_collections";
						break;
					case "souscollection":
						$_SESSION["list_name"]=$msg["list_subcollections"];
						$_SESSION["list_name_msg"]="list_subcollections";
						break;
					case "categorie":
						$_SESSION["list_name"]=$msg["list_categories"];
						$_SESSION["list_name_msg"]="list_categories";
						break;
					case "indexint":
						$_SESSION["list_name"]=$msg["list_indexint"];
						$_SESSION["list_name_msg"]="list_indexint";
						break;
					case "keyword":
						$_SESSION["list_name"]=$msg["list_keywords"];
						$_SESSION["list_name_msg"]="list_keywords";
						break;
					case "docnum":
						$_SESSION["list_name"]=$msg["docnum_list"];
						$_SESSION["list_name_msg"]="docnum_list";
						break;
				}
			}
			break;
		case "extended_search":
			if (!$facette_test || (strpos($_SERVER['HTTP_REFERER'],$_SESSION['last_authority']['lvl']) !== false && $_SESSION['last_authority']['need_new_search'])) {
				$_SESSION["lq_page"]=$page_;
				$_SESSION["lq_affiliate_page"]=$affiliate_page;
				$_SESSION["lq_catalog_page"]=$catalog_page;
				$_SESSION["lq_mode"]="extended";
				$_SESSION["list_name"]=$msg["list_titles"];
				$_SESSION["list_name_msg"]="list_titles";
			}
			break;
	}
	//Si on est en navigation par facette
	if ($facette_test) {
		$_SESSION["lq_facette"]=(isset($_SESSION["facette"]) ? $_SESSION["facette"] : '');
		//La recherche étendue pour les facettes
		$_SESSION["lq_facette_search"]["lq_page"]=$page_;
		$_SESSION["lq_facette_search"]["lq_affiliate_page"]=$affiliate_page;
		$_SESSION["lq_facette_search"]["lq_catalog_page"]=$catalog_page;
		$_SESSION["lq_facette_search"]["lq_mode"]="extended";
		$my_search = new search();
		$_SESSION["lq_facette_search"]["lq_search"]=$my_search->serialize_search();
		$_SESSION["lq_facette_search"]["lq_notice_view"]=$_SESSION["notice_view".$_SESSION["last_query"]];
	}
}

function get_last_history() {
	global $search_type;
	global $opac_search_other_function;
	global $facette_test;
	global $reinit_facette;

	if ($reinit_facette==1) {
		unset($_SESSION["lq_facette"]);
		unset($_SESSION["lq_facette_search"]);
		unset($_SESSION["lq_facette_test"]);
	}

	$search_type = $_SESSION["search_type".$_SESSION["last_query"]] ?? "";
	$facette_test = $_SESSION["lq_facette_test"] ?? "";

	if($search_type == "module" && (empty($_SESSION['facette']) || is_countable($_SESSION['facette']) && count($_SESSION['facette']) == 0)){
		//Cas spécial pour section_see
		$ajout_section='';
		if ($_SESSION['last_module_search']['search_mod']=='section_see') {
			$ajout_section='&location='.$_SESSION['last_module_search']['search_location'];
			if ($_SESSION['last_module_search']['search_plettreaut']) {
				$ajout_section.='&plettreaut='.$_SESSION["last_module_search"]["search_plettreaut"];
			} elseif ($_SESSION["last_module_search"]["search_dcote"] || $_SESSION["last_module_search"]["search_lcote"] || $_SESSION["last_module_search"]["search_nc"] || $_SESSION["last_module_search"]["search_ssub"]) {
				$ajout_section.='&dcote='.$_SESSION["last_module_search"]["search_dcote"];
				$ajout_section.='&lcote='.$_SESSION["last_module_search"]["search_lcote"];
				$ajout_section.='&nc='.$_SESSION["last_module_search"]["search_nc"];
				$ajout_section.='&ssub='.$_SESSION["last_module_search"]["search_ssub"];
			}
		}elseif ($_SESSION['last_module_search']['search_mod']=='categ_see') {
			if ($_SESSION['last_module_search']['search_nb_level_enfants']) {
				$ajout_section.='&nb_level_enfants='.$_SESSION["last_module_search"]["search_nb_level_enfants"];
			}
			if ($_SESSION['last_module_search']['search_nb_level_parents']) {
				$ajout_section.='&nb_level_parents='.$_SESSION["last_module_search"]["search_nb_level_parents"];
			}
		}
		header("Location: ./index.php?lvl=".$_SESSION['last_module_search']['search_mod'].$ajout_section."&id=".$_SESSION['last_module_search']['search_id']);
	}

	switch ($search_type) {
		case "simple_search":
			if (!$facette_test) {
				global $user_query, $mode, $count, $typdoc, $clause;
				global $clause_bull, $clause_bull_num_notice, $tri, $pert;
				global $page, $l_typdoc, $join, $id_thes, $map_emprises_query;

				$user_query = $_SESSION["lq_user_query"] ?? "";
				$mode = $_SESSION["lq_mode"] ?? "";
				$count = $_SESSION["lq_count"] ?? "";
				$typdoc = $_SESSION["lq_typdoc"] ?? "";
				$clause = $_SESSION["lq_clause"] ?? "";
				$clause_bull = $_SESSION["lq_clause_bull"] ?? "";
				$clause_bull_num_notice = $_SESSION["lq_clause_bull_num_notice"] ?? "";
				$tri = $_SESSION["lq_tri"] ?? "";
				$pert = $_SESSION["lq_pert"] ?? "";
				$page = $_SESSION["lq_page"] ?? "";
				$affiliate_page = $_SESSION["lq_affiliate_page"] ?? "";
				$catalog_page = $_SESSION["lq_catalog_page"] ?? "";
				$l_typdoc = $_SESSION["lq_l_typdoc"] ?? "";
				$join = $_SESSION["lq_join"] ?? "";
				$id_thes = $_SESSION["lq_id_thes"] ?? "";
				$_SESSION["facette"] = $_SESSION["lq_facette"] ?? "";
				$_SESSION["level1"] = $_SESSION["lq_level1"] ?? "";
				if (isset($_SESSION["lq_map_emprises_query"])) {
					$map_emprises_query = $_SESSION["lq_map_emprises_query"];
				}

				if ($opac_search_other_function) {
					search_other_function_get_history($_SESSION["last_query"]);
				}
			}
			break;
		case "module" :
			global $mode;
			$mode = "extended";
			global $search;
			if(empty($search)) {
				$search=array();
			}
			$search[0]="s_1";
			$op_="EQ";

			//operateur
			$op="op_0_".$search[0];
			global ${$op};
			${$op}=$op_;

			//contenu de la recherche
			$field="field_0_".$search[0];
			$field_=array();
			$field_[0]=$_SESSION['last_query'];
			global ${$field};
			${$field}=$field_;

			//opérateur inter-champ
			$inter="inter_0_".$search[0];
			global ${$inter};
			${$inter}="";

			//variables auxiliaires
			$fieldvar_="fieldvar_0_".$search[0];
			global ${$fieldvar_};
			${$fieldvar_}="";
			$fieldvar=${$fieldvar_};

			break;
		case "extended_search":
			global $page,$mode,$catalog_page,$affiliate_page;
			get_history($_SESSION["last_query"]);
			$page=$_SESSION["lq_page"];
			$affiliate_page=$_SESSION["lq_affiliate_page"];
			$catalog_page=$_SESSION["lq_catalog_page"];
			$mode=$_SESSION["lq_mode"];
			break;
		case "external_search" :
		    $my_search = new search("search_fields_unimarc");
		    $my_search->json_decode_search($_SESSION["last_unimarc_search"]);
		    break;
	}
	if ($facette_test) {
		global $page,$mode,$catalog_page,$affiliate_page;
		$_SESSION["facette"]=$_SESSION["lq_facette"];
		$page=$_SESSION["lq_facette_search"]["lq_page"];
		$affiliate_page=$_SESSION["lq_facette_search"]["lq_affiliate_page"];
		$catalog_page=$_SESSION["lq_facette_search"]["lq_catalog_page"];
		$mode=$_SESSION["lq_facette_search"]["lq_mode"];
		$my_search = new search();
		$my_search->unserialize_search($_SESSION["lq_facette_search"]["lq_search"]);
		$_SESSION["notice_view".$_SESSION["last_query"]]=$_SESSION["lq_facette_search"]["lq_notice_view"];
	}
}
/**
 * Stocke la dernière autorité consultée dans la session
 *
 * @return void
 */
function rec_last_authorities(){
	global $lvl,$id,$page,$from;
	global $location,$plettreaut,$dcote,$lcote,$nc,$ssub;
	global $nb_level_enfants, $nb_level_parents;

	if(empty($_SESSION["last_module_search"])) {
	    $_SESSION["last_module_search"] = array();
	}
	$_SESSION["last_module_search"]["search_mod"]="$lvl";
	$_SESSION["last_module_search"]["search_id"]=$id;
	$_SESSION["last_module_search"]["search_page"]=$page;
	$_SESSION["last_module_search"]['need_new_search'] = true;

	if ($lvl=='section_see') {
		$_SESSION["last_module_search"]["search_location"]=$location;
		$_SESSION["last_module_search"]["search_plettreaut"]=$plettreaut;
		$_SESSION["last_module_search"]["search_dcote"]=$dcote;
		$_SESSION["last_module_search"]["search_lcote"]=$lcote;
		$_SESSION["last_module_search"]["search_nc"]=$nc;
		$_SESSION["last_module_search"]["search_ssub"]=$ssub;
	}

	if ($lvl=='categ_see') {
		$_SESSION["last_module_search"]["search_nb_level_enfants"]=$nb_level_enfants;
		$_SESSION["last_module_search"]["search_nb_level_parents"]=$nb_level_parents;
	}

	if($from == "search"){
		$_SESSION["last_module_search"]['need_new_search'] = false;
		if ($_SESSION["last_query"]) {
			$n=$_SESSION["last_query"];
		} else {
			$n=$_SESSION["nb_queries"];
		}
		$_SESSION["notice_view".$n]["search_mod"]="$lvl";
		$_SESSION["notice_view".$n]["search_id"]=$id;
		$_SESSION["notice_view".$n]["search_page"]=$page;
	}
}

function get_history_row($n) {
	global $opac_autolevel2, $msg, $charset;
	global $opac_rgaa_active;
	$html = "";

	switch($_SESSION["search_type".$n]) {
		case 'ai_search' :
			$html = AiSessionSemanticModel::get_history_row($n);
			break;
		case 'search_universes' :
			$html = search_universes_history::get_history_row($n);
			break;
		default :
			if($opac_rgaa_active){
				$html =  "<div class='search_history_li search_history_item'>";
			}else{
				$html =  "<li class='search_history_li search_history_item'>";
			}
			$html .=  "<input id='checkbox_history " . $n . "' type=checkbox name='cases_suppr[]' data-search-id='" . $n . "' value='" . $n . "' title='".htmlentities($msg['rgaa_checkbox_check'], ENT_QUOTES, $charset)."'>";

			$etiquette = "<span class='etiq_champ'>#" . $n . "</span> ";

			if ($opac_autolevel2==2) {
				$link_search = "<a href=\"javascript:document.forms['search_" . $n . "'].submit();\">" .get_human_query($n)."</a>";
			} else {
				$link_search =  "<a href=\"./index.php?lvl=search_result&get_query=".$n."\">".get_human_query($n)."</a>";
			}



			if($opac_rgaa_active){
				$html .= "<label for='checkbox_history " . $n . "'> ". $etiquette . $link_search ." </label>";
			}else{
				$html .= $etiquette . $link_search;
			}
			if($opac_rgaa_active){
				$html .=  "</div>";
			}else{
				$html .=  "</li>";
			}
			break;
	}
	return $html;
}
?>