<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: XMLlist.class.php,v 1.45 2024/01/26 10:35:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// classe de gestion des documents XML
require_once($class_path."/cache_factory.class.php");

if ( ! defined( 'XML_LIST_CLASS' ) ) {
  define( 'XML_LIST_CLASS', 1 );

class XMLlist {
	
	public $analyseur;
	public $fichierXml;
	public $fichierXmlSubst; // nom du fichier XML de substitution au cas où.
	public $current;
	public $table;
	public $table_js;
	public $tablefav;
	public $flag_fav;
	public $s;
	public $no_cache; // rechargement, même si cache
	public $flag_elt ; // pour traitement des entrées supprimées
	public $flag_order;
	public $order;
	public $js_group;
	public $attributesToParse=array();
	public $attributes=array();
	public static $ignore_subst_file = false;

	// constructeur
	public function __construct($fichier, $s=1, $no_cache=0) {
		$this->fichierXml = $fichier;
		if(static::$ignore_subst_file) {
			$this->fichierXmlSubst = $fichier ;
		} else {
			$this->fichierXmlSubst = str_replace(".xml", "", $fichier)."_subst.xml" ;
		}
		$this->s = $s;
		$this->no_cache=$no_cache;
		$this->flag_order = false;
	}
		                

	//Méthodes
	public function debutBalise($parser, $nom, $attributs) {
		global $_starttag; $_starttag=true;
		if($nom == 'ENTRY' && $attributs['CODE'])
			$this->current = $attributs['CODE'];
		if($nom == 'ENTRY' && isset($attributs['ORDER'])) {
			$this->flag_order = true;
			$this->order[$attributs['CODE']] =  $attributs['ORDER'];
		}	
		if($nom == 'ENTRY' && isset($attributs['JS'])){
			$this->js_group = $attributs['JS'];
		}
		foreach ($this->attributesToParse as $attribute){
			if ($nom == 'ENTRY' && isset($attribute['default_value'])) {
				$this->attributes[$attributs['CODE']][$attribute['name']] = $attribute['default_value'];
			}
			if ($nom == 'ENTRY' && isset($attributs[$attribute['name']])){
				$this->attributes[$attributs['CODE']][$attribute['name']]=$attributs[$attribute['name']];
			}
		}
		if($nom == 'XMLlist') {
			$this->table = array();
			$this->fav = array();
		}
	}
	
	/**
	 * Définit une série d'attributs supplémentaires à parser
	 * @param array $attributes array('name','default_value')
	 */
	public function setAttributesToParse($attributes=array()){
		$this->attributesToParse=$attributes;
	}

	public function getAttributes(){
		return $this->attributes;
	}
	  
	//Méthodes
	public function debutBaliseSubst($parser, $nom, $attributs) {
		global $_starttag; $_starttag=true;
		if($nom == 'ENTRY' && $attributs['CODE']) {
			$this->flag_elt = false ;
			$this->current = $attributs['CODE'];
		}
		if($nom == 'ENTRY' && isset($attributs['ORDER'])) {
			$this->flag_order = true;
			$this->order[$attributs['CODE']] =  $attributs['ORDER'];
		}
		if($nom == 'ENTRY' && isset($attributs['JS'])){
			$this->js_group = $attributs['JS'];
		}
		foreach ($this->attributesToParse as $attribute){
			if ($nom == 'ENTRY' && isset($attribute['default_value'])) {
				$this->attributes[$attributs['CODE']][$attribute['name']] = $attribute['default_value'];
			}
			if ($nom == 'ENTRY' && !empty($attributs[$attribute['name']])) {
				$this->attributes[$attributs['CODE']][$attribute['name']] = $attributs[$attribute['name']];
			}
		}
		if($nom == 'ENTRY' && isset($attributs['FAV'])) {
			$this->flag_fav =  $attributs['FAV'];
		}
	}
	
	public function finBalise($parser, $nom) {
	    global $check_messages;
	    
		// ICI pour affichage des codes des messages en dur
	    $check_messages = intval($check_messages);
	    if(isset($_SESSION["CHECK-MESSAGES"]) || $check_messages) {
	        if (($_SESSION["CHECK-MESSAGES"]==1 || $check_messages==1) && strpos($this->fichierXml, "messages")) {
	            if(isset($this->table[$this->current])) {
	               $this->table[$this->current] = "__".$this->current."##".$this->table[$this->current]."**";
	            }
	        }
		}
		$this->current = '';
		$this->js_group = "";
	}

	public function finBaliseSubst($parser, $nom) {
	    global $check_messages;
	    
		// ICI pour affichage des codes des messages en dur
	    $check_messages = intval($check_messages);
	    if(isset($_SESSION["CHECK-MESSAGES"]) || $check_messages) {
	        if (($_SESSION["CHECK-MESSAGES"]==1 || $check_messages==1) && strpos($this->fichierXml, "messages")) {
	            if(isset($this->table[$this->current])) {
	               $this->table[$this->current] = "__".$this->current."##".$this->table[$this->current]."**";
	            }
	        }
		}
		if ((!$this->flag_elt) && ($nom=='ENTRY')) unset($this->table[$this->current]) ;
		$this->current = '';
		$this->js_group = "";
		$this->flag_fav =  false;
	}
	
	public function texte($parser, $data) {
		global $_starttag; 
		if($this->current)
			if ($_starttag) {
				if($this->js_group){
					$this->table_js[$this->js_group][$this->current] = $data;
				}else{
					$this->table[$this->current] = $data;
				}
				$_starttag=false;
			} else {
				if($this->js_group){
					$this->table_js[$this->js_group][$this->current].= $data;
				}else{
					$this->table[$this->current] .= $data;
				}
			}
		}

	public function texteSubst($parser, $data) {
		global $_starttag; 
		$this->flag_elt = true ;
		if ($this->current) {
			if ($_starttag) {
				if($this->js_group){
					$this->table_js[$this->js_group][$this->current] = $data;
				}else{
					$this->table[$this->current] = $data;
				}
				$_starttag=false;
			} else {
				if($this->js_group){
					$this->table_js[$this->js_group][$this->current].= $data;
				}else{
					$this->table[$this->current] .= $data;
				}
			}
			if ($this->flag_fav) $this->tablefav[$this->current] = $this->flag_fav;
		}
	}
	

 // Modif Armelle Nedelec recherche de l'encodage du fichier xml et transformation en charset'
 	public function analyser() 
 	{
 		global $charset, $KEY_CACHE_FILE_XML;
 		global $base_path, $class_path;
 		global $check_messages;
 		
		if (!($fp = @fopen($this->fichierXml, "r"))) {
			die(htmlentities("impossible d'ouvrir le fichier XML $this->fichierXml", ENT_QUOTES, $charset));
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
		if (!$this->no_cache){
		    $cache_php=cache_factory::getCache();
		    if($check_messages == 1 || $check_messages == -1) {
		        if(is_object($cache_php) && get_class($cache_php) == 'cache_apcu') {
		            $cache_php->clearCache();
		            $cache_php=false;
		        }
		    }
			$key_file="";
			if ($cache_php) {
				$key_file=getcwd().$fileName.filemtime($this->fichierXml);
				if($this->fichierXmlSubst && file_exists($this->fichierXmlSubst)){
					$key_file.=filemtime($this->fichierXmlSubst);
				}
				$key_file=$KEY_CACHE_FILE_XML.md5($key_file);
				if($tmp_key = $cache_php->getFromCache($key_file)){
					if($tables = $cache_php->getFromCache($tmp_key)){
						if(count($tables) == 4){
							fclose($fp);
							$this->table = $tables[0];
							$this->table_js = $tables[1];
							$this->tablefav = $tables[2];
							$this->attributes = $tables[3];
							$dejaParse = true;
						}
					}
				}
			}else{
				if (file_exists($tempFile)) {
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
				if($check_messages == 1 || $check_messages == -1) {
				    $dejaParse = false;
				}
				if($dejaParse){
					$tmp = fopen($tempFile, "r");
					$tables = unserialize(fread($tmp,filesize($tempFile)));
					fclose($tmp);
					if(count($tables) == 4){
						fclose($fp);
						$this->table = $tables[0];
						$this->table_js = $tables[1];
						$this->tablefav = $tables[2];
						$this->attributes = $tables[3];
					}else{
						unlink($tempFile);
						$dejaParse = false;
					}
					
				}
			}
		}
		
		if(!$dejaParse){
			$this->table = array();
			$this->table_js = array();
			$this->tablefav = array();
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
				die( sprintf( "erreur XML %s à la ligne: %d ( $this->fichierXml )\n\n",
				xml_error_string(xml_get_error_code( $this->analyseur ) ),
				xml_get_current_line_number( $this->analyseur) ) );
			}
	
			xml_parser_free($this->analyseur);
			unset($this->analyseur);
	
			if (is_file($this->fichierXmlSubst) && $fp = @fopen($this->fichierXmlSubst, "r")) {
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
						die( sprintf( "erreur XML %s à la ligne: %d ( $this->fichierXmlSubst )\n\n",
						xml_error_string(xml_get_error_code( $this->analyseur ) ),
						xml_get_current_line_number( $this->analyseur) ) );
						}
					xml_parser_free($this->analyseur);
					unset($this->analyseur);
				}	
			}
			if ($this->s && is_array($this->table)) {
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
			if(!static::$ignore_subst_file) {
				require_once($class_path.'/misc/files/misc_file_list.class.php');
				$path = substr($this->fichierXml, 0, strrpos($this->fichierXml, '/'));
				$filename = substr($this->fichierXml, strrpos($this->fichierXml, '/')+1);
				$misc_file_list = new misc_file_list($path, $filename);
				$this->table = $misc_file_list->apply_substitution($this->table);
			}
			//MB: La table "table_js" est composé de sous table, elle ne peut donc pas être triée avec "strtoupper"
			/*if ($this->s && is_array($this->table_js)) {
				reset($this->table_js);
				$tmp=array();
				$tmp=array_map("convert_diacrit",$this->table_js);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
					$tmp[$key]=$this->table_js[$key];//On reprend les bons couples clé / libellé
				}
				$this->table_js=$tmp;
			}*/
			if ($this->s && is_array($this->tablefav) && count($this->tablefav)) {
				reset($this->tablefav);
				$tmp=array();
				$tmp=array_map("convert_diacrit",$this->tablefav);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
					$tmp[$key]=$this->tablefav[$key];//On reprend les bons couples clé / libellé
				}
				$this->tablefav=$tmp;
			}
			if ($this->s && is_array($this->attributes)) {
				reset($this->attributes);
				$tmp=array();				
				foreach ( $this->attributes as  $key => $attributes ) {		
					$tmp_attributes=array();		
					$tmp_attributes=array_map("convert_diacrit",$attributes);//On enlève les accents
					$tmp_attributes=array_map("strtoupper",$tmp_attributes);//On met en majuscule
					asort($tmp);
					$tmp[$key]=$tmp_attributes;
				}
				$this->attributes=$tmp;				
			}			
			if($this->flag_order == true){
				$table_tmp = array();
				asort($this->order);
				foreach ($this->order as $key =>$value){
					if($this->table[$key]) {
					    $table_tmp[$key] = $this->table[$key] ?? "";
						unset($this->table[$key]);
					}
				}
				$this->table = $table_tmp + $this->table;//array_merge réécrivait les clés numériques donc problème.
				$table_tmp = array();
				asort($this->order);
				foreach ($this->order as $key =>$value){
					$table_tmp[$key] = $this->table_js[$key] ?? "";
					unset($this->table_js[$key]);
				}
				$this->table_js = $table_tmp + $this->table_js;//array_merge réécrivait les clés numériques donc problème.
				if (count($this->tablefav)) {
					$table_tmp = array();
					asort($this->order);
					foreach ($this->order as $key =>$value){
						if (isset($this->tablefav[$key])) {
						    $table_tmp[$key] = $this->tablefav[$key] ?? "";
							unset($this->tablefav[$key]);
						}
					}
					$this->tablefav = $table_tmp + $this->tablefav;//array_merge réécrivait les clés numériques donc problème.
				}
				$table_tmp = array();
				asort($this->order);
				foreach ($this->order as $key =>$value){
					$table_tmp[$key] = $this->attributes[$key] ?? "";
					unset($this->attributes[$key]);
				}
				$this->attributes = $table_tmp + $this->attributes;//array_merge réécrivait les clés numériques donc problème.
			}
			
			//on écrit le temporaire
			if ($key_file) {
				$key_file_content=$KEY_CACHE_FILE_XML.md5(serialize(array($this->table,$this->table_js,$this->tablefav,$this->attributes)));
				$cache_php->setInCache($key_file_content, array($this->table,$this->table_js,$this->tablefav,$this->attributes));
				$cache_php->setInCache($key_file,$key_file_content);
			}else{
			    if (file_exists($tempFile)) {
    				$tmp = fopen($tempFile, "wb");
    				fwrite($tmp,serialize(array($this->table,$this->table_js,$this->tablefav,$this->attributes)));
    				fclose($tmp);
			    }
			}
		}
	}
}

} # fin de définition
