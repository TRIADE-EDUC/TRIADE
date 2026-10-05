<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: legifrance.class.php,v 1.14.4.3 2025/04/16 12:16:51 dbellamy Exp $

global $class_path;
require_once($class_path."/curl.class.php");

class legifrance extends connector {

    private $listeCodes;
    private $listeJuridictions;
    private $listeNatures;
    private $listeTypeDoc;

    public function __construct($connector_path="")
    {
        global $base_path;
        parent::__construct($connector_path);

        $this->listeCodes=json_decode(file_get_contents($base_path.'/admin/connecteurs/in/legifrance/codes_legi_dalloz.json'),true);
        $this->listeJuridictions=json_decode(file_get_contents($base_path.'/admin/connecteurs/in/legifrance/juridictions_legi_dalloz.json'),true);
        $this->listeNatures=json_decode(file_get_contents($base_path.'/admin/connecteurs/in/legifrance/natures_legi.json'),true);
        $this->listeTypeDoc=json_decode(file_get_contents($base_path.'/admin/connecteurs/in/legifrance/typdoc_legi.json'),true);
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "legifrance";
    }

    //Formulaire des propriétés générales
    public function get_property_form() {
        global $charset;
        $this->fetch_global_properties();
        //Affichage du formulaire en fonction de $this->parameters
        $url='';
        if ($this->parameters) {
            $vars = unserialize($this->parameters);
            $url=$vars['url'];
            $limit=$vars['limit'];
        }
        $form="<div class='row'>
                <div class='colonne3'>
                        <label for='url'>".$this->msg["legifrance_url"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='url' id='url' class='saisie-120em' value='".htmlentities($url,ENT_QUOTES,$charset)."'/>
                </div>
        </div>
        <div class='row'>
                <div class='colonne3'>
                        <label for='url'>".$this->msg["legifrance_limit"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='limit' id='url' class='saisie-60em' value='".htmlentities($limit,ENT_QUOTES,$charset)."'/>
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
        global $url,$limit;
        //Mise en forme des paramètres à partir de variables globales (mettre le résultat dans $this->parameters)
        $keys = array();
        $keys['url']= stripslashes($url);
        $keys['limit']= intval(stripslashes($limit));
        $this->parameters = serialize($keys);
    }

