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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body bgcolor="#FAEBD7" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>

             <!-- // texte du menu qui defile   -->
               <?php include("./librairie_php/lib_defilement.php"); ?>
             <!-- // fin du texte   -->

             </TD><td width="472" valign="middle" rowspan="3" align="center">

             <!--   -->
             <div align='center'><?php top_h(); ?>
             <!--  -->

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr bgcolor="#666666">
<td height="2"> <b><font  color="red"><font  color="#FFFFFF">Affectation d'un élève à un stage</font></b></td>
</tr>
<tr bgcolor="#CCCCCC">
<td >
     <!-- // debut form  -->
     <blockquote><BR>
               <?php print LANGELE4?> : <select id="saisie_classe" name="saisie_classe">
                                   <option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select> <BR>
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
</UL></UL></UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>
 
<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if(isset($_POST["consult"]) || isset($_POST["saisie_classe"]) ) {


if(isset($_POST["create"])) {
	if ( ($_POST["idstage"] != 0) && ($_POST["ident"] != 0) ) {
		$cr=create_eleve_stage($_POST["ideleve"],$_POST["ident"],$_POST["lieu"],$_POST["ville"],$_POST["idprof"],$_POST["date"],$_POST["loge"],$_POST["nourri"],$_POST["xservice"],$_POST["raison"],$_POST["info"],$_POST["idstage"],$_POST["postal"]);
		if($cr == 1){
			history_cmd($_SESSION["nom"],"CREATION","Eleve Stage");
			alertJs("Création Enregistré");
		}else {
			error(0);
		}
	}
}


$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<BR><BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#CCCCCC" height="85">
<tr bgcolor="#666666">
	<td colspan="3" height="2"><font  color="#FFFFFF">
		<?php print LANGELE4?> : <font color="#FCE4BA"><B><?php print $cl?></font>
	</font></td>
</tr>
<?php
if( count($data) <= 0 )
	{
	print("<tr><td align=center valign=center>".LANGRECH1."</td></tr>");
	}
else {
?>
<tr><td>&nbsp;</td></tr>
<tr bgcolor="#FFFFFF"><td> <B><?php print ucwords(LANGIMP8)?></B></td><td colspan=2><B><?php print ucwords(LANGIMP9)?></B></td></tr>
<?php
for($i=0;$i<count($data);$i++)
	{
	?>
	<tr>
	<td bgcolor="#FFFFFF"><?php print strtoupper($data[$i][2])?></td>
	<td bgcolor="#FFFFFF"><?php print ucwords($data[$i][3])?></td>
	<td bgcolor="#FFFFFF" width=5><input type=button onclick="open('gestion_stage_affec_eleve_2.php?id=<?php print $data[$i][1]?>&idclasse=<?php print $saisie_classe ?>','_parent','')" value="Affecter" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td>

	</tr>
	<?php
	}
      }
print "</table>";
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier 
Pgclose();
?>
</BODY>
</HTML>
