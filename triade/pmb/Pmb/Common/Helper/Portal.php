<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Portal.php,v 1.18.2.2 2024/06/28 09:57:31 qvarin Exp $
namespace Pmb\Common\Helper;

use Pmb\CMS\Library\UrlBuilder\RootUrlBuilder;
use Pmb\Common\Orm\CmsPagesOrm;

class Portal
{

    public static $type_page_opac = null;

    public const IN_POST_AND_GET = 0;

    public const IN_GET = 1;

    public const IN_POST = 2;

    /**
     * Liste des sous types de page de recherche
     *
     * @var array
     */
    public const SEARCH_SUB_PAGES = array(
        201,
        202,
        203,
        204,
        207,
        208,
        301,
        302,
        303,
        304,
        305,
        306,
        307,
        308,
        309,
        401,
        402,
        403,
        404,
        405,
        406,
        407,
        408,
        409,
        410,
        411
    );

    /**
     * Liste des sous types de page de recherche univer/segment
     *
     * @var array
     */
    public const UNIVERS_SEGMENT_SUB_PAGES = array(
        3401,
        3501
    );

    public const PAGES = array(

        "recherche" => 1,
        "result" => 2,
        "result_noti" => 3,
        "result_aut" => 4,
        "aut" => 5,
        "display" => 6,
        "empr" => 7,
        "caddie" => 8,
        "histo" => 9,
        "etagere" => 10,
        "infopage" => 11,
        "tag" => 12,
        "notation" => 13,
        "sugg" => 14,
        "rss" => 15,
        "section" => 16,
        "sort" => 17,
        "information" => 18,
        "doc_command" => 19,
        "doc_num" => 20,
        "authperso" => 21,
        "perio_a2z" => 22,
        "bannette" => 23,
        "faq" => 24,
        "cms" => 25,
        "extend" => 26,
        "result_docnum" => 27,
        "accueil" => 28,
        "ajax" => 29,
        // URLs externes = 30
        "contact_form" => 31,
        "collstate_bulletins_display" => 32,
        "pixel" => 33,
        "search_universe" => 34,
        "search_segment" => 35,
        "pnb" => 36,
        "animation" => 40
    );

