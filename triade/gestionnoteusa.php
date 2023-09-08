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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGNOTEUSA1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<BR>
<?php
if (NOTEUSA == "non") {
	print "<center><font color=red class='T2' >".LANGMESS37.".</font></center>";
}else {

	include_once("librairie_php/db_triade.php");
	validerequete("menuadmin");
	// connexion P
	$cnx=cnx();
	error($cnx);

	if(isset($_GET["idsupp"])) {
		$cr=supp_config_note_usa($_GET["idsupp"]);
		if ($cr) {
			history_cmd($_SESSION["nom"],"SUPPRESSION","config note USA");
		}
	}

	if(isset($_POST["create"])) {
		if  ((empty($_POST["libelle"])) ||  ( ! is_numeric($_POST["max"])) ||  ( ! is_numeric($_POST["min"]))   ) {
			print "<ul><font color=red>".LANGCOM2."</font></ul>";
		}else{
			if ($_POST["min"] >= $_POST["max"]){
				print "<ul><font color=red>".LANGCOM1."</font></ul>";
			}else{
				$cr=enr_config_note_usa(trim($_POST["libelle"]),trim($_POST["min"]),trim($_POST["max"]));
				if ($cr) {
					history_cmd($_SESSION["nom"],"CONFIG","note USA");
				}
			}
		}
	}

?>

<ul>
<?php print LANGNOTEUSA2 ?>
<br><i><?php print LANGNOTEUSA3 ?></i><br>
<i>(Ne rentrer que des entiers.)</i>
<hr>
<br>
<form method=post>
<?php print LANGNOTEUSA4 ?> <input type=text size=2 name=min value='<?php print $_POST["min"]?>'>  <?php print LANGNOTEUSA4bis ?>
<input type=text size=2 name=max value='<?php print $_POST["max"]?>'>  <?php print LANGNOTEUSA4ter ?>
<input type=text size=2 name=libelle value='<?php print $_POST["libelle"]?>'>
<br><br><br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create"); //text,nomInput</script>
</form>
<BR><br>
</ul>
<br>
<table align=center border=1 bgcolor="#FFFFFF">
<tr><td align=center>&nbsp;<?php print LANGNOTEUSA5 ?> &nbsp;</td><td align=center>&nbsp;<?php print LANGNOTEUSA5bis ?>&nbsp;</td>
<td align=center>&nbsp;<?php print LANGNOTEUSA5ter ?>&nbsp;</td></tr>
<?php
$data=aff_config_note_usa();
// id,libelle,min,max
for($i=0;$i<count($data);$i++) {
	print "<tr><td align=center>".$data[$i][2]."</td><td align=center>".$data[$i][3]."</td><td align=center><b>".$data[$i][1]."</b></td>";
	print "<td><input type=button value='Supprimer' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;' onclick=\"open('gestionnoteusa.php?idsupp=".$data[$i][0]."','_parent','');\" ></td></tr>";

}
?>
</table>
<br><br>


<?php brmozilla($_SESSION["navigateur"]); ?>

<?php
}

?>

<!-- // fin  -->
</td></tr></table>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>

</BODY></HTML>
