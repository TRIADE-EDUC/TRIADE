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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_bascule_select.js"></script>
<script language="JavaScript" src="./librairie_js/lib_ordre_liste.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
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
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCHER1?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
     <!-- // debut form  -->
     <form action='./recherche_complexe_2.php' method=post name="formulaire" >
     <BR>
     <input type=hidden name="saisie_nb_recherche" size=6>
     <input type=hidden name="saisie_recherche_final" size=6>
     <input type=hidden name="saisie_fichier_format" value='<?php print $_POST["saisie_fichier_format"]?>' size=6>
     <input type=hidden name="saisie_separateur" value='<?php print $_POST["saisie_separateur"]?>' size=6>
     <?php
     ?>
	     <ul><font class=T2><?php print LANGRECH5 ?> :</font> <img src="./image/commun/affichage.gif" align=center></ul>
     <table border=0 width=100%>
     <tr><td width=33% align=center>
     		<select size=34 name="saisie_depart"  style="width:140px">
			<?php include("./librairie_php/lib_recherche_complexe.php")?>
		</select>
	  </td>
	  <td width=33% align=center>
<input type="button" value="<?php print LANGCHER5?> >>>" onClick="calcul('+1');Deplacer(this.form.saisie_depart,this.form.saisie_recherche,'Choisissez un élèment')" class="bouton2" >
<br /><br /><br /><br />
<input type="button" value="&lt;&lt;&lt; <?php print LANGCHER6 ?>" onClick="calcul('-1');Deplacer(this.form.saisie_recherche,this.form.saisie_depart,'Choisissez un élèment')" class="bouton2" >
	  </td>
	  <td width=33% align=center>
     		<select size=34 name="saisie_recherche" style="width:140px" multiple="multiple">
		<OPTION>-------------</OPTION>
		</select>
		<script language="javascript">
			// suppression de la ligne  mais on la garde pour la largeur
			document.formulaire.saisie_recherche.options.length=0;
		</script>
</td>
	  <td>
<?php print LANGRECH6 ?> <br><br>
<input type=button value='<?php print LANGCHER7 ?>' style='width:100px' onClick='tjs_haut(this.form.saisie_recherche)'  class="bouton2" >
<br><br>
<input type=button value='<?php print LANGCHER8 ?>' style='width:100px' onClick='tjs_bas(this.form.saisie_recherche)' class="bouton2" >
	  </td>
     </tr>
     <tr><td colspan=4><br><ul><input type="submit" value="<?php print LANGCHER9?> >" class="BUTTON" onclick="prepEnvoi()" ></ul></td></tr>
     </table>
      </form>
 <!-- // fin form -->
 </td></tr></table>
<br /><br />
<script type="text/JavaScript">InitBulle('#000000','#CCCCFF','red',1);</script>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
</BODY>
</HTML>
