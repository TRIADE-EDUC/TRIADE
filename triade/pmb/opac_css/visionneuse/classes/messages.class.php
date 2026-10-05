<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: messages.class.php,v 1.10 2020/04/24 07:32:34 dgoron Exp $


require_once($class_path."/cache_factory.class.php");

class message {
	
	public $analyseur;
	public $fichierXml;
	public $fichierXmlSubst; // nom du fichier XML de substitution au cas où.
	public $current;
	public $table;
	public $tablefav;
	public $flag_fav;
	public $s;
	public $flag_elt ; // pour traitement des entrées supprimées
	public $flag_order;
	public $order;

	// constructeur
	public function __construct($fichier, $s=1) {
		$this->fichierXml = $fichier;
		$this->fichierXmlSubst = str_replace(".xml", "", $fichier)."_subst.xml" ;
		$this->s = $s;
		$this->flag_order = false;
		$this->analyser();
	}

	//Méthodes
	public function debutBalise($parser, $nom, $attributs) {
		global $_starttag; $_starttag=true;
		if($nom == 'ENTRY' && $attributs['CODE'])
			$this->current = $attributs['CODE'];
		if($nom == 'ENTRY' && !empty($attributs['ORDER'])) {
			$this->flag_order = true;
			$this->order[$attributs['CODE']] =  $attributs['ORDER'];
			}
		if($nom == 'XMLlist') {
			$this->table = array();
			$this->fav = array();
		}
	}
	
	//Méthodes
	public function debutBaliseSubst($parser, $nom, $attributs) {
		global $_starttag; $_starttag=true;
		if($nom == 'ENTRY' && $attributs['CODE']) {
			$this->flag_elt = false ;
			$this->current = $attributs['CODE'];
			}
		if($nom == 'ENTRY' && $attributs['ORDER']) {
			$this->flag_order = true;
			$this->order[$attributs['CODE']] =  $attributs['ORDER'];
			}
		if($nom == 'ENTRY' && $attributs['FAV']) {
			$this->flag_fav =  $attributs['FAV'];
			}
	}
	
	public function finBalise($parser, $nom) {
		// ICI pour affichage des codes des messages en dur 
		if (isset($_SESSION["CHECK-MESSAGES"]) && $_SESSION["CHECK-MESSAGES"]==1 && strpos($this->fichierXml, "messages"))
			$this->table[$this->current] = "__".$this->current."##".$this->table[$this->current]."**";
		$this->current = '';
		}

	public function finBaliseSubst($parser, $nom) {
		// ICI pour affichage des codes des messages en dur 
		if ($_SESSION["CHECK-MESSAGES"]==1 && strpos($this->fichierXml, "messages"))
			$this->table[$this->current] = "__".$this->current."##".$this->table[$this->current]."**";
		if ((!$this->flag_elt) && ($nom=='ENTRY')) unset($this->table[$this->current]) ;
		$this->current = '';
		$this->flag_fav =  false;
		}
	
	public function texte($parser, $data) {
		global $_starttag; 
		if($this->current)
			if ($_starttag) {
				$this->table[$this->current] = $data;
				$_starttag=false;
			} else $this->table[$this->current] .= $data;
		}

	public function texteSubst($parser, $data) {
		global $_starttag; 
		$this->flag_elt = true ;
		if ($this->current) {
			if ($_starttag) {
				$this->table[$this->current] = $data;
				$_starttag=false;
			} else $this->table[$this->current] .= $data;
			$this->tablefav[$this->current] = $this->flag_fav;
		}
	}
	

 // Modif Armelle Nedelec recherche de l'encodage du fichier xml et transformation en charset'
 	public function analyser() 
 	{
 		global $charset,$visionneuse_path,$KEY_CACHE_FILE_XML;
 		$fileInfo = pathinfo($this->fichierXml);
		$fileName = preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset);
		if($this->fichierXmlSubst && file_exists($this->fichierXmlSubst)){
			$tempFile = $visionneuse_path."/temp/XMLWithSubst".$fileName.".tmp";
			$with_subst=true;
		}else{
			$tempFile = $visionneuse_path."/temp/XML".$fileName.".tmp";
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
 					if(count($tables) == 1){
	 					$this->table = $tables[0];
 						$dejaParse = true;
 					}
 				}
 			}
 		}else{
	 		if (file_exists($tempFile) ) {
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
	 		if ($dejaParse) {
	 			$tmp = fopen($tempFile, "r");
	 			$tables = unserialize(fread($tmp,filesize($tempFile)));
	 			fclose($tmp);
	 			if(count($tables) == 1){
	 				$this->table = $tables[0];	 				
	 			}else{
	 				unlink($tempFile);
	 				$dejaParse = false;
	 			}
	 		}
 		}
 		
 		if(!$dejaParse){
			if (!($fp = @fopen($this->fichierXml, "r"))) {
			    die(htmlentities("impossible d'ouvrir le fichier XML $this->fichierXml", ENT_QUOTES, $charset));
			}
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
				die( sprintf( "erreur XML %s à la ligne: %d ( $this->fichierXml )\n\n",
				xml_error_string(xml_get_error_code( $this->analyseur ) ),
				xml_get_current_line_number( $this->analyseur) ) );
			}
	
			xml_parser_free($this->analyseur);
			unset($this->analyseur);
	
			if ($fp = @fopen($this->fichierXmlSubst, "r")) {
				$file_sizeSubst=filesize ($this->fichierXmlSubst);
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
					die( sprintf( "erreur XML %s à la ligne: %d ( $this->fichierXmlSubst )\n\n",
					xml_error_string(xml_get_error_code( $this->analyseur ) ),
					xml_get_current_line_number( $this->analyseur) ) );
					}
				xml_parser_free($this->analyseur);
				unset($this->analyseur);
				}
			if ($this->s) {
				reset($this->table);
				$tmp=array();
				$tmp=array_map("convert_diacrit",$this->table);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
	       			$tmp[$key]=$this->table[$key];//On reprend les bons couples clé / libellé
				}
				$this->table=$tmp;
			}
			if($this->flag_order == true){
				$table_tmp = array();
				asort($this->order);
				foreach ($this->order as $key =>$value){
					$table_tmp[$key] = $this->table[$key];
					unset($this->table[$key]);
				}
				$this->table = array_merge($table_tmp,$this->table);
			}
			//on écrit le temporaire
			if ($key_file) {
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->table)));
				$cache_php->setInCache($key_file_content, array($this->table));
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
				$tmp = fopen($tempFile, "wb");
				fwrite($tmp,serialize(array($this->table)));
				fclose($tmp);
			}
 		}
	}
}