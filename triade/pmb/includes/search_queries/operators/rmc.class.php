<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rmc.class.php,v 1.4.4.1 2025/05/19 13:05:55 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

class rmc
{

    /**
     * Type d'entité
     *
     * @var string
     */
    private $entity_type = "";
    
    /**
     * Titre de l'onglet
     *
     * @var string
     */
    private $title = "";

    /**
     * Class search
     *
     * @var search|search_authorities
     */
    private $search;
    
    private $rmc_type;

    public function __construct($params)
    {
        $this->entity_type = $params['type'] ?? "";
        $this->rmc_type = $params['rmc_type'] ?? "";
        $this->search = $params['search'] ?? "";
        $this->title = $this->get_title();
    }

    public function get_field($i, $n, $field_search, $pp)
    {
        global $msg, $charset;

        // Récupération de la valeur de saisie
        $value_ = "field_" . $i . "_" . $field_search;
        global ${$value_};
        $value = ${$value_};

        // Récupération de la human query
        $fieldvar_ = "fieldvar_" . $i . "_" . $field_search . "_1";
        global ${$fieldvar_};
        $fieldvar = stripslashes(${$fieldvar_});

        // Récupération de la recherche en json
        $fieldvar2_ = "fieldvar_" . $i . "_" . $field_search . "_2";
        global ${$fieldvar2_};
        $fieldvar2 = stripslashes(${$fieldvar2_});

        if (empty($fieldvar) && ! empty($value)) {
            // si on a perdu l'human query on la reconstruit
            $fieldvar = $this->make_human_query($value);
        }

        if (empty($fieldvar2) && ! empty($value) && ! empty($value[0])) {
            // si on a perdu l'human query on la reconstruit
            $fieldvar2 = encoding_normalize::json_encode(unserialize($value[0]));
        }

        return "<div class='row'>
                    <label class='etiquette'>" . htmlentities($msg['search_equations'], ENT_QUOTES, $charset) . "</label>
                </div>
                <div class='row' id='rmc_search_" . $i . "'>
                    <input type='hidden' name='" . $value_ . "[]' id='" . $field_search . "_rmc_search_" . $i . "_data' value='" . $value[0] . "'/>
                    <input type='hidden' name='" . $fieldvar_ . "' id='" . $field_search . "_rmc_search_" . $i . "_human_query' value='" . htmlentities($fieldvar, ENT_QUOTES, $charset) . "'/>
                    <input type='hidden' name='" . $fieldvar2_ . "' id='" . $field_search . "_rmc_search_" . $i . "_json' value='" . $fieldvar2 . "'/>
                    <span id='" . $field_search . "_rmc_search_" . $i . "_human'>" . (! empty($fieldvar) ? $fieldvar : "") . "</span>
                    <img id='" . $field_search . "_open_rmc_" . $i . "' class='new_tab' src='" . get_url_icon('b_edit.png') . "' title=\"" . htmlentities($msg['search_segment_add_set'], ENT_QUOTES, $charset) . "\" />
                    <img id='" . $field_search . "_remove_rmc_" . $i . "' src='" . get_url_icon('cross_rmc.png') . "' title=\"" . htmlentities($msg['search_segment_delete_set'], ENT_QUOTES, $charset) . "\" />
                </div>" . $this->get_script($i, $field_search);
    }

    private function get_script($i, $field_search)
    {
        global $msg;
        
        return '<script>
                require(["dojo/on", "dojo/dom", "dojo/dom-style", "dojo/mouse", "dojo/topic", "dojo/dom-attr", "dojo/domReady!"],
                    function(on, dom, domStyle, mouse, topic, domAttr) {
            
                        on(' . $field_search . '_open_rmc_' . $i . ', "click", function(evt) {
                            
                            search_data = "";
                            var idNode  = "' . $field_search . '_rmc_search_' . $i . '";
                            var node = dom.byId(idNode + "_json");
                            if (node) {
                                search_data = node.value;
                            }
                            
                            var base_url = "./select.php?what=' . $this->entity_type . '";
                            if (domAttr.get(document.body, "page_name")) {
                                base_url += "&current_alert="+ domAttr.get(document.body, "page_name");
                                if (domAttr.get(document.body, "page_name") == "catalog") {
                                    base_url += "&mode=6";
                                }
                            }
                            base_url += "&action=advanced_search&no_search=1&search_data="+ search_data +"&method=saveAdvancedSearch&rmc_tab=false&class_name=tabContainer&id_champ=" + idNode;
                            if("ontology" == "' . $this->entity_type . '"){
                                base_url += "&element=concept"; 
                            }
                            if ( window.location !== window.parent.location ) {
                                window.top.postMessage(JSON.stringify({
                                    eventType: "openPopup",
                        			url: base_url,
                                    iframe: true,
                                    title: "' . $this->title . '",
                                }), "*");
                            } else {
                                topic.publish("openPopup", "openPopup", "buttonClicked", {
                        			url: base_url,
                                    iframe: true,
                                    title: "' . $this->title . '",
                                });
                            }
                        });
                                        
                        on(' . $field_search . '_remove_rmc_' . $i . ', "click", function(evt){
                            
                            var idNode  = "' . $field_search . '_rmc_search_' . $i . '_data";
                            var idNodeJSON  = "' . $field_search . '_rmc_search_' . $i . '_json";
                            var idNodeHuman  = "' . $field_search . '_rmc_search_' . $i . '_human";
                            var idNodeHumanQuery  = "' . $field_search . '_rmc_search_' . $i . '_human_query";
                                
                            var node = dom.byId(idNode);
                            if (node) {
                                node.value = "";
                            }
                                
                            var nodeJSON = dom.byId(idNodeJSON);
                            if (nodeJSON) {
                                nodeJSON.value = "";
                            }
                                
                            var nodeHumanQuery = dom.byId(idNodeHumanQuery);
                            if (nodeHumanQuery) {
                                nodeHumanQuery.value = "";
                            }
                                
                            var nodeHuman = dom.byId(idNodeHuman);
                            if (nodeHuman) {
                                nodeHuman.innerHTML = "";
                            }
                        });
                    }
                );
                </script>';
    }

