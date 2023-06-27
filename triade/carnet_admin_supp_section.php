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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<title>Création Section</title>
<script language="JavaScript">
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
	for (i=0;i<data.length;i++) {
		tab.push(data[i].value);
	}
	document.formulaire.saisie_recherche_final.value=tab.join(",");
}

</script>
</head>
<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();


if (isset($_POST["supp"])) {
	$section=$_POST["saisie_section"];
	$cr=supp_section($section);
	if ($cr) {
		alertJs("Section supprimée \\n\\n L'Equipe TRIADE");
	}
}

?>
<table border=1 align="center" width='100%' >
<tr><td width=13% align=center bgcolor='yellow' >&nbsp;Nom&nbsp;de&nbsp;la&nbsp;section&nbsp;</td>
<td width=33% align=center bgcolor='yellow' > Carnet utilisé </td>
<td width=5% align=center bgcolor='yellow' > Supprimer </td>
</tr>	  

<?php 
$data=listeSection(); //id,libelle,listeidclasse
for($i=0;$i<count($data);$i++) {
	$idsection=$data[$i][0];
	$data2=listeCarnet(); //id
	$carnet="aucun";
	for($j=0;$j<count($data2);$j++) {
		$idcarnet=$data2[$j][0];
		$cr=verifSectionCarnet($idcarnet,$idsection);
		if ($cr) {
			$supp="&nbsp;<i>Impossible</i>";
			$carnet=chercheNomCarnet($idcarnet);
			break;
		}
	}
	if (!$cr) {
		$supp="<input type='submit' value='Supprimer' class='bouton2' name='supp' /><input type='hidden' name='saisie_section' value='$idsection' />";
	}
?>
<form method=post name="formulaire" >
<tr class='tabnormal' onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
<td >&nbsp;&nbsp;<?php print $data[$i][1] ?></td>
<td >&nbsp;&nbsp;<?php print $carnet ?></td>
<td ><?php print $supp ?></td>
</tr>
</form>
<?php } ?>


</table>

<br><br>


<center><input type="button" value="Fermer la fenêtre" onclick="parent.window.close();" class="BUTTON"  /></center>
</td></tr>




</BODY></HTML>
