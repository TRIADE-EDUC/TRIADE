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
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// affichage de la classe
$anneeScolaire=$_POST["anneeScolaire"];
if(isset($_POST["sClasseGrp"])) {
	$saisie_classe=$_POST["sClasseGrp"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' AND annee_scolaire='$anneeScolaire' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	$cl=$data[0][0];
?>
<form method='post' action='compta_fiche3.php' name='formulaire' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1' > Imprimer fiche d'état des règlements / <?php print LANGELE4 ?> : <font id="color2"><B><?php print ucwords($cl) ?></font></font></td>
</tr>
<?php
	if( count($data) <= 0 ) {
		print("<tr id='cadreCentral0' ><td align=center valign=center>".LANGPROJ6."</td></tr>");
	}else{
?>
		<tr bgcolor="#FFFFFF"><td> <B><?php print LANGELE2 ?></B></td><td><B><?php print LANGELE3 ?></B></td>
		<td width=5%><input type='checkbox' onclick='checktous();' name='tous' ></td></tr>
	<?php
		for($i=0;$i<count($data);$i++) {
		?>
			<tr  id="tr<?php print $i ?>" class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'"  >
			<td><?php print strtoupper($data[$i][2])?></td>
			<td><?php print ucwords($data[$i][3])?></td>
			<td><input type='checkbox' value="<?php print $data[$i][1] ?>" name='ideleve[]' onClick="DisplayLigne('tr<?php print $i ?>');" /></td>
			</tr>
		<?php
		}
		print "<tr bgcolor='#FFFFFF' ><td colspan='3' align='center'>";
		print "<br><input type='submit' value='Imprimer' class='BUTTON' /><br><br></td></tr>";
	}
	print "</table>";
	print "<input type='hidden' name='anneeScolaire' value='$anneeScolaire' />";
	print "</form>";
}
?>
<script>
function checktous() {
	var nb=<?php print  $i ?>;
	for(var i=0;i<=nb;i++) {
		if (document.formulaire.tous.checked == false) {
			document.formulaire.elements[i].checked=false;
			document.getElementById('tr'+i).style.backgroundColor='';
		}else{
			document.formulaire.elements[i].checked=true;
			document.getElementById('tr'+i).style.backgroundColor='#c0c0c0';
		}
	}
	if (document.formulaire.tous.checked == false) {
		document.formulaire.tous.checked=false;
	}else{
		document.formulaire.tous.checked=true;
	}

}
</script>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
?>

<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
