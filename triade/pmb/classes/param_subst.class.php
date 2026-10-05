<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param_subst.class.php,v 1.8.8.1 2025/03/14 13:27:45 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $include_path;
require_once("$include_path/templates/param_subst.tpl.php");
require_once($include_path."/parser.inc.php");
require_once($base_path.'/admin/param/param_func.inc.php');

class param_subst {
    public $type='';
    public $module = '';
    public $module_num = 0;
    public $allow_section = 0;
	public $subst_param = array();
	public $values = array();
	public $no_subst_param = array();
	
	public function __construct($type, $module, $module_num) {
		$this->type = $type;// opac, acquisition...
		$this->module = $module;// opac_view
		$this->module_num = $module_num;// pour évolution...
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $lang,$include_path;
		
		if (file_exists($include_path."/section_param/$lang.xml")) {
			_parser_($include_path."/section_param/$lang.xml",array("SECTION"=>"_section_"),"PMBSECTIONS");
			$this->allow_section=1;
		}
		
		$this->subst_param=array();	
		$myQuery = pmb_mysql_query("SELECT * FROM param_subst where subst_type_param= '".$this->type."' and  subst_module_param= '".$this->module."' and subst_module_num= '".$this->module_num."' ");		
		if(pmb_mysql_num_rows($myQuery)){			
			while(($r=pmb_mysql_fetch_assoc($myQuery))) {
				$this->subst_param[]=$r;
			}
		}			
		$this->no_subst_param=array();		
		$myQuery = pmb_mysql_query("SELECT * FROM parametres where type_param= '".$this->type."' and gestion=0 order by section_param,sstype_param");					
		while(($r=pmb_mysql_fetch_assoc($myQuery))) {	
			$found=0;
			foreach($this->subst_param as $key => $subst_param){				
				if($subst_param['subst_sstype_param']==$r['sstype_param']){
					$this->subst_param[$key]['valeur_param_origine']=$r['valeur_param'];
					$this->subst_param[$key]['section_param']=$r['section_param'];
					$found=1;
					break;
				}
			}
			if(!$found){
				$this->no_subst_param[]=$r;
			}
		}
	}

	
	public function get_form_list($link_modif_param) {
		global $charset,$msg;
		global $tpl_param_subst_table,$tpl_param_subst_table_line;
		global $tpl_param_table,$tpl_param_table_line;
		global $form_sstype_param; // si memorisation du formulaire, pour mettre en rouge le param				
		global $section_table;
		
		$form="<script type='text/javascript' src='./javascript/tablist.js'></script>";
		$lines="";
		if(count($this->subst_param)){
			$pair="odd";
			$section_param='';	
			foreach($this->subst_param as $subst_param){
				if (($section_param!=$subst_param["section_param"])&&($this->allow_section)) {
					$section_param=$subst_param["section_param"];	 
					$lines.="\n<tr><th colspan='5'><b>".$section_table[$section_param]["LIB"]."</b></th></tr>";
				} 							
				if($pair!="odd")$pair="odd"; else $pair="even";		
				if($form_sstype_param== $subst_param["subst_sstype_param"])$style = "background: rgb(255, 34, 34) none repeat scroll 0% 0%; cursor: pointer; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;";
				else $style = "cursor: pointer;";				
				$line=str_replace('!!link_edit!!', $link_modif_param."&param_subst=!!name!!&action_subst=edit", $tpl_param_subst_table_line);
				$line=str_replace('!!link_suppr!!', $link_modif_param."&param_subst=!!name!!&action_subst=suppr", $line);
				$line=str_replace('!!odd_even!!', $pair, $line);
				$line=str_replace('!!name!!', htmlentities($subst_param["subst_sstype_param"],ENT_QUOTES,$charset), $line);
				$line=str_replace('!!origin_value!!', htmlentities($subst_param["valeur_param_origine"],ENT_QUOTES,$charset), $line);
				$line=str_replace('!!value!!', htmlentities($subst_param["subst_valeur_param"],ENT_QUOTES,$charset), $line);
				$line=str_replace('!!comment!!', htmlentities($subst_param["subst_comment_param"],ENT_QUOTES,$charset), $line);
				$line=str_replace('!!style!!', $style, $line);
				$lines.=$line;
			}			
		} 
		$form_subst_param=str_replace('!!subst_table_lines!!', $lines, $tpl_param_subst_table);
		if($form_sstype_param)$plus=1; else $plus=0;
		$form_subst_param= gen_plus(1,$msg["param_subst_title"],$form_subst_param,$plus);
		if(count($this->no_subst_param)){
			$lines="";
			$pair="odd";	
			$section_param='';	
			foreach($this->no_subst_param as $param){					
				if (($section_param!=$param["section_param"])&&($this->allow_section)) {
					$section_param=$param["section_param"];	 
					$lines.="\n<tr><th colspan='3'><b>".$section_table[$section_param]["LIB"]."</b></th></tr>";
				} 
				if($pair!="odd")$pair="odd"; else $pair="even";			
				$line=str_replace('!!link_edit!!', $link_modif_param."&param_subst=!!name!!&action_subst=edit", $tpl_param_table_line);
				$line=str_replace('!!odd_even!!', $pair, $line);
				$line=str_replace('!!name!!', htmlentities($param["sstype_param"],ENT_QUOTES,$charset), $line);
				$line=str_replace('!!value!!', htmlentities($param["valeur_param"],ENT_QUOTES,$charset), $line);
				$line=str_replace('!!comment!!', htmlentities($param["comment_param"],ENT_QUOTES,$charset), $line);
				$lines.=$line;
			}					
		} 
		$form_param=str_replace('!!table_lines!!', $lines, $tpl_param_table);	
		$form_param= gen_plus(2,$msg["param_origin_title"],$form_param,0);
		return($form.$form_subst_param.$form_param);
	}
	
