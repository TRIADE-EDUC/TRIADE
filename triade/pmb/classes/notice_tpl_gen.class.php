<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_tpl_gen.class.php,v 1.18 2021/12/28 08:46:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path . "/parse_format.class.php");
require_once ($class_path . "/notice_info.class.php");

class notice_tpl_gen {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------	
	public $id;		// MySQL id in table 'notice_tpl'
	public $name;		// nom du template
	public $comment;	// description du template
	public $code ; 	// Code du template
	
	protected static $instances;
	
	// ---------------------------------------------------------------
	//		constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {			
		$this->id = intval($id);
		$this->getData();
	}
	
	// ---------------------------------------------------------------
	//		getData() : récupération infos 
	// ---------------------------------------------------------------
	public function getData() {
		$this->name = '';			
		$this->comment = '';
		$this->code =array();
		if($this->id) {
			$requete = "SELECT * FROM notice_tpl WHERE notpl_id='".$this->id."' LIMIT 1 ";
			$result = pmb_mysql_query($requete);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);				
				$this->name	= $temp->notpl_name;
				$this->comment	= $temp->notpl_comment	;
				// récup code		
				$requete = "SELECT * FROM notice_tplcode  WHERE num_notpl='".$this->id."' ";
				$result_code = pmb_mysql_query($requete);
				if(pmb_mysql_num_rows($result_code)) {
					while(($temp_code= pmb_mysql_fetch_object($result_code))) {
						$this->code[$temp_code->notplcode_localisation][$temp_code->notplcode_niveau_biblio] [$temp_code->notplcode_typdoc]=$temp_code->nottplcode_code;	
					}
				}			
			} else {
				// pas trouvé avec cette clé
				$this->id = 0;								
			}
		}
	}
	
	public function build_notice($id_notice,$location=0,$in_relation=false, $id_bannette = 0){
		global $parser_environnement;

		$parser_environnement['id_template'] = $this->id;
		$parser=parse_format::get_instance('notice_tpl.inc.php', $in_relation);			
		
		$requete = "SELECT typdoc, niveau_biblio FROM notices WHERE notice_id='".$id_notice."' LIMIT 1 ";
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)) {
			$temp = pmb_mysql_fetch_object($result);				
			$typdoc	= $temp->typdoc;			
			$niveau_biblio	= $temp->niveau_biblio;				
			//$niveau_hierar	= $temp->niveau_hierar;		
		} else return "";
		
		// Recherche du code à appliquer (du particulier au général)
		if(!empty($this->code[$location][$niveau_biblio][$typdoc])) {
			$code=$this->code[$location][$niveau_biblio][$typdoc];
		} elseif (!empty($this->code[$location][$niveau_biblio][0])) {
			$code=$this->code[$location][$niveau_biblio][0];
		} elseif (!empty($this->code[0][$niveau_biblio][$typdoc])) {
			$code=$this->code[0][$niveau_biblio][$typdoc];
		} elseif (!empty($this->code[0][$niveau_biblio][0])) {
			$code=$this->code[0][$niveau_biblio][0];
		} elseif (!empty($this->code[0][0][$typdoc])) {
			$code=$this->code[0][0][$typdoc];
		} elseif (!empty($this->code[0][0][0])) {
			$code=$this->code[0][0][0];
		} else return "";
		
		$temp = pmb_mysql_fetch_object($result);							
		$parser->cmd = $code;
		$parser_environnement['id_notice']=$id_notice;
		$parser_environnement['id_bannette']=$id_bannette;
		
		return $parser->exec_cmd();		
	}
	
	public function get_print_css_style() {
		$css_style = "
			<style type='text/css'>
				.tpl_vignette {
					width: auto !important;
				    max-width: 140px;
				    max-height: 200px;
				    -moz-box-shadow: 1px 1px 5px #666666;
				    -webkit-box-shadow: 1px 1px 5px #666666;
				    box-shadow: 1px 1px 5px #666666;
				}
			</style>
				";
		return $css_style;
	}
	
	static public function gen_tpl_select($select_name="notice_tpl", $selected_id=0, $onchange="",$no_affempty=0,$no_aff_defaut=0, $specific_noselected_message='') {		
		global $msg;
		// 
		$requete = "SELECT notpl_id, if(notpl_comment!='',concat(notpl_name,'. ',notpl_comment),notpl_name) as nom FROM notice_tpl ORDER BY notpl_name ";
		$result = pmb_mysql_query($requete);
		if (!pmb_mysql_num_rows($result) && !$no_affempty) {
			return '';	
		}
		if(!$no_aff_defaut) {
			$msg_defaut = $msg["notice_tpl_list_default"];
			if (trim($specific_noselected_message)) {
				$msg_defaut = $specific_noselected_message;
			}
			return gen_liste ($requete, "notpl_id", "nom", $select_name, $onchange, $selected_id, 0, $msg_defaut, 0, $msg_defaut, 0) ;
		} else {
			return gen_liste ($requete, "notpl_id", "nom", $select_name, $onchange, $selected_id, 0, '', 0,'', 0) ;
		}
		
	}
	
	public static function get_instance($id=0) {
		$id = intval($id);
		if(!isset(static::$instances[$id])) {
			static::$instances[$id] = new notice_tpl_gen($id);
		}
		return static::$instances[$id];
	}
} // fin class 