    public const SUB_PAGES = array(

        "index_simple_search" => 101,

        "index_extended_search" => 102,
        "index_term_search" => 103,
        "index_external_search" => 104,
        "index_tags_search" => 105,
        "index_search_perso" => 106,
        "index_default" => 107,
        "index_perio_a2z" => 108,
        "extended_search_authorities" => 109,
        "ai_search" => 110,

        "search_result_simple_search" => 201, //Jamais atteint, on va en 207
        "search_result_term_search" => 203,
        "search_result_extended_search" => 202,
        "search_result_external_search" => 204,
        "search_result_tags_search" => 205,
        "search_result_search_perso" => 206,
        "search_result_default" => 207,
        "search_result_search_authorities" => 208,
        "search_result_ai_search" => 209,

        "more_results_title" => 301,
        "more_results_tous" => 302,

        "more_results_docnum" => 303,
        "lastrecords" => 303,

        "more_results_extended" => 304,
        "more_results_external" => 305,
        "more_results_affiliate" => 306,
        "more_results_facette_test" => 307,
        "more_results_reinit_facette" => 308,

        "more_results_auteur" => 401,
        "more_results_editeur" => 402,
        "more_results_categorie" => 403,
        "more_results_titre_uniforme" => 404,
        "more_results_collection" => 405,
        "more_results_souscollection" => 406,
        "more_results_indexint" => 407,
        "more_results_keyword_tags" => 309,
        "more_results_keyword" => 408,
        "more_results_abstract" => 409,
        "more_results_concept" => 410,
        "more_results_extended_authorities" => 411,

        "author_see" => 501,
        "publisher_see" => 502,
        "categ_see" => 503,
        "titre_uniforme_see" => 504,
        "coll_see" => 505,
        "subcoll_see" => 506,
        "indexint_see" => 507,
        "serie_see" => 508,
        "concept_see" => 509,

        "notice_display" => 601,
        "notice_display_s" => 602,
        "bulletin_display" => 603,
        "notice_display_a" => 604,

        "late" => 701,
        "all" => 702,
        "resa" => 703,
        "change_password" => 704,
        "valid_change_password" => 705,
        "bannette" => 706,
        "bannette_gerer" => 707,
        "bannette_creer" => 708,
        "make_sugg_default" => 709,
        "valid_sugg_default" => 710,
        "view_sugg" => 711,
        "make_multi_sugg" => 712,
        "private_list" => 713,
        "public_list" => 714,
        "demande_list" => 715,
        "list_print_list" => 716,
        "do_dmde" => 717,
        "list_dmde" => 718,
        "old" => 719,
        "pret" => 720,
        "retour" => 721,
        "import_sugg" => 722,
        "empr" => 725,
        "scan_requests_list" => 726,
        "bannette_edit" => 727,
        "bannette_unsubscribe" => 728,
        "resa_planning" => 724,
        "askmdp" => 730,
        "subscribe" => 731,
        "animations_list" => 732,
        "contribution_area_list" => 733,
        "contribution_area_new" => 734,
        "contribution_area_list_draft" => 735,
        "contribution_area_done" => 736,
        "pnb_loan_list" => 737,
        "contribution_area_form" => 738,
        "show_cart_default" => 801,
        "cart_print_action" => 802,
        "export" => 803,
        "resa_cart" => 804,
        "show_cart_raz_cart" => 805,
        "show_cart_del" => 806,
        "cart_print" => 807,
        "show_cart_sort" => 808,
        "transform_to_sugg" => 809,
        "show_list" => 810,
        "search_history" => 901,

        "etagere_see" => 1001,
        "etageres_see" => 1002,
        "infopages" => 1101,
        "notation_list" => 1301,
        "notation_add" => 1302,
        "tag_add" => 1201,
        "make_sugg" => 1401,
        "valid_sugg" => 1402,
        "rss_see" => 1501,
        "section_see" => 1601,
        "sort" => 1701,
        "information" => 1801,
        "doc_command" => 1901,

        "doc_num_data" => 2001,
        "doc_num_visio" => 2002,
        "doc_num_ajax" => 2003,
        "authperso_see" => 2101,
        "perio_a2z_see" => 2201,
        "bannette_see" => 2301,
        "faq" => 2401,
        "cmspage" => 2500,
        "extend" => 2601,
        "index" => 2801,
        "ajax_biblio" => 2901,
        "ajax_biblio_s" => 2902,
        "ajax_biblio_b" => 2903,
        "ajax_biblio_a" => 2904,

        "ajax_external" => 3001,
        "ajax_external_url_internal" => 3002,
        "contact_form" => 3101,
        "collstate_bulletins_display" => 3201,
        "search_universe" => 3401,
        "search_segment" => 3501,

        "animations_see" => 4001,
        "animation_see" => 4002,
        "registration_add" => 4003,
        "registration_delete" => 4004,
        "registration_save" => 4005,
        "registration_view" => 4006
    );

    private static $POST = null;

    private static $GET = null;

    public static function getValue($index, $where = self::IN_POST_AND_GET)
    {
        $post = static::getVarPost();
        $get = static::getVarGet();

        if ($where == self::IN_POST && (isset($post[$index]) && $post[$index])) {
            return $post[$index];
        } elseif ($where == self::IN_GET && (isset($get[$index]) && $get[$index])) {
            return $get[$index];
        } elseif ($where == self::IN_POST_AND_GET) {
            if (! empty(static::getValue($index, self::IN_POST))) {
                return static::getValue($index, self::IN_POST);
            } elseif (! empty(static::getValue($index, self::IN_GET))) {
                return static::getValue($index, self::IN_GET);
            }
        }
        return '';
    }

