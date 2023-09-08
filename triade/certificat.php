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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE33?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td >
<br><br>
<table border=0 align=center width=100% >
<tr><td  align="right"  width=50% >
<form method=post onsubmit="return valide_consul_classe()" name="formulaire"  >
<font class=T2><?php print LANGELE4?> :</font> <select id="saisie_classe" name="saisie_classe">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php
select_classe(); // creation des options
?>
</select></td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script>
</form>
</td></tr>
<tr><td colspan=2 height=20>
</td></tr>
<tr><td align="right" >
<font class=T2><?php print LANGMESS325 ?> </font> </td><td>
<script language=JavaScript>buttonMagic("<?php print CLICKICI?>","certificat_param.php","certif_create","scrollbars=yes,width=700,height=680",""); //text,nomInput</script>
<tr><td colspan=2 height=20></td></tr>
<tr><td align="right" >
<font class=T2><?php print LANGMESS326 ?> </font> </td><td align='top'>
<script language=JavaScript>buttonMagic("<?php print CLICKICI?>","certificat_param_import.php","_parent","",""); //text,nomInput</script></td></tr>
<?php
if (isset($_GET["idsup"])) {
	unlink("data/parametrage/certificat.rtf");
}

if (file_exists("data/parametrage/certificat.rtf")) {
	print "<tr><td></td><td >[&nbsp;<a href='telecharger.php?fichier=data/parametrage/certificat.rtf'>".LANGTMESS452."</a>&nbsp;]&nbsp;[&nbsp;<a href='certificat.php?idsup'>".LANGBT50."</a>&nbsp;]</td></tr>";
	$text=LANGTMESS451;
	print "<tr><td colspan=2 align=center><br /><br /><font color='red'>$text</font></td></tr>";
}
?>
</table>
<br><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>


<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if ((isset($_POST["consult"])) || (isset($_POST["num_certif"])))  {
$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];

$num_certif=$_POST["num_certif"];

?>
<BR><BR><BR>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan="3"><b><font id='menumodule1' >
<?php print LANGELE4?> : <font id='color2'><b><?php print $cl?></b></font> / <?php print LANGCOM3 ?> <font id="color2"><b><?php print count($data) ?></b></font></font></td></tr>
<?php
if( count($data) <= 0 )	{
	print("<tr id='cadreCentral0'  ><td align=center valign=center>".LANGRECH1."</td></tr>");
}else {
	if (!file_exists("data/parametrage/certificat$num_certif.rtf")) {
		print "<tr id='cadreCentral0' ><td colspan=3><br><form method=post action='certificat1.php'>";
		print "<script language=JavaScript>buttonMagicSubmit('".LANGBT49."','tous');</script>";
		print "<input type=hidden name='idclasse' value=\"".$_POST["saisie_classe"]."\">";
		print "<input type=hidden name='num_certif' value='".$_POST["num_certif"]."' ></form>";
		?>
		<form method='post'><font class='T2'><?php print LANGTMESS453 ?> </font><select name='num_certif' onChange="this.form.submit()" >
		<?php if ($_POST["num_certif"] != "") print "<option value='".$_POST["num_certif"]."'>".preg_replace('/_/','',$_POST["num_certif"])."</option>"; ?>
		<option value=''></option>
		<option value='_A'>A</option>
		<option value='_B'>B</option>
		<option value='_C'>C</option>
		</select>
		<input type='hidden' name='saisie_classe' value='<?php print $saisie_classe ?>' /></form>
		<?php
		print "<br>";
		print "</td></tr>";
	}else{
		print "<tr id='cadreCentral0' ><td colspan=3><br><form method=post action='certificat12.php'>";
		print "<script language=JavaScript>buttonMagicSubmit('".LANGBT49."','tous');</script>";
		print "<input type=hidden name='idclasse' value=\"".$_POST["saisie_classe"]."\">";
		print "<input type=hidden name='num_certif' value='".$_POST["num_certif"]."' ></form>";
		?>
		<form method='post'><font class='T2'><?php print LANGTMESS453 ?> </font><select name='num_certif' onChange="this.form.submit()" >
		<?php if ($_POST["num_certif"] != "") print "<option value='".$_POST["num_certif"]."'>".preg_replace('/_/','',$_POST["num_certif"])."</option>"; ?>
		<option value=''></option>
		<option value='_A'>A</option>
		<option value='_B'>B</option>
		<option value='_C'>C</option>
		</select><input type='hidden' name='saisie_classe' value='<?php print $saisie_classe ?>' /></form>
		<?php
		print "<br>";
		print "</td></tr>";
	}
	?>

	<tr bgcolor="yellow"><td><B><?php print ucwords(LANGIMP8)?></B></td><td colspan=2><B><?php print ucwords(LANGIMP9)?></B></td></tr>
	<?php
	for($i=0;$i<count($data);$i++) {
	?>
		<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
		<td ><?php print strtoupper($data[$i][2])?></td>
		<td ><?php print ucwords($data[$i][3])?></td>
		<?php
		if (file_exists("data/parametrage/certificat$num_certif.rtf")) {
		?>
			<td width=165 align='center' >[<a href="certificat11.php?eid=<?php print $data[$i][1] ?>&num_certif=<?php print $num_certif?>"><?php print LANGCERT1 ?></a>]</td></tr>
		<?php }else{ ?>
			<td width=165 align='center' >[<a href="certificat1.php?eid=<?php print $data[$i][1] ?>&num_certif=<?php print $num_certif?>"><?php print LANGCERT1 ?></a>]</td></tr>
		<?php
		}
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