    public function rec_record($record,$source_id,$search_id,$url) {
        //Initialisation
        $ref="";
        $ufield="";
        $usubfield="";
        $field_order=0;
        $subfield_order=0;
        $value="";
        $date_import=date("Y-m-d H:i:s",time());

        $ref = md5($record->uri);

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
        if  (($this->del_old) || ( (!$this->del_old) && (!$ref_exists) ) ) {
            //Insertion de l'entï¿½te
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

            $fields=[
                "titre"=>[["200","a"]],
                "contenu"=>[["327","a"]],
                "nature"=>[["200","e"]],
                "id_texte"=>[["001",""],["901","a"]],
                "cid"=>[["901","b"]],
                "uri"=>[["856","u"]],
                "nor"=>[["901","c"]],
                "eli"=>[["901","d"]],
                "numero"=>[["200","h"]],
                "juridiction"=>[["901","j"]]
            ];

            //Titre des articles
            if ($record->numero) $record->titre=$record->nature." ".$record->numero." / ".$record->titre;

            //URL vers légifrance
            $legiType="";
            if ($record->cid) {
                $record->uri="https://www.legifrance.gouv.fr/affichTexte.do?cidTexte=".$record->cid;
                if ($record->nature=='Article') $record->uri.="&idArticle=".$record->uniq;
                $legiType="LEGI";
            } else if (strpos($record->uri, "juri/judi")!==false) {
                $record->uri="https://www.legifrance.gouv.fr/affichJuriJudi.do?idTexte=".$record->uniq;
                $legiType="JURI - Judiciaire";
            } else if (strpos($record->uri, "juri/admin")!==false) {
                $record->uri="https://www.legifrance.gouv.fr/affichJuriAdmin.do?idTexte=".$record->uniq;
                $legiType="JURI - Administrative";
            } else if (strpos($record->uri, "juri/constit")!==false) {
                $record->uri="https://www.legifrance.gouv.fr/affichJuriConst.do?idTexte=".$record->uniq;
                $legiType="JURI - Constitutionnelle";
            }
            $recordsToInsert=[];
            foreach($record as $key=>$value) {
                if ($key=='nature') {
                    if ($this->listeTypeDoc[$legiType]) {
                        if (in_array($value, $this->listeTypeDoc[$legiType]['typdoc'])) {
                            $typdoc = $this->listeTypeDoc[$legiType]['value'];
                        }
                    }
                    if ($this->listeNatures[$value])
                        $value=$this->listeNatures[$value];
                }
                for ($i=0; $i<count($fields[$key]); $i++) {
                    $ufield=$fields[$key][$i][0];
                    $usubfield=$fields[$key][$i][1];
                    $field_order=0;
                    $recordsToInsert[]=[
                        "source_id"=>$source_id,
                        "ref"=>$ref,
                        "date_import"=>$date_import,
                        "ufield"=>$ufield,
                        "usubfield"=>$usubfield,
                        "field_order"=>$field_order,
                        "subfield_order"=>0,
                        "value"=>$value,
                        "recid"=>$recid,
                        "search_id"=>$search_id
                    ];
                    //$this->insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, 0, $value, $recid, $search_id);
                }
            }
            if(!empty($record->date_debut)){
                $recordsToInsert[]=[
                    "source_id"=>$source_id,
                    "ref"=>$ref,
                    "date_import"=>$date_import,
                    "ufield"=>"210",
                    "usubfield"=>"d",
                    "field_order"=>$field_order,
                    "subfield_order"=>0,
                    "value"=>date('Y', $record->date_debut),
                    "recid"=>$recid,
                    "search_id"=>$search_id
                ];
                $recordsToInsert[]=[
                    "source_id"=>$source_id,
                    "ref"=>$ref,
                    "date_import"=>$date_import,
                    "ufield"=>"902",
                    "usubfield"=>"b",
                    "field_order"=>$field_order,
                    "subfield_order"=>0,
                    "value"=>date('d/m/Y', $record->date_debut),
                    "recid"=>$recid,
                    "search_id"=>$search_id
                ];
                //$this->insert_content_into_entrepot($source_id, $ref, $date_import, "210", "d", $field_order, 0, date('Y', $record->date_debut), $recid, $search_id);
                //$this->insert_content_into_entrepot($source_id, $ref, $date_import, "902", "b", $field_order, 0, date('d/m/Y', $record->date_debut), $recid, $search_id);
            }

            if(!empty($record->date_decision)){
                $recordsToInsert[]=[
                    "source_id"=>$source_id,
                    "ref"=>$ref,
                    "date_import"=>$date_import,
                    "ufield"=>"210",
                    "usubfield"=>"d",
                    "field_order"=>$field_order,
                    "subfield_order"=>0,
                    "value"=>date('Y', $record->date_decision),
                    "recid"=>$recid,
                    "search_id"=>$search_id
                ];
                $recordsToInsert[]=[
                    "source_id"=>$source_id,
                    "ref"=>$ref,
                    "date_import"=>$date_import,
                    "ufield"=>"902",
                    "usubfield"=>"b",
                    "field_order"=>$field_order,
                    "subfield_order"=>0,
                    "value"=>date('d/m/Y', $record->date_decision),
                    "recid"=>$recid,
                    "search_id"=>$search_id
                ];
                //$this->insert_content_into_entrepot($source_id, $ref, $date_import, "210", "d", $field_order, 0, date('Y', $record->date_decision), $recid, $search_id);
                //$this->insert_content_into_entrepot($source_id, $ref, $date_import, "902", "b", $field_order, 0, date('d/m/Y', $record->date_decision), $recid, $search_id);
            }
            if (!empty($typdoc)) {
                $recordsToInsert[] = [
                    "source_id" => $source_id,
                    "ref" => $ref,
                    "date_import" => $date_import,
                    "ufield" => "900",
                    "usubfield" => "a",
                    "field_order" => $field_order,
                    "subfield_order" => 0,
                    "value" => $typdoc,
                    "recid" => $recid,
                    "search_id" => $search_id
                ];
            }
            $recordsToInsert[]=[
                "source_id"=>$source_id,
                "ref"=>$ref,
                "date_import"=>$date_import,
                "ufield"=>"801",
                "usubfield"=>"b",
                "field_order"=>$field_order,
                "subfield_order"=>0,
                "value"=>"LÃ©gifrance ".$legiType,
                "recid"=>$recid,
                "search_id"=>$search_id
            ];
            //$this->insert_content_into_entrepot($source_id, $ref, $date_import, "801", "b", $field_order, 0, "Legifrance ".$legiType, $recid, $search_id);

            //if (($record->dtbase!="LEGI")&&(!$record->nature)) {
            //    $this->insert_content_into_entrepot($source_id, $ref, $date_import, "900", "a", $field_order, 0, "Jurisprudence", $recid, $search_id);
            //}
            $this->insert_content_into_entrepot_multiple($recordsToInsert);
            $this->rec_isbd_record($source_id, $ref, $recid);
            $this->n_recu++;
        }
    }