    public static function getTypePage(string $url = ""): string
    {
        $niveau = static::getValue('lvl');
        $type = static::getValue('search_type_asked');
        $mode = static::getValue('mode');
        $sugg = static::getValue('oresa', self::IN_GET);

        // Note : Type page = 30 pour les URLs externes
        $page = self::PAGES;

        // pour le panier
        $action = static::getValue('action');

        // url
        if (empty($url)) {
            $url = $_SERVER['REQUEST_URI'];
        }

        // Avis et tags
        if (strpos($url, 'avis.php') && strpos($url, 'liste')) {
            return $page['notation'];
        } elseif (strpos($url, 'avis.php') && strpos($url, 'add')) {
            return $page['notation'];
        } elseif (strpos($url, 'addtags.php')) {
            return $page['tag'];
        } elseif (strpos($url, 'askmdp.php') || strpos($url, 'subscribe.php')) {
            return $page['empr'];
        }

        // tags recherche
        $tags = static::getValue('tags');

        // Document numérique
        if (strpos($url, 'doc_num.php') || strpos($url, 'doc_num_data.php') || strpos($url, 'visionneuse.php')) {
            return $page['doc_num'];
        }

        // appel AJAX
        if (strpos($url, 'ajax.php')) {
            return $page['ajax'];
        }

        // pixel blanc
        if (strpos($url, 'pixel.php')) {
            return $page['pixel'];
        }

        $type_page = '';
        switch ($niveau) {
            case 'author_see':
            case 'titre_uniforme_see':
            case 'serie_see':
            case 'categ_see':
            case 'indexint_see':
            case 'publisher_see':
            case 'coll_see':
            case 'subcoll_see':
            case 'concept_see':
                $type_page = $page['aut'];
                break;
            case 'more_results':
                if ($mode) {
                    switch ($mode) {
                        case 'tous':
                        case 'titre':
                        case 'title':
                        case 'extended':
                        case 'abstract':
                            $type_page = $page['result_noti'];
                            break;
                        case 'auteur':
                        case 'editeur':
                        case 'titre_uniforme':
                        case 'collection':
                        case 'souscollection':
                        case 'categorie':
                        case 'indexint':
                        case 'concept':
                        case 'extended_authorities':
                            $type_page = $page['result_aut'];
                            break;
                        case 'keyword':
                            if ($tags) {
                                $type_page = $page['result_noti'];
                            } else {
                                $type_page = $page['result_aut'];
                            }
                            break;
                        case 'docnum':
                            $type_page = $page['result_docnum'];
                            break;
                        default:
                            if (substr($mode, 0, 10) == "authperso_") {
                                $type_page = $page['result_aut'];
                            } else {
                                $type_page = $page['result_noti'];
                            }
                            break;
                    }
                } else {
                    // autolevel
                    $type_page = $page['result_noti'];
                }
                break;
            case 'notice_display':
            case 'bulletin_display':
                $type_page = $page['display'];
                break;
            case 'search_result':
                $type_page = $page['result'];
                break;
            case 'search_history':
                $type_page = $page['histo'];
                break;
            case 'etagere_see':
            case 'etageres_see':
                $type_page = $page['etagere'];
                break;
            case 'transform_to_sugg':
            case 'show_list':
            case 'cart':
            case 'show_cart':
            case 'resa_cart':
                $type_page = $page['caddie'];
                break;
            case 'section_see':
                $type_page = $page['section'];
                break;
            case 'rss_see':
                $type_page = $page['rss'];
                break;
            case 'doc_command':
                $type_page = $page['doc_command'];
                break;
            case 'sort':
                $type_page = $page['sort'];
                break;
            case 'lastrecords':
                $type_page = $page['result_noti'];
                break;
            case 'authperso_see':
                $type_page = $page['authperso'];
                break;
            case 'information':
                $type_page = $page['information'];
                break;
            case 'infopages':
                $type_page = $page['infopage'];
                break;
            case 'extend':
                $type_page = $page['extend'];
                break;
            case 'perio_a2z_see':
                $type_page = $page['perio_a2z'];
                break;
            case 'cmspage':
                $type_page = $page['cms'];
                break;
            case 'bannette_see':
                $type_page = $page['bannette'];
                break;
            case "faq":
                $type_page = $page['faq'];
                break;
            case "contact_form":
                $type_page = $page['contact_form'];
                break;
            case "collstate_bulletins_display":
                $type_page = $page['collstate_bulletins_display'];
                break;
            case 'search_universe':
                $type_page = $page['search_universe'];
                break;
            case 'search_segment':
                $type_page = $page['search_segment'];
                break;
            case 'index':
                $type_page = $page['recherche'];
                break;
            case 'make_sugg':
                if ($sugg) {
                    $type_page = $page['sugg'];
                } else {
                    $type_page = $page['empr'];
                }
                break;
            case 'valid_sugg':
            case 'view_sugg':
            case 'late':
            case 'change_password':
            case 'valid_change_password':
            case 'message':
            case 'all':
            case 'old':
            case 'pret':
            case 'retour':
            case 'resa':
            case 'resa_planning':
            case 'bannette':
            case 'bannette_gerer':
            case 'bannette_creer':
            case 'bannette_edit':
            case 'bannette_unsubscribe':
            case 'make_multi_sugg':
            case 'import_sugg':
            case 'private_list':
            case 'public_list':
            case 'demande_list':
            case 'do_dmde':
            case 'list_dmde':
            case 'scan_requests_list':
                $type_page = $page['empr'];
                break;
            case 'animations_see':
            case 'animation_see':
            case 'registration':
                $type_page = $page['animation'];
                break;
            case 'contribution_area':
                $type_page = $page['empr'];
                break;
            default:
                $search_type_asked = static::getValue('search_type_asked');
                if ($search_type_asked == "extended_search_authorities") {
                    return $page["result"];
                }

                // pas de lvl
                if ($type) {
                    $type_page = $page['recherche'];
                } elseif (strpos($url, 'empr.php')) {
                    $type_page = $page['empr'];
                } elseif ((strpos($url, 'index.php')) || (! strpos($url, '.php'))) {
                    $type_page = $page['accueil'];
                } else {
                    $type_page = $page['recherche'];
                }
                if ($action == 'export') {
                    $type_page = $page['caddie'];
                }
                break;
        }
        return $type_page;
    }