    public function make_search($i, $field_search)
    {
        global $search;

        $table_tempo = "";
        
        if (! $this->is_empty($i, $field_search)) {

            // Récupération de la valeur de saisie
            $value_ = "field_" . $i . "_" . $field_search;
            global ${$value_};
            $value = ${$value_};

            // enregistrement de l'environnement courant
            $this->search->push();
            $search_tempo = unserialize($value[0]);
            $search = $search_tempo['SEARCH'];
            if (is_countable($search_tempo['SEARCH'])) {
                for ($j = 0; $j < count($search_tempo['SEARCH']); $j ++) {
                    // Récupération de l'opérateur
                    $op = "op_" . $j . "_" . $search_tempo[$j]['SEARCH'];
                    global ${$op};
                    ${$op} = $search_tempo[$j]['OP'];
    
                    // // Récupération du contenu de la recherche
                    $field_ = "field_" . $j . "_" . $search_tempo[$j]['SEARCH'];
                    global ${$field_};
                    ${$field_} = $search_tempo[$j]['FIELD'];
    
                    // Récupération de l'opérateur inter-champ
                    $inter = "inter_" . $j . "_" . $search_tempo[$j]['SEARCH'];
                    global ${$inter};
                    ${$inter} = $search_tempo[$j]['INTER'];
    
                    // Récupération des variables auxiliaires
                    $fieldvar_ = "fieldvar_" . $j . "_" . $search_tempo[$j]['SEARCH'];
                    global ${$fieldvar_};
                    ${$fieldvar_} = $search_tempo[$j]['FIELDVAR'];
                }
            }
            $sc = $this->get_instance_seach();
            $table_tempo = $sc->make_search("tempo_" . $value_);
            
            // restauration de l'environnement courant
            $this->search->pull();
        }

        return $table_tempo;
    }

    private function get_instance_seach()
    {
        switch ($this->rmc_type) {
            case "record":
                return new search(false, "search_fields");

            default:
            case "autorites":
                return new search_authorities(false, "search_fields_authorities");
        }
    }

    public function is_empty($i, $field_search)
    {
        // Récupération de la valeur de saisie
        $value_ = "field_" . $i . "_" . $field_search;
        global ${$value_};
        $value = ${$value_};

        if (empty($value) || ! is_array($value) || empty($value[0])) {
            return true;
        }
        return false;
    }

    public function make_human_query($value)
    {
        global $search, $charset;

        $human_query = "";
        if (!empty($value[0])) {
            
            // enregistrement de l'environnement courant
            $this->search->push();
            
            $sc = $this->get_instance_seach();
            $human_query = $sc->make_serialized_human_query($value[0]);
            
            // restauration de l'environnement courant
            $this->search->pull();
        }

        return $human_query;
    }
    
    public function get_title()
    {
        global $msg;
        
        switch ($this->entity_type) {
            case 'auteur':
                return $msg['search_extended']." | ".$msg['133'];
            case 'collection':
                return $msg['search_extended']." | ".$msg['136'];
            case 'authperso':
                return $msg['search_extended']." | ".$msg['132'];
            case 'categorie':
                return $msg['search_extended']." | ".$msg['134'];
            case 'indexint':
                return $msg['search_extended']." | ".$msg['indexint_menu'];
            case 'ontology':
                return $msg['search_extended']." | ".$msg['ontology_skos_menu'];
            case 'editeur':
                return $msg['search_extended']." | ".$msg['135'];
            case 'serie':
                return $msg['search_extended']." | ".$msg['series'];
            case 'subcollection':
                return $msg['search_extended']." | ".$msg['137'];
            case 'titre_uniforme':
                return $msg['search_extended']." | ".$msg['catal_onglet_titre_uniforme'];
            case 'notice':
                return $msg['search_extended']." | ".$msg['130'];
            default:
                return $msg['search_extended'];
        }
    }
}