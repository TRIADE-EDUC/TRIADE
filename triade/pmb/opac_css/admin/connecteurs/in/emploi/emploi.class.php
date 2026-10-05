<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: emploi.class.php,v 1.9.4.1 2025/04/16 12:16:50 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/search.class.php");

class emploi extends connector {

	//Variables internes pour la progression de la récupération des notices
	public $current_set;			//Set en cours de synchronisation
	public $total_sets;			//Nombre total de sets sélectionnés
	public $metadata_prefix;		//Préfixe du format de données courant
	public $n_recu;				//Nombre de notices reçues
	public $xslt_transform;		//Feuille xslt transmise
	public $sets_names;			//Nom des sets pour faire plus joli !!
	public $schema_config;

	/**
	 *
	 * {@inheritDoc}
	 * @see connector::get_id()
	 */
    public function get_id()
    {
    	return "emploi";
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::is_repository()
     */
	public function is_repository()
	{
	    return connector::REPOSITORY_YES;
	}

   public function source_get_property_form($source_id) {
    	global $charset;

    	$params=$this->get_source_params($source_id);
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		//URL
		if (!isset($url))
			$url = "http://travail-emploi.gouv.fr/etudes-recherche-statistiques-de,76/etudes-et-recherche,77/publications-dares,98/dares-analyses-dares-indicateurs,102";
		$form="<div class='row'>
			<div class='colonne3'>
				<label for='url'>".$this->msg["emploi_url"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='url' id='url' class='saisie-60em' value='".htmlentities($url,ENT_QUOTES,$charset)."'/>
			</div>
		</div>";

		return $form;
    }

    public function make_serialized_source_properties($source_id) {
    	global $url;
    	$t = array();
    	$t["url"]=stripslashes($url);
		$this->sources[$source_id]["PARAMETERS"]=serialize($t);
	}

	//Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
	public function fetch_default_global_values() {
		parent::fetch_default_global_values();
		$this->timeout=40;
		$this->repository=1;
		$this->ttl=60000;
	}

	public function cancel_maj($source_id) {
		return true;
	}

	public function break_maj($source_id) {
		return true;
	}

    public function parse_xml($ch,$data) {
		$notices=explode("6",$data);
		print $notices[1];
    	return strlen($data);
	}

	public function search($source_id,$query,$search_id) {
		global $base_path,$charset;

		$params=$this->get_source_params($source_id);
		$this->fetch_global_properties();
		if ($params["PARAMETERS"]) {
			//Affichage du formulaire avec $params["PARAMETERS"]
			$vars=unserialize($params["PARAMETERS"]);
			foreach ($vars as $key=>$val) {
				global ${$key};
				${$key}=$val;
			}
		}
		if (!isset($url)) {
			$this->error_message = $this->msg["emploi_unconfigured"];
			$this->error = 1;
			return;
		}
		$boolsearch="";
		foreach ($query as $element) {
			if($boolsearch)$boolsearch.=" ";
			$boolsearch.= implode(" ", $element->values);
		}
		$boolsearch=str_replace(" ", "+", $boolsearch);

		//$addr=$url."/search?value21=true&value22=true&discontin=factbooks&value1=".rawurlencode($boolsearch)."&option1=titleAbstract&option18=sort&site=fr&form_name=quick&option21=discontinued&option22=excludeKeyTableEditions&option19=content_type&value19=books";
		$ch = curl_init();

		//$addr="http://travail-emploi.gouv.fr/etudes-recherche-statistiques-de,76/etudes-et-recherche,77/publications-dares,98/dares-analyses-dares-indicateurs,102/";
		$addr=$url;
		// configuration des options CURL
		curl_setopt($ch, CURLOPT_URL, $addr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		configurer_proxy_curl($ch,$addr);
	 	$html=curl_exec($ch);
 		if (!$html) {
 			$sortir=1;
 		} else {
 			if ($charset=="utf-8")  $html=encoding_normalize::utf8_normalize($html);

	 		$notice_list=$this->get_field_betwen_2sep($html,"<h2 class=\"smaller\">","</ul>");

	 		$notice_list=$this->get_field_betwen_2sep($notice_list,"<ul>","<ul>");

	 		$sep_notices="</li>\n";
	 		$notices_html=explode($sep_notices,$notice_list);
	 		//print printr($notices_html);
	 		foreach($notices_html as $notice_html){
	 			$data_notice=array();

		 		$link=$this->get_field_betwen_2sep($notice_html,"<a href=\"","\">");
		 		$link=	"http://travail-emploi.gouv.fr/".$link;

		 		curl_setopt($ch, CURLOPT_URL, $link);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				configurer_proxy_curl($ch,$link);
 				$html_notice_complete=curl_exec($ch);

	//	 		http://travail-emploi.gouv.fr/IMG/pdf/2012-035.pdf
		 		if (!$html_notice_complete) {
		 			continue;
		 		}
		 		if ($charset=="utf-8")  $html_notice_complete=encoding_normalize::utf8_normalize($html_notice_complete);
		 		$data_notice["Url_notice"]=$link;

		 		$html_notice_complete=$this->get_field_from_sep($html_notice_complete,"<div class=\"gris clearfix\">");

			 	$data_notice["Publication_Date"]=$this->get_field_betwen_2sep($html_notice_complete,"<span class=\"date\">","</span>");
			 	$data_notice["Title"]=$this->get_field_betwen_2sep($html_notice_complete,"<h1>","</h1>");

		 		$data_notice["Abstract"]=$this->get_field_betwen_2sep($html_notice_complete,"<div class=\"texteencadre-spip spip\">","</div>",1);

				$zone_docnum=$this->get_field_from_sep($html_notice_complete," spip_documents spip_lien_ok\">");
		 		$link_docnum=$this->get_field_betwen_2sep($zone_docnum,"<a href=\"","\" ");
		 		if($link_docnum){
		 			$data_notice["doc_links"][0]["link"]="http://travail-emploi.gouv.fr/".$link_docnum;
			 		$data_notice["id"]=$this->get_field_betwen_2sep($link_docnum,"IMG/pdf/",".pdf");
		 		}

	 		//	printr ($data_notice);
	 			$this->rec_record($this->notice_2_uni($data_notice),$source_id,$search_id);
	 			if($nb++>20){
	 				break;
	 			}
 			}
 		}

 		curl_close($ch);

	}

	public function notice_2_uni($nt) {

		$unimarc=array();
		$unimarc["001"][0]=$nt["id"];

		$unimarc["200"][0]["a"][0]=$nt["Title"];

		//Editeurs
		if ($nt["Publication"]) $unimarc["210"][0]["c"][0]=$nt["Publication"];
		if ($nt["Publication_Date"]) $unimarc["210"][0]["d"][]=$nt["Publication_Date"];

		// DOI
		if ($nt["DOI"]){
			$unimarc["014"][0]["a"][0]=$nt["DOI"];
			$unimarc["014"][0]["b"][0]="DOI";
		}

		if ($nt["Affiliation"])  $unimarc["300"][0]["a"][0]=$nt["Affiliation"];

		//Résumé
		if ($nt["Abstract"])  $unimarc["330"][0]["a"][0]=$nt["Abstract"];

		if (count($nt["numberofpages"])) $unimarc["215"][0]["a"][]=$nt["numberofpages"];

		//Auteurs
		$aut=array();
		if (count($nt["Authors"])) {
			if (count($nt["Authors"])>1) $autf="701"; else $autf="700";
			for ($i=0; $i<count($nt["Authors"]); $i++) {

				$name_surname=explode(",",$nt["Authors"][$i]);
				if(count($name_surname)==2) {
					$aut[$i]["a"][0]=$name_surname[0];
					$aut[$i]["b"][0]=$name_surname[1];
				} else{
					$aut[$i]["a"][0]=$nt["Authors"][$i];
				}
			//	$aut[$i]["4"][0]="070";
			}
			$unimarc[$autf]=$aut;
		}
		if ($nt["Url_notice"]){
			$unimarc["856"][0]["u"][0]=$nt["Url_notice"];
		}

		// Link doc num
		if(count($nt["doc_links"])) {
			$i=0;
			foreach($nt["doc_links"] as $key =>$val){
				$unimarc["897"][$i]["a"][0]=$val["link"];
				if($val["label"])
					$unimarc["897"][$i]["b"][0]=$val["label"];
				else
					$unimarc["897"][$i]["b"][0]=$key;
				$i++;
			}
		}
		// Keywords
		if ($nt["Keywords"])  $unimarc["610"][0]["a"][0]=$nt["Keywords"];
		// Origin
		if ($nt["Origin"])  $unimarc["801"][0]["b"][0]=$nt["Origin"];

		return $unimarc;
	}

	public function rec_record($record,$source_id) {
		global $charset,$base_path,$url,$search_index;

		$date_import=date("Y-m-d H:i:s",time());

		//Recherche du 001
		$ref=$record["001"][0];
		//Mise à jour
		if ($ref) {
			//Si conservation des anciennes notices, on regarde si elle existe
			if (!$this->del_old) {
				$ref_exists = $this->has_ref($source_id, $ref);
				if($ref_exists) return 1;
			}
			//Si pas de conservation des anciennes notices, on supprime
			if ($this->del_old) {
				$this->delete_from_entrepot($source_id, $ref);
				$this->delete_from_external_count($source_id, $ref);
			}
			//Si pas de conservation ou reférence inexistante
			if (($this->del_old)||((!$this->del_old)&&(!$ref_exists))) {
				//Insertion de l'entête
				$n_header["rs"]="*";
				$n_header["ru"]="*";
				$n_header["el"]="1";
				$n_header["bl"]="m";
				$n_header["hl"]="0";
				$n_header["dt"]=$this->types[$search_index[$url][0]];
				if (!$n_header["dt"]) $n_header["dt"]="a";

				$n_header["001"]=$record["001"][0];
				//Récupération d'un ID
				$recid = $this->insert_into_external_count($source_id, $ref);

				foreach($n_header as $hc=>$code) {
					$this->insert_header_into_entrepot($source_id, $ref, $date_import, $hc, $code, $recid);
				}

				$field_order=0;
				foreach ($record as $field=>$val) {
					for ($i=0; $i<count($val); $i++) {
						if (is_array($val[$i])) {
							foreach ($val[$i] as $sfield=>$vals) {
								for ($j=0; $j<count($vals); $j++) {
									$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, $sfield, $field_order, $j, $vals[$j], $recid);
								}
							}
						} else {
							$this->insert_content_into_entrepot($source_id, $ref, $date_import, $field, '', $field_order, 0, $val[$i], $recid);
						}
						$field_order++;
					}
				}
				$this->rec_isbd_record($source_id, $ref, $recid);
				$this->n_recu++;
			}
		}
	}

	public function get_field_from_sep($chaine, $deb,$html_decode=0,$keep_tags=""){
		global $charset;
		$i_deb=strpos($chaine,$deb);
		if ($i_deb === false) return "";
		$i_deb+=strlen($deb);
		if($html_decode){
			//return html_entity_decode(substr($chaine,$i_deb),ENT_QUOTES,$charset);
			return html_entity_decode(strip_tags(substr($chaine,$i_deb),$keep_tags),ENT_QUOTES,$charset);
		}else
			return substr($chaine,$i_deb);

	}

	public function get_field_betwen_2sep($chaine, $deb,$end,$html_decode=0,$keep_tags=""){
		global $charset;
		$i_deb=strpos($chaine,$deb);
		if ($i_deb === false) return "";
		$i_deb+=strlen($deb);
		$chaine_deb=substr($chaine,$i_deb);
		$i_end=strpos($chaine_deb,$end);
		if ($i_end === false) return "";
		if($html_decode){
			// return html_entity_decode(substr($chaine_deb,0,$i_end),ENT_QUOTES,$charset);
			return html_entity_decode(strip_tags(substr($chaine_deb,0,$i_end),$keep_tags),ENT_QUOTES,$charset);
		}else
			return substr($chaine_deb,0,$i_end);
	}
}// class end


