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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include("./librairie_php/lib_licence.php"); ?>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="468" bgcolor="#0B3A0C" >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Création compte pour la direction</font></b></td></tr>
<tr id='cadreCentral0'><td valign=top>
<!-- // debut de la saisie -->
<blockquote>
<BR>
<form method=post name="formulaire">

<fieldset><legend>Information de connexion</legend>
<table width=80% border=0 cellpadding="2" cellspacing="2" >
<tr><td colspan=2 >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<font class="T2">M : </font><input type=radio name="saisie_intitule" value='0' class='btradio1' checked="checked">
<font class="T2">Mme : </font><input type=radio name="saisie_intitule" value='1'  class='btradio1' >
<font class="T2">Mlle : </font><input type=radio name="saisie_intitule" value='2'  class='btradio1' >
</td></tr>
<tr><td align=right width=40%><font class="T2"><?php print "Nom"?> : </font></td><td><input type=text name="saisie_creat_nom"  size=33 maxlength=30> </td></tr>
<tr><td align=right><font class="T2"><?php print "Prénom"?> : </font></td><td><input type=text name="saisie_creat_prenom"   size=33 maxlength=30> </td></tr>
<tr><td align=right><font class="T2"><?php print "Mot&nbsp;de&nbsp;passe"?> : </font></td><td><input type=text name="saisie_creat_password" size=33 maxlength=20></td></tr>
</table>
</fieldset>
<br><br>
</blockquote>
<ul>

<script language=JavaScript>buttonMagicSubmit("<?php print "Enregistrer"?>","create"); //text,nomInput</script>
<BR><br>
</ul>
</form>
<?php
if(isset($_POST["create"])):
	include_once("../common/config.inc.php");
	include_once("../librairie_php/db_triade.php");
	// connexion P
	$cnx=cnx();
	error($cnx);
	// requete ? prenom2 ?
		$cr=create_personnel_via_admin($_POST["saisie_creat_nom"],$_POST["saisie_creat_prenom"],$_POST["saisie_creat_password"],'ADM',$_POST["saisie_intitule"]);
		if($cr > 0){
			history_cmd("admin-triade","CREATION","administration");
			alertJs("Compte créé - Equipe Triade");
		}elseif ($cr == -1) {
			alertJs("Ce compte existe déjà \\n\\n L'Equipe Triade.");
		}else {
			include_once("../librairie_php/langue-text-fr.php");
			$affiche=affichageMessageSecurite2();	
			alertJs($affiche);
		}
	Pgclose();
endif;
?>

</td></tr></table>
<br><br>
<table border="0" cellpadding="3" cellspacing="1" width="468" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Liste des membres de la direction</font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<table width=100%>
<?php
include_once('../librairie_php/db_triade.php');
$cnx=cnx();
$data=affPers('ADM');
for($i=0;$i<count($data);$i++) {
	print "<tr class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\">\n";
	print "<td >".civ($data[$i][1])."&nbsp;".strtoupper($data[$i][2])."</td>\n";
	print "<td >".ucfirst($data[$i][3])."</td>\n";
	print "<td width=5><input type=button class=button value=\"Visualiser / Modifier\" onclick=\"open('modif_admin.php?saisie_id=".$data[$i][0]."','_parent','');\" ></td>\n";
	print "</tr>\n";
}
?>
</table>

<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
