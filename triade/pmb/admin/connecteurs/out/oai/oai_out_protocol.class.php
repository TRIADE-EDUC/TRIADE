<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: oai_out_protocol.class.php,v 1.35.2.1.2.1 2025/03/12 14:19:30 dgoron Exp $
//There be komodo dragons

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/*
 =========================================================================================================================
 Comment ça marche toutes ces classes?

 Départ
 |
 v
 .----------------------------------------------------------.
 |                      oai_out_server                      |
 |----------------------------------------------------------|-------------.
 | partie connecteur: fait le lien entre toutes les classes |             |
 '----------------------------------------------------------'             |
 |                                                                |
 |                                                                |
 |                                                                |
 v Instancie et utilise                                           |
 .--------------------------------------------------------.               |
 |                    oai_out_protocol                    |               |
 |--------------------------------------------------------|               |
 | Gêre les différents verbes OAI et génêre les pages XML |               |
 '--------------------------------------------------------'               |
 |                                                                |
 |                                                                |
 |                                                                |
 v Utilise                                                        v Instancie
 .--------------------------------------------------.             .-----------------------------------------------.
 |           abstraite:oai_out_get_records          |             |          oai_out_get_records_notice           |
 |--------------------------------------------------|   hérite de |-----------------------------------------------|
 | S'occupe de récupêrer les enregistrements et les |<------------| Gère les infos et les enregistrements pour un |
 | infos relatives au contenu de l'entrepot         |             | entrepot de notices                           |
 '--------------------------------------------------'             '-----------------------------------------------'
 |
 |
 |
 v Instancie et utilise
 .-------------------------------------.                          .------------------------------------------.
 |     external_services_converter     |                          |  external_services_converter_oairecord   |
 |-------------------------------------|               hérite de  |------------------------------------------|
 |.Gère le cache des formats convertis |<-------------------------| S'occupe de convertir les notices        |
 '-------------------------------------'                          | en enregistrements OAI                   |
 '------------------------------------------'

 =========================================================================================================================
 * */


global $class_path, $include_path;
require_once ($class_path . "/connecteurs_out.class.php");
require_once ($include_path . "/connecteurs_out_common.inc.php");
require_once ($class_path . "/connecteurs_out_sets.class.php");
require_once ($class_path . "/external_services_converters.class.php");


//Gestion des dates
/**
 * \brief Gestion simplifiée des dates selon la norme iso8601
 *
 * Conversion réciproque des dates format unix en dates au format iso8601
 * @author Florent TETART
 */
class iso8601
{
    public $granularity; /*!< \brief Granularité courante des dates en format iso8601 : YYYY-MM-DD ou YYYY-MM-DDThh:mm:ssZ */


    /**
     * \brief Constructeur
     *
     * @param string $granularity Granularité des dates manipulées : YYYY-MM-DD ou YYYY-MM-DDThh:mm:ssZ
     */
    public function __construct($granularity="YYYY-MM-DD") {
        $this->granularity=$granularity;
    }


    /**
     * \brief Conversion d'une date unix (nomnbres de secondes depuis le 01/01/1970) en date au format iso8601 selon la granularité
     *
     * @param integer $time : date au format unix (nombres de secondes depuis le 01/01/1970)
     *
     * @return string : date au format YYYY-MM-DD ou YYYY-MM-DDThh:mm:ssZ selon la granularité
     */
    public function unixtime_to_iso8601($time)
    {
        $granularity = str_replace("T", "\\T", $this->granularity);
        $granularity = str_replace("Z", "\\Z", $granularity);
        $granularity = str_replace("YYYY", "Y", $granularity);
        $granularity = str_replace("DD", "d", $granularity);
        $granularity = str_replace("hh", "H", $granularity);
        $granularity = str_replace("mm", "i", $granularity);
        $granularity = str_replace("MM", "m", $granularity);
        $granularity = str_replace("ss", "s", $granularity);
        $date = date($granularity, $time);
        return $date;
    }


    /**
     * \brief Conversion d'une date au format iso8601 en date au format unix (nomnbres de secondes depuis le 01/01/1970) selon la granularité
     *
     * @param string $date : date au format iso8601 YYYY-MM-DD ou YYYY-MM-DDThh:mm:ssZ selon la granularité
     *
     * @return integer : date au format unix (nombres de secondes depuis le 01/01/1970)
     */
    public function iso8601_to_unixtime($date)
    {
        $parts = explode("T", $date);
        if (count($parts) == 2) {
            $day = $parts[0];
            $time = $parts[1];
        } else {
            $day = $parts[0];
        }
        $days = explode("-", $day);
        if ($this->granularity == "YYYY-MM-DDThh:mm:ssZ") {
            if ($time)
                $times = explode(":", $time);
                if ($times[2]) {
                    if (substr($times[2], strlen($times[2]) - 1, 1) == "Z")
                        $times[2] = substr($times[2], 0, strlen($times[2]) - 1);
                }
        }
        $unixtime = mktime((int) $times[0], (int) $times[1], (int) $time[2], (int) $days[1], (int) $days[2], (int) $days[0]);
        return $unixtime;
    }
}


/*
 * oai_out_protocol
 * \brief Cette classe gère toute l'entrée sortie http et le protocol oai
 * Cette classe ne connait pas ses enregistrements ni leur types, elle les récupère grace à une instance d'une classe fille de oai_out_get_records
 * Norme du protocole: http://www.openarchives.org/OAI/openarchivesprotocol.html
 */
class oai_out_protocol
{
    private $msg=array();
    private $repositoryName="";
    private $adminEmail;
    private $sets=array();
    private $repositoryIdentifier="";
    private $oai_out_get_records_object=NULL;
    private $known_metadata_formats=array(
        "pmb_xml_unimarc" => array(
            "metadataPrefix" => "pmb_xml_unimarc",
            "metadataNamespace" => "http://www.pmbservices.fr",
            "schema" => "http://www.pmbservices.fr/notice.xsd"
        ),
        "oai_dc" => array(
            "metadataPrefix" => "oai_dc",
            "metadataNamespace" => "http://www.openarchives.org/OAI/2.0/oai_dc/",
            "schema" => "http://www.openarchives.org/OAI/2.0/oai_dc.xsd"
        )
    );
    private $nb_results=100;
    private $token_life_expectancy=600;
    private $compression=true;
    private $deletion_support="no";
    private $errored=false;
    private $xmlheader_sent=false;
    private $base_url="";


    //Constructeur
    public function __construct(
        $oai_out_get_records_object,
        &$msg,
        $repositoryName,
        $adminEmail,
        $sets,
        $repositoryIdentifier,
        $nb_results,
        $token_life_expectancy,
        $compression,
        $deletion_support,
        $additional_metadataformats,
        $base_url,
        $deletion_transient_duration = 0)
    {
        $this->msg = $msg;
        $this->oai_out_get_records_object = $oai_out_get_records_object;
        $this->repositoryName = $repositoryName;
        $this->adminEmail = $adminEmail;
        $this->sets = $sets;
        $this->repositoryIdentifier = $repositoryIdentifier;
        $this->nb_results = $nb_results;
        $this->token_life_expectancy = $token_life_expectancy;
        $this->compression = $compression;
        $this->deletion_support = $deletion_support;
        $this->known_metadata_formats = array_merge($this->known_metadata_formats, $additional_metadataformats);
        $this->base_url = $base_url;
    }


    //Renvoie l'entête
    public function oai_header()
    {
        global $verb;
        $iso8601 = new iso8601("YYYY-MM-DDThh:mm:ssZ");
        $curdate = $iso8601->unixtime_to_iso8601(time());
        $this->xmlheader_sent = true;
        return '<?xml version="1.0" encoding="UTF-8" ?>
            <?xml-stylesheet type="text/xsl" href="connecteurs/out/oai/oai2.xsl" ?>
            <OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
                <responseDate>'.$curdate.'</responseDate>
                <request '.($this->errored ? '' : 'verb="'.$verb.'"').'>'.XMLEntities($this->base_url).'</request>';
    }


    // Renvoie le pied de page
    public function oai_footer()
    {
        return '</OAI-PMH>';
    }