    private function makeQuery($criterias,$url,$source_id,$search_id,$limit,$corpus) {
        $get=$url."/$corpus/search/?";
        $c=[];
        foreach($criterias as $key=>$val) {
            $c[]=$key.'='.rawurlencode($val);
        }
        $get.=implode('&',$c);
        //Appel Curl
        $curl =  new Curl();
        $result = $curl->get($get);

        if ($result) {
            $result=json_decode($result);
            if (!$result->error) {
                $result=$result->result;
                //Nombre :
                $total=$result->total;
                $red=0;
                //$requete="alter table entrepot_source_$source_id disable keys";
                //pmb_mysql_query($requete);
                while ($red<$total) {
                    $nb=$result->nb;
                    for ($i=0; $i<$nb; $i++) {
                        $elt=$result->matches[$i];
                        if ($elt) {
                            $this->rec_record($elt,$source_id,$search_id,$url);
                        }
                        $red++;
                        if ($red>$limit) break;
                    }
                    if ($red>$limit) break;
                    if ($red<$total) {
                        $result = $curl->get($get.'&start='.$red.'&nb=500');
                        if ($result) {
                            $result=json_decode($result);
                            if ($result->error) {
                                break;
                            }
                            $result=$result->result;
                        }
                    }
                }
                //$requete="alter table entrepot_source_$source_id enable keys";
                //pmb_mysql_query($requete);
            }
        }

    }

    private function date2unix($date) {
        $d=explode("/",$date);
        $unix=$d[2]."-".$d[1]."-".$d[0];
        return $unix;
    }

    private function addDate($criterias,$qv,$isJuri=false) {
        if ($criterias['date']) {
            if (count($criterias['date'])==1) {
                switch ($criterias['date'][0]->op) {
                    case "EQ":
                        $operator="exact_date";
                        break;
                    case "LT":
                        $operator="before";
                        break;
                    case "GT":
                        $operator="after";
                        break;
                    case "LTEQ":
                        $operator="before";
                        break;
                    case "GTEQ":
                        $operator="after";
                        break;
                    default:
                        $operator="exact_date";
                        break;
                }
                if ($isJuri)
                    $qv["restrict_date"]="date_decision";
                    else
                        $qv["restrict_date"]="date_debut";
                        $qv[$operator]=$this->date2unix($criterias['date'][0]->values[0]);
            } else if (count($criterias['date'])==2) {
                if ($isJuri)
                    $qv["restrict_date"]="date_decision";
                    else
                        $qv["restrict_date"]="date_debut";
                        $qv["after"]=$this->date2unix($criterias['date'][0]->values[0]);
                        $qv["before"]=$this->date2unix($criterias['date'][1]->values[0]);
            }
        }
        return $qv;
    }

