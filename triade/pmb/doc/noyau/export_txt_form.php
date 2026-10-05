<?php
header("Content-Type: application/download\n");
header("Content-Disposition: attachement; filename=\"tables_export.txt\"");

	include 'db_doc.php';
	$doc=new db_doc();
	$doc->parsage();
	$t_table=$doc->get_table();
	$t_relation=$doc->get_relation();
	$t_parcours=$doc->get_parcours();
	$ln="\r\n";

	
	function unhtmlentities ($string) {
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}
		
	foreach($t_parcours as $key=>$value){	
		$t=$t_table[$key]['ATTRS'];
		print "Table".sep(2).$t_table[$key]['NAME'].$ln;
		print "Description".sep(1).$t_table[$key]['DESC'].$ln.$ln;
		print sep(13)."   Références".$ln.sep(1)."Nom champ".sep(2)."Type".sep(3)."Signe".sep(2)."Infos. complémentaires".sep(2)."à d'autres tables".sep(1)."Valeur par défaut".sep(1)."Description".$ln.$ln;
		
		foreach ($t as $k=>$v)
		{
			if(strlen($v['SIGNE'])<12){$sign="Signé";}else{ $sign="Non signé";}
			$cle="Clé primaire";
			if(strlen($v['KEY'])==0){$cle="";}elseif(strlen($v['KEY'])>20){$cle="Clé étrangère";}
			print sep(1).elt($v['NAME']).elt($v['TYPE']).elt($sign).elt($cle).elt($t_table[$v['REF']]['NAME']).elt($v['DEFVAL']).elt($v['DESC']).$ln;
		}
		
		print $ln.$ln;
		
	}
	
	function sep($int){
		$sep="";
		for($i=0;$i<$int;$i++){
			$sep=$sep."\t";
		}
		return $sep;
	}
	
	function elt($str){
		$lg=strlen($str);
		if($lg<2){$i=3;}elseif($lg<8){$i=3;}elseif ($lg>15){$i=1;}else{$i=2;}
		return $str.sep($i);
	}
	
