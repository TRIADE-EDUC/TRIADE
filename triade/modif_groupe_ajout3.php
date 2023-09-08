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
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Liste Elève </title>
<script language="javaScript">
var nbElems=0;
function calcul(op) {
        // calcul le nombre d'élèment
        nbElems = eval(nbElems + op);
        if (nbElems < 0 ) { nbElems = 0; }
        document.formulaire.saisie_nb_recherche.value=nbElems;
}

function prepEnvoi() {
        var hid = new String();
        var tab = new Array();
        var data = window.document.formulaire.saisie_recherche.options;
        for (i=0;i<data.length;i++)
        {
                tab.push(data[i].value);
        }
        document.formulaire.saisie_recherche_final.value=tab.join(",");
}

</script>
</head>
<body id='bodyfond2' >
<center>
<?php
include("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuprof") {
	$saisie_classe=$_POST["sClasseGrp"];
	$cnx=cnx();
	verif_profp_class($_SESSION["id_pers"],$saisie_classe);
	$nomClasse=chercheClasse_nom($saisie_classe);
}else{
	$saisie_classe="";
	validerequete("menuadmin");
	$cnx=cnx();
}

$gid=$_POST["gid"];
$sql="SELECT libelle,liste_elev FROM ${prefixe}groupes WHERE group_id='$gid'";

$res=execSql($sql);
$data=chargeMat($res);
$nomgrp=$data[0][0];
$liste_eleves=preg_replace('/\{/',"",$data[0][1]);
$liste_eleves=preg_replace('/\}/',"",$liste_eleves);
unset($data);
if ($liste_eleves != "") {
	$sql="SELECT nom,prenom,libelle,elev_id FROM ${prefixe}eleves, ${prefixe}classes where classe=code_class AND elev_id IN ($liste_eleves)";
	$res=execSql($sql);
	$data=chargeMat($res);
}


$saisie_classe=$_POST["saisie_classe"];
$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data2=chargeMat($res);
$nomclasse=chercheClasse($saisie_classe);
?>

<form method=post name="formulaire" action="modif_groupe_ajout4.php" >
<table border="1" width=99% bordercolor="#000000">
<TR>
<TD bgcolor="yellow" ><b><?php print LANGGRP34 ?><font color=red><?php print $nomclasse[0][1] ?> </font></B></TD>
<TD bgcolor="yellow" width=20>&nbsp;</TD>
<TD bgcolor="yellow" ><B><?php print LANGGRP35 ?> <font color=red><?php print $nomgrp ?> </font> </B></TD>
<TD bgcolor="yellow" width=20>&nbsp;</TD>
</tr>
<tr>
<td valign=top align=center><br><select name="saisie_depart"  style="width:120px" size="<?php print count($data2) ?>"  >
<?php
for($i=0;$i<count($data2);$i++) {
    if (verifEleveDansGroupe($data2[$i][1],$gid)) {
	    print "<option value='".$data2[$i][1]."' >".ucwords($data2[$i][2])." ".ucwords($data2[$i][3]). "</option>";
    }
}
?>
</select>
</td>
<td align=center >
&nbsp;<input type="button" value="<?php print LANGSTAGE3 ?> >>>" onClick="calcul('+1');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'Choisissez un élèment')" class="bouton2" >&nbsp;
</td>
<td valign=top align=center><br><select name="saisie_recherche" style="width:130px" multiple="multiple" size="<?php print count($data)+count($data2)  ?>" >
<?php
for($i=0;$i<count($data);$i++) {
	print "<option value='".$data[$i][3]."' >".ucwords($data[$i][0])." ".ucwords($data[$i][1]). "</option>";
}
?>
</select>
<br><br>
</td>
<td align=center>
          &nbsp;Trier par ordre&nbsp; <br><br>
&nbsp;<input type=button value='<?php print LANGCHER7 ?>' style='width:100px' onClick='tjs_haut(this.form.saisie_recherche)' class="bouton2" >&nbsp;
<br><br>
&nbsp;<input type=button value='<?php print LANGCHER8 ?>' style='width:100px' onClick='tjs_bas(this.form.saisie_recherche)' class="bouton2" >&nbsp;
          </td>


</tr></table>
<BR><BR>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicFermeture()</script></td>
<input type=hidden name="saisie_recherche_final">
<input type=hidden name='gid' value="<?php print $_POST["gid"];?>">
<input type=hidden name='saisie_intitule' value="<?php print trim($nomgrp);?>">
<input type=hidden name="saisie_nb_recherche" size=6>
<input type=hidden name="sClasseGrp" value="<?php print $_POST["sClasseGrp"];?>" > 
<td><script language=JavaScript>buttonMagicSubmit3("<?php print LANGGRP36 ?>","create","onclick='prepEnvoi()'"); </script>
</td></tr></table>
</center>
</form>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
