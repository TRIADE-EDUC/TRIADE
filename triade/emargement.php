<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
	setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
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
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_comptaSupp.js"></script>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGMESS303 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
include_once('librairie_php/db_triade.php');
validerequete("3");
$cnx=cnx();
$datap=config_param_visu("hauteuremarg");
$hauteur=$datap[0][0];
if ($hauteur != "") {
	$option="<option value='$hauteur' id='select0'>$hauteur</option>";
}else{
    $hauteur=6;
}

nettoyageEdt();

?>
<br />

<table border='0' align=center width="100%">
<tr><td align='center' colspan='2' >
<form method='post' >
<font class="T2"><?php print LANGBULL29 ?> :</font>
<select name='anneeScolaire' onChange="this.form.submit()"  >
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>
</form>
</td></tr>

<tr><td height='20' width='50%' ></td></tr>


<tr><td height='20' colspan='2' align='center' > <b><font class="T2 shadow"><?php print LANGMESS304 ?></font></b> </td></tr>
<tr><td height='20' width='50%' ></td></tr>
<tr>
<form action='emargementvierge.php?idclasse' method='post' name='form1' >
<input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur1'  />
<td align=right><font class="T2"><?php print LANGMESS305 ?> :</font></td>
<td align=left ><select id="saisie_classe" name="saisie_classe" onChange="this.form.submit();">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe2(20); // creation des options
?>
</select></td></tr>
</form>

<tr><td height='20'></td></tr>

<tr>
<form action='emargementviergeexamen.php' method='post' name='form2' >
<input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur2' />
<td align=right ><font class="T2"><?php print LANGMESS306 ?> :</font></td>
<td align=left ><select id="saisie_classe" name="saisie_classe" onChange="this.form.submit();">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_classe2(20); // creation des options
?>
</select></td></tr>
</form>
<tr><td height='20'></td></tr>
<tr><td height='20' colspan='2' align='center'> <b><font class="T2 shadow"><?php print LANGMESS307 ?></font></b> </td></tr>
<tr><td height='20'></td></tr>

<form action='emargementvierge.php?idgroupe' method='post' name='form1' >
<input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur3' />
<td align=right ><font class="T2"><?php print LANGMESS305 ?> :</font></td>
<td align=left ><select id="saisie_groupe" name="saisie_groupe" onChange="this.form.submit();">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_groupe_id(); // creation des options
?>
</select></td></tr>
</form>


<tr><td height='20'></td></tr>

<tr>
<form action='emargementviergeexamen.php' method='post' name='form2' >
<input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur4' />
<td align=right ><font class="T2"><?php print LANGMESS306 ?> :</font></td>
<td align=left ><select id="saisie_groupe" name="saisie_groupe" onChange="this.form.submit();">
<option id='select0' ><?php print LANGCHOIX?></option>
<?php
select_groupe_id(20); // creation des options
?>
</select></td></tr>
</form>
<tr><td height='20'></td></tr>
<tr><td height='20' colspan='2'><hr></td></tr>

<tr><td height='20'></td></tr>

<tr>
<form action='emargementdujour.php' method='post' name='form3' >
<input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur5'  />
<td align=right><font class="T2"><?php print LANGMESS314 ?> :</font></td>
<td align=left ><script language=JavaScript> buttonMagicSubmit3("<?php print LANGBT28?>","create","")</script></td></tr>
<input type="hidden" name="datedujour" value="<?php print date("d/m/Y") ?>" />
</form>

<tr><td height='20'></td></tr>

<form action='emargementdujour.php' method='post' name="formulaire" >
<input type='hidden' name='hauteur' value='<?php print $hauteur ?>' id='hauteur6'  />
<td align=right><font class="T2"><?php print LANGMESS315 ?></td><td>
<input type="text" name="datedujour" value="<?php print date("d/m/Y") ?>"  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;<?php
include_once("librairie_php/calendar.php");
calendarDim("id1","document.formulaire.datedujour",$_SESSION["langue"],"0","0");
?></td></tr>
<td align=right><font class="T2">au&nbsp;</td><td><input type="text" name="datedujourfin" value=""  onclick="this.value=''" size=12 class="bouton2" onKeyPress="onlyChar(event)" />&nbsp;<?php
include_once("librairie_php/calendar.php");
calendarDim("id2","document.formulaire.datedujourfin",$_SESSION["langue"],"0","0");
?>&nbsp;</font></td>
<tr><td align=right><font class="T2"><?php print LANGMESS316 ?> </font></td><td><select id="saisie_classe" name="saisie_classe" >
<option id='select0' value='tous' ><?php print LANGAFF5 ?></option>
<?php
select_classe2(20); // creation des options
?>
</select>
</td></tr>
<tr><td align=right ><font class="T2"><?php print LANGMESS317 ?>  </font></td><td><select id="saisie_prof" name="saisie_prof" >
<option id='select0' value='tous' ><?php print LANGMESS318 ?></option>
<?php select_personne_2('ENS','25'); ?>
</select>
</td></tr>
<tr><td></td><td align=right valign='bottom' ><br><script language=JavaScript> buttonMagicSubmit3("<?php print LANGBT28?>","create","")</script></td></tr>
</form>
<form name="form0" >
<td >
         <div align="right"><br><font class="T2"><?php print LANGMESS319 ?> : </font></div>
        </td>
	<td colspan="2" ><br>
	
	<select name="hauteur" size=1 onChange='affecttaille(this.value)' >
		<?php print $option ?>
		<option value="4" id='select1'>04</option>
		<option value="5" id='select1'>05</option>
		<option value="5.5" id='select1'>05.5</option>
		<option value="6" id='select1'>06</option>
		<option value="7" id='select1'>07</option>
		<option value="8" id='select1'>08</option>
		<option value="9" id='select1' >09</option>
		<option value="10" id='select1'>10</option>
		<option value="11" id='select1'>11</option>
		<option value="12" id='select1'>12</option>
		<option value="13" id='select1'>13</option>
		<option value="14" id='select1'>14</option>
		<option value="15" id='select1'>15</option>
	</select>
	 </td>
    </tr>
</form>


</table>

<script>
function affecttaille(val) {
	document.getElementById('hauteur1').value=val;
	document.getElementById('hauteur2').value=val;
	document.getElementById('hauteur3').value=val;
	document.getElementById('hauteur4').value=val;
	document.getElementById('hauteur5').value=val;
	document.getElementById('hauteur6').value=val;
}

</script>

<br /><br />
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
 <SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>  
   </BODY></HTML>