    // Renvoie une erreur
    public function oai_error($error_code, $error_string)
    {
        global $charset;

        $this->errored = true;
        $buffer = XMLEntities($error_string);
        $buffer = $charset != "utf-8" ? encoding_normalize::utf8_normalize($buffer) : $buffer;
        $result = '<error code="' . XMLEntities($error_code) . '">' . $buffer . '</error>';
        return $result;
    }


    //Renvoie le résultat du verb Identify
    public function oai_identify()
    {
        global $charset;

        $params = array_merge($_GET,$_POST);
        unset($params['verb']);
        unset($params['source_id']);
        unset($params['database']);
        if (count($params)) {
            return $this->oai_error('badArgument', $this->msg['badArgument']);
        }

        $result = '<Identify>';
        $buffer = XMLEntities($this->repositoryName);
        $buffer = $charset != 'utf-8' ? encoding_normalize::utf8_normalize($buffer) : $buffer;
        $result .= '<repositoryName>' . $buffer . '</repositoryName>';
        $result .= '<baseURL>' . XMLEntities($this->base_url) . '</baseURL>';
        $result .= '<protocolVersion>2.0</protocolVersion>';
        $buffer = XMLEntities($this->adminEmail);
        $buffer = $charset != "utf-8" ? encoding_normalize::utf8_normalize($buffer) : $buffer;
        $result .= '<adminEmail>' . $buffer . '</adminEmail>';

        $unix_earliestdate = $this->oai_out_get_records_object->get_earliest_datestamp();
        $iso8601 = new iso8601("YYYY-MM-DDThh:mm:ssZ");
        $earliestdate = $iso8601->unixtime_to_iso8601($unix_earliestdate);
        $result .= '<earliestDatestamp>' . $earliestdate . '</earliestDatestamp>';

        $result .= '<deletedRecord>' . $this->deletion_support . '</deletedRecord>';
        $result .= '<granularity>YYYY-MM-DDThh:mm:ssZ</granularity>';

        $buffer = XMLEntities($this->oai_out_get_records_object->get_sample_oai_identifier());
        $buffer = $charset != "utf-8" ? encoding_normalize::utf8_normalize($buffer) : $buffer;
        $buffer_ri = XMLEntities($this->oai_out_get_records_object->repository_identifier);
        $buffer_ri = $charset != "utf-8" ? encoding_normalize::utf8_normalize($buffer_ri) : $buffer_ri;
        $result .= '<description>
            <oai-identifier xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier http://www.openarchives.org/OAI/2.0/oai-identifier.xsd" xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier">
                <scheme>oai</scheme>
                <repositoryIdentifier>' . $buffer_ri . '</repositoryIdentifier>
                <delimiter>:</delimiter>
                <sampleIdentifier>' . $buffer . '</sampleIdentifier>
            </oai-identifier>
        </description>';
        $result .= "</Identify>";

        return $result;
    }


    //Renvoie le résultat du verb ListSets
    public function oai_list_sets()
    {
        global $charset;
        $result = '';
        $result .= '<ListSets>';
        foreach ($this->sets as $aset) {
            $buffer = XMLEntities($aset["caption"]);
            $buffer = $charset != "utf-8" ? encoding_normalize::utf8_normalize($buffer) : $buffer;

            $result .= '<set>
                <setSpec>set_' . XMLEntities($aset["id"]) . '</setSpec>
                <setName>' . $buffer . '</setName>
            </set>';
        }
        $result .= '</ListSets>';
        return $result;
    }