    public static function getSubTypePage(string $url = "", array $notice = [])
    {
        $get = static::getVarGet();

        // récuperation des différentes variables nécessaires à l'identification des pages
        $niveau = static::getValue('lvl');
        // type recherche
        $type = static::getValue('search_type_asked');
        // pour recherche prédéfinie
        $perso = static::getValue('onglet_persopac');
        // pour les types d'autorité
        $mode = static::getValue('mode');

        // nivo biblio
        if (isset($notice['niveau_biblio']) && $notice['niveau_biblio']) {
            $biblio = $notice['niveau_biblio'];
        } else {
            $biblio = '';
        }

        // suggestion
        $sugg = static::getValue('oresa', self::IN_GET);
        if (empty($sugg)) {
            $url_ref = $_SERVER['REQUEST_URI'];
            $sugg = strpos($url_ref, 'oresa=popup');
        }

        // pour le panier
        $action = static::getValue('action');

        // url
        if (empty($url)) {
            $url = $_SERVER['REQUEST_URI'];
        }

        // Avis et tags
        if (strpos($url, 'avis.php') && strpos($url, 'liste')) {
            return static::SUB_PAGES['notation_list'];
        } elseif (strpos($url, 'avis.php') && strpos($url, 'add')) {
            return static::SUB_PAGES['notation_add'];
        } elseif (strpos($url, 'addtags.php')) {
            return static::SUB_PAGES['tag_add'];
        } elseif (strpos($url, 'askmdp.php')) {
            return static::SUB_PAGES['askmdp'];
        } elseif (strpos($url, 'subscribe.php')) {
            return static::SUB_PAGES['subscribe'];
        }

        // Document numérique
        if (strpos($url, 'doc_num.php') || strpos($url, 'doc_num_data.php')) {
            return static::SUB_PAGES['doc_num_data'];
        } elseif (strpos($url, 'visionneuse.php')) {
            return static::SUB_PAGES['doc_num_visio'];
        }

        // facettes
        if ((isset($get['reinit_facette']) && $get['reinit_facette']) || isset($get['param_delete_facette'])) { // param_delete_facette peut être égal à 0
            return static::SUB_PAGES['more_results_reinit_facette'];
        } elseif (isset($get['facette_test']) && $get['facette_test']) {
            return static::SUB_PAGES['more_results_facette_test'];
        }

        // recherches affiliées
        $tab = static::getValue('tab', self::IN_GET);

        // tags recherche
        $tags = static::getValue('tags');

        // appel AJAX - Log expand notice
        if (strpos($url, 'ajax.php') && (strpos($url, 'storage') || strpos($url, 'expand_notice'))) {
            if (! empty($biblio) && ! empty(static::SUB_PAGES["ajax_biblio_$biblio"])) {
                return static::SUB_PAGES["ajax_biblio_$biblio"];
            } else {
                return static::SUB_PAGES["ajax_biblio"];
            }
        }

        // appel AJAX - Consultation d'un document du portfolio
        if (strpos($url, 'ajax.php') && strpos($url, 'cms') && strpos($url, 'document') && strpos($url, 'render')) {
            return static::SUB_PAGES["doc_num_ajax"];
        }

        // appel AJAX - Log url externe
        if (strpos($url, 'ajax.php') && strpos($url, 'log')) {
            if (strpos($url, 'external_url_internal')) {
                return static::SUB_PAGES["ajax_external_url_internal"];
            } else {
                return static::SUB_PAGES["ajax_external"];
            }
        }

        $search_type = '';

        switch ($niveau) {
            case 'more_results':
                if ($tab == 'affiliate') {
                    return static::SUB_PAGES["more_results_affiliate"];
                } else {
                    switch ($mode) {
                        case 'titre':
                        case 'title':
                            return static::SUB_PAGES["more_results_title"];
                        case 'keyword':
                            if ($tags) {
                                return static::SUB_PAGES["more_results_keyword_tags"];
                            } else {
                                return static::SUB_PAGES["more_results_keyword"];
                            }
                            break;
                        default:
                            if (! empty(static::SUB_PAGES["{$niveau}_{$mode}"])) {
                                return static::SUB_PAGES["{$niveau}_{$mode}"];
                            }
                            return static::SUB_PAGES["more_results_tous"];
                    }
                }
                break;
            case 'notice_display':
                if ($biblio == "b") {
                    return static::SUB_PAGES["bulletin_display"];
                } elseif (! empty(static::SUB_PAGES["{$niveau}_{$biblio}"])) {
                    return static::SUB_PAGES["{$niveau}_{$biblio}"];
                } else {
                    return static::SUB_PAGES["notice_display"];
                }
                break;
            case 'search_result':
                $search_type_asked = static::getValue('search_type_asked');
                if ($search_type_asked == "extended_search_authorities") {
                    return static::SUB_PAGES["search_result_search_authorities"];
                }

                if ($type == "extended_search") {
                    if ($perso) {
                        return static::SUB_PAGES["search_result_search_perso"];
                    } else {
                        return static::SUB_PAGES["search_result_extended_search"];
                    }
                } elseif (! empty(static::SUB_PAGES["{$niveau}_{$type}"])) {
                    return static::SUB_PAGES["{$niveau}_{$type}"];
                } else {
                    return static::SUB_PAGES["search_result_default"];
                }
                break;
            case 'show_cart':
                if (isset($get['raz_cart']) && $get['raz_cart']) {
                    return static::SUB_PAGES["show_cart_raz_cart"];
                } elseif (isset($get['action']) && $get['action'] == 'del') {
                    return static::SUB_PAGES["show_cart_del"];
                } elseif (isset($get['sort'])) { // Peut être égal à 0
                    return static::SUB_PAGES["show_cart_sort"];
                } else {
                    return static::SUB_PAGES["show_cart_default"];
                }
                break;
            case 'cmspage':
            	// sous-type commence par 25 suivi de l'identifiant de la page
            	$url_query = parse_url($url, PHP_URL_QUERY);
            	if (null === $url_query) {
            		$url_query = $url;
            	}

            	parse_str($url_query, $query);
            	$page_id = intval($query['pageid'] ?? 0);
            	if (!empty($page_id) && CmsPagesOrm::exist($page_id)) {
            		return "25" . str_pad($page_id, 2, "0", STR_PAD_LEFT);
            	}
            	return static::SUB_PAGES["cmspage"];
                break;
            case 'index':
                $search_type_asked = static::getValue('search_type_asked');
                if ($search_type_asked == "extended_search_authorities") {
                    return static::SUB_PAGES["extended_search_authorities"];
                }

                if ($type == "extended_search") {
                    if ($perso) {
                        return static::SUB_PAGES["index_search_perso"];
                    } else {
                        return static::SUB_PAGES["index_extended_search"];
                    }
                } elseif (! empty(static::SUB_PAGES["{$niveau}_{$type}"])) {
                    return static::SUB_PAGES["{$niveau}_{$type}"];
                } else {
                    return static::SUB_PAGES["index_default"];
                }
                break;
            case 'make_sugg':
                if ($sugg) {
                    return static::SUB_PAGES["make_sugg"];
                } else {
                    return static::SUB_PAGES["make_sugg_default"];
                }
                break;
            case 'valid_sugg':
                if ($sugg) {
                    return static::SUB_PAGES["valid_sugg"];
                } else {
                    return static::SUB_PAGES["valid_sugg_default"];
                }
                break;
            case 'cart':
                if (strpos($url, 'print.php')) {
                    if ($action) {
                        return static::SUB_PAGES["cart_print_action"];
                    } else {
                        return static::SUB_PAGES["cart_print"];
                    }
                } else {
                    return static::SUB_PAGES["show_cart_default"];
                }
                break;
            case 'list':
                if ($action == "print_list") {
                    return static::SUB_PAGES["list_print_list"];
                }
                break;
            case 'registration':
                if (! empty(static::SUB_PAGES["{$niveau}_{$action}"])) {
                    return static::SUB_PAGES["{$niveau}_{$action}"];
                }
                break;
            case 'list_dmde':
                $sub = static::getValue('sub');
                if (!empty($sub)) {
                    return static::SUB_PAGES["do_dmde"];
                }
                return static::SUB_PAGES["list_dmde"];
                break;
            case 'contribution_area':
                $sub = static::getValue('sub');
                if ($sub == "area") {
                    return static::SUB_PAGES["contribution_area_new"];
                }

                $form_id = static::getValue('form_id');
                if (!empty($form_id)) {
                    return static::SUB_PAGES["contribution_area_form"];
                }
                break;
            default:
                $search_type_asked = static::getValue('search_type_asked');
                if ($search_type_asked == "extended_search_authorities") {
                    return static::SUB_PAGES["extended_search_authorities"];
                }

                if (! empty(static::SUB_PAGES[$niveau])) {
                    return static::SUB_PAGES[$niveau];
                }
                break;
        }

        switch ($type) {
            case 'external_search':
                return static::SUB_PAGES["index_external_search"];
            case 'term_search':
                return static::SUB_PAGES["index_term_search"];
            case 'extended_search':
                if ($type == "extended_search") {
                    if ($perso) {
                        return static::SUB_PAGES["index_search_perso"];
                    } else {
                        return static::SUB_PAGES["index_extended_search"];
                    }
                }
                break;
            case 'search_perso':
                return static::SUB_PAGES["search_result_search_perso"];
            case 'tags_search':
                return static::SUB_PAGES["index_tags_search"];
            case 'simple_search':
                return static::SUB_PAGES["index_simple_search"];
            case 'perio_a2z':
                return static::SUB_PAGES["index_perio_a2z"];
            default:
                // pas de lvl ni de type
                if ($type == "ai_search") {
                    $search_type = static::SUB_PAGES["ai_search"];
                } elseif (strpos($url, 'empr.php')) {
                    $search_type = static::SUB_PAGES["empr"];
                } elseif ((strpos($url, 'index.php')) || (! strpos($url, '.php'))) {
                    $search_type = static::SUB_PAGES["index"];
                } else {
                    $search_type = static::SUB_PAGES["index_default"];
                }

                if ($action == 'export') {
                    $search_type = static::SUB_PAGES["export"];
                }
                break;
        }
        return $search_type;
    }

