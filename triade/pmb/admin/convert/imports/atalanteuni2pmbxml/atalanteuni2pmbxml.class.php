<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: atalanteuni2pmbxml.class.php,v 1.3 2022/04/21 07:34:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/admin/convert/convert.class.php");

class atalanteuni2pmbxml extends convert {

	public static function convert_data($notice, $s, $islast, $isfirst, $param_path) {
		global $typ_doc_atalante;
		global $charset;
		
		if (!$typ_doc_atalante) {
			$typ_doc_atalante=array("DOC"=>"a","VID"=>"g","PMU"=>"c","URL"=>"l","SON"=>"j");
		}
		
		$fields=explode(chr(0x01).chr(0x0A),$notice);
		$data="<notice>\n";
		
		$typ_doc=$typ_doc_atalante[substr($fields[0],0,3)];
		
		if ($typ_doc) {
			$data.="  <dt>".$typ_doc."</dt>\n";
		}
		
		$zs = array();
		for ($i=0; $i<count($fields)-1; $i++) {
			$field=explode("@",$fields[$i]);
			$cf=substr($field[2],0,3);
			$csf=substr($field[2],3,1);
			switch ($cf) {
				case '990':
					switch ($csf) {
						case 'a':
							$cf1='995';
							$csf1='k';
							break;
						case 'b':
							$cf1='995';
							$csf1='4';
							break;
						case 'x':
							$cf1='995';
							$csf1='5';
							break;
						case 'z':
							$cf1='995';
							$csf1='6';
							break;	
					}
					break;
				default:
					$cf1=$cf;
					$csf1=$csf;
					break;
			}
			$zs[$cf1][$csf1]=$field[3];
		}
		foreach ($zs as $key => $val) {
			$data.="  <f c='".$key."' ind='  '>\n";
			foreach ($val as $keys => $vals) {
				if ((substr($key,0,1)=="7") && ($keys=="4") && ($vals=="Auteur")) $vals = "070" ;  
				$data.="    <s c='".$keys."'>".htmlspecialchars($vals,ENT_QUOTES,$charset)."</s>\n";
			}
			$data.="  </f>\n";
		}
		$data.="</notice>\n";
		$r = array();
		$r['VALID'] = true;
		$r['ERROR'] = "";
		$r['DATA'] = $data;
		return $r;
	}
}
