<?php 

include 'db_doc.php';

$doc=new db_doc();
$doc->parsage();
$t_table=$doc->get_table();
$t_parcours=$doc->get_parcours();
$t_relation=$doc->get_relation();

print "<style>td { font-size:12px;}</style>";

print "<html><body><table style='width:100%; border-spacing:0px; padding:1px'><tbody>";

$pair=1;
	//On parcourt les tables triées dans l'ordre alphabetique
foreach($t_parcours as $k=>$v) {
	if ($pair==1){$color='#DDDDDD';}else{$color='#FFFFFF';	}
	print "<tr bgcolor=$color><td>";
		//Nom table + description
	print "<a name=$k>	<a href=\"\"onClick=\"javascript:sty=document.getElementById('t$k').style.display; if (sty=='') { document.getElementById('t$k').style.display='none'; document.getElementById('i$k').src='plus.gif';} else { document.getElementById('t$k').style.display=''; document.getElementById('i$k').src='minus.gif';} return false;\"><img src=\"plus.gif\" border=0 id=\"i$k\"></a><b>";
	print "<a name='$k' href='#$k' onclick=\"parent.description.location='db_description.php?table=$k'\">".htmlentities($t_table[$k]['NAME'],ENT_QUOTES,'iso-8859-1')."</a><br />
		<td/><tr/>
	<tr bgcolor=$color><td>";
	print "<i>".htmlentities($t_table[$k]['DESC'],ENT_QUOTES,'iso-8859-1')."<i/><br />
	<td/><tr />";
		//tables liées
	print "<tr id='t".$k."' style='display:none'>
	<td style='border-width:1px;border-style:solid;border-color:#000000'>
	<blockquote>";
		//raccourcis vers les tables liées
	foreach ( $t_table[$k]['LIENS'] as $val=>$ind){
		if ($t_relation[$ind]['T_PERE']==$k){
			print "<a href='#".$t_relation[$ind]['T_FILS']."'onclick=\"parent.description.location='db_description.php?table=".$t_relation[$ind]['T_FILS']."'\">".$t_table[$t_relation[$ind]['T_FILS']]['NAME']."</a>";
		}else{
			print "<a href='#".$t_relation[$ind]['T_PERE']."'onclick=\"parent.description.location='db_description.php?table=".$t_relation[$ind]['T_PERE']."'\">".$t_table[$t_relation[$ind]['T_PERE']]['NAME']."</a>";
		}
		print $t_relation[$ind]['DESC']."<br/>";
		
	}
	if ($pair==1){$pair=0;}else{$pair=1;}
	print "</blockquote></td></tr>";
	
}
print "<tbody/>	<table/></body></html>";
