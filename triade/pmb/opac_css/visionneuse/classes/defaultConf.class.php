<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: defaultConf.class.php,v 1.6 2019-05-09 10:35:37 ngantier Exp $

require_once("$visionneuse_path/classes/XMLClass.class.php");

class defaultConf extends XMLClass{
	public $defaultMimetype=array();	//tableau associatif mimetype => class  
	public $file;						//xml à parser
	public $analyseur;					//parseur
	
	
    public function __construct(){
    	global $visionneuse_path;
    	$this->file = "$visionneuse_path/includes/defaultClassMimetype.xml";
    	$this->analyser();
    }
    
	//Méthodes
	public function debutBalise($parser, $nom, $attributs){
		global $_starttag; $_starttag=true;
		if($nom == 'MIMETYPE' && $attributs['TYPE'] && $attributs['CLASS']){
			$this->defaultMimetype[$attributs['TYPE']] = $attributs['CLASS'];
		}
		if($nom == 'MIMETYPES'){
			$this->defaultMimetype = array();
		}
	}
	
	//on fait tout dans la méthode débutBalise....
	public function finBalise($parser, $nom){//besoin de rien
	}   
	public function texte($parser, $data){//la non plus
	}
	
	public function analyser($file=""){
 		global $charset;
		
		if (!($fp = @fopen($this->file , "r"))) {
		    die(htmlentities("impossible d'ouvrir le fichier $this->file", ENT_QUOTES, $charset));
		}
		$data = fread ($fp,filesize($this->file));

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
			die( sprintf( "erreur XML %s à la ligne: %d ( $this->file )\n\n",
			xml_error_string(xml_get_error_code( $this->analyseur ) ),
			xml_get_current_line_number( $this->analyseur) ) );
		}

		xml_parser_free($this->analyseur);
 	}
}
?>