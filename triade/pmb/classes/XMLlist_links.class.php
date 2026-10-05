<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: XMLlist_links.class.php,v 1.15.2.1 2024/11/06 16:23:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classe de gestion des documents XML
require_once($class_path."/cache_factory.class.php");

class XMLlist_links extends XMLlist {

	public $inverse_of = array();	// Tableau des attributs inverseOf dans le fichier XML
	public $sens = '';					// Attribut sens dans le fichier XML

	// constructeur
	public function __construct($fichier, $s=1) {
		parent::__construct($fichier,$s);
	}


	//Méthodes
	public function debutBalise($parser, $nom, $attributs) {
		parent::debutBalise($parser, $nom, $attributs);
		global $_starttag;

		if($nom == 'ENTRY' && $attributs['INVERSEOF']){
			$this->inverse_of[$attributs['CODE']] = $attributs['INVERSEOF'];
		}
		$this->sens = 'flat';
		if($nom == 'ENTRY' && $attributs['SENS']){
			$this->sens = $attributs['SENS'];
		}
	}

	//Méthodes
	public function debutBaliseSubst($parser, $nom, $attributs) {
		global $_starttag;
		parent::debutBaliseSubst($parser, $nom, $attributs);

		if($nom == 'ENTRY' && $attributs['INVERSEOF']){
			$this->inverse_of[$attributs['CODE']] = $attributs['INVERSEOF'];
		}
		$this->sens = 'flat';
		if($nom == 'ENTRY' && $attributs['SENS']){
			$this->sens = $attributs['SENS'];
		}
		$table = $this->table;
		foreach($table as $sens => $infos){
			foreach($infos as $code => $label){
				if(isset($attributs['CODE']) && ($code == $attributs['CODE'])){
					unset($this->table[$sens][$code]);
					break;
				}
			}

		}
	}

	public function finBalise($parser, $nom) {
		parent::finBalise($parser, $nom);
		$this->sens = '';
	}

	public function finBaliseSubst($parser, $nom) {
		parent::finBaliseSubst($parser, $nom);
		$this->sens = '';
	}

	public function texte($parser, $data) {
		global $_starttag;
		if($this->current){
			if ($_starttag) {
				$this->table[$this->sens][$this->current] = $data;
				$_starttag=false;
			} else {
				$this->table[$this->sens][$this->current].= $data;
			}
		}
	}

	public function texteSubst($parser, $data) {
		global $_starttag;
		$this->flag_elt = true;
		if ($this->current) {
		if ($_starttag) {
				$this->table[$this->sens][$this->current] = $data;
				$_starttag=false;
			} else {
				$this->table[$this->sens][$this->current].= $data;
			}
		}
	}


