<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_pdf.class.php,v 1.6 2017-08-10 09:19:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * Classe qui permet la gestion de l'indexation des fichiers PDF
 */
class index_pdf{
	
	public $fichier='';
	
	public function __construct($filename, $mimetype='', $extension=''){
		$this->fichier = $filename;
	}
	
	/**
	 * Méthode qui retourne le texte à indexer des pdf
	 */
	public function get_text($filename){
		global $charset;
		
		$texte = '';
		$fp = popen("pdftotext -enc UTF-8 ".$filename." -", "r");
		while(!feof($fp)){
			$line = fgets($fp,4096); 
			$texte .= $line;
			// Si trop gros, il faudra faire ceci, ou pas:
			// if(strlen($texte)>65536) break;
		}
		pclose($fp);
	
		if($charset != "utf-8"){
			return utf8_decode($texte);
		}
		return $texte;
	}
}
?>