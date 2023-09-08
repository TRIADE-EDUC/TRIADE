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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
<script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// affichage de la classe
$saisie_classe=$_GET["sClasseGrp"];
verif_profp_class($_SESSION["id_pers"],$saisie_classe);
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];

?>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="85" style="padding-left:20px" >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROFP1?></b></font> <font color=red><?php print $cl?></font> </td></tr>
<tr id='cadreCentral0'>
<td align='center' >

<?php
// id,idclasse,commentaire,date_saisie
if (isset($_POST["create"])) {
	news_prof_p($_POST["resultat"],$_GET["sClasseGrp"],$_SESSION["id_pers"]);
}
?>
<table align=center width='100%' >
<tr><td><br />
<ul><font class=T2><?php print LANGTPROBL8 ?> </font></ul>
<form method=post>

<textarea id="editor" name="resultat" cols='200' ></textarea>
<script type="text/javascript">
var colorGRAPH='<?php print GRAPH ?>';
//<![CDATA[
CKEDITOR.replace( 'editor', { width:'100%' , height: '320px' , language:'<?php print ($_SESSION["langue"] == "fr") ? "fr" : "en";  ?>' } );
//]]>
</script>
<br>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPROFP2?>","create"); //text,nomInput</script>
<?php if (isset($_SESSION["profpclasse"])) { print "<script>buttonMagicRetour('profp2.php','_self')</script>"; } ?>
<br><br>
</form>
</td></tr>
</table>
<br><br>
<?php
if (isset($_GET["suppmess"])) {
	delete_news_prof_p($_GET["suppmess"]);
}

// id,idclasse,commentaire,date_saisie
$data=aff_news_prof_p($_GET["sClasseGrp"],$_SESSION["id_pers"]);

?>

<table align='center' border='1' bordercolor="#CCCCCC" width='100%' style='border-collapse: collapse;' >
<tr><td  bgcolor="#FFFFFF" bordercolor="#000000">
<table align='center' border='0' width='97%' >
<?php
for($i=0;$i<count($data);$i++) {
	$message=$data[$i][2];
	$message=preg_replace('/<p>\&nbsp;<\/p>/','',$message);
	$message=preg_replace('#(\\\\r|\\\\r\\\\n|\\\\n)#', ' ',$message);
	$message=stripslashes($message);
	$nomprofp2=recherche_personne($data[$i][4]);
	if ($nomprofp2 == $nomprofp) { 
		$nomprofp2="";
	}
?>
<tr><td>
&nbsp;<?php print $message?>
<br>
<div align=right> <?php print $nomprofp2 ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?php print dateForm($data[$i][3])?>&nbsp;&nbsp;[<a href="profpmessage.php?suppmess=<?php print $data[$i][0]?>&sClasseGrp=<?php print $saisie_classe?>"><?php print LANGBT50?></a>]&nbsp;&nbsp;</div>
</td>
</tr>
<tr><td><hr width=97%></td></tr>
<?php
}
?>
</table>
</td></tr>
</table>
</td>
</tr>
</table>


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
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
