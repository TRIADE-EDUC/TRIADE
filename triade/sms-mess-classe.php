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
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGSMS7?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<?php
if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) {
	$saisie_classe=$_POST["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);
	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];
}
?>
     <!-- // debut form  -->
     <blockquote><BR>
	       <font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe" >
				    <?php
				    print "<option id='select0' >".LANGCHOIX."</option>";
			 	    if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) {
					print "<option id='select1' selected='selected' value='".$_POST["saisie_classe"]."' >$cl</option>";   
				    }
				    select_classe(); // creation des options
				    ?>
				    </select> <BR>
<UL><UL><UL>
<table>
<tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","consult"); //text,nomInput</script></td><td>
<?php 
if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) {
	print "&nbsp;&nbsp; Tél : <select onChange='document.formulaire.submit()' name='choixtel' >
		<option  id='select0' >".LANGCHOIX."</option>
		<option value='cherchetelportable1' id='select1' >Tél. Portable 1</option>
		<option value='cherchetelportable2' id='select1' >Tél. Portable 2</option>
		<option value='cherchetel' id='select1' >Téléphone</option>
		<option value='cherchetelpere' id='select1' >Tél. Prof. Père</option>
		<option value='cherchetelmere' id='select1' >Tél. Prof. mère</option>
		<option value='cherchetelEleve' id='select1' >Tél. Elève</option>
	       </select>";
}
?>
</td></tr></table>
</UL></UL></UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>

<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if ((isset($_POST["consult"])) || (isset($_POST["choixtel"]))) {
	$o=0;
	$filtreSMS=config_param_visu('smsfiltre');
	$filtreSMS=$filtreSMS[0][0];
	$choixtel=$_POST["choixtel"];
?>
	<BR><BR><BR>
	<form method="post" action="sms-mess-classe1.php" >
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
	<tr id='coulBar0' ><td height="2" colspan="3"><b><font   id='menumodule1' >
	<?php print LANGELE4?> : <font id="color2"><b><?php print $cl?></b></font>&nbsp;&nbsp; <?php print LANGCOM3 ?><font id="color2"><b><?php print count($data) ?></b></font></font></td>
	</tr>
<?php 
	if( count($data) <= 0 ) {
		print("<tr><td align=center valign=center id='cadreCentral0'><font class=T2>".LANGRECH1."</font></td></tr>");
	} else {
?>
		<tr >
		<td bgcolor="yellow" ><B><?php print ucwords(LANGIMP8)." "; print ucwords(LANGIMP9); ?></B></td>
		<td bgcolor="yellow" width="50%" align="center"><table width='100%' ><tr><td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php print ucwords("Envoyer")?></b>
				</td><td  align='right' ><b>Tous</b> <input type='checkbox' onclick="tous()" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>
		</td></tr>
<?php
		for($i=0;$i<count($data);$i++) {
	?>
	<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<td><?php print strtoupper($data[$i][2])." " ; print trunchaine(ucwords($data[$i][3]),30); ?></td>
	<td>
<?php
	$idEleve=$data[$i][1];
	$tel=cherchetel($data[$i][1]);
	$tel1=cherchetelportable1($data[$i][1]); 
	$tel2=cherchetelportable2($data[$i][1]); 
	$tel3=cherchetelEleve($data[$i][1]); 
	$tel4=cherchetelpere($data[$i][1]); 
	$tel5=cherchetelmere($data[$i][1]); 

	print "<table border=0 width='100%' >";
		$tel1=preg_replace('/ /',"",$tel1);
		$tel1=preg_replace('/\./',"",$tel1);
		$tel1=preg_replace('/-/',"",$tel1);
		$tel1=preg_replace('/_/',"",$tel1);
		if (is_numeric($tel1)) {
		       if ($choixtel == "cherchetelportable1") { $checked1="checked='checked'"; } 
			$o++; 
			print "<tr><td align='right' width=50% >Tél. Portable 1 : </td><td>$tel1 <input $checked1 type=checkbox value='$idEleve#$tel1' name='tel$o' id='tel$o' /></td></tr>"; 
		}
		$tel2=preg_replace('/ /',"",$tel2);
		$tel2=preg_replace('/\./',"",$tel2);
		if (is_numeric($tel2)) { 
		       if ($choixtel == "cherchetelportable2") { $checked2="checked='checked'"; } 
			$o++; 
			print "<tr><td align='right' width=50% >Tél. Portable 2 : </td><td>$tel2 <input $checked2 type=checkbox value='$idEleve#$tel2' name='tel$o' id='tel$o' /></td></tr>"; 
		}
		$tel=preg_replace('/ /',"",$tel);
		$tel=preg_replace('/\./',"",$tel);
		if (is_numeric($tel)) { 
		       if ($choixtel == "cherchetel") { $checked="checked='checked'"; } 
			$o++; 
			print "<tr><td align='right' width=50% >Téléphone : </td><td>$tel  <input $checked type=checkbox value='$idEleve#$tel' name='tel$o' id='tel$o' /></td></tr>"; 
		}
		$tel3=preg_replace('/ /',"",$tel3);
		$tel3=preg_replace('/\./',"",$tel3);
		if (is_numeric($tel3)) { 
		       if ($choixtel == "cherchetelEleve") { $checked3="checked='checked'"; } 
			$o++; 
			print "<tr><td align='right' width=50% >Tél. Elève : </td><td>$tel3  <input $checked3 type=checkbox value='$idEleve#$tel3' name='tel$o' id='tel$o' /></td></tr>"; 
		}
		$tel4=preg_replace('/ /',"",$tel4);
		$tel4=preg_replace('/\./',"",$tel4);
		if (is_numeric($tel4)) { 
		       if ($choixtel == "cherchetelpere") { $checked4="checked='checked'"; } 
			$o++; 
			print "<tr><td align='right' width=50% >Tél. Prof. Père : </td><td>$tel4  <input $checked4 type=checkbox value='$idEleve#$tel4' name='tel$o' id='tel$o' /></td></tr>"; 
		}
		$tel5=preg_replace('/ /',"",$tel5);
		$tel5=preg_replace('/\./',"",$tel5);
		if (is_numeric($tel5)) { 
		       if ($choixtel == "cherchetelmere") { $checked5="checked='checked'"; } 
			$o++; 
			print "<tr><td align='right' width=50% >Tél. Prof. Mère : </td><td>$tel5  <input $checked5 type=checkbox value='$idEleve#$tel5' name='tel$o' id='tel$o' /></td></tr>"; 
		}

	print "</table>";
?>
</td>
	</tr>
	<?php
	}
	print "<tr><td colspan='2' bgcolor='#FFFFFF' align='center' ><br><br><input type='submit' name='envSmsClasse' value='Enregistrer' class='BUTTON' /><br><br>";
	print "<input type='hidden' name='nbtel' value='$o' /></td></tr>";
}

	print "</table>";

	print "</form>";
}
?>
<script>
var okcheck='0';
function tous() {
	var i='0';
	if (okcheck == '0') {
		okcheck='1';
		for(i=1;i<=<?php print $o ?>;i++) {
			document.getElementById('tel'+i).checked=true;
		}
	}else{
		okcheck='0';
		for(i=1;i<=<?php print $o ?>;i++) {
			document.getElementById('tel'+i).checked=false;
		}
	}
}
</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
