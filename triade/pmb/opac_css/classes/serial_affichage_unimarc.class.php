<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_affichage_unimarc.class.php,v 1.7 2022/01/10 10:23:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once("$class_path/notice_affichage_unimarc.class.php");

//on essaye d'afficher un périodique avec une notice de pério, bulletin ou article dans les entrepot
class serial_affichage_unimarc extends notice_affichage_unimarc {
	
	public function __construct($id, $liens, $cart=0, $to_print=0, $entrepots_localisations=array()){
		parent::__construct($id, $liens, $cart, $to_print, $entrepots_localisations);
	}

	// récupération des valeurs en table---------------------------------------
	public function fetch_data() {
		$requete = "SELECT source_id FROM external_count WHERE rid=".addslashes($this->notice_id);
		$myQuery = pmb_mysql_query($requete);
		$source_id = pmb_mysql_result($myQuery, 0, 0);
	
		$requete="select * from entrepot_source_".$source_id." where recid='".addslashes($this->notice_id)."' group by field_order,ufield,usubfield,subfield_order,value";
		$myQuery = pmb_mysql_query($requete);
		
		$notice= new stdClass();
		$lpfo="";
		$n_ed=-1;
		
		$exemplaires = array();
		$doc_nums = array();
		$cpt_notice_pperso=0;
		$notice->notice_pperso= array();
		$notice->tit1 = '';
		$notice->tit2 = '';
		$notice->tit3 = '';
		$notice->tit4 = '';
		$notice->index_l = '';
		if(pmb_mysql_num_rows($myQuery)) {
			$is_article = false;
			while ($l=pmb_mysql_fetch_object($myQuery)) {
				if (!$this->source_id) {
					$this->source_id=$l->source_id;
					$requete="select name from connectors_sources where source_id=".$l->source_id;
					$rsname=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($rsname)) $this->source_name=pmb_mysql_result($rsname,0,0);
				}
				//$this->unimarc[$l->ufield][$l->field_order][$l->usubfield][$l->subfield_order];
				switch ($l->ufield) {
					//dt
					case "dt":
						$notice->typdoc=$l->value;
						break;
					case "bl":
						if($l->value == 'a'){
							$notice->niveau_biblio=$l->value;
						} else $notice->niveau_biblio='m'; //On force le document au type monographie 					
						break;
					case "hl":					
						if($l->value == '2'){
							$notice->niveau_hierar=$l->value;
						} else $notice->niveau_hierar='0'; //On force le niveau à zéro
						break;
					//ISBN
					case "011":
						if ($l->usubfield=="a") $notice->code=$l->value;
						break;
					//Titres
					case "200":
						switch ($l->usubfield) {
							case "a":
								$notice->tit1.=($notice->tit1?" ":"").$l->value;
								break;
							case "c":
								$notice->tit2.=($notice->tit2?" ":"").$l->value;
								break;
							case "d":
								$notice->tit3.=($notice->tit3?" ":"").$l->value;
								break;
							case "e":
								$notice->tit4.=($notice->tit4?" ":"").$l->value;
								break;
						}
						break;
					//Editeur
					case "210":
					case "214":
					case "219":
						if($l->field_order!=$lpfo) {
							$lpfo=$l->field_order;
							$n_ed++;
						}
						switch ($l->usubfield) {
							case "a":
								$this->publishers[$n_ed]["city"]=$l->value;
								break;
							case "c":
								$this->publishers[$n_ed]["name"]=$l->value;
								break;
							case "d":
								$this->publishers[$n_ed]["year"]=$l->value;
								$this->year=$l->value;
								break;
						}
						break;
					//Collation
					case "215":
						switch ($l->usubfield) {
							case "a":
								$notice->npages=$l->value;
								break;
							case "c":
								$notice->ill=$l->value;
								break;
							case "d":
								$notice->size=$l->value;
								break;
							case "e":
								$notice->accomp=$l->value;
								break;
						}
						break;
					//Note generale
					case "300":
						$notice->n_gen[]=$l->value;
						break;
					//Note de contenu
					case "327":
						$notice->n_contenu[]=$l->value;
						break;
					//Note de resume
					case "330":
						$notice->n_resume[]=$l->value;
						break;
					//Serie ou Pério
					case "461":		
						switch($l->usubfield){
							case 'x':
								$this->perio_issn = $l->value;
							break;
							case 't':
								$this->parent_title = $l->value;
							break;
							case '9':
								$is_article = true;
						    break;
						}				
						break;
					//Bulletins
					case "463" :
						switch($l->usubfield){
							case 't':
								$notice->bulletin_titre = $l->value;
							break;
							case 'v':
								$this->parent_numero = $l->value;
							break;
							case 'd':
								$this->parent_aff_date_date = $l->value;
							break;
							case 'e':
								$this->parent_date = $l->value;
							break;
						}
						break;
					//Mots cles
					case "610":
						switch ($l->usubfield) {
							case "a":
								$notice->index_l.=($notice->index_l?" / ":"").$l->value;
								break;
						}
						break;
					//Indexations décimales..;
					case "676":
					case "686":
						switch ($l->usubfield) {
							case "a":
								$notice->indexint[] = $l->value;
								break;
						}
						break;					
						
					//URL
					case "856":
						switch ($l->usubfield) {
							case "u":
								$notice->lien=$l->value;
								break;
							case "q":
								$notice->eformat=$l->value;
								break;
							case "t":
								$notice->lien_texte=$l->value;
								break;
						}
						break;
						// champs perso notice
					case "900":
						switch ($l->usubfield) {
							case "a":
								if(!empty($notice->notice_pperso[$cpt_notice_pperso]['value'])){
									$cpt_notice_pperso++;
								}
								$notice->notice_pperso[$cpt_notice_pperso]['value']=$l->value;
								break;
							case "l":
								$notice->notice_pperso[$cpt_notice_pperso]['libelle']=$l->value;
								break;
							case "n":
								$notice->notice_pperso[$cpt_notice_pperso]['name']=$l->value;
								break;
							case "t":
								$notice->notice_pperso[$cpt_notice_pperso]['type']=$l->value;
								break;
						}
						break;
					case "996":
						$exemplaires[$l->field_order][$l->usubfield] = $l->value; 
						break;
					//Thumbnail
					case "896":
						switch ($l->usubfield) {
							case "a":
								$notice->thumbnail_url=$l->value;
						}
						break;
					//Documents numériques
					case "897":
						$doc_nums[$l->field_order][$l->usubfield] = $l->value;
						break;
				}
			}
		}
		$this->exemplaires = $exemplaires;
		$this->docnums = $doc_nums;
		
