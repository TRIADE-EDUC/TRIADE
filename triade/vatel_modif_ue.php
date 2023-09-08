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
<form  method="post" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Gestion des Unités d'enseignements</font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  -->
<BR>
<?php
include_once('librairie_php/db_triade.php');
include("librairie_php/fonctions_vatel.php"); 
$cnx=cnx();
validerequete("menuadmin");

if (isset($_POST["create"])){
	$cr1=vatel_modif_ue($_POST,$_POST['id_detail']);
	vatel_supp_ue($_POST['id_detail'],'ue_detail');
	for($i=0;$i<=$_POST["nb"];$i++) {
		$code_matiere=$_POST["code_matiere_$i"];
		if ($code_matiere > 0) { 
			$idprof=$_POST["idprof_$i"];
			vatel_create_due_bis($code_matiere,$_POST['id_detail'],$idprof);
		}
	}
 	
}

$data_ue=vatel_liste_ue($_GET['id']); // code_ue,code_classe,semestre,num_ue,nom_ue,coef_ue,ects,idpers_prof,nom_ue_en ,annee_scolaire  

?>
<table>
<td align='right'><font class="T2"><?php print LANGBULL3?> :</font> </td>
<td> 
        <select name='annee_scolaire' >
<?php
        filtreAnneeScolaireSelectNote($data_ue[0][9],3);
?>
	</select>
</td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Nom :</font> </td><td><input type=text name="nom_ue" size=40  maxlength='40' value="<?php print $data_ue[0][4]?>"></td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Ordre d'apparition :</font> </td><td> <input type=text name="num_ue" size='2' value="<?php print $data_ue[0][3]?>"> ( au sein du bulletin de zéro à n ) </td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Coef. :</font> </td><td> <input type=text name="coef_ue" size='2' value="<?php print $data_ue[0][5]?>" ></td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>ECTS :</font> </td><td> <input type=text name="ects_ue" size='2' value="<?php print $data_ue[0][6]?>" ></td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Classe :</font> </td><td> <select name='code_classe'>
<option value=''></option>
<?php
$data=affClasse();
for($i=0;$i<count($data);$i++)
        {
	print "<option STYLE='color:#000066;background-color:#CCCCFF' value='".$data[$i][0]."'";
	if ($data_ue[0][1]==$data[$i][0]) {
		print "selected";
	      	$nomclasse=$data[$i][1];	
	} 
	print " >".$data[$i][1]."</option>";
        }
?>
</select></td></tr>
<?php
if($cr1){
  alertJs(LANGDONENR." \\n\\nATTENTION !! METTRE A JOUR LES AFFECTATIONS DE LA CLASSE $nomclasse SUR LES DONNEES UNITE D'ENSEIGNEMENT !! ");
}
?>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Semestre :</font></td><td> 
	 <select name="semestre">
                     <option STYLE='color:#000066;background-color:#CCCCFF' value="0" <?php if ($data_ue[0][2]==0) { print 'selected';  } ?> >1 et 2</option>
                     <option STYLE='color:#000066;background-color:#CCCCFF' value="1" <?php if ($data_ue[0][2]==1) { print 'selected';  } ?> >1</option>
                     <option STYLE='color:#000066;background-color:#CCCCFF' value="2" <?php if ($data_ue[0][2]==2) { print 'selected';  } ?> >2</option>
     </select><br>
</td></tr>
<tr><td align='right' >&nbsp;&nbsp;<font class=T2>Professeur Principal :</font> </td><td> <select name='idpers_profp'>
<option  id='select0' value='0' ><?php print LANGCHOIX ?></option>
<?php
$idprofp=$data_ue[0][7];
if ($idprofp > 0) print "<option  id='select1' value='$idprofp' selected='selected'>".strtoupper(recherche_personne_nom($idprofp,'ENS'))." ".ucfirst(recherche_personne_prenom($idprofp,'ENS'))."</option>";
?>
<option  id='select0' value='0' ><?php print LANGCHOIX ?></option>
<?php

select_personne_2('ENS','40');
?>
</select>
</table>
<br><br>
<table border=1 style="border-collapse: collapse;" >
<tr>
<td bgcolor='yellow' ><font class=T2>Matière</font></td>
<td bgcolor='yellow' ><font class=T2>Enseignant</font></td>
</tr>
<?php
$data_detail=vatel_liste_uedetail($_GET['id']); //code_ue_detail,code_ue,code_matiere,code_enseignant
$data=affMatiere();

for($i=0;$i<count($data);$i++)  {
	$checked="";
	$id="";
	$code_enseignant="0";
	if ($data[$i][1] != "") {
		for($j=0;$j<count($data_detail);$j++)  {
			$code_enseignant=$data_detail[$j][3];
			if ($data_detail[$j][2]==$data[$i][0]) {
				$checked="checked='checked'"; 
				$id=$data_detail[$j][0];
				break;
			}else{
				$code_enseignant="0";
			}
		}
	}
	
	print "<tr>"; 
	print "<td><input type='checkbox' $checked name='code_matiere_$i' value='".$data[$i][0]."' > ".strtolower($data[$i][1]); 
	print "</td><td><select name='idprof_$i'>";
	if ($code_enseignant > 0) {
		print "<option  id='select1' value='$code_enseignant' >".ucfirst(recherche_personne_nom($code_enseignant,"ENS"))." ".ucfirst(recherche_personne_prenom($code_enseignant,"ENS"))."</option>";
	}else{
		print "<option  id='select0' value='0' >".LANGCHOIX."</option>;";
	}
	select_personne_2('ENS','20'); 
	print "</select></td></tr>";
}
Pgclose();
?><input type="hidden" value="<?php print $_GET['id']?>" name="id_detail"></td></tr>
<tr><td height='20' id='bordure' ></td></tr>
<tr><td colspan="3" id='bordure' >
<input type='hidden' name='nb' value='<?php print count($data) ?>' /> 
<script language=JavaScript>buttonMagicSubmit("<?php print "Valider modif."?>","create"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print "Lister / Modifier";?>","vatel_list_ue.php","_parent","","");</script>
<script language=JavaScript>buttonMagic("<?php print "Supprimer"; ?>","vatel_supp_ue.php?id=<?php print $_GET['id']?>","_parent","","");</script>&nbsp;&nbsp;
<br><br><br>
</td></tr></table>
<?php brmozilla($_SESSION["navigateur"]); ?>
<!-- // fin  -->
</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY></HTML>