	public function exec_param_form($link_modif_param) {
		global $msg;
		global $tpl_param_subst_form;
		global $param_subst;		
		global $action_subst;
		
		if($action_subst=="save"){
			return $this->save_param_form($link_modif_param);
		}elseif($action_subst=="suppr"){
			$req="DELETE from param_subst where subst_type_param='".$this->type."' and	subst_module_param='".$this->module."' and subst_module_num='".$this->module_num."' and	subst_sstype_param='".$param_subst."' limit 1";
			pmb_mysql_query($req);	
			$this->fetch_data();
			return "";
		}		
		$found_subst=0; 
		$prefixe="";
		foreach($this->subst_param as $param_data){					
			if($param_data['subst_sstype_param']==$param_subst){				
				$found_subst=1;
				$prefixe="subst_";
				break;
			}
		}	
		if(!$found_subst){
			foreach($this->no_subst_param as $param_data){				
				if($param_data['sstype_param']==$param_subst){
					break;
				}
			}					
		}		
		$title = $msg[1606]; // modification	
		$form = str_replace('!!form_title!!', $title, $tpl_param_subst_form);
		$form = str_replace('!!link_save!!', $link_modif_param."&param_subst=!!sstype_param!!&action_subst=save", $form);			
		$form = str_replace('!!type_param!!', $this->type, $form);
		$form = str_replace('!!sstype_param!!', $param_data[$prefixe.'sstype_param'], $form);
		$form = str_replace('!!valeur_param!!', $param_data[$prefixe.'valeur_param'], $form);
		$form = str_replace('!!comment_param!!', $param_data[$prefixe.'comment_param'], $form);	
		$form = str_replace('!!link_annuler!!', "onClick=\"history.go(-1);\"", $form);	
		return $form;	
	}
	
	public function save_param_form() {
		global $msg;
		global $form_sstype_param, $form_valeur_param, $comment_param;
		
		$found_subst=0; 
		foreach($this->subst_param as $param_data){				
			if($param_data['subst_sstype_param']==$form_sstype_param){				
				$found_subst=1;
				break;
			}
		}	
		if(!$found_subst){
			$req="INSERT INTO param_subst SET 
			subst_type_param='".$this->type."',
			subst_module_param='".$this->module."',
			subst_module_num='".$this->module_num."',			  
			subst_sstype_param='".$form_sstype_param."',
			subst_valeur_param='".$form_valeur_param."',
			subst_comment_param='".$comment_param."' ";
			$erreur=pmb_mysql_query($req);			
			if(!$erreur) {
				error_message($msg["opac_view_form_edit"], $msg["opac_view_form_add_error"],1);
				exit;
			}
		} else {
			$req="UPDATE param_subst SET 
			subst_valeur_param='".$form_valeur_param."',
			subst_comment_param='".$comment_param."' 
			where subst_type_param='".$this->type."' and subst_module_param='".$this->module."' and subst_module_num='".$this->module_num."' and subst_sstype_param='".$form_sstype_param."' limit 1";
			$erreur=pmb_mysql_query($req);
			if(!$erreur) {
				error_message($msg["opac_view_form_edit"], $msg["opac_view_form_add_error"],1);
				exit;
			}
		}	
		$this->fetch_data();
		return "";	
	}
	
	
	public function save_param($sstype_param,$valeur_param,$comment_param="") {
		global $msg;
		
		$found_subst=0;
		foreach($this->subst_param as $param_data){
			if($param_data['subst_sstype_param']==$form_sstype_param){
				$found_subst=1;
				break;
			}
		}
		if(!$found_subst){
			$req="INSERT INTO param_subst SET
			subst_type_param='".$this->type."',
			subst_module_param='".$this->module."',
			subst_module_num='".$this->module_num."',
			subst_sstype_param='".$sstype_param."',
			subst_valeur_param='".$valeur_param."',
			subst_comment_param='".$comment_param."' ";
			$erreur=pmb_mysql_query($req);
			if(!$erreur) {
				error_message($msg["opac_view_form_edit"], $msg["opac_view_form_add_error"],1);
				exit;
			}
		} else {
			$req="UPDATE param_subst SET
			subst_valeur_param='".$valeur_param."',
			subst_comment_param='".$comment_param."'
			where subst_type_param='".$this->type."' and subst_module_param='".$this->module."' and subst_module_num='".$this->module_num."' 
			and subst_sstype_param='".$sstype_param."' limit 1";
			$erreur=pmb_mysql_query($req);
			if(!$erreur) {
				error_message($msg["opac_view_form_edit"], $msg["opac_view_form_add_error"],1);
				exit;
			}
		}
		return "";
	}
	
	public function delete_param_value($sstype_param,$valeur_param) {
		$req="DELETE from param_subst where subst_type_param='".$this->type."' and	subst_module_param='".$this->module."' and subst_sstype_param='$sstype_param' and	subst_valeur_param='".$valeur_param."' ";
		pmb_mysql_query($req);
	}
}
?>