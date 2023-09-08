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
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
// connexion P
$cnx=cnx();

if(isset($_POST["creat_sanction"])):
        $cr=create_sanction($_POST["saisie_intitule"],$_POST["saisie_category"]);
        if($cr == 1){
  //              alertJs(LANGSANC1);
        }
        else {
                  error(0);
        }
endif;



if (isset($_POST["creat_category"])) {
		$cr=create_category($_POST["saisie_intituleg"]);
		        if($cr == 1){
		  //              alertJs(LANGSANC1);
		        }
		        else {
		                  error(0);
        }
}

if(isset($_POST["creat_supp_category"])):
		$cr3=verif_si_affecte($_POST["saisie_int_supp"]);
		if ($cr3 == 0) {
        	$cr2=supp_category($_POST["saisie_int_supp"]);
       	 	if($cr2 == 1){
      	  	    // alertJs("Sanction supprimée -- Service Triade");
     	  	 }else {
                error(0);
        	}
        }else{
          	alertJs(LANGSANC2);
        }
endif;


if(isset($_POST["creat_supp_nb_sanction"])):
        $cr2=supp_config_nb_sanction($_POST[saisie_supp_nb]);
        if($cr2 == 1){
        //        alertJs("Sanction supprimée -- Service Triade");
        }
        else {
                error(0);
        }
endif;

if(isset($_POST["creat_config_retenue"])):
        $cr2=creat_config_retenue($_POST[saisie_sanction_2],$_POST[saisie_nb],$_SESSION[nom],date("Y-m-d"));
        if($cr2 == 1){
          //     alertJs("Donnée Enregistrée -- Service Triade");
        }
        else {
                error(0);
        }
endif;


if(isset($_POST["creat_supp"])):
        $cr2=supp_sanction_2($_POST["saisie_int_supp"]);
		 if($cr2 == 1){
		        //        alertJs("Sanction supprimée -- Service Triade");
		        }
		        else {
		                error(0);
        }
endif;



?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSANC3 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<BR>
     <blockquote>
<B><font class="T2"><?php print LANGSANC4 ?></font></B>
<form method=post >
<table border=0><tr><td>
<font class="T2"><?php print LANGSANC5 ?> :</font>
<input type=text size=30 maxlength=30 name="saisie_intituleg">
</td><td width=50%>
<table align=center><tr><td><script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","creat_category"); //text,nomInput</script>&nbsp;&nbsp;</td></tr></table>
</td></tr></table>
</form>
<BR>
<form method=POST>
<font class="T2"><?php print LANGSANC5 ?> :</font>
<select name="saisie_int_supp">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_category();
?>
</select> <input type=submit name="creat_supp_category" value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br><br>
</form>
</blockquote>
</table>
<BR><BR>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSANC3 ?></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<BR>
     <blockquote>
<B><font class="T2"><?php print LANGSANC6 ?></font></B>
<form method=post onsubmit="return valide_sanction();" name="formulaire">
<table border=0><tr><td colspan='2'>
<font class="T2"><?php print LANGSANC5 ?> :</font>
<select name="saisie_category">
<option value='-1' STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_category();
?>
</td><td>
<tr><td>
<font class="T2"><?php print LANGSANC7 ?> :</font>
<input type=text size=30 maxlength=30 name="saisie_intitule">
</td><td width=50%>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","creat_sanction"); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
</td></tr></table>
</form>
<BR>
<form method=POST>
<font class="T2"><?php print LANGSANC7 ?> :</font>
<select name="saisie_int_supp">
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_sanction2();
?>
</select> <input type=submit name="creat_supp" value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br><br>
</form>
     </blockquote>
</table>
<BR><BR>


<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGSANC8 ?></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // fin  -->
<BR>
<UL>
<B><font class="T2"><?php print LANGSANC9 ?></font></B>
<form method=post mane=formulaire >
<table width=70% border=0 >
<TR>
<TD>
<font class="T2"><?php print LANGSANC10 ?> :</font>
<select name=saisie_sanction_2>
<option STYLE='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
<?php
select_category();
?>
</select>
</TD></TR>
<TR>
<td>
<?php print LANGSANC11 ?> :
<select name=saisie_nb>
<option value=2 STYLE='color:#000066;background-color:#FCE4BA'>2</option>
<option value=3 STYLE='color:#000066;background-color:#FCE4BA'>3</option>
<option value=4 STYLE='color:#000066;background-color:#FCE4BA'>4</option>
</select> fois
</td>
</tr>
</table>
</UL>
<BR>
<table border=0 align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","creat_config_retenue"); //text,nomInput</script>
</td></tr></table>
</form>
<BR>
<UL><B> Déjà attribué: <B></UL>
<table width=100% border=1 bordercolor="#000000">
<TR>
<td bgcolor="yellow">&nbsp;<?php print LANGDISC20 ?> </TD>
<td width=25% bgcolor="yellow">&nbsp;<?php print LANGSANC12 ?> </TD>
<td bgcolor="yellow">&nbsp;<?php print LANGSANC13 ?> </TD>
<td width=15% bgcolor="yellow" align="center" > <?php print "&nbsp;Saisie&nbsp;le&nbsp;" ?> </TD>
<td width=5% bgcolor="yellow">&nbsp;<?php print LANGBT50?> </TD>
</TR>
<?php
$data=affSanction_nb_retenue();
// $data : tab bidim - soustab 3 champs
for($j=0;$j<count($data);$j++) {
	if (count($data) >= 1) {

?>
<form method=post>
<TR  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
<TD>
&nbsp;<?php
$nom_sanction=chercheCategory($data[$j][0]);
print $nom_sanction[0][1];
?>
</td><td>
&nbsp;<?php print $data[$j][1]?>
</td><td>
&nbsp;<?php print $data[$j][2]?>
</td><td align=center>
&nbsp;<?php print dateForm($data[$j][3])?>&nbsp;
</TD>
<TD>
<input type=submit name="creat_supp_nb_sanction" value="<?php print LANGBT50?>" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"><br><br>
<input type=hidden name=saisie_supp_nb value="<?php print $data[$j][0]?>">
</td>
</tr>
</form>
<?php
	}

}
?>
</table>
<BR>
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
</BODY></HTML>
