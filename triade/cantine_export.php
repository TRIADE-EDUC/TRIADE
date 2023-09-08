<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
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
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des donn&eacute;es la cantine" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
$cnx=cnx();
if ( (verifDroit($_SESSION["id_pers"],"cantine")) || ($_SESSION["membre"] == "menuadmin" )) { 
?>

<!-- // fin  -->
<br><br>
<?php
	
require_once "./librairie_php/xlsxwriter.class.php";
	
if (!is_dir("./data/cantine/")) {
	mkdir("./data/cantine/");
	htaccess("./data/cantine/");
}

$fichier="./data/cantine/export_cantine_".$_SESSION["id_pers"].".xlsx";
@unlink($fichier);
	
$writer = new XLSXWriter();
$writer->writeSheetHeader('Compta', array('NOM'=>'string','PRENOM'=>'string','CLASSE'=>'string','CREDIT'=>'string') );
$data=recupCreditCantine(); // idpers, prix
for($i=0;$i<count($data);$i++)	{
	$idpers=$data[$i][0];
	$membre=$data[$i][2];
	$credit=recupSumCreditCantine($idpers,$membre);
	$credit=number_format($credit,'2',',','');
	if ($membre == "menueleve") {
		$nom=recherche_eleve_nom($idpers,$membre);                
		$prenom=recherche_eleve_prenom($idpers,$membre);
		$idclasse=chercheIdClasseDunEleve($idpers);	
		$classe=chercheClasse_nom($idclasse);
	}else{
		$nom=recherche_personne_nom($idpers,$membre);
                $prenom=recherche_personne_prenom($idpers,$membre);
		$classe="Staff";
	}
	$writer->writeSheetRow('Compta', array($nom, $prenom, $classe, $credit) );
}
$writer->writeToFile("$fichier");
//echo '#'.floor((memory_get_peak_usage())/1024/1024)."MB"."\n";

?>
</font>
</form>
<center>
<table><tr><td><input type=button onclick="open('visu_document.php?fichier=<?php print $fichier?>','_blank','');" value="<?php print "Récupération de l'exportation" ?>"  class="bouton2"></td>
<td><script language=JavaScript>buttonMagicRetour('cantine.php','_self')</script></td></tr></table>
<br /></center>
<br><br>



<?php }else{ ?>
<br><font class="T2" id="color3"><center><img src="image/commun/img_ssl.gif" align='center' /> Accès réservé</center></font>
<?php } ?>

<br>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
