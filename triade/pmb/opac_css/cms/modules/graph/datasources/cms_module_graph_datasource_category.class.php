<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_graph_datasource_category.class.php,v 1.7 2023/10/27 11:29:45 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $opac_thesaurus, $opac_thesaurus_defaut;

class cms_module_graph_datasource_category extends cms_module_common_datasource_list
{

    /**
     * Permet de définir la liste des parametres et leur valeur par defaut
     */
    public const DEFAULT_VALUE = [
        'specific_levels_to_load' => 2,
        'generic_levels_to_load' => 2
    ];

    public function __construct($id = 0)
    {
        parent::__construct($id);
        $this->sortable = false;
        $this->limitable = false;
    }

    /**
     * Definition des selecteurs utilisables pour la source de donnees
     *
     *  @return array
     */
    public function get_available_selectors()
    {
        return [
            "cms_module_common_selector_global_var",
            "cms_module_common_selector_category",
        ];
    }

    /**
     * Recuperation du formulaire
	 *
     * @return string
     */
    public function get_form()
    {
        $form = parent::get_form();

        $this->parameters['specific_levels_to_load'] = $this->parameters['specific_levels_to_load'] ?? static::DEFAULT_VALUE['specific_levels_to_load'];
        $this->parameters['generic_levels_to_load'] = $this->parameters['generic_levels_to_load'] ?? static::DEFAULT_VALUE['generic_levels_to_load'];

        /* Niveaux inferieurs */
        $form .= "<div class='row'>
            <div class='colonne3'>
                <label for=''>" . $this->format_text($this->msg['cms_module_graph_datasource_category_specific_levels_to_load']) . "</label>
            </div>
            <div class='colonne-suite'>
                <input min='0' max='10' required
                    type='number'
                    id='specific_levels_to_load'
                    name='". $this->get_form_value_name('specific_levels_to_load') ."'
                    class='saisie-20em'
                    value='". intval($this->parameters['specific_levels_to_load']) ."' />
            </div>
        </div>";
        /* Niveaux superieurs */
        $form .= "<div class='row'>
            <div class='colonne3'>
                <label for=''>" . $this->format_text($this->msg['cms_module_graph_datasource_category_generic_levels_to_load']) . "</label>
            </div>
            <div class='colonne-suite'>
                <input min='0' max='10' required
                    type='number'
                    id='generic_levels_to_load'
                    name='". $this->get_form_value_name('generic_levels_to_load') ."'
                    class='saisie-20em'
                    value='". intval($this->parameters['generic_levels_to_load']) ."' />
            </div>
        </div>";
        return $form;
    }

    /*
     * Sauvegarde des donnees depuis le formulaire
     *
     * @return void
     */
    public function save_form()
    {
        $this->parameters['specific_levels_to_load'] = $this->get_value_from_form('specific_levels_to_load');
        $this->parameters['generic_levels_to_load'] = $this->get_value_from_form('generic_levels_to_load');
        return parent::save_form();
    }

    /**
     * Recuperation des donnees de la source
     *
     * @return array
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        if ($selector) {
            $idNoeud = intval($selector->get_value());
            $data = $this->getDataById($idNoeud);
            return $data;
        }
    }

    /**
     * Recuperation des donnees de la source depuis un id de noeud
     *
     * @param $idNoeud
     *
     * @return array
     */
    public function getDataById($idNoeud = 0)
    {
        $idNoeud = intval($idNoeud);

        $specific_levels_to_load = ($this->parameters['specific_levels_to_load']) ?? static::DEFAULT_VALUE['specific_levels_to_load'];
        $generic_levels_to_load = ($this->parameters['generic_levels_to_load']) ?? static::DEFAULT_VALUE['generic_levels_to_load'];

        $data = [];

        if ( !$idNoeud ) {

            // Pas de noeud defini, on retourne la liste des thesaurus accessibles en OPAC
            $data["current"] = 'root';
            $data['tree'] = $this->getNodesFromFakeRootNode($this->msg['cms_module_graph_datasource_category_fake_root_name'], $specific_levels_to_load);

        } else {

            $data['current'] = $idNoeud;
            $data['tree'] = $this->getNodesFromParent($idNoeud, $specific_levels_to_load, $generic_levels_to_load);
        }
        return $data;

    }

    /**
     * Generation des noeuds depuis un noeud Root factice
     *
     * @param string $name : nom du noeud Root
     * @param int $specific_levels_to_load : nb de niveaux specifiques a charger
     *
     * @return array
     */
    protected function getNodesFromFakeRootNode(string $name = "ROOT", int $specific_levels_to_load = 0)
    {
        $name = (trim($name)) ? $name : "ROOT";
        $children = $this->getOPACThesaurusNodes($specific_levels_to_load);
        return [
            'id'    => 'root',
            'name'  => $name,
            'data'  => [
                'hasChildren'   => !empty($children),
                'entity_type'   => TYPE_CATEGORY,
                'entity'        => new StdClass,
                'tooltip'       => '',
            ],
            'children' => $children,
        ];
    }


