<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_docnum.class.php,v 1.30 2024/03/13 09:57:06 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator.class.php');
require_once($class_path.'/explnum.class.php');
require_once($include_path.'/explnum.inc.php');
require_once($class_path.'/upload_folder.class.php');
require_once($class_path.'/acces.class.php');

class rdf_entities_integrator_docnum extends rdf_entities_integrator
{
    protected $table_name = 'explnum';

    protected $table_key = 'explnum_id';

    protected $ppersos_prefix = 'explnum';

    protected $thumbnail_alreay_update = false;

    protected $notice_id = 0;

    protected $bulletin_id = 0;

    protected function init_map_fields()
    {
        $this->map_fields = array_merge(parent::init_map_fields(), array(
                'http://www.pmbservices.fr/ontology#thumbnail' => 'explnum_vignette',
                'http://www.pmbservices.fr/ontology#label' => 'explnum_nom',
                'http://www.pmbservices.fr/ontology#has_docnum_status' => 'explnum_docnum_statut',
                'http://www.pmbservices.fr/ontology#thumbnail_url' => 'explnum_vignette',
                // Concernant le répertoire d'upload, il existe une méthode (pas sur de comprendre pourquoi), mais on en a besoin maintenant en property de la classe pour Viméo notament
                'http://www.pmbservices.fr/ontology#upload_directory' => 'explnum_repertoire'
        ));
        return $this->map_fields;
    }

