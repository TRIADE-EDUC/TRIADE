<?php

// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_result.class.php,v 1.30.4.1 2025/01/23 15:06:47 qvarin Exp $

use Pmb\AI\Library\AiSearcher;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $opac_search_other_function;
if ($opac_search_other_function) {
    require_once($include_path."/".$opac_search_other_function);
}

//Surlignage
require_once($include_path."/javascript/surligner.inc.php");
require_once($include_path."/surlignage.inc.php");

// template pour un encadré du résultat
require_once($include_path.'/templates/search_result.tpl.php');

class search_result
{
    protected static $url_base;

    protected static $search_type;

    protected static $user_query;

    public static function get_title()
    {
        global $msg, $charset;
        global $opac_show_results_first_page;

        $title = "";
        switch (static::$search_type) {
            case "simple_search":
                //Activation surlignage
                if ($opac_show_results_first_page) {
                    $activation_surlignage = activation_surlignage();
                } else {
                    $activation_surlignage = '';
                }
                $title .= "{$msg['search_result_for']}<b>".htmlspecialchars(stripslashes(static::$user_query), ENT_QUOTES, $charset)."</b>".$activation_surlignage;
                break;
            case "extended_search":
                $title .= "{$msg['search_result']}";
                break;
            case "external_search":
                if ($_SESSION["ext_type"] != "multi") {
                    $title .= "{$msg['search_result_for']}<b>".htmlentities(stripslashes(static::$user_query), ENT_QUOTES, $charset)."</b>";
                } else {
                    $title .= "{$msg['search_result']}";
                }
                break;
            case "tags_search":
                $title .= "{$msg['search_result_for']}<b>".htmlentities(stripslashes(static::$user_query), ENT_QUOTES, $charset)."</b>";
                break;
            case "extended_search_authorities":
                $title .= "{$msg['search_result_authorities']}";
                break;
        }
        return common::format_title($title, true);
    }

    protected static function get_display_search_tabs_form()
    {
        global $css;
        global $get_query, $launch_search, $mode;
        global $opac_show_results_first_page;
        global $surligne;
        global $base_path, $charset;

        search_view::set_search_type(static::$search_type);
        search_view::set_user_query(static::$user_query);
        search_view::set_url_base($base_path.'/index.php?');
        $search_tabs_form = search_view::get_display_search_tabs_form(static::$user_query, $css);

        if ((!$get_query) && (!((static::$search_type == "extended_search") && ($launch_search != 1))) && (!$mode)) {//On ne met pas dans l'historique les résultats obtenus en cliquant sur le mot-clé d'une notice
            rec_history();
            $_SESSION["new_last_query"] = $_SESSION["nb_queries"];
        }

        //Activation surlignage
        if ($opac_show_results_first_page) {
            $search_tabs_form = str_replace("!!surligne!!", (isset($surligne) ? htmlentities($surligne, ENT_QUOTES, $charset) : ''), $search_tabs_form);
        } else {
            $search_tabs_form = str_replace("!!surligne!!", "", $search_tabs_form);
        }
        return $search_tabs_form;
    }

    protected static function get_display_level1_authorities_search($type, $classname = '')
    {
        if($classname) {
            static::load_class('/search/level1/'.$classname.'.class.php');
            $level1_authorities_search = new $classname($type);
        } else {
            static::load_class('/search/level1/level1_authorities_search.class.php');
            $level1_authorities_search = new level1_authorities_search($type);
        }
        $level1_authorities_search->set_form_action(static::format_url('lvl=more_results'));
        $level1_authorities_search->set_user_query(static::$user_query);
        $level1_authorities_search->proceed();
        return $level1_authorities_search->get_nb_results();
    }

    protected static function get_display_level1_records_search($type, $classname = '')
    {
        if($classname) {
            static::load_class('/search/level1/'.$classname.'.class.php');
            $level1_records_search = new $classname($type);
        } else {
            static::load_class('/search/level1/level1_records_search.class.php');
            $level1_records_search = new level1_records_search($type);
        }
        $level1_records_search->set_form_action(static::format_url('lvl=more_results'));
        $level1_records_search->set_user_query(static::$user_query);
        $level1_records_search->proceed();
        return $level1_records_search->get_nb_results();
    }

