<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_ontology.class.php,v 1.11.4.4 2025/03/04 15:50:01 dbellamy Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

// Classe de gestion des recherches avancees
class search_ontology extends search
{

    /**
     *
     * @var onto_ontology
     */
    protected $ontology;

    protected $class_infos = [];

    protected $property_infos = [];

    /**
     *
     * @var ontology
     */
    protected $onto;


    public function __construct($rec_history = false, $fichier_xml = '', $full_path = '', $ontology = null)
    {
        $this->rec_history = $rec_history;
        $this->full_path = $full_path;
        $this->fichier_xml = $fichier_xml;
        $this->ontology = $ontology;
        $this->parse_search_file();
        $this->strip_slashes();
    }

    // Parse du fichier de configuration
    protected function parse_search_file()
    {
        global $include_path, $base_path, $charset;
        global $msg, $KEY_CACHE_FILE_XML;
        global $pmb_opac_url, $lang;

        $filepath = "";
        if (! $this->full_path) {
            if ($this->fichier_xml == '') {
                $this->fichier_xml = 'search_fields';
            }
            if (! static::$ignore_subst_file && file_exists($include_path . "/search_queries/" . $this->fichier_xml . "_subst.xml")) {
                $filepath = $include_path . "/search_queries/" . $this->fichier_xml . "_subst.xml";
            } else {
                $filepath = $include_path . "/search_queries/" . $this->fichier_xml . ".xml";
            }
        } else {
            if (! static::$ignore_subst_file && file_exists($this->full_path . $this->fichier_xml . "_subst.xml")) {
                $filepath = $this->full_path . $this->fichier_xml . "_subst.xml";
            } else {
                $filepath = $this->full_path . $this->fichier_xml . ".xml";
            }
        }
        $fileInfo = pathinfo($filepath);
        $fileName = preg_replace("/[^a-z0-9]/i", "", $fileInfo['dirname'] . $fileInfo['filename'] . $lang . $charset);
        $tempFile = $base_path . "/temp/XML" . $fileName . ".tmp";
        $dejaParse = false;

        // $cache_php=cache_factory::getCache();
        $key_file = "";
        if ($cache_php) {
            $key_file = getcwd() . $fileName . filemtime($filepath);
            $key_file = $KEY_CACHE_FILE_XML . md5($key_file);
            if ($tmp_key = $cache_php->getFromCache($key_file)) {
                if ($cache = $cache_php->getFromCache($tmp_key)) {
                    if (is_array($cache) && (count($cache) == 15)) {
                        $this->groups_used = $cache[0];
                        $this->groups = $cache[1];
                        $this->memory_engine_allowed = $cache[2];
                        $this->operators = $cache[3];
                        $this->op_empty = $cache[4];
                        $this->fixedfields = $cache[5];
                        $this->dynamics_not_visible = $cache[6];
                        $this->dynamicfields_order = $cache[7];
                        $this->dynamicfields_hidebycustomname = $cache[8];
                        $this->dynamicfields = $cache[9];
                        $this->specials_not_visible = $cache[10];
                        $this->tableau_speciaux = $cache[11];
                        $this->keyName = $cache[12];
                        $this->tableName = $cache[13];
                        $this->specialfields = $cache[14];
                        $this->op_special = $cache[15];
                        $dejaParse = true;
                    }
                }
            }
        } else {
            if (file_exists($tempFile)) {
                // Le fichier XML original a-t-il été modifié ultérieurement ?
                if (filemtime($filepath) > filemtime($tempFile)) {
                    // on va re-générer le pseudo-cache
                    unlink($tempFile);
                } else {
                    $dejaParse = true;
                }
            }
            if ($dejaParse) {
                $tmp = fopen($tempFile, "r");
                $cache = unserialize(fread($tmp, filesize($tempFile)));
                fclose($tmp);
                if (is_array($cache) && (count($cache) == 15)) {
                    $this->groups_used = $cache[0];
                    $this->groups = $cache[1];
                    $this->memory_engine_allowed = $cache[2];
                    $this->operators = $cache[3];
                    $this->op_empty = $cache[4];
                    $this->fixedfields = $cache[5];
                    $this->dynamics_not_visible = $cache[6];
                    $this->dynamicfields_order = $cache[7];
                    $this->dynamicfields_hidebycustomname = $cache[8];
                    $this->dynamicfields = $cache[9];
                    $this->specials_not_visible = $cache[10];
                    $this->tableau_speciaux = $cache[11];
                    $this->keyName = $cache[12];
                    $this->tableName = $cache[13];
                    $this->specialfields = $cache[14];
                    $this->op_special = $cache[15];
                } else {
                    // SOUCIS de cache...
                    unlink($tempFile);
                    $dejaParse = false;
                }
            }
        }
        if (! $dejaParse) {
            if ($this->fichier_xml == 'search_fields_opac' || strpos($this->full_path, '/opac_css/') !== false) {
                $save_msg = $msg;
                $url = $pmb_opac_url . "includes/messages/$lang.xml";
                $fichier_xml = $base_path . "/temp/opac_lang.xml";
                curl_load_opac_file($url, $fichier_xml);
                $messages = new XMLlist("$base_path/temp/opac_lang.xml", 0);
                $messages->analyser();
                $msg = $messages->table;
            }
            $fp = fopen($filepath, "r") or die("Can't find XML file");
            $size = filesize($filepath);
            $xml = fread($fp, $size);
            fclose($fp);
            $param = _parser_text_no_function_($xml, "PMBFIELDS");

            if (isset($param['GROUPS'])) {
                $this->groups_used = true;
                $this->groups = array();
                foreach ($param['GROUPS'][0]['GROUP'] as $group) {
                    $this->groups[$group['ID']] = array(
                        'label' => (substr($group['LABEL'][0]['value'], 0, 4) == "msg:" ? $msg[substr($group['LABEL'][0]['value'], 4, strlen($group['LABEL'][0]['value']) - 4)] : $group['LABEL'][0]['value']),
                        'order' => $group['ORDER'][0]['value'],
                        'objects_type' => (isset($group['OBJECTS_TYPE'][0]['value']) ? $group['OBJECTS_TYPE'][0]['value'] : '')
                    );
                }
                foreach ($this->ontology->get_classes() as $c) {
                    /**
                     *
                     * @var onto_common_class $class
                     */
                    $class = $this->ontology->get_class($c->uri);
                    $this->groups[$class->field] = array(
                        'label' => $class->label,
                        'order' => $class->field,
                        'objects_type' => $class->pmb_name
                    );
                }
                uasort($this->groups, array(
                    $this,
                    'sort_groups'
                ));
            }

            // Lecture parametre memory_engine_allowed
            if (isset($param['MEMORYENGINEALLOWED'][0]['value']) && $param['MEMORYENGINEALLOWED'][0]['value'] == 'yes') {
                $this->memory_engine_allowed = true;
            }

            // Lecture des operateurs
            for ($i = 0; $i < count($param["OPERATORS"][0]["OPERATOR"]); $i ++) {
                $operator_ = $param["OPERATORS"][0]["OPERATOR"][$i];
                if (substr($operator_["value"], 0, 4) == "msg:") {
                    $this->operators[$operator_["NAME"]] = $msg[substr($operator_["value"], 4, strlen($operator_["value"]) - 4)];
                } else {
                    $this->operators[$operator_["NAME"]] = $operator_["value"];
                }
                if (isset($operator_["EMPTYALLOWED"]) && ($operator_["EMPTYALLOWED"] == "yes")) {
                    $this->op_empty[$operator_["NAME"]] = true;
                } else {
                    $this->op_empty[$operator_["NAME"]] = false;
                }
                if (isset($operator_["SPECIAL"]) && ($operator_["SPECIAL"] == "yes")) {
                    $this->op_special[$operator_["NAME"]] = true;
                } else {
                    $this->op_special[$operator_["NAME"]] = false;
                }
            }

            // Lecture des champs fixes
            if (! isset($param["FIXEDFIELDS"][0]["FIELD"])) {
                $param["FIXEDFIELDS"][0]["FIELD"] = array();
            }
            for ($i = 0; $i < count($param["FIXEDFIELDS"][0]["FIELD"]); $i ++) {
                $t = array();
                $ff = $param["FIXEDFIELDS"][0]["FIELD"][$i];

                if (substr($ff["TITLE"], 0, 4) == "msg:" && isset($msg[substr($ff["TITLE"], 4, strlen($ff["TITLE"]) - 4)])) {
                    $t["TITLE"] = $msg[substr($ff["TITLE"], 4, strlen($ff["TITLE"]) - 4)];
                } else {
                    $t["TITLE"] = $ff["TITLE"];
                }
                $t["ID"] = $ff["ID"];
                $t["NOTDISPLAYCOL"] = (isset($ff["NOTDISPLAYCOL"]) ? $ff["NOTDISPLAYCOL"] : '');
                $t["UNIMARCFIELD"] = (isset($ff["UNIMARCFIELD"]) ? $ff["UNIMARCFIELD"] : '');
                $t["INPUT_TYPE"] = (isset($ff["INPUT"][0]["TYPE"]) ? $ff["INPUT"][0]["TYPE"] : '');
                $t["INPUT_FILTERING"] = (isset($ff["INPUT"][0]["FILTERING"]) ? $ff["INPUT"][0]["FILTERING"] : '');
                $t["INPUT_OPTIONS"] = (isset($ff["INPUT"][0]) ? $ff["INPUT"][0] : '');
                if ($this->groups_used) {
                    $t["GROUP"] = (isset($ff["GROUP"]) ? $ff["GROUP"] : '');
                }
                $t["SEPARATOR"] = '';
                if (isset($ff["SEPARATOR"])) {
                    if (substr($ff["SEPARATOR"], 0, 4) == "msg:") {
                        $t["SEPARATOR"] = $msg[substr($ff["SEPARATOR"], 4, strlen($ff["SEPARATOR"]) - 4)];
                    } else {
                        $t["SEPARATOR"] = $ff["SEPARATOR"];
                    }
                }
				if(isset($ff["DELNOTALLOWED"]) && $ff["DELNOTALLOWED"]=="yes") {
					$t["DELNOTALLOWED"]=true;
				} else {
					$t["DELNOTALLOWED"]=false;
				}
                // Visibilite
                if (isset($ff["VISIBLE"]) && $ff["VISIBLE"] == "no")
                    $t["VISIBLE"] = false;
                else
                    $t["VISIBLE"] = true;

                // Moteur memory
                if (isset($ff['MEMORYENGINEFORBIDDEN']) && $ff['MEMORYENGINEFORBIDDEN'] == 'yes')
                    $t['MEMORYENGINEFORBIDDEN'] = true;
                else
                    $t['MEMORYENGINEFORBIDDEN'] = false;

                // Variables
                $t["VAR"] = array();
                if (isset($ff["VARIABLE"])) {
                    for ($j = 0; $j < count($ff["VARIABLE"]); $j ++) {
                        $v = array();
                        $vv = $ff["VARIABLE"][$j];
                        $v["NAME"] = $vv["NAME"];
                        $v["TYPE"] = $vv["TYPE"];
                        $v["COMMENT"] = '';
                        if (isset($vv["COMMENT"])) {
                            if (substr($vv["COMMENT"], 0, 4) == "msg:" && isset($msg[substr($vv["COMMENT"], 4, strlen($vv["COMMENT"]) - 4)])) {
                                $v["COMMENT"] = $msg[substr($vv["COMMENT"], 4, strlen($vv["COMMENT"]) - 4)];
                            } else {
                                $v["COMMENT"] = $vv["COMMENT"];
                            }
                        }
						$v["SPAN"]=(isset($vv["SPAN"]) ? $vv["SPAN"] : '');
                        // Recherche des options
                        reset($vv);
                        foreach ($vv as $key => $val) {
                            if (is_array($val)) {
                                $v["OPTIONS"][$key] = $val;
                            }
                        }
                        $v["PLACE"] = (isset($vv["PLACE"]) ? $vv["PLACE"] : '');
                        $v["CLASS"] = (isset($vv["CLASS"]) ? $vv["CLASS"] : '');
                        $t["VAR"][] = $v;
                    }
                }

                if (! isset($ff["VISIBILITY"]))
                    $t["VISIBILITY"] = true;
                else if ($ff["VISIBILITY"] == "yes")
                    $t["VISIBILITY"] = true;
                else
                    $t["VISIBILITY"] = false;

                for ($j = 0; $j < count($ff["QUERY"]); $j ++) {
                    $q = array();
                    $q["OPERATOR"] = $ff["QUERY"][$j]["FOR"];
					if(!isset($ff["QUERY"][$j]["MULTIPLE"])) $ff["QUERY"][$j]["MULTIPLE"] = '';
					if(!isset($ff["QUERY"][$j]["CONDITIONAL"])) $ff["QUERY"][$j]["CONDITIONAL"] = '';
                    if (($ff["QUERY"][$j]["MULTIPLE"] == "yes") || ($ff["QUERY"][$j]["CONDITIONAL"] == "yes")) {
						if($ff["QUERY"][$j]["MULTIPLE"]=="yes") $element = "PART";
						else $element = "VAR";

                        for ($k = 0; $k < count($ff["QUERY"][$j][$element]); $k ++) {
                            $pquery = $ff["QUERY"][$j][$element][$k];
                            if ($element == "VAR") {
                                $q[$k]["CONDITIONAL"]["name"] = $pquery["NAME"];
                                $q[$k]["CONDITIONAL"]["value"] = $pquery["VALUE"][0]["value"];
                            }
                            if (isset($pquery["MULTIPLEWORDS"]) && $pquery["MULTIPLEWORDS"] == "yes")
                                $q[$k]["MULTIPLE_WORDS"] = true;
                            else
                                $q[$k]["MULTIPLE_WORDS"] = false;
                            if (isset($pquery["REGDIACRIT"]) && $pquery["REGDIACRIT"] == "yes")
                                $q[$k]["REGDIACRIT"] = true;
                            else
                                $q[$k]["REGDIACRIT"] = false;
                            if (isset($pquery["KEEP_EMPTYWORD"]) && $pquery["KEEP_EMPTYWORD"] == "yes")
                                $q[$k]["KEEP_EMPTYWORD"] = true;
                            else
                                $q[$k]["KEEP_EMPTYWORD"] = false;
                            if (isset($pquery["REPEAT"])) {
                                $q[$k]["REPEAT"]["NAME"] = $pquery["REPEAT"][0]["NAME"];
                                $q[$k]["REPEAT"]["ON"] = $pquery["REPEAT"][0]["ON"];
                                $q[$k]["REPEAT"]["SEPARATOR"] = $pquery["REPEAT"][0]["SEPARATOR"];
                                $q[$k]["REPEAT"]["OPERATOR"] = $pquery["REPEAT"][0]["OPERATOR"];
                                $q[$k]["REPEAT"]["ORDERTERM"] = (isset($pquery["REPEAT"][0]["ORDERTERM"]) ? $pquery["REPEAT"][0]["ORDERTERM"] : '');
                            }
                            if (isset($pquery["BOOLEANSEARCH"]) && $pquery["BOOLEANSEARCH"] == "yes") {
                                $q[$k]["BOOLEAN"] = true;
                                if ($pquery["BOOLEAN"]) {
                                    for ($z = 0; $z < count($pquery["BOOLEAN"]); $z ++) {
                                        $q[$k]["TABLE"][$z] = $pquery["BOOLEAN"][$z]["TABLE"][0]["value"];
                                        $q[$k]["INDEX_L"][$z] = $pquery["BOOLEAN"][$z]["INDEX_L"][0]["value"];
                                        $q[$k]["INDEX_I"][$z] = $pquery["BOOLEAN"][$z]["INDEX_I"][0]["value"];
                                        $q[$k]["ID_FIELD"][$z] = $pquery["BOOLEAN"][$z]["ID_FIELD"][0]["value"];
                                        if (isset($pquery["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]) && $pquery["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"] == "yes") {
                                            $q[$k]["KEEP_EMPTY_WORDS"][$z] = 1;
                                            $q[$k]["KEEP_EMPTY_WORDS_FOR_CHECK"] = 1;
                                        }
                                        if (isset($pquery["BOOLEAN"][$z]["FULLTEXT"][0]["value"]) && $pquery["BOOLEAN"][$z]["FULLTEXT"][0]["value"] == "yes") {
                                            $q[$k]["FULLTEXT"][$z] = 1;
                                        }
                                    }
                                } else {
                                    $q[$k]["TABLE"] = $pquery["TABLE"][0]["value"];
                                    $q[$k]["INDEX_L"] = $pquery["INDEX_L"][0]["value"];
                                    $q[$k]["INDEX_I"] = $pquery["INDEX_I"][0]["value"];
                                    $q[$k]["ID_FIELD"] = $pquery["ID_FIELD"][0]["value"];
                                    if ($pquery["KEEP_EMPTY_WORDS"][0]["value"] == "yes") {
                                        $q[$k]["KEEP_EMPTY_WORDS"] = 1;
                                        $q[$k]["KEEP_EMPTY_WORDS_FOR_CHECK"] = 1;
                                    }
                                    if (isset($pquery["FULLTEXT"][0]["value"]) && $pquery["FULLTEXT"][0]["value"] == "yes") {
                                        $q[$k]["FULLTEXT"] = 1;
                                    }
                                }
                            } else
                                $q[$k]["BOOLEAN"] = false;
                            if (isset($ff["QUERY"][$j]["MODE"])) {
                                $q[$k]["MODE"] = $ff["QUERY"][$j]["MODE"];
                            }else {
                                $q[$k]["MODE"] = "SQL";
                            }
                            if (isset($pquery["ISBNSEARCH"]) && $pquery["ISBNSEARCH"] == "yes") {
                                $q[$k]["ISBN"] = true;
                            } else
                                $q[$k]["ISBN"] = false;
                            if (isset($pquery["DETECTDATE"])) {
                                $q[$k]["DETECTDATE"] = $pquery["DETECTDATE"];
                            } else
                                $q[$k]["DETECTDATE"] = false;
                            $q[$k]["MAIN"] = (isset($pquery["MAIN"][0]["value"]) ? $pquery["MAIN"][0]["value"] : '');
                            $q[$k]["MULTIPLE_TERM"] = (isset($pquery["MULTIPLETERM"][0]["value"]) ? $pquery["MULTIPLETERM"][0]["value"] : '');
                            $q[$k]["MULTIPLE_OPERATOR"] = (isset($pquery["MULTIPLEOPERATOR"][0]["value"]) ? $pquery["MULTIPLEOPERATOR"][0]["value"] : '');
                        }
                        $t["QUERIES"][] = $q;
                        $t["QUERIES_INDEX"][$q["OPERATOR"]] = count($t["QUERIES"]) - 1;
                    } else {
                        if (isset($ff["QUERY"][$j]["CUSTOM_SEARCH"]) && $ff["QUERY"][$j]["CUSTOM_SEARCH"] == "yes")
                            $q[0]["CUSTOM_SEARCH"] = true;
                        else
                            $q[0]["CUSTOM_SEARCH"] = false;
                        if (isset($ff["QUERY"][$j]["MULTIPLEWORDS"]) && $ff["QUERY"][$j]["MULTIPLEWORDS"] == "yes")
                            $q[0]["MULTIPLE_WORDS"] = true;
                        else
                            $q[0]["MULTIPLE_WORDS"] = false;
                        if (isset($ff["QUERY"][$j]["REGDIACRIT"]) && $ff["QUERY"][$j]["REGDIACRIT"] == "yes")
                            $q[0]["REGDIACRIT"] = true;
                        else
                            $q[0]["REGDIACRIT"] = false;
                        if (isset($ff["QUERY"][$j]["KEEP_EMPTYWORD"]) && $ff["QUERY"][$j]["KEEP_EMPTYWORD"] == "yes")
                            $q[0]["KEEP_EMPTYWORD"] = true;
                        else
                            $q[0]["KEEP_EMPTYWORD"] = false;
                        if (isset($ff["QUERY"][$j]["REPEAT"])) {
                            $q[0]["REPEAT"]["NAME"] = $ff["QUERY"][$j]["REPEAT"][0]["NAME"];
                            $q[0]["REPEAT"]["ON"] = $ff["QUERY"][$j]["REPEAT"][0]["ON"];
                            $q[0]["REPEAT"]["SEPARATOR"] = $ff["QUERY"][$j]["REPEAT"][0]["SEPARATOR"];
                            $q[0]["REPEAT"]["OPERATOR"] = $ff["QUERY"][$j]["REPEAT"][0]["OPERATOR"];
                            $q[0]["REPEAT"]["ORDERTERM"] = (isset($ff["QUERY"][$j]["REPEAT"][0]["ORDERTERM"]) ? $ff["QUERY"][$j]["REPEAT"][0]["ORDERTERM"] : '');
                        }
                        if (isset($ff["QUERY"][$j]["MODE"])) {
                            $q[0]["MODE"] = $ff["QUERY"][$j]["MODE"];
                        }else {
                            $q[0]["MODE"] = "SQL";
                        }
                        if (isset($ff["QUERY"][$j]["BOOLEANSEARCH"]) && $ff["QUERY"][$j]["BOOLEANSEARCH"] == "yes") {
                            $q[0]["BOOLEAN"] = true;
                            if (isset($ff["QUERY"][$j]["BOOLEAN"])) {
                                for ($z = 0; $z < count($ff["QUERY"][$j]["BOOLEAN"]); $z ++) {
                                    $q[0]["TABLE"][$z] = $ff["QUERY"][$j]["BOOLEAN"][$z]["TABLE"][0]["value"];
                                    $q[0]["INDEX_L"][$z] = $ff["QUERY"][$j]["BOOLEAN"][$z]["INDEX_L"][0]["value"];
                                    $q[0]["INDEX_I"][$z] = $ff["QUERY"][$j]["BOOLEAN"][$z]["INDEX_I"][0]["value"];
                                    $q[0]["ID_FIELD"][$z] = $ff["QUERY"][$j]["BOOLEAN"][$z]["ID_FIELD"][0]["value"];
                                    if (isset($ff["QUERY"][$j]["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"]) && $ff["QUERY"][$j]["BOOLEAN"][$z]["KEEP_EMPTY_WORDS"][0]["value"] == "yes") {
                                        $q[0]["KEEP_EMPTY_WORDS"][$z] = 1;
                                        $q[0]["KEEP_EMPTY_WORDS_FOR_CHECK"] = 1;
                                    }
                                }
                            } else {
                                $q[0]["TABLE"] = $ff["QUERY"][$j]["TABLE"][0]["value"];
                                $q[0]["INDEX_L"] = $ff["QUERY"][$j]["INDEX_L"][0]["value"];
                                $q[0]["INDEX_I"] = $ff["QUERY"][$j]["INDEX_I"][0]["value"];
                                $q[0]["ID_FIELD"] = $ff["QUERY"][$j]["ID_FIELD"][0]["value"];
                                if (isset($ff["QUERY"][$j]["KEEP_EMPTY_WORDS"][0]["value"]) && $ff["QUERY"][$j]["KEEP_EMPTY_WORDS"][0]["value"] == "yes") {
                                    $q[0]["KEEP_EMPTY_WORDS"] = 1;
                                    $q[0]["KEEP_EMPTY_WORDS_FOR_CHECK"] = 1;
                                }
                            }
                        } else
                            $q[0]["BOOLEAN"] = false;
                        // prise en compte ou non du paramétrage du stemming
                        if (isset($ff["QUERY"][$j]['STEMMING']) && $ff["QUERY"][$j]['STEMMING'] == "no") {
                            $q[0]["STEMMING"] = false;
                        } else {
                            $q[0]["STEMMING"] = true;
                        }
                        // modif arnaud pour notices_mots_global_index..
                        if (isset($ff["QUERY"][$j]['WORDSEARCH']) && $ff["QUERY"][$j]['WORDSEARCH'] == "yes") {
                            $q[0]["WORD"] = true;
                            if (isset($ff["QUERY"][$j]['CLASS'][0]['NAME'])) {
                                $q[0]['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['NAME'];
                                if (count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
                                    $q[0]['FIELDSRESTRICT'] = array();
                                    foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
                                        $subfieldsrestrict = array();
                                        if (isset($fieldrestrict['SUB'])) {
                                            foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
                                                $subfieldsrestrict[] = array(
                                                    'sub_field' => $subfieldrestrict['SUB_FIELD'][0]['value'],
                                                    'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
                                                    'op' => $subfieldrestrict['OP'][0]['value'],
                                                    'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
                                                );
                                            }
                                        }
                                        $q[0]['FIELDSRESTRICT'][] = array(
                                            'field' => $fieldrestrict['FIELD'][0]['value'],
                                            'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
                                            'op' => $fieldrestrict['OP'][0]['value'],
                                            'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
                                            'sub' => $subfieldsrestrict
                                        );
                                    }
                                }
                            } else if (isset($ff["QUERY"][$j]['CLASS'][0]['TYPE'])) {
                                $q[0]['TYPE'] = $ff["QUERY"][$j]['CLASS'][0]['TYPE'];
                                if (isset($ff["QUERY"][$j]['CLASS'][0]['MODE'])) {
                                    $q[0]['MODE'] = $ff["QUERY"][$j]['CLASS'][0]['MODE'];
                                }
                                if (isset($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT']) && count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
                                    $q[0]['FIELDSRESTRICT'] = array();
                                    foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
                                        $subfieldsrestrict = array();
                                        if (isset($fieldrestrict['SUB'])) {
                                            foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
                                                $subfieldsrestrict[] = array(
                                                    'sub_field' => (isset($subfieldrestrict['SUB_FIELD'][0]['value'])) ? $subfieldrestrict['SUB_FIELD'][0]['value'] : '',
                                                    'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
                                                    'op' => $subfieldrestrict['OP'][0]['value'],
                                                    'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
                                                );
                                            }
                                        }
                                        $q[0]['FIELDSRESTRICT'][] = array(
                                            'field' => $fieldrestrict['FIELD'][0]['value'],
                                            'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
                                            'op' => $fieldrestrict['OP'][0]['value'],
                                            'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
                                            'sub' => $subfieldsrestrict
                                        );
                                    }
                                }
                            } else {
                                $q[0]['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['value'];
                            }
                            $q[0]['FOLDER'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FOLDER']) ? $ff["QUERY"][$j]['CLASS'][0]['FOLDER'] : '');
                            $q[0]['FIELDS'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FIELDS']) ? $ff["QUERY"][$j]['CLASS'][0]['FIELDS'] : '');
                        } else
                            $q[0]["WORD"] = false;
                        // fin modif arnaud
                        if (isset($ff["QUERY"][$j]["ISBNSEARCH"]) && $ff["QUERY"][$j]["ISBNSEARCH"] == "yes") {
                            $q[0]["ISBN"] = true;
                        } else
                            $q[0]["ISBN"] = false;
                        if (isset($ff["QUERY"][$j]["DETECTDATE"])) {
                            $q[0]["DETECTDATE"] = $ff["QUERY"][$j]["DETECTDATE"];
                        } else
                            $q[0]["DETECTDATE"] = false;
                        $q[0]["MAIN"] = (isset($ff["QUERY"][$j]["MAIN"][0]["value"]) ? $ff["QUERY"][$j]["MAIN"][0]["value"] : '');

                        if (isset($ff["QUERY"][$j]['SPECIAL'])) {
                            $q[0]["SPECIAL"] = array();
                            $q[0]["SPECIAL"]["CLASS"] = (isset($ff["QUERY"][$j]['SPECIAL'][0]["CLASS"]) ? $ff["QUERY"][$j]['SPECIAL'][0]["CLASS"] : '');
                            $q[0]["SPECIAL"]['PARAMS'] = array();

                            // Variables
                            if (isset($ff["QUERY"][$j]['SPECIAL'][0]['VARIABLE'])) {
                                $length = count($ff["QUERY"][$j]['SPECIAL'][0]['VARIABLE']);
                                $params = array();

                                for ($x = 0; $x < $length; $x ++) {
                                    $variable = $ff["QUERY"][$j]['SPECIAL'][0]['VARIABLE'][$x];
                                    $v = array();
                                    $v["NAME"] = $variable['NAME'];
                                    $v["TYPE"] = $variable['TYPE'];

                                    $v["COMMENT"] = '';
                                    if (isset($variable['COMMENT'])) {
                                        if (substr($variable['COMMENT'], 0, 4) == "msg:" && isset($msg[substr($variable['COMMENT'], 4)])) {
                                            $v["COMMENT"] = $msg[substr($variable['COMMENT'], 4)];
                                        } else {
                                            $v["COMMENT"] = $variable['COMMENT'];
                                        }
                                    }

                                    $v["DEFAULT"] = array();
                                    if (isset($variable['DEFAULT'])) {
                                        $v["DEFAULT"] = $variable['DEFAULT'][0];
                                    }
                                    $params[] = $v;
                                }

                                $q[0]["SPECIAL"]['PARAMS'] = $params;
                            }
                        }

                        $q[0]["MULTIPLE_TERM"] = (isset($ff["QUERY"][$j]["MULTIPLETERM"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLETERM"][0]["value"] : '');
                        $q[0]["MULTIPLE_OPERATOR"] = (isset($ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"] : '');
                        $t["QUERIES"][] = $q;
                        $t["QUERIES_INDEX"][$q["OPERATOR"]] = count($t["QUERIES"]) - 1;
                    }
                }

                // recuperation des visibilites parametrees
                $t["VARVIS"] = array();
                if (isset($ff["VAR"])) {
                    for ($j = 0; $j < count($ff["VAR"]); $j ++) {
                        $q = array();
                        $q["NAME"] = $ff["VAR"][$j]["NAME"];
                        if ($ff["VAR"][$j]["VISIBILITY"] == "yes")
                            $q["VISIBILITY"] = true;
                        else
                            $q["VISIBILITY"] = false;
                        for ($k = 0; $k < count($ff["VAR"][$j]["VALUE"]); $k ++) {
                            $v = array();
                            if ($ff["VAR"][$j]["VALUE"][$k]["VISIBILITY"] == "yes")
                                $v[$ff["VAR"][$j]["VALUE"][$k]["value"]] = true;
                            else
                                $v[$ff["VAR"][$j]["VALUE"][$k]["value"]] = false;
                        } // fin for <value ...
                        $q["VALUE"] = $v;
                        $t["VARVIS"][] = $q;
                    } // fin for
                }
                foreach ($this->ontology->get_classes() as $c) {
                    /**
                     *
                     * @var onto_common_class $class
                     */
                    $class = $this->ontology->get_class($c->uri);
                    $temp = $t;
                    if(empty($temp['GROUP'])){
                        $temp['GROUP']  = $class->field;
                    }
                    $this->fixedfields[$class->field."s".$ff["ID"]] = $temp;
                }
            }

            // Lecture des champs dynamiques
            if (isset($param["DYNAMICFIELDS"][0]["VISIBLE"]) && $param["DYNAMICFIELDS"][0]["VISIBLE"] == "no")
                $this->dynamics_not_visible = true;
            if (! isset($param["DYNAMICFIELDS"][0]["FIELDTYPE"]) || ! $param["DYNAMICFIELDS"][0]["FIELDTYPE"]) { // Pour le cas de fichiers subst basés sur l'ancienne version
                $tmp = (isset($param["DYNAMICFIELDS"][0]["FIELD"]) ? $param["DYNAMICFIELDS"][0]["FIELD"] : '');
                unset($param["DYNAMICFIELDS"]);
                $param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["PREFIX"] = "d";
                $param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["TYPE"] = "notices";
                $param["DYNAMICFIELDS"][0]["FIELDTYPE"][0]["FIELD"] = $tmp;
                unset($tmp);
            }
            // Ordre des champs persos
            if (isset($param["DYNAMICFIELDS"][0]["OPTION"][0]["ORDER"])) {
                $this->dynamicfields_order = $param["DYNAMICFIELDS"][0]["OPTION"][0]["ORDER"];
            } else {
                $this->dynamicfields_order = '';
            }
            for ($h = 0; $h < count($param["DYNAMICFIELDS"][0]["FIELDTYPE"]); $h ++) {
                $champType = array();
                $ft = $param["DYNAMICFIELDS"][0]["FIELDTYPE"][$h];
                $champType["TYPE"] = $ft["TYPE"];
                // Exclusion de champs persos cités par nom
                if (isset($ft["HIDEBYCUSTOMNAME"])) {
                    $this->dynamicfields_hidebycustomname[$ft["TYPE"]] = $ft["HIDEBYCUSTOMNAME"];
                }

                if ($this->groups_used) {
                    $champType["GROUP"] = (isset($ft["GROUP"]) ? $ft["GROUP"] : '');
                }
                if (! empty($ft["FIELD"])) {
                    for ($i = 0; $i < count($ft["FIELD"]); $i ++) {
                        $t = array();
                        $ff = $ft["FIELD"][$i];
                        $t["DATATYPE"] = $ff["DATATYPE"];
                        $t["NOTDISPLAYCOL"] = (isset($ff["NOTDISPLAYCOL"]) ? $ff["NOTDISPLAYCOL"] : '');
                        // Moteur memory
                        if (isset($ff['MEMORYENGINEFORBIDDEN']) && $ff['MEMORYENGINEFORBIDDEN'] == 'yes')
                            $t['MEMORYENGINEFORBIDDEN'] = true;
                        else
                            $t['MEMORYENGINEFORBIDDEN'] = false;
                        $q = array();
                        for ($j = 0; $j < count($ff["QUERY"]); $j ++) {
                            $q["OPERATOR"] = $ff["QUERY"][$j]["FOR"];
                            if (isset($ff["QUERY"][$j]["MULTIPLEWORDS"]) && $ff["QUERY"][$j]["MULTIPLEWORDS"] == "yes")
                                $q["MULTIPLE_WORDS"] = true;
                            else
                                $q["MULTIPLE_WORDS"] = false;
                            if (isset($ff["QUERY"][$j]["REGDIACRIT"]) && $ff["QUERY"][$j]["REGDIACRIT"] == "yes")
                                $q["REGDIACRIT"] = true;
                            else
                                $q["REGDIACRIT"] = false;
                            if (isset($ff["QUERY"][$j]["KEEP_EMPTYWORD"]) && $ff["QUERY"][$j]["KEEP_EMPTYWORD"] == "yes")
                                $q["KEEP_EMPTYWORD"] = true;
                            else
                                $q["KEEP_EMPTYWORD"] = false;
                            if (isset($ff["QUERY"][$j]["MODE"]))
                                $q["MODE"] = $ff["QUERY"][$j]["MODE"];
                            else
                                $q["MODE"] = "SQL";
                            if (isset($ff["QUERY"][$j]["DEFAULT_OPERATOR"]))
                                $q["DEFAULT_OPERATOR"] = $ff["QUERY"][$j]["DEFAULT_OPERATOR"];
                            $q["NOT_ALLOWED_FOR"] = array();
                            $naf = (isset($ff["QUERY"][$j]["NOTALLOWEDFOR"]) ? $ff["QUERY"][$j]["NOTALLOWEDFOR"] : '');
                            if ($naf) {
                                $naf_ = explode(",", $naf);
                                $q["NOT_ALLOWED_FOR"] = $naf_;
                            }
                            if (isset($ff["QUERY"][$j]['WORDSEARCH']) && $ff["QUERY"][$j]['WORDSEARCH'] == "yes") {
                                $q["WORD"] = true;
                                if (isset($ff["QUERY"][$j]['CLASS'][0]['NAME'])) {
                                    $q['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['NAME'];
                                    if (count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
                                        $q['FIELDSRESTRICT'] = array();
                                        foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
                                            $subfieldsrestrict = array();
                                            if (isset($fieldrestrict['SUB'])) {
                                                foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
                                                    $subfieldsrestrict[] = array(
                                                        'sub_field' => $subfieldrestrict['SUB_FIELD'][0]['value'],
                                                        'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
                                                        'op' => $subfieldrestrict['OP'][0]['value'],
                                                        'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
                                                    );
                                                }
                                            }
                                            $q['FIELDSRESTRICT'][] = array(
                                                'field' => $fieldrestrict['FIELD'][0]['value'],
                                                'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
                                                'op' => $fieldrestrict['OP'][0]['value'],
                                                'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
                                                'sub' => $subfieldsrestrict
                                            );
                                        }
                                    }
                                } elseif (isset($ff["QUERY"][$j]['CLASS'][0]['TYPE'])) {
                                    $q['TYPE'] = $ff["QUERY"][$j]['CLASS'][0]['TYPE'];
                                    if (isset($ff["QUERY"][$j]['CLASS'][0]['MODE'])) {
                                        $q['MODE'] = $ff["QUERY"][$j]['CLASS'][0]['MODE'];
                                    }
                                    if (isset($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT']) && count($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'])) {
                                        $q['FIELDSRESTRICT'] = array();
                                        foreach ($ff["QUERY"][$j]['CLASS'][0]['FIELDRESTRICT'] as $fieldrestrict) {
                                            $subfieldsrestrict = array();
                                            if (isset($fieldrestrict['SUB'])) {
                                                foreach ($fieldrestrict['SUB'][0]['FIELDRESTRICT'] as $subfieldrestrict) {
                                                    $subfieldsrestrict[] = array(
                                                        'sub_field' => $subfieldrestrict['SUB_FIELD'][0]['value'],
                                                        'values' => explode(',', $subfieldrestrict['VALUES'][0]['value']),
                                                        'op' => $subfieldrestrict['OP'][0]['value'],
                                                        'not' => (isset($subfieldrestrict['NOT'][0]['value']) ? $subfieldrestrict['NOT'][0]['value'] : '')
                                                    );
                                                }
                                            }
                                            $q['FIELDSRESTRICT'][] = array(
                                                'field' => $fieldrestrict['FIELD'][0]['value'],
                                                'values' => explode(',', $fieldrestrict['VALUES'][0]['value']),
                                                'op' => $fieldrestrict['OP'][0]['value'],
                                                'not' => (isset($fieldrestrict['NOT'][0]['value']) ? $fieldrestrict['NOT'][0]['value'] : ''),
                                                'sub' => $subfieldsrestrict
                                            );
                                        }
                                    }
                                } else {
                                    $q['CLASS'] = $ff["QUERY"][$j]['CLASS'][0]['value'];
                                }
                                $q['FOLDER'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FOLDER']) ? $ff["QUERY"][$j]['CLASS'][0]['FOLDER'] : '');
                                $q['FIELDS'] = (isset($ff["QUERY"][$j]['CLASS'][0]['FIELDS']) ? $ff["QUERY"][$j]['CLASS'][0]['FIELDS'] : '');
                            } else
                                $q["WORD"] = false;
                            if (isset($ff["QUERY"][$j]['SEARCHABLEONLY']) && $ff["QUERY"][$j]['SEARCHABLEONLY'] == "yes") {
                                $q["SEARCHABLEONLY"] = true;
                            } else
                                $q["SEARCHABLEONLY"] = false;

                            $q["MAIN"] = (isset($ff["QUERY"][$j]["MAIN"][0]["value"]) ? $ff["QUERY"][$j]["MAIN"][0]["value"] : '');
                            $q["MULTIPLE_TERM"] = (isset($ff["QUERY"][$j]["MULTIPLETERM"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLETERM"][0]["value"] : '');
                            $q["MULTIPLE_OPERATOR"] = (isset($ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"]) ? $ff["QUERY"][$j]["MULTIPLEOPERATOR"][0]["value"] : '');
                            $t["QUERIES"][] = $q;
                            $t["QUERIES_INDEX"][$q["OPERATOR"]] = count($t["QUERIES"]) - 1;
                        }
                        $champType["FIELD"][$ff["ID"]] = $t;
                    }
                }
                $this->dynamicfields[$ft["PREFIX"]] = $champType;
            }

            // Lecture des champs speciaux
            if (isset($param["SPECIALFIELDS"][0]["VISIBLE"]) && $param["SPECIALFIELDS"][0]["VISIBLE"] == "no") {
                $this->specials_not_visible = true;
            }
            if (! empty($param["SPECIALFIELDS"][0]["FIELD"]) && is_array($param["SPECIALFIELDS"][0]["FIELD"])) {
                for ($i = 0; $i < count($param["SPECIALFIELDS"][0]["FIELD"]); $i ++) {
                    $t = array();
                    $sf = $param["SPECIALFIELDS"][0]["FIELD"][$i];
                    if (substr($sf["TITLE"], 0, 4) == "msg:" && isset($msg[substr($sf["TITLE"], 4, strlen($sf["TITLE"]) - 4)])) {
                        $t["TITLE"] = $msg[substr($sf["TITLE"], 4, strlen($sf["TITLE"]) - 4)];
                    } else {
                        $t["TITLE"] = $sf["TITLE"];
                    }
                    if ($this->groups_used) {
                        $t["GROUP"] = (isset($sf["GROUP"]) ? $sf["GROUP"] : '');
                    }
                    $t["NOTDISPLAYCOL"] = (isset($sf["NOTDISPLAYCOL"]) ? $sf["NOTDISPLAYCOL"] : '');
                    $t["UNIMARCFIELD"] = (isset($sf["UNIMARCFIELD"]) ? $sf["UNIMARCFIELD"] : '');
                    $t["SEPARATOR"] = '';
                    if (isset($sf["SEPARATOR"])) {
                        if (substr($sf["SEPARATOR"], 0, 4) == "msg:") {
                            $t["SEPARATOR"] = $msg[substr($sf["SEPARATOR"], 4, strlen($sf["SEPARATOR"]) - 4)];
                        } else {
                            $t["SEPARATOR"] = $sf["SEPARATOR"];
                        }
                    }
                    $t["TYPE"] = $sf["TYPE"];

                    // Visibilite
                    if (isset($sf["VISIBLE"]) && $sf["VISIBLE"] == "no")
                        $t["VISIBLE"] = false;
                    else
                        $t["VISIBLE"] = true;

                    if (isset($sf["DELNOTALLOWED"]) && $sf["DELNOTALLOWED"] == "yes")
                        $t["DELNOTALLOWED"] = true;
                    else
                        $t["DELNOTALLOWED"] = false;
                    $this->specialfields[$sf["ID"]] = $t;
                }
            }
            if (is_array($this->specialfields) && (count($this->specialfields) != 0)) {
                if (file_exists($include_path . "/search_queries/specials/catalog_subst.xml")) {
                    $nom_fichier = $include_path . "/search_queries/specials/catalog_subst.xml";
                } else {
                    $nom_fichier = $include_path . "/search_queries/specials/catalog.xml";
                }
                $parametres = file_get_contents($nom_fichier);
                $this->tableau_speciaux = _parser_text_no_function_($parametres, "SPECIALFIELDS");
            }
            $this->keyName = (isset($param["KEYNAME"][0]["value"]) ? $param["KEYNAME"][0]["value"] : '');
            if(!$this->keyName) {
                $this->keyName="uri_id";
            }
            $this->tableName="onto_uri";
            $tmp_array_cache = array(
                $this->groups_used,
                $this->groups,
                $this->memory_engine_allowed,
                $this->operators,
                $this->op_empty,
                $this->fixedfields,
                $this->dynamics_not_visible,
                $this->dynamicfields_order,
                $this->dynamicfields_hidebycustomname,
                $this->dynamicfields,
                $this->specials_not_visible,
                $this->tableau_speciaux,
                $this->keyName,
                $this->tableName,
                $this->specialfields,
                $this->op_special
            );
            if ($key_file) {
                $key_file_content = $KEY_CACHE_FILE_XML . md5(serialize($tmp_array_cache));
                $cache_php->setInCache($key_file_content, $tmp_array_cache);
                $cache_php->setInCache($key_file, $key_file_content);
            } else {
                $tmp = fopen($tempFile, "wb");
                fwrite($tmp, serialize($tmp_array_cache));
                fclose($tmp);
            }
            if ($this->fichier_xml == 'search_fields_opac' || strpos($this->full_path, '/opac_css/') !== false) {
                $msg = $save_msg;
            }
        }
    }

    // fin parse_search_file
    protected function get_variable_field($var_field, $n, $search, $var_table, $fieldvar)
    {
        global $charset, $msg;
        if (empty($fieldvar)) {
            $fieldvar = array();
        }
        $variable_field = '';

        if ($var_field["TYPE"] == "input") {
            $varname = $var_field["NAME"];
            $visibility = 1;
            if (isset($var_field["OPTIONS"]["VAR"][0])) {
                $vis = $var_field["OPTIONS"]["VAR"][0];
                if ($vis["NAME"]) {
                    $vis_name = $vis["NAME"];
                    global ${$vis_name};
                    if ($vis["VISIBILITY"] == "no")
                        $visibility = 0;
                    for ($k = 0; $k < count($vis["VALUE"]); $k ++) {
                        if ($vis["VALUE"][$k]["value"] == ${$vis_name}) {
                            if ($vis["VALUE"][$k]["VISIBILITY"] == "no")
                                $sub_vis = 0;
                            else
                                $sub_vis = 1;
                            if ($vis["VISIBILITY"] == "no")
                                $visibility |= $sub_vis;
                            else
                                $visibility &= $sub_vis;
                            break;
                        }
                    }
                }
            }
            $vdefault = [];
            // Recherche de la valeur par defaut
            if (isset($var_field["OPTIONS"]["DEFAULT"][0])) {
                $vdefault = $var_field["OPTIONS"]["DEFAULT"][0];
            }
            if (count($vdefault)) {
                switch ($vdefault["TYPE"]) {
                    case "var":
                        $default = $var_table[$vdefault["value"]];
                        break;
                    case "value":
                    default:
                        $default = $vdefault["value"];
                }
            } else
                $default = "";

            if ($visibility) {
                $variable_field .= "<span class='ui-panel-display'>";
                $variable_field .= "&nbsp;";
                if (isset($var_field["CLASS"]) && $var_field["CLASS"]) {
                    $variable_field .= "<span class='" . $var_field["CLASS"] . "'>";
                }
                if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
                    $variable_field .= "<span class='" . $var_field["OPTIONS"]["INPUT"][0]["CLASS"] . "'>";
                }
                if (isset($var_field["SPAN"]) && $var_field["SPAN"]) {
                    $variable_field .= "<span class='" . $var_field["SPAN"] . "'>" . $var_field["COMMENT"] . "</span>";
                } else {
                    $variable_field .= htmlentities($var_field["COMMENT"], ENT_QUOTES, $charset);
                }
                $input = $var_field["OPTIONS"]["INPUT"][0];
                if (! empty($input["QUERY"][0]["TRANSLATIONTABLENAME"]) && ! empty($input["QUERY"][0]["TRANSLATIONFIELDNAME"])) {
                    $trans_table = $input["QUERY"][0]["TRANSLATIONTABLENAME"];
                    $trans_field = $input["QUERY"][0]["TRANSLATIONFIELDNAME"];
                } else {
                    $trans_table = '';
                    $trans_field = '';
                }
                switch ($input["TYPE"]) {
                    case "query_list":
                        if ((! isset($fieldvar[$varname]) || ! $fieldvar[$varname]) && ($default))
                            $fieldvar[$varname][0] = $default;
                        $variable_field .= "&nbsp;<span class='search_value'><select id=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" name=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\">\n";
                        $query_list_result = pmb_mysql_query($input["QUERY"][0]["value"]);
                        $var_tmp = $concat = "";
                        while ($line = pmb_mysql_fetch_array($query_list_result)) {
                            if ($concat)
                                $concat .= ",";
                            $concat .= $line[0];
                            $var_tmp .= "<option value=\"" . htmlentities($line[0], ENT_QUOTES, $charset) . "\"";
                            $as = @array_search($line[0], $fieldvar[$varname]);
                            if (($as !== false) && ($as !== NULL))
                                $var_tmp .= " selected";
                            $var_tmp .= ">";
                            if (! empty($trans_table) && ! empty($trans_field)) {
                                $var_tmp .= translation::get_translated_text($line[0], $trans_table, $trans_field, $line[1]);
                            } else {
                                $var_tmp .= htmlentities($line[1], ENT_QUOTES, $charset);
                            }
                            $var_tmp .= "</option>\n";
                        }
                        if ($input["QUERY"][0]["ALLCHOICE"] == "yes") {
                            $variable_field .= "<option value=\"" . htmlentities($concat, ENT_QUOTES, $charset) . "\"";
                            $as = @array_search($concat, $fieldvar[$varname]);
                            if (($as !== false) && ($as !== NULL))
                                $variable_field .= " selected";
                            $variable_field .= ">" . htmlentities($msg[substr($input["QUERY"][0]["TITLEALLCHOICE"], 4, strlen($input["QUERY"][0]["TITLEALLCHOICE"]) - 4)], ENT_QUOTES, $charset) . "</option>\n";
                        }
                        $variable_field .= $var_tmp;
                        $variable_field .= "</select></span>";
                        if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
                            $variable_field .= "</span>";
                        }
                        break;
                    case "checkbox":
                        if (! isset($input["DEFAULT_ON"]) || ! $input["DEFAULT_ON"]) {
                            if ((! isset($fieldvar[$varname]) || ! $fieldvar[$varname]) && ($default))
                                $fieldvar[$varname][0] = $default;
                        } elseif (! isset($fieldvar[$input["DEFAULT_ON"]][0]) || ! $fieldvar[$input["DEFAULT_ON"]][0])
                            $fieldvar[$varname][0] = $default;
                        $variable_field .= "&nbsp;<input type=\"checkbox\" name=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" value=\"" . $input["VALUE"][0]["value"] . "\" ";
                        if (! empty($fieldvar[$varname][0]) && $input["VALUE"][0]["value"] == $fieldvar[$varname][0])
                            $variable_field .= "checked";
                        $variable_field .= "/>\n";
                        if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
                            $variable_field .= "</span>";
                        }
                        break;
                    case "radio":
                        if ((! isset($fieldvar[$varname]) || ! $fieldvar[$varname]) && ($default))
                            $fieldvar[$varname][0] = $default;
                        foreach ($input["OPTIONS"][0]["LABEL"] as $radio_value) {
                            $variable_field .= "&nbsp;<input type=\"radio\" name=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" value=\"" . $radio_value["VALUE"] . "\" ";
                            if (! empty($fieldvar[$varname][0]) && $radio_value["VALUE"] == $fieldvar[$varname][0])
                                $variable_field .= "checked";
                            $variable_field .= "/>" . htmlentities($msg[substr($radio_value["value"], 4, strlen($radio_value["value"]) - 4)], ENT_QUOTES, $charset);
                        }
                        $variable_field .= "\n";
                        if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
                            $variable_field .= "</span>";
                        }
                        break;
                    case "hidden":
                        if ((! isset($fieldvar[$varname]) || ! $fieldvar[$varname]) && ($default))
                            $fieldvar[$varname][0] = $default;
                        if (isset($input["VALUE"][0]) && is_array($input["VALUE"][0]))
                            $hidden_value = $input["VALUE"][0]["value"];
                        else
                            $hidden_value = $fieldvar[$varname][0];
                        $variable_field .= "<input type='hidden' id=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" name=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" value=\"" . htmlentities($hidden_value, ENT_QUOTES, $charset) . "\"/>";
                        if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
                            $variable_field .= "</span>";
                        }
                        break;
                    case "number":
                        if ((! isset($fieldvar[$varname]) || ! $fieldvar[$varname]) && ($default))
                            $fieldvar[$varname][0] = $default;
                        if (is_array($input["VALUE"][0]))
                            $hidden_value = $input["VALUE"][0]["value"];
                        else
                            $hidden_value = $fieldvar[$varname][0];
                        $variable_field .= "<input type='number' id=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" name=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" value=\"" . htmlentities($hidden_value, ENT_QUOTES, $charset) . "\"/>";
                        if (isset($var_field["OPTIONS"]["INPUT"][0]["CLASS"]) && $var_field["OPTIONS"]["INPUT"][0]["CLASS"]) {
                            $variable_field .= "</span>";
                        }
                        break;
                }
                if (isset($var_field["CLASS"]) && $var_field["CLASS"]) {
                    $variable_field .= "</span>";
                }
                $variable_field .= "</span>";
            } else {
                if ($vis["HIDDEN"] != "no")
                    $variable_field .= "<input type='hidden' name=\"fieldvar_" . $n . "_" . $search . "[" . $varname . "][]\" value=\"" . htmlentities($default, ENT_QUOTES, $charset) . "\"/>";
            }
        }

        return $variable_field;
    }

    public function make_search($prefixe = "")
    {
        global $search;
        global $msg;
        global $include_path;
        global $pmb_search_stemming_active;
        global $default_tmp_storage_engine;

        $this->error_message = "";
        $main = "";
        $last_table = "";
        $field_keyName = $this->keyName;
        $field_tableName = $this->tableName;

        // Pour chaque champ
        if (is_array($search) && count($search)) {
            for ($i = 0; $i < count($search); $i ++) {
                // construction de la requete
                $s = explode("_", $search[$i]);

                // Recuperation de l'operateur
                $op = "op_" . $i . "_" . $search[$i];
                global ${$op};

                // Recuperation du contenu de la recherche
                $field_ = "field_" . $i . "_" . $search[$i];
                global ${$field_};
                $field = ${$field_};

                $field1_ = "field_" . $i . "_" . $search[$i] . '_1';
                global ${$field1_};
                $field1 = ${$field1_};

                // Recuperation de l'operateur inter-champ
                $inter = "inter_" . $i . "_" . $search[$i];
                global ${$inter};

                // Recuperation des variables auxiliaires
                $fieldvar_ = "fieldvar_" . $i . "_" . $search[$i];
                global ${$fieldvar_};
                $fieldvar = ${$fieldvar_};

                // Si c'est un champ fixe
                if ($s[0] == "f") {
                    $ff = $this->fixedfields[$s[1]];

                    // Choix du moteur
                    if ($this->memory_engine_allowed && ! $ff['MEMORYENGINEFORBIDDEN']) {
                        $this->current_engine = 'MEMORY';
                    } else {
                        $this->current_engine = $default_tmp_storage_engine;
                    }

                    // Calcul des variables
                    $var_table = array();
                    if (is_array($ff["VAR"]) && count($ff["VAR"])) {
                        for ($j = 0; $j < count($ff["VAR"]); $j ++) {
                            switch ($ff["VAR"][$j]["TYPE"]) {
                                case "input":
                                    $var_table[$ff["VAR"][$j]["NAME"]] = @implode(",", $fieldvar[$ff["VAR"][$j]["NAME"]]);
                                    break;
                                case "global":
                                    $global_name = $ff["VAR"][$j]["NAME"];
                                    global ${$global_name};
                                    $var_table[$ff["VAR"][$j]["NAME"]] = ${$global_name};
                                    break;
                                case "calculated":
                                    $calc = $ff["VAR"][$j]["OPTIONS"]["CALC"][0];
                                    switch ($calc["TYPE"]) {
                                        case "value_from_query":
                                            $query_calc = $calc["QUERY"][0]["value"];
                                            @reset($var_table);
                                            foreach ($var_table as $var_name => $var_value) {
                                                $query_calc = str_replace("!!" . $var_name . "!!", $var_value, $query_calc);
                                            }
                                            $r_calc = pmb_mysql_query($query_calc);
                                            $var_table[$ff["VAR"][$j]["NAME"]] = pmb_mysql_result($r_calc, 0, 0);
                                            break;
                                    }
                                    break;
                            }
                        }
                    }
                    $q_index = $ff["QUERIES_INDEX"];
                    // Recuperation de la requete associee au champ et a l'operateur
                    $q = $ff["QUERIES"][$q_index[${$op}]];

                    // Si c'est une requete conditionnelle, on sélectionne la bonne requete et on supprime les autres
                    if (isset($q[0]["CONDITIONAL"]) && $q[0]["CONDITIONAL"]) {
                        $k_default = 0;
                        $q_temp = array();
                        $q_temp["OPERATOR"] = $q["OPERATOR"];
                        for ($k = 0; $k < count($q) - 1; $k ++) {
                            if ($var_table[$q[$k]["CONDITIONAL"]["name"]] == $q[$k]["CONDITIONAL"]["value"])
                                break;
                            if ($q[$k]["CONDITIONAL"]["value"] == "default")
                                $k_default = $k;
                        }
                        if ($k == count($q) - 1)
                            $k = $k_default;
                        $q_temp[0] = $q[$k];
                        $q = $q_temp;
                    }

                    // Remplacement par les variables eventuelles pour chaque requete
                    if (is_array($q) && count($q)) {
                        for ($k = 0; $k < count($q) - 1; $k ++) {
                            reset($var_table);
                            foreach ($var_table as $var_name => $var_value) {
                                $q[$k]["MAIN"] = str_replace("!!" . $var_name . "!!", $var_value, $q[$k]["MAIN"]);
                                $q[$k]["MULTIPLE_TERM"] = str_replace("!!" . $var_name . "!!", $var_value, $q[$k]["MULTIPLE_TERM"]);
                            }
                        }
                    }
                    $last_main_table = "";

                    // pour les listes, si un opérateur permet une valeur vide, il en faut une...
                    if ($this->op_empty[${$op}] && ! is_array($field)) {
                        $field = array();
                        $field[0] = "";
                    }
                    if (! $this->op_empty[${$op}]) {
                        // nettoyage des valeurs
                        if (${$op} == 'AUTHORITY') {
                            $field = $this->clean_completion_empty_values($field);
                        } else {
                            $field = $this->clean_empty_values($field);
                        }
                    }

                    // Pour chaque valeur du champ
                    if (is_array($field) && count($field)) {
                        for ($j = 0; $j < count($field); $j ++) {
                            // Pour chaque requete
                            $field_origine = $field[$j];
                            if (! empty($q)) {
                                for ($z = 0; $z < count($q) - 1; $z ++) {
                                    // Si le nettoyage de la saisie est demande
                                    if ($q[$z]["KEEP_EMPTYWORD"])
                                        $field[$j] = strip_empty_chars($field_origine);
                                    elseif ($q[$z]["REGDIACRIT"])
                                        $field[$j] = strip_empty_words($field_origine);
                                    elseif ($q[$z]["DETECTDATE"]) {
                                        $field[$j] = detectFormatDate($field_origine, $q[$z]["DETECTDATE"]);
                                    } else
                                        $field[$j] = $field_origine;
                                    $main = $q[$z]["MAIN"];
                                    // Si il y a plusieurs termes possibles on construit la requete avec le terme !!multiple_term!!
                                    if ($q[$z]["MULTIPLE_WORDS"]) {
                                        $terms = explode(" ", $field[$j]);
                                        // Pour chaque terme,
                                        $multiple_terms = array();
                                        for ($k = 0; $k < count($terms); $k ++) {
                                            $terms[$k] = str_replace('*', '%', $terms[$k]);
                                            $multiple_terms[] = str_replace("!!p!!", $terms[$k], $q[$z]["MULTIPLE_TERM"]);
                                        }
                                        $final_term = implode(" " . $q[$z]["MULTIPLE_OPERATOR"] . " ", $multiple_terms);
                                        $main = str_replace("!!multiple_term!!", $final_term, $main);
                                        // Si la saisie est un ISBN
                                    } else if ($q[$z]["ISBN"]) {
                                        // Code brut
                                        $terms[0] = $field[$j];
                                        // EAN ?
                                        if (isEAN($field[$j])) {
                                            // C'est un isbn ?
                                            if (isISBN($field[$j])) {
                                                $rawisbn = preg_replace('/-|\.| /', '', $field[$j]);
                                                // On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
                                                $terms[1] = formatISBN($rawisbn, 10);
                                                $terms[2] = formatISBN($rawisbn, 13);
                                                $terms[3] = preg_replace('/-|\.| /', '', $terms[1]);
                                                $terms[4] = preg_replace('/-|\.| /', '', $terms[2]);
                                            }
                                        } else if (isISBN($field[$j])) {
                                            $rawisbn = preg_replace('/-|\.| /', '', $field[$j]);
                                            // On envoi tout ce qu'on sait faire en matiere d'ISBN, en raw et en formatte, en 10 et en 13
                                            $terms[1] = formatISBN($rawisbn, 10);
                                            $terms[2] = formatISBN($rawisbn, 13);
                                            $terms[3] = preg_replace('/-|\.| /', '', $terms[1]);
                                            $terms[4] = preg_replace('/-|\.| /', '', $terms[2]);
                                        }
                                        // Pour chaque terme,
                                        $multiple_terms = array();
                                        for ($k = 0; $k < count($terms); $k ++) {
                                            $terms[$k] = str_replace('*', '%', $terms[$k]);
                                            $multiple_terms[] = str_replace("!!p!!", $terms[$k], $q[$z]["MULTIPLE_TERM"]);
                                        }
                                        $final_term = implode(" " . $q[$z]["MULTIPLE_OPERATOR"] . " ", $multiple_terms);
                                        $main = str_replace("!!multiple_term!!", $final_term, $main);
                                    } else if ($q[$z]["BOOLEAN"]) {
                                        if ($q[$z]['STEMMING']) {
                                            $stemming = $pmb_search_stemming_active;
                                        } else {
                                            $stemming = 0;
                                        }
                                        $aq = new analyse_query($field[$j], 0, 0, 1, 0, $stemming);
                                        $aq1 = new analyse_query($field[$j], 0, 0, 1, 1, $stemming);
                                        if (isset($q[$z]["KEEP_EMPTY_WORDS_FOR_CHECK"]) && $q[$z]["KEEP_EMPTY_WORDS_FOR_CHECK"])
                                            $err = $aq1->error;
                                        else
                                            $err = $aq->error;
                                        if (! $err) {
                                            if (is_array($q[$z]["TABLE"])) {
                                                for ($z1 = 0; $z1 < count($q[$z]["TABLE"]); $z1 ++) {
                                                    $is_fulltext = false;
                                                    if (isset($q[$z]["FULLTEXT"][$z1]) && $q[$z]["FULLTEXT"][$z1])
                                                        $is_fulltext = true;
                                                    if (! isset($q[$z]["KEEP_EMPTY_WORDS"][$z1]) || ! $q[$z]["KEEP_EMPTY_WORDS"][$z1])
                                                        $members = $aq->get_query_members($q[$z]["TABLE"][$z1], $q[$z]["INDEX_L"][$z1], $q[$z]["INDEX_I"][$z1], $q[$z]["ID_FIELD"][$z1], $q[$z]["RESTRICT"][$z1], 0, 0, $is_fulltext);
                                                    else
                                                        $members = $aq1->get_query_members($q[$z]["TABLE"][$z1], $q[$z]["INDEX_L"][$z1], $q[$z]["INDEX_I"][$z1], $q[$z]["ID_FIELD"][$z1], $q[$z]["RESTRICT"][$z1], 0, 0, $is_fulltext);
                                                    $main = str_replace("!!pert_term_" . ($z1 + 1) . "!!", $members["select"], $main);
                                                    $main = str_replace("!!where_term_" . ($z1 + 1) . "!!", $members["where"], $main);
                                                }
                                            } else {
                                                $is_fulltext = false;
                                                if (isset($q[$z]["FULLTEXT"]) && $q[$z]["FULLTEXT"])
                                                    $is_fulltext = true;
                                                if (isset($q[$z]["KEEP_EMPTY_WORDS"]) && $q[$z]["KEEP_EMPTY_WORDS"])
                                                    $members = $aq1->get_query_members($q[$z]["TABLE"], $q[$z]["INDEX_L"], $q[$z]["INDEX_I"], $q[$z]["ID_FIELD"], (! empty($q[$z]["RESTRICT"]) ? $q[$z]["RESTRICT"] : ''), 0, 0, $is_fulltext);
                                                else
                                                    $members = $aq->get_query_members($q[$z]["TABLE"], $q[$z]["INDEX_L"], $q[$z]["INDEX_I"], $q[$z]["ID_FIELD"], (! empty($q[$z]["RESTRICT"]) ? $q[$z]["RESTRICT"] : ''), 0, 0, $is_fulltext);
                                                $main = str_replace("!!pert_term!!", $members["select"], $main);
                                                $main = str_replace("!!where_term!!", $members["where"], $main);
                                            }
                                        } else {
                                            $main = "select " . $field_keyName . " from " . $this->tableName . " where " . $field_keyName . "=0";
                                            $this->error_message = sprintf($msg["searcher_syntax_error_desc"], $aq->current_car, $aq->input_html, $aq->error_message);
                                        }
                                    } elseif (! empty($q[$z]["WORD"])) {
                                        // Pour savoir si la recherche tous champs inclut les docnum
                                        global $multi_crit_indexation_docnum_allfields;

                                        $multi_crit_indexation_docnum_allfields = - 1;
                                        if (! empty($var_table["is_num"])) {
                                            $multi_crit_indexation_docnum_allfields = 1;
                                        }

                                        // Pour savoir si la recherche inclut les oeuvres
                                        global $multi_crit_indexation_oeuvre_title;

                                        $multi_crit_indexation_oeuvre_title = - 1;
                                        if (! empty($var_table["oeuvre_query"])) {
                                            $multi_crit_indexation_oeuvre_title = 1;
                                        }

                                        if (isset($q[$z]['TYPE']) && $q[$z]['TYPE']) {
                                            $mode = '';
                                            if (isset($q[$z]['MODE'])) {
                                                $mode = $q[$z]['MODE'];
                                            }
                                            if ($q[$z]["FIELDS"]) {
                                                $searcher = searcher_factory::get_searcher($q[$z]['TYPE'], $mode, $field[$j], $q[$z]["FIELDS"]);
                                            } else {
                                                $searcher = searcher_factory::get_searcher($q[$z]['TYPE'], $mode, $field[$j],$this->get_onto()->get_id());
                                            }
                                        } else {
                                            // recherche par terme...
                                            if ($q[$z]["FIELDS"]) {
                                                $searcher = new $q[$z]['CLASS']($field[$j], $q[$z]["FIELDS"]);
                                            } else {
                                                $searcher = new $q[$z]['CLASS']($field[$j]);
                                            }
                                        }
                                        if (! empty($var_table)) {
                                            $searcher->add_var_table($var_table);
                                        }
                                        if (isset($q[$z]['FIELDSRESTRICT']) && is_array($q[$z]['FIELDSRESTRICT'])) {
                                            $searcher->add_fields_restrict($q[$z]['FIELDSRESTRICT']);
                                        }else{
                                            // On doit préciser sur quelle classe ca cherche !
                                            $tmp = explode("s",$s[1]);
                                            $searcher->add_fields_restrict([[
                                                'field' => "code_champ",
                                                'values' => array($tmp[0]),
                                                'op' => "or",
                                                'not' => false
                                            ]]);
                                        }
                                        $main = $searcher->get_full_query();
                                    } elseif ($q[$z]['CUSTOM_SEARCH']) {
                                        if ($this->op_special[${$op}]) {
                                            $table_tempo = $this->custom_search_op_special($ff, ${$op}, $i, $search[$i]);
                                        }
                                        if (! empty($table_tempo)) {
                                            $main = str_replace("!!table_tempo!!", $table_tempo, $main);
                                        }
                                    } else {
                                        $field[$j] = str_replace('*', '%', $field[$j]);
                                        $main = str_replace("!!p!!", addslashes($field[$j]), $main);
                                        $main = str_replace("!!p1!!", (isset($field1[$j]) ? addslashes($field1[$j]) : ''), $main);
                                    }
                                    if ($q[$z]['MODE'] == "sparql") {
                                        $main = $this->get_sql_query_from_sparql($main, $tmp[0]);
                                    }
                                    // Y-a-t-il une close repeat ?
                                    if (isset($q[$z]["REPEAT"]) && $q[$z]["REPEAT"]) {
                                        // Si oui, on repete !!
                                        $onvals = $q[$z]["REPEAT"]["ON"];
                                        global ${$onvals};
                                        $onvalst = explode($q[$z]["REPEAT"]["SEPARATOR"], ${$onvals});
                                        $mains = array();
                                        for ($ir = 0; $ir < count($onvalst); $ir ++) {
                                            $mains[] = str_replace("!!" . $q[$z]["REPEAT"]["NAME"] . "!!", $onvalst[$ir], $main);
                                        }
                                        $main = implode(" " . $q[$z]["REPEAT"]["OPERATOR"] . " ", $mains);
                                        $main = "select * from (" . $main . ") as sbquery" . ($q[$z]["REPEAT"]["ORDERTERM"] ? " order by " . $q[$z]["REPEAT"]["ORDERTERM"] : "");
                                    }
                                    if ($z < (count($q) - 2)){
                                        pmb_mysql_query($main);
                                    }
                                }
                            }

                            if (isset($fieldvar["operator_between_multiple_authorities"])) {
                                $operator = $fieldvar["operator_between_multiple_authorities"][0];
                            } elseif (isset($q["DEFAULT_OPERATOR"])) {
                                $operator = $q["DEFAULT_OPERATOR"];
                            } else {
                                $operator = ($this->get_multi_search_operator() ? $this->get_multi_search_operator() : "or");
                            }
                            if (count($field) > 1) {
                                $suffixe = $i . "_" . $j;
                                if ($operator == "or") {
                                    // Ou logique si plusieurs valeurs
                                    if ($prefixe) {
                                        $this->gen_temporary_table($prefixe . "mf_" . $suffixe, $main);
                                    } else {
                                        $this->gen_temporary_table("mf_" . $suffixe, $main);
                                    }

                                    if ($last_main_table) {
                                        if ($prefixe) {
                                            $requete = "insert ignore into " . $prefixe . "mf_" . $suffixe . " select " . $last_main_table . ".* from " . $last_main_table;
                                        } else {
                                            $requete = "insert ignore into mf_" . $suffixe . " select " . $last_main_table . ".* from " . $last_main_table;
                                        }
                                        pmb_mysql_query($requete);
                                        // pmb_mysql_query("drop table if exists mf_".$suffixe);
                                        pmb_mysql_query("drop table if exists " . $last_main_table);
                                    } // else pmb_mysql_query("drop table if exists mf_".$suffixe);
                                    if ($prefixe) {
                                        $last_main_table = $prefixe . "mf_" . $suffixe;
                                    } else {
                                        $last_main_table = "mf_" . $suffixe;
                                    }
                                } elseif ($operator == "and") {
                                    // ET logique si plusieurs valeurs
                                    if ($prefixe) {
                                        $this->gen_temporary_table($prefixe . "mf_" . $suffixe, $main);
                                    } else {
                                        $this->gen_temporary_table("mf_" . $suffixe, $main);
                                    }

                                    if ($last_main_table) {
                                        if ($j > 1) {
                                            $search_table = $last_main_table;
                                        } else {
                                            $search_table = $last_tables;
                                        }
                                        if ($prefixe) {
                                            $requete = $this->generate_query_op_and($prefixe, $suffixe, $search_table);
                                        } else {
                                            $requete = $this->generate_query_op_and("", $suffixe, $search_table);
                                        }
                                        pmb_mysql_query($requete);
                                        pmb_mysql_query("drop table if exists " . $last_tables);
                                    }
                                    if ($prefixe) {
                                        $last_tables = $prefixe . "mf_" . $suffixe;
                                    } else {
                                        $last_tables = "mf_" . $suffixe;
                                    }
                                    if ($prefixe) {
                                        $last_main_table = $prefixe . "and_result_" . $suffixe;
                                    } else {
                                        $last_main_table = "and_result_" . $suffixe;
                                    }
                                }
                            } // else print $main;
                        }
                    }
                    if ($last_main_table) {
                        $main = "select * from " . $last_main_table;
                    }
                } elseif ($s[0] == "s") {
                    // instancier la classe de traitement du champ special
                    $type = $this->specialfields[$s[1]]["TYPE"];
                    if ($type == "facette") {
                        // Traitement final
                        if (! empty($search_previous_table)) {
                            if (! empty($tsearched_sources)) {
                                for ($i = 0; $i < count($tsearched_sources); $i ++) {
                                    $requete = "delete $last_table from $last_table join entrepot_source_" . $tsearched_sources[$i] . " on recid = notice_id where notice_id NOT IN (SELECT notice_id FROM $search_previous_table)";
                                    pmb_mysql_query($requete);
                                }
                            } else {
                                $requete = "insert ignore into $last_table (notice_id,pert) select notice_id,pert from $search_previous_table where notice_id NOT IN (SELECT notice_id FROM $last_table)";
                                pmb_mysql_query($requete);
                            }
                            $search_previous_table = "";
                        }
                    }
                    for ($is = 0; $is < count($this->tableau_speciaux["TYPE"]); $is ++) {
                        if ($this->tableau_speciaux["TYPE"][$is]["NAME"] == $type) {
                            $sf = $this->specialfields[$s[1]];
                            require_once ($include_path . "/search_queries/specials/" . $this->tableau_speciaux["TYPE"][$is]["PATH"] . "/search.class.php");
                            $specialclass = new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1], $i, $sf, $this);
                            if (method_exists($specialclass, 'set_xml_file')) {
                                $specialclass->set_xml_file($this->fichier_xml);
                            }
                            $last_main_table = $specialclass->make_search();
                            break;
                        }
                    }
                    if (! empty($last_main_table)) {
                        $main = "select * from " . $last_main_table;
                    } else {
                        if (empty($main)) {
                            continue;
                        }
                    }
                } elseif ($s[0] == "authperso") {
                    // on est sur le cas de la recherche "Tous les champs" de l'autorité perso
                    // $s["1"] vaut l'identifiant du type d'autorité perso
                    $df = $this->dynamicfields["a"]["FIELD"]["10"];
                    $q_index = $df["QUERIES_INDEX"];
                    $q = $df["QUERIES"][$q_index[${$op}]];

                    // Choix du moteur
                    if ($this->memory_engine_allowed && ! $df['MEMORYENGINEFORBIDDEN']) {
                        $this->current_engine = 'MEMORY';
                    } else {
                        $this->current_engine = $default_tmp_storage_engine;
                    }

                    // Pour chaque valeur du champ
                    $last_main_table = "";
                    if (count($field) == 0)
                        $field[0] = "";
                    for ($j = 0; $j < count($field); $j ++) {
                        if ($q["KEEP_EMPTYWORD"])
                            $field[$j] = strip_empty_chars($field[$j]);
                        elseif ($q["REGDIACRIT"])
                            $field[$j] = strip_empty_words($field[$j]);
                        $main = $q["MAIN"];
                        // Si il y a plusieurs termes possibles
                        if ($q["MULTIPLE_WORDS"]) {
                            $terms = explode(" ", $field[$j]);
                            // Pour chaque terme
                            $multiple_terms = array();
                            for ($k = 0; $k < count($terms); $k ++) {
                                $terms[$k] = str_replace('*', '%', $terms[$k]);
                                $mt = str_replace("!!p!!", addslashes($terms[$k]), $q["MULTIPLE_TERM"]);
                                $mt = str_replace("!!autperso_type_num!!", $s[1], $mt);
                                $multiple_terms[] = $mt;
                            }
                            $final_term = implode(" " . $q["MULTIPLE_OPERATOR"] . " ", $multiple_terms);
                            $main = str_replace("!!multiple_term!!", $final_term, $main);
                        } else {
                            $field[$j] = str_replace('*', '%', $field[$j]);
                            $main = str_replace("!!p!!", addslashes($field[$j]), $main);
                        }
                        $main = str_replace("!!autperso_type_num!!", $s[1], $main);

                        if (! empty($q["WORD"])) {
                            // Recherche par termes...
                            $mode = '';
                            if (isset($q['MODE'])) {
                                $mode = $q['MODE'];
                            }
                            $field[$j] = str_replace('%', '*', $field[$j]);
                            $searcher = searcher_factory::get_searcher($q['TYPE'], $mode, $field[$j], $s[1]);
                            $main = $searcher->get_full_query();
                        }
                        // Choix de l'operateur dans la liste
                        if (isset($fieldvar["operator_between_multiple_authorities"])) {
                            $operator = $fieldvar["operator_between_multiple_authorities"][0];
                        } elseif (isset($q["DEFAULT_OPERATOR"])) {
                            $operator = $q["DEFAULT_OPERATOR"];
                        } else {
                            $operator = ($this->get_multi_search_operator() ? $this->get_multi_search_operator() : "or");
                        }
                        if (count($field) > 1) {
                            $suffixe = $i . "_" . $j;
                            if ($operator == "or") {
                                // Ou logique si plusieurs valeurs
                                if ($prefixe) {
                                    $this->gen_temporary_table($prefixe . "mf_" . $suffixe, $main);
                                } else {
                                    $this->gen_temporary_table("mf_" . $suffixe, $main);
                                }

                                if ($last_main_table) {
                                    if ($prefixe) {
                                        $requete = "insert ignore into " . $prefixe . "mf_" . $suffixe . " select " . $last_main_table . ".* from " . $last_main_table;
                                    } else {
                                        $requete = "insert ignore into mf_" . $suffixe . " select " . $last_main_table . ".* from " . $last_main_table;
                                    }
                                    pmb_mysql_query($requete);
                                    // pmb_mysql_query("drop table if exists mf_".$suffixe);
                                    pmb_mysql_query("drop table if exists " . $last_main_table);
                                } // else pmb_mysql_query("drop table if exists mf_".$suffixe);
                                if ($prefixe) {
                                    $last_main_table = $prefixe . "mf_" . $suffixe;
                                } else {
                                    $last_main_table = "mf_" . $suffixe;
                                }
                            } elseif ($operator == "and") {
                                // ET logique si plusieurs valeurs
                                if ($prefixe) {
                                    $this->gen_temporary_table($prefixe . "mf_" . $suffixe, $main);
                                } else {
                                    $this->gen_temporary_table("mf_" . $suffixe, $main);
                                }

                                if ($last_main_table) {
                                    if ($j > 1) {
                                        $search_table = $last_main_table;
                                    } else {
                                        $search_table = $last_tables;
                                    }
                                    if ($prefixe) {
                                        $requete = $this->generate_query_op_and($prefixe, $suffixe, $search_table);
                                    } else {
                                        $requete = $this->generate_query_op_and("", $suffixe, $search_table);
                                    }
                                    pmb_mysql_query($requete);
                                    pmb_mysql_query("drop table if exists " . $last_tables);
                                }
                                if ($prefixe) {
                                    $last_tables = $prefixe . "mf_" . $suffixe;
                                } else {
                                    $last_tables = "mf_" . $suffixe;
                                }
                                if ($prefixe) {
                                    $last_main_table = $prefixe . "and_result_" . $suffixe;
                                } else {
                                    $last_main_table = "and_result_" . $suffixe;
                                }
                            }
                        } // else print $main;
                    }
                    if ($last_main_table) {
                        $main = "select * from " . $last_main_table;
                    }
                } else {
                    $datatype = str_replace('http://www.pmbservices.fr/ontology#', '', $this->get_property_infos($s[1])->pmb_datatype);
                    $df = $this->get_id_from_datatype($datatype, $s[0]);
                    $q_index = $this->dynamicfields[$s[0]]["FIELD"][$df]["QUERIES_INDEX"];
                    $q = $this->dynamicfields[$s[0]]["FIELD"][$df]["QUERIES"][$q_index[${$op}]];
                    // Choix du moteur
                    if ($this->memory_engine_allowed && ! $df['MEMORYENGINEFORBIDDEN']) {
                        $this->current_engine = 'MEMORY';
                    } else {
                        $this->current_engine = $default_tmp_storage_engine;
                    }

                    // Pour chaque valeur du champ
                    $last_main_table = "";
                    if (count($field) == 0)
                        $field[0] = "";
                    for ($j = 0; $j < count($field); $j ++) {
                        // appel de la classe dynamique associée au type de champ s'il y en a une
                        if (file_exists($include_path . "/search_queries/dynamics/dynamic_search_" . $datatype . ".class.php")) {
                            require_once ($include_path . "/search_queries/dynamics/dynamic_search_" . $datatype . ".class.php");
                            $dynamic_class_name = "dynamic_search_" . $datatype;
                            $dynamic_class = new $dynamic_class_name($s[1], $s[0], $i, $df, $this);
                            $main = $dynamic_class->get_query($field[$j], $field1[$j]);
                        } else {
                            if ($q["KEEP_EMPTYWORD"])
                                $field[$j] = strip_empty_chars($field[$j]);
                            elseif ($q["REGDIACRIT"])
                                $field[$j] = strip_empty_words($field[$j]);
                            $main = $q["MAIN"];
                            // Si il y a plusieurs termes possibles
                            if ($q["MULTIPLE_WORDS"]) {
                                $terms = explode(" ", $field[$j]);
                                // Pour chaque terme
                                $multiple_terms = array();
                                for ($k = 0; $k < count($terms); $k ++) {
                                    $terms[$k] = str_replace('*', '%', $terms[$k]);
                                    $mt = str_replace("!!p!!", addslashes($terms[$k]), $q["MULTIPLE_TERM"]);
                                    $mt = str_replace("!!field!!", $s[1], $mt);
                                    $multiple_terms[] = $mt;
                                }
                                $final_term = implode(" " . $q["MULTIPLE_OPERATOR"] . " ", $multiple_terms);
                                $main = str_replace("!!multiple_term!!", $final_term, $main);
                            } elseif ($q["WORD"]) {
                                if (isset($q['TYPE']) && $q['TYPE']) {
                                    $mode = '';
                                    if (isset($q['MODE'])) {
                                        $mode = $q['MODE'];
                                    }
                                    if ($q["FIELDS"]) {
                                        $searcher = searcher_factory::get_searcher($q['TYPE'], $mode, $field[$j], $q["FIELDS"]);
                                    } else {
                                        $searcher = searcher_factory::get_searcher($q['TYPE'], $mode, $field[$j], $s[1]);
                                    }
                                } else {
                                    // recherche par terme...
                                    if ($q["FIELDS"]) {
                                        $searcher = new $q['CLASS']($field[$j], $q["FIELDS"]);
                                    } else {
                                        $searcher = new $q['CLASS']($field[$j]);
                                    }
                                }

                                if (isset($q['FIELDSRESTRICT']) && is_array($q['FIELDSRESTRICT'])) {
                                    $searcher->add_fields_restrict($q['FIELDSRESTRICT']);
                                }
                                $main = $searcher->get_full_query();
                            } else {
                                $field[$j] = str_replace('*', '%', $field[$j]);
                                $main = str_replace("!!p!!", addslashes($field[$j]), $main);
                                $main = str_replace("!!p1!!", (isset($field1[$j]) ? addslashes($field1[$j]) : ''), $main);
                                if ($q['MODE'] == "sparql") {
                                    $main = $this->get_sql_query_from_sparql($main, $s[1]);
                                }
                            }
                            $main = str_replace("!!field!!", $s[1], $main);
                        }
                        // Choix de l'operateur dans la liste
                        if (isset($q["DEFAULT_OPERATOR"])) {
                            $operator = $q["DEFAULT_OPERATOR"];
                        } else {
                            $operator = ($this->get_multi_search_operator() ? $this->get_multi_search_operator() : "or");
                        }
                        if (count($field) > 1) {
                            $suffixe = $i . "_" . $j;
                            if ($operator == "or") {
                                // Ou logique si plusieurs valeurs
                                if ($prefixe) {
                                    $this->gen_temporary_table($prefixe . "mf_" . $suffixe, $main);
                                } else {
                                    $this->gen_temporary_table("mf_" . $suffixe, $main);
                                }

                                if ($last_main_table) {
                                    if ($prefixe) {
                                        $requete = "insert ignore into " . $prefixe . "mf_" . $suffixe . " select " . $last_main_table . ".* from " . $last_main_table;
                                    } else {
                                        $requete = "insert ignore into mf_" . $suffixe . " select " . $last_main_table . ".* from " . $last_main_table;
                                    }
                                    pmb_mysql_query($requete);
                                    // pmb_mysql_query("drop table if exists mf_".$suffixe);
                                    pmb_mysql_query("drop table if exists " . $last_main_table);
                                } // else pmb_mysql_query("drop table if exists mf_".$suffixe);
                                if ($prefixe) {
                                    $last_main_table = $prefixe . "mf_" . $suffixe;
                                } else {
                                    $last_main_table = "mf_" . $suffixe;
                                }
                            } elseif ($operator == "and") {
                                // ET logique si plusieurs valeurs
                                if ($prefixe) {
                                    $this->gen_temporary_table($prefixe . "mf_" . $suffixe, $main);
                                } else {
                                    $this->gen_temporary_table("mf_" . $suffixe, $main);
                                }

                                if ($last_main_table) {
                                    if ($j > 1) {
                                        $search_table = $last_main_table;
                                    } else {
                                        $search_table = $last_tables;
                                    }
                                    if ($prefixe) {
                                        $requete = $this->generate_query_op_and($prefixe, $suffixe, $search_table);
                                    } else {
                                        $requete = $this->generate_query_op_and("", $suffixe, $search_table);
                                    }
                                    pmb_mysql_query($requete);
                                    pmb_mysql_query("drop table if exists " . $last_tables);
                                }
                                if ($prefixe) {
                                    $last_tables = $prefixe . "mf_" . $suffixe;
                                } else {
                                    $last_tables = "mf_" . $suffixe;
                                }
                                if ($prefixe) {
                                    $last_main_table = $prefixe . "and_result_" . $suffixe;
                                } else {
                                    $last_main_table = "and_result_" . $suffixe;
                                }
                            }
                        } // else print $main;
                    }

                    if ($last_main_table) {
                        $main = "select * from " . $last_main_table;
                    }
                }
                if ($prefixe) {
                    $table = $prefixe . "t_" . $i . "_" . $search[$i];
                    $this->gen_temporary_table($table, $main, true);
                } else {
                    $table = "t_" . $i . "_" . $search[$i];
                    $this->gen_temporary_table($table, $main, true);
                }
                if (! empty($last_main_table)) {
                    $requete = "drop table if exists " . $last_main_table;
                    pmb_mysql_query($requete);
                }

                // On supprime la table temporaire si elle existe (exemple : DSI multiples via le planificateur)
                if ($prefixe) {
                    pmb_mysql_query("drop table if exists " . $prefixe . "t" . $i);
                } else {
                    pmb_mysql_query("drop table if exists t" . $i);
                }

                if ($prefixe) {
                    $requete = "create temporary table " . $prefixe . "t" . $i . " ENGINE=" . $this->current_engine . " ";
                } else {
                    $requete = "create temporary table t" . $i . " ENGINE=" . $this->current_engine . " ";
                }
                $isfirst_criteria = false;
                switch (${$inter}) {
                    case "and":
                        $requete .= "select ";
                        $req_col = "SHOW columns FROM " . $table;
                        $res_col = pmb_mysql_query($req_col);
                        while ($col = pmb_mysql_fetch_object($res_col)) {
                            if ($col->Field == "pert") {
                                $requete .= "SUM(" . $table . ".pert + " . $last_table . ".pert) AS pert,";
                            } else {
                                $requete .= $table . "." . $col->Field . ",";
                            }
                        }
                        $requete = substr($requete, 0, - 1);
                        $requete .= " from $last_table,$table where " . $table . "." . $field_keyName . "=" . $last_table . "." . $field_keyName . " group by " . $field_keyName;
                        pmb_mysql_query($requete);
                        break;
                    case "or":
                        // Si la table précédente est vide, c'est comme au premier jour !
                        $requete_c = "select count(*) from " . $last_table;
                        if (! pmb_mysql_result(pmb_mysql_query($requete_c), 0, 0)) {
                            $isfirst_criteria = true;
                        } else {
                            $requete .= "select * from " . $table;
                            pmb_mysql_query($requete);
                            if ($prefixe) {
                                $this->upgrade_columns_temporary_table($prefixe . "t" . $i, $field_keyName);
                            } else {
                                $this->upgrade_columns_temporary_table("t" . $i, $field_keyName);
                            }
                            if ($prefixe) {
                                $requete = "insert into " . $prefixe . "t" . $i . " ($field_keyName,idiot) select distinct " . $last_table . "." . $field_keyName . "," . $last_table . ".idiot from " . $last_table . " left join " . $table . " on " . $last_table . ".$field_keyName=" . $table . ".$field_keyName where " . $table . ".$field_keyName is null";
                            } else {
                                $requete = "insert into t" . $i . " ($field_keyName,idiot) select distinct " . $last_table . "." . $field_keyName . "," . $last_table . ".idiot from " . $last_table . " left join " . $table . " on " . $last_table . ".$field_keyName=" . $table . ".$field_keyName where " . $table . ".$field_keyName is null";
                                // print $requete;
                            }
                            pmb_mysql_query($requete);
                        }
                        break;
                    case "ex":
                        // $requete_not="create temporary table ".$table."_b select notices.notice_id from notices left join ".$table." on notices.notice_id=".$table.".notice_id where ".$table.".notice_id is null";
                        // pmb_mysql_query($requete_not);
                        // $requete_not="alter table ".$table."_b add idiot int(1), add unique(notice_id)";
                        // pmb_mysql_query($requete_not);
                        $requete .= "select " . $last_table . ".* from $last_table left join " . $table . " on " . $table . ".$field_keyName=" . $last_table . ".$field_keyName where " . $table . ".$field_keyName is null";
                        pmb_mysql_query($requete);
                        // $requete="drop table if exists ".$table."_b";
                        // pmb_mysql_query($requete);
                        if ($prefixe) {
                            $this->upgrade_columns_temporary_table($prefixe . "t" . $i, $field_keyName);
                        } else {
                            $this->upgrade_columns_temporary_table("t" . $i, $field_keyName);
                        }
                        break;
                    default:
                        $isfirst_criteria = true;
                        if ($prefixe) {
                            $requete = "create temporary table " . $prefixe . "t" . $i . " ( idiot int(1), " . $field_keyName . " int(1) ) ENGINE=" . $this->current_engine . " ";
                            pmb_mysql_query($requete);
                            $requete = "alter table " . $prefixe . "t" . $i . " add unique(" . $field_keyName . ")";
                            pmb_mysql_query($requete);
                        } else {
                            $requete = "create temporary table t" . $i . " ( idiot int(1), " . $field_keyName . " int(1) )  ENGINE=" . $this->current_engine . " ";
                            pmb_mysql_query($requete);
                            $requete = "alter table t" . $i . " add unique(" . $field_keyName . ")";
                            pmb_mysql_query($requete);
                        }
                        break;
                }
                if (! $isfirst_criteria) {
                    if ($last_table) {
                        pmb_mysql_query("drop table if exists " . $last_table);
                    }
                    if ($table) {
                        pmb_mysql_query("drop table if exists " . $table);
                    }
                    if ($prefixe) {
                        $last_table = $prefixe . "t" . $i;
                    } else {
                        $last_table = "t" . $i;
                    }
                } else {
                    if ($last_table) {
                        pmb_mysql_query("drop table if exists " . $last_table);
                    }
                    $last_table = $table;
                }
            }
        }
        // Traitement final
        if (! empty($search_previous_table)) {
            if (! empty($tsearched_sources)) {
                for ($i = 0; $i < count($tsearched_sources); $i ++) {
                    $requete = "delete $last_table from $last_table join entrepot_source_" . $tsearched_sources[$i] . " on recid = notice_id where notice_id NOT IN (SELECT notice_id FROM $search_previous_table)";
                    pmb_mysql_query($requete);
                }
            } else {
                $requete = "insert ignore into $last_table (notice_id,pert) select notice_id,pert from $search_previous_table where notice_id NOT IN (SELECT notice_id FROM $last_table)";
                pmb_mysql_query($requete);
            }
        }
        return $last_table;
    }

    public function make_human_query()
    {
        global $search;
        global $msg;
        global $charset;
        global $include_path;

        $r = "";
        if (is_array($search) && count($search)) {
            for ($i = 0; $i < count($search); $i ++) {
                $s = explode("_", $search[$i]);
                if ($s[0] == "f") {
                    $title = '';
                    if (isset($this->fixedfields[$s[1]]["TITLE"])) {
                        $title = $this->fixedfields[$s[1]]["TITLE"];
                    }
                } elseif ($s[0] == "s") {
                    $title = $this->specialfields[$s[1]]["TITLE"];
                } elseif ($s[0] == "authperso") {
                    $title = $this->authpersos[$s[1]]['name'];
                } else {
                    $title = $this->get_property_infos($s['1'])->label;
                }
                $op = "op_" . $i . "_" . $search[$i];
                global ${$op};
                if (${$op}) {
                    $operator = $this->operators[${$op}];
                } else {
                    $operator = "";
                }
                $field = $this->get_global_value("field_" . $i . "_" . $search[$i]);

                $field1 = $this->get_global_value("field_" . $i . "_" . $search[$i] . "_1");

                // Recuperation des variables auxiliaires
                $fieldvar_ = "fieldvar_" . $i . "_" . $search[$i];
                global ${$fieldvar_};
                $fieldvar = ${$fieldvar_};
                if (! is_array($fieldvar))
                    $fieldvar = array();

                $field_aff = array();
                $fieldvar_aff = array();
                $operator_multi = ($this->get_multi_search_operator() ? $this->get_multi_search_operator() : "or");
                if ($s[0] == "f" && ! empty($this->fixedfields[$s[1]])) {
                    $ff = $this->fixedfields[$s[1]];
                    $q_index = $ff["QUERIES_INDEX"];
                    if (${$op}) {
                        $q = $ff["QUERIES"][$q_index[${$op}]];
                    } else {
                        $q = array();
                    }
                    if (isset($fieldvar["operator_between_multiple_authorities"])) {
                        $operator_multi = $fieldvar["operator_between_multiple_authorities"][0];
                    } else {
                        if (isset($q["DEFAULT_OPERATOR"]))
                            $operator_multi = $q["DEFAULT_OPERATOR"];
                    }
                    switch ($this->fixedfields[$s[1]]["INPUT_TYPE"]) {
                        case "list":
                            if (${$op} == 'EQ') {
                                $field_aff = self::get_list_display($this->fixedfields[$s[1]], $field);
                            } else {
                                $field_aff = $this->clean_empty_values($field);
                            }
                            break;
                        case "checkbox_list":
                            if (${$op} == 'EQ') {
                                $field_aff = self::get_checkbox_list_display($this->fixedfields[$s[1]], $field);
                            } else {
                                $field_aff = $this->clean_empty_values($field);
                            }
                            break;
                        case "checkbox_query_list":
                        case "query_list":
                            if (${$op} == 'EQ') {
                                $field_aff = self::get_query_list_display($this->fixedfields[$s[1]], $field);
                            } else {
                                $field_aff = $this->clean_empty_values($field);
                            }
                            break;
                        case "marc_list":
                            if (${$op} == 'EQ') {
                                $field_aff = self::get_marc_list_display($this->fixedfields[$s[1]], $field);
                            } else {
                                $field_aff = $this->clean_empty_values($field);
                            }
                            break;
                        case "date":
                            switch ($q['OPERATOR']) {
                                case 'LESS_THAN_DAYS':
                                case 'MORE_THAN_DAYS':
                                    $field_aff[0] = $field[0] . " " . htmlentities($msg['days'], ENT_QUOTES, $charset);
                                    break;
                                default:
                                    $field_aff[0] = format_date($field[0]);
                                    break;
                            }
                            if ($q['OPERATOR'] == 'BETWEEN' && $field1[0]) {
                                $field_aff[0] .= ' - ' . format_date($field1[0]);
                            }
                            break;
                        case "authoritie":
                            if (is_countable($field)) {
                                $tmp_size = sizeof($field);
                                for ($j = 0; $j < $tmp_size; $j ++) {
                                    if ((${$op} == "AUTHORITY") && (($field[$j] === "") || ($field[$j] === "0"))) {
                                        unset($field[$j]);
                                    } elseif (is_numeric($field[$j]) && (${$op} == "AUTHORITY")) {
                                        $field[$j] = self::get_authoritie_display($field[$j], $ff['INPUT_OPTIONS']['SELECTOR']);

                                        if ($ff['INPUT_OPTIONS']['SELECTOR'] == "categorie") {
                                            if (isset($fieldvar["id_thesaurus"])) {
                                                unset($fieldvar["id_thesaurus"]);
                                            }
                                        } elseif ($ff['INPUT_OPTIONS']['SELECTOR'] == "onto") {
                                            if (isset($fieldvar["id_scheme"])) {
                                                unset($fieldvar["id_scheme"]);
                                            }
                                        } elseif ($ff['INPUT_OPTIONS']['SELECTOR'] == "vedette") {
                                            if (isset($fieldvar["grammars"])) {
                                                unset($fieldvar["grammars"]);
                                            }
                                        }
                                    } else if ($ff['INPUT_OPTIONS']['SELECTOR'] == "instruments" && is_numeric($field[$j])) {
                                        $field[$j] = nomenclature_instrument::get_instrument_name_from_id($field[$j]);
                                    }
                                }
                            }
                            $field_aff = $this->clean_empty_values($field);
                            break;
                        default:
                            $field_aff = $this->clean_empty_values($field);
                            break;
                    }

                    // Opérateur spécial on fait donc appel à la class
                    if ($this->op_special[${$op}]) {
                        $field_aff[0] = $this->make_human_query_special_op($ff, ${$op}, $field);
                    }

                    // Ajout des variables si necessaire
                    reset($fieldvar);
                    $fieldvar_aff = array();
                    foreach ($fieldvar as $var_name => $var_value) {
                        // Recherche de la variable par son nom
                        $vvar = $this->fixedfields[$s[1]]["VAR"];
                        for ($j = 0; $j < count($vvar); $j ++) {
                            if (($vvar[$j]["TYPE"] == "input") && ($vvar[$j]["NAME"] == $var_name)) {

                                // Calcul de la visibilite
                                $varname = $vvar[$j]["NAME"];
                                $visibility = 1;
                                if (isset($vvar[$j]["OPTIONS"]["VAR"][0])) {
                                    $vis = $vvar[$j]["OPTIONS"]["VAR"][0];
                                    if ($vis["NAME"]) {
                                        $vis_name = $vis["NAME"];
                                        global ${$vis_name};
                                        if ($vis["VISIBILITY"] == "no")
                                            $visibility = 0;
                                        for ($k = 0; $k < count($vis["VALUE"]); $k ++) {
                                            if ($vis["VALUE"][$k]["value"] == ${$vis_name}) {
                                                if ($vis["VALUE"][$k]["VISIBILITY"] == "no")
                                                    $sub_vis = 0;
                                                else
                                                    $sub_vis = 1;
                                                if ($vis["VISIBILITY"] == "no")
                                                    $visibility |= $sub_vis;
                                                else
                                                    $visibility &= $sub_vis;
                                                break;
                                            }
                                        }
                                    }
                                }

                                $var_list_aff = array();
                                $flag_aff = false;

                                if ($visibility) {
                                    switch ($vvar[$j]["OPTIONS"]["INPUT"][0]["TYPE"]) {
                                        case "query_list":
                                            $query_list = $vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["value"];
                                            $r_list = pmb_mysql_query($query_list);
                                            while ($line = pmb_mysql_fetch_array($r_list)) {
                                                $as = array_search($line[0], $var_value);
                                                if (($as !== false) && ($as !== NULL)) {
                                                    $var_list_aff[] = $line[1];
                                                }
                                            }
                                            if ($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["ALLCHOICE"] == "yes" && count($var_list_aff) == 0) {
                                                $var_list_aff[] = $msg[substr($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"], 4, strlen($vvar[$j]["OPTIONS"]["INPUT"][0]["QUERY"][0]["TITLEALLCHOICE"]) - 4)];
                                            }
                                            $fieldvar_aff[] = implode(" " . $msg["search_or"] . " ", $var_list_aff);
                                            $flag_aff = true;
                                            break;
                                        case "checkbox":
                                            $value = $var_value[0];
                                            $label_list = $vvar[$j]["OPTIONS"]["INPUT"][0]["COMMENTS"][0]["LABEL"];
                                            for ($indice = 0; $indice < count($label_list); $indice ++) {
                                                if ($value == $label_list[$indice]["VALUE"]) {
                                                    $libelle = $label_list[$indice]["value"];
                                                    if (substr($libelle, 0, 4) == "msg:") {
                                                        $libelle = $msg[substr($libelle, 4, strlen($libelle) - 4)];
                                                    }
                                                    break;
                                                }
                                            }

                                            if ($libelle) {
                                                $fieldvar_aff[] = $libelle;
                                                $flag_aff = true;
                                            }
                                            break;
                                    }
                                    if ($flag_aff)
                                        $fieldvar_aff[count($fieldvar_aff) - 1] = $vvar[$j]["COMMENT"] . " : " . $fieldvar_aff[count($fieldvar_aff) - 1];
                                }
                            }
                        }
                    }
                } elseif ($s[0] == "s") {
                    // appel de la fonction make_human_query de la classe du champ special
                    // Recherche du type
                    $type = $this->specialfields[$s[1]]["TYPE"];
                    if (! empty($this->tableau_speciaux['TYPE'])) {
                        for ($is = 0; $is < count($this->tableau_speciaux["TYPE"]); $is ++) {
                            if ($this->tableau_speciaux["TYPE"][$is]["NAME"] == $type) {
                                $sf = $this->specialfields[$s[1]];
                                require_once ($include_path . "/search_queries/specials/" . $this->tableau_speciaux["TYPE"][$is]["PATH"] . "/search.class.php");
                                $specialclass = new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1], $i, $sf, $this);
                                $field_aff = $specialclass->make_human_query();
                                $field_aff[0] = html_entity_decode(strip_tags($field_aff[0]), ENT_QUOTES, $charset);
                                break;
                            }
                        }
                    }
                } elseif ($s[0] == "authperso") {
                    if (isset($fieldvar["operator_between_multiple_authorities"])) {
                        $operator_multi = $fieldvar["operator_between_multiple_authorities"][0];
                    } else {
                        if (isset($q["DEFAULT_OPERATOR"]))
                            $operator_multi = $q["DEFAULT_OPERATOR"];
                    }
                    if (is_countable($field)) {
                        $tmpsize = sizeof($field);
                        for ($j = 0; $j < $tmpsize; $j ++) {
                            if ((${$op} == "AUTHORITY") && (($field[$j] === "") || ($field[$j] === "0"))) {
                                unset($field[$j]);
                            } elseif (is_numeric($field[$j]) && (${$op} == "AUTHORITY")) {
                                $field[$j] = authperso::get_isbd($field[$j]);
                            }
                        }
                    }
                    $field_aff = $field;
                } else {

                    $datatype = str_replace('http://www.pmbservices.fr/ontology#', '', $this->get_property_infos($s[1])->pmb_datatype);
                    $df = $this->get_id_from_datatype($datatype, $s[0]);
                    $ofield = $this->dynamicfields[$s[0]]["FIELD"][$df];

                    $q_index = $ofield["QUERIES_INDEX"];
                    if (${$op}) {
                        $q = $ofield["QUERIES"][$q_index[${$op}]];
                    } else {
                        $q = array();
                    }
                    if (isset($q["DEFAULT_OPERATOR"]))
                        $operator_multi = $q["DEFAULT_OPERATOR"];
                    for ($j = 0; $j < count($field); $j ++) {
                        // appel de la classe dynamique associée au type de champ s'il y en a une
                        if (file_exists($include_path . "/search_queries/dynamics/dynamic_search_" . $datatype . ".class.php")) {
                            require_once ($include_path . "/search_queries/dynamics/dynamic_search_" . $datatype . ".class.php");
                            $dynamic_class_name = "dynamic_search_" . $datatype;
                            $dynamic_class = new $dynamic_class_name($s[1], $s[0], $i, $df, $this);
                            $field_aff[$j] = $dynamic_class->make_human_query($field[$j], $field1[$j]);
                        } else {
                            switch($datatype){
                                case "marclist":
                                    if (${$op} == 'EQ') {
                                        $field_aff = self::get_marclist_onto_display($this->get_property_infos($s[1])->pmb_marclist_type, $field);
                                    } else {
                                        $field_aff = $this->clean_empty_values($field[$j]);
                                    }
                                    break;
                                default :
                                    $field[$j] = $this->clean_empty_values($field[$j]);
                                    if(!empty($field[$j])){
                                        if (${$op} == "AUTHORITY") {
                                            $query = 'select ?type where { <'.$field[$j].'> rdf:type ?type }';
                                            $results = $this->get_onto()->exec_data_query($query);
                                            if(!empty($results)){
                                                $type = $results[0]->type;
                                            }
                                            $classname = onto_common_entity::get_entity_class_name($this->get_onto()->get_handler()->get_pmb_name($type),$this->ontology->name);
                                            $entity = new $classname($field[$j],$this->get_onto()->get_handler());
                                            $libelle = $entity->get_isbd();
                                            $field_aff[$j] = $entity->get_isbd();

                                        }else{
                                           $field_aff[$j] = $field[$j];
                                        }

                                        if ($q['OPERATOR'] == 'BETWEEN' && $field1[$j]) {
                                            $field_aff[$j] .= ' - ' . $field1[$j];
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }

                switch ($operator_multi) {
                    case "and":
                        $op_list = $msg["search_and"];
                        break;
                    case "or":
                        $op_list = $msg["search_or"];
                        break;
                    default:
                        $op_list = $msg["search_or"];
                        break;
                }
                if (is_array($field_aff)) {
                    $texte = implode(" " . $op_list . " ", $field_aff);
                } else {
                    $texte = "";
                }
                if (count($fieldvar_aff))
                    $texte .= " [" . implode(" ; ", $fieldvar_aff) . "]";
                $inter = "inter_" . $i . "_" . $search[$i];
                global ${$inter};
                switch (${$inter}) {
                    case "and":
                        $inter_op = $msg["search_and"];
                        break;
                    case "or":
                        $inter_op = $msg["search_or"];
                        break;
                    case "ex":
                        $inter_op = $msg["search_exept"];
                        break;
                    default:
                        $inter_op = "";
                        break;
                }
                if ($inter_op)
                    $inter_op = "<strong>" . htmlentities($inter_op, ENT_QUOTES, $charset) . "</strong>";
                if ($this->op_special[${$op}]) {
                    $r .= $inter_op . " <i><strong>" . htmlentities($title, ENT_QUOTES, $charset) . "</strong> " . htmlentities($operator, ENT_QUOTES, $charset) . " (" . $texte . ")</i> ";
                } elseif ((isset($ff['INPUT_OPTIONS']['SELECTOR']) && $ff['INPUT_OPTIONS']['SELECTOR'] == 'instruments') && (! empty($fieldvar))) {
                    $r .= $inter_op . " <i><strong>" . htmlentities($title, ENT_QUOTES, $charset) . "</strong> (" . nomenclature_instrument::get_instrument_name_from_id($field[0]) . ' ' . $operator . ' ' . $fieldvar['number_instruments'][0] . ') ';
                } elseif ((isset($ff['INPUT_OPTIONS']['SELECTOR']) && $ff['INPUT_OPTIONS']['SELECTOR'] == 'voices') && (! empty($fieldvar))) {
                    $r .= $inter_op . " <i><strong>" . htmlentities($title, ENT_QUOTES, $charset) . "</strong> (" . nomenclature_voice::get_voice_name_from_id($field[0]) . ' ' . $operator . ' ' . $fieldvar['number_voices'][0] . ') ';
                } else {
                    $r .= $inter_op . " <i><strong>" . htmlentities($title, ENT_QUOTES, $charset) . "</strong> " . htmlentities($operator, ENT_QUOTES, $charset) . " (" . htmlentities($texte, ENT_QUOTES, $charset) . ")</i> ";
                }
            }
        }
        if ($r) {
            $r = "<span class='search-human-query'>" . $r . "</span>";
        }
        return $r;
    }

    public function get_list_criteria()
    {
        global $msg, $charset;
        global $include_path;
        global $class_id;

        if (! empty($this->list_criteria)) {
            return $this-onto>list_criteria;
        }
        $this->list_criteria = array();

        // Traitement des champs fixes
        if (isset($this->fixedfields) && is_array($this->fixedfields)) {
            reset($this->fixedfields);
            // On s'en sert pour flaguer si la classe est indexée et affiche ou non le critère...
            global $class_is_indexed;
            $class_is_indexed = 0;
            foreach ($this->fixedfields as $id => $ff) {
                $tmp = explode('s',$id);
                foreach ($this->ontology->get_classes() as $c) {
                    /**
                     * @var onto_common_class $class
                     */
                    $class = $this->ontology->get_class($c->uri);
                    if($class->field == $tmp[0] && is_object($this->get_onto())){
                        $class_is_indexed = $this->get_onto()->get_handler()->class_is_indexed($c->uri);
                        break;
                    }
                }
                if ($this->visibility($ff)) {
                    if (isset($ff["GROUP"]) && isset($this->groups[$ff["GROUP"]]['label'])) {
                        $this->add_criteria($this->groups[$ff["GROUP"]]['label'], "f_" . $id, $ff["TITLE"]);
                    } else {
                        $this->add_criteria($msg["search_extended_lonely_fields"], "f_" . $id, $ff["TITLE"]);
                    }
                }
            }
        }
        if(!empty($class_id)){
            $restricted_class = onto_common_uri::get_uri($class_id);
        }
        // Traitement des champs dynamiques
        foreach ($this->ontology->get_classes() as $c) {
            $class = $this->ontology->get_class($c->uri);
            if(!empty($restricted_class) && $restricted_class != $c->uri){
                continue;
            }
            foreach ($class->get_properties() as $uri_property) {
                $property = $class->get_property($uri_property);
                $this->add_criteria($class->label, "o_" . $class->field . "s" . $property->subfield, $property->get_label());
            }
        }

        // Traitement des champs spéciaux
        if (! $this->specials_not_visible && $this->specialfields) {
            foreach ($this->specialfields as $id => $sf) {
                for ($i = 0; $i < count($this->tableau_speciaux['TYPE']); $i ++) {
                    if ($this->tableau_speciaux["TYPE"][$i]["NAME"] == $sf['TYPE']) {
                        require_once ($include_path . "/search_queries/specials/" . $this->tableau_speciaux["TYPE"][$i]["PATH"] . "/search.class.php");
                        $classname = $this->tableau_speciaux["TYPE"][$i]["CLASS"];
                        if ((isset($sf['VISIBLE']) && $sf['VISIBLE'] && ! method_exists($classname, 'check_visibility')) || (method_exists($classname, 'check_visibility') && $classname::check_visibility() == true)) {
                            if (isset($sf["GROUP"]) && $sf["GROUP"]) {
                                $this->add_criteria($this->groups[$sf["GROUP"]]['label'], "s_" . $id, $sf["TITLE"]);
                            } else {
                                $this->add_criteria($msg["search_extended_lonely_fields"], "s_" . $id, $sf["TITLE"]);
                            }
                        }
                    }
                }
            }
        }
        /**
         * On parcourt la propriété groups contenant les
         * groupes ordonnés selon l'ordre défini dans le XML
         */
        $this->sort_list_criteria();

        return $this->list_criteria;
    }

    public function show_dnd_form()
    {
        global $javascript_path, $extended_search_dnd_tpl, $extended_search_dnd_tab_tpl;
        global $mode, $external_type;
        global $current_module, $rmc_tab;

        $search_controller_class = 'SearchOntoController';
        $unique_identifier = md5(microtime());

        if (isset($rmc_tab) && $rmc_tab == 'false') {
            $form = $extended_search_dnd_tpl;
        } else {
            $form = $extended_search_dnd_tab_tpl;
        }

        $form = str_replace('!!unique_identifier!!', $unique_identifier, $form);
        $form = str_replace('!!search_controller_class!!', $search_controller_class, $form);
        $form = str_replace('!!search_controller_module!!', $current_module, $form);
        $form = str_replace('!!search_ontology_id!!', (!empty($this->get_onto()) ? ", '".$this->get_onto()->get_id()."'" : ''), $form);
        return $form;
    }

    public function get_already_selected_fields()
    {
        global $add_field;
        global $delete_field;
        global $search;
        global $launch_search;
        global $charset;
        global $msg;
        global $include_path;

        // Affichage des champs deja saisis
        $r = "";
        $n = 0;
        $this->script_window_onload = '';
        $r .= "<table id='extended-search-container' class='table-no-border' role='presentation'>";
        if (is_array($search) && count($search)) {
            for ($i = 0; $i < count($search); $i ++) {
                if ((string) $i != $delete_field) {
                    $f = explode("_", $search[$i]);
                    // On regarde si l'on doit masquer des colonnes
                    $notdisplaycol = array();
                    if ($f[0] == "f") {
                        if ($this->fixedfields[$f[1]]["NOTDISPLAYCOL"]) {
                            $notdisplaycol = explode(",", $this->fixedfields[$f[1]]["NOTDISPLAYCOL"]);
                        }
                    } elseif ($f[0] == "s") {
                        if ($this->specialfields[$f[1]]["NOTDISPLAYCOL"]) {
                            $notdisplaycol = explode(",", $this->specialfields[$f[1]]["NOTDISPLAYCOL"]);
                        }
                    }
                    $r .= "<tr>";
                    $r .= "<td " . (in_array("1", $notdisplaycol) ? "style='display:none;'" : "") . ">"; // Colonne 1
                    $r .= "<input type='hidden' name='search[]' value='" . $search[$i] . "'>";
                    $r .= "</td>";
                    $r .= "<td class='search_first_column' " . (in_array("2", $notdisplaycol) ? "style='display:none;'" : "") . ">"; // Colonne 2
                    if ($n > 0) {
                        $inter = "inter_" . $i . "_" . $search[$i];
                        global ${$inter};
                        $r .= "<span class='search_operator'><select name='inter_" . $n . "_" . $search[$i] . "'>";
                        $r .= "<option value='and' ";
                        if (${$inter} == "and")
                            $r .= " selected";
                        $r .= ">" . $msg["search_and"] . "</option>";
                        $r .= "<option value='or' ";
                        if (${$inter} == "or")
                            $r .= " selected";
                        $r .= ">" . $msg["search_or"] . "</option>";
                        $r .= "<option value='ex' ";
                        if (${$inter} == "ex")
                            $r .= " selected";
                        $r .= ">" . $msg["search_exept"] . "</option>";
                        $r .= "</select></span>";
                    } else
                        $r .= "&nbsp;";
                    $r .= "</td>";

                    $r .= "<td " . (in_array("3", $notdisplaycol) ? "style='display:none;'" : "") . "><span class='search_critere'>"; // Colonne 3
                    if ($f[0] == "f") {
                        $r .= htmlentities($this->fixedfields[$f[1]]["TITLE"], ENT_QUOTES, $charset);
                    } elseif ($f[0] == "s") {
                        $r .= htmlentities($this->specialfields[$f[1]]["TITLE"], ENT_QUOTES, $charset);
                    } elseif ($f[0] == "authperso") {
                        $r .= htmlentities($this->authpersos[$f[1]]['name'], ENT_QUOTES, $charset);
                    } else {

                        $r .= htmlentities($this->get_property_infos($f[1])->get_label(), ENT_QUOTES, $charset);
                    }
                    $r .= "</span></td>";
                    // Recherche des operateurs possibles
                    $r .= "<td " . (in_array("4", $notdisplaycol) ? "style='display:none;'" : "") . ">"; // Colonne 4
                    $op = "op_" . $i . "_" . $search[$i];
                    global ${$op};
                    if ($f[0] == "f") {
                        $r .= "<span class='search_sous_critere'><select name='op_" . $n . "_" . $search[$i] . "' id='op_" . $n . "_" . $search[$i] . "'";
                        // gestion des autorités
                        $onchange = "";
                        if (isset($this->fixedfields[$f[1]]["QUERIES_INDEX"]["AUTHORITY"])) {
                            $selector = $this->fixedfields[$f[1]]["INPUT_OPTIONS"]["SELECTOR"];
                            $p1 = $this->fixedfields[$f[1]]["INPUT_OPTIONS"]["P1"];
                            $p2 = $this->fixedfields[$f[1]]["INPUT_OPTIONS"]["P2"];
                        }
                        $onchange = " onchange='operatorChanged(\"" . $n . "_" . $search[$i] . "\",this.value, \"" . $this->fixedfields[$f[1]]['INPUT_TYPE'] . "\");' ";
                        $r .= "$onchange>\n";

                        // Personnalisation des opérateurs par l'interface
                        $this->fixedfields[$f[1]]["QUERIES"] = $this->get_misc_search_fields()->apply_operators_substitution($search[$i], $this->fixedfields[$f[1]]["QUERIES"]);

                        if (is_countable($this->fixedfields[$f[1]]["QUERIES"])) {
                            for ($j = 0; $j < count($this->fixedfields[$f[1]]["QUERIES"]); $j ++) {
                                $q = $this->fixedfields[$f[1]]["QUERIES"][$j];
                                $r .= "<option value='" . $q["OPERATOR"] . "' ";
                                if (${$op} == $q["OPERATOR"])
                                    $r .= " selected";
                                $r .= ">" . htmlentities($this->operators[$q["OPERATOR"]], ENT_QUOTES, $charset) . "</option>\n";
                            }
                        }
                        $r .= "</select></span>";
                        $this->script_window_onload .= " operatorChanged('" . $n . "_" . $search[$i] . "', document.getElementById('op_" . $n . "_" . $search[$i] . "').value,'" . $this->fixedfields[$f[1]]['INPUT_TYPE'] . "'); ";
                    } elseif ($f[0] == "s") {
                        // appel de la fonction get_input_box de la classe du champ special
                        $type = $this->specialfields[$f[1]]["TYPE"];
                        for ($is = 0; $is < count($this->tableau_speciaux["TYPE"]); $is ++) {
                            if ($this->tableau_speciaux["TYPE"][$is]["NAME"] == $type) {
                                $sf = $this->specialfields[$f[1]];
                                require_once ($include_path . "/search_queries/specials/" . $this->tableau_speciaux["TYPE"][$is]["PATH"] . "/search.class.php");
                                $specialclass = new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($f[1], $n, $sf, $this);
                                $q = $specialclass->get_op();
                                if (count($q)) {
                                    $r .= "<span class='search_sous_critere'><select id='op_" . $n . "_" . $search[$i] . "' name='op_" . $n . "_" . $search[$i] . "'>\n";
                                    foreach ($q as $key => $value) {
                                        $r .= "<option value='" . $key . "' ";
                                        if (${$op} == $key)
                                            $r .= "selected";
                                        $r .= ">" . htmlentities($value, ENT_QUOTES, $charset) . "</option>\n";
                                    }
                                    $r .= "</select></span>";
                                } else
                                    $r .= "&nbsp;";
                                break;
                            }
                        }
                    } elseif ($f[0] == "authperso") {
                        $type = '';
                        // on est sur le cas de la recherche "Tous les champs" de l'autorité perso
                        // $f["1"] vaut l'identifiant du type d'autorité perso
                        $df = 10;
                        $r .= "<span class='search_sous_critere'><select name='op_" . $n . "_" . $search[$i] . "' id='op_" . $n . "_" . $search[$i] . "'";
                        // gestion des autorités
                        $onchange = "";
                        if (isset($this->dynamicfields["a"]["FIELD"][$df]["QUERIES_INDEX"]["AUTHORITY"]) && isset($this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"])) {
                            $selector = $this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"]["SELECTOR"];
                            $p1 = $this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"]["P1"];
                            $p2 = $this->dynamicfields["a"]["FIELD"][$df]["INPUT_OPTIONS"]["P2"];
                            $onchange = " onchange='operatorChanged(\"" . $n . "_" . $search[$i] . "\",this.value);' ";
                        }
                        $r .= "$onchange>\n";

                        // Personnalisation des opérateurs par l'interface
                        $this->dynamicfields["a"]["FIELD"][$df]["QUERIES"] = $this->get_misc_search_fields()->apply_operators_substitution($search[$i], $this->dynamicfields["a"]["FIELD"][$df]["QUERIES"]);

                        for ($j = 0; $j < count($this->dynamicfields["a"]["FIELD"][$df]["QUERIES"]); $j ++) {
                            $q = $this->dynamicfields["a"]["FIELD"][$df]["QUERIES"][$j];
                            $as = array_search($type, $q["NOT_ALLOWED_FOR"]);
                            if (! (($as !== null) && ($as !== false))) {
                                $r .= "<option value='" . $q["OPERATOR"] . "' ";
                                if (${$op} == $q["OPERATOR"])
                                    $r .= "selected";
                                $r .= ">" . htmlentities($this->operators[$q["OPERATOR"]], ENT_QUOTES, $charset) . "</option>\n";
                            }
                        }
                        $r .= "</select></span>";
                        $r .= "&nbsp;";
                    } else {
                        $datatype = str_replace('http://www.pmbservices.fr/ontology#', '', $this->get_property_infos($f[1])->pmb_datatype);
                        $df = $this->get_id_from_datatype($datatype, $f[0]);
                        $onchange = " onchange=\"operatorChanged('" . $n . "_" . $search[$i] . "', this.value,'" . $datatype . "');\" ";
                        $r .= "<span class='search_sous_critere'><select name='op_" . $n . "_" . $search[$i] . "'  id='op_" . $n . "_" . $search[$i] . "' " . $onchange . ">\n";

                        // Personnalisation des opérateurs par l'interface
                        $this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"] = $this->get_misc_search_fields()->apply_operators_substitution($search[$i], $this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"]);
                        for ($j = 0; $j < count($this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"]); $j ++) {
                            $q = $this->dynamicfields[$f[0]]["FIELD"][$df]["QUERIES"][$j];
                            $as = array_search($type, $q["NOT_ALLOWED_FOR"]);
                            if (! (($as !== null) && ($as !== false))) {
                                $r .= "<option value='" . $q["OPERATOR"] . "' ";
                                if (${$op} == $q["OPERATOR"])
                                    $r .= "selected";
                                $r .= ">" . htmlentities($this->operators[$q["OPERATOR"]], ENT_QUOTES, $charset) . "</option>\n";
                            }
                        }
                        $r .= "</select></span>&nbsp;";
                        $this->script_window_onload .= " operatorChanged('" . $n . "_" . $search[$i] . "', document.getElementById('op_" . $n . "_" . $search[$i] . "').value,'" . $datatype . "'); ";
                    }
                    $r .= "</td>";

                    // Affichage du champ de saisie
                    $r .= "<td " . (count($notdisplaycol) ? "colspan='" . (count($notdisplaycol) + 1) . "'" : "") . " " . (in_array("5", $notdisplaycol) ? "style='display:none;'" : "") . " class='td-border-display'>"; // Colonne 5

                    if (! empty($this->op_special[${$op}])) {
                        $r .= $this->get_field_op_special(${$op}, $this->fixedfields[$f[1]], $i, $n, $search[$i], $this->pp);
                    } else {
                        $r .= $this->get_field($i, $n, $search[$i]);
                    }

                    $r .= "</td>";
                    $delnotallowed = false;
                    if ($f[0] == "f") {
                        $delnotallowed = (isset($this->fixedfields[$f[1]]["DELNOTALLOWED"]) ? $this->fixedfields[$f[1]]["DELNOTALLOWED"] : '');
                    } elseif ($f[0] == "s") {
                        $delnotallowed = (isset($this->specialfields[$f[1]]["DELNOTALLOWED"]) ? $this->specialfields[$f[1]]["DELNOTALLOWED"] : '');
                    }
                    if ($this->limited_search) {
                        $script_limit = " this.form.limited_search.value='0'; ";
                    } else {
                        $script_limit = "";
                    }
                    $r .= "<td " . (in_array("6", $notdisplaycol) ? "style='display:none;'" : "") . "><span class='search_cancel'>" . (! $delnotallowed ? "<input id='delete_field_button_" . $n . "' type='button' class='bouton' value='" . $msg["raz"] . "' onClick=\"enable_operators(); this.form.delete_field.value='" . $n . "'; this.form.action=''; this.form.target=''; $script_limit this.form.submit();\">" : "&nbsp;") . "</td>"; // Colonne 6
                    $r .= "</tr>\n";
                    $n ++;
                }
            }
        }
        // Recherche explicite
        $r .= "</table>\n";
        $r .= "<input type='hidden' name='explicit_search' value='1'/>\n";
        $r .= "<input type='hidden' name='search_xml_file' value='" . $this->fichier_xml . "'/>\n";
        $r .= "<input type='hidden' name='search_xml_file_full_path' value='" . $this->full_path . "'/>\n";
        return $r;
    }

    protected function get_field($i, $n, $search, $pp = '')
    {
        global $charset;
        global $aff_list_empr_search;
        global $msg;
        global $include_path;
        global $pmb_map_base_layer_type;
        global $pmb_map_base_layer_params;
        global $pmb_map_size_search_edition, $pmb_map_bounding_box;

        $r = "";
        $s = explode("_", $search);

        // Champ
        $v = $this->get_global_value("field_" . $i . "_" . $search);
        if ($v == "")
            $v = array();

        $v1 = $this->get_global_value("field_" . $i . "_" . $search . '_1');
        if ($v1 == "")
            $v1 = array();

        // Variables
        $fieldvar = $this->get_global_value("fieldvar_" . $i . "_" . $search);
        if ($s[0] == "f") {
            // Champs fixes
            $ff = $this->fixedfields[$s[1]];

            // Variables globales et input
            $vvar = array();
            $var_table = array();
            for ($j = 0; $j < count($ff["VAR"]); $j ++) {
                switch ($ff["VAR"][$j]["TYPE"]) {
                    case "input":
                        $valvar = "fieldvar_" . $i . "_" . $search . "[\"" . $ff["VAR"][$j]["NAME"] . "\"]";
                        global ${$valvar};
                        $vvar[$ff["VAR"][$j]["NAME"]] = ${$valvar};
                        if ($vvar[$ff["VAR"][$j]["NAME"]] == "")
                            $vvar[$ff["VAR"][$j]["NAME"]] = array();
                        $var_table[$ff["VAR"][$j]["NAME"]] = $vvar[$ff["VAR"][$j]["NAME"]];
                        break;
                    case "global":
                        $global_name = $ff["VAR"][$j]["NAME"];
                        global ${$global_name};
                        $var_table[$ff["VAR"][$j]["NAME"]] = ${$global_name};
                        break;
                }
            }

            // Traitement des variables d'entree
            // Variables
            $r_top = '';
            $r_bottom = '';
            if (! empty($ff["VAR"])) {
                for ($j = 0; $j < count($ff["VAR"]); $j ++) {
                    if ($ff["VAR"][$j]["PLACE"] == 'top') {
                        $r_top .= $this->get_variable_field($ff["VAR"][$j], $n, $search, $var_table, $fieldvar);
                    } else {
                        $r_bottom .= $this->get_variable_field($ff["VAR"][$j], $n, $search, $var_table, $fieldvar);
                    }
                }
            }

            // Affichage des variables ayant l'attribut place='top'
            $r .= $r_top;

            switch ($ff["INPUT_TYPE"]) {
                case "authoritie_external":
                    $op = "op_" . $i . "_" . $search;
                    global ${$op};
                    $libelle = "";
                    if (${$op} == "AUTHORITY") {
                        if ($v[0] != 0) {
                            $libelle = self::get_authoritie_display($v[0], $ff['INPUT_OPTIONS']['SELECTOR']);
                        }
                        ${$op} == "BOOLEAN";
                        $r .= "<script>document.forms['search_form']." . $op . ".options[0].selected=true;</script>";
                    }

                    if ($libelle) {
                        $r .= "<span class='search_value'><input type='text' name='field_" . $n . "_" . $search . "[]' value='" . htmlentities($libelle, ENT_QUOTES, $charset) . "' class='ext_search_txt'/></span>";
                    } else {
                        $r .= "<span class='search_value'><input type='text' name='field_" . $n . "_" . $search . "[]' value='" . htmlentities($v[0], ENT_QUOTES, $charset) . "' class='ext_search_txt'/></span>";
                    }
                    break;
                case "authoritie":
                    $params = array(
                        'ajax' => $ff["INPUT_OPTIONS"]["AJAX"],
                        'selector' => $ff["INPUT_OPTIONS"]["SELECTOR"],
                        'p1' => $ff["INPUT_OPTIONS"]["P1"],
                        'p2' => $ff["INPUT_OPTIONS"]["P2"],
                        'att_id_filter' => (isset($ff["INPUT_OPTIONS"]["ATT_ID_FILTER"]) ? $ff["INPUT_OPTIONS"]["ATT_ID_FILTER"] : ''),
                        'param1' => (isset($ff["INPUT_OPTIONS"]["PARAM1"]) ? $ff["INPUT_OPTIONS"]["PARAM1"] : '')
                    );
                    $r .= $this->get_completion_authority_field($i, $n, $search, $v, $params);
                    break;
                case "text":
                    $input_placeholder = '';
                    if (isset($ff['INPUT_OPTIONS']['PLACEHOLDER'])) {
                        if (substr($ff['INPUT_OPTIONS']["PLACEHOLDER"], 0, 4) == "msg:") {
                            $input_placeholder = $msg[substr($ff['INPUT_OPTIONS']["PLACEHOLDER"], 4, strlen($ff['INPUT_OPTIONS']["PLACEHOLDER"]) - 4)];
                        } else {
                            $input_placeholder = $ff['INPUT_OPTIONS']["PLACEHOLDER"];
                        }
                    }
                    if (! isset($v[0]))
                        $v[0] = '';
                    $r .= "<span class='search_value'><input type='text' name='field_" . $n . "_" . $search . "[]' value='" . htmlentities($v[0], ENT_QUOTES, $charset) . "' " . ($input_placeholder ? "placeholder='" . htmlentities($input_placeholder, ENT_QUOTES, $charset) . "' alt='" . htmlentities($input_placeholder, ENT_QUOTES, $charset) . "' title='" . htmlentities($input_placeholder, ENT_QUOTES, $charset) . "'" : "") . " class='ext_search_txt'/></span>";
                    break;
                case "query_list":
                case "list":
                case "marc_list":
                    if (isset($ff["INPUT_OPTIONS"]["COMPLETION"]) && $ff["INPUT_OPTIONS"]["COMPLETION"] == 'yes') {
                        $params = array(
                            'ajax' => $ff["INPUT_TYPE"],
                            'selector' => $ff["INPUT_TYPE"],
                            'p1' => 'p1',
                            'p2' => 'p2'
                        );
                        $r .= $this->get_completion_selection_field($i, $n, $search, $v, $params);
                    } else {
                        $multiple = 'multiple';
                        if (isset($ff["INPUT_OPTIONS"]["MULTIPLE"]) && $ff["INPUT_OPTIONS"]["MULTIPLE"] == 'no') {
                            $multiple = '';
                        }
                        $r .= "<span class='search_value'><select name='field_" . $n . "_" . $search . "[]' $multiple size='5' class=\"ext_search_txt\">";
                        $list = $this->get_options_list_field($ff);
                        foreach ($list as $key => $value) {
                            if (is_array($value)) {
                                if (! $key) {
                                    $key = $msg['classementGen_default_libelle'];
                                }
                                $r .= "<optgroup label='" . htmlentities($key, ENT_QUOTES, $charset) . "'>";
                                foreach ($value as $sub_key => $sub_value) {
                                    $r .= "<option value='" . htmlentities($sub_key, ENT_QUOTES, $charset) . "' ";
                                    $as = array_search($sub_key, $v);
                                    if (($as !== null) && ($as !== false))
                                        $r .= " selected";
                                    $r .= ">" . htmlentities($sub_value, ENT_QUOTES, $charset) . "</option>";
                                }
                                $r .= "</optgroup>";
                            } else {
                                $r .= "<option value='" . htmlentities($key, ENT_QUOTES, $charset) . "' ";
                                $as = array_search($key, $v);
                                if (($as !== null) && ($as !== false))
                                    $r .= " selected";
                                $r .= ">" . htmlentities($value, ENT_QUOTES, $charset) . "</option>";
                            }
                        }
                        $r .= "</select></span>";
                    }
                    break;
                case "checkbox_list":
                case "checkbox_marc_list":
                case "checkbox_query_list":
                    $r .= "<span class='search_value'>";
                    $list = $this->get_options_list_field($ff);
                    foreach ($list as $key => $value) {
                        $r .= "<input type='checkbox' name='field_" . $n . "_" . $search . "[]' value='" . htmlentities($key, ENT_QUOTES, $charset) . "' ";
                        $as = array_search($key, $v);
                        if (($as !== null) && ($as !== false))
                            $r .= " checked='checked'";
                        $r .= " />&nbsp;" . htmlentities($value, ENT_QUOTES, $charset);
                    }
                    $r .= "</span>";
                    break;
                case "date":
                    $field = array();
                    $op = "op_" . $i . "_" . $search;
                    global ${$op};
                    $field['OP'] = ${$op};
                    if (! isset($v[0]))
                        $v[0] = '';
                    $field['VALUES'][0] = $v[0];
                    if (! isset($v1[0]))
                        $v1[0] = '';
                    $field['VALUES1'][0] = $v1[0];

                    $r .= "<span class='search_value'>" . $aff_list_empr_search['date_box']($field, "", "field_" . $n . "_" . $search) . "</span>";
                    break;
                case "map":
                    $layer_params = json_decode($pmb_map_base_layer_params, true);
                    $baselayer = "baseLayerType: dojox.geo.openlayers.BaseLayerType." . $pmb_map_base_layer_type;
                    if (! empty($layer_params) && count($layer_params)) {
                        if ($layer_params['name'])
                            $baselayer .= ",baseLayerName:\"" . $layer_params['name'] . "\"";
                        if ($layer_params['url'])
                            $baselayer .= ",baseLayerUrl:\"" . $layer_params['url'] . "\"";
                        if ($layer_params['options'])
                            $baselayer .= ",baseLayerOptions:" . json_encode($layer_params['options']);
                    }
                    $initialFit = '';
                    if (! count($v)) {
                        if ($pmb_map_bounding_box) {
                            $map_bounding_box = $pmb_map_bounding_box;
                        } else {
                            $map_bounding_box = '-5 50,9 50,9 40,-5 40,-5 50';
                        }
                        $map_hold = new map_hold_polygon("bounding", 0, "polygon((" . $map_bounding_box . "))");
                        if ($map_hold) {
                            $coords = $map_hold->get_coords();
                            $initialFit = explode(',', map_objects_controler::get_coord_initialFit($coords));
                        } else {
                            $initialFit = array(
                                0,
                                0,
                                0,
                                0
                            );
                        }
                    }
                    $size = explode("*", $pmb_map_size_search_edition);
                    if (count($size) != 2) {
                        $map_size = "width:800px; height:480px;";
                    } else {
                        if (is_numeric($size[0]))
                            $size[0] .= 'px';
                        if (is_numeric($size[1]))
                            $size[1] .= 'px';
                        $map_size = "width:" . $size[0] . "; height:" . $size[1] . ";";
                    }
                    $map_holds = array();
                    foreach ($v as $map_hold) {
                        $map_holds[] = array(
                            "wkt" => $map_hold,
                            "type" => "search",
                            "color" => null,
                            "objects" => array()
                        );
                    }
                    $r .= "<div id='map_search_" . $n . "_" . $search . "' data-dojo-type='apps/map/map_controler' style='$map_size' data-dojo-props='" . $baselayer . ",mode:\"search_criteria\",hiddenField:\"field_" . $n . "_" . $search . "\",initialFit:" . json_encode($initialFit, true) . ",searchHolds:" . json_encode($map_holds, true) . "'></div>";

                    break;
            }
            // Affichage des variables n'ayant pas l'attribut place='top'
            $r .= $r_bottom;
        } elseif ($s[0] == "authperso") {
            $params = array(
                'ajax' => $s[0] . '_' . $s[1],
                'selector' => $s[0],
                'p1' => 'p1',
                'p2' => 'p2'
            );
            $r = $this->get_completion_authority_field($i, $n, $search, $v, $params);
        } elseif ($s[0] == "s") {
            // appel de la fonction get_input_box de la classe du champ special
            $type = $this->specialfields[$s[1]]["TYPE"];
            for ($is = 0; $is < count($this->tableau_speciaux["TYPE"]); $is ++) {
                if ($this->tableau_speciaux["TYPE"][$is]["NAME"] == $type) {
                    $sf = $this->specialfields[$s[1]];
                    if ($this->full_path && file_exists($this->full_path . "/specials/" . $this->tableau_speciaux["TYPE"][$is]["PATH"] . "/search.class.php"))
                        require_once ($this->full_path . "/specials/" . $this->tableau_speciaux["TYPE"][$is]["PATH"] . "/search.class.php");
                    else
                        require_once ($include_path . "/search_queries/specials/" . $this->tableau_speciaux["TYPE"][$is]["PATH"] . "/search.class.php");
                    $specialclass = new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1], $n, $sf, $this);
                    $r = $specialclass->get_input_box();
                    break;
                }
            }
        } else {
            $datatype = str_replace('http://www.pmbservices.fr/ontology#', '', $this->get_property_infos($s[1])->pmb_datatype);
            $df = $this->get_id_from_datatype($datatype, $s[0]);
            $ofield = $this->dynamicfields[$s[0]]["FIELD"][$df];
            switch ($ofield['DATATYPE']) {
                case 'resource_pmb_selector':
                    $params = $this->get_ajax_completion_params_from_property($this->get_property_infos($s[1]));
                    $r .= $this->get_completion_authority_field($i, $n, $search, $v, $params);
                    break;
                case 'resource_selector':
                    $params = $this->get_ajax_completion_params_from_property($this->get_property_infos($s[1]));
                    $r .= $this->get_completion_onto_field($i, $n, $search, $v, $params);
                    break;
                case 'date':
                    $field = array();
                    $op = "op_" . $i . "_" . $search;
                    global ${$op};
                    $field['OP'] = ${$op};
                    if (! isset($v[0]))
                        $v[0] = '';
                    $field['VALUES'][0] = $v[0];
                    if (! isset($v1[0]))
                        $v1[0] = '';
                    $field['VALUES1'][0] = $v1[0];
                    $r .= "<span class='search_value'>" . $aff_list_empr_search['date_box']($field, $check_scripts, "field_" . $n . "_" . $search) . "</span>";
                    break;
                case 'marclist' :
//                     $multiple = 'multiple';
//                     if (isset($ff["INPUT_OPTIONS"]["MULTIPLE"]) && $ff["INPUT_OPTIONS"]["MULTIPLE"] == 'no') {
//                         $multiple = '';
//                     }
                    $r.="<span class='search_value'><select name='field_".$n."_".$search."[]' $multiple size='5' class=\"ext_search_txt\">";
                    $list = $this->get_marclist_list_field($this->get_property_infos($s[1]));
                    foreach ($list as $key=>$value) {
                        if(is_array($value)) {
                            if(!$key) {
                                $key = $msg['classementGen_default_libelle'];
                            }
                            $r.="<optgroup label='".htmlentities($key,ENT_QUOTES,$charset)."'>";
                            foreach($value as $sub_key=>$sub_value) {
                                $r.="<option value='".htmlentities($sub_key,ENT_QUOTES,$charset)."' ";
                                $as=array_search($sub_key,$v);
                                if (($as!==null)&&($as!==false)) $r.=" selected";
                                $r.=">".htmlentities($sub_value,ENT_QUOTES,$charset)."</option>";
                            }
                            $r.="</optgroup>";
                        } else {
                            $r.="<option value='".htmlentities($key,ENT_QUOTES,$charset)."' ";
                            $as=array_search($key,$v);
                            if (($as!==null)&&($as!==false)) $r.=" selected";
                            $r.=">".htmlentities($value,ENT_QUOTES,$charset)."</option>";
                        }
                    }
                    $r.="</select></span>";
                    break;
                // TODO les autres datatypes !
                default:
                    if (! isset($v[0]))
                        $v[0] = '';
                    $r .= "<span class='search_value'><input type='text' name='field_" . $n . "_" . $search . "[]' value='" . htmlentities($v[0], ENT_QUOTES, $charset) . "' class='ext_search_txt'/></span>";
                    break;
            }
        }
        return $r;
    }

    public function show_results($url, $url_to_search_form, $hidden_form = true, $search_target = "", $acces = false)
    {
        global $begin_result_liste;
        global $search;
        global $msg;
        global $pmb_nb_max_tri;
        global $debug;

        // Y-a-t-il des champs ?
        if (count($search) == 0) {
            array_pop($_SESSION["session_history"]);
            error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
            exit();
        }
        $recherche_externe = false;
        // Savoir si l'on peut faire une recherche externe à partir des critères choisis
        // Verification des champs vides
        for ($i = 0; $i < count($search); $i ++) {
            $op = $this->get_global_value("op_" . $i . "_" . $search[$i]);

            $field = $this->get_global_value("field_" . $i . "_" . $search[$i]);
            $field1 = $this->get_global_value("field_" . $i . "_" . $search[$i] . "_1");

            $s = explode("_", $search[$i]);
            $bool = false;
            if ($s[0] == "f") {
                $champ = $this->fixedfields[$s[1]]["TITLE"];

                if ($this->op_special[$op]) {
                    if ($this->is_empty_op_special($this->fixedfields[$s[1]], $op, $i, $search[$i])) {
                        $bool = true;
                    }
                } else if ($this->is_empty($field, "field_" . $i . "_" . $search[$i]) && $this->is_empty($field1, "field_" . $i . "_" . $search[$i] . "_1")) {
                    $bool = true;
                }
            } elseif ($s[0] == "s") {
                $recherche_externe = false;
                $champ = $this->specialfields[$s[1]]["TITLE"];
                $type = $this->specialfields[$s[1]]["TYPE"];
                for ($is = 0; $is < count($this->tableau_speciaux["TYPE"]); $is ++) {
                    if ($this->tableau_speciaux["TYPE"][$is]["NAME"] == $type) {
                        $sf = $this->specialfields[$s[1]];
                        global $include_path;
                        require_once ($include_path . "/search_queries/specials/" . $this->tableau_speciaux["TYPE"][$is]["PATH"] . "/search.class.php");
                        $specialclass = new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1], $i, $sf, $this);
                        $bool = $specialclass->is_empty($field);
                        break;
                    }
                }
            } else {

                $champ = $this->get_property_infos($s[1])->label;
                if ($this->is_empty($field, "field_" . $i . "_" . $search[$i])) {
                    $bool = true;
                }
            }
            if (($bool) && (! $this->op_empty[$op])) {
                $query_data = array_pop($_SESSION["session_history"]);
                error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"], $champ), 1);
                print $this->get_back_button($query_data);
                exit();
            }
        }
        $table = $this->make_search();

        if ($acces == true) {
            $this->filter_searchtable_from_accessrights($table);
        }
        if (! empty($this->context_parameters['in_selector'])) {
            $this->filter_searchtable_without_no_display($table);
        }

        $requete = "select count(1) from $table";
        if ($res = pmb_mysql_query($requete)) {
            $nb_results = pmb_mysql_result($res, 0, 0);
        } else {
            $query_data = array_pop($_SESSION["session_history"]);
            error_message_history("", $msg["search_impossible"], 1);
            print $this->get_back_button($query_data);
            exit();
        }

        // gestion du tri
        $has_sort = false;
        if ($nb_results <= $pmb_nb_max_tri) {
            if ($_SESSION["tri"]) {
                $table = $this->sort_results($table);
                $has_sort = true;
            }
        }
        // fin gestion tri
        // Y-a-t-il une erreur lors de la recherche ?
        if ($this->error_message) {
            $query_data = array_pop($_SESSION["session_history"]);
            error_message_history("", $this->error_message, 1);
            print $this->get_back_button($query_data);
            exit();
        }

        if ($hidden_form) {
            print $this->make_hidden_search_form($url, $this->get_hidden_form_name(), "", false);
            print facette_search_compare::form_write_facette_compare();
            print "</form>";
        }

        $human_requete = $this->make_human_query();
        print "<h3 class='section-sub-title'>";
        print "<strong>" . $msg["search_search_extended"] . "</strong> : " . $human_requete;
        if ($debug)
            print "<br />" . $this->serialize_search();
        if ($nb_results) {
            print $this->get_display_nb_results($nb_results);
            print "</h3>";
            print $begin_result_liste;
            print $this->get_display_icons($nb_results, $recherche_externe);
        } else
            print "<br />" . $msg["1915"] . "</h3> ";
        print "<div class='row'>";
        if (empty($this->context_parameters['in_selector'])) {
            print "<input type='button' class='bouton' onClick=\"document." . $this->get_hidden_form_name() . ".action='" . $url_to_search_form . "'; document." . $this->get_hidden_form_name() . ".target='" . $search_target . "'; document." . $this->get_hidden_form_name() . ".submit(); return false;\" value=\"" . $msg["search_back"] . "\"/>";
            print $this->get_display_actions();
        }
        // if ($nb_results) print searcher::get_check_uncheck_all_buttons();
        print "</div>";
        print "<div class='row'>";
        print $this->get_current_search_map();
        print "</div>";
        $this->show_objects_results($table, $has_sort);

        $this->get_navbar($nb_results, $hidden_form);
    }

    protected function get_property_infos($crit)
    {
        if (! empty($this->property_infos[$crit])) {
            return $this->property_infos[$crit];
        }
        $of = explode("s", $crit);
        $this->property_infos[$crit] = [];
        foreach ($this->get_class_infos($crit)->get_properties() as $uri_property) {
            $property = $this->get_class_infos($crit)->get_property($uri_property);
            if ($property->subfield == $of[1]) {
                $this->property_infos[$crit] = $property;
                break;
            }
        }
        return $this->property_infos[$crit];
    }

    protected function get_class_infos($crit)
    {
        if (! empty($this->class_infos[$crit])) {
            return $this->class_infos[$crit];
        }
        $of = explode("s", $crit);
        $this->property_infos[$crit] = [];
        foreach ($this->ontology->get_classes() as $c) {
            $class = $this->ontology->get_class($c->uri);
            if ($class->field == $of[0]) {
                $this->class_infos[$crit] = $class;
                break;
            }
        }
        return $this->class_infos[$crit];
    }

    protected function get_sql_query_from_sparql($main, $crit)
    {
        $main = str_replace("!!type!!", addslashes($this->get_class_infos($crit)->uri), $main);
        $main = str_replace("!!predicat!!", addslashes($this->get_property_infos($crit)->uri), $main);
        // On va jouer la query...

        $results = $this->get_onto()->exec_data_query($main);
        if (empty($results)) {
            return "select uri_id as id_item from onto_uri where 0";
        }
        $uris = [];
        foreach ($results as $r) {
            $uris[] = $r->entity;
        }
        return 'select uri_id as id_item from onto_uri where uri in ("' . implode('","', $uris) . '")';
    }

    protected function get_display_actions()
    {
        return "";
    }

    protected function get_display_icons($nb_results, $recherche_externe = false)
    {
        global $msg, $mode;
        global $pmb_allow_external_search;
        global $pmb_nb_max_tri;
        global $affich_tris_result_liste;
        global $affich_external_tris_result_liste;

        $display_icons = "";
        if ($this->rec_history) {
            // Affichage des liens paniers et impression
            $current = $_SESSION["CURRENT"];
            if ($current !== false) {
                $tri_id_info = $_SESSION["tri"] ? "&sort_id=" . $_SESSION["tri"] : "";
                $display_icons .= "<span class='space-disable'>&nbsp;</span>
									<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=$current&action=print_prepare$tri_id_info','print',600,700,-2,-2,'scrollbars=yes,menubar=0,resizable=yes'); return false;\">
										<img src='" . get_url_icon('basket_small_20x20.gif') . "' style='border:0px' class='center' alt=\"" . $msg["histo_add_to_cart"] . "\" title=\"" . $msg["histo_add_to_cart"] . "\">
									</a>
									<span class='space-disable'>&nbsp;</span>
									<a href='#' onClick=\"openPopUp('./print.php?current_print=$current&action_print=print_prepare$tri_id_info','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); w.focus(); return false;\">
										<img src='" . get_url_icon('print.gif') . "' style='border:0px' class='center' alt=\"" . $msg["histo_print"] . "\" title=\"" . $msg["histo_print"] . "\"/>
									</a>";
                $display_icons .= "<span class='space-disable'>&nbsp;</span>
									<a href='#' onClick=\"openPopUp('./download.php?current_download=$current&action_download=download_prepare" . $tri_id_info . "','download'); return false;\">
										<img src='" . get_url_icon('upload.gif') . "' style='border:0px' class='center' alt=\"" . $msg["docnum_download"] . "\" title=\"" . $msg["docnum_download"] . "\"/>
									</a>";
            }
        }
        return $display_icons;
    }

    protected function get_display_nb_results($nb_results)
    {
        global $msg;

        return " => " . $nb_results . " " . $msg['onto_nb_results'] . "<br />\n";
    }

    protected function show_objects_results($table, $has_sort)
    {
        global $search;
        global $nb_per_page_search;
        global $page;

        $nb_per_page_search = intval($nb_per_page_search);
        $page = intval($page);
        if($page) {
            $start_page = $nb_per_page_search * ($page-1);
        } else {
            $start_page = 0;
        }
        $query = "select $table.* from " . $table;

        // Pas de tri pour le moment
        // if(count($search) > 1 && !$has_sort) {
        // $query .= " order by index_serie, tnvol, index_sew";
        // }
        $query .= " limit " . $start_page . "," . $nb_per_page_search;

        $result = pmb_mysql_query($query);
        $elements = array();
        while ($r = pmb_mysql_fetch_object($result)) {
            $elements[] = $r->{$this->keyName};
        }
        $this->get_elements_list_ui_class_name();
        $elements_list_ui_class_instance = new $this->elements_list_ui_class_name($elements, count($elements), false);
        $elements_list_ui_class_instance->add_context_parameter('in_search', '1');
        $elements_list_ui_class_instance->set_ontology($this->ontology);
        print "<div class='row'>" . $elements_list_ui_class_instance->get_elements_list() . "</div>";
    }

    public function get_elements_list_ui_class_name()
    {
        if (! isset($this->elements_list_ui_class_name)) {
            $this->elements_list_ui_class_name = "elements_onto_list_ui";
        }
        return $this->elements_list_ui_class_name;
    }

    protected function get_ajax_completion_params_from_property(onto_common_property $property)
    {
        switch ($property->pmb_datatype) {
            case 'http://www.pmbservices.fr/ontology#resource_pmb_selector':
                return [
                    'ajax' => $this->get_ajax_completion_param($property),
                    'selector' => $this->get_ajax_selector_param($property),
                    'p1' => 'p1',
                    'p2' => 'p2',
                    'att_id_filter' => '',
                    'param1' => ''
                ];
            case 'http://www.pmbservices.fr/ontology#resource_selector':
                return [
                    'ajax' => $this->get_ajax_completion_param($property),
                    'selector' => $this->get_ajax_selector_param($property),
                    'p1' => 'param1',
                    'p2' => 'param2',
                    'att_id_filter' => implode("||",$property->range),
                    'param1' => ''
                ];
        }
        return false;
    }

    protected function get_ajax_completion_param(onto_common_property $property)
    {
        switch ($property->range[0]) {
            case "http://www.pmbservices.fr/ontology#author":
                return "authors";
            default:
             //   var_dump($property->range);
                break;
        }
        return '';
    }

    protected function get_ajax_selector_param(onto_common_property $property)
    {
        switch ($property->range[0]) {
            case "http://www.pmbservices.fr/ontology#author":
                return "auteur";
            default:
                return 'ontologies&ontology_id='.$this->get_onto()->get_id().'&objs='.$property->pmb_name;
                break;
        }
        return '';
    }
    protected function get_completion_onto_field($i, $n, $search, $v, $params = array())
    {
        global $charset;
        global $msg;

        $fnamesans = "field_" . $n . "_" . $search;

        $fname = "field_" . $n . "_" . $search . "[]";
        $fname_id = "field_" . $n . "_" . $search . "_id";

        $fnamesanslib = "field_" . $n . "_" . $search . "_lib";
        $fnamelib = "field_" . $n . "_" . $search . "_lib[]";
        $fname_name_aut_id="fieldvar_".$n."_".$search."[authority_id][]";
        $fname_aut_id="fieldvar_".$n."_".$search."_authority_id";
        $fnamevar_id = "";

        $selector = $params['selector'];
        $p1 = $params['p1'];
        $p2 = $params['p2'];

        $op = $this->get_global_value("op_" . $i . "_" . $search);

        $v = $this->clean_completion_empty_values($v);
        $nb_values = count($v);
        if (! $nb_values) {
            // Création de la ligne
            $nb_values = 1;
        }

        $nb_max_aut = $nb_values - 1;
        $r = "<span class='ui-panel-display'>";
        $r .= "<input type='hidden' id='$fnamesans" . "_max_aut' value='" . $nb_max_aut . "'>";
        if ($params['selector'] != 'instruments') {
            $r .= "<input class='bouton' value='...' id='$fnamesans" . "_authority_selector' title='" . htmlentities($msg['title_select_from_list'], ENT_QUOTES, $charset) . "' onclick=\"openPopUp('./select.php?what=$selector&caller=search_form&$p1=" . $fname_id . "_0&$p2=" . $fnamesanslib . "_0&deb_rech=&callback=authoritySelected&infield=" . $fnamesans . "_0', 'selector')\" type=\"button\">";
        }
        $r .= "</span>";
        $r .= "<div id='el$fnamesans'>
		          <div class='search_group'>";
        for ($inc = 0; $inc < $nb_values; $inc ++) {
            if (! isset($v[$inc]))
                $v[$inc] = '';
            switch ($op) {
                case 'AUTHORITY':
                    if (!empty($v[$inc])) {
                        $query = 'select ?type where { <'.$v[$inc].'> rdf:type ?type }';
                        $results = $this->get_onto()->exec_data_query($query);
                        if(!empty($results)){
                            $type = $results[0]->type;
                        }
                        $classname = onto_common_entity::get_entity_class_name($this->get_onto()->get_handler()->get_pmb_name($type),$this->ontology->name);
                        $entity = new $classname($v[$inc],$this->get_onto()->get_handler());
                        $libelle = $entity->get_isbd();
                    } else {
                        $libelle = "";
                    }
                    break;
                default:
                    $libelle = $v[$inc];
                    break;
            }
            $r .= "<input id='" . $fnamesans . "_" . $inc . "' name='".$fname."'  type='hidden' />";
            $r .= "<span class='search_value'>
                    <input type='text' value='" . htmlentities($libelle, ENT_QUOTES, $charset) . "'
                        class='".($op == "AUTHORITY" ? "authorities " : "")."saisie-20emr expand_completion'
                        $fnamevar_id
                        id='".$fnamesanslib."_".$inc."'
                        name='$fnamelib'
                        autfield='".$fname_id."_".$inc."'
                        onkeyup='fieldChanged(\"".$fnamesans."\",".$inc.",this.value,event);'
                        completion='".$params['ajax']."'
                        callback='authoritySelected'
                        att_id_filter='".$params['att_id_filter']."'
                        value='" . htmlentities($libelle, ENT_QUOTES, $charset) . "'
                        autocomplete='off'>
				</span>";
            $r .= "<input class='bouton vider' type='button' onclick='this.form." . $fnamesanslib . "_" . $inc . ".value=\"\";this.form." . $fname_id . "_" . $inc . ".value=\"0\";this.form." . $fname_aut_id . "_" . $inc . ".value=\"0\";this.form." . $fnamesans . "_" . $inc . ".value=\"0\"; enable_operator(\"" . $fnamesans . "\", \"" . $i . "\");' value='" . $msg['raz'] . "'>";
            $r .= "<input type='hidden' id='" . $fname_aut_id . "_" . $inc . "' name='$fname_name_aut_id' value='" . htmlentities($v[$inc], ENT_QUOTES, $charset) . "' />";
            $r .= "<input type='hidden' name='" . $fname_id . "_" . $inc . "' id='" . $fname_id . "_" . $inc . "' value='" . htmlentities($v[$inc], ENT_QUOTES, $charset) . "' /><br>";
        }
        $r .= "</div></div>";
        if ($nb_values > 1) {
            $r .= "<script>
					document.getElementById('op_" . $n . "_" . $search . "').disabled=true;
					if(operators_to_enable.indexOf('op_" . $n . "_" . $search . "') === -1) {
						operators_to_enable.push('op_" . $n . "_" . $search . "');
					}
				</script>";
        }
        return $r;
    }

    public function get_marclist_list_field(onto_common_property $property, $start='', $limit=0) {
        $list = array();

        $options = clone marc_list_collection::get_instance($property->pmb_marclist_type);

        if (count($options->inverse_of)) {
            // sous tableau genre ascendant descendant...
            foreach ($options->table as $table) {
                $tmp = $tmp + $table;
            }
            $options->table = $tmp;
        } else {
            $tmp = $options->table;
}
        $tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
        $tmp=array_map("strtoupper",$tmp);//On met en majuscule
        asort($tmp);//Tri sur les valeurs en majuscule sans accent
        foreach ( $tmp as $key => $value ) {
            $tmp[$key]=$options->table[$key];//On reprend les bons couples clé / libellé
        }
        $options->table=$tmp;
        reset($options->table);

        // gestion restriction par code utilise.
        $existrestrict=false;
        $restrictqueryarray=array();
        foreach ($options->table as $key => $val) {
            if (!$start || strtolower(substr($val,0,strlen($start)))==strtolower($start)) {
                if ((!$existrestrict) || (array_search($key,$restrictqueryarray)!==false)) {
                    $list[$key] = $val;
                }
            }
        }
        return $list;
    }

    public static function get_marclist_onto_display($datatype, $field) {
        $field_aff = array();

        $opt = marc_list_collection::get_instance($datatype);

        $tmp = array();
        if (count($opt->inverse_of)) {
            // sous tableau genre ascendant descendant...
            foreach ($opt->table as $table) {
                foreach($table as $code => $label) {
                    $tmp[$code] = $label;
                }
            }
        } else {
            $tmp = $opt->table;
        }
        if (is_array($field)) {
            $nb_fields = count($field);
            for ($j = 0; $j < $nb_fields; $j++) {
                if (isset($field[$j]) && ($field[$j]!=="")) {
                    $field_aff[] = $tmp[$field[$j]];
                }
            }
        }
        return $field_aff;
    }


    /**
     *
     * @return ontology|boolean
     */
    protected function get_onto()
    {
        if(is_object($this->onto)){
            return $this->onto;
        }
        $this->onto = ontologies::get_ontology_by_pmbname($this->ontology->name);
        return $this->onto;

    }
}