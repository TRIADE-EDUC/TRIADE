<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_epub.class.php,v 1.4 2019/06/05 09:04:42 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/epubData.class.php");

/**
 * Classe qui permet la gestion de l'indexation des fichiers epub
 */
class index_epub{
	
	public $fichier='';
	
	public function __construct($filename, $mimetype='', $extension=''){
		$this->fichier = $filename;
	}
	
	/**
	 * Méthode qui retourne le texte à indexer des epub
	 */
	public function get_text($filename){
		global $charset;
		
		$epub=new epub_Data($this->fichier);
		return $epub->getFullTextContent($charset);
	}
}
?>