<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bcdimemodocnet2pmbxmluni_input.class.php,v 1.3 2023/08/23 10:25:54 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once ($base_path."/admin/convert/convert_input.class.php");
require_once $class_path."/import/import_records.class.php";

class bcdimemodocnet2pmbxmluni_input extends convert_input {
	
	public function _get_n_notices_($fi,$file_in,$input_params,$origine) {
		//mysql_query("delete from import_marc");
		$index=array();
		$i=false;
		$i_notpartie=false;
		$i_notgenerale=false;
		$encoding="";
		$n=1;
		$fcontents="";
		while ($i===false) {
			$i=strpos($fcontents,"<".$input_params['NOTICEELEMENT'].">");
			if ($i===false) $i=strpos($fcontents,"<".$input_params['NOTICEELEMENT']." ");
			if ($i!==false) {
				//on pense à récup le charset du xml
				if($encoding === ""){
					$s=strpos($fcontents,"<?xml");
					$e=strpos($fcontents,"?>");
					if(isset($s) && isset($e)){
						$entete = pmb_substr($fcontents,$s,$e+2);
						$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
						$m = array();
						if (preg_match($rx,$entete, $m)) {
							$encoding = strtoupper($m[1]);
						}
					}
				} 
				$i1=strpos($fcontents,"</".$input_params['NOTICEELEMENT'].">");
				
				while ((!feof($fi))&&($i1===false)) {
					$fcontents.=fread($fi,4096);
					$i1=strpos($fcontents,"</".$input_params['NOTICEELEMENT'].">");
				}
				
				// nouvelles versions de BCDI : la notice mere et la notice fille sont dans 1 seule notice => si on a une fille (=notice partie), on extrait la mere (notice generale) pour envoyer le bon nombre de noties à traiter
				$i_notpartie=strpos($fcontents,"<NOTICE_PARTIE>");
				$i1_notpartie=strpos($fcontents,"</NOTICE_PARTIE>");
				$i_notgenerale=strpos($fcontents,"<NOTICE_GENERALE>");
				$i1_notgenerale=strpos($fcontents,"</NOTICE_GENERALE>");
				if($i_notpartie!==false && $i1_notpartie!==false) {
					if($i_notgenerale!==false && $i1_notgenerale!==false){
						// envoi de la notice mere 
						$notice="<".$input_params['NOTICEELEMENT'].">".substr($fcontents,$i_notgenerale,intval($i1_notgenerale) + strlen("</NOTICE_GENERALE>") - intval($i_notgenerale))."</".$input_params['NOTICEELEMENT'].">";
						$notice=import_records::get_encoded_buffer($notice);
						$requete="insert into import_marc (no_notice, notice, origine, encoding) values($n,'".addslashes($notice)."','$origine','$encoding')";
						pmb_mysql_query($requete);
						$n++;
						$index[]=$n;
						// envoi de la notice fille
						$notice=substr($fcontents,$i,intval($i1) + strlen("</".$input_params['NOTICEELEMENT'].">")- intval($i));
						$notice=import_records::get_encoded_buffer($notice);
						$requete="insert into import_marc (no_notice, notice, origine, encoding) values($n,'".addslashes($notice)."','$origine','$encoding')";
						pmb_mysql_query($requete);
						$n++;
						$index[]=$n;
						$fcontents=substr($fcontents,$i1+strlen("</".$input_params['NOTICEELEMENT'].">"));
						$i=false;					
					}				
				} 
				// si pas de fille, on traite la notice de manière standard
				elseif ($i1!==false) {
					$notice=substr($fcontents,$i,intval($i1) + strlen("</".$input_params['NOTICEELEMENT'].">") - intval($i));
					$notice=import_records::get_encoded_buffer($notice);
					$requete="insert into import_marc (no_notice, notice, origine, encoding) values($n,'".addslashes($notice)."','$origine','$encoding')";
					pmb_mysql_query($requete);
					$n++;
					$index[]=$n;
					$fcontents=substr($fcontents,$i1+strlen("</".$input_params['NOTICEELEMENT'].">"));
					$i=false;
				}
			} else {
				if (!feof($fi))
					$fcontents.=fread($fi,4096);
				else break;
			}
		}
	
		return $index;
	}
}

?>