<?php
session_start();
$anneeScolaire=$_COOKIE["anneescolairefiltre"];
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
<HEAD
<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
<META http-equiv="pragma" content = "no-cache">
<meta http-equiv="Cache" content="no store" />
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<?php include("./librairie_php/googleanalyse.php"); ?>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS74 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
?>
<br />
<table border=0 align=center width="100%">
<tr>
<form action='compta_consulte.php' method='post'>
<td align=right><font class="T2"><?php print LANGMESS71 ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='compta_fiche.php' method='post'>
<td align=right><font class="T2"><?php print LANGMESS72 ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='compta_consulte_retard.php' method='post'>
<td align=right><font class="T2"><?php print LANGMESS73 ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='compta_export.php' method='post'>
<td align=right><font class="T2"><?php print "Exportation (format xls)" ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>
<tr><td height='20'></td></tr>
<tr>
<form action='compta_listing.php' method='post'>
<td align=right><font class="T2"><?php print "Listing des paiements par classe (format xls)" ?> :</font></td>
<td align=left ><script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1 ?>","rien"); //text,nomInput</script></td>
</tr>
</form>

<tr><td></td></tr>
<tr><td></td></tr>
</table>
</td></tr></table>

<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Bilan financier" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign='top'>
<?php 
$visu=0;
if (PASSMODULEBILANFINANCIER != "oui") { $visu=1; }
if (isset($_SESSION["adminplus"])) { $visu=1; }
if ($visu == 0) {
	if ($_GET["saisie_resultat"] == "erreur" ) {  $message_erreur="<font size=3 color=RED>Erreur de connexion</font><BR><br>"; }
?>
<form method=post action='./base_de_donne_central.php' >
<TABLE border=0 bordercolor="#000000" width=100% height=200>
<TR>
<TD align=center bordercolor="#FFFFFF" id='bordure' >
<?php print "$message_erreur" ?>
<font class="T2">
<?php print LANGPER12?>
</font>
<CENTER><br>
<input type=password name='saisie_code1'  size=10> ----
<input type=password name='saisie_code2'  size=10> ----
<input type=password name='saisie_code3'  size=10>
</CENTER><BR><BR>
<input type=hidden name='base' value="bilanfinancier">
<table align=center>
<tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPER13?>","rien"); //text,nomInput</script>
</td></tr></table>
<?php 
}else{ 
	if (isset($_POST["anneescolairefiltre"])) {
		$anneescolairefiltre=$_POST["anneescolairefiltre"];
	}
?>
<br>
<form method='post' action="comptaetat.php" >
&nbsp;&nbsp;<font class=T2>Filtre : </font><select onChange='this.form.submit()' name='anneescolairefiltre' > 
	<option value=''><?php print LANGCHOIX ?></option> <?php filtreAnneeScolaireSelect($anneescolairefiltre) ?> </select><br><br>
</form>
<table style='border-collapse: collapse;' width="100%" border="1" align="center" >
<tr>
<td bgcolor='yellow' ><font class="T2"><?php print "Classe" ?></font></td>
<td bgcolor='yellow' width='5'><font class="T2">&nbsp;Total&nbsp;</font></td>
<td bgcolor='yellow' width='5'><font class="T2">&nbsp;Reçu&nbsp;</font></td>
<td bgcolor='yellow' width='5'><font class="T2">&nbsp;Moy.&nbsp;%&nbsp;</font></td>
</tr>
<?php
$data=visu_affectation($anneescolairefiltre); //code_classe,f.libelle
for($i=0;$i<count($data);$i++) {
	$idclasse=$data[$i][0];
	print "<tr>";
	print "<td bgcolor='#FFFFFF' >".$data[$i][1]."</td>";
	$dataV=recupConfigVersement($idclasse,$anneescolairefiltre); //id,idclasse,libellevers,montantvers,datevers
	if ($dataV == "") { $dataV=array(); }
	$sql="SELECT elev_id FROM ${prefixe}eleves WHERE classe='$idclasse'";
	$res=execSql($sql);
        $dataEl=chargeMat($res);
	for($h=0;$h<count($dataEl);$h++) {
		$ideleve=$dataEl[$h][0];
		$dataVE=recupConfigVersementEleve($ideleve,$anneescolairefiltre);
		if ($dataVE == "") { $dataVE=array(); }
		$dataVI=array_merge($dataV,$dataVE);
		for($j=0;$j<count($dataVI);$j++) {
			$id=$dataVI[$j][0];

			if (verifcomptaExclu($id,$ideleve)) continue; 

			$dataO=recupInfoVersement($ideleve,$id); // ideleve,idversement,montantvers,datevers,modepaiement
			$montantVers+=$dataO[0][2];
			$montantavers+=$dataVI[$j][3];
		}
		//unset($dataV);
		unset($dataVE);
	}
	$pourcentage=($montantVers/$montantavers)*100;

	print "<td align='right' bgcolor='#FFFFFF'  >&nbsp;&nbsp;".number_format($montantavers,2,'.','')."&nbsp;".unitemonnaie()."&nbsp;&nbsp;</td>";
	print "<td align='right'  bgcolor='#FFFFFF' >&nbsp;&nbsp;".number_format($montantVers,2,'.','')."&nbsp;".unitemonnaie()."&nbsp;&nbsp;</td>";
	print "<td align='right'  bgcolor='#FFFFFF' >&nbsp;&nbsp;".number_format($pourcentage,2,'.','')."%&nbsp;&nbsp;</td>";
	print "</tr>";
	
	$sommeVers+=$montantVers;
	$sommeAVers+=$montantavers;
	$sommepourcentage+=number_format($pourcentage,2,'.','');
	$nbpourcentage++;

	unset($dataV);
	unset($dataVE);
	unset($montantVers);
	unset($montantavers);
}
	print "<tr>";
	print "<td align='right' bgcolor='#FFFFFF'><b>Total</b></td>";
	$val=number_format($sommeAVers,'2',',',' ');
	print "<td align='right'  bgcolor='#FFFFFF'><b>&nbsp;".$val." ".unitemonnaie()."</b> </td>";
	$val=number_format($sommeVers,'2',',',' ');
	print "<td align='right'  bgcolor='#FFFFFF'><b>&nbsp;".$val." ".unitemonnaie()."</b> </td>";
	if ($nbpourcentage != 0) { $sommepourcentage=$sommepourcentage/$nbpourcentage; }
	$val=number_format($sommepourcentage,2,'.','');
	print "<td align='right'  bgcolor='#FFFFFF'><b>&nbsp;".$val." %</b> </td>";

} 

?>
</tr></table>
<br><br>

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
    PgClose();
     ?>
   </BODY></HTML>
