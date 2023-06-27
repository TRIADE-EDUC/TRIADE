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
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("3");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE86 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>

<?php
$prenom=recherche_eleve_prenom($_POST["ideleve"]);
$nom=recherche_eleve_nom($_POST["ideleve"]);



$ideleve=$_POST["ideleve"];
$idclasse=$_POST["saisie_classe"];
$id=$_POST["id"];


$idstage=$_POST["idstage"];
$ident=$_POST["ident"];
$lieu=$_POST["lieu"];
$ville=$_POST["ville"];
$postal=$_POST["postal"];
$responsable=$_POST["responsable"];
$idprof=$_POST["idprof"];
$date=$_POST["date"];
$loge=$_POST["loge"];
$nourri=$_POST["nourri"];
$xservice=$_POST["xservice"];
$raison=$_POST["raison"];
$info=$_POST["info"];
$tel=$_POST["tel"];
$idtuteur=$_POST["idtuteur"];
$horairedebutjournalier=$_POST["horairedebutjournalier"];
$horairefinjournalier=$_POST["horairefinjournalier"];
$date2=$_POST["date2"];
$idprof2=$_POST["idprof2"];
$pays=$_POST["pays"];
$service=$_POST["service"];
$indemnitestage=$_POST["indemnitestage"];


update_stage_eleve($id,$idstage,$ident,$lieu,$ville,$postal,$responsable,$idprof,$date,$loge,$nourri,$xservice,$raison,$info,$ideleve,$tel,$idtuteur,$horairedebutjournalier,$horairefinjournalier,$date2,$idprof2,$pays,$service,$indemnitestage);

?>
<font class=T2><center><?php print LANGSTAGE87 ?></center></font><br><br>
<table align=center>
<tr>
<td>
<script language=JavaScript>buttonMagic("<?php print LANGSTAGE73 ?>","gestion_stage_modif_eleve_2.php?id=<?php print $_POST[ideleve]?>","_parent","","") </script>
</td>
</td></tr></table>
<br><br>
</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
     	print "<SCRIPT type='text/javascript' ";
       	print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
       	print "</SCRIPT>";
}else{
       	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
      	print "</SCRIPT>";
      	top_d();
      	print "<SCRIPT type='text/javascript' ";
      	print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
	print "</SCRIPT>";
}
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