 // Modif Armelle Nedelec recherche de l'encodage du fichier xml et transformation en charset'
 	public function analyser() {
 		global $charset;
 		global $base_path, $KEY_CACHE_FILE_XML;
 		global $pmb_display_errors;
		if (!($fp = @fopen($this->fichierXml, "r"))) {
		    if($pmb_display_errors) {
		        print_r("impossible d'ouvrir le fichier XML $this->fichierXml");
		    }
			return ;
		}
 		//vérification fichier pseudo-cache dans les temporaires
		$fileInfo = pathinfo($this->fichierXml);
		$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
		if($this->fichierXmlSubst && file_exists($this->fichierXmlSubst)){
			$tempFile = $base_path."/temp/XMLWithSubst".$fileName.".tmp";
			$with_subst=true;
		}else{
			$tempFile = $base_path."/temp/XML".$fileName.".tmp";
			$with_subst=false;
		}
		$dejaParse = false;

		$cache_php=cache_factory::getCache();
		$key_file="";
		if ($cache_php) {
			$key_file=getcwd().$fileName.filemtime($this->fichierXml);
			if($this->fichierXmlSubst && file_exists($this->fichierXmlSubst)){
				$key_file.=filemtime($this->fichierXmlSubst);
			}
			$key_file=$KEY_CACHE_FILE_XML.md5($key_file);
			if($tmp_key = $cache_php->getFromCache($key_file)){
				if($tables = $cache_php->getFromCache($tmp_key)){
					if(count($tables) == 3){
						fclose($fp);
						$this->table = $tables[0];
						$this->inverse_of = $tables[1];
						$this->attributes = $tables[2];
						$dejaParse = true;
					}
				}
			}
		}else{
			if(file_exists($tempFile)){
				//Le fichier XML original a-t-il été modifié ultérieurement ?
				if(filemtime($this->fichierXml)>filemtime($tempFile)){
					//on va re-générer le pseudo-cache
					unlink($tempFile);
				} else {
					//On regarde aussi si le fichier subst à été modifié après le fichier temp
					if($with_subst){
						if(filemtime($this->fichierXmlSubst)>filemtime($tempFile)){
							//on va re-générer le pseudo-cache
							unlink($tempFile);
						} else {
							$dejaParse = true;
						}
					}else{
						$dejaParse = true;
					}
				}
			}
			if($dejaParse){
				$tmp = fopen($tempFile, "r");
				$tables = unserialize(fread($tmp,filesize($tempFile)));
				fclose($tmp);
				if(count($tables) == 3){
					fclose($fp);
					$this->table = $tables[0];
					$this->inverse_of = $tables[1];
					$this->attributes = $tables[2];
				}else{
					unlink($tempFile);
					$dejaParse = false;
				}
			}
		}

		if(!$dejaParse){
			$this->table = array();
			$this->inverse_of = array();
			$this->attributes = array();
			$file_size=filesize ($this->fichierXml);
			$data = fread ($fp, $file_size);

	 		$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
			if (preg_match($rx, $data, $m)) $encoding = strtoupper($m[1]);
				else $encoding = "ISO-8859-1";

	 		$this->analyseur = xml_parser_create($encoding);
	 		xml_parser_set_option($this->analyseur, XML_OPTION_TARGET_ENCODING, $charset);
			xml_parser_set_option($this->analyseur, XML_OPTION_CASE_FOLDING, true);
			xml_set_object($this->analyseur, $this);
			xml_set_element_handler($this->analyseur, "debutBalise", "finBalise");
			xml_set_character_data_handler($this->analyseur, "texte");

			fclose($fp);

			if ( !xml_parse( $this->analyseur, $data, TRUE ) ) {
			    if($pmb_display_errors) {
			        print_r( sprintf( "erreur XML %s à la ligne: %d ( $this->fichierXml )\n\n",
    				xml_error_string(xml_get_error_code( $this->analyseur ) ),
    				xml_get_current_line_number( $this->analyseur) ) );
			    }
				return ;
			}

			xml_parser_free($this->analyseur);
			unset($this->analyseur);

			if ($fp = @fopen($this->fichierXmlSubst, "r")) {
				$file_sizeSubst=filesize ($this->fichierXmlSubst);
				if($file_sizeSubst) {
					$data = fread ($fp, $file_sizeSubst);
					fclose($fp);
			 		$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
					if (preg_match($rx, $data, $m)) $encoding = strtoupper($m[1]);
						else $encoding = "ISO-8859-1";
					$this->analyseur = xml_parser_create($encoding);
					xml_parser_set_option($this->analyseur, XML_OPTION_TARGET_ENCODING, $charset);
					xml_parser_set_option($this->analyseur, XML_OPTION_CASE_FOLDING, true);
					xml_set_object($this->analyseur, $this);
					xml_set_element_handler($this->analyseur, "debutBaliseSubst", "finBaliseSubst");
					xml_set_character_data_handler($this->analyseur, "texteSubst");
					if ( !xml_parse( $this->analyseur, $data, TRUE ) ) {
					    if($pmb_display_errors) {
					        print_r( sprintf( "erreur XML %s à la ligne: %d ( $this->fichierXmlSubst )\n\n",
    						xml_error_string(xml_get_error_code( $this->analyseur ) ),
    						xml_get_current_line_number( $this->analyseur) ) );
					    }
						return ;
					}
					xml_parser_free($this->analyseur);
					unset($this->analyseur);
				}
			}

			if ($this->s && is_array($this->table)) {
				reset($this->table);
				$tmp = array();
				if (is_array($this->order)) {
					asort($this->order);
				}
				foreach($this->table as $sens => $links){
					if (!$this->flag_order) {
						$tmp[$sens] = array_map("convert_diacrit",$this->table[$sens]); //On enlève les accents
						$tmp[$sens]=array_map("strtoupper",$tmp[$sens]);//On met en majuscule
						asort($tmp[$sens]);//Tri sur les valeurs en majuscule sans accent
						foreach ( $tmp[$sens] as $key => $value ) {
							$tmp[$sens][$key]= $this->table[$sens][$key];
						}
					} else {
						$tmp[$sens] = array();
						foreach ($this->order as $key =>$value){
							if (isset($links[$key])) {
								$tmp[$sens][$key] = $links[$key];
								unset($this->table[$sens][$key]);
							}
						}
						$tmp[$sens] = $tmp[$sens] + $this->table[$sens];
					}
				}
				$this->table=$tmp;
			}
			//on écrit le temporaire
			if ($key_file) {
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->table,$this->inverse_of,$this->attributes)));
				$cache_php->setInCache($key_file_content, array($this->table,$this->inverse_of,$this->attributes));
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
				$tmp = fopen($tempFile, "wb");
				fwrite($tmp,serialize(array($this->table,$this->inverse_of,$this->attributes)));
				fclose($tmp);
			}
		}
		if(is_resource($fp)) {
			@fclose($fp);
		}
	}
}
