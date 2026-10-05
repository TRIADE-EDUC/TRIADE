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
	$sep="\t";
	
	print $espace;
	
	function unhtmlentities ($string) {
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr ($string, $trans_tbl);
	}
		
	foreach($t_parcours as $key=>$value){	
		$t=$t_table[$key]['ATTRS'];
		print "Table".$sep.$sep.$t_table[$key]['NAME'].$ln;
		print "Description".$sep.$t_table[$key]['DESC'].$ln.$ln;
		print "Nom champ".$sep."Type".$sep."Signe".$sep."Infos. complémentaires".$sep."Réf. à d'autres tables".$sep."Valeur par défaut".$sep."Description".$sep.$ln.$ln;
		
		foreach ($t as $k=>$v)
		{
			if(strlen($v['SIGNE'])<12){$sign="Signé";}else{ $sign="Non signé";}
			$cle="Clé primaire";
			if(strlen($v['KEY'])==0){$cle="";}elseif(strlen($v['KEY'])>20){$cle="Clé étrangère";}
			print $v['NAME'].$sep.$v['TYPE'].$sep.$sign.$sep.$cle.$sep.$t_table[$v['REF']]['NAME'].$sep.$v['DEFVAL'].$sep.$v['DESC'].$ln;
		}
		
		print $ln.$ln;
		
	}
	

			


