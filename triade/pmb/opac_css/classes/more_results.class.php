<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: more_results.class.php,v 1.21 2019-06-13 09:48:58 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $opac_search_other_function;
if ($opac_search_other_function) require_once($include_path."/".$opac_search_other_function);

require_once($class_path."/record_display_modes.class.php");
require_once($class_path."/search/level2/level2_authorities_search.class.php");
require_once($class_path."/search/level2/level2_records_search.class.php");
require_once($class_path."/search/level2/level2_authpersos_search.class.php");
require_once($base_path.'/classes/facette_search.class.php');
require_once($base_path.'/classes/facettes_external.class.php');

//Surlignage
require_once($include_path."/javascript/surligner.inc.php");
require_once($include_path."/surlignage.inc.php");

require_once($include_path."/templates/more_results.tpl.php");

class more_results {

	protected static $search_type;
	
	protected static $user_query;
	
	protected static $level2_search;
	
	protected static $url_base;
	
	public static function get_title() {
		global $msg, $charset;
		global $opac_show_results_first_page;
		
		
		return $title;
	}
	
	public static function get_page() {
		global $page;
		global $tab;
		global $opac_allow_affiliate_search, $mode;
		global $catalog_page, $affiliate_page;
		
		if(!$page){
			$page=1;
			if($opac_allow_affiliate_search && ($mode != 'external' && $mode != 'docnum')) {
				$affiliate_page = $catalog_page = 1;
			}
		} else{
			if($opac_allow_affiliate_search && ($mode != 'external' && $mode != 'docnum')){
				if($tab == "affiliate"){
					$page = $affiliate_page;
				}else{
					$page = $catalog_page;
				}
			}
			if(!$page){
				$page=1;
			}
		}
		return $page;
	}
	
	public static function proceed() {
		global $msg, $charset;
		global $opac_search_results_per_page;
		global $page;
		global $opac_cart_allow, $opac_cart_only_for_subscriber;
		global $add_cart_link;
		global $opac_allow_affiliate_search;
		global $mode;
		global $count;
		global $nav_displayed;
		global $debut, $limiter;
		global $active_facette, $active_facettes_external;
		global $tab;
		global $facettes_tpl, $facettes_lvl1;
		global $opac_simple_search_suggestions;
		global $inclure_recherche;
		
		print $inclure_recherche;
		
		//on s'assure d'avoir un onglet sélectionné...
		if ($opac_allow_affiliate_search && ($mode != 'external' && $mode != 'docnum') && $tab == "") $tab = "catalog";
		
		// nombre de références par pages (10 par défaut)
		if (!$opac_search_results_per_page) $opac_search_results_per_page=10;
		if (!$page) $page = static::get_page();
		$debut =($page-1)*$opac_search_results_per_page;
		$limiter = "LIMIT $debut,$opac_search_results_per_page";
		
		if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
			$add_cart_link="<span class=\"addCart\"><a href='javascript:document.cart_values.submit()' title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a></span>";
		}
		
		if($mode == "keyword") {
			global $tags;
			if ($tags == "ok") {
				//recherche par tags
				$searcher = new searcher_tags(static::$user_query);
			} else {
				$searcher = new searcher_keywords(static::$user_query);
			}
			$count = $searcher->get_nb_results();
		}
		
		$active_facette = 0;
		$active_facettes_external = 0;
		$recordmodes = record_display_modes::get_instance();
		$nav_displayed = (is_object($recordmodes) ? $recordmodes->is_nav_displayed($recordmodes->get_current_mode()) : true);
		
		// affichage recherche
		print static::display_list();
		
		//gestion des facette si active
		if (($active_facette) && ($tab != "affiliate")) {
			$facettes_tpl = static::get_display_facets();
		}
		//gestion des facette externes si active
		if (($active_facettes_external) && ($tab != "affiliate")) {
			$facettes_tpl .= static::get_display_facets_external();
		}
		
		$facettes_lvl1 = facettes::do_level1();
		//suggestions : on affiche le bloc si une recherche a été tapée, différente de juste '*' et si le paramètre est bien activé
		if (trim(str_replace('*', '', static::$user_query)) && $opac_simple_search_suggestions) {
			$facettes_tpl .= facettes::make_facette_suggest(static::$user_query);
		}
		$catal_navbar = static::get_navbar();
		//AU premier coup, la facette n'est pas forcément dans le bon mode ...
		
