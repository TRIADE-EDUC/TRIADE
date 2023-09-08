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
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
if ($_SESSION["membre"] == "menupersonnel") {
        if (!verifDroit($_SESSION["id_pers"],"edt")) {
                accesNonReserveFen();
                exit;
        }
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGTMESS490 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<br />
<ul>
<!-- // fin  -->

<?php
if (isset($_POST["duplique"])) {
	duplicateEDT($_POST["idclasseSource"],$_POST["idclasseDestination"],$_POST["saisie_date_debut"],$_POST["saisie_date_fin"]);
	print "<center><font class='T2' id='color3' ><b>".LANGDONENR."</b></font></center><br>";	
}
?>
<form method="post" action="edt_duplicate.php" name="formulaire" >
<font class='T2'>
<?php
$data=affClasseSansOffline(); //code_class,libelle,desclong,offline,idsite
?>
Indiquer la classe source : <select name='idclasseSource' >
			    <option id='select0' ><?php print LANGCHOIX ?></option>
			    <?php 
			    for($i=0;$i<count($data);$i++) {
				print "<option id='select1' value='".$data[$i][0]."' >".$data[$i][1]."</option>";
			    }
			    ?>
			    </select>
<br /><br />
Indiquer la classe de destination : <select name='idclasseDestination' >
			    <option id='select0' ><?php print LANGCHOIX ?></option>
			    <?php 
			    for($i=0;$i<count($data);$i++) {
				print "<option id='select1' value='".$data[$i][0]."' >".$data[$i][1]."</option>";
			    }
			    ?>
			    </select>
<br /><br />
<?php print LANGTMESS491 ?> <?php print LANGMESS109 ?> <input type='text' name='saisie_date_debut' size=12 class="bouton2" onKeyPress="onlyChar(event)" /> 
<?php
include_once("librairie_php/calendar.php");
calendar("idZ1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0");
?>
<?php print LANGMESS110 ?> <input type='text'  onclick="this.value=''" name="saisie_date_fin"  size=12 class="bouton2" onKeyPress="onlyChar(event)"  />
<?php
calendar("idZ2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0");
?>



<br /><br />
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","duplique");</script>
<script language=JavaScript>buttonMagicRetour("edt.php",'_self');</script>
</font>
</ul>
</form>
<br /><br />
<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php PgClose();  ?>
</BODY></HTML>