    // Renvoie le résultat du verb ListRecords
    public function oai_list_records($root_tag = 'ListRecords')
    {
        global $set, $resumptionToken, $from, $until;
        global $metadataPrefix;

        // pour compatibilité avec l'ancien fonctionnement
        $metadataPrefix = str_replace('convert:', 'convert_', $metadataPrefix);

        // Vérifications des parametres passes
        $params = array_merge($_GET, $_POST);
        unset($params['verb']);
        unset($params['source_id']);
        unset($params['database']);

        // resumptionToken is an exclusive argument
        // http://www.openarchives.org/OAI/2.0/openarchivesprotocol.htm#ListIdentifiers
        if (isset($params['resumptionToken'])) {
            unset($params['resumptionToken']);
            if (count($params)) {
                $error = $this->oai_error('badArgument', $this->msg['badArgument']);
                $error .= $this->oai_error('badResumptionToken', $this->msg['badResumptionToken']);
                return $error;
            }
        } else if (! isset($params['metadataPrefix'])) {
            $error = $this->oai_error('badArgument', $this->msg['badArgument']);
            return $error;
        }

        if (! $metadataPrefix) {
            $metadataPrefix = 'oai_dc';
        }
        if ((substr($metadataPrefix, 0, 8) != 'convert_') && ! in_array($metadataPrefix, array_keys($this->known_metadata_formats))) {
            return $this->oai_error('cannotDisseminateFormat', sprintf($this->msg['cannotDisseminateFormat'], XMLEntities($metadataPrefix)));
        }
        if ($root_tag == 'ListIdentifiers') {
            $metadataPrefix = '__oai_identifier';
        }

        // Un peu de ménage dans les tokens
        $sql = "DELETE FROM connectors_out_oai_tokens WHERE NOW() >= connectors_out_oai_token_expirationdate";
        pmb_mysql_query($sql);

        // On aura besoin d'un objet date iso magique
        $iso8601 = new iso8601("YYYY-MM-DDThh:mm:ssZ");

        $result = "";

        $max_records = $this->nb_results;
        $total_number_of_records = 0;
        $datefrom = false;
        $dateuntil = false;
        $cursor = 0;

        // Un token? Cherchons le dans la base de donnée et restaurons son environnement
        if ($resumptionToken) {

            $sql = "SELECT connectors_out_oai_token_environnement FROM connectors_out_oai_tokens WHERE connectors_out_oai_token_token = '" . addslashes($resumptionToken) . "'";
            $res = pmb_mysql_query($sql);
            if (! pmb_mysql_num_rows($res)) {
                return $this->oai_error('badResumptionToken', $this->msg['badResumptionToken']);
            }
            $row = pmb_mysql_fetch_assoc($res);
            $config = unserialize($row['connectors_out_oai_token_environnement']);
            $set_id_list = $config["sets"];
            $datefrom = $config["datefrom"];
            $dateuntil = $config["dateuntil"];
            $metadataPrefix = $config["metadataprefix"];
            $cursor = ! empty($config["cursor"]) ? $config['cursor'] : 0;

        } else {

            // Sinon config de début de recherche
            // Vérifions si on souhaite un set précis
            $error = false;
            if (! empty($set)) {
                $the_set_id = substr($set, 4);
                // On a un id, vérifions qu'il existe dans la liste
                $found = false;
                foreach ($this->sets as $aset) {
                    if ($aset['id'] == $the_set_id) {
                        $found = true;
                        break;
                    }
                }
                // Non? Erreur!
                if (! $found) {
                    $error = $this->oai_error('noRecordsMatch', $this->msg['noRecordsMatch']);
                    $error .= $this->oai_error('noSetHierarchy', $this->msg['noSetHierarchy']);
                } else {
                    // Oui? On génère la "liste" des sets
                    $set_id_list = array(
                        0 => array(
                            'id' => $the_set_id
                        )
                    );
                }
            } else {
                // Sinon on fouille dans tous les sets
                $set_id_list = $this->sets;
            }
            if ($error) {
                return $error;
            }

            $from_format = 0;
            if (! empty($from)) {
                if (preg_match("#^\d{4}-\d{2}-\d{2}$#", $from)) {
                    $from_format = 1;
                } else if (preg_match("#^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$#", $from)) {
                    $from_format = 2;
                }
                if (! $from_format) {
                    $error = $this->oai_error('badArgument', $this->msg['badArgument']);
                    return $error;
                }
                $datefrom = $iso8601->iso8601_to_unixtime($from);
                if ($datefrom < 0)
                    $datefrom = 0;
            }
            $until_format = 0;
            if (! empty($until)) {
                if (preg_match("#^\d{4}-\d{2}-\d{2}$#", $until)) {
                    $until_format = 1;
                } else if (preg_match("#^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$#", $until)) {
                    $until_format = 2;
                }
                if (! $until_format) {
                    $error = $this->oai_error('badArgument', $this->msg['badArgument']);
                    return $error;
                }
                $dateuntil = $iso8601->iso8601_to_unixtime($until);
                if ($dateuntil < 0)
                    $dateuntil = 0;
            }

            if ($from_format && $until_format && ($from_format != $until_format)) {
                $error = $this->oai_error('badArgument', $this->msg['badArgument']);
                return $error;
            }
        }

        // Recuperation des Ids de la source
        $content = $this->oai_out_get_records_object->getRecordIds($set_id_list, $datefrom, $dateuntil);

        //Comptage des ids
        $nb_ids = 0;
        foreach ($content['sets'] as $set_id => &$set) {
            $nb_ids = $nb_ids + count($set['ids']) + count($set['deleted_ids']);
        }

        // Si le curseur est different de zero, on retire le nb d'Ids correspondants du resultat
        if( $cursor > 0 ) {
            $i = $cursor;
            foreach ($content['sets'] as $set_id => &$set) {
                while (count($set['ids']) && $i > 0) {
                    array_shift($set['ids']);
                    $i--;
                }
                while (count($set['deleted_ids']) && $i > 0) {
                    array_shift($set['deleted_ids']);
                    $i--;
                }
            }
        }

        // Liste des ids a convertir
        $ids_to_convert = [];
        $deleted_ids_to_convert = [];
        $computed_ids = [];
        foreach ($content['sets'] as $set_id => &$set) {
            while (count($set['ids']) && count($computed_ids) < $max_records) {
                $id = array_shift($set['ids']);
                if (! in_array($id, $computed_ids)) {
                    $computed_ids[] = $id;
                    $ids_to_convert[$set_id][] = $id;
                }
            }
            while (count($set['deleted_ids']) && count($computed_ids) < $max_records) {
                $id = array_shift($set['deleted_ids']);
                if (! in_array($id, $computed_ids)) {
                    $computed_ids[] = $id;
                    $deleted_ids_to_convert[$set_id][] = $id;
                }
            }
        }

        // Si pas d'enregistrement, le protocole veut qu'on renvoie une erreur
        if (! count($computed_ids)) {
            return $this->oai_error('noRecordsMatch', $this->msg['noRecordsMatch']);
        }

        //Calcul du nb d'ids restant a traiter
        $nb_remaining_ids = 0;
        foreach ($content['sets'] as $set_id => &$set) {
            $nb_remaining_ids += count($set['ids']) + count($set['deleted_ids']);
        }

        // file_put_contents("/tmp/oai.log", PHP_EOL.">> computed_ids = ".print_r($computed_ids, true), FILE_APPEND);
        // file_put_contents("/tmp/oai.log", PHP_EOL . ">> ids_to_convert = " . print_r($ids_to_convert, true), FILE_APPEND);
        // file_put_contents("/tmp/oai.log", PHP_EOL . ">> deleted_ids_to_convert = " . print_r($deleted_ids_to_convert, true), FILE_APPEND);
        // file_put_contents("/tmp/oai.log", PHP_EOL . ">> nb_ids = " . $nb_ids, FILE_APPEND);
        // file_put_contents("/tmp/oai.log", PHP_EOL . ">> nb_computed_ids = " . count($computed_ids), FILE_APPEND);
        // file_put_contents("/tmp/oai.log", PHP_EOL . ">> nb_remaining_ids = " . $nb_remaining_ids, FILE_APPEND);
        // file_put_contents("/tmp/oai.log", PHP_EOL.">> remaining content = ".print_r($content, true), FILE_APPEND);

        // Traitement des enregistrements
        $converted_records = [];
        foreach ($ids_to_convert as $set_id => &$ids) {
            $converted_records[$set_id] = $this->oai_out_get_records_object->getRecordsBySet($set_id, $metadataPrefix, $datefrom, $dateuntil, $ids);
        }

        // Traitement des enregistrements supprimes
        $converted_deleted_records = [];
        foreach ($deleted_ids_to_convert as $set_id => &$ids) {
            $converted_deleted_records = $this->oai_out_get_records_object->getDeletedRecords($ids);
        }

        // Calcul environnement + token
        $resumption_token = '';
        if($nb_remaining_ids) {
            $environment = [
                "sets" => $set_id_list,
                "datefrom" => $datefrom,
                "dateuntil" => $dateuntil,
                "metadataprefix" => $metadataPrefix,
                "cursor" => $nb_ids - $nb_remaining_ids,
            ];
            // file_put_contents("/tmp/oai.log", PHP_EOL.">> environment = ".print_r($environment, true), FILE_APPEND);

            $token = md5(microtime());
            // file_put_contents("/tmp/oai.log", "token = ".$token.PHP_EOL, FILE_APPEND);
            $token_expiration_date = time() + $this->token_life_expectancy;

            $query = "INSERT INTO connectors_out_oai_tokens (connectors_out_oai_token_token, connectors_out_oai_token_environnement, connectors_out_oai_token_expirationdate) ";
            $query.= "VALUES ('".$token."', '".addslashes(serialize($environment))."', NOW() + INTERVAL ".$this->token_life_expectancy." SECOND)";
            pmb_mysql_query($query);

            $resumption_token = '<resumptionToken expirationDate="'.$iso8601->unixtime_to_iso8601($token_expiration_date).'" completeListSize="'.$total_number_of_records.'" cursor="'.$nb_remaining_ids.'">'.$token.'</resumptionToken>';
        }

        // Affichage
        $result .= " <" . $root_tag . ">";
        foreach ($converted_records as $set_id => $records) {
            foreach ($records as $record) {
                $result .= $record;
            }
        }
        foreach ($converted_deleted_records as $record) {
            $result .= $record;
        }
        $result.= $resumption_token;
        $result .= " </" . $root_tag . ">";

        // c'est moche pour l'instant, mais c'est validé pour Florent
        // pour les conversions persos, il faut le faire dans la feuille xslt
        if ($metadataPrefix == 'pmb_xml_unimarc' && $this->oai_out_get_records_object->oai_pmh_valid) {
            $result = str_replace('<notice>',
                '<notice xmlns="http://www.pmbservices.fr" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.pmbservices.fr http://www.pmbservices.fr/notice.xsd">', $result);
        }

        return $result;
    }


    public function oai_list_identifier()
    {
        return $this->oai_list_records('ListIdentifiers');
    }


    public function oai_get_record()
    {
        global $identifier;
        global $metadataPrefix;

        // pour compatibilité avec l'ancien fonctionnement
        $metadataPrefix = str_replace('convert:', 'convert_', $metadataPrefix);

        if (! $metadataPrefix) {
            return $this->oai_error("badArgument", $this->msg['badArgument']);
        } elseif ((substr($metadataPrefix, 0, 8) != 'convert_') && ! in_array($metadataPrefix, array_keys($this->known_metadata_formats))) {
            return $this->oai_error('cannotDisseminateFormat', sprintf($this->msg['cannotDisseminateFormat'], XMLEntities($metadataPrefix)));
        }

        if (! $identifier) {
            return $this->oai_error("badArgument", $this->msg['badArgument']);
        }

        $record = $this->oai_out_get_records_object->get_record($identifier, $metadataPrefix);
        if ($record === false) {
            return $this->oai_error("idDoesNotExist", $this->msg['idDoesNotExist']);
        }

        // c'est moche pour l'instant, mais c'est validé pour Florent
        // pour les conversions persos, il faut le faire dans la feuille xslt
        if ($metadataPrefix == 'pmb_xml_unimarc' && $this->oai_out_get_records_object->oai_pmh_valid) {
            $record = str_replace('<notice>',
                '<notice xmlns="http://www.pmbservices.fr" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.pmbservices.fr http://www.pmbservices.fr/notice.xsd">', $record);
        }

        $result = "<GetRecord>";
        $result .= $record;
        $result .= "</GetRecord>";
        return $result;
    }


