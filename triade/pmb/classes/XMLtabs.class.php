<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: XMLtabs.class.php,v 1.3 2017-06-30 14:08:17 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class XMLtabs {
	
	public $analyseur;
	public $fichierXml;
	public $table;

	// constructeur
	public function __construct($fichier, $s=1) {
		$this->fichierXml = $fichier;
		$this->s = $s;
		$this->flag_order = false;		
	}
		                
	//Méthodes
	public function debutBalise($parser, $nom, $attributs) {
		global $_starttag; 
		$_starttag=true;
		
		if($nom == 'TAB' && $attributs['NAME'] && $attributs['LABEL'])
			$this->table[$attributs['NAME']] =array("label"=>$attributs['LABEL'],"desc"=>$attributs['DESC']);
		if($nom == 'TABS') {
			$this->fav = array();
		}
	}
		
	public function finBalise($parser, $nom) {}

	public function texte($parser, $data) {}

 // Modif Armelle Nedelec recherche de l'encodage du fichier xml et transformation en charset'
 	public function analyser() {
 		global $charset;
		if (!($fp = @fopen($this->fichierXml, "r"))) {
			die("impossible d'ouvrir le fichier XML $this->fichierXml");
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
	}
}