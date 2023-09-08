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
<script language="JavaScript" src="./librairie_js/lib_stage.js"></script>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGSTAGE44 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
validerequete("2");

if (isset($_POST["create"])) {

	if ($_POST["id"] != "") {
        		$cr=stage_modif($_POST["id"],$_POST["num"],$_POST["debutdate"],$_POST["findate"],$_POST["saisie_classe"],$_POST["nom_stage"],$_POST["jourstage"]);
			$cr=1;
			if($cr == 1){
                  	history_cmd($_SESSION["nom"],"MODIFICATION","date de stage");
        			print "<font color=red><br><br><center>".LANGSTAGE53.".";
				print "</center></font><br><br>";
			}else {
				print "<font color=red><br><br><center>";
				print LANGSTAGE52;
				print "</center></font><br><br>";
			}

	}else{

        		$cr=stage_ajout($_POST["num"],$_POST["debutdate"],$_POST["findate"],$_POST["saisie_classe"],$_POST["nom_stage"]);
			$cr=1;
			if($cr == 1){
                	history_cmd($_SESSION["nom"],"CREATION","date de stage");
        		print "<font color=red><br><br><center>".LANGSTAGE54." ".$_POST["debutdate"]." au ";
				print $_POST["findate"]." <br> ".LANGSTAGE55." ".chercheClasse_nom($_POST["saisie_classe"]) ;
				print LANGSTAGE56.".";
				print "</center></font><br><br>";
			}else {
				print "<font color=red><br><br><center>";
				print LANGSTAGE52;
				print "</center></font><br><br>";
			}
	}

}



$submitform="onsubmit='return validedatestage()'";

if (isset($_GET["id"])) {
	$data=recherchedatestage($_GET["id"]);
	// idclasse,datedebut,datefin,numstage,id,nom_stage,jourdesemaine
	for($i=0;$i<count($data);$i++) {
		$numstage=$data[$i][3];
		$datedebut=dateForm($data[$i][1]);
		$datefin=dateForm($data[$i][2]);
		$nomstage=$data[$i][5];
		$jourdesemaine=$data[$i][6];
		$idclasse="<option STYLE='color:#000066;background-color:#FCE4BA' value='".$data[$i][0]."'>".chercheClasse_nom($data[$i][0])."</option>";
		$id=$data[$i][4];
	}
	$submitform="onsubmit='return validedatestage2()'";
}


?>
<br>
<ul>
<font class=T2>
<form method=post <?php print $submitform?> name="formulaire">
<?php print LANGSTAGE48 ?> : <input type=text name="num" size=3 value='<?php print $numstage; ?>'><br><br>
<?php print "Nom de stage" ?> : <input type=text name="nom_stage" size=30 maxlength=50 value='<?php print $nomstage; ?>'><br><br>
<?php print LANGSTAGE45 ?> : <input type=text name="debutdate" size=12 value='<?php print $datedebut; ?>' class=bouton2>
<?php
 include_once("librairie_php/calendar.php");
 calendar("id1","document.formulaire.debutdate",$_SESSION["langue"],"0");
?>
<br><br>
<?php print LANGSTAGE46 ?> : <input type=text name="findate" size=12 value='<?php print $datefin; ?>' class=bouton2>
<?php
 include_once("librairie_php/calendar.php");
 calendar("id2","document.formulaire.findate",$_SESSION["langue"],"0");
?>

<br><br>
<?php print LANGELE4?> : <select name="saisie_classe">
<?php
if (!isset($_GET['id'])) { ?>
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX?></option>
<?php } ?>
<?php
print $idclasse;
select_classe(); // creation des options
?>
</select>
<br>
<br>
<font class='T2'>
<?php print "En Entreprise le : " ?> </font>
<?php
$liste=explode(',',$jourdesemaine);
foreach($liste as $key=>$value) {
	if ($value == '1') $checkL="checked='checked'";
	if ($value == '2') $checkMA="checked='checked'";
	if ($value == '3') $checkME="checked='checked'";
	if ($value == '4') $checkJ="checked='checked'";
	if ($value == '5') $checkV="checked='checked'";
	if ($value == '6') $checkS="checked='checked'";
	if ($value == '7') $checkD="checked='checked'";
}
?>
<input type=checkbox name="jourstage[]" value="1"  id="j1"   <?php print $checkL ?> /> L 
<input type=checkbox name="jourstage[]" value="2"  id="j2"   <?php print $checkMA ?> /> M 
<input type=checkbox name="jourstage[]" value="3"  id="j3"   <?php print $checkME ?> /> M 
<input type=checkbox name="jourstage[]" value="4"  id="j4"   <?php print $checkJ ?> /> J 
<input type=checkbox name="jourstage[]" value="5"  id="j5"   <?php print $checkV ?> /> V 
<input type=checkbox name="jourstage[]" value="6"  id="j6"   <?php print $checkS ?> /> S 
<input type=checkbox name="jourstage[]" value="7"  id="j7"   <?php print $checkD ?> /> D 

<br /><br /><br />

<input type=hidden name=id value='<?php print $id;?>'>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE47 ?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagicRetour('gestion_stage.php','_parent')</script>
<br><br>
</form>
</ul>

</font>
<!-- // fin  -->
</td></tr></table>


<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION['membre'] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;
// deconnexion en fin de fichier
	Pgclose();
?>
</BODY></HTML>