    // Renvoie le résultat du verb ListMetadataFormat
    public function oai_list_metadata_formats()
    {
        // Vérifications des parametres passes
        $params = array_merge($_GET, $_POST);
        $identifier = 0;
        unset($params['verb']);
        unset($params['source_id']);
        unset($params['database']);
        if (isset($params['identifier'])) {
            $identifier = $params['identifier'];
            unset($params['identifier']);
        }
        if (count($params)) {
            $error = $this->oai_error('badArgument', $this->msg['badArgument']);
            return $error;
        }

        if (($identifier) && ($this->oai_out_get_records_object->get_record($identifier, 'oai_dc') == false)) {
            return $this->oai_error('idDoesNotExist', $this->msg['idDoesNotExist']);
        }

        //Sinon, il faut retourner la liste des ListMetadataFormats
        $result = '<ListMetadataFormats>';
        foreach ($this->known_metadata_formats as $aformat) {
            $result .= '<metadataFormat>
                <metadataPrefix>' . $aformat['metadataPrefix'] . '</metadataPrefix>
                <schema>' . $aformat['schema'] . '</schema>
                <metadataNamespace>' . $aformat['metadataNamespace'] . '</metadataNamespace>
            </metadataFormat>';
        }
        $result .= '</ListMetadataFormats>';

        return $result;
    }

    public function sets_being_refreshed()
    {
        global $set;
        global $verb;

        $set_id_list = array();
        // dans certains cas, cela n'a pas d'importance...
        switch ($verb) {
            case 'ListRecords':
            case 'GetRecord':
                // Vérifions si on souhaite un set précis
                if (isset($set) && $set) {
                    $the_set_id = substr($set, 4);
                    // On a un id, vérifions qu'il existe dans la liste
                    $found = false;
                    foreach ($this->sets as $aset) {
                        if ($aset["id"] == $the_set_id) {
                            $found = true;
                            break;
                        }
                    }
                    // Non? Erreur!
                    if ($found) {
                        $set_id_list = array(
                            $the_set_id
                        );
                    }
                } else {
                    $set_id_list = array();
                    foreach ($this->sets as $aset) {
                        $set_id_list[] = $aset['id'];
                    }
                }
                $query = "select being_refreshed from connectors_out_sets where being_refreshed=1 and connector_out_set_id in (" . implode(",", $set_id_list) . ")";
                $res = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($res) > 0) {
                    $result = true;
                } else {
                    $result = false;
                }
                break;
            default:
                $result = false;
                break;
        }
        return $result;
    }
}


/*
 * oai_out_get_records
 * \brief Cette classe utilisée par la classe oai_out_protocol permet de récupérer les metadatas des enregistrements (en UTF-8)
 *
 */
abstract class oai_out_get_records
{

    public $error_code = "";

    public $error_string = "";

    private $msg = [];

    protected $total_record_count_per_set = [];

    protected static $deleted_records = array();

    // Constructeur
    public function __construct(&$msg)
    {
        $this->msg = $msg;
    }

    // Renvoie un exemple d'identifier
    abstract public function get_sample_oai_identifier();

    // Renvoie le datestamp du plus vieil enregistrement
    abstract public function get_earliest_datestamp();

    // Retourne le nombre d'enregistrements
    abstract public function get_record_count($set_id, $datefrom = false, $dateuntil = false);

    // Retrouve un enregistrement
    abstract public function get_record($rec_id, $format);

    // Liste les enregistrements
    abstract public function get_records($set_id = "", $format = 'oai_dc', $first = false, $count = false, $datefrom = false, $dateuntil = false);

    public static function set_deleted_records($deleted_records = array())
    {
        static::$deleted_records = $deleted_records;
    }

    public static function get_deleted_records()
    {
        return static::$deleted_records;
    }
}


/*
 * oai_out_get_records_notice
 * \brief Cette classe récupère les enregistrements pour un entrepot oai de notices
 *
 */
class oai_out_get_records_notice extends oai_out_get_records
{

    // Duree du cache en secondes
    public $oai_cache_duration = 86400;

    // Tableau d'ids des sets inclus dans l'entrepot
    public $source_set_ids = [];

    // Id de l'entrepot OAI
    public $repository_identifier = "";

    // Id de statut des notices supprimees
    public $notice_statut_deletion = 0;

    // Inclure exemplaires
    public $include_items = 0;

    // Inclure liens
    public $include_links = ['genere_lien'=>0];

    // Feuille xslt
    protected $xslt = "";

    // Gestion des suppressions (0: non, 1: temporaire (transient), 2: permanent)
    public $deletion_management = 0;

    // Duree de conservation des enregistrements supprimes en mode temporaire
    public $deletion_management_transient_duration = 0;

    // Utilisation de la date de modification des exemplaires 0: non, 1:oui
    public $use_items_update_date = 0;

    // Nettoyage html 0: non, 1:oui
    public $clean_html = 0;

    // Nb total d'enregistrements par set
    protected $total_record_count = [];

    // Donnees collectees
    protected $data = [];
    
    // Date de création ou date de mise à jour dans le datestamp
    public $header_datestamp = 'update_date';

    // Constructeur
    public function __construct(&$msg, $xslt = "")
    {
        parent::__construct($msg);
        $this->xslt = $xslt;
    }

    public function get_sample_oai_identifier()
    {
        $result = 123456789;
        // Allons chercher un notice_id dans un set
        foreach ($this->source_set_ids as $asetid) {
            $co_set = $this->initializeSet($asetid);
            $values = $co_set->get_values();
            // Set vide? On cherche dans un autre
            if (! $values)
                continue;
                // On en a un? On le prend
                $result = $values[0];
                break;
        }
        $result = "oai:" . $this->repository_identifier . ":" . $result;
        return $result;
    }

    /**
     * Recupere la date de mise a jour la plus ancienne definie dans les sets
     *
     * {@inheritdoc}
     * @see oai_out_get_records::get_earliest_datestamp()
     *
     * @return int : timestamp
     */
    public function get_earliest_datestamp()
    {
        $current_min_unix_timestamp = time();
        foreach ($this->source_set_ids as $asetid) {
            $co_set = $this->initializeSet($asetid);
            if ($this->header_datestamp == 'create_date') {
                $set_min_date = $co_set->get_earliest_createdate();
            } else {
                $set_min_date = $co_set->get_earliest_updatedate();
            }
            $current_min_unix_timestamp = $set_min_date < $current_min_unix_timestamp ? $set_min_date : $current_min_unix_timestamp;
        }
        return $current_min_unix_timestamp;
    }

    public function get_record($rec_id, $format)
    {
        // Extrayons l'id de la notice
        $notice_id = substr(strrchr($rec_id, ":"), 1);
        if (! $notice_id)
            return false;

            // Vérifions que la notice est bien dans les sets de la source
            $notice_sets = connector_out_set_noticecaddie::get_notice_setlist($notice_id);
            $notice_sets = array_intersect($notice_sets, $this->source_set_ids);
            if (! $notice_sets) {
                return false;
            }
            $co_set = $this->initializeSet($notice_sets[0]);
            $oai_cache = new external_services_converter_oairecord(1, $this->oai_cache_duration, $co_set->cache->cache_duration_in_seconds(), $this->source_set_ids, $this->repository_identifier, $this->notice_statut_deletion, $this->include_items, $this->xslt, $this->include_links, $this->deletion_management, $this->deletion_management_transient_duration);
            $oai_cache->set_clean_html($this->clean_html);
            $oai_cache->set_header_datestamp($this->header_datestamp);
            $records = $oai_cache->convert_batch(array($notice_id), $format, 'utf-8');

            return $records ? $records[$notice_id] : false;
    }

