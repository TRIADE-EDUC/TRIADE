<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: electre.class.php,v 1.10.4.8 2025/05/06 15:25:30 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\Thumbnail\Models\Sources\Entities\Common\Electre\ElectreAPIClient;

class electre extends connector
{

    /* URL de base de l'API */
    protected $electre_base_url = '';

    /* URL de recuperation d'un token */
    protected $electre_token_url = '';

    /* Identifiant client */
    protected $electre_client_id = '';

    /* Nom client */
    protected $electre_client_user;
    /* Secret client */
    protected $electre_client_secret = '';

    /* nb max resultats recherche */
    protected $electre_max_results;

    /* Instance classe client */
    protected $electre_client = null;

    protected $buffer = [];

    /* Tableau nom fonction => code */
    protected static $author_functions = null;

    protected $electreConfig = null;

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "electre";
    }

    /**
     * Recuperation des parametres de la source
     */
    protected function unserialize_source_params($source_id)
    {
        $params = parent::unserialize_source_params($source_id);
        if(!empty($params['PARAMETERS']['electre_base_url'])) {
            $this->electre_base_url = $params['PARAMETERS']['electre_base_url'];
        }
        if(!empty($params['PARAMETERS']['electre_token_url'])) {
            $this->electre_token_url = $params['PARAMETERS']['electre_token_url'];
        }
        if(!empty($params['PARAMETERS']['electre_client_id'])) {
            $this->electre_client_id = $params['PARAMETERS']['electre_client_id'];
        }
        if(!empty($params['PARAMETERS']['electre_client_secret'])) {
            $this->electre_client_secret = $params['PARAMETERS']['electre_client_secret'];
        }
        if(!empty($params['PARAMETERS']['electre_client_user'])) {
            $this->electre_client_user = $params['PARAMETERS']['electre_client_user'];
        }
        if(!empty($params['PARAMETERS']['electre_max_results'])) {
            $this->electre_max_results = $params['PARAMETERS']['electre_max_results'];
        }
        return $params;
    }


    /**
     * Sauvegarde des parametres de la source
     */
    public function make_serialized_source_properties($source_id)
    {

        global $electre_base_url, $electre_token_url;
        global $electre_client_id, $electre_client_user, $electre_client_secret;
        global $electre_max_results;

        if(empty($electre_base_url)) {
            $electre_base_url = '';
        }
        if(empty($electre_token_url)) {
            $electre_token_url = '';
        }
        if(empty($electre_client_id)) {
            $electre_client_id = '';
        }
        if(empty($electre_client_user)) {
            $electre_client_user = '';
        }
        if(empty($electre_client_secret)) {
            $electre_client_secret = '';
        }

        $electre_max_results = intval($electre_max_results);
        if(empty($electre_max_results)) {
            $electre_max_results = $this->electre_max_results;
        }

        $this->sources[$source_id]['PARAMETERS'] = serialize(
            [
                'electre_base_url'          => stripslashes($electre_base_url),
                'electre_token_url'         => stripslashes($electre_token_url),
                'electre_client_id'         => stripslashes($electre_client_id),
                'electre_client_user'       => stripslashes($electre_client_user),
                'electre_client_secret'     => stripslashes($electre_client_secret),
                'electre_max_results'       => $electre_max_results,
            ]
            );
    }


    /**
     * Construction du formulaire des proprietes de la source
     */
    public function source_get_property_form($source_id)
    {
        global $charset;

        $this->unserialize_source_params($source_id);

        if(!$this->electre_base_url) {
            $this->electre_base_url = ElectreAPIClient::DEFAULT_API_BASE_URL;
        }
        if(!$this->electre_token_url) {
            $this->electre_token_url = ElectreAPIClient::DEFAULT_API_TOKEN_URL;
        }
        if(!$this->electre_client_id) {
            $this->electre_client_id = ElectreAPIClient::DEFAULT_CLIENT_ID;
        }
        if(!$this->electre_max_results) {
            $this->electre_max_results = ElectreAPIClient::DEFAULT_MAX_RESULTS;
        }

        $form = "
            <div class='row'>&nbsp;</div>
                <h3>".$this->msg['electre_ws']."</h3>
            <div class='row'>&nbsp;</div>

            <div class='row'>
                <div class='colonne3'>
                	<label for='electre_base_url'>".$this->msg["electre_base_url"]."</label>
                </div>
                <div class='colonne_suite'>
                	<input type='text' name='electre_base_url' id='electre_base_url' class='saisie-80em' value='".htmlentities($this->electre_base_url,ENT_QUOTES,$charset)."' />
                </div>
            </div>

            <div class='row'>
                <div class='colonne3'>
                    <label for='electre_token_url'>".$this->msg["electre_token_url"]."</label>
                </div>
                <div class='colonne_suite'>
                    <input type='text' name='electre_token_url' id='electre_token_url' class='saisie-80em' value='".htmlentities($this->electre_token_url,ENT_QUOTES,$charset)."' />
                </div>
            </div>

            <div class='row'>
                <div class='colonne3'>
                    <label for='electre_client_id' >".$this->msg["electre_client_id"]."</label>
                </div>
                <div class='colonne_suite'>
                    <input type='text' name='electre_client_id' id='electre_client_id' class='saisie-30em' value='".htmlentities($this->electre_client_id,ENT_QUOTES,$charset)."' />
                </div>
            </div>

            <div class='row'>
                <div class='colonne3'>
                    <label for='electre_client_user' >".$this->msg["electre_client_user"]."</label>
                </div>
                <div class='colonne_suite'>
                    <input type='text' name='electre_client_user' id='electre_client_user' class='saisie-30em' value='".htmlentities($this->electre_client_user,ENT_QUOTES,$charset)."' />
                </div>
            </div>

            <div class='row'>
                <div class='colonne3'>
                    <label for='electre_client_secret' >".$this->msg["electre_client_secret"]."</label>
                </div>
                <div class='colonne_suite'>
                    <input type='password' name='electre_client_secret' id='electre_client_secret' class='saisie-30em' autocomplete='off' value='".htmlentities($this->electre_client_secret,ENT_QUOTES,$charset)."' />
                    <span class='fa fa-eye' onclick='toggle_password(this, \"electre_client_secret\");' ></span>
                </div>
            </div>

            <div class='row'>&nbsp;</div>
            <h3>".$this->msg['electre_search_params']."</h3>
            <div class='row'>&nbsp;</div>

            <div class='row'>
                <div class='colonne3'>
                    <label for='electre_max_results' >".$this->msg["electre_max_results"]."</label>
                </div>
                <div class='colonne_suite'>
                    <input type='number' step='1' min='0' name='electre_max_results' id='electre_max_results' class='saisie-10em' value='".$this->electre_max_results."' />
                </div>
            </div>
            <div class='row'></div>";
        return $form;
    }


    /**
     * Recherche
     *
     * @param int $source_id : Id source
     * @param array $query : Tableau parametres recherche
     * @param string $search_id : Id recherche
     */
    public function search($source_id, $query, $search_id)
    {
        // check parametres
        $source_id = intval($source_id);
        if(!$source_id) {
            return;
        }
        if(!is_array($query) || empty($query)) {
            return;
        }

        // instanciation client
        $client = $this->get_client($source_id);

        // construction recherche
        for ($i=0 ; $i < count($query) ; $i++) {

            $done = false;
            $j = 0;
            $search_mapping = $this->getSearchMapping($query[$i]->ufield, $query[$i]->values[0]);
            while(!$done) {

                if(!isset($search_mapping['query_params']['offset'])) {
                    $search_mapping['query_params']['offset'] = 0;
                } else {
                    $search_mapping['query_params']['offset'] += 100;
                }

                $search_results = $client->search($search_mapping['query_params'], $search_mapping['method']);
                if (empty($search_results['notices'])) {
                    break;
                }

                $this->prepare_records($source_id, $search_id, $search_results);
                $this->rec_records($source_id, $search_id);

                if($search_results['offset'] + $search_results['limit'] >= $search_results['total'] ) {
                    $done = true;
                }
                if($search_results['offset'] + $search_results['limit'] >= $this->electre_max_results) {
                    $done = true;
                }
                $j++;
                if($j > 10) {
                    $done = true;
                }
            }
        }
    }



    /**
     * Instanciation client API
     *
     * @param int $source_id
     */
    protected function get_client(int $source_id)
    {
        if(is_null($this->electre_client)) {
            $params = $this->get_source_params($source_id);
            $params = unserialize($params['PARAMETERS']);
            $this->electre_client_id = $params['electre_client_id'] ?? '';
            $this->electre_client_secret = $params['electre_client_secret'] ?? '';
            $this->electre_client_user = $params['electre_client_user'] ?? '';
            $this->electre_base_url = $params['electre_base_url'] ?? ElectreAPIClient::DEFAULT_API_BASE_URL;
            $this->electre_token_url = $params['electre_token_url'] ?? ElectreAPIClient::DEFAULT_API_TOKEN_URL;
            $this->electre_max_results = $params['electre_max_results'] ?? ElectreAPIClient::DEFAULT_MAX_RESULTS;

            $this->electre_client = new ElectreAPIClient(
                $this->electre_client_id,
                $this->electre_client_secret,
                $this->electre_client_user,
                $this->electre_base_url,
                $this->electre_token_url
            );
        }
        return $this->electre_client;
    }



    /**
     * Retourne la methode de recherche et les parametres a utiliser au niveau de l'API
     *
     * @param string $ufield : id champ de recherche
     * Voir attributs unimarcField decrits dans :
     * - includes/search_queries/search_fields_unimarc.xml
     * - includes/search_queries/search_simple_fields_unimarc.xml
     *
     * @return array : ['query_params' , 'method'];
     */
    protected function getSearchMapping(string $ufield, string $value )
    {
        // TODO : Prendre en compte les autorisations definies cote API
        // Pour l'instant, seul le parametre q est autorise
        // Il permet une recherche libre sur les champs titre, auteur, éditeur, collection, ISBN, EAN, avec un OU logique.

        $search_mapping = [];
        $search_mapping['query_params'] = ['q' => $value];
        $search_mapping['method'] = 'notices';
        return $search_mapping;
    }


    /**
     * Mise en forme des resultats
     *
     * @param int $source_id : Id source
     * @param string $search_id : Id recherche
     * @param array $search_results
     *
     */
    protected function prepare_records($source_id, $search_id, $search_results = [])
    {
        if( empty($search_results['notices']) || !is_array($search_results['notices']) ) {
            return;
        }

        // Traitement des notices
        $records = $search_results['notices'];
        foreach($records as $record) {

            $this->prepare_record($source_id, $search_id, $record);
        }

        // TODO : Traitement des liens
        // TODO : Traitement des facettes
    }


    /**
     * Mise en forme d'un resultat
     *
     * @param int $source_id : Id source
     * @param string $search_id : Id recherche
     * @param array $record
     *
     * @return void
     */
    protected function prepare_record($source_id, $search_id, $record = [])
    {

        if(empty($record) || !is_array($record)) {
            return;
        }

        // noticeId
        $ref = $record['noticeId'];

        // Id deja existant
        if($this->has_ref($source_id, $ref, $search_id)){
            return;
        }


        // type doc et entetes
        $config = $this->getElectreConfig();
        if (isset($record['support'])) {
            $support = $config['supports'][$record['support']['supportId']] ?? $config['supports']['__DEFAULT__'];
        } else {
            $support = $config['supports']['__DEFAULT__'];
        }

        if (empty($support['typdoc'])) {
            $support['typdoc'] = $config['supports']['__DEFAULT__']['typdoc'] ?? 'a';
        }

        $unimarc_headers = [
            "rs" => "*",
            "ru" => "*",
            "el" => "*",
            "bl" => "m",
            "hl" => "0",
            "dt" => $support['typdoc'],
        ];

        $unimarc_record = [];
        $fo = 0;
        $so = 0;

        // noticeId : Identifiant de la notice chez Electre dans le nouveau référentiel mis en place sur Electre NG.
        $unimarc_record[] = [
            'ufield' => '001',
            'usubfield' => '',
            'value' => $ref,
            'field_order' => $fo++,
            'subfield_order' => $so,
        ];

        // Isbn
        $isbn = $record['isbns'][0] ?? '';
        if($isbn) {
            $unimarc_record[] = [
                'ufield' => '010',
                'usubfield' => 'a',
                'value' => $isbn,
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }

        // prix
        /* prix => [
         *      'ttc' => '?',
         *      'devise' => '?'
         */
        $prix = $record['prix']['ttc'] ?? '';
        $devise = $record['prix']['devise'] ?? '';
        $prix .= ($prix && $devise) ? ' '. $devise : '';
        if($prix) {
            $unimarc_record[] = [
                'ufield' => '010',
                'usubfield' => 'd',
                'value' => $prix,
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }

        // languesEcriture : Langues dans lesquelles est écrit l'ouvrage. (Plusieurs langues possibles).
        /*
         languesEcriture => [
         'codeLangue' => '?',
         'libelleLangue' => '?',
         ];
         */
        if( !empty($record['languesEcriture']) ) {
            foreach($record['languesEcriture'] as $langueEcriture ) {
                if($langueEcriture['codeLangue']) {
                    $unimarc_record[] = [
                        'ufield' => '101',
                        'usubfield' => 'a',
                        'value' => $langueEcriture['codeLangue'],
                        'field_order' => $fo++,
                        'subfield_order' => $so,
                    ];
                }
            }
        }

        // groupeTitres : Ensemble de titres, sous-titres et mentions associés
        // titresPrincipaux
        // titresContenus
        // typeTitre : titre simple, titres parallèles, titres simple suivi de, ou précédé de, titres contenus...(un titre simple est au minimum fourni)
        /*
         groupeTitres => [
         'titresPrincipaux' => [
         0 => [
         'titres' => [
         0 => [
         'typeTitre' => 'simple',
         'libelle' => '?',
         'sousTitres' => [
         0 => '?'
         ]
         ]
         ],
         'mentions' => [
         0 => '?'
         ]
         ]
         ],
         'titresContenus' => [
         0 => [
         'titres' => [
         0 => [
         'typeTitre' => 'simple',
         'libelle' => '?',
         'sousTitres' => [
         0 => '?'
         ]
         ]
         ],
         'mentions' => [
         0 => '?'
         ]
         ]
         ]
         ]
         */
        $titre = '';
        $sous_titre = '';
        $titre_serie = '';
        $volume_serie = '';
        $titre_volume = '';

        $titresPrincipaux = $record['groupeTitres']['titresPrincipaux'][0]['titres'] ?? [];
        if(!empty($titresPrincipaux)) {
            foreach($titresPrincipaux as $titrePrincipal) {
                $typeTitre = $titrePrincipal['typeTitre'] ?? '';
                $libelle = $titrePrincipal['libelle'] ?? '';
                $sousTitres = $titrePrincipal['sousTitres'] ?? '';
                if($typeTitre == 'simple' && $libelle) {
                    $titre = $libelle;
                    if(!empty($sousTitres[0])) {
                        $sous_titre = $sousTitres[0];
                    }
                    continue;
                }
            }
        }


        $titresDensemble = $record['groupeTitres']['titresDensemble'][0]['titres'] ?? [];
        $volume_serie = $record['groupeTitres']['titresDensemble'][0]['numeroVolume'] ?? '';
        if(!empty($titresDensemble)) {
            foreach($titresDensemble as $titreDensemble) {
                $typeTitre = $titreDensemble['typeTitre'] ?? '';
                $libelle = $titreDensemble['libelle'] ?? '';
                $sousTitres = $titreDensemble['sousTitres'] ?? '';
                if($typeTitre == 'simple' && $libelle) {
                    $titre_serie = $libelle;
                    if(!empty($sousTitres[0])) {
                        $titre_volume = $sousTitres[0];
                    }
                    continue;
                }
            }
        }


        $titre = $titre ? $titre : $titre_volume;

        if(!$titre) {
            return;
        }

        $unimarc_record[] = [
            'ufield' => '200',
            'usubfield' => 'a',
            'value' => $titre,
            'field_order' => $fo++,
            'subfield_order' => $so,
        ];

        if($sous_titre) {
            $unimarc_record[] = [
                'ufield' => '200',
                'usubfield' => 'e',
                'value' => $sous_titre,
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }

        if($titre_serie) {
            $unimarc_record[] = [
                'ufield' => '461',
                'usubfield' => 't',
                'value' => $titre_serie,
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }
        if($volume_serie) {
            $unimarc_record[] = [
                'ufield' => '461',
                'usubfield' => 'v',
                'value' => $volume_serie,
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }


        // editeurs
        /* editeurs => [
         *      0 => [
         *          'formeBibEditeur' => '?',
         *          'lieuPublication' => '?',
         *      ]
         * ]
         */
        if( !empty($record['editeurs']) ) {
            foreach($record['editeurs'] as $editeur) {
                $formeBibEditeur = $editeur['formeBibEditeur'] ?? '';
                $lieuPublication = $editeur['lieuPublication'] ?? '';
                if($lieuPublication) {
                    $unimarc_record[] = [
                        'ufield' => '210',
                        'usubfield' => 'a',
                        'value' => $lieuPublication,
                        'field_order' => $fo,
                        'subfield_order' => $so++,
                    ];
                }
                if($formeBibEditeur) {
                    $unimarc_record[] = [
                        'ufield' => '210',
                        'usubfield' => 'c',
                        'value' => $formeBibEditeur,
                        'field_order' => $fo,
                        'subfield_order' => $so++,
                    ];
                }
                $fo++;
                $so = 0;
            }
        }

        // anneeEdition
        if( !empty($record['anneeEdition']) ) {
            $unimarc_record[] = [
                'ufield' => '210',
                'usubfield' => 'd',
                'value' => $record['anneeEdition'],
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }

        // descriptionPhysique
        /* descriptionPhysique => [
         *      'nbPages => '?',
         * ]
         */
        if( !empty($record['descriptionPhysique']['nbPages']) ) {
            $config = $this->getElectreConfig();
            $pages = $record['descriptionPhysique']['nbPages'] . $config['template']['215a_pages_unit'];
            $unimarc_record[] = [
                'ufield' => '215',
                'usubfield' => 'a',
                'value' => $this->makeTemplate($config['template']['215a'], ['pages' => $pages]),
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }

        // nbVolume typeVolume / reliure / mentionIllustrations > 215$c
        $config = $this->getElectreConfig();
        $data215c = [
            'volume' => !empty($record['descriptionPhysique']['nbVolumes']) ? $record['descriptionPhysique']['nbVolumes'] : '',
            'typeVolume' => !empty($record['descriptionPhysique']['typeVolume']) ? $record['descriptionPhysique']['typeVolume'] : '',
            'reliure' => !empty($record['descriptionPhysique']['reliure']) ? $record['descriptionPhysique']['reliure'] : '',
            'mentionIllustration' => !empty($record['descriptionPhysique']['mentionIllustrations']) ? $record['descriptionPhysique']['mentionIllustrations'] : ''
        ];
        $unimarc_record[] = [
            'ufield' => '215',
            'usubfield' => 'c',
            'value' => $this->makeTemplate($config['template']['215c'], $data215c),
            'field_order' => $fo++,
            'subfield_order' => $so,
        ];

        // hauteur / largeur / epaisseur / poids > 215$d
        $config = $this->getElectreConfig();
        $dimensions = '';
        $tab_dimensions = [];
        $tab_dimensions[] = !empty($record['descriptionPhysique']['hauteur']) ? $record['descriptionPhysique']['hauteur'] : '';
        $tab_dimensions[] = !empty($record['descriptionPhysique']['largeur']) ? $record['descriptionPhysique']['largeur'] : '';
        $tab_dimensions[] = !empty($record['descriptionPhysique']['epaisseur']) ? $record['descriptionPhysique']['epaisseur'] : '';
        if(!empty($tab_dimensions)) {
            $dimensions = implode($config['template']['215d_dimensions_sep'], $tab_dimensions);
            $dimensions.= $config['template']['215d_dimensions_unit'];
        }
        $poids = !empty($record['descriptionPhysique']['poids']) ? $record['descriptionPhysique']['poids'] . $config['template']['215d_poids_unit'] : '';

        $data215d = [
            'dimensions' => $dimensions,
            'poids' => $poids
        ];
        $unimarc_record[] = [
            'ufield' => '215',
            'usubfield' => 'd',
            'value' => $this->makeTemplate($config['template']['215d'], $data215d),
            'field_order' => $fo++,
            'subfield_order' => $so,
        ];

        // notes
        /* notes => [
         *      'type => [
         *          'codeNote' => '?',
         *          'libelleNote' => '?',
         *          ],
         *       'contenuNote' => '?'
         * ]
         */
        $notes = [];
        if( !empty($record['notes']) ) {
            foreach ($record['notes'] as $note) {

                $note['contenuNote'] = trim($note['contenuNote']);
                if ($note['contenuNote'] !== '') {
                    $notes[] = $note['type']['libelleNote'] . ' : ' .$note['contenuNote'];
                }
            }
        }
        if(count($notes)) {
            $unimarc_record[] = [
                'ufield' => '327',
                'usubfield' => 'a',
                'value' => implode("\r\n", $notes),
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }
        // resume Electre
        if( !empty($record['resumeElectre']) ) {
            $unimarc_record[] = [
                'ufield' => '330',
                'usubfield' => 'a',
                'value' => $record['resumeElectre'],
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }

        // quatriemeDeCouvertureResume
        if( !empty($record['quatriemeDeCouvertureResume']) ) {
            $unimarc_record[] = [
                'ufield' => '930',
                'usubfield' => 'a',
                'value' => $record['quatriemeDeCouvertureResume'],
                'field_order' => $fo++,
                'subfield_order' => $so,
            ];
        }

        // collections / sous-collections
        /* collections => [
         *     0 => [
         *         'libelleCollection' => '?',
         *         'issn' => '?',
         *         'noDeCollection' => '?',
         *         'sousCollection' => [
         *             'libelleSousCollection' => '?',
         *             'issnSousCollection' => '?',
         *             'noDeSousCollection' => '?',
         *         ]
         *     ]
         * ]
         */
        $libelle_collection = '';
        $issn_collection = '';
        $numero_collection = '';
        $libelle_sous_collection = '';
        $issn_sous_collection = '';
        $numero_sous_collection = '';

        if(!empty($record['collections'][0])) {
            $libelle_collection = $record['collections'][0]['libelleCollection'] ?? '';
            if($libelle_collection) {

                $issn_collection = $record['collections'][0]['issn'] ?? '';
                $numero_collection = $record['collections'][0]['noDeCollection'] ?? '';

                $so = 0;
                $unimarc_record[] = [
                    'ufield' => '410',
                    'usubfield' => 't',
                    'value' => $libelle_collection,
                    'field_order' => $fo,
                    'subfield_order' => $so++,
                ];
                if($issn_collection) {
                    $unimarc_record[] = [
                        'ufield' => '410',
                        'usubfield' => 'x',
                        'value' => $issn_collection,
                        'field_order' => $fo,
                        'subfield_order' => $so++,
                    ];
                }
                if($numero_collection) {
                    $unimarc_record[] = [
                        'ufield' => '410',
                        'usubfield' => 'v',
                        'value' => $numero_collection,
                        'field_order' => $fo,
                        'subfield_order' => $so++,
                    ];
                }
                $fo++;

                $libelle_sous_collection = $record['collections'][0]['sousCollection']['libelleSousCollection'] ?? '';
                if($libelle_sous_collection) {

                    $issn_sous_collection = $record['collections'][0]['sousCollection']['issnSousCollection'] ?? '';
                    $numero_sous_collection = $record['collections'][0]['sousCollection']['noDeSousCollection'] ?? '';

                    $so = 0;
                    $unimarc_record[] = [
                        'ufield' => '411',
                        'usubfield' => 't',
                        'value' => $libelle_sous_collection,
                        'field_order' => $fo,
                        'subfield_order' => $so++,
                    ];
                    if($issn_sous_collection) {
                        $unimarc_record[] = [
                            'ufield' => '411',
                            'usubfield' => 'x',
                            'value' => $issn_sous_collection,
                            'field_order' => $fo,
                            'subfield_order' => $so++,
                        ];
                    }
                    if($numero_sous_collection) {
                        $unimarc_record[] = [
                            'ufield' => '411',
                            'usubfield' => 'v',
                            'value' => $numero_sous_collection,
                            'field_order' => $fo,
                            'subfield_order' => $so++,
                        ];
                    }
                    $fo++;
                }

            }
        }



        // auteursPrincipaux
        /* auteursPrincipaux => [
         *      0 => [
         *          'nom' => '?',
         *          'prenom' => '?',
         *          'qualificatifs' => '?',     date de naissance, date de mort, titre nobiliaire...
         *          'typeContribution' => '?'   Enumerated values in: ["Auteur", "Auteur (photographe)", "Auteur (illustrateur)", "Interviewer", "Personne interviewée", "Auteur du texte", "Auteur originel",
         *                                     "Auteur adapté", "Adaptateur", "Auteur douteux, prétendu", "Auteur (artiste)", "Librettiste", "Parolier", "Compositeur", "Programmeur", "Infographiste",
         *                                     "Scénariste", "Dialoguiste", "Concepteur", "Producteur", "Réalisateur de film"]
         *          'idBnf' => '?'              identifiant BNF de l'auteur
         *      ]
         * ]
         */

        //
        $auteursPrincipaux = $record['auteursPrincipaux'] ?? [];
        if(!empty($auteursPrincipaux)) {
            $ufield = '700';
            foreach( $auteursPrincipaux as $k => $auteurPrincipal) {
                $nom = $auteurPrincipal['nom'] ?? '';
                $prenom = $auteurPrincipal['prenom'] ?? '';
                if (empty($prenom) && strpos($nom, ',') !== false) {
                    $tmp = explode(',', $nom, 2);
                    $nom = trim($tmp[1]);
                    $prenom = trim($tmp[0]);
                }

                $qualificatifs_dates = $auteurPrincipal['qualificatifs'][0] ?? '';
                $typeContribution = $this->getFonctionFromTypeContribution($auteurPrincipal['typeContribution'] ?? '');
                if($k) {
                    $ufield = '701';
                }

                if($nom) {
                    $unimarc_record[] = [
                        'ufield' => $ufield,
                        'usubfield' => 'a',
                        'value' => $nom,
                        'field_order' => $fo,
                        'subfield_order' => $so++,
                    ];
                    if($prenom) {
                        $unimarc_record[] = [
                            'ufield' => $ufield,
                            'usubfield' => 'b',
                            'value' => $prenom,
                            'field_order' => $fo,
                            'subfield_order' => $so,
                        ];
                    }
                    if($qualificatifs_dates) {
                        $unimarc_record[] = [
                            'ufield' => $ufield,
                            'usubfield' => 'f',
                            'value' => $qualificatifs_dates,
                            'field_order' => $fo,
                            'subfield_order' => $so++,
                        ];
                    }
                    if($typeContribution) {
                        $unimarc_record[] = [
                            'ufield' => $ufield,
                            'usubfield' => '4',
                            'value' => $typeContribution,
                            'field_order' => $fo,
                            'subfield_order' => $so++,
                        ];
                    }
                    $fo++;
                    $so = 0;
                }
            }
        }

        // auteursSecondaires
        /* auteursSecondaires => [
         *      0 => [
         *          'nom' => '?',
         *          'prenom' => '?',
         *          'qualificatifs' => '?',     date de naissance, date de mort, titre nobiliaire...
         *          'typeContribution' => '?'   Enumerated values in: ["Traducteur", "Directeur de publication", "Editeur scientifique (ou intellectuel)", "Commentateur de texte", "Inconnu",
         *                                      "Auteur de l'idée originale", "Rédacteur / Rapporteur", "Photographe", "Illustrateur", "Coloriste", "Calligraphe", "Cartographe", "Auteur du matériel d'accompagnement",
         *                                      "Maquettiste", "Préfacier", "Postfacier", "Organisateur d'un congrès", "Organisateur d'une exposition", "Commentateur audio", "Photographe (film)", "Chorégraphe",
         *                                      "Directeur artistique", "Autres", "Collaborateur", "Narrateur", "Chef d'une interprétation musicale", "Soliste vocal", "Soliste instrumental", "Groupe musical",
         *                                      "Chanteur","Acteur", "Danseur", "Autres interprètes", "Directeur de collection", "Fondateur d'une revue, d'une collection"]
         *          'idBnf' => '?'              identifiant BNF de l'auteur
         *      ]
         * ]
         */
        $auteursSecondaires = $record['auteursSecondaires'] ?? [];
        if(!empty($auteursSecondaires)) {
            $ufield = '702';
            foreach( $auteursSecondaires as $auteurSecondaire) {
                $nom = $auteurSecondaire['nom'] ?? '';
                $prenom = $auteurSecondaire['prenom'] ?? '';
                if (empty($prenom) && strpos($nom, ',') !== false) {
                    $tmp = explode(',', $nom, 2);
                    $nom = trim($tmp[1]);
                    $prenom = trim($tmp[0]);
                }

                $qualificatifs_dates = $auteurSecondaire['qualificatifs'][0] ?? '';
                $typeContribution = $this->getFonctionFromTypeContribution($auteurSecondaire['typeContribution'] ?? '');

                if($nom) {
                    $unimarc_record[] = [
                        'ufield' => $ufield,
                        'usubfield' => 'a',
                        'value' => $nom,
                        'field_order' => $fo,
                        'subfield_order' => $so++,
                    ];

                    if($prenom) {
                        $unimarc_record[] = [
                            'ufield' => $ufield,
                            'usubfield' => 'b',
                            'value' => $prenom,
                            'field_order' => $fo,
                            'subfield_order' => $so++,
                        ];
                    }
                    if($qualificatifs_dates) {
                        $unimarc_record[] = [
                            'ufield' => $ufield,
                            'usubfield' => 'f',
                            'value' => $qualificatifs_dates,
                            'field_order' => $fo,
                            'subfield_order' => $so++,
                        ];
                    }
                    if($typeContribution) {
                        $unimarc_record[] = [
                            'ufield' => $ufield,
                            'usubfield' => '4',
                            'value' => $typeContribution,
                            'field_order' => $fo,
                            'subfield_order' => $so++,
                        ];
                    }
                    $fo++;
                    $so = 0;
                }
            }
        }

        $this->buffer['records'][$ref]['header'] = $unimarc_headers;
        $this->buffer['records'][$ref]['content'] = $unimarc_record;
    }


    /**
     * Recuperation du code fonction a partir du libelle
     *
     * @param string $typeContribution
     * @return string
     */
    protected function getFonctionFromTypeContribution(string $typeContribution = '')
    {
        $config = $this->getElectreConfig();
        if (!empty($config['functions'][$typeContribution])) {
            return $config['functions'][$typeContribution];
        }
        return $config['functions']['__DEFAULT__'] ?? '';
    }


    /**
     * Enregistrement des notices dans l'entrepot
     */
    protected function rec_records($source_id, $search_id)
    {
        if(empty($this->buffer['records'])) {
            return;
        }
        $this->buffer['source_id'] = $source_id;
        $this->buffer['search_id'] = $search_id;
        $date_import=date("Y-m-d H:i:s",time());
        $this->buffer['date_import'] = $date_import;

        foreach($this->buffer['records'] as $ref => $record) {
            $this->buffer['records'][$ref]['recid'] = $this->insert_into_external_count($this->buffer['source_id'], $ref);
        }

        $this->insert_records_into_entrepot($this->buffer);
    }

    /**
     * Retourne la config Electre
     *
     * @return array
     */
    protected function getElectreConfig() {
		if (isset($this->electreConfig)) {
			return $this->electreConfig;
		}

		$contents = [];
		$search_fields_file = __DIR__.'/electre.json';
		if (is_readable($search_fields_file)) {
            $contents = file_get_contents($search_fields_file);
            $contents = encoding_normalize::json_decode($contents, true);
		}

        $this->electreConfig = [];
		if ($contents) {
            $contentsSubst = [];
            $search_fields_file_subst = __DIR__.'/electre_subst.json';
            if (is_readable($search_fields_file_subst)) {
                $contentsSubst = file_get_contents($search_fields_file_subst);
                $contentsSubst = encoding_normalize::json_decode($contentsSubst, true);
            }

            if (!empty($contentsSubst['template'])) {
                foreach ($contentsSubst['template'] as $key => $value) {
                    $contents['template'][$key] = $value;
                }
            }

            if (!empty($contentsSubst['functions'])) {
                foreach ($contentsSubst['functions'] as $key => $value) {
                    $contents['functions'][$key] = $value;
                }
            }

            $this->electreConfig = $contents;
            unset($this->electreConfig['functions']['__COMMENT__']);
        }

		return $this->electreConfig;
	}

    /**
     * Calcule un template avec les variables donnees
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    private function makeTemplate(string $template, array $data)
    {
        $keys = array_keys($data);
        $keys = array_map(function ($key) { return '{' . $key . '}'; }, $keys);
        $result = str_replace($keys, $data, $template);
        return trim($result);
    }

}
