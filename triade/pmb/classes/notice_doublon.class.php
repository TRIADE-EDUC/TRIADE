<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_doublon.class.php,v 1.16.6.1 2024/10/15 09:04:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/parametres_perso.class.php");

class notice_doublon {
    public $source_id = 0;
	public $external = false;		//booléen qui détermine si l'on est en recherche externe ou non...
	public $signature = '';
	public $duplicate;
	public static $fields;
	
	// constructeur
	public function __construct($external = false,$source_id=0) {
		global $include_path;
		
		$this->source_id = $source_id;
		$this->external= $external; 	
		// lecture des fonctions de pièges à exécuter pour faire un pret
		if(!isset(static::$fields)) {
			$this->parse_xml_fields($include_path."/notice/notice.xml");
		}
	}

	public function parse_xml_fields($filename) {
		global $msg;
		$f_pos=strrpos($filename,'.');
		$f_end=substr($filename,$f_pos);
		$f_deb=substr($filename,0,$f_pos);
		if (file_exists($f_deb."_subst".$f_end)) $filename=$f_deb."_subst".$f_end;
		$fp=fopen($filename,"r") or die("Can't find XML file");
		$xml=fread($fp,filesize($filename));
		fclose($fp);
		$param=_parser_text_no_function_($xml, "FIELDS");
		
		for($i=0; $i<count($param['FIELD']); $i++) {
			
			$name=$param['FIELD'][$i]['NAME'];	
			static::$fields[$name]['name'] = $param['FIELD'][$i]['NAME'];;
			static::$fields[$name]['size_max'] = $param['FIELD'][$i]['SIZE_MAX'];
			static::$fields[$name]['html'] = $param['FIELD'][$i]['HTML'][0]['value'];
			static::$fields[$name]['html_ext'] = $param['FIELD'][$i]['HTML_EXT'][0]['value'];
			static::$fields[$name]['sql'] = $param['FIELD'][$i]['SQL'][0]['value'];
			if(isset($param['FIELD'][$i]['SQL_EXT'][0]['value'])) {
				static::$fields[$name]['sql_ext']= $param['FIELD'][$i]['SQL_EXT'][0]['value'];
			} else {
				static::$fields[$name]['sql_ext']= '';
			}
			$label = $param['FIELD'][$i]['LABEL'];
			if(stripos($label,'msg:')===0 ) {
				$label = $msg[substr($label,4)];
			}
			static::$fields[$name]['label']= $label;
		}
		return 0;
	}
	
	public function read_field_form($field) {
		if(!empty(static::$fields[$field])) {
			if($this->external) $html=static::$fields[$field]["html_ext"];
			else $html=static::$fields[$field]["html"];
		} else {
			$html='';
		}
		if(!empty(static::$fields[$field])) {
			$size_max=	static::$fields[$field]["size_max"];
		} else {
			$size_max= 0;
		}
		
		if(!$html) {
			// c'est surement un param perso
			$p_perso=new parametres_perso("notices");
			$chaine=$p_perso->read_form_fields_perso($field); 			
			return $chaine;
		} else  {
			$chaine='';
			for($i=0;$i<$size_max;$i++) {
				$chaine.=stripslashes($GLOBALS[$html]);
				// incrément du name de l'objet dans le formulaire
				$html++;				
			}	
			return $chaine;
		}	
	}
	
	public function read_field_database($field,$id) {
		if($this->external) $rqt = static::$fields[$field]["sql_ext"];	
		else $rqt=static::$fields[$field]["sql"];	
 		if(!$rqt) {			
			// c'est surement un param perso
			$p_perso=new parametres_perso("notices");
			$p_perso->read_base_fields_perso($field,$id); 		
			return '';	
		} else {
			$rqt=str_replace('!!id!!',$id,$rqt);
			if($this->external) $rqt=str_replace('!!source_id!!',$this->source_id,$rqt);	
			$result = pmb_mysql_query($rqt);			
			if (($row = pmb_mysql_fetch_row($result) ) ) {
	        	return $row[0];
			} else {
				// rien
				return '';		
			}	
 		}	
	}
	
	public function gen_signature($id=0) {
		global $pmb_notice_controle_doublons;

		$field_list=explode(',',str_replace(' ','',$pmb_notice_controle_doublons));
				
		// Pas de control activé en paramétrage: Sortir.
		if( ($metod = $field_list[0]) < 1 ) return 0;
		$chaine='';
		foreach($field_list as  $i => $field) {
			if ($i>0){	
				if (!$id) {
					// le formulaire à lire
					$chaine.= $this->read_field_form($field);
				} else {
					// la base à lire
					$chaine.= $this->read_field_database($field,$id);
				}	
			}	
		}
		if($metod == 3 && $chaine) {
			$chaine = pmb_strtolower(strip_empty_chars(convert_diacrit($chaine)));
		}
		// encodage signature par SOUNDEX (option 2) et par md5 (32 caractères)
		if($metod == 2) {	
			$rqt = "SELECT SOUNDEX('".addslashes($chaine)."')";
			$result = pmb_mysql_query($rqt);				
			if (($row = pmb_mysql_fetch_row($result) ) ) {
	        	$chaine = $row[0];
			}					
		}
		$this->signature = md5($chaine);	
		return $this->signature;
	}			
	
	public function getDuplicate() {
		$q = "select signature, niveau_biblio ,niveau_hierar ,notice_id from notices where signature='".$this->signature."' limit 1";
		$r = pmb_mysql_query($q);
		if (pmb_mysql_num_rows($r)) {
			$this->duplicate= pmb_mysql_fetch_object($r);
		}		
		return $this->duplicate;
	}

// Fin class notice_doublon		
}

?>