    public static function get_display_simple_result()
    {
        global $msg, $charset;
        global $base_path, $class_path, $include_path;

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

        // on récupère les globales de ce qui est autorisé en recherche dans le paramétrage de l'OPAC
        global	$opac_modules_search_title,
        $opac_modules_search_author,
        $opac_modules_search_publisher,
        $opac_modules_search_titre_uniforme,
        $opac_modules_search_collection,
        $opac_modules_search_subcollection,
        $opac_modules_search_category,
        $opac_modules_search_indexint,
        $opac_modules_search_keywords,
        $opac_modules_search_abstract,
        $opac_modules_search_docnum,
        $opac_modules_search_all,
        $opac_modules_search_concept;

        // globales pour les require (provisoire)
        global $opac_search_other_function;
        global $typdoc;
        global $user_query;
        global $pmb_logs_activate;
        global $opac_allow_affiliate_search;

        $total_results = 0;

        if ($opac_modules_search_title && $look_TITLE) {
            $total_results += static::get_display_level1_records_search('title');
        }

        if ($opac_modules_search_author && $look_AUTHOR) {
            $total_results += static::get_display_level1_authorities_search('authors', 'level1_authors_search');
        }

        if ($opac_modules_search_publisher && $look_PUBLISHER) {
            $total_results += static::get_display_level1_authorities_search('publishers');
        }
        if ($opac_modules_search_titre_uniforme && $look_TITRE_UNIFORME) {
            $total_results += static::get_display_level1_authorities_search('titres_uniformes');
        }
        if ($opac_modules_search_collection && $look_COLLECTION) {
            $total_results += static::get_display_level1_authorities_search('collections');
        }

        if ($opac_modules_search_subcollection && $look_SUBCOLLECTION) {
            $total_results += static::get_display_level1_authorities_search('subcollections');
        }

        if ($opac_modules_search_category && $look_CATEGORY) {
            $total_results += static::get_display_level1_authorities_search('categories', 'level1_categories_search');
        }

        if ($opac_modules_search_indexint && $look_INDEXINT) {
            $total_results += static::get_display_level1_authorities_search('indexint');
        }

        if ($opac_modules_search_keywords && $look_KEYWORDS) {
            $total_results += static::get_display_level1_records_search('keywords', 'level1_records_keywords_search');
        }

        if ($opac_modules_search_abstract && $look_ABSTRACT) {
            $total_results += static::get_display_level1_records_search('abstract');
        }

        if ($opac_modules_search_docnum && $look_DOCNUM) {
            require_once($base_path.'/search/level1/docnum.inc.php');
            $total_results += $nb_result_docnum;
        }

        if ($opac_modules_search_concept && $look_CONCEPT) {
            $total_results += static::get_display_level1_authorities_search('concepts', 'level1_concepts_search');
        }

        if ($opac_modules_search_all && $look_ALL) {
            $nb_results = static::get_display_level1_records_search('all', 'level1_records_all_search');
            $total_results += $nb_results;
            $nb_all_results = $nb_results;
        }

        //Parcours pour les autorités personalisées
        $query = "select id_authperso from authperso where authperso_opac_search > 0";
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            static::load_class('/search/level1/level1_authperso_search.class.php');
            while($row = pmb_mysql_fetch_object($result)) {
                $look_ = "look_AUTHPERSO_".$row->id_authperso."#";
                global ${$look_};
                if(${$look_}) {
                    $level1_authorities_search = new level1_authperso_search('authperso');
                    $level1_authorities_search->set_authperso_id($row->id_authperso);
                    $level1_authorities_search->set_form_action(static::$url_base.'lvl=more_results');
                    $level1_authorities_search->set_user_query(static::$user_query);
                    $level1_authorities_search->proceed();
                    $total_results += $level1_authorities_search->get_nb_results();
                }
            }
        }
        return $total_results;
    }

    public static function proceed_simple_search()
    {
        global $msg;
        global $opac_search_other_function;
        global $opac_stemming_active;
        global $opac_show_suggest;
        global $opac_resa_popup;
        global $opac_allow_external_search;
        global $opac_allow_affiliate_search;
        global $opac_modules_search_docnum, $look_DOCNUM;
        global $opac_simple_search_suggestions;
        global $opac_autolevel2;
        global $get_query;
        global $nb_all_results;
        global $opac_rgaa_active;

        if (static::$user_query == "") {
            if ($opac_search_other_function) {
                if (search_other_function_has_values()) {
                    static::$user_query = "*";
                }
            }
        }
        if (static::$user_query != "") {
            $_SESSION["level1"] = array();

            $aq = new analyse_query(stripslashes(static::$user_query), 0, 0, 1, 1, $opac_stemming_active);
            if ($aq->error) {
                print pmb_bidi(sprintf($msg["searcher_syntax_error_desc"], $aq->current_car, $aq->input_html, $aq->error_message)."<br /><br />");
                return false;
            }
            $total_results = static::get_display_simple_result();

            if ($opac_show_suggest) {
                $bt_sugg = "&nbsp;&nbsp;&nbsp;<span class='search_bt_sugg' >";
                if ($opac_resa_popup) {
                    if ($opac_rgaa_active) {
                        $bt_sugg .= "<a
                            href=\"./do_resa.php?lvl=make_sugg&oresa=popup\"
                            target=\"_blank\"
                            onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" ";
                    } else {
                        $bt_sugg .= "<a href=\"#\" onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" ";
                    }
                } else {
                    $bt_sugg .= "<a href=\"./do_resa.php?lvl=make_sugg&oresa=popup\" ";
                }
                $bt_sugg .= " title='".$msg["empr_bt_make_sugg"]."' >".$msg['empr_bt_make_sugg'];
                $bt_sugg .= "</a></span>";
            } else {
                $bt_sugg = "";
            }

            if ($opac_allow_external_search) {
                $bt_external = "<span class='search_bt_external'>";
                $bt_external .= "<a href='javascript:document.search_input.action=\"".static::format_url('search_type_asked=external_search&external_type=simple')."\"; document.search_input.submit();'
			        title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a>";
                $bt_external .= "</span>";
            } else {
                $bt_external = "";
            }

            // affichage pied-de-page
            if(!$total_results && (!$opac_allow_affiliate_search || ($opac_modules_search_docnum && $look_DOCNUM))) {
                print "<span class='search_no_result'>".$msg['no_result']."</span> ".$bt_sugg.($bt_external ? "&nbsp;&nbsp;&nbsp;$bt_external" : "");
            } elseif ($bt_external || $bt_sugg) {
                print "<br /><div class='row'>".$bt_sugg.($bt_external ? "&nbsp;&nbsp;&nbsp;$bt_external" : "")."</div>";
            }
            //if (($nb_all_results)&&(!$get_query))

            //Suggestions
            if(!$total_results && $opac_simple_search_suggestions) {
                $tableSuggest = "";
                if ($opac_autolevel2 == 2) {
                    $actionSuggest = static::format_url("lvl=more_results&autolevel1=1");
                } else {
                    $actionSuggest = static::format_url("lvl=search_result&search_type_asked=simple_search");
                }

                $termes = str_replace('*', '', stripslashes(static::$user_query));
                if (trim($termes)) {
                    $suggestion = new suggest($termes);
                    $tmpArray = array();
                    $tmpArray = $suggestion->listUniqueSimilars();

                    if(count($tmpArray)) {
                        $tableSuggest .= "<table class='facette_suggest'><tbody>";
                        foreach($tmpArray as $word) {
                            $tableSuggest .= "<tr>
								<td>
									<a href='".$actionSuggest."&user_query=".rawurlencode($word)."'>
										<span class='facette_libelle'>".$word."</span>
									</a>
								</td>
							</tr>";
                        }
                        $tableSuggest .= "</tbody></table>";

                        print "<div id='facette_suggest'><h3>".$msg['facette_suggest']."</h3>".$tableSuggest."</div>";
                    }
                }
            }

            if (($nb_all_results) && ($opac_autolevel2) && !$get_query) {
                print "<script>document.forms['search_tous'].submit();</script>";
            } else {
                print "<script>document.getElementById('search_result').style.display='';</script>";
            }
        } else {
            print "<span class='search_no_result'>".$msg['no_result']."</span>";
        }
    }

    public static function proceed()
    {
        global $msg, $charset;
        global $base_path;
        global $class_path;
        global $mode;
        global $opac_autolevel2;
        global $inclure_recherche;
        global $opac_allow_affiliate_search;
        global $facette_test;
        global $opac_show_suggest;
        global $opac_resa_popup;
        global $opac_allow_external_search;
        global $opac_autolevel2;
        global $get_query;
        global $nb_all_results;
        global $search_error_message;
        global $nb_result_external;
        global $opac_rgaa_active;

        print $inclure_recherche;
        print static::get_display_search_tabs_form();

        print "<div id=\"search_result\" ".((!$mode) && (static::$search_type == "simple_search") && ($opac_autolevel2) ? "style='display:none'" : "").">\n";

        // lien pour retour au sommaire
        unset($_SESSION['facette']);
        if (!$mode) {
            print static::get_title();
            switch (static::$search_type) {
                case "simple_search":
                    static::proceed_simple_search();
                    break;
                case "extended_search":
                    $allow_search_affiliate_and_external = true;
                    if($opac_allow_affiliate_search || $opac_allow_external_search) {
                        $es_uni = new search("search_fields_unimarc");
                        if((isset($_SESSION['facette']) && count($_SESSION['facette'])) || $facette_test || $es_uni->has_forbidden_fields()) {
                            $allow_search_affiliate_and_external = false;
                        }
                    }
                    $nb_result_extended = static::get_display_level1_records_search('extended', 'level1_records_extended_search');
                    if ($opac_show_suggest) {
                        $bt_sugg = "&nbsp;&nbsp;&nbsp;<span class='search_bt_sugg' >";
                        if ($opac_resa_popup) {
                        	if ($opac_rgaa_active) {
                        		$bt_sugg .= "<a
		                            href=\"./do_resa.php?lvl=make_sugg&oresa=popup\"
		                            target=\"_blank\"
		                            onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" ";
                        	} else {
                        		$bt_sugg .= "<a href=\"#\" onClick=\"w=window.open('./do_resa.php?lvl=make_sugg&oresa=popup','doresa','scrollbars=yes,width=600,height=600,menubar=0,resizable=yes'); w.focus(); return false;\" ";
                        	}
                        } else {
                            $bt_sugg .= "<a href=\"./do_resa.php?lvl=make_sugg&oresa=popup\" ";
                        }
                        $bt_sugg .= " title='".$msg["empr_bt_make_sugg"]."' >".$msg['empr_bt_make_sugg'];
                        $bt_sugg .= "</a></span>";
                    } else {
                        $bt_sugg = "";
                    }
                    if ($opac_allow_external_search && $allow_search_affiliate_and_external) {
                        $bt_external = "<span class='search_bt_external' ><a href='javascript:document.search_form.action=\"".static::format_url('search_type_asked=external_search&external_type=multi')."\"; document.search_form.submit();' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
                    } else {
                        $bt_external = "";
                    }

                    if (!$nb_result_extended) {
                        print "<span class='search_no_result'>".$msg['no_result']."</span> <span class='search_error_message'>".htmlentities($search_error_message, ENT_QUOTES, $charset)."</span> ".$bt_sugg.($bt_external ? "&nbsp;&nbsp;&nbsp;$bt_external" : "");
                    } elseif ($bt_external || $bt_sugg) {
                        print $bt_sugg.($bt_external ? "&nbsp;&nbsp;&nbsp;$bt_external" : "");
                    }
                    break;
                case "external_search":
                    require_once($base_path.'/search/level1/external.inc.php');
                    if (!$nb_result_external) {
                        print "<span class='search_no_result'>".$msg['no_result']."</span> <span class='search_error_message'>".htmlentities($search_error_message, ENT_QUOTES, $charset)."</span>";
                    }
                    break;
                    // *************************************************
                    // Tags
                case "tags_search":
                    $tag = new tags();
                    if (static::$user_query == "*") {
                        echo $tag->listeAlphabetique();
                    } else {
                        echo $tag->chercheTag(static::$user_query);
                    }
                    break;
                case "extended_search_authorities":
                    $nb_result_extended = static::get_display_level1_authorities_search('extended', 'level1_authorities_extended_search');
                    break;
                case "ai_search":
                    static::proceed_ai_search();
                    break;

            }
        } else {
            switch ($mode) {
                case "keyword":
                    static::get_display_level1_records_search('keywords', 'level1_records_keywords_search');
                    break;
            }
        }
        print "</div>";
    }

    public static function set_url_base($url_base)
    {
        static::$url_base = $url_base;
    }

    public static function set_search_type($search_type)
    {
        static::$search_type = $search_type;
    }

    public static function set_user_query($user_query)
    {
        static::$user_query = $user_query;
    }

    public static function format_url($url)
    {
        if(strpos(static::$url_base, "lvl=search_segment")) {
            return static::$url_base.str_replace('lvl', '&action', $url);
        } else {
            return static::$url_base.$url;
        }
    }

    protected static function load_class($file)
    {
        global $base_path;
        global $class_path;
        global $include_path;
        global $javascript_path;
        global $styles_path;
        global $msg,$charset;

        if(file_exists($class_path.$file)) {
            require_once($class_path.$file);
        } else {
            return false;
        }
        return true;
    }


    public static function proceed_ai_search()
    {
        $ai_search = new AiSearcher(static::$user_query);
        $ai_search->proceed();
    }
}
