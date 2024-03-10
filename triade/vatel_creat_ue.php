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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<script language="JavaScript" src="librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onLoad="Init();" >
<?php include("librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form  method="post" name="formulaire" onSubmit="return verifAnneeScolaire()" >
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des Unités d'enseignements</font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<?php
include_once('librairie_php/db_triade.php');
include("librairie_php/fonctions_vatel.php"); 
$cnx=cnx();
if(isset($_POST["create"])){        
	validerequete("menuadmin");
        $anneeScolaire=$_POST["annee_scolaire"];
        if ($anneeScolaire == "") {
                alertJs("Création impossible, année scolaire non indiquée.");
        }else{
                $cr=vatel_create($_POST,'ue');
                for($i=0;$i<=$_POST["nb"];$i++) { 
                       $code_matiere=$_POST["code_matiere_$i"];
                        if ($code_matiere > 0) {
                                $idprof=$_POST["idprof_$i"];
                                vatel_create_due_bis($code_matiere,$cr,$idprof);
                        }
                }
                if($cr) {
                        alertJs("Nouvelle unité d'enseignement créée.");
                }
        }

}
?>
<BR>
<table>
<td align='right'><font class="T2"><?php print LANGBULL3?> :</font> </td>
<td> 
<select name='annee_scolaire' >
<?php
$anneeScolaire=$_COOKIE["anneeScolaire"];
filtreAnneeScolaireSelectNote($anneeScolaire,3);
?>
</select>
</td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Nom :</font> </td><td><input type=text name="nom_ue" size=40  maxlength=40></td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Ordre d'apparition :</font> </td><td> <input type=text name="num_ue" size='2' value="<?php print $data_ue[0][3]?>"> ( au sein du bulletin de zéro à n ) </td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Coef. :</font> </td><td> <input type=text name="coef_ue" size='2' ></td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>ECTS :</font> </td><td> <input type=text name="ects_ue" size='2' ></td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Classe :</font> </td><td> <select name='code_classe'>
<?php
$data=affClasse();
for($i=0;$i<count($data);$i++){
	print "<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][0]."'>".strtoupper($data[$i][1])."</option>";
}
?>
</select></td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Semestre :</font></td><td> 
	<select name="semestre">
        <option STYLE='color:#000066;background-color:#CCCCFF' value="0">1 et 2</option>
        <option STYLE='color:#000066;background-color:#CCCCFF' value="1">1</option>
        <option STYLE='color:#000066;background-color:#CCCCFF' value="2">2</option>
    </Select>
</td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Professeur Principal :</font> </td><td> <select name='idpers_profp'>
<option  id='select0' value='0' ><?php print LANGCHOIX ?></option>
<?php
select_personne_2('ENS','40');
?>
</select>
</table>
<br>
<ul>
<table border='1' style="border-collapse: collapse;" width='90%'>
<tr>
<td width=5%></td>
<td bgcolor='yellow' ><font class=T2>&nbsp;Matière&nbsp;</font></td>
<td bgcolor='yellow' width='1%'><font class=T2>&nbsp;Enseignant&nbsp;</font></td>
</tr>
<?php
$cnx=cnx();
$data=affMatiere();
for($i=0;$i<count($data);$i++)  {
	if ($data[$i][1] != "") {
		print "<tr  class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<td><input type='checkbox' name='code_matiere_$i' value='".$data[$i][0]."'></td>"; 
	 	print "<td>&nbsp;".$data[$i][1]." ".preg_replace('/^0$/',"",$data[$i][2]);
	 	print "</td><td><select name='idprof_$i'>
			<option  id='select0' value='0' >".LANGCHOIX."</option>";
	       		select_personne_2('ENS','30'); 
	 	print "</select></td></tr>";
	}
}
Pgclose();
?>
</table>
<br><br>
<table>
<tr><td colspan="4" align="center" >
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT14?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print "Lister / Modifier"?>","vatel_list_ue.php","_parent","","");</script>&nbsp;&nbsp;
<input type='hidden' name='nb' value='<?php print count($data) ?>' /> 
</form>
<br><br><br>
</td></tr></table>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</ul>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
   </BODY></HTML>
