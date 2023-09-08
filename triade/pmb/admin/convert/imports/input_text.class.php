<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: input_text.class.php,v 1.1 2018-07-25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_input.class.php");

class input_text extends convert_input {

	public function _get_n_notices_($fi,$file_in,$input_params,$origine) {
		//pmb_mysql_query("delete from import_marc");
		$index=array();
		$fcontents=fread($fi,filesize($file_in));
		$i_=0;
		$i=0;
		$n=1;
		$flag_head=0;
		
		if ($input_params["HEADER"]=="yes") {
			$flag_head=1;
		}
		
		//Ajout du saut de ligne pour la dernière 
		$fcontents.="\r\n";
		
		while ($fcontents!="") {
			//Recherche de l'élément de fin de ligne
			$i2=strpos($fcontents,"\r\n");
			$i1=strpos($fcontents,"\n");
			if (($i1)&&($i2)){
				if($i1<$i2){
					$i=$i1;
					$endchar=0;
				}else{
					$i=$i2;
					$endchar=1;
				}
			}else{
				if ($i2!==false) {
					$i=$i2;
					$endchar=1;
				} else {
					$i=$i1;
					$endchar=0;
				}
			}
			if ($i!==false) {
				//Si trouvé
				
				//Si 1ère ligne passée
				if (!$flag_head) {
					$sub=substr($fcontents,0,$i);
					if (trim($sub)!="") {
						$t=array();
						$t["POS"]=$i_;
						$t["LENGHT"]=$i;
						$requete="insert into import_marc (no_notice, notice, origine) values($n,'".addslashes($sub)."','$origine')";
						pmb_mysql_query($requete);
						$n++;
						$fcontents=substr($fcontents,$i+$endchar+1);
						$index[]=$t;
					} else {
						$fcontents=substr($fcontents,$i+$endchar+1);
					}
				} else {
					$flag_head=0;
					$fcontents=substr($fcontents,$i+$endchar+1);
				}
				$i_=$i+$endchar+$i_+1;
				
			} else {
				$fcontents="";
			}
		}
		return $index;
	}
}
