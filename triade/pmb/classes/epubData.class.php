<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: epubData.class.php,v 1.8.2.1 2024/09/10 09:54:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class epub_Data {
	
	public $filename = ''; //Fichier source de l'eBook
	public $opfFile = ''; //Fichier d'entrée de l'eBook
	public $opfDir = ''; //Répertoire d'entrée de l'eBook
	public $metas = array(); //Tableau des métadatas de l'eBook
	public $items = array(); //Liste des fichiers composant l'eBook
	public $spine = array(); //Ordre d'affichage des fichiers
	public $spineToc = ''; //Fichier table des matières
	public $spinePageMap = ''; //Fichier liste des pages
	public $pages = array(); //Liste des pages
	public $toc = array(); //Table des matières
	public $charset = ''; //Charset de l'epub
	public $cover_item;	//path vers l'image de couverture
	
	//Constructeur
	public function __construct($filename){
		if (is_file($filename)){
			$this->filename = $filename;
			if ($this->isValidEpub()) {
				$this->fetchMetadatas();
				$this->fetchItems();
				$this->fetchSpine();
				$this->fetchPages();
				$this->fetchToc();
			} else {
				print ("Fichier eBook non valide : ".$this->filename.".\n");
			}
		} else {
			print ("Fichier non trouvé : '".$filename."'.\n");
		}
	}
	
	//Récupération du contenu texte en vue d'indexation de l'eBook
	public function getFullTextContent($otherCharset='utf-8') {
		$chaineRetour = '';
		foreach ($this->spine as $spinId) {
			if ($this->items[$spinId]) {
				$contents = html_entity_decode($this->getContentFile($this->opfDir.$this->items[$spinId]["href"]));
			}			
			$chaineRetour .= strip_tags($contents);			
		}
		if ($otherCharset != "utf-8") {
			$chaineRetour = encoding_normalize::utf8_decode($chaineRetour);
		}
		return $chaineRetour;
	}
	
	//Récupération du contenu d'une page
	public function getPageContent($page) {
		$chaineRetour = $this->getContentFile($this->opfDir.$page);
		if (!preg_match('`meta charset`',$chaineRetour)) {
			$chaineRetour = str_replace("<head>","<head><meta charset=\"UTF-8\">",$chaineRetour);
		}
		return $chaineRetour;
	}
	
	private function isValidEpub() {
		$isValid = true;
		//On vérifie le fichier "mimetype" et son contenu
		$mime = $this->getContentFile("mimetype");
		if (!preg_match('(application\/epub\+zip)', $mime)) {
			$isValid = false;
		}
		return $isValid;
	}
	
	//Récupération des métadatas
	private function fetchMetadatas() {
		//On ouvre le container.xml
		$contents = $this->getContentFile("META-INF/container.xml");
		if (trim($contents)) {
			$xml = simplexml_load_string($contents);
			//On va chercher le fichier opf
			$tmpFile = $this->decodeCharset($xml->rootfiles->rootfile->attributes()->{'full-path'});
			$tmpArray = explode("/",$tmpFile);
			$this->opfFile =array_pop($tmpArray);
			$this->opfDir = implode("/",$tmpArray)."/";
			if ($this->opfDir == '/') {
				$this->opfDir = '';
			}
			$contents = $this->getContentFile($this->opfDir.$this->opfFile);
			//On cherche le charset
			$this->charset = strtolower(mb_detect_encoding($contents));
			$xml = simplexml_load_string($contents);		
			//on déclare les namespaces
			$namespaces = $xml->getNamespaces(true);
			foreach ($namespaces as $k=>$v) {
				if (trim($k)) {
					define(strtoupper($k), $v);
				}
			}
			//on va chercher les metas
			$xmlMeta = $xml->children('OPF', false)->metadata->children('DC', false);
			if (!empty($xmlMeta)) {
    			foreach ($xmlMeta as $k=>$v) {
    				$key = $this->decodeCharset($k);
    				switch($key){
    					case "creator":
    					case "contributor":
    					case "date" :	
    						$aut = array();
    						foreach($v->attributes('OPF',false) as $k_attr => $v_attr){
    							$aut[strtolower($this->decodeCharset($k_attr))]=$this->decodeCharset($v_attr);
    						}
    						$aut['value'] = $this->decodeCharset($v);
    						$this->metas[$this->decodeCharset($k)][]=$aut;
    						break;
    					case "identifier":
    						$attrs = $v->attributes('OPF',false);
    						foreach($attrs as $k_attr => $v_attr){
    							$this->metas[$this->decodeCharset($k)][strtolower($this->decodeCharset($v_attr))]=$this->decodeCharset($v);
    						}
    						//le cas où la norme est bien loin...
    						if(!$this->metas[$this->decodeCharset($k)]){
    							$this->metas[$this->decodeCharset($k)]['value'] = $this->decodeCharset($v);
    						}
    						break;
    					default :
    						$this->metas[$this->decodeCharset($k)][] = $this->decodeCharset($v);
    						break;
    				}
    			}
			}
			//on regarde si on a une mage de couverture...
			$metastag = $xml->children('OPF', false)->metadata->children('OPF',false);
			$this->cover_item ="";
			if (!empty($metastag)) {
    			foreach ($metastag as $k=>$v) {
    				$key = $this->decodeCharset($k);
    				if($key != "meta"){
    					continue;
    				}
    				$attrs = $v->attributes();
    				if($attrs->{"name"} == "cover") {
    					$this->cover_item = $this->decodeCharset($attrs->{"content"});
    					break;
    				}
    			}
			}
		}
	}
	
	//Récupération des items de l'eBook
	private function fetchItems() {
		$contents = $this->getContentFile($this->opfDir.$this->opfFile);
		if (trim($contents)) {
			$xml = simplexml_load_string($contents);
			foreach ($xml->manifest->item as $item) {
				$this->items[$this->decodeCharset($item->attributes()->{'id'})]["href"] =  $this->decodeCharset($item->attributes()->{'href'});
				$this->items[$this->decodeCharset($item->attributes()->{'id'})]["media-type"] =  $this->decodeCharset($item->attributes()->{'media-type'});
			}
		}
	}
	
	//Récupération de l'ordre d'affichage des fichiers
	private function fetchSpine() {
		$contents = $this->getContentFile($this->opfDir.$this->opfFile);
		if (trim($contents)) {
			$xml = simplexml_load_string($contents);
			$this->spineToc = $this->decodeCharset($xml->spine->attributes()->{'toc'});
			$this->spinePageMap = $this->decodeCharset($xml->spine->attributes()->{'page-map'});
			foreach ($xml->spine->itemref as $item) {
				$this->spine[] =  $this->decodeCharset($item->attributes()->{'idref'});
			}
		}
	}
	
	//Récupération des pages de l'eBook
	private function fetchPages() {
		$contents = $this->getContentFile($this->opfDir.$this->items[$this->spinePageMap]["href"]);
		if (trim($contents)) {
			$xml = simplexml_load_string($contents);
			foreach ($xml->page as $item) {
				$tmpArray = array();
				$tmpArray['name'] = $this->decodeCharset($item->attributes()->{'name'});
				$tmpArray['href'] = $this->decodeCharset($item->attributes()->{'href'});
				$this->pages[] = $tmpArray;
			}
		} else {
			//Pafois nous n'avons pas de liste des pages
			foreach ($this->items as $item) {
				if ($item["media-type"]=='application/xhtml+xml') {
					$tmpArray = array();
					$tmpArray['name'] = $item["href"];
					$tmpArray['href'] = $item["href"];
					$this->pages[] = $tmpArray;
				}
			}
		}
	}
	
	//Récupération de la table des matières
	private function fetchToc() {
		$contents = $this->getContentFile($this->opfDir.$this->items[$this->spineToc]["href"]);
			if (trim($contents)) {
			$xml = simplexml_load_string($contents);
			foreach ($xml->navMap->navPoint as $item) {
				$this->readNavPoint($item,0);
			}
		}
	}
	
	//méthode pour retrouver de façon récurrente les points de navigation
	private function readNavPoint($simpleXmlObject,$level){
		$tmpArray = array();
		$tmpArray['playOrder'] = $this->decodeCharset($simpleXmlObject->attributes()->{'playOrder'});
		$tmpArray['text'] = $this->decodeCharset($simpleXmlObject->navLabel->text);
		$tmpArray['content'] = $this->decodeCharset($simpleXmlObject->content->attributes()->{'src'});
		$tmpArray['level'] = $level;
		$this->toc[] = $tmpArray;
		$level++;
		foreach ($simpleXmlObject->navPoint as $item) {
			$this->readNavPoint($item,$level);
		}
	}
	
	//On va chercher le contenu d'un fichier de l'archive eBook
	private function getContentFile($file){
		//On essaye de gagner du temps pour l'affichage dans la visionneuse
		session_write_close();
		$zip = zip_open($this->filename);
		$contents = "";
		if ($zip) {
			while ($zip_entry = zip_read($zip)) {
				if(zip_entry_name($zip_entry) == $file && zip_entry_open($zip, $zip_entry))	{
					$contents = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					zip_entry_close($zip_entry);
				}
			}
			zip_close($zip);
			return $contents;
		}
	}
	
	//Fonction de décodage selon l'environnement
	private function decodeCharset($string) {		
		$string = htmlentities($string,ENT_QUOTES,$this->charset);
		return $string;
	}
	
	//Pour la route...
	public function getCoverContent(){
		return $this->getContentFile($this->opfDir.$this->items[$this->cover_item]['href']);
	}
}
?>