    public function get_records($set_id = 0, $format = 'oai_dc', $first = false, $count = false, $datefrom = false, $dateuntil = false)
    {
        // Récupérons du cache les ids des notices
        $co_set = $this->initializeSet($set_id);

        // la méthode update_if_expired renvoie toutes les notices en cache
        // il faut donc ré-initialiser si des critères de date sont présents
        if ($datefrom || $dateuntil) {
            $co_set->cache->values = array();
        }
        $notice_ids = $co_set->get_values($first, $count, $datefrom, $dateuntil, $this->use_items_update_date);

        $parametres = array();
        if (! empty($this->include_links) && is_array($this->include_links)) {
            $parametres = $this->include_links;
        }

        // On recupere les notices liees en fonction des parametres
        $notice_list = export::get_list_notice_id($notice_ids, $parametres);
        if (! empty($notice_list)) {
            $notice_ids = $notice_list;
        }

        $this->total_record_count[$set_id] = $co_set->get_value_count($datefrom, $dateuntil, $this->use_items_update_date);

        // Récupérons les enregistrements (avec gestion du cache)
        $oai_cache = new external_services_converter_oairecord(1, $this->oai_cache_duration, $co_set->cache->cache_duration_in_seconds(), $this->source_set_ids, $this->repository_identifier, $this->notice_statut_deletion, $this->include_items, $this->xslt, $this->include_links, $this->deletion_management, $this->deletion_management_transient_duration);
        $oai_cache->set_date_from($datefrom);
        $oai_cache->set_date_until($dateuntil);
        $oai_cache->set_clean_html($this->clean_html);
        $oai_cache->set_header_datestamp($this->header_datestamp);
        $records = $oai_cache->convert_batch($notice_ids, $format, 'utf-8');

        return $records;
    }


    /**
     * Recupere le nb d'enregistrements d'un set
     *
     * WARNING : Ne tient pas compte des notices liees et des notices supprimees
     *
     * @param int $set_id
     * @param int $datefrom: unixtime
     * @param int $dateuntil : unixtime
     *
     * @return int
     */
    public function get_record_count($set_id, $datefrom = false, $dateuntil = false)
    {
        if (! isset($this->total_record_count[$set_id])) {
            $co_set = $this->initializeSet($set_id);
            $this->total_record_count[$set_id] = $co_set->get_value_count($datefrom, $dateuntil, $this->use_items_update_date);
        }
        return $this->total_record_count[$set_id];
    }


    /**
     * Recupere les ids des enregistrements d'une source
     *
     * @param [id=>int, caption=>string]] $set_list : liste de sets
     * @param int $datefrom : unixtime
     * @param int $dateuntil : unixtime
     *
     * @return [
     *     'sets' => [
     *         'set_id' => [
     *             'ids' => [],
     *             'deleted_ids => [],
     *     ]
     * ]
     */
    public function getRecordIds($set_list = [], $datefrom = 0, $dateuntil = 0)
    {
        $tmp_set_list = (is_array($set_list)) ? $set_list : [];
        $set_list = [];
        foreach ($tmp_set_list as $set) {
            $set_id = (empty($set['id'])) ? 0 : intval($set['id']);
            $set_caption = (empty($set['caption']) || ! is_string($set['caption'])) ? '' : $set['caption'];
            if ($set['id']) {
                $set_list[] = [
                    'id' => $set_id,
                    'caption' => $set_caption,
                    'ids' => [],
                    'deleted_ids' => []
                ];
            }
        }
        unset($tmp_set_list);
        if (empty($set_list)) {
            return [];
        }
        $datefrom = intval($datefrom);
        $dateuntil = intval($dateuntil);
        $content = [
            'sets' => [],
            'ids' => [],
            'deleted_ids' => []
        ];

        // Parcours des sets
        foreach ($set_list as &$set) {

            $ids = [];
            $deleted_ids = [];

            $this->data['sets'][$set['id']] = [];
            // $this->data['sets'][$set['id']]['caption'] = $set['caption'];

            // Recupere les ids de notices du set
            $ids = $this->getRecordIdsBySet($set['id'], $datefrom, $dateuntil);
            $this->updateSetsByIdList($set['id'], $ids);

            // Recupere les ids des notices supprimees du set
            $deleted_ids = $this->getDeletedRecordIdsBySet($set['id'], $datefrom, $dateuntil);
            $this->updateSetsByIdList($set['id'], $deleted_ids);

            // Retire les ids des notices supprimees des ids des notices du set
            $ids = array_values(array_diff($ids, $deleted_ids));

            // Retire les ids deja presents dans le resultat final
            $ids = array_values(array_diff($ids, $content['ids']));
            $deleted_ids = array_values(array_diff($deleted_ids, $content['deleted_ids']));

            // Met a jour le resultat final
            $content['sets'][$set['id']]['ids'] = $ids;
            // $content['sets'][$set['id']]['caption'] = $set['caption'];
            $content['sets'][$set['id']]['deleted_ids'] = $deleted_ids;

            $content['ids'] = array_merge($content['ids'], $ids);
            $content['deleted_ids'] = array_merge($content['deleted_ids'], $deleted_ids);
        }
        // $content['sets_by_id'] = $this->data['sets_by_id'];
        unset($content['ids']);
        unset($content['deleted_ids']);
        return $content;
    }


    /**
     * Met a jour la liste des sets / id de notice
     *
     * @param int $set
     * @param [] $record_ids
     */
    protected function updateSetsByIdList($set_id, $record_ids = [])
    {
        if(is_countable($record_ids)) {
            foreach ($record_ids as $id) {
                if ( (! isset($this->data['sets_by_id'][$id])) || (! in_array($set_id, $this->data['sets_by_id'][$id])) ) {
                    $this->data['sets_by_id'][$id][] = $set_id;
                }
            }
        }
    }


    /**
     * Recupere les ids des enregistrements d'un set
     *
     * WARNING : Ne tient pas compte des notices supprimees
     *
     * @param int $set_id
     * @param int $datefrom : unixtime
     * @param int $dateuntil : unixtime
     *
     * @return [int]
     */
    protected function getRecordIdsBySet($set_id, $datefrom = false, $dateuntil = false)
    {
        global $base_path;

        if (! isset($this->data[$set_id]['ids'])) {

            $co_set = $this->initializeSet($set_id);
            $ids = $co_set->get_values(false, false, $datefrom, $dateuntil, $this->use_items_update_date);

            // Recupere les notices liees
            $parametres = [];
            if (! empty($this->include_links) && is_array($this->include_links)) {
                $parametres = $this->include_links;
            }

            // Mise en cache des identifiants de notices liées
            $key = "oai_set_linked_ids_" . $set_id;
            $cache_php = cache_factory::getCache();
            if($cache_php !== false) {
                $linked_ids = $cache_php->getFromCache($key);

                if(empty($linked_ids)) {
                    $linked_ids = export::get_list_notice_id($ids, $parametres);
                    $cache_php->setInCache($key, $linked_ids, $this->oai_cache_duration);
                }

            } else {
                $cache_linked_ids_path = $base_path . '/temp/' . $key . '.txt';
                if(file_exists($cache_linked_ids_path)) {
                    if ((time() - filemtime($cache_linked_ids_path)) > $this->oai_cache_duration) {
                        unlink($cache_linked_ids_path);
                    }
                }

                if(file_exists($cache_linked_ids_path)) {
                    $linked_ids = explode(",", file_get_contents($cache_linked_ids_path));
                } else {
                    $linked_ids = export::get_list_notice_id($ids, $parametres);
                    file_put_contents($cache_linked_ids_path, implode(",", $linked_ids), FILE_APPEND);
                }
            }

            if (! empty($linked_ids)) {
                $ids = array_values(array_unique(array_merge($ids, $linked_ids)));
            }

            $this->data[$set_id]['ids'] = $ids;
        }

        return $this->data[$set_id]['ids'];
    }