    public static function getLabel($type)
    {
        global $lang, $include_path;
        if (! isset(static::$type_page_opac)) {
            if (file_exists($include_path . "/interpreter/statopac/$lang.xml")) {
                $liste_libelle = new \XMLlist($include_path . "/interpreter/statopac/$lang.xml");
            } else {
                $liste_libelle = new \XMLlist($include_path . "/interpreter/statopac/fr_FR.xml");
            }
            $liste_libelle->analyser();
            static::$type_page_opac = $liste_libelle->table;
            $query = "SELECT id_page, page_name FROM cms_pages";
            $result = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    static::$type_page_opac["25" . str_pad($row->id_page, 2, "0", STR_PAD_LEFT)] = $row->page_name;
                }
            }
        }
        return static::$type_page_opac[$type] ?? "page {$type}";
    }

    private static function getListValue(array $array, array $ignore = array())
    {
        $list = array();
        foreach ($array as $value) {

            if (in_array($value, $ignore)) {
                continue;
            }

            $label = static::getLabel($value);
            if (! empty($label)) {
                $list[] = array(
                    "value" => $value,
                    "label" => $label
                );
            }
        }
        return $list;
    }

    public static function getTypeList(array $ignore = array())
    {
        return static::getListValue(static::PAGES, $ignore);
    }

    public static function getTypeFromSubType($subType)
    {
    	$type = intval($subType / 100);
    	switch ($type) {
    		case 40:
    			// cas spécifique pour les animations
    			return 39;
    		case 33:
    			// cas spécifique pour les univers
    			return 34;
    		case 34:
    			// cas spécifique pour les segments
    			return 35;
    		default:
    			return $type;
    	}
    }

    public static function getSubTypeList(array $ignore = array(), bool $makeUrl = true)
    {
        $subTypeList = static::getListValue(static::SUB_PAGES, $ignore);
        if ($makeUrl) {
            $index = count($subTypeList);
            for ($i = 0; $i < $index; $i++) {
            	$type = self::getTypeFromSubType($subTypeList[$i]['value']);
                $urlBuilder = RootUrlBuilder::getClassUrlBuilder($type, $subTypeList[$i]['value']);
                $subTypeList[$i]['url'] = $urlBuilder->makeUrl();
            }
        }

        $query = "SELECT id_page, page_name FROM cms_pages";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {

                $value = "25" . str_pad($row->id_page, 2, "0", STR_PAD_LEFT);
                $value = intval($value);

                if (in_array($value, $ignore)) {
                    continue;
                }

                $subType = [
                    "value" => $value,
                    "label" => $row->page_name
                ];

                if ($makeUrl) {
                    $urlBuilder = RootUrlBuilder::getClassUrlBuilder(25, $value);
                    $subType['url'] = $urlBuilder->makeUrl();
                }

                $subTypeList[] = $subType;
            }
        }

        return $subTypeList;
    }

    public static function getAllSubTypes()
    {
        $subTypes = static::SUB_PAGES;
        $query = "SELECT id_page FROM cms_pages";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $value = "25" . str_pad($row->id_page, 2, "0", STR_PAD_LEFT);
                $value = intval($value);

                $subTypes["cmspage_{$row->id_page}"] = $value;
            }
        }
        return $subTypes;
    }

    public static function getVarPost()
    {
        return isset(static::$POST) ? static::$POST : $_POST;
    }

    public static function setVarPost($post)
    {
        static::$POST = $post;
    }

    public static function unsetVarPost()
    {
        static::$POST = null;
    }

    public static function getVarGET()
    {
        return isset(static::$GET) ? static::$GET : $_GET;
    }

    public static function setVarGET($get)
    {
        static::$GET = $get;
    }

    public static function unsetVarGET()
    {
        static::$GET = null;
    }
}