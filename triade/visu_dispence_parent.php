<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
        $anneeScolaire=$_POST["anneeScolaire"];
}
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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();

$Seid=$_SESSION["id_pers"];
if ($_SESSION["membre"] == "menututeur") { $Seid=""; }
if (isset($_POST["idelevetuteur"])) {
	$Seid=$_POST["idelevetuteur"];
	$_SESSION["idelevetuteur"]=$Seid;
	$Scid=chercheClasseEleve($Seid);
	$_SESSION["idClasse"]=$Scid;
}
if (isset($_SESSION["idelevetuteur"])) {
	$Seid=$_SESSION["idelevetuteur"];	
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<form method="post" action="visu_dispence_parent.php" >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGPARENT9 ?></font></b><?php
if ($_SESSION["membre"] == "menututeur") {
?>
	&nbsp;&nbsp;
	<select name='idelevetuteur' onchange="this.form.submit()" >
	<?php 
	if ($Seid != "") {
		$nom=recherche_eleve_nom($Seid);
		$prenom=recherche_eleve_prenom($Seid);
	       	print "<option id='select1' value='$Seid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
	}else{
		print "<option id='select0' >".LANGCHOIX."</option>";
	}
	listEleveTuteur($_SESSION["id_pers"],30)
	?>
	</select>
<?php
}
?>
</td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<br />
<font class="T2"><?php print LANGBULL29 ?> :</font>
<select name='anneeScolaire' onchange="this.form.submit()" >
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select><br /><br />
</form>
<!-- // fin  -->
<table border="1" bordercolor="#000000" width="100%" style='border-collapse: collapse;' >
<TR>
<TD bgcolor='yellow' align=center width=10%><?php print LANGPARENT10 ?></td>
<TD bgcolor='yellow' align=center ><?php print LANGDISP2 ?> </td>
<TD bgcolor='yellow' align=center width=5%> <?php print LANGPARENT11 ?>  </td>
<TD bgcolor='yellow' align=center width=10%> <?php print LANGPARENT12 ?>  </td>
</TR>
<?php
$data_2=affDispence($_SESSION["id_pers"]);
// $data : tab bidim - soustab 3 champs
for($j=0;$j<count($data_2);$j++)
        {
        if ($data_2[$j][10] != " ") {
                $suite="<BR>".$data_2[$j][10];
                $suite2="<BR>".$data_2[$j][11];
        }
        if ($data_2[$j][12] != " ") {
                $suite3="<BR>".$data_2[$j][12];
                $suite4="<BR>".$data_2[$j][13];
        }


?>

        <TR  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<TD align=center valign=top><?php print dateForm($data_2[$j][2])?>
        <br />au<br /><?php print dateForm($data_2[$j][3])?>
        </td>
        <TD valign=top>
	<script language=JavaScript>
        <?php
                if ($data_2[$j][6] == "1" ) {
                        $booleen="Oui";
                }else {
                        $booleen="Non";
                }
        ?>
</script>
<?php print LANGPARENT13 ?> : <?php print $booleen?>  <BR>
<?php print LANGDISP2 ?> : <?php print $data_2[$j][7]?><br />
<?php print "Matière" ?> : <?php print chercheMatiereNom($data_2[$j][1]) ?></td>
        <TD valign=top>
<input type=text onfocus=this.blur() maxlength=5 value="<?php print trim($data_2[$j][8])?>" size=5 STYLE='color:#000066;background-color:#FCCCCC'>
<input type=text onfocus=this.blur() maxlength=5 value="<?php print trim($data_2[$j][10])?>" size=5 STYLE='color:#000066;background-color:#FCCCCC'>
<input type=text onfocus=this.blur() maxlength=5 value="<?php print trim($data_2[$j][12])?>" size=5 STYLE='color:#000066;background-color:#FCCCCC'>
</tr>
<TD valign=top>
<?php
$k=9;
for ($a=0;$a<3;$a++) {
?>
<input type=text value="<?php print trim($data_2[$j][$k])?>" size=12 onfocus="this.blur()" STYLE='color:#000066;background-color:#FCCCCC'>
<?php
 $k=$k+2;
 }
?>
</tr>
<?php } ?>
</table>
<br><br>
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
     Pgclose();
     ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
   </BODY></HTML>
