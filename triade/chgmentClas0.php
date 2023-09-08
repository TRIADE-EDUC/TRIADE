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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php");
if (empty($_SESSION["adminplus"])) {
	print "<script>";
	print "location.href='./base_de_donne_key.php'";
	print "</script>";
	exit;
}
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method='post' name="formulaire"  >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE23 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br />
     <!-- // debut form  -->
<blockquote>
<br>

<font class=T2><?php print "Indiquer l'année scolaire en cours des étudiants" ?> :</font> <select name="annee">
<?php filtreAnneeScolaireSelectNote('',4) ?>
<option value="" id='select1' ><?php print LANGMESS379 ?></option>
</select>

<br><br>

<font class=T2><?php print LANGELE4 ?> :</font> <select id="saisie_classe" name="saisie_classe">
<option  STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_classe(); // creation des options
?>
</select> <br /><br />

<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script></UL></UL></UL>
<BR><br /></blockquote>
</form>

<?php
if(isset($_POST["consult"])) {
?>
	<center>
	<?php print LANGBASE25 ?> -> <a href="#" onclick="open('chgmentClashelp.php','help','width=450,height=300');"><img src="./image/help.gif" border=0></a>
	<br><br>
	</center>
<?php
}
?>
<!-- // fin form -->
</td></tr></table>
<?php
// affichage de la classe
if(isset($_POST["consult"])) {
	$saisie_classe=$_POST["saisie_classe"];
	$annee=$_POST["annee"];
	$sql="SELECT c.libelle,e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c WHERE e.classe='$saisie_classe' AND c.code_class='$saisie_classe' AND (e.annee_scolaire='$annee' OR e.annee_scolaire IS NULL ) ORDER BY e.nom,e.prenom";
	$res=execSql($sql);
	$data=chargeMat($res);
	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];
?>
<BR><BR><BR>
<form method=post action="chgmentClas1.php" name='formulairec' >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2" colspan=3><b><font   id='menumodule1' ><?php print "Inidquer l'année scolaire future pour les étudiants " ?> <font id="color2"><B><?php print $cl?></font> :  
			<select name="anneefutur" onChange="valideSelect(this.value)" >
			<?php filtreAnneeScolaireSelectNote('',2) ?>
			</select>  </font></td></tr>
<?php
if( count($data) <= 0 ) {
	print("<tr id='cadreCentral0'><td align=center valign=center>".LANGPROJ6."</td></tr>");
}else{
?>
	<tr><td bgcolor="yellow"> <B><?php print LANGNA1 ?></B></td><td bgcolor="yellow"><B><?php print LANGNA2 ?></B></td> <td bgcolor="yellow"><B><?php print LANGBASE36 ?></B></td></tr>

<?php
for($i=0;$i<count($data);$i++) {
	?>
	<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<td ><?php print infoBulleEleveSansLoupe($data[$i][1],strtoupper($data[$i][2]))?>
			      <input type=hidden name="idEleve_<?php print $i ?>" value="<?php print $data[$i][1]?>" ></td>
	<td ><?php print ucwords($data[$i][3])?></td>
	<td width=5%><select id="new_classe_<?php print $i ?>" name="new_classe_<?php print $i ?>" disabled='disabled' >
	<option value='rien'  STYLE='color:#000066;background-color:#FCE4BA' ><?php print LANGCHOIX ?></option>
	<option value='quit'  STYLE='color:#000066;background-color:red' ><?php print LANGBASE37 ?></option>
	<option value='sansclasse'  STYLE='color:#000066;background-color:yellow' ><?php print "Sans Classe" ?></option>
	<?php
	select_classe2('10'); // creation des options
	?>
	</select>
	</td>
	</tr>
	<?php
	}
      }
print "</table>";
print "<input type=hidden name='nbEleve' value='".count($data)."'>";
print "<br><br>";
?>
<script>
function valideSelect(choix) {
	if (choix != "") {
		for(i=0;i<<?php print count($data)?>;i++) {
			document.getElementById('new_classe_'+i).disabled=false;
		}
	}else{
		for(i=0;i<<?php print count($data)?>;i++) {
			document.getElementById('new_classe_'+i).disabled=true;
			document.getElementById('new_classe_'+i).selectedIndex=0;
		}

	}	
}
</script>
<?php
	if( count($data) > 0 ) {
?>
		<ul><ul><ul><script language=JavaScript>buttonMagicSubmit("<?php print LANGBASE38 ?>","rien"); //text,nomInput</script></ul></ul></ul><br><br>
<?php
	}
}
?>
</form>
<br><br>

<form method="post" action="chgmentClas00.php" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE23?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<br>
<ul>
<font class=T2><?php print "Indiquer l'année scolaire de l'étudiant en cours" ?> :</font> <select name="annee">
					<?php filtreAnneeScolaireSelectNote('',4) ?>
					<option value="" id='select1' ><?php print LANGMESS379 ?></option>
					</select>

<br><br>
<font class=T2><?php print LANGMESS381 ?></font><br><br>
</ul>
<ul>
<?php checkbox_classe2() ?>
</ul>
<UL><UL><UL><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consult"); //text,nomInput</script></UL></UL></UL>
<br><br><br>
</td></tr></table>
</form>



<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<script language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</script>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
