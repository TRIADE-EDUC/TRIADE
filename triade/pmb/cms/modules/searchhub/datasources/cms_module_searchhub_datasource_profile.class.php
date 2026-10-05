<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_searchhub_datasource_profile.class.php,v 1.1.2.3.2.1 2025/02/17 15:30:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_searchhub_datasource_profile extends cms_module_common_datasource
{
    /**
     * Renvoie la liste des selecteurs disponibles
     *
     * @return string[]
     */
    public function get_available_selectors()
    {
        return [
            "cms_module_searchhub_selector_profile",
        ];
    }

    /**
     * Retourne les donneees ou false si aucune donnée definie
     *
     * @return array|false
     */
	public function get_datas()
    {
		$selector = $this->get_selected_selector();
        if ($selector) {
            $query = "SELECT managed_module_box FROM cms_managed_modules JOIN cms_cadres ON id_cadre = '". intval($this->cadre_parent) ."' and cadre_object = managed_module_name";
			$result = pmb_mysql_query($query);

            if (pmb_mysql_num_rows($result)) {
				$managed_module_box = pmb_mysql_result($result,0,0);
				$managed_module_box = unserialize($managed_module_box);
                if (!empty($managed_module_box) && is_array($managed_module_box)) {
                    $profiles = $managed_module_box['module']['profiles'] ?? [];
                    if (!empty($profiles) && is_array($profiles)) {
                        $selectedProfile = false;
                        foreach ($profiles as $profile) {
                            if ($profile['id'] == $selector->get_value()) {
                                $selectedProfile = $profile;
                                break;
                            }
                        }
                        if ($selectedProfile) {
                            $selectedProfile['searches'] = $this->filter_search($selectedProfile['searches'] ?? []);
                            $selectedProfile['searches'] = $this->translate_search($selectedProfile['searches']);
                            return [ 'profile' => $selectedProfile ];
                        }
                    }
                }
			}
        }
        return false;
    }

    /**
     * Filtre les recherches en fonction de la visibilite
     *
     * @param array $searches
     * @return array
     */
    private function filter_search($searches) {
        $searchesFiltered = [];

        foreach ($searches as $search) {
            if (!empty($search['active'])) {
                switch ($search['settings']['visibility'] ?? '') {
                    case 'categories':
                        if (
                            !empty($_SESSION["id_empr_session"]) &&
                            !empty($search['settings']['categories']) &&
                            $this->empr_has_category($search['settings']['categories'])
                        ) {
                            $searchesFiltered[] = $search;
                        }
                        break;

                    case 'onlyConnected':
                        if (!empty($_SESSION["id_empr_session"])) {
                            $searchesFiltered[] = $search;
                        }
                        break;

                    case 'all':
                    default:
                        $searchesFiltered[] = $search;
                        break;
                }
            }
        }
        return $searchesFiltered;
    }

    /**
     * Determine if the current user has the given category
     *
     * @param int[] $category
     * @return bool
     */
    private function empr_has_category($categories) {
        $categories = array_map('intval', $categories);
        $categories = implode(',', $categories);

        $query = 'SELECT 1 FROM empr WHERE id_empr = "'. intval($_SESSION["id_empr_session"]) .'" AND empr_categ IN ('.$categories.')';
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            return true;
        }
        return false;
    }

    /**
     * Traduit les recherches
     *
     * @param array $searches
     * @return array
     */
    private function translate_search($searches) {
        global $lang;
        foreach ($searches as $key => $search) {
            if (!empty($search['translation'])) {
                $searches[$key]['name'] = $search['translation']['name'][$lang] ?? $search['name'];
                $searches[$key]['description'] = $search['translation']['description'][$lang] ?? $search['description'];
                $searches[$key]['settings']['placeholder'] = $search['translation']['placeholder'][$lang] ?? $search['settings']['placeholder'];
            }
        }
        return $searches;
    }
}