    /**
     * Generation noeuds Thesaurus
     *
     * @param int $specific_levels_to_load : nb de niveaux specifiques a charger
     *
     * @return array
     */
    protected function getOPACThesaurusNodes(int $specific_levels_to_load = 0)
    {
        global $opac_thesaurus, $opac_thesaurus_defaut;

        $full_list = thesaurus::getFullThesaurusList();
        $final_list = [];

        //mode = 0, on prend le thesaurus par defaut, sinon, on prend tous les thesaurus actifs en opac
        if( !$opac_thesaurus ) {
            $final_list[$opac_thesaurus_defaut] = $full_list[$opac_thesaurus_defaut];
        } else {
            foreach($full_list as $k => $v) {
                if( $v['opac_active'] == 1 ) {
                    $final_list[$k] = $v;
                }
            }
        }

        $opac_thesaurus_nodes = [];

        foreach($final_list as $k => $v) {
            if($v['opac_active']) {
                $thesaurus = new thesaurus($k);
                $opac_thesaurus_nodes[] = [
                    'id'    => 'thes_'.$k,
                    'name'  => $v['libelle_thesaurus'],
                    'data'  => [
                        'entity_type'   => TYPE_CATEGORY,
                        'hasChildren'   => categories::hasChildren($v['num_noeud_racine']),
                        'entity'        => new StdClass,
                        'tooltip'       => '',
                    ],
                    'children' => $this->getChildrenCategoryNodes($v['num_noeud_racine'], $thesaurus, $specific_levels_to_load),
                ];
                unset($thesaurus);
            }

        }

        return $opac_thesaurus_nodes;
    }


    /**
     * Recherche noeud parent et generation des noeuds enfants a partir de ce noeud
     *
     * @param int $idNoeud
     * @param int $specific_levels_to_load
     * @param int $generic_levels_to_load
     *
     * @return array
    */
    protected function getNodesFromParent(int $idNoeud = 0, int $specific_levels_to_load = 0, int $generic_levels_to_load = 0)
    {
        $thesaurus = thesaurus::getByEltId($idNoeud);
        $parents = noeuds::listAncestors($idNoeud);
        $parents = array_slice($parents, 0, $generic_levels_to_load);
        $nbParents = count($parents);
        if(!empty($parents)) {
            $idParent = array_pop($parents);
        }
        $category = $this->fetchCategory($idParent, $thesaurus);

        return [
            'id' => $category['id_noeud'],
            'name' => $category['libelle_categorie'],
            'data' => [
                'entity_type' => TYPE_CATEGORY,
                'hasChildren' => categories::hasChildren($category['id_noeud']),
                'entity' => authorities_collection::get_authority('authority', 0, [
                    "num_object" => $category['id_noeud'],
                    "type_object" => AUT_TABLE_CATEG
                ]),
                'tooltip' => ''
            ],
            'children' => $this->getChildrenCategoryNodes($idParent, $thesaurus, $specific_levels_to_load + $nbParents),
        ];
    }


    /**
     * Generation noeuds specifiques d'une categorie (fonction recursive)
     *
     * @param int $idNoeud : id du noeud parent
     * @param thesaurus $thesaurus : objet thesaurus
     * @param int $specific_levels_to_load : nb de niveaux specifiques a charger
     *
     * @return array
     */
    protected function getChildrenCategoryNodes(int $idNoeud = 0, thesaurus $thesaurus = null, int $specific_levels_to_load = 0)
    {
        if ( !$idNoeud || is_null($thesaurus) ) {
            return [];
        }
        $specific_levels_to_load --;
        if( 0 > $specific_levels_to_load ) {
            return [];
        }
        $nodes = [];
        $children = $this->fetchChildrenCategory($idNoeud);
        for ($i = 0; $i < count($children); $i++) {

            if (empty($children[$i])) {
                continue;
            }

            $nodes[] = [
                "id" => $children[$i]['num_noeud'],
                "name" => $children[$i]['libelle_categorie'],
                'data' => [
                    'entity_type' => TYPE_CATEGORY,
                    'hasChildren' => categories::hasChildren($children[$i]['num_noeud']),
                    'entity' => authorities_collection::get_authority('authority', 0, [
                        "num_object" => $children[$i]['num_noeud'],
                        "type_object" => AUT_TABLE_CATEG
                    ]),
                    'tooltip' => ''
                ],
                "children" => $this->getChildrenCategoryNodes($children[$i]['num_noeud'], $thesaurus, $specific_levels_to_load),
            ];
        }
        return $nodes;
    }


    /**
     * Permet d'aller chercher les infos d'une categorie donnee
     *
     * @param integer $idNoeud
     * @param thesaurus $thesaurus
     * @return array|null
     */
    protected function fetchCategory(int $idNoeud, thesaurus $thesaurus)
    {
        global $lang;

        $result = pmb_mysql_query("SELECT id_noeud, num_parent, libelle_categorie, if (langue = '{$lang}', 2, if(langue= '{$thesaurus->langue_defaut}' , 1, 0)) as langOrder
            FROM noeuds, categories
            WHERE id_noeud ='{$idNoeud}' AND noeuds.id_noeud = categories.num_noeud
            ORDER BY langOrder DESC LIMIT 1
        ");

        if (pmb_mysql_num_rows($result)) {
            return pmb_mysql_fetch_assoc($result);
        }
        return null;
    }

    /**
     * Permet d'aller chercher les enfants d'une categorie donnee
     *
     * @param integer $idNoeud
     * @return array
     */
    protected function fetchChildrenCategory(int $idNoeud)
    {
        global $lang;

        $children = [];
        $result = categories::listChilds($idNoeud, $lang, 0);

        if (pmb_mysql_num_rows($result)) {
            while($row = pmb_mysql_fetch_assoc($result)) {
                $children[] = $row;
            }
        }

        return $children;
    }

}
