<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestion_import.class.php,v 1.8 2021/12/16 06:50:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once($include_path."/templates/suggestion_multi.tpl.php");
require_once("$include_path/parser.inc.php");

function _item_($param) {
	global $catalog;
	global $catalog_import;
	if(isset($param['IMPORTNAME'])) {
		$catalog[]=$param['IMPORTNAME'];
	} else {
		$catalog[]='';
	}
	if(isset($param['IMPORT'])) {
		$catalog_import[]=$param['IMPORT'];
	} else {
		$catalog_import[]='';
	}
}

class suggestion_import{
	
	public $id_import=0;
	
	public function __construct($id_imp=0){
		$this->id_import = $id_imp;
	}

	public function show_form(){
		global $import_sug_form, $charset, $msg;
		global $catalog;
		global $catalog_import;
		global $explnumdoc_id;
		
		if($explnumdoc_id){
			$req = "select SUBSTRING(explnum_doc_data,1,250) as data, sugg_source, origine, type_origine from explnum_doc
			join explnum_doc_sugg on num_explnum_doc=id_explnum_doc
			left join suggestions on num_suggestion=id_suggestion
			left join suggestions_origine so on so.num_suggestion=id_suggestion
			where id_explnum_doc='".$explnumdoc_id."'";
			$res = pmb_mysql_query($req);
			$expl = pmb_mysql_fetch_object($res);
			$import_file = htmlentities($expl->data,ENT_QUOTES,$charset);
			$import_sug_form = str_replace('!!explnum_id!!',$explnumdoc_id,$import_sug_form);
			$source = $expl->sugg_source;
			$origine = $expl->origine;
			$type_origine = $expl->type_origine;
			$import_sug_form = str_replace('!!origine_id!!',$origine,$import_sug_form);
			$import_sug_form = str_replace('!!type_origine!!',$type_origine,$import_sug_form);
		} else {
			$import_sug_form = str_replace('!!explnum_id!!',"",$import_sug_form);
			$import_file = "<input class='saisie-80em' size='65' type='file' name='import_file' id='import_file' />	";
			$source = 0;
			$import_sug_form = str_replace('!!origine_id!!',"",$import_sug_form);
			$import_sug_form = str_replace('!!type_origine!!',"",$import_sug_form);
		} 
		$import_sug_form = str_replace('!!import_file!!',$import_file,$import_sug_form);
		$req = "select * from suggestions_source order by libelle_source";
		$res= pmb_mysql_query($req);
		$option = "<option value='0'>".htmlentities($msg['acquisition_sugg_no_src'],ENT_QUOTES,$charset)."</option>";
		while(($src=pmb_mysql_fetch_object($res))){
			if($src->id_source == $source) $selected="selected";
			else $selected ="";
			$option .= "<option value='".$src->id_source."' $selected>".htmlentities($src->libelle_source,ENT_QUOTES,$charset)."</option>";
		}
		$selecteur = "<select id='src_liste' name='src_liste'>".$option."</select>";	
		$import_sug_form = str_replace('!!liste_source!!',$selecteur,$import_sug_form);
		
		//Lecture des différents imports possibles
		if (file_exists("./admin/convert/imports/catalog_subst.xml"))
			$fic_catal = "./admin/convert/imports/catalog_subst.xml";
		else
			$fic_catal = "./admin/convert/imports/catalog.xml";		
		$catalog=array();
		_parser_($fic_catal,array("ITEM"=>"_item_"),"CATALOG");		
		//Création de la liste des types d'import
		$select_import="<select name=\"import_type\">\n";
		$select_import .= "<option value='uni'>".$msg['acquisition_import_sugg_uni']."</option>";
		for ($i=0; $i<count($catalog); $i++) {
			if ($catalog_import[$i]=="yes" && $catalog[$i]) {
			   $select_import.="<option value=\"$i\">".$catalog[$i]."</option>\n";
			}
		}
		$select_import.="</select>";
		$import_sug_form = str_replace('!!liste_import!!',$select_import,$import_sug_form);
		print $import_sug_form;
	}
	
}
?>