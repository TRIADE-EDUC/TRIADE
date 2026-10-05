<?php
global $class_path;
require_once($class_path."/curl.class.php");

class mediawiki extends connector {

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "mediawiki";
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
                $typedoclabel=$vars['typedoclabel'];
        }
        $form="<div class='row'>
                <div class='colonne3'>
                        <label for='url'>".$this->msg["mediawiki_url"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='url' id='url' class='saisie-120em' value='".htmlentities($url ?? "", ENT_QUOTES, $charset)."'/>
                </div>
        </div>
        <div class='row'>
                <div class='colonne3'>
                        <label for='limit'>".$this->msg["mediawiki_limit"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='limit' id='limit' class='saisie-60em' value='".htmlentities($limit ?? "", ENT_QUOTES, $charset)."'/>
                </div>
        </div>
        <div class='row'>
                <div class='colonne3'>
                        <label for='typedoclabel'>".$this->msg["mediawiki_label"]."</label>
                </div>
                <div class='colonne_suite'>
                        <input type='text' name='typedoclabel' id='typedoclabel' class='saisie-60em' value='".htmlentities($typedoclabel ?? "", ENT_QUOTES, $charset)."'/>
                </div>
        </div>";

        $form.="
                <div class='row'></div>
                ";
        return $form;
    }

    public function make_serialized_source_properties($source_id) {
        global $url,$limit,$typedoclabel;
	$this->sources[$source_id]["PARAMETERS"]=serialize(['url'=>$url,'limit'=>$limit,'typedoclabel'=>$typedoclabel]);
    }

    //Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
    public function fetch_default_global_values() {
            parent::fetch_default_global_values();
            $this->repository=2;
    }

    public function rec_record($record,$source_id,$search_id,$url,$typedoclabel) {
        //Initialisation
        $ref="";
        $ufield="";
        $usubfield="";
        $field_order=0;
        $subfield_order=0;
        $value="";
        $date_import=date("Y-m-d H:i:s",time());

        $params=$this->get_source_params($source_id);

        $ref = md5($record->title);

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

            $fields=[
                "title"=>[["200","a"]],
                "snippet"=>[["327","a"]],
            ];

            foreach($record as $key=>$value) {
                for ($i=0; $i<count($fields[$key]); $i++) {
                    $ufield=$fields[$key][$i][0];
                    $usubfield=$fields[$key][$i][1];
                    $field_order=0;
                    $this->insert_content_into_entrepot($source_id, $ref, $date_import, $ufield, $usubfield, $field_order, 0, $value, $recid, $search_id);
                }
            }
            $date=date_create($record->timestamp);
            $year=$date->format("Y");
            $datef=$date->format("d/m/Y");
            $this->insert_content_into_entrepot($source_id, $ref, $date_import, "210", "d", 0, 0, $year, $recid, $search_id);
            $this->insert_content_into_entrepot($source_id, $ref, $date_import, "902", "b", 0, 0, $datef, $recid, $search_id);
            $this->insert_content_into_entrepot($source_id, $ref, $date_import, "900", "a", 0, 0, $typedoclabel, $recid, $search_id);
            $this->insert_content_into_entrepot($source_id, $ref, $date_import, "856", "u", 0, 0,substr($url,0,strlen($url)-7). rawurlencode(str_replace(" ","_",$record->title)), $recid, $search_id);
            $this->insert_content_into_entrepot($source_id, $ref, $date_import, "801", "b", 0, 0, $params["NAME"], $recid, $search_id);
            $this->rec_isbd_record($source_id, $ref, $recid);
            $this->n_recu++;
        }
    }

    //Fonction de recherche
    public function search($source_id,$query,$search_id) {
        global $base_path;

        $params=$this->get_source_params($source_id);
        $params_source=unserialize($params["PARAMETERS"]);

        $url=$params_source['url'];
        $limit=$params_source['limit'];
        $typedoclabel=$params_source['typedoclabel'];

        if (!$limit) $limit=100;

        foreach($query as $amterm) {
           switch ($amterm->ufield) {
               case 'XXX':
                   $criterias['q']= rawurlencode($amterm->values[0]);
                   break;
               default:
                   break;
           }
        }
        //Requête CURL au webservice...
        $get=$url."?action=query&list=search&srwhat=text&srsearch=".$criterias['q']."&format=json";
        //Appel Curl
        $curl =  new Curl();
        $curl->set_option('CURLOPT_SSL_VERIFYPEER',false);
        $result = $curl->get($get);
        if ($result) {
            $result=json_decode($result);
            $continue=(!empty($result->continue)?$result->continue:null);
            $result=$result->query;
            //Nombre :
            $total=($result->searchinfo->totalhits>$limit?$limit:$result->searchinfo->totalhits);
            $red=0;
            while ($red<$total) {
                for ($i=0; $i<count($result->search); $i++) {
                    $elt=$result->search[$i];
                    if ($elt) {
                        $this->rec_record($elt,$source_id,$search_id,$url,$typedoclabel);
                    }
                    $red++;
                    if ($red>$limit) break;
                }
                if ($red>$limit) break;
                if ($red<$total) {
                    if ($continue) {
                        $getc=$get;
                        foreach ($continue as $key=>$val) {
                            $getc.="&".$key."=".rawurlencode($val);
                        }
                        $result = $curl->get($getc);
                        if ($result) {
                            $result=json_decode($result);
                            if ($result->error) {
                                break;
                            }
                            $continue=(!empty($result->continue)?$result->continue:null);
                            $result=$result->query;
                        }
                    } else break;
                }
            }
        }
    }
}
