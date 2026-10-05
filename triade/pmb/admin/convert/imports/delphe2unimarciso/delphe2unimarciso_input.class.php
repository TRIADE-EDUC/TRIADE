<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: delphe2unimarciso_input.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_input.class.php");

class delphe2unimarciso_input extends convert_input {
	
	public function _get_n_notices_($fi,$file_in,$input_params,$origine) {
		global $base_path;
		
		$first=true;
		$stop=false;
		$content="";
		$index=array();
		$n=1;
		$i=0;
		//Lecture du fichier d'entrée
		while (!feof($fi)) {
			$notice=fgets($fi,4096);
			if ($i>0 && $notice) {
				$requete="INSERT INTO import_marc (no_notice, notice, origine) VALUES ($n,'".addslashes($notice)."','$origine')";
				pmb_mysql_query($requete);
				$n++;
				$t=array();
				$t["POS"]=$n;
				$t["LENGHT"]=1;
				$index[]=$t;
			}
			$i++;
		}
		return $index;
	}
}

?>