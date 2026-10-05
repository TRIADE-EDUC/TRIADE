<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sudoc.class.php,v 1.9.12.1 2025/04/16 12:16:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path,$base_path, $include_path;
require_once($class_path."/connecteurs.class.php");
require_once($class_path."/curl.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($base_path.'/classes/iso2709.class.php');
require_once($include_path."/parser.inc.php");

class sudoc extends connector {

	//Variables internes pour la progression de la récupération des notices
	public $del_old;				//Supression ou non des notices dejà existantes

	public $profile;				//Profil Amazon
	public $match;					//Tableau des critères UNIMARC / AMAZON
	public $current_site;			//Site courant du profile (n°)
	public $searchindexes;			//Liste des indexes de recherche possibles pour le site
	public $current_searchindex;	//Numéro de l'index de recherche de la classe
	public $match_index;			//Type de recherche (power ou simple)
	public $types;					//Types de documents pour la conversino des notices

	//Résultat de la synchro
	public $error;					//Y-a-t-il eu une erreur
	public $error_message;			//Si oui, message correspondant

    /**
     *
     * {@inheritDoc}
     * @see connector::get_id()
     */
    public function get_id()
    {
    	return "sudoc";
    }

    /**
	 *
	 * {@inheritDoc}
	 * @see connector::enrichment_is_allow()
	 */
	public function enrichment_is_allow()
	{
	    return connector::ENRICHMENT_YES;
	}

	public function getEnrichmentHeader(){
		$header= array();
		$header[]= "<!-- Script d'enrichissement pour le Sudoc-->";
		return $header;
	}

	public function getTypeOfEnrichment($source_id){
		$type['type'] = array(
			array(
				'code' => "sudoc",
				'label' => $this->msg['sudoc']
			)
		);
		$type['source_id'] = $source_id;
		return $type;
	}

	public function build_error(){
		$enrichment= array();
		$enrichment['sudoc']['content'] = $this->msg['sudoc_no_infos'];
		$enrichment['source_label']= $this->msg['sudoc_enrichment_source'];
		return $enrichment;
	}

	public function getEnrichment($notice_id,$source_id,$type="",$enrich_params=array()){
		global $charset;

		$enrichment= array();
		$this->noticeToEnrich = $notice_id;

		// récupération du code sudoc (PPN) de la notice stocké dans le champ perso de type "resolve" avec pour label "SUDOC"
		$mes_pp= new parametres_perso("notices");
		$mes_pp->get_values($notice_id);
		$values = $mes_pp->values;
		foreach ( $values as $field_id => $vals ) {
			if($mes_pp->t_fields[$field_id]['TYPE'] == "resolve"){
				$field_options = $mes_pp->t_fields[$field_id]['OPTIONS'][0];
				foreach($field_options['RESOLVE'] as $resolve){
					if(strtoupper($resolve['LABEL'])=="SUDOC"){
						$infos = explode('|',$vals[0]);
						$ppn=$infos[0];
					}
				}
			}
		}
		if($ppn==""){
			return $this->build_error();
		}
		$url="carmin.sudoc.abes.fr";
		$port="210";
		$base="abes-z39-public";
		$format="unimarc";

		$term="@attr 1=12 @attr 2=3 \"$ppn\" ";
		$id = yaz_connect("$url:$port/$base", array("piggyback"=>false));
		yaz_range ($id, 1, 1);
		yaz_syntax($id,strtolower($format));
		yaz_search($id,"rpn",$term);

		$options=array("timeout"=>45);

		//Override le timeout du serveur mysql, pour être sûr que le socket dure assez longtemps pour aller jusqu'aux ajouts des résultats dans la base.
		$sql = "set wait_timeout = 120";
		pmb_mysql_query($sql);

		yaz_wait($options);

		$error = yaz_error($id);
		$error_info = yaz_addinfo($id);
		if (!empty($error)) {
			yaz_close ($id);
			return $this->build_error();
		} else {
			$hits = yaz_hits($id);
			$hits = intval($hits);
			if($hits){
				$rec = yaz_record($id,1,"raw");
				$record = new iso2709_record($rec);
				if(!$record->valid()) {
					yaz_close ($id);
					return $this->build_error();
				}

				$lines="";

				$document->document_type = $record->inner_guide['dt'];
				$document->bibliographic_level = $record->inner_guide['bl'];
				$document->hierarchic_level = $record->inner_guide['hl'];
				if ($document->hierarchic_level=="") {
					if ($document->bibliographic_level=="s") $document->hierarchic_level="1";
					if ($document->bibliographic_level=="m") $document->hierarchic_level="0";
				}

				$indicateur = array();

				$cle_list= array();
				for ($i=0;$i<count($record->inner_directory);$i++) {
					$cle=$record->inner_directory[$i]['label'];

					$indicateur[$cle][]=substr($record->inner_data[$i]['content'],0,2);

					$field_array=$record->get_subfield_array_array($cle);

					$line="";
					if(!$cle_list[$cle]){
						foreach($field_array as $field){
							$line.=$cle."  ";
							foreach($field as $ss_field){
								$line.="$".$ss_field["label"].$ss_field["content"];
							}
							$line.="<br>";
						}
					}
					$cle_list[$cle]=1;
					$lines.=$line;
				}
				if($lines==""){
					yaz_close ($id);
					return $this->build_error();
				}
			}else{
				yaz_close ($id);
				return $this->build_error();
			}
		}
		yaz_close ($id);

		$enrichment['sudoc']['content'] = $lines;
		$enrichment['source_label']= $this->msg['sudoc_enrichment_source'];
		return $enrichment;
	}
}