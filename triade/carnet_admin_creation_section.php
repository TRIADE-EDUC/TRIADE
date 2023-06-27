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
?>
<br />
<form method=post name="formulaire" >
<table border=0 align="center">
<tr><td align="center" colspan=3><font class="T2">Nom de la section : </font><input type="text" name="saisie_section" size=4 maxlength=5 /> (Max 5 caractères)<br><br></td></tr>


<tr><td width=33% align=center>
		Classes disponibles
     		<select size=18 name="saisie_depart"  style="width:120px">
			<?php select_classe();  ?>
		</select>
	  </td>
	  <td width=20% align=center>
<input type="button" value="<?php print LANGCHER5?> >>>" onClick="calcul('+1');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'Choisissez un élément')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
<br><br>
<input type="button" value="&lt;&lt;&lt; <?php print LANGCHER6 ?>" onClick="calcul('-1');Deplacer(this.form.saisie_recherche,this.form.saisie_depart,'Choisissez un élément')" STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" >
	  </td>
	  <td width=33% align=center>
		Classes liées à cette section
     		<select size=18 name="saisie_recherche" style="width:130px" multiple="multiple">
		<OPTION>-------------</OPTION>
		</select>
		<script language="javascript">
			// suppression de la ligne  mais on la garde pour la largeur
			document.formulaire.saisie_recherche.options.length=0;
		</script>
</td></tr>

<tr><td colspan="3" align="center"><br />
<input type=hidden name="saisie_nb_recherche" size=6>
<input type=hidden name="saisie_recherche_final" size=6>
<input type="submit" value="<?php print "Enregistrer"?>" name="create" class="BUTTON" onclick="prepEnvoi()" >
<input type="button" value="Supprimer une section" onclick="open('carnet_admin_supp_section.php','_parent','');" class="BUTTON"   />
<input type="button" value="Modifier une section" onclick="open('carnet_admin_modif_section.php','_parent','');" class="BUTTON"   />
<br><br>
<input type="button" value="Fermer la fenêtre" onclick="parent.window.close();" class="BUTTON"  />
</td></tr>

</table>
</form>


<?php
if (isset($_POST["create"])) {
	$nbElement=$_POST["saisie_nb_recherche"];
	$listeIdClasse=$_POST["saisie_recherche_final"];
	$section=$_POST["saisie_section"];
	$cr=enr_section($listeIdClasse,$section);
	if ($cr) {
		alertJs("Section enregistrée \\n\\n L'Equipe TRIADE");
	}else{
		alertJs("Section non enregistrée, le nom de cette section existe déjà.\\n\\n L'Equipe TRIADE");
	}
}

?>

</BODY></HTML>
