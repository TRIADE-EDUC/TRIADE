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
error_reporting(0);
include("./librairie_php/lib_licence.php"); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="../librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="../librairie_js/lib_ordre_liste.js"></script>
<title>Triade</title>
</head>
<body id="bodyfond" marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
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
	var data = window.document.formulaire.bulletin_2.options;
	for (i=0;i<data.length;i++)
	{
		tab.push(data[i].value);
	}
	document.formulaire.saisie_recherche_final.value=tab.join(",");
}

</script>
<table border="0" cellpadding="3" cellspacing="1" width="703"  height="503" bgcolor="#0B3A0C">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Configuration de l'affichage des bulletins</font></b></td></tr>
<tr id="cadreCentral0" ><td valign='top'>
<form name='formulaire' action="configbulletin2.php" method='post' >

<input type=hidden name="saisie_nb_recherche" />
<input type=hidden name="saisie_recherche_final" />

<?php
include_once("../librairie_php/lib_bulletin.php");
include_once("../librairie_php/langue.php");
?>
<table border=0 width=100%>
<tr><td width=33% align=center>
<select size=30 name="bulletin_1" style="width:220px" multiple="multiple">
<?php listingBulletin() ?>
<?php listBulletinBlanc() ?>
</select>
</td>
	  <td width=33% align=center>
<input type="button" value="<?php print LANGCHER5?> >>>" onClick="calcul('+1');Deplacer(this.form.bulletin_1,this.form.bulletin_2,'Choisissez un élèment')" class="bouton2" >
<br /><br /><br /><br />
<input type="button" value="&lt;&lt;&lt; <?php print LANGCHER6 ?>" onClick="calcul('-1');Deplacer(this.form.bulletin_2,this.form.bulletin_1,'Choisissez un élèment')" class="bouton2" >
	  </td>
	  <td width=33% align=center>
     		<select size=30 name="bulletin_2" style="width:220px" multiple="multiple">
		<?php
		if (file_exists("../common/config.bulletin.php")) {
			include_once("../common/config.bulletin.php");
			$liste=LISTEBULLETIN;
			$tab=explode(",",$liste);
			foreach($tab as $key=>$value) {
				$libelle=RecupBulletin(trim($value));
				print "<option id='select1' value='$value'>$libelle</option>";
			}
		}else{
			print "<option>-------------</option>";
		}
		?>
		</select>
		<?php if ($liste == "") { ?>
			<script language="javascript">document.formulaire.bulletin_2.options.length=0;</script>
		<?php } ?>
</td>
	  <td>
<?php print LANGRECH6 ?> <br><br>
<input type=button value='<?php print LANGCHER7 ?>' style='width:100px' onClick='tjs_haut(this.form.bulletin_2)'  class="bouton2" >
<br><br>
<input type=button value='<?php print LANGCHER8 ?>' style='width:100px' onClick='tjs_bas(this.form.bulletin_2)' class="bouton2" >
	  </td>
     </tr>
     <tr><td colspan=4><br><ul><input type="submit" value="<?php print VALIDER ?>" class="BUTTON" onclick="prepEnvoi()" ></ul></td></tr>
     </table>
</form>
</td></tr></table>
</body>
</html>
