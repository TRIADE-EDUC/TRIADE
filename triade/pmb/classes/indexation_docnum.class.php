<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexation_docnum.class.php,v 1.39 2021/12/23 08:16:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once($class_path."/XMLlist.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_pdf.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_html.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_txt.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_oo.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_bnf.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_mso.class.php");
require_once("$base_path/catalog/explnum/index_docnum/index_epub.class.php");
require_once("$class_path/curl.class.php");
require_once("$class_path/explnum.class.php");
require_once("$include_path/explnum.inc.php");
require_once($class_path."/sphinx/sphinx_explnums_indexer.class.php");

/**
 * Classe de gestion de l'indexation des documents numériques
 */
class indexation_docnum {
	
	public $id_explnum;
	public $fichier='';
	public $file_content='';
	public $file_url='';
	public $mimetype='';
	public $explnum_nomfichier='';
	public $ext='';
	public $os='';
	public $class_associee='';
	public $texte='';
	public $vignette='';
	private static $sphinx_indexer;
		
	
	/**
	 * Constructeur
	 */
	public function __construct($id, $texte=''){
		$this->id_explnum = $id;
		if(!$texte){
			$this->fetch_data();
			if ($this->file_content || $this->mimetype == 'URL') {
				$this->run_index();
			}
		} else {
			$this->texte = $texte;
		}
		
	}
	
	/**
	 * Parcours des données de la table explnum
	 */
	public function fetch_data(){
		$explnum = new explnum($this->id_explnum);
		
		if ($content = $explnum->get_file_content()) {
			$this->file_content = $content;
		}else{
			$this->file_content ="";
		}
		$this->file_url = $explnum->explnum_url;
		$this->mimetype = $explnum->explnum_mimetype;
		$this->explnum_nomfichier = $explnum->explnum_nomfichier;
			$this->ext = $explnum->explnum_extfichier;
	}
	
	/**
	 * Pour avoir la taille en octets
	 */
	public function return_bytes($val) {
	    $val = trim($val);
	    $last = strtolower($val[strlen($val)-1]);
	    switch($last) {
	        // Le modifieur 'G' est disponible depuis PHP 5.1.0
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }
	    return $val;
	}
	/**
	 * Exécution du processus d'indexation
	 */
	public function run_index(){
		if($this->mimetype == 'URL'){
			//récupération par cURL
			$this->get_file_from_curl($this->file_url);
			create_tableau_mimetype();
			$this->mimetype = trouve_mimetype($this->fichier);
			if(!$this->mimetype){
				//Test sur l'extension du fichier
				$this->ext = extension_fichier($this->file_url);
				$this->mimetype = trouve_mimetype($this->file_url,$this->ext);
			}
			if(!$this->mimetype && $this->explnum_nomfichier){
				//Test sur l'extension du fichier
				$this->ext = extension_fichier($this->explnum_nomfichier);
				$this->mimetype = trouve_mimetype($this->file_url,$this->ext);
			}
			if ($this->mimetype && !$this->ext) {
				$this->ext = extension_fichier($this->file_url);
			}
			if ($this->mimetype && $this->explnum_nomfichier && !$this->ext) {
				$this->ext = extension_fichier($this->explnum_nomfichier);
			}
			if (!$this->mimetype) {
				$this->mimetype = "URL";
			}
			global $prefix_url_image ;
			if ($prefix_url_image) $tmpprefix_url_image = $prefix_url_image; 
				else $tmpprefix_url_image = "./" ;
			if($tmp = construire_vignette('',"",$this->file_url)){
				$this->vignette = $tmp;
			}else{
				$this->vignette = construire_vignette('',$tmpprefix_url_image."images/mimetype/".icone_mimetype($this->mimetype, $this->ext));
			}
		} else {
			//récupération dans la base
			$this->get_file($this->file_content);
			create_tableau_mimetype();
			if(!$this->mimetype) $this->mimetype = trouve_mimetype($this->fichier);
			if(!$this->mimetype && $this->explnum_nomfichier){
				//Test sur l'extension du fichier
				$this->ext = extension_fichier($this->explnum_nomfichier);
				$this->mimetype = trouve_mimetype($this->fichier,$this->ext);
			}
		}
		if (file_exists($this->fichier)) {
			//On parse le XML pour recupérer le nom de la classe
			$this->parse_xml();
			//On choisit la classe correspondant au traitement du type MIME
			$this->choose_class($this->class_associee);
		}
	}
	
	/**
	 * On récupère le nom de la classe de traitement en fonction du  mimetype
	 */
	public function parse_xml(){
		global $base_path;
		
		$parse = new XMLlist("$base_path/catalog/explnum/index_docnum/index_doc.xml");	
		$parse->analyser();
		if($this->mimetype) {
			$class = $parse->table[$this->mimetype];
		}
		if($class) {
			$this->class_associee = $class;
		} else {
			$this->class_associee = '';
		}
	}
	
	/**
	 * On récupère le texte du document numérique grâce à la bonne classe
	 */
	public function choose_class($class_name){
		if($class_name){
			$index_class = new $class_name($this->fichier,$this->mimetype,$this->ext);
			$this->texte = $index_class->get_text($this->fichier);
		}
	}
	
	/**
	 * On récupère le contenu du fichier qui est en base
	 */
	public function get_file($filecontent){
		global $base_path;
		
		//On définit un nom unique dans le dossier temporaire
		$nom_temp = session_id().microtime();
		$nom_temp = str_replace(' ','_',$nom_temp);
		$nom_temp = str_replace('.','_',$nom_temp);
		
		//On écrit le contenu dans le fichier
		$fd = fopen("$base_path/temp/".$nom_temp,"w");
		fwrite($fd,$filecontent);
		fclose($fd);	
		$this->fichier = "$base_path/temp/".$nom_temp;	
	}
	
	/**
	 * On récupère le contenu du fichier à distance
	 */
	public function get_file_from_curl($f_url){
		global $base_path;
		
		//On définit un nom unique dans le dossier temporaire
		$nom_temp = session_id().microtime();
		$nom_temp = str_replace(' ','_',$nom_temp);
		$nom_temp = str_replace('.','_',$nom_temp);
		$this->fichier = "$base_path/temp/".$nom_temp;
		$aCurl = new Curl();
		$aCurl->timeout=5;
		$aCurl->save_file_name=$this->fichier; 
		$aCurl->get($f_url);	
		
	}
	
	/**
	 * On indexe le document numérique
	 */
	public function indexer(){
		global $sphinx_active; 
		
		$explnum_index_sew = strip_empty_words($this->texte);
		if(strlen($explnum_index_sew)) {
			$explnum_index_sew = ' '.$explnum_index_sew.' ';
		}
		$rqt = " update explnum set explnum_index_sew='".addslashes($explnum_index_sew)."', explnum_index_wew='".addslashes($this->texte)."' where explnum_id='".$this->id_explnum."'";
		pmb_mysql_query($rqt);	
		if (file_exists($this->fichier)) unlink($this->fichier);	
		if($sphinx_active){
			$si = self::get_sphinx_indexer();
			$si->fillIndex($this->id_explnum);
		}
	}
	
	/**
	 * On supprime l'index du document numérique
	 */
	public function desindexer(){
		$rqt = " update explnum set explnum_index_sew='', explnum_index_wew='' where explnum_id='".$this->id_explnum."'";
		pmb_mysql_query($rqt);
	}
		
	private static function get_sphinx_indexer(){
		if(!self::$sphinx_indexer){
			self::$sphinx_indexer = new sphinx_explnums_indexer();
		}
		return self::$sphinx_indexer;
	}
}