    /**
     * Recupere les ids des enregistrements supprimes d'un set
     *
     * @param int $set_id
     *
     * @return [int]
     */
    protected function getDeletedRecordIdsBySet($set_id, $datefrom = 0, $dateuntil = 0)
    {
        $set_id = intval($set_id);
        if (! $set_id) {
            return [];
        }
        $datefrom = intval($datefrom);
        $dateuntil = intval($dateuntil);

        if (! isset($this->data[$set_id]['deleted_ids'])) {

            $deleted_ids = [];

            switch (true) {

                // Gestion de suppression liee a un statut de notice
                case (0 !== $this->notice_statut_deletion):

                    if (! empty($this->data[$set_id]['ids'])) {
                        $query = "select notice_id from notices where statut = " . $this->notice_statut_deletion . " ";
                        $query .= "and notice_id in (" . implode(',', $this->data[$set_id]['ids']) . ") ";
                        if ($datefrom) {
                            $query .= " and update_date > FROM_UNIXTIME(" . $datefrom . ")";
                        }
                        if ($dateuntil) {
                            $query .= " and update_date < FROM_UNIXTIME(" . $dateuntil . ")";
                        }
                        $result = pmb_mysql_query($query);
                        if (pmb_mysql_num_rows($result)) {
                            while ($row = pmb_mysql_fetch_assoc($result)) {
                                $deleted_ids[] = $row['notice_id'];
                            }
                        }
                    }
                    break;

                    // Gestion de suppression temporaire (transient)
                case ((1 == $this->deletion_management) && (0 !== $this->deletion_management_transient_duration)):

                    $query = "select num_notice as notice_id from connectors_out_oai_deleted_records ";
                    $query .= "where num_set = " . $set_id . " ";
                    if ($datefrom) {
                        $query .= " and deletion_date > FROM_UNIXTIME(" . $datefrom . ")";
                    }
                    if ($dateuntil) {
                        $query .= " and deletion_date < FROM_UNIXTIME(" . $dateuntil . ")";
                    }
                    $query .= " and timestampdiff(second, deletion_date, now()) < " . $this->deletion_management_transient_duration;

                    $result = pmb_mysql_query($query);
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_assoc($result)) {
                            $deleted_ids[] = $row['notice_id'];
                        }
                    }
                    break;

                    // Gestion de suppression permanente
                case (2 == $this->deletion_management):

                    $query = "select num_notice as notice_id from connectors_out_oai_deleted_records ";
                    $query .= "where num_set = " . $set_id . " ";
                    if ($datefrom) {
                        $query .= " and deletion_date > FROM_UNIXTIME(" . $datefrom . ")";
                    }
                    if ($dateuntil) {
                        $query .= " and deletion_date < FROM_UNIXTIME(" . $dateuntil . ")";
                    }

                    $result = pmb_mysql_query($query);
                    if (pmb_mysql_num_rows($result)) {
                        while ($row = pmb_mysql_fetch_assoc($result)) {
                            $deleted_ids[] = $row['notice_id'];
                        }
                    }
                    break;

                    // Pas de gestion des enregistrements supprimes
                default:
                case (0 == $this->deletion_management):
                    break;
            }

            $this->data[$set_id]['deleted_ids'] = $deleted_ids;
        }
        return $this->data[$set_id]['deleted_ids'];
    }

    /**
     * Convertit les ids selon le format defini
     *
     * @param int $set_id
     * @param string $format
     * @param int $datefrom : unixtime
     * @param int $dateuntil : unixtime
     * @param array $record_ids : ids a convertir
     *
     * @return []
     */
    public function getRecordsBySet($set_id = 0, $format = 'oai_dc', $datefrom = 0, $dateuntil = 0, $record_ids = [])
    {
        $co_set = $this->initializeSet($set_id, false);
        // Récupérons les enregistrements (avec gestion du cache)
        $oai_cache = new external_services_converter_oairecord(1, $this->oai_cache_duration, $co_set->cache->cache_duration_in_seconds(), $this->source_set_ids, $this->repository_identifier,
            $this->notice_statut_deletion, $this->include_items, $this->xslt, $this->include_links, $this->deletion_management, $this->deletion_management_transient_duration);
        $oai_cache->set_date_from($datefrom);
        $oai_cache->set_date_until($dateuntil);
        $oai_cache->set_clean_html($this->clean_html);
        $oai_cache->set_header_datestamp($this->header_datestamp);
        $records = $oai_cache->convert_batch($record_ids, $format, 'utf-8');
        return $records;
    }


    /**
     * Convertit les ids des enregistrements supprimes
     *
     * @param [$record_ids] : ids a convertir
     * @return []
     */
    public function getDeletedRecords($record_ids = [])
    {
        if( !is_array($record_ids) ) {
            return [];
        }
        array_walk($record_ids, function(&$a) {$a = intval($a);});
        if(empty($record_ids)) {
            return [];
        }

        //Recuperation dates de suppression
        $datestamps = [];
        $query = '';
        switch(true) {

            // Gestion de suppression liee a un statut de notice
            case ( 0 !== $this->notice_statut_deletion ) :
                if ($this->header_datestamp == 'create_date') {
                    $query = "SELECT notice_id, UNIX_TIMESTAMP(create_date) AS datestamp FROM notices WHERE notice_id IN (".implode(",", $record_ids).")";
                } else {
                    $query = "SELECT notice_id, UNIX_TIMESTAMP(update_date) AS datestamp FROM notices WHERE notice_id IN (".implode(",", $record_ids).")";
                }
                break;

                // Gestion de suppression temporaire (transient)
            case ( (1 == $this->deletion_management) && (0 !== $this->deletion_management_transient_duration) ) :
                // Gestion de suppression permanente
            case ( 2 == $this->deletion_management) :
                $query = "SELECT num_notice AS notice_id, UNIX_TIMESTAMP(deletion_date) AS datestamp FROM connectors_out_oai_deleted_records WHERE num_set IN (".implode(",", $this->source_set_ids).") group by num_notice";
                break;

                //Pas de gestion des enregistrements supprimes
            default :
            case ( 0 == $this->deletion_management) :
                break;
        }
        if($query) {
            $result = pmb_mysql_query($query);
            if(pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $datestamps[$row['notice_id']] = $row['datestamp'];
                }
            }
        }

        $iso8601 = new iso8601("YYYY-MM-DDThh:mm:ssZ");
        $records = [];
        foreach($record_ids as $record_id) {
            $set_list = !empty($this->data['sets_by_id'][$record_id]) ? $this->data['sets_by_id'][$record_id] : [];
            $sets = '';
            foreach ($set_list as $set_id) {
                $sets .= "<setSpec>set_".$set_id."</setSpec>";
            }
            $records[] =
            "<record>
                <header status=\"deleted\">
                <identifier>oai:".XMLEntities($this->repository_identifier).":".$record_id."</identifier>
                   {$sets}
                <datestamp>".$iso8601->unixtime_to_iso8601($datestamps[$record_id])."</datestamp>
                </header>
            </record>";

        }
        return $records;
    }


    /**
     * Instancie et met a jour un set
     *
     * @param int $set_id
     * @param boolean $update : effectuer la mise à jour du set
     *
     * @return connector_out_set
     */
    protected function initializeSet($set_id, $update = true)
    {
        $co_set = new_connector_out_set_typed($set_id);
        if($update) {
            $co_set->update_if_expired($this);
        }
        return $co_set;
    }


    public function updateDeletedRecordsCacheCallback($action = '', $set_id = 0, $cache_id = 0, $ids = [])
    {
        $set_id = intval($set_id);
        $cache_id = intval($cache_id);
        $ids = is_array($ids) ? $ids : [];
        array_walk($ids, function(&$a) {$a = intval($a);});

        switch (true) {

            case ( ('removeFromDeleteds') == $action && $set_id && $cache_id ) :

                $query = "delete from connectors_out_oai_deleted_records where num_set = ".$set_id." and num_notice in ";
                $query.= "(select connectors_out_setcache_values_value from connectors_out_setcache_values where connectors_out_setcache_values_cachenum = ".$cache_id.")";
                pmb_mysql_query($query);
                break;

            case ( ('addToDeleteds' == $action) && $set_id && !empty($ids) ) :
                $values = [];
                for( $i = 0 ; $i < count($ids); $i++) {
                    $values[] = "(".$set_id.", ".$ids[$i].", now())";
                }
                $query = "insert into connectors_out_oai_deleted_records (num_set, num_notice, deletion_date) values ".implode(",", $values);
                pmb_mysql_query($query);
                break;
        }
    }
}


