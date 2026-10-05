<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dalloz.class.php,v 1.12.4.3 2025/04/16 12:16:51 dbellamy Exp $

global $class_path;
require_once($class_path."/curl.class.php");

class dalloz extends connector {

    static $FDOC=[
        "DZ/ACTUALITES"=>"Actualités",
        "DZ/CODES-SECS"=>"Code",
        "DZ/JURISPRUDENCE"=>"Jurisprudence",
        "EL/SOURCES"=>"Jurisprudence",
        "DZ/OASIS"=>"Fiches d'orientation",
        "DZ/ENCYCLOPEDIES"=>"Encyclopédie",
        "DZ/FORMULES"=>"Formules"
    ];

    private $listeCodes;
    private $listeJuridictions;
    private $listeTypedoc;

    public function __construct($connector_path="")
    {
        global $base_path;
        parent::__construct($connector_path);

        $this->listeCodes=json_decode(file_get_contents($base_path.'/admin/connecteurs/in/dalloz/codes_legi_dalloz.json'),true);
        $this->listeJuridictions=json_decode(file_get_contents($base_path.'/admin/connecteurs/in/dalloz/juridictions_legi_dalloz.json'),true);
        $this->listeTypedoc=json_decode(file_get_contents($base_path.'/admin/connecteurs/in/dalloz/typdoc_dalloz.json'),true);
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "dalloz";
    }

    //Formulaire des proprietes generales
    public function get_property_form() {
        global $charset;
        $this->fetch_global_properties();
        //Affichage du formulaire en fonction de $this->parameters
        $apikey='';
        if ($this->parameters) {
            $vars = unserialize($this->parameters);
            $apikey=$vars['apikey'];
            $limit=$vars['limit'];
        }
        $form="<div class='row'>
                <div class='colonne3'>
                        <label for='apikey'>".$this->msg["dalloz_apikey"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='apikey' id='apikey' class='saisie-120em' value='".htmlentities($apikey,ENT_QUOTES,$charset)."'/>
                </div>
        </div>
        <div class='row'>
                <div class='colonne3'>
                        <label for='limit'>".$this->msg["dalloz_limit"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='limit' id='limit' class='saisie-60em' value='".htmlentities($limit,ENT_QUOTES,$charset)."'/>
                </div>
        </div>";

        $form.="
                <div class='row'></div>
                ";
        return $form;
    }

    public function source_get_property_form($source_id) {
        $form ="<div class='row'></div>";
        return $form;
    }

    public function make_serialized_source_properties($source_id) {
        $this->sources[$source_id]["PARAMETERS"]=serialize([]);
    }


    public function make_serialized_properties() {
        global $apikey,$limit;
        //Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
        $keys = array();
        $keys['apikey']= stripslashes($apikey);
        $keys['limit']= intval(stripslashes($limit));
        $this->parameters = serialize($keys);
    }

