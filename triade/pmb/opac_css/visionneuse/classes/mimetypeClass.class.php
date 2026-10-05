<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mimetypeClass.class.php,v 1.10.4.1 2025/01/30 09:08:07 tsamson Exp $

require_once("$visionneuse_path/classes/XMLClass.class.php");

class mimetypeClass extends XMLClass{
	public $defaultMimetype=array();	//tableau associatif (mimetype => class)  
	public $repertoire;				//répertoire des classes d'affichages
	public $currentRep;				//rep courant 
	public $analyseur;					//parseur
	public $mimetypeFiles;				//tableau de l'ensemble des manifest   (class => manifest)
	public $classMimetypes;			//tableau associatif des mimetypes supportés par chaque classe (class => (mimetype1,mimetype2,...))
	public $mimetypeClasses;			//tableau associatif des classes dispo pour chaque mimetype (mimetype => (class1,class2,...))
	public $descriptions;				//tableau associatif des descriptions de chaque classe (class => desc)
	public $screenshoots;				//tableau associatif des screenshoots (class => url) 
	public $currentClass;
	
	
    public function __construct($repertoire){
    	$this->repertoire = $repertoire;
    	$this->lireRep($this->repertoire);
    	$this->invertMimetypeTab();
    }
    
	//Méthodes
	public function debutBalise($parser, $nom, $attributs){
		global $_starttag; $_starttag=true;
		if($nom == 'MANIFEST' && $attributs['NAME']){
			$this->currentClass = $attributs['NAME'];
		}
		if($nom == 'MIMETYPE' && $attributs['NAME']){
			$this->classMimetypes[$this->currentClass][]=$attributs['NAME'];
		}
		if($nom == 'DESC' && $attributs['MSG']){
			$this->descriptions[$this->currentClass]=$attributs['MSG'];
		}
		if($nom == 'SCREENSHOOT' && $attributs['URL']){
			$this->screenshoots[$this->currentClass]=$attributs['URL'];
		}
	}
	
	//on fait tout dans la méthode débutBalise....
	public function finBalise($parser, $nom){//besoin de rien
	}   
	public function texte($parser, $data){//la non plus
	}
	
	public function analyser($file=""){
 		global $charset;
		
		if (!($fp = @fopen($file , "r"))) {
		    die(htmlentities("impossible d'ouvrir le fichier $file", ENT_QUOTES, $charset));
		}
		$data = fread ($fp,filesize($file));

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
			die( sprintf( "erreur XML %s à la ligne: %d ( $file )\n\n",
			xml_error_string(xml_get_error_code( $this->analyseur ) ),
			xml_get_current_line_number( $this->analyseur) ) );
		}

		xml_parser_free($this->analyseur);
		unset($this->analyseur);
 	}
 
  	public function lireRep($rep){
  		
		$dh = opendir($rep);
		if (!$dh) return;
		while (($file = readdir($dh)) !== false){
			//on évite les repertoires système...
			if ($file != "." && $file != "..") {
				//si c'est un répertoire, on est sur un sous-dossier de mimtypes qui contient une classe et son manisfest
				if(is_dir($rep.$file)){
					if (file_exists($rep.$file."/manifest.xml")){
						$this->analyser($rep.$file."/manifest.xml");
					}
				}
			}
		}	
		closedir($dh);
	}

	public function invertMimetypeTab(){
		foreach($this->classMimetypes as $class => $mimetypes){
			foreach($mimetypes as $mimetype){
				$exist = false;
				if (!isset($this->mimetypeClasses[$mimetype])) {
					$this->mimetypeClasses[$mimetype] = array();
				}
				if (is_countable($this->mimetypeClasses[$mimetype])) {
					for($i=0 ; $i<sizeof($this->mimetypeClasses[$mimetype]);$i++){
						if($this->mimetypeClasses[$mimetype][0] == $class)
						$exist = true;
					}
				}
				if ($exist === false) $this->mimetypeClasses[$mimetype][] = $class;
			}
		}
	}
}
?>