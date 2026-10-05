<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sphinx_authperso_indexer.class.php,v 1.13.2.1.2.1 2025/05/30 09:07:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;
require_once $class_path . '/sphinx/sphinx_indexer.class.php';

class sphinx_authperso_indexer extends sphinx_authorities_indexer
{

    public function __construct()
    {
        global $include_path;
        $this->type = AUT_TABLE_AUTHPERSO;
        $this->default_index = "authperso";
        parent::__construct();
        $this->setChampBaseFilepath($include_path . "/indexation/authorities/authperso/champs_base.xml");
    }


    protected function addSpecificsFilters($id, $filters = [])
    {
        $filters = parent::addSpecificsFilters($id, $filters);

        //Recuperation du statut
        $query = "select num_statut from authorities where id_authority = $id and type_object = $this->type";
        $result = pmb_mysql_query($query);
        $row = pmb_mysql_fetch_object($result);
        if (! array_key_exists('multi', $filters)) {
            $filters['multi'] = array();
        }
        if (is_object($row)) {
            $filters['multi']['status'] = $row->num_statut;
        }
        return $filters;
    }


    protected function parse_file()
    {
        if (!is_array($this->indexes) || !count($this->indexes)) {
            $params = _parser_text_no_function_(file_get_contents($this->getChampBaseFilepath()), 'INDEXATION');
            $this->indexes = array();
            $result = pmb_mysql_query('select id_authperso from authperso');
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_object($result)) {
                    $index_name = $this->default_index . '_' . $row->id_authperso;
                    for ($i = 0; $i < count($params['FIELD']); $i++) {
                        $field = 'f';
                        $fields = $attributes = array();
                        // On s'assure juste d'avoir un index
                        if (!isset($params["FIELD"][$i]['INDEX_NAME'])) {
                            $params["FIELD"][$i]['INDEX_NAME'] = $index_name;
                        }
                        // On initialise le tableau
                        if (!isset($this->indexes[$params["FIELD"][$i]['INDEX_NAME']])) {
                            $this->indexes[$params["FIELD"][$i]['INDEX_NAME']] = array(
                                'fields' => array(),
                                'attributes' => array('dummy')

                            );
                        }
                        // Pas d'infos viables, on ne perd de temps...
                        if (!isset($params["FIELD"][$i]['TABLE'])) {
                            continue;
                        }
                        // On récupère l'identifiant
                        if (isset($params["FIELD"][$i]['ID'])) {
                            $field .= '_' . str_replace('!!id_authperso!!', $row->id_authperso, $params["FIELD"][$i]['ID']);;
                        }
                        // Si pas de tablefield, on regarde si c'est pas des elements externes avant de sortir
                        if (!isset($params["FIELD"][$i]['TABLE'][0]['TABLEFIELD'])) {

                            switch ($params["FIELD"][$i]['DATATYPE']) {
                                case 'custom_field':
                                    //Traitement des champs perso !
                                    switch ($params["FIELD"][$i]['TABLE']) {
                                        case 'notices':
                                        default:
                                            $pperso = new parametres_perso($params["FIELD"][$i]['TABLE'][0]['value']);
                                            break;
                                    }
                                    // Pour chaque champ perso
                                    foreach ($pperso->t_fields as $pperso_id => $pperso_infos) {
                                        // Si le champ est declare recherchable
                                        if ($pperso_infos['SEARCH']) {
                                            $fields[] = $field . '_' . str_pad($pperso_id, 2, "0", STR_PAD_LEFT);
                                            // $attributes[] = $field.'_'.$pperso_id;
                                            $this->insert_index[$params["FIELD"][$i]['INDEX_NAME']][] = $field . '_' . str_pad($pperso_id, 2, "0", STR_PAD_LEFT);
                                            $this->fields_pond[$field . '_' . str_pad($pperso_id, 2, "0", STR_PAD_LEFT)] = $pperso_infos['POND'] * $this->multiple;
                                        }
                                    }
                                    break;
                                case 'authperso':
                                    //TODO Sortir l'ISDB de l'autorite perso comme attribut!
                                    $authpersos = authpersos::get_instance();
                                    foreach ($authpersos->info as $authperso_id => $authperso_info) {
                                        for ($j = 0; $j < count($authperso_info['fields']); $j++) {
                                            $field = 'f_' . ($params["FIELD"][$i]['ID'] + $authperso_id);
                                            if ($authperso_info['fields'][$j]['search']) {
                                                $fields[] = $field . '_' . str_pad($authperso_info['fields'][$j]['id'], 2, "0", STR_PAD_LEFT);
                                                // $attributes[] = $field.'_'.str_pad($authperso_info['fields'][$j]['id'], 2,"0",STR_PAD_LEFT);
                                                $this->insert_index[$params["FIELD"][$i]['INDEX_NAME']][] = $field . '_' . str_pad($authperso_info['fields'][$j]['id'], 2, "0", STR_PAD_LEFT);
                                                $this->fields_pond[$field . '_' . str_pad($authperso_info['fields'][$j]['id'], 2, "0", STR_PAD_LEFT)] = $authperso_info['fields'][$j]['pond'] *
                                                    $this->multiple;
                                            }
                                        }
                                    }
                                    break;
                                default:
                                    break; //useless
                            }
                        } else {
                            // Pour chaque table citee
                            for ($j = 0; $j < count($params["FIELD"][$i]['TABLE']); $j++) {
                                //Pour chaque colonne citee dans la table courante
                                for ($k = 0; $k < count($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD']); $k++) {
                                    // Pas d'id a ce niveau = code_ss_champ = 00
                                    if (!isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'])) {
                                        $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'] = "00";
                                    }
                                    // Ponderation nulle, c'est un champ de facette pur... pas de recherche
                                    if (!isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND']) || isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND']) * 1 > 0) {
                                        $fields[] = $field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
                                    }
                                    //TODO Lire un parametre qui nous dit si on veut ou non du champ en attribut
                                    // $attributes[] = $field.'_'.$params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
                                    $this->insert_index[$params["FIELD"][$i]['INDEX_NAME']][] = $field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID'];
                                    if (isset($params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND'])) {
                                        $this->fields_pond[$field . '_' . $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['ID']] = $params["FIELD"][$i]['TABLE'][$j]['TABLEFIELD'][$k]['POND'] *
                                            $this->multiple;
                                    }
                                }
                            }
                            if (!empty($params["FIELD"][$i]['ISBD'])) {
                                $attributes[] = $field . '_' . $params["FIELD"][$i]['ISBD'][0]['ID'];
                                $this->insert_index[$params["FIELD"][$i]['INDEX_NAME']][] = $field . '_' . $params["FIELD"][$i]['ISBD'][0]['ID'];
                            }
                        }
                        $this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['fields'] = array_unique(array_merge($this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['fields'], $fields));
                        $this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['attributes'] = array_unique(array_merge($this->indexes[$params["FIELD"][$i]['INDEX_NAME']]['attributes'], $attributes));
                        // On unset le nom de l'index pour le parcours des autres autoritEs persos
                        unset($params["FIELD"][$i]['INDEX_NAME']);
                    }
                    // On met tout dans le tableau string pour garder le fonctionnement initial
                    $this->indexes[$index_name]['attributes']['string'] = $this->indexes[$index_name]['attributes'];
                    foreach ($this->indexes[$index_name]['attributes'] as $key => $attribute) {
                        if (is_numeric($key)) {
                            unset($this->indexes[$index_name]['attributes'][$key]);
                        }
                    }
                    foreach ($this->filters as $type => $filter) {
                        $nb_filters = count($filter);
                        for ($i = 0; $i < $nb_filters; $i++) {
                            $this->indexes[$index_name]['attributes'][$type][] = $this->filters[$type][$i];
                        }
                    }
                }
            }
        }
    }


    public function get_pperso_field($authperso_id = 0)
    {
        $this->p_perso_field = "f_100" . $authperso_id . "100";
        return $this->p_perso_field;
    }


    public function deleteIndex($object_id = 0)
    {
        global $sphinx_indexes_prefix, $id_authperso;

        $object_id = (int) $object_id;
        $langs = $this->getAvailableLanguages();
        $table = $sphinx_indexes_prefix . $this->default_index . '_' . $id_authperso;
        $nb_langs = count($langs);
        for ($i = 0; $i < $nb_langs; $i++) {
            foreach ($this->indexes as $index_name => $infos) {
                if ($table == $sphinx_indexes_prefix . $index_name) {
                    pmb_mysql_query('delete from ' . $sphinx_indexes_prefix . $index_name . ($langs[$i] != '' ? '_' . $langs[$i] : '') . ' where id = ' . $object_id, $this->getDBHandler());
                }
            }
        }
    }


    /**
     * Remplissage d'un ensemble index
     *
     * @param [int] $object_ids : id objets a indexer. Si vide, remplissage de l'ensemble des index
     * @param boolean $showProgression : affichage progression en console
     *
     */
    public function fillIndexes($object_ids = [], $showProgression = false)
    {
        global $sphinx_indexes_prefix;

        array_walk($object_ids, function (&$a) {
            $a = intval($a);
        });
        $showProgression = boolval($showProgression);

        $this->parse_file();
        $dbh = $this->getDBHandler();
        $langs = $this->getAvailableLanguages();
        $imploded_langs = implode('","', $langs);
        $separator = $this->getSeparator();

        pmb_mysql_query('set session group_concat_max_len = 16777216');

        // Suppression index sphinx
        $this->deleteIndexes($object_ids);

        // Remplissage des indexs...
        $rqt = "SELECT $this->object_key FROM $this->object_table WHERE type_object = $this->type";
        if (!empty($object_ids)) {
            $rqt .= " AND $this->object_key in (" . implode(',', $object_ids) . ")";
        }
        $res = pmb_mysql_query($rqt);
        if ($res) {

            if ($showProgression) {
                print ProgressBar::start(pmb_mysql_num_rows($res), "Index " . $this->default_index);
            }

            while ($row = pmb_mysql_fetch_assoc($res)) {

                $id = $row[$this->object_key];
                $authperso_id = $this->get_authperso_id_from_authority($id);

                // Construction de l'index
                $inserts = [];

                $rqt = 'SELECT code_champ, code_ss_champ, lang, group_concat(value SEPARATOR "' . $separator . '") AS value ' .
                    ' FROM ' . $this->object_index_table .
                    ' WHERE ' . $this->object_id . ' = ' . $id . ' AND lang in ("' . $imploded_langs . '") GROUP BY code_champ, code_ss_champ, lang';
                $res_notice = pmb_mysql_query($rqt);

                while ($champ = pmb_mysql_fetch_assoc($res_notice)) {
                    if (in_array($champ['lang'], $langs)) {
                        $code_champ = str_pad($champ['code_champ'], 3, "0", STR_PAD_LEFT);
                        $code_ss_champ = str_pad($champ['code_ss_champ'], 2, "0", STR_PAD_LEFT);
                        $field = 'f_' . $code_champ . '_' . $code_ss_champ;

                        if (in_array($field, $this->insert_index["authperso_$authperso_id"])) {
                            $inserts["authperso_$authperso_id" . ($champ['lang'] ? '_' . $champ['lang'] : '')][$field] = addslashes(encoding_normalize::utf8_normalize($champ['value']));
                        }
                    }
                }
                $inserts = $this->getSpecificsFiltersValues($id, $inserts);

                foreach ($inserts as $insert_table => $fields) {
                    $keys = $values =  "";
                    foreach ($fields as $key => $value) {
                        if (!empty($keys)) {
                            $keys .= ",";
                            $values .= ",";
                        }
                        $keys .= $key;
                        if (substr($key, 0, 2) !== "f_") {
                            $values .= $value;
                        } else {
                            $values .= "'$value'";
                        };
                    }
                    $table = $sphinx_indexes_prefix . $insert_table;
                    $query = "INSERT INTO $table (id, $keys) values ($id, $values)";
                    if (!pmb_mysql_query($query, $dbh)) {
                        print "$table : " . pmb_mysql_error($dbh) . "($query)\n";
                    }
                }
                if ($showProgression) {
                    print ProgressBar::next();
                }
            }
            if ($showProgression) {
                print ProgressBar::finish();
            }
        }
    }


    private function get_authperso_id_from_authority($id_authority)
    {
        $req = "SELECT authperso_authority_authperso_num FROM authperso_authorities, authperso WHERE id_authperso = authperso_authority_authperso_num AND id_authperso_authority IN (SELECT num_object FROM authorities WHERE id_authority = $id_authority)";
        $res = pmb_mysql_query($req);
        if ($row = pmb_mysql_fetch_object($res)) {
            return $row->authperso_authority_authperso_num;
        }
        return 0;
    }
}
