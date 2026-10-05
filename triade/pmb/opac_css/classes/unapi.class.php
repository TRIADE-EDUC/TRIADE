<?php
// +--------------------------------------------------------------------------+
// | PMB est sous licence GPL, la réutilisation du code est cadrée            |
// +--------------------------------------------------------------------------+
// $Id: unapi.class.php,v 1.3 2017/07/03 13:07:42 dgoron Exp $

require_once ($include_path."/parser.inc.php");

class unapi {
	public $id = 0;	//id de la notice
	public $format;	//format demandé
	public $notice;	//notice dans le format demandé
	public $formats;	//tableau regroupant les infos du XML

    public function __construct($format,$id) {
    	$this->format = $format;
    	$this->id = $id;
    	$this->formats = array();
    	
    	$this->getFormats();
    	
    	if($this->format){
    		if($this->id) $this->getNotice();
    	}else{
    		$this->sendFormats();
    	}   	
    }
    
	public function getFormats(){
    	global $charset;
    	global $base_path;
    	
    	//l'entete du xml    	
		$this->xml = "<?xml version='1.0' encoding='$charset'?>
	<formats ".($this->id ? "id='".$this->id."'": "").">";

		if (file_exists("$base_path/admin/convert/imports/zotero_subst.xml"))
			$fic_zotero = "$base_path/admin/convert/imports/zotero_subst.xml";
		else $fic_zotero = "$base_path/admin/convert/imports/zotero.xml";	
		_parser_($fic_zotero, array("FORMAT" => array('obj' => $this,'method' => "getFormatInfo")), "FORMATS");
		$this->xml .= "
	</formats>"; 			
	}   
   
    public function getFormatInfo($format){
    	global $charset;
    	
    	$this->formats[$format['NAME']] = $format;
    	$this->xml .= "
		<format name='".$format['NAME']."' type='".$format['TYPE']."'/>";
    }
    
     public function sendFormats(){
     	global $charset;
  
		header("Content-type: application/xml; charset=" .$charset, true);
		print $this->xml;
    }
       
    public function getNotice(){
    	global $charset;

		//on récupère l'identifiant du l'export associé au format
		$this->typeExport = start_export::get_id_by_path($this->formats[$this->format]['TRANSFORM']);
		
		//on a ce qu'il faut, on récupère la notice dans le bon format
    	$this->notice = cree_export_notices(array($this->id),$this->typeExport,1);
	
		//on envoi le bon mimetype
		if($this->formats[$this->format]['TYPE'])
			header("Content-type: ".$this->formats[$this->format]['TYPE']."; charset=" .$charset, true);
		//on affiche la notice
		print $this->notice;
    }
}
?>