<?php

// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_searchhub.class.php,v 1.1.2.3 2025/01/17 10:40:47 gneveu Exp $

use Pmb\Common\Library\CSRF\CollectionCSRF;
use Pmb\Common\Views\VueJsView;

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

class cms_module_searchhub extends cms_module_common_module
{

    /**
     * Constructeur
     *
     * @param integer $id (optional, default: 0)
     */
    public function __construct($id = 0)
    {
        $this->module_path = __DIR__;
        parent::__construct($id);
    }

    /**
     * Retourne le formulaire d'administration du module
     *
     * @return string
     */
    public function get_manage_form()
    {
        global $action;

        if (!empty($action) && $action == 'save_form') {
            // On evite de conserver save_form dans l'URL
            header('Location: ./cms.php?categ=manage&sub=searchhub&quoi=module', true, 303);
            exit();
        }

        $categories = [];
        $query = 'SELECT id_categ_empr, libelle FROM empr_categ ORDER BY libelle';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $categories[$row['id_categ_empr']] = $row['libelle'];
            }
		}

        $vueJs = new VueJsView(
            'cms/cms_module_searchhub',
            [
                'url_webservice' => './ajax.php?module=cms&categ=module&action=ajax&elem=cms_module_searchhub',
                'profiles' => $this->managed_datas['module']['profiles'] ?? [],
                'msg' => $this->msg,
                'categories' => $categories,
                'csrf_tokens' => (new CollectionCSRF())->getArrayTokens()
            ]
        );
        return $vueJs->render();
    }

    /**
     * Enregistre le formulaire d'administration du module
     *
     * @global array $profile
     * @global int $deleted_profile
     * @return array
     */
    public function save_manage_form()
    {
        global $profile, $deleted_profile;

        if (!empty($deleted_profile)) {
            $this->managed_datas['module']['profiles'] = array_filter($this->managed_datas['module']['profiles'], function ($profile) use ($deleted_profile) {
                return $profile['id'] != $deleted_profile;
            });
            return $this->managed_datas['module'];
        }

        if (empty($profile) || !is_array($profile)) {
            header('Location: ./cms.php?categ=manage&sub=searchhub&quoi=module', true, 303);
            exit();
        }

		$parameters = $this->managed_datas['module'] ?? [];
        $parameters['profiles'] ??= [];

        $profile = $this->format_profile_data($profile);
        if (empty($profile['id'])) {
            $profile['id'] = cms_module_searchhub::get_max_profile_id($parameters['profiles']);
            $parameters['profiles'][] = $profile;
        } else {
            foreach ($parameters['profiles'] as $key => $profileSaved) {
                if ($profileSaved['id'] == $profile['id']) {
                    $parameters['profiles'][$key] = $profile;
                }
            }
        }
        return $parameters;
    }

    /**
     * Formate les donnees d'un profile pour les enregistrer
     *
     * @param array $profile
     * @return array
     */
    private function format_profile_data($profile)
    {
        $profile['id'] = intval($profile['id']);
        $profile['searches'] ??= [];
        if (is_countable($profile['searches']) && !empty($profile['searches'])) {
            $nb_searches = count($profile['searches']);
            for ($i = 0; $i < $nb_searches; $i++) {
                $profile['searches'][$i]['settings'] = encoding_normalize::json_decode(stripslashes($profile['searches'][$i]['settings']), true);
            }
        }
        return $profile;
    }

    /**
     * Retourne le dernier id de profile
     *
     * @param array $profiles
     * @return integer
     */
    private function get_max_profile_id(array $profiles)
    {
        $max = 0;
		if (!empty($profiles)){
			foreach	($profiles as $profile) {
                $id = intval($profile['id']);
                if ($id > $max) {
                    $max = $id;
                }
			}
		}
		return $max + 1;
    }

    public function execute_ajax()
    {
        global $do, $universe_id;
        $response = array();
        switch ($do) {
            case 'get_universes_segments':
                $query = "SELECT id_search_universe, search_universe_label FROM search_universes ORDER BY search_universe_label";
                $result = pmb_mysql_query($query);
                $universes = array();
                if (pmb_mysql_num_rows($result)) {
                    while ($row = pmb_mysql_fetch_assoc($result)) {
                        $query_segments = "SELECT id_search_segment, search_segment_label FROM search_segments WHERE search_segment_num_universe  = ".$row['id_search_universe']." ORDER BY search_segment_label";
                        $result_segments = pmb_mysql_query($query_segments);
                        $segments = array();
                        if (pmb_mysql_num_rows($result_segments)) {
                            while ($row_segments = pmb_mysql_fetch_assoc($result_segments)) {
                                $segments[] = [
                                    'id' => $row_segments['id_search_segment'],
                                    'label' => $row_segments['search_segment_label']
                                ];
                            }
                        }
                        $universes[] = [
                            'id' => $row['id_search_universe'],
                            'label' => $row['search_universe_label'],
                            'segments' => $segments
                        ];
                    }
                }
                $response['content'] = encoding_normalize::json_encode($universes);
                $response['content-type'] = "application/json";
                break;
            default :
                $response = parent::execute_ajax();
                break;
        }
        return $response;
    }

}
