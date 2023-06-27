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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des données élèves" ?>  </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<ul><font class=T2><?php print "Indiquer les données à exporter" ?></font></ul>
<br />

<form method="post" action="export_eleve_2.php" >
<table border=1 width="100%" bordercolor='#000000' >
<tr>
<td id='bordure' class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<input type="checkbox" name="liste[]" value='nom' > nom <?php print INTITULEELEVE ?>  </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='prenom' > prénom <?php print INTITULEELEVE ?>  </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='classe' > classe  </td>
<td id='bordure'  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='date_naissance' > date naissance  </td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='adresse_eleve' >Adr. <?php print INTITULEELEVE ?></td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='ccp_eleve' >CCP <?php print INTITULEELEVE ?></td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='commune_eleve' >Commune <?php print INTITULEELEVE ?></td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='pays_eleve' >Pays <?php print INTITULEELEVE ?></td>
</tr>


<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='sexe' > sexe  </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='lieu_naissance' > lieu naissance </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='nationalite' > Nationalité  </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='numero_eleve' > INE - N° <?php print INTITULEELEVE ?>  </td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='regime' > régime </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='lv1' > langue vivant 1 </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='lv2' > langue vivant 2 </td>
<td id='bordure'  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='option' > Option </td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='civ_1' > Civ. tuteur 1 </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='nomtuteur' > nom tuteur 1 </td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='prenomtuteur' > prénom tuteur 1</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='adr1' >adr. tuteur 1</td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='code_post_adr1' >CCP tuteur 1</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='commune_adr1' >commune tuteur 1</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='tel_port_1' >Tél. port. tuteur 1</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='email' > Email tuteur 1 </td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='civ_2' >Civ. tuteur 2</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='nomtuteur_2' >nom tuteur 2</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='prenomtuteur_2' >prénom tuteur 2</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='adr2' >adr. tuteur 2</td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='code_post_adr2' > CCP tuteur 2</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='commune_adr2' >commune tuteur 2</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='tel_port_2' >Tél. port. tuteur 2</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='email_resp_2' >Email tuteur 2</td>
</tr>


<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='telephone' >Téléphone</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='tel_eleve' >Tél. port. <?php print INTITULEELEVE ?></td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='profession_pere' >Prof. Père</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='tel_prof_pere' >Tél. Prof. Père</td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='profession_mere' >Prof. Mère</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='tel_prof_mere' >Tél. Prof. Mère</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='class_ant' >Classe antérieur</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='nom_etablissement' >Nom établi.</td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='numero_etablissement' >N° établi.</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='code_postal_etablissement' >CCP établi.</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='commune_etablissement' >commune établi.</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='code_barre' >Code barre</td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='email_eleve' >Email <?php print INTITULEELEVE ?></td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='email_eleve_pro' >Email <?php print INTITULEELEVE ?> Univ.</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='annee_scolaire' >Année scolaire.</td>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='information' >Informations.</td>
</tr>

<tr>
<td id='bordure'   class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
	<input type="checkbox" name="liste[]" value='tel_fixe_eleve' >Tel. Fixe <?php print INTITULEELEVE ?>.</td>
</tr>

</table>
<!--
	/* elev_id,			0
	 * nom, 			1
	 * prenom,			2
	 * classe,			3
	 * lv1,				4
	 * lv2,				5
	 * option,			6
	 * regime,			7
	 * date_naissance,		8
	 * lieu_naissance,		9
	 * nationalite,			10
	 * passwd,			11
	 * passwd_eleve,		12
	 * civ_1,			13
	 * nomtuteur,			14
	 * prenomtuteur,		15
	 * adr1,			16
	 * code_post_adr1,		17
	 * commune_adr1,		18
	 * tel_port_1,			19
	 * civ_2,			20
	 * nom_resp_2,			21
	 * prenom_resp_2,		22
	 * adr2,			23
	 * code_post_adr2,		24
	 * commune_adr2,		25
	 * tel_port_2,			26
	 * telephone,			27
	 * profession_pere,		28
	 * tel_prof_pere,		29
	 * profession_mere,		30
	 * tel_prof_mere,		31
	 * nom_etablissement,		32
	 * numero_etablissement,	33
	 * code_postal_etablissement,	34
	 * commune_etablissement,	35
	 * numero_eleve,		36
	 * photo,			37
	 * email,			38
	 * email_eleve,			39
	 * email_resp_2,		40
	 * class_ant,			41
	 * annee_ant,			42
	 * numero_gep,			43
	 * valid_forward_mail_eleve,	44
	 * valid_forward_mail_parent,	45
	 * tel_eleve,			46
	 * code_compta,			47
	 * sexe 			48
	 */
-->
<br>

<font class=T2> Ajouter d'autres colonnes : <input type=text size=3 name="nbcolplus" value="0" /></font> (<i>nbr de colonne(s) supplémentaire(s)</i>)
<br><br>
<center><input type="submit" value="Suivant -->" class="BUTTON" name="create" /> </center>
</form>

<br />
<!-- // fin  -->
</td></tr></table>
<BR>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>
</BODY></HTML>