/*
 * external_services_converter_oairecord
 * \brief Cette classe génère les enregistrements oai de notices complets et les met en cache
 *
 */
class external_services_converter_oairecord extends external_services_converter
{

    private $set_life_duration;
    private $source_set_ids = array();
    private $repository_identifier = "";
    private $deleted_record_statut = 0;
    private $include_items = 0;
    private $xslt = "";
    private $include_links = 0;
    private $deletion_management;
    private $deletion_management_transient_duration;

    protected $date_from;
    protected $date_until;
    protected $clean_html = 0;
    // Date de création ou date de mise à jour dans le datestamp
    protected $header_datestamp = 'update_date';

    public function __construct(
        $object_type,
        $life_duration,
        $set_life_duration,
        $source_set_ids,
        $repository_identifier,
        $deleted_record_statut,
        $include_items,
        $xslt = "",
        $include_links = 0,
        $deletion_management = 0,
        $deletion_management_transient_duration = 0)
    {
        parent::__construct($object_type, $life_duration);
        $this->set_life_duration = (int) $set_life_duration;
        $this->source_set_ids = $source_set_ids;
        $this->repository_identifier = $repository_identifier;
        $this->deleted_record_statut = $deleted_record_statut;
        $this->include_items = $include_items;
        $this->xslt = $xslt;
        $this->include_links = $include_links;
        $this->deletion_management = $deletion_management;
        $this->deletion_management_transient_duration = $deletion_management_transient_duration;
    }


    public function convert_batch($objects, $format, $target_charset='utf-8')
    {
        //Va chercher dans le cache les notices encore bonnes
        parent::convert_batch($objects, "oai_".$format, $target_charset);
        //Convertit les notices qui doivent l'être
        $this->convert_uncachedoairecords($format, $target_charset);
        return $this->results;
    }


    public function convert_batch_to_oairecords($notices_to_convert = [], $format = 'oai_dc', $target_charset = 'utf-8')
    {
        if ( empty($notices_to_convert)  || (! is_array($notices_to_convert)) ) {
            return;
        }

        // Allons chercher les dates et les statuts des notices
        $notice_datestamps = array();
        $notice_statuts = array();
        $notice_ids = $notices_to_convert;
        // Par paquets de 100 pour ne pas brusquer mysql
        $notice_idsz = array_chunk($notice_ids, 100);
        $iso8601 = new iso8601("YYYY-MM-DDThh:mm:ssZ");
        foreach ($notice_idsz as $anotice_ids) {
            if ($this->header_datestamp == 'create_date') {
                $sql = "SELECT notice_id, UNIX_TIMESTAMP(create_date) AS datestamp, statut FROM notices WHERE notice_id IN (" . implode(",", $anotice_ids) . ")";
            } else {
                $sql = "SELECT notice_id, UNIX_TIMESTAMP(update_date) AS datestamp, statut FROM notices WHERE notice_id IN (" . implode(",", $anotice_ids) . ")";
            }
            $res = pmb_mysql_query($sql);
            while ($row = pmb_mysql_fetch_assoc($res)) {
                $notice_datestamps[$row["notice_id"]] = $iso8601->unixtime_to_iso8601($row["datestamp"]);
                $notice_statuts[$row["notice_id"]] = $row["statut"];
            }
        }

        /*
         * C'est fait dans une autre methode avec le nouveau process
         * //Si il existe un status correspondant à la suppression, on génère ces enregistrements et on les supprime de la liste à générer.
         * $deleted_records = [];
         * if ($this->deleted_record_statut) {
         *
         * foreach ($notice_statuts as $notice_id => $anotice_statut) {
         * if ($anotice_statut == $this->deleted_record_statut) {
         * $notice_sets = connector_out_set_noticecaddie::get_notice_setlist($notice_id);
         * $notice_sets = array_intersect($notice_sets, $this->source_set_ids);
         * $deleted_records[$notice_id] = array(
         * 'datestamp' => $notice_datestamps[$notice_id],
         * 'sets' => $notice_sets
         * );
         * unset($notices_to_convert[array_search($notice_id, $notices_to_convert)]);
         * }
         * }
         * } else if ($this->deletion_management) {
         * $deleted_records = $this->get_deleted_records($this->source_set_ids, $notices_to_convert, $iso8601);
         * }
         * oai_out_get_records::set_deleted_records($deleted_records);
         */

        // Convertissons les notices au format demandé si on ne souhaite pas uniquement les entêtes
        $only_identifier = ($format == "__oai_identifier");
        if (! $only_identifier) {
            $converter = new external_services_converter_notices(1, $this->set_life_duration);
            $converter->params["include_items"] = $this->include_items;
            $converter->params["include_links"] = $this->include_links;
            $converter->params["clean_html"] = $this->clean_html;
            $converter->params["header_datestamp"] = $this->header_datestamp;
            // $metadatas contient les infos des notices
            $metadatas = $converter->convert_batch($notices_to_convert, $format, $target_charset, $this->xslt);
        } else {
        	$metadatas = array();
        }

        //Fabriquons les enregistrements
        foreach ($notices_to_convert as $notice_id) {
            $notice_sets = connector_out_set_noticecaddie::get_notice_setlist($notice_id);
            $notice_sets = array_intersect($notice_sets, $this->source_set_ids);
            $oai_record = "";

            if (! $only_identifier) {
                $oai_record .= "<record>";
            }
            $oai_record .= '<header>
                <identifier>oai:'.XMLEntities($this->repository_identifier).':'.$notice_id.'</identifier>
                <datestamp>'.$notice_datestamps[$notice_id].'</datestamp>';
            foreach ($notice_sets as $aset_id) {
                $oai_record .= "<setSpec>set_".$aset_id."</setSpec>";
            }
            $oai_record .= '</header>';
            if (! $only_identifier) {
                $oai_record .= "<metadata>";
                $oai_record .= (!empty($metadatas[$notice_id]) ? $metadatas[$notice_id] : '');
                $oai_record .= "</metadata>";
                $oai_record .= "</record>";
            }
            $this->results[$notice_id] = $oai_record;
        }
    }


    public function convert_uncachedoairecords($format, $target_charset = 'utf-8')
    {
        $notices_to_convert = array();
        foreach ($this->results as $notice_id => $aresult) {
            if (! $aresult) {
                $notices_to_convert[] = $notice_id;
            }
        }

        $this->convert_batch_to_oairecords($notices_to_convert, $format, $target_charset);

        // Cachons les notices converties maintenant.
        foreach ($notices_to_convert as $anotice_id) {
            if ($this->results[$anotice_id])
                $this->encache_value($anotice_id, $this->results[$anotice_id], "oai_" . $format);
        }
    }

    /**
     * Récupère les notices marquées comme supprimées de tous les sets
     *
     * @param array $notice_sets
     * : Tableau des identifiants de sets
     * @param array $records_not_deleted
     * : Tableau des identifiants de notices présentes dans au moins un set
     * @param iso8601 $iso8601
     *
     * @return array : Renvoie un tableau array({id_notice} => array('datestamp', 'sets'))
     */
    private function get_deleted_records($record_sets, $records_not_deleted, $iso8601)
    {
        $deleted_records = array();
        $query = "select num_notice, num_set, unix_timestamp(deletion_date) as datestamp from connectors_out_oai_deleted_records where num_set in (" . implode(",", $record_sets) . ")";
        if (count($records_not_deleted)) {
            $query .= " and num_notice not in (" . implode(",", $records_not_deleted) . ")";
        }
        if (! empty($this->date_from)) {
            $query .= " and deletion_date > FROM_UNIXTIME(" . $this->date_from . ")";
        }
        if (! empty($this->date_until)) {
            $query .= " and deletion_date < FROM_UNIXTIME(" . $this->date_until . ")";
        }
        if ($this->deletion_management == 1) {
            $query .= " and timestampdiff(second, deletion_date, now()) < " . $this->deletion_management_transient_duration;
        }
        $result = pmb_mysql_query($query);
        if ($result && pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $deleted_records[$row->num_notice]['sets'][] = $row->num_set;
                $timestamp = $iso8601->unixtime_to_iso8601($row->datestamp);
                if (! isset($deleted_records[$row->num_notice]['datestamp']) || ($timestamp > $deleted_records[$row->num_notice]['datestamp'])) {
                    $deleted_records[$row->num_notice]['datestamp'] = $timestamp;
                }
            }
        }
        $deleted_records_in_sets = connector_out_set::listNoticesInSets(array_keys($deleted_records), $record_sets);
        if (! empty($deleted_records_in_sets)) {
            foreach ($deleted_records_in_sets as $record_id) {
                unset($deleted_records[$record_id]);
            }
        }

