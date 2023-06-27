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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content ="no-cache">
<META http-equiv="pragma" content ="no-cache">
<META http-equiv="expires" content ="-1">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_affectation.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>
Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"]?>
</title>
</head>
<body id='bodyfond2' onScroll="openPopup()" >
<?php
include_once("librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();


//variables utiles
// code_class(classes) de la classe concernée par l'affectation
$cid=$_GET["saisie_classe_envoi"];
$anneeScolaire=$_GET["anneeScolaire"];
// tableau 2 valeurs : id,libelle pour classe
$dataClasse=chercheClasse($_GET["saisie_classe_envoi"]);
$nom_classe=$dataClasse[0][1];
$matGroup=matGroup($nom_classe,$anneeScolaire);

// création de la matrice pour le select matiere
// 	sql
//		code_mat,libelle,sous_matiere
$sql=<<<SQL
SELECT
	code_mat,
	libelle,
	sous_matiere
FROM
	${prefixe}matieres
WHERE 
	offline = '0'
ORDER BY
	libelle
SQL;

$cursor=execSql($sql);
$data=chargeMat($cursor);
freeResult($cursor);
for($l=0;$l<count($data);$l++){
	for($c=0;$c<count($data);$c++){
		if(empty($data[$l][2])):
			$bool=0;
		else:
			$bool=true;
		endif;
		$matMat[$l][0]=$data[$l][0].":".$bool;
		$sl=trim($data[$l][1])." ".trim($data[$l][2]);
		$matMat[$l][1]=$sl;
	}
}
?>


<form method=post onsubmit="return valide();" action="affectation_creation4.php" name="formulaire">
<table border="1" cellpadding="3" cellspacing="1" bordercolor='#000000' height="85" align="center"  style="border-collapse: collapse;"  >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPER15?> <font id="color2"><?php print $nom_classe?></font></font></b> 
<b><font   id='menumodule1' >pour l'ann&eacute;e scolaire </font><font id="color2"><?php print $anneeScolaire ?></font></b> </td></tr>
<tr  id='cadreCentral0' >
<td >
<!-- //  debut -->
<table border="0" bgcolor="#ffffff" width=100%>
<TR><TD>
<TABLE border="1"  width=100%  style="border-collapse: collapse;"  >
<tr bgcolor="yellow" >
<td align=center><?php print LANGPER16?></td><TD align=center><?php print LANGPER17?></TD><TD align=center><?php print LANGPER18?></TD><TD align=center>&nbsp;&nbsp;<?php print LANGPER19?>&nbsp;&nbsp;</TD><TD align=center>&nbsp;&nbsp;<?php print LANGPER20?>&nbsp;&nbsp;</TD><TD align=center><?php print LANGPER21bis?></TD><TD align=center>&nbsp;<?php print "Visu.<i>*</i>"?>&nbsp;</TD><TD align=center>&nbsp;<?php print "Visu BTS Blanc.<i>***</i>"?>&nbsp;</TD><TD align=center>&nbsp;<?php print "Nbr&nbsp;d'heure&nbsp;**"?>&nbsp;</TD><TD align=center>&nbsp;<?php print "ECTS"?>&nbsp;</TD><td>&nbsp;Info sem.&nbsp;</td><td>Coef Certif</td><td>Note plancher</td>
<?php
for ($a=0;$a<$_GET["saisie_nb_matiere"];$a++) {
?>
<TR   >
	<TD>
		<input type="text" name="ordre" value="<?php print $a?>" size="3" onfocus='this.blur()'>
	</TD>
<TD>
<?php
$nameSelectMat="saisie_matiere_".$a;
print(selectHtml($nameSelectMat,1,false,$matMat));
?>
</TD>
<TD>
<select name="saisie_prof_<?php print $a?>">
<option value="0" STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX?></option>
<?php
select_personne_2('ENS','30');
// creation des options
// optimisation indispensable
?>
</select></TD>
<TD align=center><input type=text size=2 name=saisie_coef_<?php print $a?> ></TD>
<TD>
    <?php
    $nameSelectGrp="saisie_groupe_".$a;
    print(selectHtml($nameSelectGrp,1,false,$matGroup));
    ?>
</TD>
<TD align='center'><select name="saisie_langue_<?php print $a?>" >
<option id='select0' value=''><?php print LANGCHOIX?></option>
<option id='select1' value='LV1'>LV1</option>
<option id='select1' value='LV2'>LV2</option>
<option id='select1' value='LV3'>LV3</option>
<option id='select1' value='LV4'>LV4</option>
<option id='select1' value='OPT1'>OPT1</option>
<option id='select1' value='OPT2'>OPT2</option>
<option id='select1' value='OPT3'>OPT3</option>
<option id='select1' value='OPT4'>OPT4</option>
<option id='select1' value='DP3'>DP3</option>

</select></TD>


<td><input type='checkbox' name="saisie_visubull_<?php print $a?>" value='1' checked='checked' ></td>
<td><input type='checkbox' name="saisie_visubull_btsblanc_<?php print $a?>" value='1' ></td>
<td><input type='text' name="saisie_nbheure_<?php print $a?>" value='' size=3 ></td>
<td><input type='text' name="saisie_ects_<?php print $a?>" value='' size=3 ></td>
<td><select name='info_semestre_<?php print $a?>'>
<option id='select0' value='0'></option>
<option id='select1' value='1'>1</option>
<option id='select1' value='2'>2</option>
<option id='select1' value='3'>3</option>
<option id='select1' value='4'>4</option>
<option id='select1' value='5'>5</option>
<option id='select1' value='6'>6</option>
<option id='select1' value='7'>7</option>
<option id='select1' value='8'>8</option>
<option id='select1' value='9'>9</option>
<option id='select1' value='10'>10</option>
</select></td>


<td><input type='text' size='2' name="saisie_coef_certif_<?php print $a?>" /></td>
<td><input type='text' size='2' name="saisie_note_planche_<?php print $a?>" /></td>

</TR>
<?php	} ?>
</TD></TR>
</td></tr></table><BR>

<table width='100%' border='0' align='center' ><tr><td>
<input type='hidden' name='saisie_classe_envoi' value="<?php print $_GET["saisie_classe_envoi"]?>" >
<input type='hidden' name='saisie_nb_matiere' value="<?php print $a-1?>">
<input type='hidden'  name='anneeScolaire' value="<?php print $anneeScolaire?>">
<input type='hidden' name='saisie_tri' value="<?php print $_GET["tri"] ?>">
<script language=JavaScript>buttonMagic("<?php print LANGBT20?>","./affectation_creation.php","_top","",";parent.window.close();");</script>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGBT21?>","rien"); //text,nomInput</script><br><br>
</td></tr></table>
<br>
<i>* Visu. : Visualiser au sein du bulletin / ** Nombre d'heure annuelle / *** Visu. : Visualiser au sein du bulletin AFTEC BTS BLANC</i>
</TD></TR></TABLE>
     <!-- // fin  -->
     </td></tr></table>
     </form>

<!-- verif saisie -->
<script language=JavaScript>

// validation d'un champ de select
function Validselect(item){
 if (item == 0) {
        return (false) ;
 }else {
        return (true) ;
        }
}

//fonction de validation d'après la longueur de la chaîne
function ValidLongueur(item,len) {
   drapeau = 1;
   return (item.length >= len);
}

// affiche un message d'alerte
function error5(elem, text) {
// abandon si erreur déjà signalée
   if (flag) return;
   window.alert(text);
   elem.select();
   elem.focus();
   flag = true;
}

// affiche un message d'alerte
function error6(text) {
// abandon si erreur déjà signalée
   if (flag) return;
   window.alert(text);
   flag=true;
}

function valide() {
	flag=false;
	var nbmatiere=<?php print $a?>;
	nbmatiere=nbmatiere * 13;

	for (i=1;i<=nbmatiere;i++) {
		if (!Validselect(document.formulaire.elements[i].options.selectedIndex)) {
                error6(langfunc23);
       		}
		i=i+1;
		if (!Validselect(document.formulaire.elements[i].options.selectedIndex)) {
                error6(langfunc47);
       		}
		i=i+1;
		if (!ValidLongueur(document.formulaire.elements[i].value,1)) {
                	error5(document.formulaire.elements[i],langfunc48); }
        	if(isNaN(document.formulaire.elements[i].value)) {
                	error5(document.formulaire.elements[i],langfunc49); }
		i=i+10;
	}

	return !flag;
}
</script>
</BODY></HTML>