		print static::get_hidden_search_form();		
		
		// affichage du navigateur si besoin (recherche affiliées off ou multi-critère (pagin géré dans le lvl2)
		if( $mode != 'extended' && (($tab != "affiliate")|| $mode == 'external' || $mode == 'docnum') && ($nav_displayed === true)) print $catal_navbar;
	}
	
	public static function get_navbar() {
		global $opac_search_results_per_page;
		global $page;
		global $opac_allow_affiliate_search;
		global $mode;
		global $count;
		global $debut, $limiter;

		// nombre de références par pages (10 par défaut)
		if (!$opac_search_results_per_page) {
		    $opac_search_results_per_page=10;
		}
		if (!$page) {
		    $page = static::get_page();
		}
		
	    $catal_navbar= "<div class='row'>&nbsp;</div>";
	    if(!$opac_allow_affiliate_search || $mode == 'external' || $mode == 'docnum'){
	        $url_page = "javascript:document.form_values.page.value=!!page!!; document.form_values.submit()";
	        $nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
	        $action = "javascript:document.form_values.page.value=document.form.page.value; document.form_values.submit()";
	    }else{
	        $url_page = "javascript:document.form_values.page.value=!!page!!; if(document.form_values.catalog_page)document.form_values.catalog_page.value=document.form_values.page.value; document.form_values.action = \"".static::format_url('lvl=more_results&tab=catalog')."\"; document.form_values.submit()";
	        $nb_per_page_custom_url = "javascript:document.form_values.nb_per_page_custom.value=!!nb_per_page_custom!!";
	        $action = "javascript:document.form_values.page.value=document.form.page.value; if(document.form_values.catalog_page) document.form_values.catalog_page.value=document.form_values.page.value; document.form_values.action = \"".static::format_url('lvl=more_results&tab=catalog')."\"; document.form_values.submit()";
	    }
	    $catal_navbar .= "<div id='navbar'><hr />\n<div style='text-align:center'>".printnavbar($page, $count, $opac_search_results_per_page, $url_page, $nb_per_page_custom_url, $action)."</div></div>";
	    
	    return $catal_navbar;
	}
	
	public static function display_list() {
		global $msg;
		global $base_path, $class_path, $include_path;
		global $mode;
		global $active_facette;
		global $nav_displayed;
		global $opac_max_results_on_a_page, $opac_search_results_per_page;
		global $search_type;
		global $active_facettes_external;
		global $user_query;
		
		switch($mode) {
			case 'tous':
				$active_facette = 1;
				if (!$nav_displayed) $opac_search_results_per_page = $opac_max_results_on_a_page;
				static::$level2_search = new level2_records_search(static::$user_query, 'tous');
				facettes::set_facet_type('notices');
				break;
			case 'titre':
			case 'title':
				$active_facette = 1;
				if (!$nav_displayed) $opac_search_results_per_page = $opac_max_results_on_a_page;
				static::$level2_search = new level2_records_search(static::$user_query, 'titres');
				facettes::set_facet_type('notices');
				break;
			case 'auteur':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'authors');
				facettes::set_facet_type('authors');
				break;
			case 'editeur':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'publishers');
				facettes::set_facet_type('publishers');
				break;
			case 'titre_uniforme':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'titres_uniformes');
				facettes::set_facet_type('titres_uniformes');
				break;			
			case 'collection':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'collections');
				facettes::set_facet_type('collections');
				break;
			case 'souscollection':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'subcollections');
				facettes::set_facet_type('subcollections');
				break;
			case 'categorie':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'categories');
				facettes::set_facet_type('categories');
				break;
			case 'indexint':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'indexint');
				facettes::set_facet_type('indexint');
				break;
			case 'abstract':
				$active_facette = 1;
				if (!$nav_displayed) $opac_search_results_per_page = $opac_max_results_on_a_page;
				static::$level2_search = new level2_records_search(static::$user_query, 'abstract');
				break;
			case 'keyword':
				$active_facette = 1;
				if (!$nav_displayed) $opac_search_results_per_page = $opac_max_results_on_a_page;
				if ($search_type=="extended_search") $search_type="";
				static::$level2_search = new level2_records_search(static::$user_query, 'keywords');
				break;
			case 'extended':
				//On annule la navigation par critères simples
				$_SESSION["level1"]=array();
				$active_facette = 1;
				if (!$nav_displayed) $opac_search_results_per_page = $opac_max_results_on_a_page;
				require_once($base_path.'/search/level2/extended.inc.php');
				facettes::set_facet_type('notices');
				break;
			case 'extended_authorities':
				//On annule la navigation par critères simples
				$_SESSION["level1"]=array();
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search("", 'extended');
				facettes::set_facet_type('');
				break;
			case 'external':
				$active_facettes_external = 1;
				//On annule la navigation par critères simples
				$_SESSION["level1"]=array();
				require_once($base_path.'/search/level2/external.inc.php');
				facettes::set_facet_type('');
				break;
			case 'docnum':
				require_once($base_path.'/search/level2/docnum.inc.php');
				facettes::set_facet_type('');
				break;
			case 'concept':
				$active_facette = 1;
				static::$level2_search = new level2_authorities_search(static::$user_query, 'concepts');
				facettes::set_facet_type('concepts');
				break;
			default:
				if(substr($mode, 0,10) == "authperso_"){
					$active_facette = 1;
					static::$level2_search = new level2_authpersos_search(static::$user_query, 'authperso');					
					$authperso = explode('_', $mode);
					if (!empty($authperso[1])) {
					    static::$level2_search->set_authperso_id($authperso[1]);
					}
				}else
				facettes::set_facet_type('');
				print $msg['no_document_found'];
				break;
		}
		if(isset(static::$level2_search) && is_object(static::$level2_search)) {
			static::$level2_search->proceed();
		}
	}
	
	protected static function init_session_facets() {
		global $reinit_facette;
		global $searcher;
		global $es;
		global $opac_map_activate, $opac_map_activate;
		global $search_type;
		
		if ($reinit_facette) unset($_SESSION['facette']);
		if(isset($searcher) && is_object($searcher)) {
			$tab_result = $searcher->get_result();
			if (isset($_SESSION['facette']) && count($_SESSION['facette']) > 0) {
				$search_type = "extended_search";
				if(!is_object($es)) $es = new search();
			}
			$_SESSION['tab_result'] = $tab_result;
			if ($opac_map_activate == 1 || $opac_map_activate == 3) {
				searcher::check_emprises();
			}
		} elseif(isset(static::$level2_search) && is_object(static::$level2_search)) {
			$tab_result = implode(',', static::$level2_search->get_elements_ids());
			if (isset($_SESSION['facette']) && count($_SESSION['facette']) > 0) {
				$search_type = "extended_search";
				if(!is_object($es)) $es = new search_authorities();
			}
			$_SESSION['tab_result'] = $tab_result;
		}
		static::set_search_type($search_type);
		return $tab_result;
	}
	
	public static function get_display_facets() {
		global $opac_facettes_ajax;
		
		$facettes_tpl = '';
		$tab_result = static::init_session_facets();
		if (!$opac_facettes_ajax) {
			facettes::set_url_base(static::$url_base);
			$facettes_tpl .= facettes::make_facette($tab_result);
		} else {
			$facettes_tpl .= facettes::call_ajax_facettes();
		}
		return $facettes_tpl;
	}
	
	public static function get_display_facets_external() {
		global $opac_facettes_ajax;
		
		$facettes_tpl = '';
		if (!$opac_facettes_ajax) {
			$tab_result = $_SESSION['tab_result_external'];
			$facettes_tpl .= facettes_external::make_facette($tab_result);
		} else {
// 			$_SESSION['tab_result_external']=$tab_result;
			$facettes_tpl .= facettes_external::call_ajax_facettes();
		}
		return $facettes_tpl;
	}
	
	public static function get_hidden_search_form() {
		global $charset;
		global $include_path;
		global $clause , $tri, $pert, $clause_bull, $clause_bull_num_notice, $join;
		global $mode, $count, $typdoc;
		global $l_typdoc;
		global $opac_indexation_docnum_allfields;
		global $author_type;
		global $id_thes, $surligne, $tags;
		global $page, $nb_per_page_custom, $catalog_page, $affiliate_page;
		global $nbexplnum_to_photo;
		global $opac_cart_allow, $opac_cart_only_for_subscriber, $facette_test, $es;
		global $opac_allow_affiliate_search, $id_authperso;
		global $external_env;
		
		$page+= 0;
		if(!isset($id_authperso)) $id_authperso = 0;
		// affichage recherche
		$clause = stripslashes($clause);
		$tri = stripslashes($tri);
		$pert = stripslashes($pert);
		$clause_bull = stripslashes($clause_bull);
		$clause_bull_num_notice = stripslashes($clause_bull_num_notice);
		$join = stripslashes($join);
		/*	 les données disponibles dans ce script sont :
		 $user_query : la requête utilisateur
		 $mode : sur quoi porte la recherche
		 $count : le nombre de résultats trouvés
		 $clause : la chaine contenant la clause MySQL
		 $tri : la chaine contenant la clause MySQL de tri
		 */
		$form = '';
		switch (static::$search_type) {
			case 'simple_search':
				// Gestion des alertes à partir de la recherche simple
				include_once($include_path."/alert_see.inc.php");
				$form .= $alert_see_mc_values;
			case 'tags_search':
				// constitution du form pour la suite
				$f_values = "<input type=\"hidden\" name=\"user_query\" value=\"".htmlentities(static::$user_query,ENT_QUOTES,$charset)."\">\n";
				$f_values .= "<input type=\"hidden\" name=\"mode\" value=\"$mode\">\n";
				$f_values .= "<input type=\"hidden\" name=\"count\" value=\"$count\">\n";
				$f_values .= "<input type=\"hidden\" name=\"typdoc\" value=\"".$typdoc."\">";
				$f_values .= "<input type=\"hidden\" name=\"id_authperso\" value=\"".$id_authperso."\">";
				if (function_exists("search_other_function_post_values")){
					$f_values .=search_other_function_post_values();
				}
				$f_values .= "<input type=\"hidden\" name=\"clause\" value=\"".htmlentities($clause,ENT_QUOTES,$charset)."\">\n";
				$f_values .= "<input type=\"hidden\" name=\"clause_bull\" value=\"".htmlentities($clause_bull,ENT_QUOTES,$charset)."\">\n";
				$f_values .= "<input type=\"hidden\" name=\"clause_bull_num_notice\" value=\"".htmlentities($clause_bull_num_notice,ENT_QUOTES,$charset)."\">\n";
				if($opac_indexation_docnum_allfields)
					$f_values .= "<input type=\"hidden\" name=\"join\" value=\"".htmlentities($join,ENT_QUOTES,$charset)."\">\n";
					$f_values .= "<input type=\"hidden\" name=\"tri\" value=\"".htmlentities($tri,ENT_QUOTES,$charset)."\">\n";
					$f_values .= "<input type=\"hidden\" name=\"pert\" value=\"".htmlentities($pert,ENT_QUOTES,$charset)."\">\n";
					$f_values .= "<input type=\"hidden\" name=\"l_typdoc\" value=\"".htmlentities($l_typdoc,ENT_QUOTES,$charset)."\">\n";
					$f_values .= "<input type=\"hidden\" id='author_type' name=\"author_type\" value=\"".(isset($author_type) ? $author_type : '')."\">\n";
					$f_values .= "<input type=\"hidden\" id=\"id_thes\" name=\"id_thes\" value=\"".$id_thes."\">\n";
					$f_values .= "<input type=\"hidden\" name=\"surligne\" value=\"".(isset($surligne) ? htmlentities($surligne,ENT_QUOTES,$charset) : '')."\">\n";
					$f_values .= "<input type=\"hidden\" name=\"tags\" value=\"".(isset($tags) ? htmlentities($tags,ENT_QUOTES,$charset) : '')."\">\n";
		
					$form .= "<form name=\"form_values\" action=\"".static::format_url('lvl=more_results')."\" method=\"post\">\n";
		
					$form.=facette_search_compare::form_write_facette_compare();
		
					$form .= $f_values;
					$form .= "<input type=\"hidden\" name=\"page\" value=\"$page\">
					<input type=\"hidden\" name=\"nb_per_page_custom\" value=\"$nb_per_page_custom\">\n";
					if($opac_allow_affiliate_search){
						$form .= "<input type=\"hidden\" name=\"catalog_page\" value=\"$catalog_page\">\n";
						$form .= "<input type=\"hidden\" name=\"affiliate_page\" value=\"$affiliate_page\">\n";
					}
					$form .= "<input type=\"hidden\" name=\"nbexplnum_to_photo\" value=\"".$nbexplnum_to_photo."\">\n";
					$form .= "</form>";
					if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
						$form .= "<form name='cart_values' action='./cart_info.php?lvl=more_results' method='post' target='cart_info'>\n";
						$form .= $f_values;
						$form .= "</form>";
					}
					break;
			case 'extended_search':
				$form=$es->make_hidden_search_form(static::format_url('lvl=more_results&mode=extended'),"form_values","",false);
		
				$form.=facette_search_compare::form_write_facette_compare();
		
				if($opac_allow_affiliate_search){
					$form .= "<input type=\"hidden\" name=\"catalog_page\" value=\"$catalog_page\">\n";
					$form .= "<input type=\"hidden\" name=\"affiliate_page\" value=\"$affiliate_page\">\n";
				}
				if($facette_test) $form .= "<input type=\"hidden\" name=\"facette_test\" value=\"2\">\n";
				$form.="</form>";
				if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
					$form.=$es->make_hidden_search_form("./cart_info.php?lvl=more_results&mode=extended","cart_values","cart_info","",false);
					if($opac_allow_affiliate_search){
						$form .= "<input type=\"hidden\" name=\"catalog_page\" value=\"$catalog_page\">\n";
						$form .= "<input type=\"hidden\" name=\"affiliate_page\" value=\"$affiliate_page\">\n";
					}
					$form.="</form>";
				}
				break;
			case 'external_search':
				$form=$es->make_hidden_search_form(static::format_url('lvl=more_results&mode=external'),"form_values","",false);
		
				$form.=facettes_external_search_compare::form_write_facette_compare();
		
				$form .= "<input type=\"hidden\" name=\"count\" value=\"$count\">\n";
				if ($_SESSION["ext_type"]!="multi") {
					$form.="<input type='hidden' name='external_env' value='".htmlentities(stripslashes($external_env),ENT_QUOTES,$charset)."'/>";
					$form.="</form>";
				} else $form.="</form>";
				if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"])))
					$form.=$es->make_hidden_search_form("./cart_info.php?lvl=more_results&mode=external","cart_values","cart_info");
					break;
			case 'extended_search_authorities':
				$form=$es->make_hidden_search_form(static::format_url('lvl=more_results&mode=extended_authorities'),"form_values","",false);
				
				$form.=facette_search_compare::form_write_facette_compare();
				
				if($opac_allow_affiliate_search){
					$form .= "<input type=\"hidden\" name=\"catalog_page\" value=\"$catalog_page\">\n";
					$form .= "<input type=\"hidden\" name=\"affiliate_page\" value=\"$affiliate_page\">\n";
				}
				if($facette_test) $form .= "<input type=\"hidden\" name=\"facette_test\" value=\"2\">\n";
				$form.="</form>";
				if ((($opac_cart_allow)&&(!$opac_cart_only_for_subscriber))||(($opac_cart_allow)&&($_SESSION["user_code"]))) {
					$form.=$es->make_hidden_search_form("./cart_info.php?lvl=more_results&mode=extended_authorities","cart_values","cart_info","",false);
					if($opac_allow_affiliate_search){
						$form .= "<input type=\"hidden\" name=\"catalog_page\" value=\"$catalog_page\">\n";
						$form .= "<input type=\"hidden\" name=\"affiliate_page\" value=\"$affiliate_page\">\n";
					}
					$form.="</form>";
				}
				break;
		}
		return $form;
	}
	
	public static function set_search_type($search_type) {
		static::$search_type = $search_type;
	}
	
	public static function set_user_query($user_query) {
		static::$user_query = $user_query;
	}
	
	public static function set_url_base($url_base) {
		static::$url_base = $url_base;
	}
	
	public static function format_url($url) {
		global $base_path;
	
		if(strpos(static::$url_base, "lvl=search_segment")) {
			return static::$url_base.str_replace('lvl', '&action', $url);
		} else {
			return static::$url_base.$url;
		}
	}
}