    public function rec_record($record,$source_id,$search_id) {
        global $charset;
        //Initialisation
        $ref="";
        $ufield="";
        $usubfield="";
        $field_order=0;
        $subfield_order=0;
        $value="";
        $date_import=date("Y-m-d H:i:s",time());

        $ref = md5($record->Digest->UrId);

        //Si conservation des anciennes notices, on regarde si elle existe
        if (!$this->del_old) {
            $ref_exists = $this->has_ref($source_id, $ref);
        }
        //Si pas de conservation des anciennes notices, on supprime
        if ($this->del_old) {
            $this->delete_from_entrepot($source_id, $ref);
            $this->delete_from_external_count($source_id, $ref);
        }
        $ref_exists = false;
        //Si pas de conservation ou reference inexistante
        if ( ($this->del_old) || ( (!$this->del_old) && (!$ref_exists) ) ) {
            //Insertion de l'entete
            $n_header["rs"]="*";
            $n_header["ru"]="*";
            $n_header["el"]="*";
            $n_header["bl"]="m";
            $n_header["hl"]="0";
            $n_header["dt"]="a";

            //Récupération d'un ID
            $recid = $this->insert_into_external_count($source_id, $ref);

            foreach($n_header as $hc=>$code) {
                $this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid, $search_id);
            }

            $to_insert=[];
            //Champs communs
            //ur-id
            $to_insert[]=["ufield"=>"001","usubfield"=>"","field_order"=>0,"value"=>$record->NodeValues->{'ur-id'}];
            //Sommaire
            $to_insert[]=["ufield"=>"330","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->summary];
            //Source
            $to_insert[]=["ufield"=>"801","usubfield"=>"b","field_order"=>0,"value"=>"Dalloz"];
            //Traitement de UR-METAS
            $urMetas=json_decode($record->NodeValues->{'ur-metas'});

            $url="https://www.dalloz-avocats.fr/documentation/Document?id=".$urMetas->{"els-id"};

            //Lien
            /*$urInfos=$record->NodeValues->{'ur-infos'};
             $urls=[];*/
            /*$lien=preg_match("/@@exaDocLink{url:'([^\']*)'/",$urInfos,$urls);
             if ($lien) {
             $url=$urls[1];
             }
             $dockey=[];
             $lien=preg_match("/@@exaDocLink{dockey:'([^\']*)'/",$urInfos,$dockeys);
             if ($lien) {
             $url="https://www.dalloz-avocats.fr/documentation/Document?id=".$dockeys[1];
             }*/

            $to_insert[]=["ufield"=>"856","usubfield"=>"u","field_order"=>0,"value"=>$url];
            //Type de document
            /*$td=explode("/",$record->PapidocUri);
             $to_insert[]=["ufield"=>"900","usubfield"=>"a","field_order"=>0,"value"=>self::$FDOC[$td[0]."/".$td[1]]];*/

            //Titre
            $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$urMetas->titre];

            $value = $urMetas->categorie;
            if (array_key_exists($urMetas->categorie, $this->listeTypedoc)) {
                $value = $this->listeTypedoc[$urMetas->categorie];
            }
            //Type de document
            if ($urMetas->categorie == 'Encyclopedie') {
                $urMetas->categorie = ($charset != 'utf-8' ? 'Encyclop�die' : encoding_normalize::utf8_normalize('Encyclop�die'));
            }
            $to_insert[]=["ufield"=>"900","usubfield"=>"a","field_order"=>0,"value"=> urldecode($value)];

            //En fonction du type de doc !
            /*switch (true) {
             case (substr($record->PapidocUri,0,strlen("DZ/ACTUALITES"))=="DZ/ACTUALITES"):
             $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->fulltext_type4];
             $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type3];
             break;
             case (substr($record->PapidocUri,0,strlen("DZ/OASIS"))=="DZ/OASIS"):
             $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->fulltext_type6];
             $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type7];
             break;
             case (substr($record->PapidocUri,0,strlen("DZ/CODES-SECS"))=="DZ/CODES-SECS"):
             $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->fulltext_type6];
             $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type3];
             $to_insert[]=["ufield"=>"300","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type0];
             $to_insert[]=["ufield"=>"901","usubfield"=>"h","field_order"=>0,"value"=>$record->NodeValues->legis_non_codifie_nature];
             $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>$record->NodeValues->legis_non_codifie_date];
             break;
             case (substr($record->PapidocUri,0,strlen("EL/SOURCES/JRP"))=="EL/SOURCES/JRP"):
             $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->fulltext_type5];
             $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type3];
             $to_insert[]=["ufield"=>"606","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type0];
             $to_insert[]=["ufield"=>"901","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->juris_juridiction];
             $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>$record->NodeValues->juris_date];
             break;
             case (substr($record->PapidocUri,0,strlen("DZ/JURISPRUDENCE"))=="DZ/JURISPRUDENCE"):
             $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->fulltext_type5];
             $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type3];
             $to_insert[]=["ufield"=>"606","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type0];
             $to_insert[]=["ufield"=>"901","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->juris_juridiction];
             $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>$record->NodeValues->juris_date];
             break;
             case (substr($record->PapidocUri,0,strlen("DZ/ENCYCLOPEDIES"))=="DZ/ENCYCLOPEDIES"):
             $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->fulltext_type6];
             $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type5];
             $to_insert[]=["ufield"=>"300","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type0];
             break;
             case (substr($record->PapidocUri,0,strlen("DZ/REVUES"))=="DZ/REVUES"):
             $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$record->NodeValues->fulltext_type5];
             $to_insert[]=["ufield"=>"200","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type3];
             $to_insert[]=["ufield"=>"300","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->fulltext_type0];
             $to_insert[]=["ufield"=>"700","usubfield"=>"a","field_order"=>0,"value"=>$record->NodeValues->doct_auteur];
             $to_insert[]=["ufield"=>"200","usubfield"=>"i","field_order"=>0,"value"=>$record->NodeValues->doct_titre.", ".$record->NodeValues->doct_numero." ".$record->NodeValues->doct_annee.", p.".$record->NodeValues->doct_page];
             $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>$record->NodeValues->doct_date_publication];
             break;

             }*/
            switch (true) {
                case (substr($record->PapidocUri,0,strlen("DZ/ACTUALITES"))=="DZ/ACTUALITES"):
                    if (!empty($urMetas->doctrine)) {
                        $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>substr($urMetas->doctrine->{"date-publication"},0,4)];
                        $to_insert[]=["ufield"=>"902","usubfield"=>"a","field_order"=>0,"value"=>$urMetas->doctrine->{"date-publication"}];
                        $to_insert[]=["ufield"=>"902","usubfield"=>"b","field_order"=>0,"value"=>formatdate($urMetas->doctrine->{"date-publication"})];
                        $to_insert[]=["ufield"=>"700","usubfield"=>"a","field_order"=>0,"value"=>$urMetas->doctrine->{"auteur"}];
                    }
                    break;
                case (substr($record->PapidocUri,0,strlen("DZ/OASIS"))=="DZ/OASIS"):
                    if (!empty($urMetas->doctrine)) {
                        $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>substr($urMetas->doctrine->{"date-publication"},0,4)];
                        $to_insert[]=["ufield"=>"902","usubfield"=>"a","field_order"=>0,"value"=>$urMetas->doctrine->{"date-publication"}];
                        $to_insert[]=["ufield"=>"902","usubfield"=>"b","field_order"=>0,"value"=>formatdate($urMetas->doctrine->{"date-publication"})];
                    }
                    break;
                case (substr($record->PapidocUri,0,strlen("DZ/CODES-SECS"))=="DZ/CODES-SECS"):
                    $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$urMetas->code->article." - ".$urMetas->code->nom];
                    $to_insert[]=["ufield"=>"901","usubfield"=>"h","field_order"=>0,"value"=>$urMetas->code->nom];
                    if (!empty($urMetas->plan)) $to_insert[]=["ufield"=>"300","usubfield"=>"a","field_order"=>0,"value"=>implode("\n",$urMetas->plan)];
                    break;
                case (substr($record->PapidocUri,0,strlen("EL/SOURCES/JRP"))=="EL/SOURCES/JRP"):
                    $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$urMetas->jurisprudence->reference];
                    $to_insert[]=["ufield"=>"901","usubfield"=>"e","field_order"=>0,"value"=>$urMetas->jurisprudence->juridiction." ".$urMetas->jurisprudence->ville];
                    $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>substr($urMetas->jurisprudence->date,0,4)];
                    $to_insert[]=["ufield"=>"902","usubfield"=>"a","field_order"=>0,"value"=>$urMetas->jurisprudence->date];
                    $to_insert[]=["ufield"=>"902","usubfield"=>"b","field_order"=>0,"value"=>formatdate($urMetas->jurisprudence->date)];
                    break;
                case (substr($record->PapidocUri,0,strlen("DZ/JURISPRUDENCE"))=="DZ/JURISPRUDENCE"):
                    $to_insert[]=["ufield"=>"200","usubfield"=>"e","field_order"=>0,"value"=>$urMetas->jurisprudence->reference];
                    $to_insert[]=["ufield"=>"901","usubfield"=>"e","field_order"=>0,"value"=>$urMetas->jurisprudence->juridiction." ".$urMetas->jurisprudence->ville." ".$urMetas->jurisprudence->chambre];
                    $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>substr($urMetas->jurisprudence->date,0,4)];
                    $to_insert[]=["ufield"=>"902","usubfield"=>"a","field_order"=>0,"value"=>$urMetas->jurisprudence->date];
                    $to_insert[]=["ufield"=>"902","usubfield"=>"b","field_order"=>0,"value"=>formatdate($urMetas->jurisprudence->date)];
                    break;
                case (substr($record->PapidocUri,0,strlen("DZ/ENCYCLOPEDIES"))=="DZ/ENCYCLOPEDIES"):
                    if (!empty($urMetas->plan)) $to_insert[]=["ufield"=>"300","usubfield"=>"a","field_order"=>0,"value"=>implode("\n",$urMetas->plan)];
                    break;
                case (substr($record->PapidocUri,0,strlen("DZ/FORMULES"))=="DZ/FORMULES"):
                    if (!empty($urMetas->plan)) $to_insert[]=["ufield"=>"300","usubfield"=>"a","field_order"=>0,"value"=>implode("\n",$urMetas->plan)];
                    $to_insert[]=["ufield"=>"210","usubfield"=>"d","field_order"=>0,"value"=>substr($urMetas->doctrine->{"date-publication"},0,4)];
                    $to_insert[]=["ufield"=>"902","usubfield"=>"a","field_order"=>0,"value"=>$urMetas->doctrine->{"date-publication"}];
                    $to_insert[]=["ufield"=>"902","usubfield"=>"b","field_order"=>0,"value"=>formatdate($urMetas->doctrine->{"date-publication"})];
                    break;
            }
            $record=[
                "source_id"=>$source_id,
                "ref"=>$ref,
                "date_import"=>$date_import,
                "recid"=>$recid,
                "search_id"=>$search_id,
                "subfield_order"=>0
            ];
            $records=[];
            foreach ($to_insert as $rec) {
                if ($rec["value"]) {
                    $record["ufield"]=$rec["ufield"];
                    $record["usubfield"]=$rec["usubfield"];
                    $record["field_order"]=$rec["field_order"];
                    $record["value"]=$rec["value"];
                    $records[]=$record;
                    //$this->insert_content_into_entrepot($source_id, $ref, $date_import, $rec["ufield"], $rec["usubfield"], $rec["field_order"], 0, $rec["value"], $recid, $search_id);
                }
            }
            $this->insert_content_into_entrepot_multiple($records);
            $this->rec_isbd_record($source_id, $ref, $recid);
            $this->n_recu++;
        }
    }

    private function dallozTime($date) {
        $dallozDate=explode('/',$date);
        return $dallozDate[1]."/".$dallozDate[0]."/".$dallozDate[2];
    }

    private function makeDate($criterion,$criterias,&$queryParts) {
        if ($criterias['date']) {
            if (count($criterias['date'])==1) {
                switch ($criterias['date'][0]->op) {
                    case "EQ":
                        $operator="On";
                        break;
                    case "LT":
                        $operator="Before";
                        break;
                    case "GT":
                        $operator="After";
                        break;
                    case "LTEQ":
                        $operator="Until";
                        break;
                    case "GTEQ":
                        $operator="After";
                        break;
                    default:
                        $operator="EQ";
                        break;
                }
                $queryParts[]=[
                    "criterion-id"=>$criterion,
                    "query-part-type"=>"Date range",
                    "value"=>[
                        "query-values-type"=>"Date",
                        "operator"=>$operator,
                        "value"=>$this->dallozTime($criterias['date'][0]->values[0])
                    ]
                ];
            } else if (count($criterias['date'])==2) {
                $queryParts[]=[
                    "criterion-id"=>$criterion,
                    "query-part-type"=>"Date range",
                    "value"=>[
                        "query-values-type"=>"Date Interval",
                        "lower-value"=>$this->dallozTime($criterias['date'][0]->values[0]),
                        "upper-value"=>$this->dallozTime($criterias['date'][1]->values[0])
                    ]
                ];
            }
        }
    }

    private function makeSearchNonCodifieText($criterias) {
        $queryParts=[];
        $this->makeDate("legis-non-codifie-date", $criterias, $queryParts);
        if ($criterias['texteno']) {
            $queryParts[]=[
                "criterion-id"=>"legis-non-codifie-numero",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['texteno']
                ]
            ];
        }
        if ($criterias['articleno']) {
            $queryParts[]=[
                "criterion-id"=>"legis-non-codifie-article",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['articleno']
                ]
            ];
        }
        if ($criterias['nature']) {
            $queryParts[]=[
                "criterion-id"=>"legis-non-codifie-nature",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['nature']
                ]
            ];
        }
        return $queryParts;
    }

    private function makeSearchJurisprudence2($criterias) {
        $queryParts=[
            [
                "criterion-id"=>"fdoc",
                "query-part-type"=>"Field",
                //"operator"=>"or",
                "values"=>[
                    "EL/SOURCES/JRP"
                ]
            ]
        ];
        $this->makeDate("juris-date", $criterias, $queryParts);
        if ($criterias['nodecision']) {
            $queryParts[]=[
                "criterion-id"=>"juris-reference",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['nodecision']
                ]
            ];
        }
        if ($criterias['juridiction']) {
            if (isset($this->listeJuridictions[$criterias['juridiction']])&&($this->listeJuridictions[$criterias['juridiction']]["code"])) {
                $queryParts[]=[
                    "criterion-id"=>"juris-juridiction",
                    "query-part-type"=>"Field",
                    "values"=>[
                        $this->listeJuridictions[$criterias['juridiction']]["code"]
                    ]
                ];
            }
        }
        return $queryParts;
    }

    private function makeSearchJurisprudence($criterias) {
        $queryParts=[
            [
                "criterion-id"=>"fdoc",
                "query-part-type"=>"Field",
                //"operator"=>"or",
                "values"=>[
                    "DZ/JURISPRUDENCE"
                ]
            ]
        ];
        $this->makeDate("juris-date", $criterias, $queryParts);
        if ($criterias['nodecision']) {
            $queryParts[]=[
                "criterion-id"=>"juris-reference",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['nodecision']
                ]
            ];
        }
        if ($criterias['juridiction']) {
            if (isset($this->listeJuridictions[$criterias['juridiction']])&&($this->listeJuridictions[$criterias['juridiction']]["code"])) {
                $queryParts[]=[
                    "criterion-id"=>"juris-juridiction",
                    "query-part-type"=>"Field",
                    "values"=>[
                        $this->listeJuridictions[$criterias['juridiction']]["code"]
                    ]
                ];
            }
        }
        return $queryParts;
    }

    private function makeSearchOthers($criterias) {
        $queryParts=[
            [
                "criterion-id"=>"fdoc",
                "query-part-type"=>"Field",
                "values"=>[
                    "DZ/ACTUALITES-DZFR",
                    "DZ/ACTUALITES/ACTUS-DZ",
                    "DZ/ACTUALITES-DZAV",
                    "DZ/OASIS",
                    "DZ/ENCYCLOPEDIES/BDAC",
                    "DZ/ENCYCLOPEDIES/BDAP",
                    "DZ/FORMULES/FORMBDA"
                ],
                "operator"=>"OR"
            ]
        ];
        if ($criterias['titre']) {
            $queryParts[]=[
                "criterion-id"=>"fulltext_type3",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['titre']
                ]
            ];
        }
        $this->makeDate("doct_date_publication", $criterias, $queryParts);
        return $queryParts;
    }

    private function makeSearchEncyclopedies($criterias) {
        $queryParts=[
            [
                "criterion-id"=>"fdoc",
                "query-part-type"=>"Field",
                "values"=>[
                    "DZ/ENCYCLOPEDIES/BDAC",
                    "DZ/ENCYCLOPEDIES/BDAP"
                ],
                "operator"=>"OR"
            ]
        ];
        if ($criterias['titre']) {
            $queryParts[]=[
                "criterion-id"=>"fulltext_type3",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['titre']
                ]
            ];
        }
        return $queryParts;
    }

    private function makeSearchCodifieText($criterias) {
        $queryParts=[
            [
                "criterion-id"=>"fdoc",
                "query-part-type"=>"Field",
                "values"=>[
                    "DZ/CODES-SECS",
                ]
            ]
        ];
        if ($criterias['articleno']) {
            $queryParts[]=[
                "criterion-id"=>"legis-codifie-article",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['articleno']
                ]
            ];
        }
        if ($criterias['code']) {
            if (isset($this->listeCodes[$criterias['code']])&&($this->listeCodes[$criterias['code']]["code"])) {
                $queryParts[]=[
                    "criterion-id"=>"legis-codifie-code",
                    "query-part-type"=>"Field",
                    "values"=>[
                        $this->listeCodes[$criterias['code']]["code"]
                    ]
                ];
            }
        }
        return $queryParts;
    }

    private function makeSearchRevues($criterias) {
        $queryParts=[
            [
                "criterion-id"=>"fdoc",
                "query-part-type"=>"Field",
                "values"=>[
                    "DZ/REVUES",
                ]
            ]
        ];
        $this->makeDate("doct-date-publication", $criterias, $queryParts);
        if ($criterias['trevue']) {
            $queryParts[]=[
                "criterion-id"=>"doct-titre",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['trevue']
                ]
            ];
        }
        if ($criterias['auteur']) {
            $queryParts[]=[
                "criterion-id"=>"doct-auteur",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['auteur']
                ]
            ];
        }
        if ($criterias['titre']) {
            $queryParts[]=[
                "criterion-id"=>"fulltext_type3",
                "query-part-type"=>"Field",
                "values"=>[
                    $criterias['titre']
                ]
            ];
        }
        return $queryParts;
    }

    private function makeCurlSearch($queryParts,$criterias,$apikey,$limit, $search_id, $source_id) {
        if (count($queryParts)==0) return;
        if ((count($queryParts)==1)&&($criterias['all']=='')) return;
        //Requête CURL au webservice...
        $post="http://els-sie-test.apigee.net/h2o/search-service/api/v1/search/instance/test-oap-dzavoc/results";
        $query=["request"=>[
            "query-tree"=>[]
        ],
            "result-number-per-page"=>200,
            "result-page-number"=>1,
            "meta-data-list"=>[
                /*"ur_infos",*/
                "ur-metas",
                "ur_id",
                "uri",
                "fdoc",
                "fulltext_type2"
                /*"fulltext_type7",
                 "fulltext_type6",
                 "fulltext_type5",
                 "fulltext_type4",
                 "fulltext_type3",
                 "fulltext_type0",*/
            ]
        ];
        if ($criterias['all']) {
            $query["request"]["query-tree"]["fulltext-query-part"]=[
                "query-part-type"=>"FullText",
                "value"=>$criterias['all']
            ];
        }
        $query["request"]["query-tree"]["filter-query-part"]=[
            "query-part-type"=>"Multiple queries",
            "operator"=>"AND",
            "query-parts"=>$queryParts
        ];
        //highlight_string(print_r($query,true));

        //print json_encode($query);

        //Appel Curl
        $curl =  new Curl();
        $curl->headers=[
            "Content-Type"=>"application/json",
            "x-apikey"=>$apikey
        ];
        $result = $curl->post($post,json_encode($query));
        if ($result) {
            $result=json_decode($result);
            //Nombre :
            $total=$result->resultsCount;
            $red=0;
            $page=1;
            while ($red<$total) {
                $nb=count($result->urs);
                for ($i=0; $i<$nb; $i++) {
                    $elt=$result->urs[$i];
                    if ($elt) {
                        $this->rec_record($elt,$source_id,$search_id);
                    }
                    $red++;
                    if ($red>$limit) break;
                }
                if ($red>$limit) break;
                if ($red<$total) {
                    $page++;
                    $query["result-page-number"]=$page;
                    $result=$curl->post($post,json_encode($query));
                    if ($result) {
                        $result=json_decode($result);
                    } else break;
                }
            }
        }
    }

    //Fonction de recherche
    public function search($source_id,$query,$search_id) {
        global $base_path;
        global $restrict_dalloz_search;

        if (empty($restrict_dalloz_search)) {
            $restrict_dalloz_search=["CodifiedText"=>1,"Jurisprudence"=>1,"Jurisprudence2"=>1,"Revues"=>1,"Others"=>1,"Encyclopedies"=>1];
        }
            //highlight_string(print_r($query,true));

        $this->fetch_global_properties();
        $params=unserialize($this->parameters);
        $apikey=$params['apikey'];
        $limit=$params['limit'];

        if (!$limit) $limit=100;

        $criterias=[];

        foreach($query as $amterm) {
            switch ($amterm->ufield) {
                case '200$a':
                    $criterias['titre']=$amterm->values[0];
                    break;
                case 'XXX':
                    $criterias['all']=$amterm->values[0];
                    break;
                case '330$a':
                    $criterias['resume']=$amterm->values[0];
                    break;
                case '210$d':
                    if (empty($criterias['date'])) $criterias['date']=[];
                    $criterias['date'][]=$amterm;
                    break;
                case '200$h':
                    $criterias['articleno']=$amterm->values[0];
                    break;
                case '461$t':
                    $criterias['trevue']=$amterm->values[0];
                    break;
                case '7XX':
                    $criterias['auteur']=$amterm->values[0];
                    break;
                case '901$a':
                    $criterias['texteno']=$amterm->values[0];
                    break;
                case '900$a':
                    $criterias['nature']=$amterm->values[0];
                    break;
                case '901$h':
                    $criterias['code']=$amterm->values[0];
                    break;
                case '901$e':
                    $criterias['juridiction']=$amterm->values[0];
                    break;
                case '901$c':
                    $criterias['nodecision']=$amterm->values[0];
                    break;
                default:
                    break;
            }
        }
        //Textes codifiés


        //Textes non codifiés
        /*$qp=$this->makeSearchNonCodifieText($criterias);
         $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);*/

        if (empty($criterias['juridiction'])&&empty($criterias['nodecision'])&&empty($criterias['articleno'])&&empty($criterias['code'])&&empty($criterias['auteur'])&&empty($criterias['trevue'])) {
            if (!empty($criterias['date'])) {
                if ($restrict_dalloz_search["Jurisprudence"]) {
                    $qp=$this->makeSearchJurisprudence($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Jurisprudence2"]) {
                    $qp=$this->makeSearchJurisprudence2($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Revues"]) {
                    $qp=$this->makeSearchRevues($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Others"]) {
                    $qp=$this->makeSearchOthers($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
            } else {
                //On recherche sur tout
                if ($restrict_dalloz_search["CodifiedText"]) {
                    $qp=$this->makeSearchCodifieText($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Jurisprudence"]) {
                    $qp=$this->makeSearchJurisprudence($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Jurisprudence2"]) {
                    $qp=$this->makeSearchJurisprudence2($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Revues"]) {
                    $qp=$this->makeSearchRevues($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Others"]) {
                    $qp=$this->makeSearchOthers($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Encyclopedies"]) {
                    $qp=$this->makeSearchEncyclopedies($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
            }
        } else {
            if (!empty($criterias['articleno'])||!empty($criterias['code'])) {
                if ($restrict_dalloz_search["CodifiedText"]) {
                    $qp=$this->makeSearchCodifieText($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
            } elseif (!empty($criterias['juridiction'])||!empty($criterias['nodecision'])) {
                if ($restrict_dalloz_search["Jurisprudence"]) {
                    $qp=$this->makeSearchJurisprudence($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
                if ($restrict_dalloz_search["Jurisprudence2"]) {
                    $qp=$this->makeSearchJurisprudence2($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
            } elseif (!empty($criterias['auteur'])||!empty($criterias['trevue'])) {
                if ($restrict_dalloz_search["Revues"]) {
                    $qp=$this->makeSearchRevues($criterias);
                    $this->makeCurlSearch($qp, $criterias, $apikey, $limit, $search_id, $source_id);
                }
            }
        }
    }
}
