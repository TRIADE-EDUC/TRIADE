<?php

// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_graph_datasource_concept.class.php,v 1.5.4.1 2025/04/30 08:33:13 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $opac_thesaurus, $opac_thesaurus_defaut;

class cms_module_graph_datasource_concept extends cms_module_common_datasource_list
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
            "cms_module_common_selector_concept",
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
                <label for=''>" . $this->format_text($this->msg['cms_module_graph_datasource_concept_specific_levels_to_load']) . "</label>
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
                <label for=''>" . $this->format_text($this->msg['cms_module_graph_datasource_concept_generic_levels_to_load']) . "</label>
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

    /**
     * Sauvegarde des donnees depuis le formulaire
     *
     * @return bool
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
            $idConcept = $selector->get_value();
            $data = $this->getDataById($idConcept);
            return $data;
        }
        return [];
    }

    /**
     * Recuperation des donnees de la source depuis un id de concept
     *
     * @param string $conceptUri : uri du concept
     *
     * @return array
     */
    public function getDataById(string $conceptUri = '')
    {
        if (is_numeric($conceptUri)) {
            $conceptUri = onto_common_uri::get_uri($conceptUri);
        }
        $conceptUri = trim($conceptUri);

        $specific_levels_to_load = ($this->parameters['specific_levels_to_load']) ?? static::DEFAULT_VALUE['specific_levels_to_load'];
        $generic_levels_to_load = ($this->parameters['generic_levels_to_load']) ?? static::DEFAULT_VALUE['generic_levels_to_load'];

        $data = [];
        if ( !$conceptUri ) {

            // Pas de concept fourni, on retourne la liste des schemas accessibles en OPAC
            $data["current"] = 'root';
            $data['tree'] = $this->getNodesFromFakeRootNode($this->msg['cms_module_graph_datasource_concept_fake_root_name'], $specific_levels_to_load);

        } else {

            $data['current'] = $conceptUri;
            $data['tree'] = $this->getNodesFromParent($conceptUri, $specific_levels_to_load, $generic_levels_to_load);
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
        $children = $this->getSchemeNodes($specific_levels_to_load);
        $nodes = [
            'id'    => 'root',
            'name'  => $name,
            'data'  => [
                'hasChildren'   => !empty($children),
                'entity_type'   => TYPE_CONCEPT,
                'entity'        => new StdClass,
                'tooltip'       => '',
            ],
            'children' => $children,
        ];
        return $nodes;
    }


    /**
     * Generation noeuds Schemas
     *
     * @param int $specific_levels_to_load : nb de niveaux specifiques a charger
     *
     * @return array
     */
    protected function getSchemeNodes(int $specific_levels_to_load = 0)
    {
        global $msg, $lang;

        $scheme_list = skos_concept::getSchemes();
        //Ajout noeud sans schema
        $scheme_list['no_scheme'] = [
            'default' => $msg['skos_view_concept_no_scheme']
        ];

        $scheme_nodes = [];
        foreach($scheme_list as $k => $v) {
            $children = $this->getTopConceptNodes($k, $specific_levels_to_load);
            $name = $v[substr($lang, 0, 2)] ?? $v['default'];
            $scheme_nodes[] = [
                'id'    => $k,
                'name'  => $name,
                'data'  => [
                    'hasChildren'   => !empty($children),
                    'entity_type'   => TYPE_CONCEPT,
                    'entity'        => new StdClass,
                    'tooltip'       => '',
                ],
                'children' => $this->getTopConceptNodes($k, $specific_levels_to_load),
            ];
        }
        return $scheme_nodes;
    }

    /**
     * Generation noeuds Top Concepts
     *
     * @param $schemeUri : uri du schema
     * @param int $specific_levels_to_load : nb de niveaux specifiques a charger
     *
     * @return array
     */
    protected function getTopConceptNodes(string $schemeUri = '', int $specific_levels_to_load = 0)
    {
        global $lang;

        $top_concept_nodes = skos_concept::getTopConcepts($schemeUri);
        if(empty($top_concept_nodes)) {
            return [];
        }
        $final_nodes = [];
        foreach($top_concept_nodes as $k => $v) {
            $name = $v[substr($lang, 0, 2)] ?? $v['default'];
            $final_nodes[] = [
                'id'    => $k,
                'name'  => $name,
                'data' => [
                    'hasChildren'   => skos_concept::has_children($k),
                    'entity_type' => TYPE_CONCEPT,
                    'entity' => authorities_collection::get_authority('authority', 0, [
                        "num_object" => $k,
                        "type_object" => AUT_TABLE_CONCEPT
                    ]),
                    'tooltip' => ''
                ],
                'children' => $this->getChildrenConceptNodes($k, $specific_levels_to_load),
            ];
        }
        return $final_nodes;
    }

    /**
     * Recherche du concept parent et generation des concepts enfants a partir du concept passe en parametre
     *
     * @param int $conceptUri : uri du concept
     * @param int $specific_levels_to_load : nb de niveaux specifiques a charger
     * @param int $generic_levels_to_load : nb de niveaux generiques a charger
     *
     * @return array
    */
    protected function getNodesFromParent(string $conceptUri = '', int $specific_levels_to_load = 0, int $generic_levels_to_load = 0)
    {
        $concept = new skos_concept(0, $conceptUri);
        $parent = null;
        $nbParents = 0;
        do {
            $parent = $concept->get_broaders()->get_concepts();
            if(!empty($parent[0])) {
                $concept = $parent[0];
            }

            $nbParents++;
            $generic_levels_to_load--;
        } while ($generic_levels_to_load > 0 && !empty($parent) );

        if(empty($concept)) {
            return [];
        }

        return [
            'id' => $concept->get_uri(),
            'name' => $concept->get_display_label(),
            'data' => [
            	'hasChildren'   => skos_concept::has_children($concept->get_uri()),
                'entity_type' => TYPE_CONCEPT,
                'entity' => authorities_collection::get_authority('authority', 0, [
                    "num_object" => $concept->get_uri(),
                    "type_object" => AUT_TABLE_CONCEPT
                ]),
                'tooltip' => ''
            ],
            'children' => $this->getChildrenConceptNodes($concept->get_uri(), $specific_levels_to_load + $nbParents),
        ];
    }


    /**
     * Generation noeuds specifiques d'un concept (fonction recursive)
     *
     * @param string $conceptUri : uri du concept parent
     * @param int $specific_levels_to_load : nb de niveaux specifiques a charger
     *
     * @return array
     */
    protected function getChildrenConceptNodes(string $conceptUri = '', int $specific_levels_to_load = 0)
    {
        global $lang;

        if ( !$conceptUri ) {
            return [];
        }
        $specific_levels_to_load --;
        if( 0 > $specific_levels_to_load ) {
            return [];
        }
        $final_nodes = [];
        $concept = new skos_concept(0, $conceptUri);
        $children = $concept->get_narrowers()->get_concepts();
        for ($i = 0; $i < count($children); $i++) {
            $final_nodes[] = [
                "id" => $children[$i]->get_uri(),
                "name" => $children[$i]->get_display_label(),
                'data' => [
                    'hasChildren'   => skos_concept::has_children($children[$i]->get_uri()),
                    'entity_type' => TYPE_CONCEPT,
                    'entity' => authorities_collection::get_authority('authority', 0, [
                        "num_object" => $children[$i]->get_uri(),
                        "type_object" => AUT_TABLE_CONCEPT
                    ]),
                    'tooltip' => ''
                ],
                "children" => $this->getChildrenConceptNodes($children[$i]->get_uri(), $specific_levels_to_load),
            ];
        }
        return $final_nodes;
    }

}
