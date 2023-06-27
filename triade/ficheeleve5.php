<?php
      session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
        <title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
        </head>
        <body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <?php include("./librairie_php/lib_licence.php"); ?>
	<?php
	// connexion (après include_once lib_licence.php obligatoirement)
	include_once("librairie_php/db_triade.php");
	validerequete("3");
	$cnx=cnx();
	?>
        <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
               <?php include("./librairie_php/lib_defilement.php"); ?>
             </TD><td width="472" valign="middle" rowspan="3" align="center">
             <div align='center'><?php top_h(); ?>
             <SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<?php
// affichage de l'élève (lecture seule)
$idEleve=$_GET["eid"];
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1'><?php print LANGPROF26 ?>  <font id="color2"><?php print recherche_eleve($idEleve);?></font></B></font></td></tr>
<tr id='cadreCentral0' ><td colspan=2><br>&nbsp;&nbsp;<input type=button class=BUTTON value="<-- <?php print LANGPRECE ?>" onclick="open('ficheeleve3.php?eid=<?php print $_GET["eid"]?>','_parent','')"><br><br>

<table bordercolor="#CCCC00"  width=95% align=center border=1 bgcolor="#FFFFFF" >

<?php
$data=profPinfoAff($idEleve);
// id,dateDebut,dateFin,idEleve,commentaire,nomProf
for($i=0;$i<count($data);$i++) {
?>
<tr><td ><br />&nbsp;&nbsp;
<?php print LANGPROF30?> <b><?php print dateForm($data[$i][1])?></b> au <b><?php print dateForm($data[$i][2])?></b>
<br><br>
&nbsp;&nbsp;<?php print $data[$i][4]?>
<br>
<div align=right><?php print LANGPROF31 ?>  : <?php print $data[$i][5]?> &nbsp;&nbsp;</div>
<br />
</td>
</tr>
<?php
}
?>

<?php
$choix_tri=recherche_trimestre_en_cours();
$data=recherche_intervalle_trimestre($choix_tri);
for($i=0;$i<count($data);$i++){
        $date_debut=$data[$i][0];
        $date_fin=$data[$i][1];
}
?>
</table>

<br><br><hr><br>
<table bgcolor="#FFFFFF" bordercolor="#000000" border=1 align=center>
<tr><td id="bordure"><br>
&nbsp;<font size=2><?php print LANGPROFP8 ?> : <?php $data=affRetard($idEleve); print count($data);  ?>&nbsp;&nbsp;</font><br>
<br>
<font size=2>&nbsp;<?php print LANGPROFP9 ?> : <?php $data=nombre_retard($idEleve,$date_debut,$date_fin);  print count($data);?>&nbsp;&nbsp;</font><br>
<br>
<font size=2>&nbsp;<?php print LANGPROFP10 ?> : <?php $data=affAbsence($idEleve); print count($data);  ?>&nbsp;&nbsp;</font><br>
<br>
<font size=2>&nbsp;<?php print LANGPROFP11 ?>: <?php $data=nombre_abs($idEleve,$date_debut,$date_fin);  print count($data);?>&nbsp;&nbsp;</font><br><br>
</td></tr></table>
</ul>
<br>

<ul>
&nbsp;<font size=2><u><?php print LANGPROJ9 ?></u></font><br>
<br><br>
<select size=5 style="FONT-SIZE: 12px;">
<?php
$data=affSanction_par_eleve($idEleve);
// id,id_eleve,motif,sanction,date_saisie,origin_saisie,signature_parent,attribuer_par
for($i=0;$i<count($data);$i++) {
        
?>
        <option><?php print dateForm($data[$i][4])?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                --
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php print  couperchaine(trim(rechercheCategory($data[$i][3])),13)?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                --
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php print  couperchaine(trim($data[$i][7]),19)?> </option>
                &nbsp;&nbsp;&nbsp;&nbsp;
<?php
}
?>
        <option>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </option>
</select>
<br><br>
&nbsp;<font size=2><u><?php print LANGDISC11?></u></font><br>
<br><br>
<select size=5 style="FONT-SIZE: 12px;">
<?php
$data=affRetenuTotal_par_eleve($idEleve);
//id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,sanction,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu
for($i=0;$i<count($data);$i++) {
        $data2=chercheSanction($data[$i][5]);
?>
        <option><?php print dateForm($data[$i][1])?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                --
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php print  couperchaine(trim($data2[0][1]),13)?>
                &nbsp;&nbsp;&nbsp;&nbsp;
                --
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php print  couperchaine(trim($data[$i][8]),19)?> </option>
                &nbsp;&nbsp;&nbsp;&nbsp;
<?php
}
?>
        <option>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </option>

</select>
<br><br>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire") ):
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
