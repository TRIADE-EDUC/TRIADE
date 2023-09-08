<?php
      session_start();
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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/db_triade_admin.php"); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<?php
      if ($_GET["saisie_efface"] == "oui") {
      		$fic="../data/maintenance.txt";
      		if (file_exists($fic)) {
	              unlink($fic);
		}
      }
?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Service de maintenance</font></b></td></tr>
<tr id='cadreCentral0' ><td > <p align="left"><font color="#000000">
<TABLE  bordercolor="#000000" border=0 width=100%>
<TR><TD>
<font class=T1>
<form method=post name=formulaire >
<BR><DIV align=right>
<input type=submit Value="Ajouter la maintenance" name=create STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">&nbsp;&nbsp;&nbsp;&nbsp;
<input type=button Value="Supprimer la maintenance" onclick="open('maintenance.php?saisie_efface=oui','_parent','')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">&nbsp;&nbsp;&nbsp;&nbsp;</div><BR>
<ul>
<font class="T2">
Le service Triade sera indisponible le <input type=text value="jj/mm/aaaa" readonly name="dates" size=12>
<?php
 include_once("../librairie_php/calendar.php");
 calendarDim("id2","document.formulaire.dates",$_SESSION["langue"],"0","0");
?>
<br><br>
entre
<select name=time1>
<option value="00h00" STYLE='color:#000066;background-color:#FCE4BA'>00h00</option>
<option value="01h00" STYLE='color:#000066;background-color:#FCE4BA'>01h00</option>
<option value="02h00" STYLE='color:#000066;background-color:#FCE4BA'>02h00</option>
<option value="03h00" STYLE='color:#000066;background-color:#FCE4BA'>03h00</option>
<option value="04h00" STYLE='color:#000066;background-color:#FCE4BA'>04h00</option>
<option value="05h00" STYLE='color:#000066;background-color:#FCE4BA'>05h00</option>
<option value="06h00" STYLE='color:#000066;background-color:#FCE4BA'>06h00</option>
<option value="07h00" STYLE='color:#000066;background-color:#FCE4BA'>07h00</option>
<option value="08h00" STYLE='color:#000066;background-color:#FCE4BA'>08h00</option>
<option value="09h00" STYLE='color:#000066;background-color:#FCE4BA'>09h00</option>
<option value="10h00" STYLE='color:#000066;background-color:#FCE4BA'>10h00</option>
<option value="11h00" STYLE='color:#000066;background-color:#FCE4BA'>11h00</option>
<option value="12h00" STYLE='color:#000066;background-color:#FCE4BA'>12h00</option>
<option value="13h00" STYLE='color:#000066;background-color:#FCE4BA'>13h00</option>
<option value="14h00" STYLE='color:#000066;background-color:#FCE4BA'>14h00</option>
<option value="15h00" STYLE='color:#000066;background-color:#FCE4BA'>15h00</option>
<option value="16h00" STYLE='color:#000066;background-color:#FCE4BA'>16h00</option>
<option value="17h00" STYLE='color:#000066;background-color:#FCE4BA'>17h00</option>
<option value="18h00" STYLE='color:#000066;background-color:#FCE4BA'>18h00</option>
<option value="19h00" STYLE='color:#000066;background-color:#FCE4BA'>19h00</option>
<option value="20h00" STYLE='color:#000066;background-color:#FCE4BA'>20h00</option>
<option value="21h00" STYLE='color:#000066;background-color:#FCE4BA'>21h00</option>
<option value="22h00" STYLE='color:#000066;background-color:#FCE4BA'>22h00</option>
<option value="23h00" STYLE='color:#000066;background-color:#FCE4BA'>23h00</option>
</select>
et
<select name=time2>
<option value="00h00" STYLE='color:#000066;background-color:#FCE4BA'>00h00</option>
<option value="01h00" STYLE='color:#000066;background-color:#FCE4BA'>01h00</option>
<option value="02h00" STYLE='color:#000066;background-color:#FCE4BA'>02h00</option>
<option value="03h00" STYLE='color:#000066;background-color:#FCE4BA'>03h00</option>
<option value="04h00" STYLE='color:#000066;background-color:#FCE4BA'>04h00</option>
<option value="05h00" STYLE='color:#000066;background-color:#FCE4BA'>05h00</option>
<option value="06h00" STYLE='color:#000066;background-color:#FCE4BA'>06h00</option>
<option value="07h00" STYLE='color:#000066;background-color:#FCE4BA'>07h00</option>
<option value="08h00" STYLE='color:#000066;background-color:#FCE4BA'>08h00</option>
<option value="09h00" STYLE='color:#000066;background-color:#FCE4BA'>09h00</option>
<option value="10h00" STYLE='color:#000066;background-color:#FCE4BA'>10h00</option>
<option value="11h00" STYLE='color:#000066;background-color:#FCE4BA'>11h00</option>
<option value="12h00" STYLE='color:#000066;background-color:#FCE4BA'>12h00</option>
<option value="13h00" STYLE='color:#000066;background-color:#FCE4BA'>13h00</option>
<option value="14h00" STYLE='color:#000066;background-color:#FCE4BA'>14h00</option>
<option value="15h00" STYLE='color:#000066;background-color:#FCE4BA'>15h00</option>
<option value="16h00" STYLE='color:#000066;background-color:#FCE4BA'>16h00</option>
<option value="17h00" STYLE='color:#000066;background-color:#FCE4BA'>17h00</option>
<option value="18h00" STYLE='color:#000066;background-color:#FCE4BA'>18h00</option>
<option value="19h00" STYLE='color:#000066;background-color:#FCE4BA'>19h00</option>
<option value="20h00" STYLE='color:#000066;background-color:#FCE4BA'>20h00</option>
<option value="21h00" STYLE='color:#000066;background-color:#FCE4BA'>21h00</option>
<option value="22h00" STYLE='color:#000066;background-color:#FCE4BA'>22h00</option>
<option value="23h00" STYLE='color:#000066;background-color:#FCE4BA'>23h00</option>
</select>
</font>
</ul>
               <!-- // debut de la saisie -->
               <?php
	       if (isset($_POST["create"])) {
	       		list($jour,$mois,$annee)=preg_split ('/\//', $_POST["dates"],3);
	       		if (checkdate($mois,$jour,$annee)) {
		       		$fic="../data/maintenance.txt";
			       	$fichier=fopen("$fic","w");
				$donnee=fwrite($fichier,$_POST["dates"].":".$_POST["time1"].":".$_POST["time2"]);
			}else {
				alertJs("Date refusée");
			}
		}
               ?>
</form>
</font>

<?php
$fic="../data/maintenance.txt";
$disabled="";
if (file_exists($fic)) {
	print "<br><br><br>";
       	print "&nbsp;&nbsp;&nbsp;<i>Actuellement en position sur la première page d'accès de TRIADE :</i> ";
       	print "<center>";
       	print "<hr><br>";
       	$fichier=fopen("$fic","r");
       	$message=fread($fichier,"100");
       	list($date,$time1,$time2)= preg_split ("/:/", $message, 3);
       	$message="<b><font class='T2'><font color=red>ATTENTION</font></b><br><br>";
       	$message.="Une intervention est prévue sur le logiciel <br><br>" ;
       	$message.="Le service Triade sera inaccessible le <b>$date</b> <br><br> entre  <b>$time1 et $time2</font>";
       	list($heure1,$minute1)=preg_split("/h/", $time1,2);
       	list($heure2,$minute2)=preg_split("/h/", $time2,2);
       	print $message;
       	print "<br><br>";
}


?>
</center>
<br><br>
</TD></TR></TABLE>
                   <!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
