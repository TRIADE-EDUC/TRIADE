<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSearcher.php,v 1.4.2.1 2024/10/03 07:05:32 gneveu Exp $

namespace Pmb\AI\Library;

use sort;
use facettes;
use common;
use shorturl_type_ai_search;
use facette_search_compare;
use Pmb\AI\Library\searcher\SearcherRecord;
use Pmb\AI\Library\searcher\SearcherRoot;

class AiSearcher
{

	/**
	 * Instance du moteur de recherche
	 *
	 * @var SearcherRoot|SearcherRecord
	 */
    protected $searcher;

	/**
	 * Question de l'utilisateur
	 *
	 * @var string
	 */
    protected $userQuery;

	/**
	 * Resultat de la recherche
	 *
	 * @var string
	 */
	protected $result = null;

	/**
	 * Constructeur
	 *
	 * @param string $userQuery
	 */
    public function __construct(string $userQuery)
    {
        $this->userQuery = $userQuery;
    }

	/**
	 * Retourne le titre
	 *
	 * @return string
	 */
    protected function get_title()
    {
        global $msg;
        return $msg['ai_search_found'];
    }

	/**
	 * Retourne le titre de la recherche
	 *
	 * @return string
	 */
    public function get_search_title()
    {
        global $charset, $count, $opac_search_other_function;

		$format = "<h3 class='searchResult-search' id='searchResult-search'>
			<span class='searchResult-equation'>
				<b>%s</b> %s <b>'%s'</b>
			</span>
		</h3>";

		if (empty($this->userQuery) && isset($_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"])) {
            $this->userQuery = $_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["user_query"];
        }

		$user_query = htmlentities($this->userQuery, ENT_QUOTES, $charset);
		if ($opac_search_other_function) {
            $user_query .= " ". search_other_function_human_query($_SESSION["last_query"]);
        }

        return sprintf(
			$format,
			intval($count),
			htmlentities($this->get_title(), ENT_QUOTES, $charset),
			$user_query
		);
    }

	/**
	 * Retourne le nombre d'elements par page
	 *
	 * @return int
	 */
	public function get_nb_per_page()
	{
		global $opac_search_results_per_page, $nb_per_page_custom;

		if (!$nb_per_page_custom) {
            $nb_per_page_custom = $opac_search_results_per_page;
        }

		return intval($nb_per_page_custom);
	}

	/**
	 * Retourne le debut pour la requête
	 *
	 * @return int
	 */
	public function get_start()
	{
		global $page;

		if ($page) {
			$page = intval($page);
			return ($page-1) * $this->get_nb_per_page();
		}
		return 0;
	}

    /**
     * Retourne l'instance du moteur de recherche
     *
     * @return SearcherRecord
     */
    protected function get_searcher_instance()
    {
        if (!isset($this->searcher)) {
            $this->searcher = new SearcherRecord($this->userQuery);
        }
        return $this->searcher;
    }

	/**
	 * Initialise la session pour les facettes
	 *
	 * @return void
	 */
    protected function init_session_facets()
    {
        global $count;

        if ($count) {
            $_SESSION['tab_result'] = $this->result;
            $_SESSION['tab_result_current_page'] = $this->result;
        } else {
            $_SESSION['tab_result'] = "";
            $_SESSION['tab_result_current_page'] = "";
        }
    }

	/**
	 * Initialise la session pour la visionneuse
	 *
	 * @return int
	 */
    protected function init_session_visionneuse()
    {
        if (!isset($_SESSION['ai_search_result'])) {
            $_SESSION['ai_search_result'] = array();
        }

        $index_ai_search_result = count($_SESSION['ai_search_result']);
        $_SESSION['ai_search_result'][] = explode(",", $this->result);

		return $index_ai_search_result;
    }

	/**
	 * Affiche les facettes
	 *
	 * @return void
	 */
    protected function create_display_facets()
    {
        global $opac_facettes_ajax, $base_path;
        global $facettes_tpl;

        if ($opac_facettes_ajax) {
			$facettes_tpl .= AiSearcherFacets::call_ajax_facettes();
        } else {
			AiSearcherFacets::set_url_base($base_path . '/index.php?');
            $facettes_tpl .= AiSearcherFacets::make_facette($_SESSION['tab_result_current_page']);
        }
    }

	/**
	 * Fait la recherche
	 *
	 * @return string
	 */
	protected function make_search()
	{
		global $count, $sort;
		if (null === $this->result) {
			$searcher = $this->get_searcher_instance();

			if (isset($sort)) {
				$_SESSION["last_sortnotices"] = $sort;
			}

			if (isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"]!=="") {
				$result = $searcher->get_sorted_result($_SESSION["last_sortnotices"], $this->get_start(), $this->get_nb_per_page());
			} else {
				$result = $searcher->get_sorted_result("default", $this->get_start(), $this->get_nb_per_page());
			}
			$this->result = $result;
			$count = $searcher->get_nb_results();
		}
		return $this->result;
	}

    public function get_display_result()
    {
        global $link_to_visionneuse, $opac_cart_allow, $opac_cart_only_for_subscriber;
        global $sendToVisionneuseAiSearch, $opac_short_url, $opac_visionneuse_allow;
        global $msg, $filtre_compare, $catal_navbar, $opac_rgaa_active;
        global $link_to_print_search_result, $count;


        $nbexplnum_to_photo = 0;
        if ($opac_visionneuse_allow) {
			$nbexplnum_to_photo = $this->searcher->get_nb_explnums();
        }

		$index_ai_search_result = $this->init_session_visionneuse();
        $this->init_session_facets();
        $this->create_display_facets();

        $display = "";

        // Impression
        $display .= "<span class='print_search_result'>".$link_to_print_search_result."</span>";

        // Gestion du tri
        $display .= sort::show_tris_in_result_list($count);


        // Ajout au panier
        if (
            ($opac_cart_allow && !$opac_cart_only_for_subscriber) ||
            ($opac_cart_allow && $_SESSION["user_code"])
        ) {
            $display .= "<span class='print_search_result'>";
            $display .= "<form id='form_cart_values' name='cart_values' action='./cart_info.php?lvl=ai_search' method='post' target='cart_info'>";
            $display .= "<input type='hidden' name='id' value='". $index_ai_search_result ."'>";
            $display .= "</form>";

            if ($opac_rgaa_active) {
                $display .= "<span class='addCart'>
				<button type='submit' form='form_cart_values' title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</button>
				</span>";
            } else {
                $display .= "<span class='addCart'>
				<a href='javascript:document.cart_values.submit()' title='".$msg["cart_add_result_in"]."'>".$msg["cart_add_result_in"]."</a>
				</span>";
            }

			$display .= "</span>";
        }

		// Affichage de la Visionneuse
        if ($opac_visionneuse_allow && $nbexplnum_to_photo) {
            $display .= "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
            $display .= str_replace('!!index_ai_search_result!!', $index_ai_search_result, $sendToVisionneuseAiSearch);
        }

        // Affichage du lien short url
        if ($opac_short_url) {
            $shorturl_search = new shorturl_type_ai_search();
            $display .= $shorturl_search->get_display_shorturl_in_result();
        }

        // Affichage du résultat de la recherche
        $display .= $this->get_display_elements();

        if ($filtre_compare == 'compare') {
            $display .= "<div id='navbar'><hr></div>";
            $catal_navbar = "";
        }

        return $display;
    }

	/**
	 * Affichage du résultat de la recherche
	 *
	 * @return string
	 */
	protected function get_display_elements()
	{
		global $msg, $filtre_compare, $reinit_compare;

		// On suis le flag filtre/compare
        facettes::session_filtre_compare();

		//si demande de réinitialisation
		if (isset($reinit_compare) && $reinit_compare == 1) {
			facette_search_compare::session_facette_compare(null, $reinit_compare);
		}

        $display = "<blockquote role='presentation'>";

        if ($filtre_compare=='compare') {

			//on valide la variable session qui comprend les critères de comparaisons
            facette_search_compare::session_facette_compare();

			//affichage comparateur
            $facette_compare= new facette_search_compare();
            $compare=$facette_compare->compare($this->searcher);
            if ($compare === true) {
                $display .= $facette_compare->display_compare();
            } else {
                $display .= $msg[$compare];
            }

        } else {

            $recherche_ajax_mode = 0;

			$display .= aff_notice(-1);
			$result = explode(',', $this->result);
            foreach ($result as $i => $id) {
                if ($i > 4) {
                    $recherche_ajax_mode = 1;
                }
                $display .= pmb_bidi(aff_notice($id, 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
            }
            $display .= aff_notice(-2);

        }
        $display .= "</blockquote>";

		return $display;
	}

	public function get_display_navbar()
	{
		global $opac_search_results_per_page, $count, $page;

		// nombre de références par pages (10 par défaut)
		if (!$opac_search_results_per_page) {
			$opac_search_results_per_page = 10;
		}

		if (!$page) {
			$page = 1;
		}

		$url_page = "javascript:document.form_values_navbar.page.value=!!page!!;document.form_values_navbar.submit()";
		$nb_per_page_custom_url = "javascript:document.form_values_navbar.nb_per_page_custom.value=!!nb_per_page_custom!!";
		$action = "javascript:document.form_values_navbar.page.value=document.form.page.value; document.form_values_navbar.submit()";

	    return "
		<br/>
		<div id='navbar'>
			<hr />
			<div style='text-align:center'>"
				.printnavbar(
					$page,
					$count,
					$opac_search_results_per_page,
					$url_page,
					$nb_per_page_custom_url,
					$action
				).
			"</div>
		</div>";
	}

	/**
	 * Affichage
	 *
	 * @return void
	 */
    public function proceed()
    {
        global $msg, $base_path, $get_query;

		$this->make_search();

        print '
		<div id="resultatrech">'.common::format_title($msg['resultat_recherche']).'
			<div id="resultatrech_container">
				<div id="resultatrech_see">';

        print $this->get_search_title();


        print '<div id="resultatrech_liste">';
        print $this->get_display_result();
        print '</div>';

        print '</div></div>';

		print $this->get_display_navbar();

		$index_history = $get_query ? intval($get_query) : $_SESSION["new_last_query"];
		print "
		<form name='form_values' style='display:none;' method='post' action='".$base_path."/index.php?lvl=search_result&search_type_asked=ai_search'>
			<input type='hidden' name='page' value='' />
			<input type='hidden' name='get_query' value='". $index_history ."' />
			". facette_search_compare::form_write_facette_compare() ."
		</form>
		";
		print "
		<form name='form_values_navbar' style='display:none;' method='post' action='".$base_path."/index.php?lvl=search_result&search_type_asked=ai_search'>
			<input type='hidden' name='nb_per_page_custom' value='".$this->get_nb_per_page()."' />
			<input type='hidden' name='page' value='' />
			<input type='hidden' name='get_query' value='". $index_history ."' />
			". facette_search_compare::form_write_facette_compare() ."
		</form>
		";

		$this->search_log();
    }

	/**
	 * Enregistrement des stats
	 *
	 * @param int $count
	 * @return void
	 */
    protected function search_log() {
    	global $pmb_logs_activate, $nb_results_tab;
    	global $count;

		if ($pmb_logs_activate) {
			$nb_results_tab['ai_search'] = $count;
        }
    }
}
