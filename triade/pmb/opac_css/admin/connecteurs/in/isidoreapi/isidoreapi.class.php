<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: isidoreapi.class.php,v 1.5.10.1 2025/04/16 12:16:50 dbellamy Exp $

global $class_path;
require_once($class_path."/curl.class.php");

class isidoreapi extends connector {

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
        return "isidoreapi";
    }

    //Formulaire des propriétés générales
    public function source_get_property_form($source_id) {
        global $charset;
        $params=$this->get_source_params($source_id);
        //Affichage du formulaire en fonction de $this->parameters
        $url='';
        if ($params["PARAMETERS"]) {
            $vars = unserialize($params["PARAMETERS"]);
            $url=$vars['url'];
            $limit=$vars['limit'];
        }
        $form="<div class='row'>
                <div class='colonne3'>
                        <label for='url'>".$this->msg["isidoreapi_url"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='url' id='url' class='saisie-120em' value='".htmlentities($url,ENT_QUOTES,$charset)."'/>
                </div>
        </div>
        <div class='row'>
                <div class='colonne3'>
                        <label for='url'>".$this->msg["isidoreapi_limit"]."</label>
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

    public function make_serialized_source_properties($source_id) {
        global $url,$limit;
        $this->sources[$source_id]["PARAMETERS"]=serialize(['url'=>$url,'limit'=>$limit]);
    }

    //Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
    public function fetch_default_global_values() {
        parent::fetch_default_global_values();
        $this->repository=2;
    }

    private function checkArray($value) {
        if (is_array($value))
            $val=$value[0];
            else $val=$value;
            if (is_object($val)) {
                if ($val->{"$"}) $val=$val->{"$"}; else $val="?";
            }
            return $val;
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

        $params=$this->get_source_params($source_id);

        $ref = md5($record->{"@uri"});

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
        //Si pas de conservation ou refï¿½rence inexistante
        if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
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

            foreach($record->isidore as $key=>$value) {
                switch ($key) {
                    case "title":
                        $ufield="200";
                        $usubfield="a";
                        $val=$this->checkArray($value);
                        break;
                    case "date":
                        $ufield="210";
                        $usubfield="d";
                        $val=$this->checkArray($value->{"@origin"});
                        if (preg_match("/[0-9]{4}/",$val,$m)) {
                            $val=$m[0];
                        } else $val="";
                        break;
                    case "abstract":
                        $ufield="327";
                        $usubfield="a";
                        $val=$this->checkArray($value);
                        if (is_object($val) && $val->{"$"}) {
                            $val=$val->{"$"};
                        }
                        break;
                    case "types":
                        $ufield="900";
                        $usubfield="a";
                        $val="Doctrine";
                        //$val=$this->checkArray($value->type);
                        break;
                    case "url":
                        $ufield="856";
                        $usubfield="u";
                        $val=$this->checkArray($value);
                        break;
                    case "enrichedCreators":
                        $ufield="700";
                        $usubfield="a";
                        $creator=$this->checkArray($value->creator);
                        $val=$this->checkArray($creator->{"@origin"});
                        break;
                    case "coverages":
                        $ufield="215";
                        $usubfield="a";
                        $val=$this->checkArray($value->coverage);
                        break;
                }
                $field_order=0;
                /*if (!is_object($val))
                 print($source_id." ".$ref." ".$date_import." ".$ufield." ".$usubfield." ".$field_order." 0 ".$val." ".$recid." ".$search_id).PHP_EOL;
                 else {
                 print($source_id." ".$ref." ".$date_import." ".$ufield." ".$usubfield." ".$field_order." 0 ".$recid." ".$search_id).PHP_EOL;
                 print_r($val);
                 }*/
                $this->insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, 0, $val, $recid, $search_id);
            }
            //$this->insert_content_into_entrepot($source_id, $ref, $date_import, "900", "a", 0, 0, 'Isidore', $recid, $search_id);
            $this->insert_content_into_entrepot($source_id, $ref, $date_import, "801", "b", 0, 0, $params["NAME"], $recid, $search_id);
            $this->rec_isbd_record($source_id, $ref, $recid);
            $this->n_recu++;
        }
    }

    public function make_get($url,$criterias) {
        $get=$url."/resource/search?";
        $first=true;
        foreach ($criterias as $param=>$value) {
            $get.=(!$first?"&":"").$param."=".$value;
            if ($first) $first=!$first;
        }
        return $get;
    }

    //Fonction de recherche
    public function search($source_id,$query,$search_id) {
        global $base_path;

        $params=$this->get_source_params($source_id);
        $params_source=unserialize($params["PARAMETERS"]);

        $url=$params_source['url'];
        $limit=$params_source['limit'];

        if (!$limit) $limit=100;

        foreach($query as $amterm) {
            switch ($amterm->ufield) {
                case 'XXX':
                    $criterias['q']=rawurlencode($amterm->values[0]);
                    break;
                case '461$t':
                    $criterias['collection']=rawurlencode($amterm->fieldvar['trevue_id'][0]);
                    break;
                case '7XX':
                    $criterias['author']=rawurlencode($amterm->fieldvar['auteur_id'][0]);
                    break;
                case '210$d':
                    $criterias['date']=rawurlencode(substr($amterm->values[0],strlen($amterm->values[0])-4,4));
                    break;
                case '200$a':
                    $criterias['q']=rawurlencode($amterm->values[0]);
                    break;
                default:
                    break;
            }
        }
        if (!count($criterias)) return;
        $criterias['output']='json';
        $criterias['discipline']="http://aurehal.archives-ouvertes.fr/subject/shs.droit";
        $criterias['replies']=200;
        //Requête CURL au webservice...
        $get=$this->make_get($url,$criterias);

        //Appel Curl
        $curl =  new Curl();
        $result = $curl->get($get);
        if ($result) {
            $result=json_decode($result);
            //Nombre :
            $total=($result->response->replies->meta->{"@items"}>$limit?$limit:$result->response->replies->meta->{"@items"});
            $page=0;
            $red=0;
            $result=$result->response->replies->content->reply;
            if (!is_array($result)) $result=[$result];
            while ($red<$total) {
                for ($i=0; $i<count($result); $i++) {
                    $elt=$result[$i];
                    if ($elt) {
                        $this->rec_record($elt,$source_id,$search_id,$url);
                    }
                    $red++;
                    if ($red>$limit) break;
                }
                if ($red>$limit) break;
                if ($red<$total) {
                    $page++;
                    $criterias['page']=$page;
                    $get=$this->make_get($url,$criterias);
                    $result = $curl->get($get);
                    if ($result) {
                        $result=json_decode($result);
                        $result=$result->response->replies->content->reply;
                    }
                } else break;
            }
        }
    }
}