    private function makeLegiSearch($criterias, $url, $source_id, $search_id, $limit) {
        //Textes codifiés
        $codifie = false;
        $noncodifie = false;
        $qall = false;
        if ($criterias["code"]||$criterias["numero"]) {
            $qv=[];
            if ($criterias['q'])
                $qv['q']=$criterias['q'];

                if ($criterias['code']) {
                    if (isset($this->listeCodes[$criterias['code']])&&($this->listeCodes[$criterias['code']]["legi"]))
                        $qv['code']=$criterias['code'];
                }
                if ($criterias['numero']) {
                    $qv['numero']=$criterias['numero'];
                }
                $qv['etat']='vigueur';
                $qv=$this->addDate($criterias,$qv);
                $this->makeQuery($qv,$url,$source_id,$search_id,$limit,"legi");
                $codifie=true;
        }

        if ($criterias['nor']||$criterias['nature']) {
            //Textes non codifiés
            $qv=[];

            if ($criterias['q'])
                $qv['q']=$criterias['q'];

                if ($criterias['nor']) {
                    $qv['numero']=$criterias['nor'];
                }
                if ($criterias['nature']) {
                    $nature=array_search($criterias['nature'], $this->listeNatures);
                    if ($nature) {
                        $qv['nature']=$nature;
                    }
                }
                $qv=$this->addDate($criterias,$qv);
                $qv['etat']='vigueur';
                $this->makeQuery($qv,$url,$source_id,$search_id,$limit,"legi");
                $noncodifie=true;
        }

        if ( $criterias["q"] && (!$codifie) && (!$noncodifie) ) {
            $qv=["q"=>$criterias["q"]];
            $qv['etat']='vigueur';
            $qv=$this->addDate($criterias,$qv);
            $this->makeQuery($qv,$url,$source_id,$search_id,$limit,"legi");
            $qall=true;
        }

        if ( ($criterias["date"]) && (!$codifie) && (!$noncodifie) && (!$qall) ) {
            $qv=[];
            $qv=$this->addDate($criterias,$qv);
            $this->makeQuery($qv,$url,$source_id,$search_id,$limit,"legi");
        }
    }

    private function makeJuriSearch($criterias,$url,$source_id,$search_id,$limit) {
        $qv=[];
        if ($criterias['q'])
            $qv['q']=$criterias['q'];
            if ($criterias["juridiction"]) {
                if (isset($this->listeJuridictions[$criterias['juridiction']])&&($this->listeJuridictions[$criterias['juridiction']]["legi"]))
                    $qv['juridiction']=$criterias['juridiction'];
            }
            if ($criterias["numero_affaire"]) {
                $qv["numero"]=$criterias["numero_affaire"];
            }
            $qv=$this->addDate($criterias,$qv,true);
            if (count($qv))
                $this->makeQuery($qv,$url,$source_id,$search_id,$limit,"alljuri");
    }

    private function makeAllSearch($criterias,$url,$source_id,$search_id,$limit) {

    }

    //Fonction de recherche
    public function search($source_id,$query,$search_id) {
        global $base_path;

        $this->fetch_global_properties();
        $params=unserialize($this->parameters);
        $url=$params['url'];
        $limit=$params['limit'];

        if (!$limit) $limit=100;

        $criterias=[];

        foreach($query as $amterm) {
            switch ($amterm->ufield) {
                case '200$a':
                    $criterias['titre']=$amterm->values[0];
                    break;
                case 'XXX':
                    $criterias['q']=$amterm->values[0];
                    break;
                case '330$a':
                    $criterias['contenu']=$amterm->values[0];
                    break;
                case '210$d':
                    if (empty($criterias['date'])) $criterias['date']=[];
                    $criterias['date'][]=$amterm;
                    break;
                case '200$h':
                    $criterias['numero']=$amterm->values[0];
                    break;
                case '901$a':
                    $criterias['nor']=$amterm->values[0];
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
                    $criterias['numero_affaire']=$amterm->values[0];
                    break;
                default:
                    break;
            }
        }

        if (count($criterias)) {
            if (($criterias["code"]||$criterias["numero"])||((!$criterias["juridiction"])&&(!$criterias["numero_affaire"]))) {
                $this->makeLegiSearch($criterias, $url,$source_id,$search_id,$limit);
            }
            if (($criterias["juridiction"]||$criterias["numero_affaire"])||((!$criterias["code"])&&(!$criterias["numero"]))) {
                $this->makeJuriSearch($criterias, $url,$source_id,$search_id,$limit);
            }
        }
    }
}
