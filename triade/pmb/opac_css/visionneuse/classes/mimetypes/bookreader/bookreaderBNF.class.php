<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bookreaderBNF.class.php,v 1.5 2017/07/03 09:07:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class bookreaderBNF {
	public $doc;		//le document BNF à traiter
	public $bnfClass;
	
	public function __construct($doc){
		$this->doc = $doc;
		$this->getBnfClass();
	}
	
	public function getBnfClass(){
		global $visionneuse_path;
		$class_name = $this->doc->driver->getBnfClass($this->doc->mimetype);
		$this->bnfClass = new $class_name($this->doc->driver->get_cached_filename($this->doc->id));
	}
	
	public function getPage($page){
		if (!$this->doc->driver->isInCache($this->doc->id."_".$page)) {
			$this->doc->driver->setInCache($this->doc->id."_".$page,$this->bnfClass->get_page_content($page));
		}
		print $this->doc->driver->readInCache($this->doc->id."_".$page);
	}
	
	public function getWidth($page){
		print $this->bnfClass->getWidth($page);
	}
	
	public function getHeight($page){
		print $this->bnfClass->getHeight($page);
	}
	
	public function search($user_query){
		return $this->bnfClass->search($user_query);
	}
	
	public function getBookmarks(){
		return $this->bnfClass->getBookmarks();
	}
	
	public function getPDF($pdfParams){
		$this->bnfClass->generatePDF($pdfParams);
	}
	
	public function getPageCount(){
		return $this->bnfClass->getNbPages();
	}
	
	public function getPagesSizes(){
 		$this->pagesSizes= $this->bnfClass->pagesSizes;
	}
}
?>