<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: opac_filters.class.php,v 1.4.10.1 2025/03/14 13:27:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/upload_folder.class.php");


class opac_filters {
	
    public $id_vue=0;
	public $catalog = array();			//Liste des filtres declares
	public $msg = array();
	
	//Constructeur
	public function __construct($id_vue) {
		$this->id_vue=$id_vue;
		global $base_path;
		if (file_exists($base_path."/admin/opac/opac_view/filters/catalog_subst.xml")) 
			$catalog=$base_path."/admin/opac/opac_view/filters/catalog_subst.xml";
		else
			$catalog=$base_path."/admin/opac/opac_view/filters/catalog.xml";
		$this->parse_catalog($catalog);
	}
	
	public function get_messages($lang_path) {
		global $lang;
		global $base_path;
		
		if (file_exists($base_path.$lang_path."/messages/".$lang.".xml")) {
			$file_name=$base_path.$lang_path."/messages/".$lang.".xml";
		} else if (file_exists($base_path.$lang_path."/messages/fr_FR.xml")) {
			$file_name=$base_path.$lang_path."/messages/fr_FR.xml";
		}
		if ($file_name) {
			$xmllist=new XMLlist($file_name);
			$xmllist->analyser();
			return $xmllist->table;
		}
	}
	public function parse_catalog($catalog) {
		global $base_path,$lang;
		//Construction du tableau des connecteurs disponbibles
		$xml=file_get_contents($catalog);
		$param=_parser_text_no_function_($xml,"CATALOG",$catalog);
		for ($i=0; $i<count($param["ITEM"]); $i++) {			
			$item=$param["ITEM"][$i];		
			$t=array();
			if($item["ACTIVE"]=="0") continue;
			$t["PATH"]=$item["PATH"];
			//Parse du manifest 
			$xml_manifest=file_get_contents($base_path."/admin/opac/opac_view/filters/".$item["PATH"]."/manifest.xml");
			$manifest=_parser_text_no_function_($xml_manifest,"MANIFEST");
			$t["NAME"]=$manifest["NAME"][0]["value"];
			$t["AUTHOR"]=$manifest["AUTHOR"][0]["value"];
			$t["ORG"]=$manifest["ORG"][0]["value"];
			$t["DATE"]=$manifest["DATE"][0]["value"];
			$t["STATUS"]=$manifest["STATUS"][0]["value"];
			
			//Commentaires
			$comment=array();
			for ($j=0; $j<count($manifest["COMMENT"]); $j++) {
				if(!isset($manifest["COMMENT"][$j]["lang"])) $manifest["COMMENT"][$j]["lang"] = '';
				if ($manifest["COMMENT"][$j]["lang"]==$lang) { 
					$comment=$manifest["COMMENT"][$j]["value"];
					break;
				} else if (!$manifest["COMMENT"][$j]["lang"]) {
					$c_default=$manifest["COMMENT"][$j]["value"];	
				}
			}
			if ($j==count($manifest["COMMENT"])) $comment=$c_default;
			$t["COMMENT"]=$comment;
			
			$this->catalog[$item["ID"]]=$t;
			$this->msg[$item["ID"]]=$this->get_messages("/admin/opac/opac_view/filters/".$item["PATH"]);
		}
	}	
	
	public function show_form($id) {
		global $base_path,$charset,$lang,$msg;
		
		//Inclusion de la classe
		require_once($base_path."/admin/opac/opac_view/filters/".$this->catalog[$id]["PATH"]."/".$this->catalog[$id]["NAME"].".class.php");
		eval("\$filter=new ".$this->catalog[$id]["NAME"]."(\$this->id_vue,\$this->msg[\$id]);");
		$form=$filter->get_form();			
		
		$form=str_replace("!!id!!",$id,$form);
		$form=gen_plus("filter_".$id,$this->msg[$id]["title"],$form,0);		
		return $form;
	}	
	
	public function show_all_form() {		
		$all_form="";
		
		foreach($this->catalog as $id => $val){				
			$all_form.=$this->show_form($id);
		}	
		return $all_form;
	}
	
	public function save_all_form() {	
		$all_form="";
		foreach($this->catalog as $id => $val){				
			$all_form.=$this->save_form($id);
		}	
		return $all_form;
	}
	
	public function save_form($id) {
		global $base_path,$charset,$lang,$msg;
		$all_form="";		
		//Inclusion de la classe
		require_once($base_path."/admin/opac/opac_view/filters/".$this->catalog[$id]["PATH"]."/".$this->catalog[$id]["NAME"].".class.php");
		eval("\$filter=new ".$this->catalog[$id]["NAME"]."(\$this->id_vue,\$this->msg[\$id]);");
		$form=$filter->save_form();			
	}		
}