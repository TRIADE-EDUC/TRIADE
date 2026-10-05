<?php
// +-------------------------------------------------+
// © 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_contributions.class.php,v 1.2 2020/12/22 16:55:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class vedette_contributions {
    public static function save_vedette($id_contribution, $type, $json_parameters) {
        if ($type == 'record' || $type == 'work') {
            $vedette_ids = [];
            
            // Suppression des vedettes liées
            $deleted_vedette_links_ids = vedette_contributions::delete_vedette_links($id_contribution);
            
            // Suppression des responsabilités
            pmb_mysql_query("DELETE FROM responsability_contribution WHERE responsability_contribution_num = '$id_contribution'");
            
            $parameters = encoding_normalize::json_decode($json_parameters, true);
            
            if (!empty($parameters['has_main_author'])) {
                $vedette_id = vedette_contributions::update_vedettes($parameters['has_main_author']['default_value'], 0, TYPE_CONTRIBUTION_NOTICE_RESPONSABILITY_PRINCIPAL, $id_contribution);
                $vedette_ids[] = $vedette_id;
            }
            if (!empty($parameters['has_secondary_author'])) {
                $vedette_id = vedette_contributions::update_vedettes($parameters['has_secondary_author']['default_value'], 1, TYPE_CONTRIBUTION_NOTICE_RESPONSABILITY_SECONDAIRE, $id_contribution);
                $vedette_ids[] = $vedette_id;
            }
            if (!empty($parameters['has_other_author'])) {
                $vedette_id = vedette_contributions::update_vedettes($parameters['has_other_author']['default_value'], 2, TYPE_CONTRIBUTION_NOTICE_RESPONSABILITY_AUTRE, $id_contribution);
                $vedette_ids[] = $vedette_id;
            }
            if (!empty($parameters['has_responsability_authperso'])) {
                $vedette_id = vedette_contributions::update_vedettes($parameters['has_responsability_authperso']['default_value'], 3, TYPE_CONTRIBUTION_AUTHPERSO_RESPONSABILITY, $id_contribution);
                $vedette_ids[] = $vedette_id;
            }
            if (!empty($parameters['has_responsability_author'])) {
                $vedette_id = vedette_contributions::update_vedettes($parameters['has_responsability_author']['default_value'], 4, TYPE_CONTRIBUTION_TU_RESPONSABILITY, $id_contribution);
                $vedette_ids[] = $vedette_id;
            }
            if (!empty($parameters['has_responsability_performer'])) {
                $vedette_id = vedette_contributions::update_vedettes($parameters['has_responsability_performer']['default_value'], 5, TYPE_CONTRIBUTION_TU_RESPONSABILITY_INTERPRETER_RESPONSABILITY, $id_contribution);
                $vedette_ids[] = $vedette_id;
            }
            
            foreach ($deleted_vedette_links_ids as $id_vedette) {
                if (!in_array($id_vedette, $vedette_ids)) {
                    $vedette_composee = new vedette_composee($id_vedette);
                    $vedette_composee->delete();
                }
            }
            
            contribution_area_form::save_parameters($id_contribution, $parameters);
        }
    }
    
    public static function delete_vedette_links($id_contribution) {
        $vedette_ids = [];
        $rqt = "SELECT id_responsability_contribution AS id, responsability_contribution_type AS type FROM responsability_contribution WHERE responsability_contribution_num = '$id_contribution'";
        $res = pmb_mysql_query($rqt);
        if (pmb_mysql_num_rows($res)) {
            while ($row = pmb_mysql_fetch_object($res)) {
                $id_vedette = 0;
                switch ($row->type) {
                    case 0:
                        $id_vedette = vedette_link::delete_vedette_link_from_object(new vedette_composee(0, 'notice_authors'), $row->id, TYPE_CONTRIBUTION_NOTICE_RESPONSABILITY_PRINCIPAL);
                        break;
                    case 1:
                        $id_vedette = vedette_link::delete_vedette_link_from_object(new vedette_composee(0, 'notice_authors'), $row->id, TYPE_CONTRIBUTION_NOTICE_RESPONSABILITY_AUTRE);
                        break;
                    case 2:
                        $id_vedette = vedette_link::delete_vedette_link_from_object(new vedette_composee(0, 'notice_authors'), $row->id, TYPE_CONTRIBUTION_NOTICE_RESPONSABILITY_SECONDAIRE);
                        break;
                    case 3:
                        $id_vedette = vedette_link::delete_vedette_link_from_object(new vedette_composee(0, 'notice_authors'), $row->id, TYPE_CONTRIBUTION_AUTHPERSO_RESPONSABILITY);
                        break;
                }
                if (!empty($id_vedette)) {
                    $vedette_ids[] = $id_vedette;
                }
            }
        }
        return $vedette_ids;
    }
    
    public static function update_vedettes(&$vedette_values, $type, $vedette_type, $id_contribution) {
        $id_vedette = 0;
        $rqt_ins = "INSERT INTO responsability_contribution (responsability_contribution_author_num, responsability_contribution_num, responsability_contribution_fonction, responsability_contribution_type, responsability_contribution_ordre) VALUES ";
        foreach ($vedette_values as $order => $vedette_value) {
            $vedette_values[$order]['assertions']['qualification']['id'] = 0;
            if (!empty($vedette_value['assertions']['qualification']['apercu_vedette'])) {
                $rqt = $rqt_ins . "('" . $vedette_value['value'] . "', '$id_contribution', '" . $vedette_value['assertions']['author_function'] . "', '$type', '$order')";
                pmb_mysql_query($rqt);
                $id_responsability = pmb_mysql_insert_id();
                $id_vedette = vedette_contributions::update_vedette(stripslashes_array($vedette_value), $id_responsability, $vedette_type);
                $vedette_values[$order]['assertions']['qualification']['id'] = $id_vedette;
            }
        }
        
        return $id_vedette;
    }
    
    public static function update_vedette($values, $id_responsability, $vedette_type) {
        $vedette_composee = new vedette_composee($values['assertions']['qualification']['id'], 'notice_authors');
        
        if (!empty($values['assertions']['qualification']['apercu_vedette'])) {
            $vedette_composee->set_label($values['assertions']['qualification']['apercu_vedette']);
        }
        
        $vedette_composee->reset_elements();
        $vedette_composee_id = 0;
        $tosave = false;
        
        foreach ($values['assertions']['qualification']['elements'] as $subdivision => $elements) {
            foreach ($elements as $order => $element) {
                if (!empty($element['id'] && !empty($element['label']))) {
                    $tosave = true;
                    
                    $type_element = $element['type'];
                    if (strpos($type_element, 'vedette_ontologies') === 0) {
                        $type_element = 'vedette_ontologies';
                    }
                    
                    $available_field_class_name = $vedette_composee->get_at_available_field_num($element['available_field_num']);
                    if (empty($available_field_class_name['params'])) {
                        $available_field_class_name['params'] = [];
                    }
                    
                    $vedette_element = new $type_element($element['available_field_num'], $element['id'], $element['label'], $available_field_class_name['params']);
                    $vedette_composee->add_element($vedette_element, $subdivision, $order);
                }
            }
        }
        
        if (!empty($tosave)) {
            $vedette_composee_id = $vedette_composee->save();
            if (!empty($vedette_composee_id)) {
                vedette_link::save_vedette_link($vedette_composee, $id_responsability, $vedette_type);
            }
        }
        
        return $vedette_composee_id;
    }
}