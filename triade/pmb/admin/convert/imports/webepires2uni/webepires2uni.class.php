<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: webepires2uni.class.php,v 1.2 2022/04/21 07:34:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once("$class_path/marc_table.class.php");
require_once($base_path."/admin/convert/convert.class.php");

class webepires2uni extends convert {

	protected static function make_index($descr,$tete) {
		global $charset;
		$data="";
		if ($descr) {
			$d=explode(",",$descr);
			for ($i=0; $i<count($d); $i++) {
				if ($d[$i]) {
					$data.="  <f c='606' ind='  '>\n";
					$data.="    <s c='a'>".htmlspecialchars($tete,ENT_QUOTES,$charset)."</s>\n";
					$data.="    <s c='x'>".htmlspecialchars($d[$i],ENT_QUOTES,$charset)."</s>\n";
					$data.="  </f>\n";
				}
			}
		}
		return $data;
	}
	
	public static function convert_data($notice, $s, $islast, $isfirst, $param_path) {
		global $cols;
		global $intitules;
		global $base_path,$origine;
		global $tab_functions;
		
		if (!$tab_functions) $tab_functions=new marc_list('function');
		
		if (!$cols) {
			//On lit les intitulés dans le fichier temporaire
			$fcols=fopen("$base_path/temp/".$origine."_cols.txt","r");
			if ($fcols) {
				$cols=fread($fcols,filesize("$base_path/temp/".$origine."_cols.txt"));
				fclose($fcols);
				$cols=unserialize($cols);
			}
		}
		
		$fields=explode(";;",$notice);
		$ntable=array();
		for ($i=0; $i<count($fields); $i++) {
			$ntable[$cols[$i]]=$fields[$i];
		}
		if ((!$ntable["NOM"])||(!$ntable["SITE"])) {
			$data=""; 
			$error="Titre vide<br />".$notice;
		} else {
			$error="";
			$data="<notice>\n";
			
			//Entête
			if ($s["LOCALBASE"][0]["value"]==DATA_BASE) $rs="c"; else $rs="n";
			$data.="  <rs>".$rs."</rs>\n";
			$dt="w";
			$bl="s";
			$data.="  <dt>".$dt."</dt>\n";
			$data.="<bl>".$bl."</bl>\n";
			$data.="<hl>*</hl>\n<el>1</el>\n<ru>i</ru>\n";
			//Numéro d'enregistrement
			//$data.="  <f c='001' ind='  '>".$ntable["REF"]."</f>\n";
			
			//Titre
			$data.=static::get_converted_field_uni('200', 'a', $ntable["NOM"]);
			
			//Site web
			$data.=static::get_converted_field_uni('856', 'u', $ntable["SITE"]);
		
			//Adresse mail : note générale
			$data.=static::get_converted_field_uni('300', 'a', $ntable["MEL"]);
		
			//LI : Note de contenu
			$data.=static::get_converted_field_uni('327', 'a', $ntable["LI"]);
			
			//COMMENT : Résumé
			$data.=static::get_converted_field_uni('330', 'a', $ntable["COMMENT"]);
			
			//DOC : Indexation Web
			 if ($ntable["DOC"]) {
			 	$data.=static::make_index($ntable["DOC"],"DOC");
			} 
			
			//Indexations
			if ($ntable["DE"]) {
				$data.=static::make_index($ntable["DE"],"DE");
			}
			
			$data.=static::get_converted_field_uni('676', 'a', $ntable["DO"]);
			
			//Champs spéciaux
			if (trim($ntable["OP"])) {
				$data.=static::get_converted_field_uni('900', 'a', $ntable["OP"]);
			}else{
				$data.=static::get_converted_field_uni('900', 'a', $ntable["PRISME"]);
			}
			$data.=static::get_converted_field_uni('902', 'a', date("Y")."-".date("m")."-".date("d"));
			$data.="</notice>\n";
		}
		
		$r = array();
		if (!$error) $r['VALID'] = true; else $r['VALID']=false;
		$r['ERROR'] = $error;
		$r['DATA'] = $data;
		return $r;
	}
}
