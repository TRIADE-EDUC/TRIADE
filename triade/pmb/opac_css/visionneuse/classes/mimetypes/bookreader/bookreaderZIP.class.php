<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bookreaderZIP.class.php,v 1.4 2018-02-26 17:01:59 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

if(!class_exists('zip')) {
	require_once($visionneuse_path."/../classes/zip.class.php");
}
require_once($visionneuse_path."/classes/mimetypes/bookreader/PDFMetadata.class.php");

class bookreaderZIP {
	public $doc;			//le document ZIP à traiter
	public $parameters;	//tableau décrivant les paramètres de la classe
	public $zipClass;			//l'objet zip
	public $html_ordered;	//tableau des chemins vers les fichiers html de l'ebook dans l'ordre
 	public $PDFMetadata;
	public $pagesSizes;
	
	function bookreaderZIP($doc,$parameters){
		$this->doc = $doc;
		$this->parameters = $parameters;
		$this->zipClass = new zip($this->doc->driver->get_cached_filename($this->doc->id));
// 		$this->PDFMetadata = new PDFMetadata($this->generatePDF());
	}
	
	function getPage($page){
		$len = strlen($this->getPageCount());
		if (!file_exists($this->doc->driver->get_cached_filename("page_".$this->doc->id)."-".$page)) {
 			$content = $this->zipClass->get_page_content($page);
 			$this->doc->driver->setInCache("page_".$this->doc->id."-".$page,$content);
			print $content;
		}else{
			print file_get_contents($this->doc->driver->get_cached_filename("page_".$this->doc->id)."-".$page);
		}
		header("Content-Type: image/jpg");
	}
	
	function getWidth($page){
		return $this->PDFMetadata->pagesSizes[$page]['width']*72/$this->parameters['resolution_image'];
	}
	
	function getHeight($page){
		return $this->PDFMetadata->pagesSizes[$page]['height']*72/$this->parameters['resolution_image'];
	}
	
	function getPagesSizes(){
		$this->pagesSizes= array();
		if (!$this->doc->driver->isInCache($this->doc->id."_pagessized")) {
			$this->pagesSizes = $this->zipClass->getPagesSizes();
			$this->doc->driver->setInCache($this->doc->id."_pagessized",json_encode($this->pagesSizes));
		}else{
			$this->pagesSizes = json_decode($this->doc->driver->readInCache($this->doc->id."_pagessized"));
		}
// 		foreach($this->PDFMetadata->pagesSizes as $page => $size){
// 			$this->pagesSizes[$page] = array(
// 				'width' => $size['width']*72/$this->parameters['resolution_image'],
// 				'height' => $size['height']*72/$this->parameters['resolution_image']
// 			);
// 		}
	}
	
	function getPDF($pdfParams){
		$file = $this->generatePDF();
		if (file_exists($file)){
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename="' . str_replace(" ","_",basename(utf8_decode($pdfParams["outname"]))).'"');
			readfile($file);
			exit;
		} else {
			print "Le PDF n'a pas été généré correctement.";
		}
	}
	
	function generatePDF(){
		global $charset;
		
		if (!file_exists($this->doc->driver->get_cached_filename($this->doc->id).".pdf")){
			$zip = new ZipArchive();
			$res = $zip->open($this->doc->driver->get_cached_filename($this->doc->id));
			
			if ($res === true) {
				if (!is_dir($this->doc->driver->get_cached_filename($this->doc->id)."_unzip")) mkdir($this->doc->driver->get_cached_filename($this->doc->id)."_unzip");
				$zip->extractTo($this->doc->driver->get_cached_filename($this->doc->id)."_unzip");
				$zip->close();
				$tab_html_docs = array();
				
				//- On espace les % pour les styles d'image
				//- On antislashe les espace dans les noms de fichiers pour compatibilité en ligne de commande
				foreach ($this->zipClass->entries as $file=>$entry) {
					$file_path = $this->doc->driver->get_cached_filename($this->doc->id)."_unzip/".$file;
					file_put_contents($file_path, str_replace("%", " %", file_get_contents($file_path)));
					$tab_html_docs[] = str_replace(" ", "\ ", $file_path);
				}
				$list_html_docs = implode(" ", $tab_html_docs);
				if ($this->doc->titre) {
					if ($charset != "utf-8") $titre = utf8_encode($this->doc->titre);
					else $titre = $this->doc->titre;
				} else {
					$titre = $this->doc->id;
				}
				exec("wkhtmltopdf --title ".str_replace(" ", "\ ", $titre)." --output-format pdf --encoding windows-1250 --dump-outline ".$this->doc->driver->get_cached_filename($this->doc->id)."_toc.xml --footer-center [page] cover ".$list_html_docs." ".$this->doc->driver->get_cached_filename($this->doc->id).".pdf");
				
				$this->rrmdir($this->doc->driver->get_cached_filename($this->doc->id)."_unzip");
			} else {
				print "Erreur à l'ouverture du zip!";
			}
		}
		return $this->doc->driver->get_cached_filename($this->doc->id).".pdf";
	}
	
	function getPageCount(){
// 		$page_count = $this->PDFMetadata->nb_pages;
		$page_count = $this->zipClass->getNbPages();
		return $page_count;
	}

	function rrmdir($dir){
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
}

?>