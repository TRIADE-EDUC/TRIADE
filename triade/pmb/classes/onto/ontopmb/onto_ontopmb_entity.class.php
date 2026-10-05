<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_ontopmb_entity.class.php,v 1.2 2022/11/21 13:55:55 arenou Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

/**
 * class onto_common_class
 * Permet de représenter une instance d'une entité d'une ontologie
 */
class onto_ontopmb_entity extends onto_common_entity
{
    public function get_infos()
    {
        global $msg;
        global $ontology_id;
        
        $ontology_id = intval($ontology_id);
        if (! empty($this->infos)) {
            return $this->infos;
        }
        foreach ($this->data as $property => $values) {
            switch ($property) {
                case "uri":
                case "id":
                case "type":
                case "typeLabel":
                case "isbd":
                    break;
                case "pmbdatatype":
                    $vals = [];
                    foreach (self::get_translation($values) as $value) {
                        if(!empty(onto_ontopmb_datatype_pmbdatatype_selector::$options[$value->uri])){
                            $vals[] = $msg[onto_ontopmb_datatype_pmbdatatype_selector::$options[$value->uri]];
                        }
                    }
                    $this->infos[$property] = [
                        'label' => $this->handler->get_label($property),
                        'values' => $vals
                    ];
                    break;
                case "range":
                    $vals = [];
                    foreach (self::get_translation($values) as $value) {
                        if(!empty(onto_ontopmb_datatype_range_selector::$ranges[$value->uri])){
                            $vals[] = $msg[onto_ontopmb_datatype_range_selector::$ranges[$value->uri]];
                        }else {
                            $id = $value->id;
                            $isbd = $value->get_isbd();
                            $type = $value->data['type'];
                            // On est sur une classe de l'ontologie courante, on mache le travail pour le template, sinon c'est le bazar dans l'autre sens
                            $vals[] = '<a href="?ontology_id='.$ontology_id.'&categ=ontologies&sub='.$type[0].'&action=see&id='.$id.'">'.$isbd.'</a>';
                        }
                    }
                    $this->infos[$property] = [
                        'label' => $this->handler->get_label($property),
                        'values' => $vals
                    ];
                    break;
                default:
                    $vals = [];
                    foreach (self::get_translation($values) as $value) {
                        if (is_object($value)) {
                            $vals[] = [
                                'object' => $value
                            ];
                        } else {
                            $vals[] = $value;
                        }
                    }
                    $this->infos[$property] = [
                        'label' => $this->handler->get_label($property),
                        'values' => $vals
                    ];
                    break;
            }
        }
        return $this->infos;
    }
}