		$this->notice=$notice;
		if (!$this->notice->typdoc) $this->notice->typdoc='a';
		
		// serials : si article
		//if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) 
		//$this->get_bul_info();	
		
		$this->fetch_categories() ;
	
// 		$this->fetch_auteurs() ;
		$this->fetch_langues(0) ;
		$this->fetch_langues(1) ;
		
		//on a fait un jolie fetch_data, on regarde ce qu'on a récupéré pour remettre en forme...
		switch($this->notice->niveau_biblio.$this->notice->niveau_hierar){
			//un article !
			case "a2" :
 				//highlight_string(print_r($this,true));
				$tmp = $this->notice;
				$this->notice->tit1 = $this->parent_title;
				$this->parent_title = "";
				$this->notice->code = $this->perio_issn;
				$this->perio_issn = "";
				$this->notice->niveau_biblio = "s";
				$this->notice->niveau_hierar = "1";
				$this->notice->npages = "";
				$this->parent_numero = "";
				$this->year = "";
				$this->notice->n_gen = array();
				$this->notice->lien = "";
				$this->parent_aff_date_date ="";
				unset($this->publishers[0]['year']);
				break;
			//un bulletin
			case "b2" :
			case "s2" :
				break;
			//des fois c'est simple...
			case "s1" :
			default :
				break;
			
		}
		return pmb_mysql_num_rows($myQuery);
	} // fin fetch_data

}
