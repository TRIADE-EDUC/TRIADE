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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuscolaire") {
	if (MODULEPREINSCRIPTIONVIESCOLAIRE != "oui") validerequete("menuadmin");
}else{
	validerequete("menuadmin");
}
$cnx=cnx();

if (isset($_POST["suppfiche"])) {
	suppfichepreinscription($_POST["id_eleve"]);
	alertJs("Fiche de pré-inscription supprimée.");
}

if (isset($_POST["inscription"])) {
	$tab=$_POST["listing"];
	foreach($tab as $key=>$value){
		transferePreinscription($value);
	}

}


if (isset($_POST["deletefiche"])) {
	$tab=$_POST["listing"];
	foreach($tab as $key=>$value){
		suppfichepreinscription($value);
	}
}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font  id='menumodule1' ><?php print LANGMESS335 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
<br>
<form method="post" action="preinscription_direction.php" >
<table><tr><td><font class='T2'>&nbsp;&nbsp;<?php print LANGTMESS454 ?> </td><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGTMESS455 ?>","rien"); //text,nomInput</script></td></tr></table><br />
</form>

<form method="post" action="listepreinscription.php" >
<table><tr>
<td><font class='T2'>&nbsp;&nbsp;<?php print LANGMESS347 ?> </td>
	<td><font class='T2'>
		<select id="saisie_classe" name="saisie_classe">
		<?php 
		if (isset($_POST["saisie_classe"])) {
			if ($_POST["saisie_classe"] > 0) {
				print "<option id='select1' value='".$_POST["saisie_classe"]."' >".trunchaine(chercheClasse_nom($_POST["saisie_classe"]),15)."</option>";
			}else{
				print "<option id='select1' value='Tous' >Tous</option>";
			}
		}
		?>
                <option id='select0' ><?php print LANGCHOIX ?></option>
                <option id='select0' value='Tous' ><?php print LANGTOUS ?></option>
		<?php
		select_classe2(15); // creation des options
		?>
		</select>
		<select id="filtre" name="saisie_filtre">
		<?php 
		if (isset($_POST["saisie_classe"])) {
			print "<option id='select1' value='".$_POST["saisie_filtre"]."' >".$_POST["saisie_filtre"]."</option>";
		}
		?>
                <option id='select0' value="Tous" ><?php print LANGTOUS ?></option>
                <option id='select1' value="En attente"><?php print LANGTMESS456 ?></option>
                <option id='select1' value="Accepté"><?php print LANGTMESS457 ?></option>
                <option id='select1' value="Réfusé"><?php print LANGTMESS458 ?></option>
		</select>
	</td>
	<td>année : <input type=text size=4 maxlength=4 name='annee_scolaire' value="<?php print $_POST["annee_scolaire"] ?>" /></td>
	<td><script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","rien"); //text,nomInput</script></td></tr>
</table>
<br />
</form>

<form method="post" name="form2">
<table width='100%' border='1' bordercolor='#000000' >
<tr>
<td bgcolor="yellow" id='bordure' width='5%'><font class='T2'>&nbsp;<input type='checkbox' onclick="validecase();" name="tous" value="1" />&nbsp;</font></td>
<td bgcolor="yellow" id='bordure' width='5%'><font class='T2'>&nbsp;<?php print LANGAGENDA104?>&nbsp;</font></td>
<td bgcolor="yellow" id='bordure'><font class='T2'>&nbsp;<?php print LANGTMESS459 ?>&nbsp;</font></td>
<td bgcolor="yellow" id='bordure'><font class='T2'>&nbsp;<?php print LANGNA1?>&nbsp;<?php print LANGELE3 ?>&nbsp;</font></td>
<td bgcolor="yellow" id='bordure'><font class='T2'>&nbsp;<?php print LANGELE4?>&nbsp;</font></td>
</tr>
</form>
<form method="post" name="form1" >
<?php 
$data=listingPreinscription($_POST["saisie_classe"],$_POST["saisie_filtre"],$_POST["annee_scolaire"]); //nom,prenom,classe,decision,date_demande,id,annee_scolaire
$nbliste=count($data);
for($i=0;$i<count($data);$i++) {

?>
<tr id="tr<?php print $i ?>" class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'" >
<td id='bordure'><font class='T1'>&nbsp;<input type='checkbox' name='listing[]' value='<?php print $data[$i][5] ?>' onClick="DisplayLigne('tr<?php print $i?>')" /></font></td>
<td id='bordure'><font class='T1'>&nbsp;<?php print $data[$i][6] ?>&nbsp;-&nbsp;<?php print $data[$i][6]+1 ?>&nbsp;</font></td>
<td id='bordure'><font class='T1'>&nbsp;<?php if ($data[$i][3] == "Accepté") print "<font color='green'>"; if ($data[$i][3] == "Refusé") print "<font color='red'>"; print preg_replace('/ /','&nbsp;',$data[$i][3]); if ($data[$i][3] == "accepté") print "</font>"; if ($data[$i][3] == "Refusé") print "</font>"; ?></a>&nbsp;</font>&nbsp;<a href="preinscriptiondetail.php?ideleve=<?php print $data[$i][5] ?>"><img src="image/commun/b_edit.png" border='0' align='center' /></td>
<td id='bordure'><font class='T1'>&nbsp;<?php print trunchaine(strtoupper($data[$i][0])." ".ucwords($data[$i][1]),30) ?>&nbsp;</font></td>
<td id='bordure'><font class='T1'>&nbsp;<a href='#' title="<?php print chercheClasse_nom($data[$i][2]) ?>"><?php print trunchaine(chercheClasse_nom($data[$i][2]),10) ?></a>&nbsp;</font></td>
</tr>
<?php
}
?>
</table>

<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGTMESS460 ?>","inscription"); //text,nomInput</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGTMESS461 ?>","deletefiche"); //text,nomInput</script>
</form><br>
<br></td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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

// deconnexion en fin de fichier
Pgclose();
?>
<script>
function validecase() {
	var nb='<?php print $nbliste ?>';
	var j=0;
	if (document.form2.tous.checked == true) {
		for(i=0;i<nb;i++) {
			document.form1.elements[j].checked=true;
			DisplayLigne('tr'+i);
			j=j+1;
		}
	
	}else{
		for(i=0;i<nb;i++) {
			document.form1.elements[j].checked=false;
			DisplayLigne('tr'+i);
			j=j+1;
		}
	}
}
</script>
</BODY></HTML>
