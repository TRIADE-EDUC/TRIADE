<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ascodocpsy2uni_input.class.php,v 1.2 2023/08/28 14:01:13 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_input.class.php");

class ascodocpsy2uni_input extends convert_input {
	
	public function _get_n_notices_($fi,$file_in,$input_params,$origine) {
		global $base_path,$charset;
		//pmb_mysql_query("delete from import_marc");
		
		$first=true;
		$stop=false;
		$content="";
		$index=array();
		$n=1;
		//Lecture du fichier d'entrée
		while (($data = fgetcsv($fi,0,"\t",'"')) !== FALSE) {
			$notice=implode("'^'",$data);
			
			if($_SESSION["encodage_fic_source"]){//On a forcé l'encodage
				if(($charset == "utf-8") && ($_SESSION["encodage_fic_source"] == "iso8859")){
					if(function_exists("mb_convert_encoding")){
						$notice = mb_convert_encoding($notice,"UTF-8","Windows-1252");
					}else{
						$notice=encoding_normalize::utf8_normalize($notice);
					}
				}elseif(($charset == "iso-8859-1" && ($_SESSION["encodage_fic_source"] == "utf8"))){
					if(function_exists("mb_convert_encoding")){
						$notice = mb_convert_encoding($notice,"Windows-1252","UTF-8");
					}else{
						$notice = encoding_normalize::utf8_decode($notice);
					}
				}
			}
			
			//Si c'est la première notice, c'est la ligne d'intitulés !!
			if ($first) {
				//les champs sont séparés par la tabulation
				$cols=explode("'^'",$notice);
				$fcols=fopen("$base_path/temp/".$origine."_cols.txt","w+");
				if ($fcols) {
					fwrite($fcols,serialize($cols));
					fclose($fcols);
				}
				$notice="";
				$first=false;
			}
			if ($notice) {
				$requete="insert into import_marc (no_notice, notice, origine) values($n,'".addslashes($notice)."','$origine')";
				pmb_mysql_query($requete);
				$n++;
				$t=array();
				$t["POS"]=$n;
				$t["LENGHT"]=1;
				$index[]=$t;
			}
		}
		return $index;
	}
}

?>