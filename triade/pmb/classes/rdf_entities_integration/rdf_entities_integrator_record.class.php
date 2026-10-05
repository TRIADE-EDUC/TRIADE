<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_record.class.php,v 1.35.2.1 2024/06/25 09:46:19 tsamson Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

require_once($class_path . '/rdf_entities_integration/rdf_entities_integrator.class.php');
require_once($class_path . '/notice.class.php');
require_once($class_path . '/acces.class.php');

class rdf_entities_integrator_record extends rdf_entities_integrator
{
    protected $table_name = 'notices';

    protected $table_key = 'notice_id';

    protected $ppersos_prefix = 'notices';

    protected function init_map_fields()
    {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
            'http://www.pmbservices.fr/ontology#bibliographical_lvl' => 'niveau_biblio',
            'http://www.pmbservices.fr/ontology#hierarchical_lvl' => 'niveau_hierar',
            'http://www.pmbservices.fr/ontology#doctype' => 'typdoc',
            'http://www.pmbservices.fr/ontology#tit1' => 'tit1',
            'http://www.pmbservices.fr/ontology#tit2' => 'tit2',
            'http://www.pmbservices.fr/ontology#tit3' => 'tit3',
            'http://www.pmbservices.fr/ontology#tit4' => 'tit4',
            'http://www.pmbservices.fr/ontology#tnvol' => 'tnvol',
            'http://www.pmbservices.fr/ontology#nocoll' => 'nocoll',
            'http://www.pmbservices.fr/ontology#has_date' => 'year',
            'http://www.pmbservices.fr/ontology#publishing_notice' => 'mention_edition',
            'http://www.pmbservices.fr/ontology#isbn' => 'code',
            'http://www.pmbservices.fr/ontology#nb_pages' => 'npages',
            'http://www.pmbservices.fr/ontology#illustration' => 'ill',
            'http://www.pmbservices.fr/ontology#size' => 'size',
            'http://www.pmbservices.fr/ontology#price' => 'prix',
            'http://www.pmbservices.fr/ontology#accompanying_material' => 'accomp',
            'http://www.pmbservices.fr/ontology#general_note' => 'n_gen',
            'http://www.pmbservices.fr/ontology#content_note' => 'n_contenu',
            'http://www.pmbservices.fr/ontology#resume_note' => 'n_resume',
            'http://www.pmbservices.fr/ontology#keywords' => 'index_l',
            'http://www.pmbservices.fr/ontology#url' => 'lien',
            'http://www.pmbservices.fr/ontology#eformat' => 'eformat',
            'http://www.pmbservices.fr/ontology#record_language' => 'indexation_lang',
            // 'http://www.pmbservices.fr/ontology#new_record' => 'notice_is_new',
            'http://www.pmbservices.fr/ontology#comment' => 'commentaire_gestion',
            'http://www.pmbservices.fr/ontology#thumbnail_url' => 'thumbnail_url',
            'http://www.pmbservices.fr/ontology#has_record_status' => 'statut'
        ));
        return $this->map_fields;
    }

    protected function init_foreign_fields()
    {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
            'http://www.pmbservices.fr/ontology#tparent' => 'tparent_id',
            'http://www.pmbservices.fr/ontology#has_publisher' => 'ed1_id',
            'http://www.pmbservices.fr/ontology#has_other_publisher' => 'ed2_id',
            'http://www.pmbservices.fr/ontology#has_collection' => 'coll_id',
            'http://www.pmbservices.fr/ontology#has_subcollection' => 'subcoll_id',
            'http://www.pmbservices.fr/ontology#has_indexint' => 'indexint'
        ));
        return $this->foreign_fields;
    }

    protected function init_linked_entities()
    {
        $this->linked_entities = array_merge(parent::init_linked_entities(), array(
            'http://www.pmbservices.fr/ontology#has_concept' => array(
                'table' => 'index_concept',
                'reference_field_name' => 'num_object',
                'external_field_name' => 'num_concept',
                'other_fields' => array(
                    'type_object' => TYPE_NOTICE
                )
            ),
            'http://www.pmbservices.fr/ontology#has_category' => array(
                'table' => 'notices_categories',
                'reference_field_name' => 'notcateg_notice',
                'external_field_name' => 'num_noeud'
            ),
            'http://www.pmbservices.fr/ontology#has_work' => array(
                'table' => 'notices_titres_uniformes',
                'reference_field_name' => 'ntu_num_notice',
                'external_field_name' => 'ntu_num_tu'
            ),
            'http://www.pmbservices.fr/ontology#publication_language' => array(
                'table' => 'notices_langues',
                'reference_field_name' => 'num_notice',
                'external_field_name' => 'code_langue',
                'other_fields' => array(
                    'type_langue' => '0'
                )
            ),
            'http://www.pmbservices.fr/ontology#original_language' => array(
                'table' => 'notices_langues',
                'reference_field_name' => 'num_notice',
                'external_field_name' => 'code_langue',
                'other_fields' => array(
                    'type_langue' => '1'
                )
            )
        ));
        return $this->linked_entities;
    }

    protected function init_special_fields()
    {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
            'http://www.pmbservices.fr/ontology#has_main_author' => array(
                "method" => array(
                    $this,
                    "insert_responsability"
                ),
                "arguments" => array(
                    0
                )
            ),
            'http://www.pmbservices.fr/ontology#has_other_author' => array(
                "method" => array(
                    $this,
                    "insert_responsability"
                ),
                "arguments" => array(
                    1
                )
            ),
            'http://www.pmbservices.fr/ontology#has_secondary_author' => array(
                "method" => array(
                    $this,
                    "insert_responsability"
                ),
                "arguments" => array(
                    2
                )
            ),
            'http://www.pmbservices.fr/ontology#has_linked_record' => array(
                "method" => array(
                    $this,
                    "insert_linked_record"
                ),
                "arguments" => array()
            ),
            'http://www.pmbservices.fr/ontology#has_date' => array(
                "method" => array(
                    $this,
                    "insert_parution_date"
                ),
                "arguments" => array()
            ),
            'http://www.pmbservices.fr/ontology#has_bulletin' => array(
                "method" => array(
                    $this,
                    "insert_bulletin"
                ),
                "arguments" => array()
            ),
            'http://www.pmbservices.fr/ontology#has_docnum' => array(
                "method" => array(
                    $this,
                    "insert_docnum"
                ),
                "arguments" => array()
            ),
            'http://www.pmbservices.fr/ontology#new_record' => array(
                "method" => array(
                    $this,
                    "set_new_record"
                ),
                "arguments" => array()
            ),
            'http://www.pmbservices.fr/ontology#has_expl' => array(
                "method" => array(
                    $this,
                    "insert_expl"
                ),
                "arguments" => array()
            ),
            'http://www.pmbservices.fr/ontology#thumbnail' => array(
                "method" => array(
                    $this,
                    "insert_thumbnail"
                ),
                "arguments" => array()
            )
        ));
        return $this->special_fields;
    }

    protected function init_cataloging_entities()
    {
        $this->cataloging_entities = array_merge(parent::init_cataloging_entities(), array(
            'http://www.pmbservices.fr/ontology#has_main_author' => array(
                'table' => 'responsability',
                'reference_field_name' => 'responsability_notice',
                'external_field_name' => 'responsability_author',
                'other_fields' => array(
                    'responsability_type' => 0,
                    'responsability_fonction' => '070'
                )
            ),
            'http://www.pmbservices.fr/ontology#has_other_author' => array(
                'table' => 'responsability',
                'reference_field_name' => 'responsability_notice',
                'external_field_name' => 'responsability_author',
                'other_fields' => array(
                    'responsability_type' => 1,
                    'responsability_fonction' => '070'
                )
            ),
            'http://www.pmbservices.fr/ontology#has_secondary_author' => array(
                'table' => 'responsability',
                'reference_field_name' => 'responsability_notice',
                'external_field_name' => 'responsability_author',
                'other_fields' => array(
                    'responsability_type' => 2,
                    'responsability_fonction' => '070'
                )
            )
        ));

        // auth perso
        $entities_linked = onto_pmb_entities_mapping::get_entity_rdf_linked_entities_mapping('record');

        foreach ($entities_linked as $link_name => $entity) {
            if ($entity['type'] == TYPE_AUTHPERSO) {
                $this->cataloging_entities['http://www.pmbservices.fr/ontology#' . $link_name] = array(
                    'table' => 'notices_authperso',
                    'reference_field_name' => 'notice_authperso_notice_num',
                    'external_field_name' => 'notice_authperso_authority_num'
                );
            }
        }
    }

    protected function init_base_query_elements()
    {
        // On définit les valeurs par défaut
        $this->base_query_elements = parent::init_base_query_elements();
        if (!$this->entity_id) {
            $this->base_query_elements = array_merge($this->base_query_elements, array(
                'create_date' => date('Y-m-d H:i:s')
            ));
        }
    }

    protected function post_create($uri)
    {
        global $gestion_acces_active, $gestion_acces_user_notice, $gestion_acces_empr_notice;
        if ($this->integration_type && $this->entity_id) {
            // Audit
            $query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
            $query .= 'values ("' . AUDIT_NOTICE . '", "' . $this->entity_id . '", "' . $this->contributor_id . '", "' . $this->integration_type . '", "' . $this->create_audit_comment($uri) . '", "' . $this->contributor_type . '")';
            pmb_mysql_query($query);
            if ($gestion_acces_active == 1) {
                $ac = new acces();
                // traitement des droits acces user_notice
                if ($gestion_acces_user_notice == 1) {
                    $dom_1 = $ac->setDomain(1);
                    $dom_1->applyRessourceRights($this->entity_id);
                }
                // traitement des droits acces empr_notice
                if ($gestion_acces_empr_notice == 1) {
                    $dom_2 = $ac->setDomain(2);
                    $dom_2->applyRessourceRights($this->entity_id);
                }
            }
            // Indexation
            notice::majNoticesTotal($this->entity_id);
        }
    }

    public function insert_responsability($responsability_type, $values)
    {
        $query = "	DELETE FROM responsability
					WHERE responsability_notice = '" . $this->entity_id . "'
					AND responsability_type = '" . $responsability_type . "'";
        pmb_mysql_query($query);

        foreach ($values as $value) {
            $responsability_function = $this->store->get_property($value["value"], "pmb:author_function");
            $author_uri = $this->store->get_property($value["value"], "pmb:has_author");
            $author = $this->integrate_entity($author_uri[0]['value'], true);

            if (empty($author["id"])) {
                // Id de l'auteur obligatoire
                continue;
            }

            $this->entity_data['children'][] = $author;

            $query = "	INSERT INTO responsability (responsability_author, responsability_notice, responsability_type, responsability_fonction) 
    					VALUES ('" . $author["id"] . "', '" . $this->entity_id . "', $responsability_type, '" . $responsability_function[0]['value'] . "')";
            pmb_mysql_query($query);

            $json_vedette = $this->store->get_property($value["value"], "pmb:author_qualification")[0];
            $vedette_value = json_decode($json_vedette['value']);

            $this->insert_vedette($vedette_value, pmb_mysql_insert_id());
        }
    }

    public function insert_linked_record($values)
    {
        $query = "DELETE FROM notices_relations 
                    WHERE num_notice = '" . $this->entity_id . "' OR linked_notice = '" . $this->entity_id . "'";
        pmb_mysql_query($query);

        foreach ($values as $value) {
            $query = "";

            $record = $this->store->get_property($value["value"], "pmb:has_record");
            $relation_type = $this->store->get_property($value["value"], "pmb:relation_type");
            $record = $this->integrate_entity($record[0]["value"], true);
            $this->entity_data['children'][] = $record;

            $liste_type_relation = notice_relations::get_liste_type_relation();
            $relation_type_value = $relation_type[0]["value"];
            $relation_type = explode("-", $relation_type_value);
            $ranking = notice_relations::get_next_ranking($this->entity_id, $relation_type[1]);
            $id_notice_relation = notice_relations::insert_link($this->entity_id, $record["id"], $relation_type[0]["value"], $ranking, $relation_type[1], 0);
            $add_reverse_link = $this->store->get_property($value["value"], "pmb:add_reverse_link");
            if ($add_reverse_link[0]["value"]) {
                $direction_reverse = $liste_type_relation[$relation_type[1]]->attributes[$relation_type[0]]['REVERSE_DIRECTION'];
                $id_notice_relation_reverse = notice_relations::insert_link($record["id"], $this->entity_id, $relation_type[0]["value"], $ranking, $direction_reverse, $id_notice_relation);
                $query = "UPDATE notices_relations SET num_reverse_link = '" . $id_notice_relation_reverse . "' WHERE id_notices_relations = '" . $id_notice_relation . "'";
                pmb_mysql_query($query);
            }
        }
    }

    public function insert_parution_date($values)
    {
        $date_parution_notice = notice::get_date_parution($values[0]['value']);
        $query = 'update ' . $this->table_name . ' set date_parution = "' . $date_parution_notice . '" where ' . $this->table_key . ' = "' . $this->entity_id . '"';
        pmb_mysql_query($query);
    }

    public function insert_bulletin($values)
    {
        if ($values[0]['value'] && $this->entity_id) {
            if ($values[0]['type'] === 'uri') {
                $bulletin = $this->integrate_entity($values[0]['value'], true);
                $this->entity_data['children'][] = $bulletin;
            }
            if (!empty($bulletin)) {
                $hierarchical_lvl = $this->store->get_property($this->entity_data['uri'], 'pmb:hierarchical_lvl');
                $bibliographical_lvl = $this->store->get_property($this->entity_data['uri'], 'pmb:bibliographical_lvl');
                if ((!empty($hierarchical_lvl[0]['value']) && ($hierarchical_lvl[0]['value'] == '2')) && (!empty($bibliographical_lvl[0]['value']) && ($bibliographical_lvl[0]['value'] == 'a'))) {
                    $query = "insert into analysis (analysis_bulletin, analysis_notice)
					values ('" . $bulletin["id"] . "', '" . $this->entity_id . "')";
                    pmb_mysql_query($query);
                }
            }
        }
    }

    public function insert_docnum($values)
    {
        // TODO : attention on supprime tout, on ne regarde pas du côté composition
        $query = "SELECT explnum_id FROM explnum WHERE explnum_notice = $this->entity_id";
        $result = pmb_mysql_query($query);

        $explnum_ids = array();
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $explnum_ids[] = $row["explnum_id"];
            }
        }

        $docnum_ids = array();
        foreach ($values as $key => $docnum) {
            $docnum_ids[] = $this->store->get_property($docnum["value"], "pmb:identifier")[0]['value'];
        }

        $lenght = count($explnum_ids);
        for ($i = 0; $i < $lenght; $i++) {
            if (!in_array($explnum_ids[$i], $docnum_ids, true)) {
                $explnum = new explnum($explnum_ids[$i]);
                $explnum->delete();
            }
        }

        parent::insert_item($values);
    }

    public function insert_expl($values)
    {
        parent::insert_item($values);
    }

    public function set_new_record($values)
    {
        if ($this->entity_id) {
            $req_new = "select notice_is_new, notice_date_is_new from notices where notice_id=" . $this->entity_id;
            $res_new = pmb_mysql_query($req_new);
            if (pmb_mysql_num_rows($res_new)) {
                if ($r = pmb_mysql_fetch_object($res_new)) {
                    if ($r->notice_is_new != $values[0]['value']) { // pas de changement du flag
                        $query = "UPDATE notices SET notice_is_new = " . $values[0]['value'];
                        if ($values[0]['value']) { // Changement du flag et affecté comme new
                            $query .= ", notice_date_is_new =now() ";
                        } else {
                            $query .= ", notice_date_is_new ='' ";
                        }
                        $query .= " WHERE notice_id=" . $this->entity_id;
                        pmb_mysql_query($query);
                    }
                }
            }
        }
    }

    public function insert_thumbnail($values)
    {
        global $pmb_contribution_opac_docnum_directory;

        // On recupere les donnees de la vignette
        $data = json_decode($values[0]['value']);
        $path = $data->path;

        // On recupere le dossier parametre dans la modelisation
        $upload_directory = new upload_folder($pmb_contribution_opac_docnum_directory);

        // l'endroit ou se trouve le fichier
        $rep_path = $upload_directory->repertoire_path . $path;

        $query = "
            SELECT repertoire_path
            FROM upload_repertoire
            WHERE repertoire_id ='" . thumbnail::get_parameter_img_folder_id() . "'
        ";
        $result = pmb_mysql_query($query);

        if(pmb_mysql_num_rows($result)) {
            $row = pmb_mysql_fetch_object($result);
            $filename_output = $row->repertoire_path . thumbnail::get_img_prefix() . $this->entity_id;
        }

        if (($fp = @fopen($rep_path, "rb")) && $filename_output) {
            $image = "";
            while (!feof($fp)) {
                $image .= fread($fp, 4096);
            }
            fclose($fp);
            if ($img = imagecreatefromstring($image)) {
                $parameter_img_pics_max_size = thumbnail::get_parameter_img_pics_max_size();
                if(!($parameter_img_pics_max_size * 1)) {
                    $parameter_img_pics_max_size = 100;
                }
                $redim = false;
                if (imagesx($img) >= imagesy($img)) {
                    if(imagesx($img) <= $parameter_img_pics_max_size) {
                        $largeur = imagesx($img);
                        $hauteur = imagesy($img);
                    } else {
                        $redim = true;
                        $largeur = $parameter_img_pics_max_size;
                        $hauteur = ($largeur * imagesy($img)) / imagesx($img);
                    }
                } else {
                    if(imagesy($img) <= $parameter_img_pics_max_size) {
                        $hauteur = imagesy($img);
                        $largeur = imagesx($img);
                    } else {
                        $redim = true;
                        $hauteur = $parameter_img_pics_max_size;
                        $largeur = ($hauteur * imagesx($img)) / imagesy($img);
                    }
                }
                if($redim) {
                    $dest = imagecreatetruecolor($largeur, $hauteur);
                    imagecopyresampled($dest, $img, 0, 0, 0, 0, $largeur, $hauteur, imagesx($img), imagesy($img));
                    imagepng($dest, $filename_output);
                    imagedestroy($dest);
                } else {
                    imagepng($img, $filename_output);
                }
                imagedestroy($img);
                $manag_cache = array();
                $manag_cache = getimage_cache($this->entity_id);
                //On détruit l'image si elle est en cache
                global $pmb_img_cache_folder;
                if ($pmb_img_cache_folder) {
                    if($manag_cache["location"] && preg_match("#^".$pmb_img_cache_folder."(.+)$#", $manag_cache["location"])) {
                        unlink($manag_cache["location"]);
                        global $opac_img_cache_folder;
                        if($opac_img_cache_folder && file_exists(str_replace($pmb_img_cache_folder, $opac_img_cache_folder, $manag_cache["location"]))) {
                            unlink(str_replace($pmb_img_cache_folder, $opac_img_cache_folder, $manag_cache["location"]));
                        }
                    }
                }
            }
            unlink($rep_path);
        }
    }
}