    protected function init_foreign_fields()
    {
        $this->foreign_fields = array_merge(parent::init_foreign_fields(), array());
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
                                'type_object' => TYPE_EXPLNUM
                        )
                ),
                'http://www.pmbservices.fr/ontology#location' => array(
                        'table' => 'explnum_location',
                        'reference_field_name' => 'num_explnum',
                        'external_field_name' => 'num_location'
                ),
                'http://www.pmbservices.fr/ontology#owner' => array(
                        'table' => 'explnum_lenders',
                        'reference_field_name' => 'explnum_lender_num_explnum',
                        'external_field_name' => 'explnum_lender_num_lender'
                ),
                'http://www.pmbservices.fr/ontology#licence' => array(
                        'table' => 'explnum_licence_profile_explnums',
                        'reference_field_name' => 'explnum_licence_profile_explnums_explnum_num',
                        'external_field_name' => 'explnum_licence_profile_explnums_profile_num'
                ),
        ));
        return $this->linked_entities;
    }

    protected function init_special_fields()
    {
        $this->special_fields = array_merge(parent::init_special_fields(), array(
                'http://www.pmbservices.fr/ontology#docnum_file' =>  array(
                        "method" => array($this, "insert_docnum_file"),
                        "arguments" => array()
                ),
                'http://www.pmbservices.fr/ontology#upload_directory' => array(
                        "method" => array($this, "set_upload_directory"),
                        "arguments" => array()
                ),
                'http://www.pmbservices.fr/ontology#has_record' => array(
                        "method" => array($this, "insert_record"),
                        "arguments" => array()
                ),
                'http://www.pmbservices.fr/ontology#thumbnail' => array(
                        "method" => array($this, "insert_thumbnail"),
                        "arguments" => array()
                ),
                'http://www.pmbservices.fr/ontology#thumbnail_url' => array(
                        "method" => array($this, "insert_thumbnail_url"),
                        "arguments" => array()
                ),
                'http://www.pmbservices.fr/ontology#has_bulletin' => array(
                        "method" => array($this, "insert_bulletin"),
                        "arguments" => array()
                ),
        ));
        return $this->special_fields;
    }

    public function insert_docnum_file($values)
    {
        $explnum = new \explnum($this->entity_id, $this->notice_id);
        $explnum->get_file_from_contrib($explnum->explnum_nom, $values[0]['value']);
        $explnum->update(false);
    }

    public function set_upload_directory($values)
    {
        $path = '/';
        $upload_directory = $values[0]['value'];

        $slash_pos = strpos($upload_directory, '/');
        // Si il y a un slash dans la valeur, alors c'est un répertoire navigable
        if ($slash_pos !== false) {
            $path = substr($upload_directory, $slash_pos);
            $upload_directory = substr($upload_directory, 0, $slash_pos);
        }

        $query = 'UPDATE explnum SET explnum_repertoire = "'.$upload_directory.'",
				explnum_path = "'.$path.'"
				WHERE explnum_id = '.$this->entity_id;
        pmb_mysql_query($query);
    }

    protected function post_create($uri)
    {
        global $pmb_explnum_controle_doublons, $gestion_acces_active, $gestion_acces_empr_docnum;

        if ($this->entity_id) {
            $query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
            $query .= 'values ("'.AUDIT_EXPLNUM.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.$this->create_audit_comment($uri).'", "'.$this->contributor_type.'")';
            pmb_mysql_query($query);

            $explnum = new explnum($this->entity_id);
            $fullname = '';
            if ($explnum->explnum_path) {
                $up = new upload_folder($explnum->explnum_repertoire);
                $fullname = str_replace("//", "/", $explnum->explnum_rep_path . $explnum->explnum_path . $explnum->explnum_nomfichier);
                $fullname = $up->encoder_chaine($fullname);
            }
            $url = isset($explnum->infos_docnum["url"]) ? $explnum->infos_docnum["url"] : "";
            $contenu_vignette = construire_vignette("", $fullname, $url);
            if ($contenu_vignette && !$this->thumbnail_alreay_update) {
                $req_mime = "update explnum set explnum_vignette='" . addslashes($contenu_vignette) . "' where explnum_id='" . $this->entity_id . "'";
                pmb_mysql_query($req_mime);
            }
            // en cas de lien inverse, le lien vers la notice n'est pas cree
            if (empty($explnum->explnum_notice) && !empty($this->notice_id)) {
                pmb_mysql_query($this->get_update_record_query($this->notice_id));
            }
            $indexation_docnum = new indexation_docnum($this->entity_id);
            $indexation_docnum->indexer();

            if($fullname) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimetype = finfo_file($finfo, $fullname);
                finfo_close($finfo);
                $size = filesize($fullname);
                if(!$mimetype) {
                    $mimetype = "application/data";
                }
                $query = 'UPDATE explnum set explnum_mimetype = "'.$mimetype.'", 
                    explnum_update_date=sysdate(),
                    explnum_file_size = '.intval($size).' ';
                if ($this->integration_type == 1) {
                    $query .= ', explnum_create_date=sysdate() ';
                }
                $query .= 'WHERE explnum_id = '.$this->entity_id;
                pmb_mysql_query($query);
            }

            // On calcule la signature
            pmb_mysql_query("update explnum set explnum_signature='".$explnum->gen_signature()."' where explnum_id=".$this->entity_id);

            // Traitement des droits acces user_docnum
            if ($gestion_acces_active == 1 && $gestion_acces_empr_docnum == 1) {
                $ac = new acces();
                $dom_3 = $ac->setDomain(3);
                $dom_3->applyRessourceRights($this->entity_id);
            }
        }
    }

    public function insert_bulletin($values)
    {
        $i = 0;
        foreach ($values as $value) {
            if ($i == 0 && !empty($this->bulletin_id)) {
                $query = "UPDATE explnum SET explnum_bulletin = " . $this->bulletin_id . " WHERE explnum_id = " . $this->entity_id;
            } else {
                $bulletin = $this->integrate_entity($value["value"], true);
                $this->entity_data["children"][] = $bulletin;

                $this->bulletin_id = $bulletin["id"];
                $query = "UPDATE explnum SET explnum_bulletin = " . $this->bulletin_id . " WHERE explnum_id = " . $this->entity_id;
            }
            pmb_mysql_query($query);
            $i++;
        }
    }

    public function insert_record($values)
    {
        $i = 0;

        foreach ($values as $value) {

            if ($i == 0 && !empty($this->notice_id)) {

                $query = $this->get_update_record_query($this->notice_id);
            } else {
                $record = $this->integrate_entity($value["value"], true);
                $this->entity_data["children"][] = $record;

                $query = $this->get_update_record_query($record["id"]);
            }
            pmb_mysql_query($query);
            $i++;
        }
    }
    
    /**
     * requete de maj de la notice du document
     * @param int $record_id
     * @return string
     */
    private function get_update_record_query($record_id) {
        $bulletin_id = $this->get_bull_id($record_id);
        
        if($bulletin_id !== false) {
            return "UPDATE explnum SET explnum_bulletin = " . $bulletin_id . " WHERE explnum_id = " . $this->entity_id;
        } 
        return "UPDATE explnum SET explnum_notice = " . $record_id . " WHERE explnum_id = " . $this->entity_id;
    }
    /**
     * Méthode retournant l'id de bulletin d'une notice s'il existe, renvoie false sinon
     * @param int $notice_id
     * @return int|boolean
     */
    private function get_bull_id($notice_id)
    {
        $query = "SELECT bulletin_id FROM bulletins WHERE num_notice = ".$notice_id;
        $result = pmb_mysql_query($query);
        if(pmb_mysql_num_rows($result)) {
            $params = pmb_mysql_fetch_object($result);
            return $params->bulletin_id;
        } else {
            return false;
        }
    }

    public function insert_thumbnail($values)
    {

        if ($this->entity_id) {
            $explnum = new \explnum($this->entity_id);
        }

        if ($values[0]["value"]) {
            $value = json_decode($values[0]["value"]);
            $upload_folder = new upload_folder($value->id_upload_directory);
            $filename = $upload_folder->repertoire_path.$value->path;
                $contenu_vignette = construire_vignette("", $filename, "");
        }

        if ($contenu_vignette && $explnum->explnum_id) {
            explnum::upload_thumbnail($contenu_vignette, $this->entity_id);
            $this->thumbnail_alreay_update = true;
        }
    }

    public function insert_thumbnail_url($values)
    {
        global $pmb_contribution_opac_docnum_directory;

        //Cas particulier des vignettes url déjà construite
        $path_to_delete = '';
        if ($values[0]["value"]) {
            $filename = $values[0]["value"];
            $upload_directory = new upload_folder($pmb_contribution_opac_docnum_directory);
            $rep_path = $upload_directory->repertoire_path;
            $path = "/temp/thumbnail/";

            if (is_file($rep_path.$path.$filename)) {
                $values[0]["value"] = $rep_path.$path.$filename;
                $path_to_delete = $rep_path.$path.$filename;
            }
            //Cas générique
            if (false == $this->thumbnail_alreay_update) {
                $contenu_vignette =  construire_vignette("", "", $values[0]["value"]);
            }
        }

        if ($this->entity_id && false == $this->thumbnail_alreay_update) {
            $explnum = new \explnum($this->entity_id);
        }

        if ($contenu_vignette && $explnum->explnum_id && false == $this->thumbnail_alreay_update) {
            $query = "update explnum set explnum_vignette='" . addslashes($contenu_vignette) . "' where explnum_id='" . $explnum->explnum_id . "'";
            pmb_mysql_query($query);
            $this->thumbnail_alreay_update = true;
            if (!empty($path_to_delete)) {
                unlink($path_to_delete);
            }
        }
    }
}
