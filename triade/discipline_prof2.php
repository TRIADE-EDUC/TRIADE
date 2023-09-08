<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/acces.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuprof");
$cnx=cnx();
include_once("./librairie_php/ajax-select.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<form name=formulaire  onsubmit="return valide_discipline2()" method=post action='discipline_prof3.php'>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGDISC37 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<BR>
<!-- // fin  -->
<?php
// affichage de la classe
$ident=array('sClasseGrp','cgrp','sMat');
$HPV=hashPostVar($ident);
unset($ident);
$listTmp=explode(":",$HPV[cgrp]);
unset($HPV[cgrp]);
$HPV[cid]=$listTmp[0];
$HPV[gid]=$listTmp[1];
unset($listTmp);
if($HPV[gid]){
	$who2="groupe : ".chercheGroupeNom($HPV[gid]);
        $who="<font color=\"red\"> groupe : ".chercheGroupeNom($HPV[gid]) ."</font>";
        $saisie_classe=$HPV[gid];
        if($HPV[gid]){
                $gid=$HPV[gid];
                $sqlIn=<<<SQL
                SELECT
                        liste_elev
                FROM
                        ${prefixe}groupes
                WHERE
                        group_id='$gid'
SQL;
              $curs=execSql($sqlIn);
                $in=chargeMat($curs);
              freeResult($curs);
                $in=$in[0][0];
              $in=substr($in,1);
        $in=substr($in,0,-1);
              $sql="
                SELECT
                        elev_id,elev_id,
                ";
                $sql .= " CONCAT( upper(trim(nom)),' ',trim(prenom) ) ";
                $sql .= "
                FROM
                        ${prefixe}eleves
                WHERE
                        elev_id IN ($in)
                ORDER BY
                        nom
                ";
              unset($in);
		$curs=execSql($sql);
              unset($sql);
              $data=chargeMat($curs);
                freeResult($curs);
              unset($curs);
        }
}else{
      	$cl=chercheClasse($HPV[cid]);
	$who2="classe : ".$cl[0][1];
        $saisie_classe=$HPV[cid];
      	$who=" en <font color=\"red\"> <?php print LANGABS31?> ".$cl[0][1] ."</font> année scolaire : <font color=\"red\">  $anneeScolaire </font>";
      	unset($cl);
//        $sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";

	$sql=" SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' UNION ALL SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$saisie_classe' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY s.nom";
        $res=execSql($sql);
        $data=chargeMat($res);
        $cl=$data[0][0];
}

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<UL><font class="T2"> <?php print  LANGDISC6 ?>  <?php print $who?></font><BR><BR>
<font class="T2"><?php print LANGDISC7 ?> :</font> <select name=saisie_sanction onchange="searchRequest(this,'sanction','rien','formulaire','saisie_motif')"  >
<option value="-1" STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_category();
?>
</select>
<BR><BR>
<font class="T2"><?php print LANGDISC8 ?> : </font><select name="saisie_motif">
<option></option>
</select>

<BR><BR>
<input type=hidden name="saisie_qui" value="<?php print $_SESSION["id_pers"] ?>">
<font class=T2>Description des faits : </font><br><br>
<textarea name="description_fait" cols=80 rows=5></textarea>
<br><br>
<font class="T2"><?php print LANGPROFJ ?> : </font><br><br>
<textarea name="devoir_a_faire" cols=80 rows=5></textarea>


</UL>
<table border="1" bordercolor="#000000" width="100%" id="bordure"  style="border-collapse: collapse;" >
<?php
$sub=0;
if (count($data) <= 0 ) {
        print("<tr><td align=center id='bordure' valign=center><BR>".LANGRECH1."<BR><BR></td></tr>");
}else{
?>
<tr>
<td bgcolor="yellow" >&nbsp;<B><?php print LANGNA1 ?> <?php print LANGNA2 ?></B></td>
<?php if (RETENUPROF == "oui") { ?>
<td bgcolor="yellow" width=5 align=center><B>&nbsp;<?php print LANGDISC11 ?>&nbsp;</B></td>
<?php } ?>
<td bgcolor="yellow" width=110 align=center><B>Devoir pour le</B></td>
<td bgcolor="yellow" align=center>&nbsp;
<?php 
$mess="<font face=Verdana size=1><B><font color=red>C</font></B>ochez la case si l\'élève est concerné par la sanction.</FONT>";
$information="Attention";
if ((LAN == "oui") && (AGENTWEB == "oui")) {
	$vocal="M12";
	$information="Agent Web";
	$mess="<iframe width=100 height=100 src=\'http://www.triade-educ.org/agentweb/agentmel.php?inc=5&mess=$vocal\'  MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no align=left ></iframe><br>$mess" ;
}
?>
	<A href='#' onMouseOver="AffBulle3('<?php print $information?>','./image/commun/warning.jpg','<?php print $mess ?>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>&nbsp;</td>
</tr>
<?php
for($i=0;$i<count($data);$i++) {
        
	print "<tr id='tr$i' class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\">";
	print "<td>";
	print "&nbsp;".trunchaine(ucwords($data[$i][2])." ".ucwords($data[$i][3]),25);
	$data2=cherche_sanction_day($data[$i][1]);
	if (count($data2) > 0) {
		print "&nbsp;&nbsp;&nbsp;<A href='#' onMouseOver=\"AffBulle3('Information','./image/commun/warning.jpg','<font face=Verdana size=1><B><font color=red>".count($data2)."</font></b> santion(s) déjà attribuée(s) aujourd\'hui</B></FONT>'); window.status=''; return true;\" onMouseOut='HideBulle()'><img src='./image/commun/warning.gif' border='0' /></a>";	
	}
	print "</td>";

	if (RETENUPROF == "oui") { ?>
		<td align=center>
		<select name="saisie_retenu_<?php print $i?>" >
		<option value=0 STYLE='color:#000066;background-color:#CCCCFF'><?php print LANGNON ?></option>
		<option value=1 STYLE='color:#000066;background-color:#FCCCCC'><?php print LANGOUI ?></option>
		</select>
		</td>
<?php } ?>
<td width='130' >
<input type=text name="saisie_date_devoir_<?php print $i?>" size=10 onblur="valid_date(document.formulaire.saisie_date_retenue_<?php print $i?>,document.formulaire.saisie_retenu_<?php print $i?>)"><?php
include_once("librairie_php/calendar.php");
calendarpopup("id1$i","document.formulaire.saisie_date_devoir_$i",$_SESSION["langue"],"0");
?>
</td>
<td align=center>
<input type=checkbox name="saisie_choisi_<?php print $i?>" onClick="DisplayLigne('tr<?php print $i?>')" >
<input type=hidden name=saisie_pers_<?php print $i?> value="<?php print $data[$i][1]?>">
</td>
</tr>
        <?php
        }
	$sub=1;
      }
print "</table>";
?>
<?php if ($sub == 1) { ?>
<BR>
<input type=hidden name=saisie_id value="<?php print count($data)?>">
<input type=hidden name=idclasse value="<?php print $who2 ?>">
<script>var nb=<?php print count($data)?>;</script>
<table align=center border=0><tr><td>
<script language=JavaScript>buttonMagicSubmit("Enregistrer Sanction(s)","rien"); //text,nomInput</script>
</td></tr></table>
<br>
<?php } ?>
     <!-- // fin  -->
     </td></tr></table>
     </form>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if ($_SESSION[membre] == "menuadmin") :
       print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       print "</SCRIPT>";
   else :
      print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      print "</SCRIPT>";

      top_d();

      print "<SCRIPT language='JavaScript' ";
     print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
     print "</SCRIPT>";

       endif ;
     ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