        return $deleted_records;
    }

    public function set_date_from($date_from)
    {
        $this->date_from = $date_from;
    }

    public function set_date_until($date_until)
    {
        $this->date_until = $date_until;
    }

    public function set_clean_html($clean_html)
    {
        $this->clean_html = $clean_html;
    }
    
    public function set_header_datestamp($header_datestamp)
    {
        $this->header_datestamp = $header_datestamp;
    }
}


/*
 * oai_out_server
 * \brief Cette classe fait le lien entre toutes les autres et fait tourner le bouzin
 *
 */
class oai_out_server
{

    private $msg = array();
    private $oai_source_object = null;
    private $sets=array();

    // Constructeur
    public function __construct(&$msg, &$oai_source_object)
    {
        $this->msg = $msg;
        $this->oai_source_object = $oai_source_object;
    }

    // Fait tourner le serveur
    public function process()
    {
        global $verb;

        // Pour ne pas avoir les entêtes définissant le fichier comme du xml, placer un &nx dans l'url (pour pouvoir utiliser le debugger zend par exemple)
        global $nx;
        if (! isset($nx)) {
            header('Content-Type: text/xml');
        }

        // Recuperation des sets inclus dans la source
        $outsets = new connector_out_sets();
        foreach ($outsets->sets as &$aset) {
            if (in_array($aset->id, $this->oai_source_object->included_sets))
                $this->sets[] = array(
                    "id" => $aset->id,
                    "caption" => $aset->caption
                );
        }

        // Créons l'object que le serveur va utiliser pour récupérer les enregistrements
        $get_records_objects = new oai_out_get_records_notice($this->msg, $this->oai_source_object->config['feuille_xslt']);
        $get_records_objects->oai_cache_duration = $this->oai_source_object->cache_complete_records_seconds;
        $get_records_objects->source_set_ids = $this->oai_source_object->included_sets;
        $get_records_objects->repository_identifier = $this->oai_source_object->repositoryIdentifier;
        $get_records_objects->notice_statut_deletion = $this->oai_source_object->link_status_to_deletion ? $this->oai_source_object->linked_status_to_deletion : 0;
        $get_records_objects->include_items = $this->oai_source_object->include_items ? $this->oai_source_object->include_items : 0;
        $get_records_objects->include_links = $this->oai_source_object->include_links ? $this->oai_source_object->include_links : 0;
        $get_records_objects->deletion_management = $this->oai_source_object->deletion_management ? $this->oai_source_object->deletion_management : 0;
        $get_records_objects->deletion_management_transient_duration = $this->oai_source_object->deletion_management_transient_duration ? $this->oai_source_object->deletion_management_transient_duration : 0;
        $get_records_objects->use_items_update_date = $this->oai_source_object->use_items_update_date ? $this->oai_source_object->use_items_update_date : 0;
        $get_records_objects->clean_html = $this->oai_source_object->clean_html ? $this->oai_source_object->clean_html : 0;
        $get_records_objects->oai_pmh_valid = $this->oai_source_object->oai_pmh_valid ? $this->oai_source_object->oai_pmh_valid : 0;
        $get_records_objects->header_datestamp = $this->oai_source_object->header_datestamp ? $this->oai_source_object->header_datestamp : 'update_date';
        
        // Recuperation des Formats de conversion supplementaires
        $additional_metadataformat = array();
        foreach ($this->oai_source_object->allowed_admin_convert_paths as $convert_path) {
            $additional_metadataformat["convert_" . $convert_path] = array(
                "metadataPrefix" => "convert_" . $convert_path,
                "metadataNamespace" => "http://www.pmbservices.fr/" . "convert_" . $convert_path,
                "schema" => "http://www.pmbservices.fr/notice.xsd"
            );
        }

        // Créons l'objet protocol
        if ($this->oai_source_object->link_status_to_deletion) {
            $deletion = "transient";
        } else {
            switch ($this->oai_source_object->deletion_management) {
                case 0:
                    $deletion = "no";
                    break;
                case 1:
                    $deletion = "transient";
                    $deletion_transient_duration = $this->oai_source_object->deletion_management_transient_duration;
                    break;
                case 2:
                    $deletion = "persistent";
                    break;
                default:
                    $deletion = "no";
                    break;
            }
        }

        $base_url = $this->oai_source_object->baseURL;
        if (! $base_url) {
            $base_url = curPageBaseURL();
            $base_url .= ('/' != substr($base_url, - 1)) ? '/' : '';
            $base_url .= 'ws/connector_out.php?source_id=' . $this->oai_source_object->id;
        }
        $oai_out_protocol = new oai_out_protocol($get_records_objects, $this->msg, $this->oai_source_object->repository_name, $this->oai_source_object->admin_email, $this->sets,
            $this->oai_source_object->repositoryIdentifier, $this->oai_source_object->chunksize, $this->oai_source_object->token_lifeduration, $this->oai_source_object->allow_gzip_compression,
            $deletion, $additional_metadataformat, $base_url, $deletion_transient_duration);

        // Si on peut compresser, on compresse
        if ($this->oai_source_object->allow_gzip_compression) {
            ob_start("ob_gzhandler");
        }
        $response = '';

        // Si la source n'est pas bien configuree
        if (! $this->oai_source_object->repository_name || ! $this->oai_source_object->admin_email || ! $this->oai_source_object->repositoryIdentifier) {
            echo $oai_out_protocol->oai_header();
            echo $oai_out_protocol->oai_error('unconfigured', $this->msg["unconfigured_source"]);
            echo $oai_out_protocol->oai_footer();
            return;
        }

        // Le validateur precise de verifier l'unicite des arguments
        // /!\ Il faudra peut etre prendre en compte les POSTS.
        $error = false;
        $qs_params = explode('&', $_SERVER['QUERY_STRING']);

        $args = array();
        foreach ($qs_params as $v) {
            $tmp = explode('=', $v);
            if (! $tmp[1]) {
                $tmp[1] = '';
            }
            $args[$tmp[0]] = $tmp[1];
        }
        if (count($args) != count($qs_params)) {
            $response .= $oai_out_protocol->oai_error('badArgument', $this->msg['badArgument']);
            $error = true;
        }
        unset($qs_params);
        unset($args);
        unset($tmp);

        if (! $error) {

            // Sinon c'est parti
            // on regarde si un des sets manipules est en cours de rafraichissement, si oui, on bloque tout et fait patienter le client
            if ($oai_out_protocol->sets_being_refreshed()) {

                header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
                header('Status: 503 Service Temporarily Unavailable');
                header('Retry-After: 10');

            } else {

                switch ($verb) {
                    case 'Identify':
                        $response .= $oai_out_protocol->oai_identify();
                        break;
                    case 'ListRecords':
                        $response .= $oai_out_protocol->oai_list_records();
                        break;
                    case 'GetRecord':
                        $response .= $oai_out_protocol->oai_get_record();
                        break;
                    case 'ListSets':
                        $response .= $oai_out_protocol->oai_list_sets();
                        break;
                    case 'ListIdentifiers':
                        $response .= $oai_out_protocol->oai_list_identifier();
                        break;
                    case 'ListMetadataFormats':
                        $response .= $oai_out_protocol->oai_list_metadata_formats();
                        break;
                    default:
                        $response .= $oai_out_protocol->oai_error('badVerb', $this->msg['illegal_verb']);
                        break;
                }
            }
        }
        // Header
        $response = $oai_out_protocol->oai_header() . $response;

        // Footer
        $response .= $oai_out_protocol->oai_footer();

        echo $response;
    }
}
