<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_controler.class.php,v 1.32.4.1 2025/05/09 09:02:48 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path;

require_once $class_path."/rdf_entities_conversion/rdf_entities_converter_controller.class.php";
require_once $class_path."/contribution_area/contribution_area_form.class.php";

class onto_contribution_controler extends onto_common_controler {

    protected function proceed_edit()
    {
        $this->item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($this->params->sub, $this->params->form_id, $this->params->area_id, $this->params->form_uri));

        print $this->item->get_form("./" . $this->get_base_resource() . "lvl=" . $this->params->lvl . "&sub=" . $this->params->sub . "&area_id=" . $this->params->area_id . "&id=" . $this->params->id . '&form_id=' . $this->params->form_id . '&form_uri=' . $this->params->form_uri);
    }

    public function proceed($last_item = true)
    {
        global $msg, $lvl_redirect, $unauthorized_ids;
        // on affecte la propriete item par une instance si necessaire...
        $this->init_item();
        switch ($this->params->action) {
            case 'push':
                if ($last_item) {
                    print $msg["onto_contribution_push_in_progress"];
                }
                $data = $this->proceed_push();
                if ($last_item) {
                    if (! empty($unauthorized_ids)) {
                        print "<script>
                                window.location = './empr.php?tab=contribution_area&lvl=" . (! empty($lvl_redirect) ? $lvl_redirect : 'contribution_area_list') . "&check_ids=" . $unauthorized_ids . "'
                               </script>";
                    } else {
                        print "<script>
                                window.location = './empr.php?tab=contribution_area&lvl=contribution_area_done&last_id=" . $data['id'] . "'
                               </script>";
                    }
                }
                break;
            case 'save_push':
                print encoding_normalize::json_encode($this->proceed_push());
                break;
            case 'save':
                print encoding_normalize::json_encode($this->proceed_save());
                break;
            case "save_draft":
                print encoding_normalize::json_encode($this->proceed_save(false, true));
                break;
            case 'delete':
                global $ajax;
                if ($last_item && ! $ajax) {
                    print $msg["onto_contribution_delete_in_progress"];
                }

                // proceed_delete retourner les infos que si $ajax = true
                $result = $this->proceed_delete(true);

                if ($last_item && ! $ajax) {
                    print "<script>
                            window.location = './empr.php?tab=contribution_area&lvl=" . (! empty($lvl_redirect) ? $lvl_redirect : 'contribution_area_list') . "&check_ids= " . (! empty($unauthorized_ids) ? $unauthorized_ids : '') . "'
                           </script>";
                }
                if ($ajax) {

                    $error = ! empty($result['errors']) ? true : false;
                    $retour = [
                        'success' => $error ? false : true,
                        'error' => $error,
                        'id' => $this->item->get_id(),
                        'displayLabel' => '',
                        'uri' => ''
                    ];
                    ajax_http_send_response(json_encode($retour));
                }
                break;
            case "edit_entity":
                $this->proceed_edit_entity();
                break;
            default:
                parent::proceed();
                break;
        }
    }

    protected function proceed_push()
    {
        global $class_path;
        global $pmb_contribution_ws_url, $pmb_contribution_ws_username, $pmb_contribution_ws_password;

        $return = array();
        if ($this->params->action == "save_push") {
            $return = $this->proceed_save(false);
        }

        require_once $class_path . '/jsonRPCClient.php';

        try {

            $jsonRPC = new jsonRPCClient(stripslashes($pmb_contribution_ws_url));
            $jsonRPC->setUser(stripslashes($pmb_contribution_ws_username));
            $jsonRPC->setPwd(stripslashes($pmb_contribution_ws_password));

            $result = $jsonRPC->pmbesContributions_integrate_entity($this->item->get_uri());

        } catch (Exception $e) {}

        if (! $return) {
            $return = array(
                "uri" => $this->item->get_uri(),
                "id" => $this->item->get_id()
            );
        }
        $return["entity"] = $result;
        // on enregistre un triplet faisant le lien entre l'URI et l'id de l'entite creee
        $data_store = $this->handler->get_data_store();
        $this->save_entity_id_in_store($result, $data_store);

        if (! empty($return) && ! empty($return['id'])) {
            if ($this->params->action == 'push') {
                contribution_area_forms_controller::mail_empr_contribution_validate($this->item->get_uri());
            }
            // Une fois la contribution validee on ne stocke plus aucune donnee dans le store
            $this->proceed_delete(true);
        }
        return $return;
    }

    /**
     * On enregistre les triplets faisant le lien entre l'URI et l'id des entites creees
     *
     * @param array $data : Tableau des entites a inserer sous la forme uri, id, children
     * @param onto_store $data_store : Store dans lequel on agit
     */
    protected function save_entity_id_in_store($data, $data_store)
    {
        if (empty($data)) {
            return;
        }
        $query = '	select ?pmb_id where {
                        <' . $data['uri'] . '> pmb:identifier "' . $data["id"] . '" .
                        <' . $data['uri'] . '> pmb:identifier ?pmb_id
                    }';
        $data_store->query($query);

        if (! $data_store->num_rows()) {
            $query_insert = 'insert into <pmb> {
                                <' . $data['uri'] . '> pmb:identifier "' . $data["id"] . '" .
                            }';
            $data_store->query($query_insert);
        }
        if (! empty($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->save_entity_id_in_store($child, $data_store);
            }
        }
    }

    protected function proceed_save($list = true, $draft = false)
    {
        $datatype = $this->item->get_values_from_form();

        $result = $this->handler->save($this->item, $draft);
        if ($result !== true) {
            $ui_class_name = self::resolve_ui_class_name($this->params->sub, $this->handler->get_onto_name());
            return array(
                "errors" => $ui_class_name::display_errors($this, $result, true)
            );
        } else {
            $display_label = $this->item->get_label($this->handler->get_display_labels($this->handler->get_class_uri($this->params->sub)));
            $data = array(
                "uri" => $this->item->get_uri(),
                "displayLabel" => $display_label,
                "id" => $this->item->get_id()
            );

            if ($draft) {
                $data['draft_identifier'] = $this->item->get_draft_identifier();
                $last_edit = new DateTime();
                $data['date'] = $last_edit->format('d/m/Y');
                $data['time'] = $last_edit->format('H:i:s');
                if (! empty($datatype) && ! empty($datatype["http://www.pmbservices.fr/ontology#thumbnail"]) && ! empty($datatype["http://www.pmbservices.fr/ontology#thumbnail"][0])) {
                    $thumbnail = $datatype["http://www.pmbservices.fr/ontology#thumbnail"][0];
                    $data['thumbnail_name'] = $thumbnail->get_value();
                    $data['thumbnail_data'] = urlencode($thumbnail->get_data());
                }
            }

            if ("save" == $this->params->action) {
                contribution_area_forms_controller::alert_mail_users_pmb();
            }

            return $data;
        }
    }

    protected function proceed_delete($force_delete = false)
    {
        global $ajax;

        $result = $this->handler->delete($this->item, $force_delete);

        $errors = $this->handler->get_errors();
        $result['errors'] = [];
        if (! empty($errors)) {
            $result['errors'] = $errors;
        }

        if (empty($errors) && $this->item->onto_class->pmb_name == "docnum") {
            $this->item->remove_file_uploads();
        }

        if ($ajax) {
            return $result;
        }
    }

    protected function proceed_edit_entity()
    {
        global $entity_no_convert_available;

        $assertions = rdf_entities_converter_controller::convert($this->params->id, $this->params->sub, $this->params->item_uri);
        if ($assertions != false) {
            $this->item->set_contribution_area_form(contribution_area_form::get_contribution_area_form($this->params->sub, $this->params->form_id, $this->params->area_id, $this->params->form_uri));
            $this->item->set_assertions($assertions);
            $this->item->set_identifier($this->params->id);
            print $this->item->get_form_entity("./" . $this->get_base_resource() . "lvl=" . $this->params->lvl . "&sub=" . $this->params->sub . "&area_id=" . $this->params->area_id . "&id=" . $this->params->id . '&form_id=' . $this->params->form_id . '&form_uri=' . $this->params->form_uri);
        } else {
            print $entity_no_convert_available;
        }
    }

    protected function init_item()
    {
        if (! intval($this->params->id)) {
            $this->params->id = onto_common_uri::get_id($this->params->id);
        }
        switch ($this->params->action) {
            case 'edit_entity':
                $this->params->item_uri = onto_common_uri::get_new_uri('http://www.pmbservices.fr/ontology/' . $this->params->type . '#');
                $this->item = $this->handler->get_item($this->handler->get_class_uri($this->params->sub), $this->params->item_uri);
                break;
            default:
                parent::init_item();
                break;
        }
    }
}
