<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_oo.class.php,v 1.5 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/zip.class.php");

/**
 * Classe qui permet la gestion de l'indexation des fichiers OpenOffice
 */
class index_oo{
	
	public $fichier='';
	
	public function __construct($filename, $mimetype='', $extension=''){
		$this->fichier = $filename;
	}
	
	/**
	 * Méthode qui retourne le texte à indexer des docs OpenOffice
	 */
	public function get_text($filename){
		global $charset;
		$zip = new zip($filename);
		$texte = $zip->getFileContent("content.xml");			
		//On enlève toute les balises offices
		preg_match_all("(<([^<>]*)>)",$texte,$result);	
		for($i=0;$i<sizeof($result[0]);$i++){
			$texte = str_replace($result[0][$i]," ",$texte);
		}
		
		$texte = str_replace("&apos;","'",$texte);
		$texte = str_replace("&nbsp;"," ",$texte);
		if($charset != "utf-8"){
			$texte =  utf8_decode($texte);		
		}
		$texte = html_entity_decode($texte,ENT_QUOTES,$charset);
		return $texte;
		
	}
}
?>