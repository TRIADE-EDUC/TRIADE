<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>

<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();

$taille=2000000;
$taille2="2Mo";

include_once("librairie_php/lib_get_init.php");
include_once("common/config6.inc.php");

if (MAXUPLOAD == "oui") {
	$id=php_ini_get("safe_mode");
	if ($id != 1) {
		set_time_limit(600); // en secondes
		$taille=8000000;
		$taille2="8Mo";
	}
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET63 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<?php 
if (isset($_POST["create"])) {
	@unlink("data/fichier_ASCII/carnetsuivi.xls");
	$fichier=$_FILES['fichier']['name'];
	$type=$_FILES['fichier']['type'];
	$tmp_name=$_FILES['fichier']['tmp_name'];
	$size=$_FILES['fichier']['size'];
	$idcarnet=$_POST['saisie_carnet'];
	if (!is_numeric($idcarnet)) exit; 

	if ($_POST['supp'] == '1') {
		suppCompetence($idcarnet);
		suppDescriptif($idcarnet);
	}

	if ((!empty($fichier)) &&  ($size <= $taille) &&  (preg_match('/excel/',$type))) {
		move_uploaded_file($tmp_name,"data/fichier_ASCII/carnetsuivi.xls"); 
		
		include_once('./librairie_php/reader.php');
                $data = new Spreadsheet_Excel_Reader();
//              $data->setOutputEncoding('CP1251');
                $data->setOutputEncoding('UTF-8');
                $data->read("data/fichier_ASCII/carnetsuivi.xls");
		$nb=0;
                for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                        $matiere=trim($data->sheets[0]['cells'][$i][1]);
                        $domaine=trim($data->sheets[0]['cells'][$i][2]);
                        $competence=trim($data->sheets[0]['cells'][$i][3]);
//			print "$idcarnet $matiere $domaine $competence <br>";
			$nb++;
		
			if (trim($matiere) != "") $idcompetence=enr_competence($idcarnet,$matiere);
		        enr_descriptif($idcarnet,$idcompetence,'1',$domaine);
		        enr_descriptif($idcarnet,$idcompetence,'0',$competence);

		}
		print "<br><ul>Il y a <b>$nb</b> compétence(s) d'enregistrée(s)</ul><br>";  	
		print "<UL><script language=JavaScript>buttonMagicRetour2('carnet_admin.php','_parent','".LANGCIRCU14."');</script><br><br>";
	}
	Pgclose();
}else{
?>
<br />
<form method="post" name="formulaire" ENCTYPE="multipart/form-data" >
<ul>
<table>
<tr><td align='right'><font class="T2"> <?php print LANGCARNET64 ?> :</font></td>
<td><input type=file name="fichier"  > <A href='#' onMouseOver="AffBulle3('Information','./image/commun/info.jpg','<font face=Verdana size=1><B><font color=red><?php print LANGEDT1?></font></B><?php print "ichier au format xls, <b>$taille2</b> . </font>" ?> '); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A> </td>
</tr>
<tr><td><font class="T2"> <?php print "Supprimer les données du carnet " ?> :</font></td>
<td><input type=checkbox name="supp" value='1' /> <font color='red'>oui je supprime !</font> </td>
</tr>
</table>	
</ul>
<br>
<UL><UL><UL><script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGAGENDA86 ?>","create","");</script></UL></UL></UL><br><br>
<?php brmozilla($_SESSION["navigateur"]); ?>
<input type='hidden' value="<?php print $_POST['saisie_carnet'] ?>" name="saisie_carnet" />
</form>
<?php
}
?>
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") {
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       }else{
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       }
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
