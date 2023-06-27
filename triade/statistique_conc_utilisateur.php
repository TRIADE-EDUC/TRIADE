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
<?php
include_once("./common/lib_ecole.php");
include_once("./common/lib_admin.php");
include_once("./".REPADMIN."/librairie_php/lib_error.php");
include_once("./".REPADMIN."/librairie_php/mactu.php");
if (empty($_SESSION["admin1"])) {
    print "<script language='javascript'>";
    print "location.href='/".REPECOLE."/".REPADMIN."/acces_refuse.php'";
    print "</script>";
    exit;
}
?>

<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
        <SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart.js"></SCRIPT>
            <?php include("./".REPADMIN."/librairie_php/lib_defilement.php"); ?>
            </TD><td width="472" valign="middle" rowspan="3" align="center">
            <div align='center'><?php top_h(); ?>
            <SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart1.js"></SCRIPT>
            <table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
            <tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Statistique des comptes enregistrés.</font></b></td></tr>
            <tr id='cadreCentral0'><td > <p align="left"><font color="#000000">
	    <ul>
	<br><input type=button value="Autres statistiques " onclick="history.go(-1);"  class='bouton2'>	   <br /></ul>
               <!-- // debut de la saisie -->
<?php
include_once("./common/config.inc.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
$data=affStatUtilisateur();
// $data :
$j=count($data);
$totalUtilisateur=count($data);
$limit=50;
if (isset($_POST["nbvisu"])) { 
	$limit=$_POST["nbvisu"]; 
	if (!is_numeric($limit)) {
		$limit=50;
	}
}


$data=affStatUtilisateur2($limit); // nom, prenom, date_entree, type_membre ,nb_conx, der_conx
?>
<form method='post' action='statistique_conc_utilisateur.php' >
<ul><font class='T2'>Nombre d'enregistrement au total : <b><?php print $totalUtilisateur?></b></font></ul>
<br />&nbsp;&nbsp;&nbsp;&nbsp;Les <input type='text' value='<?php print $limit ?>' name='nbvisu' onchange="this.form.submit()" size=4 /> derniers enregistrés.
</form>
 
<table border=1 bgcolor="#ffffff" width=100%>
<tr>
<td align="center" bgcolor='yellow'><b>Nom</b></td>
<td align="center" bgcolor='yellow'><b>Prénom</b></td>
<td align="center" width="5%"  bgcolor='yellow'><b>Membre</b></td>
<td align="center" width="5%"  bgcolor='yellow'><b>Valider le</b></td>
<td align="center" width="5%"  bgcolor='yellow'><b>Nb cnx</b></td>
<td align="center" width="5%"  bgcolor='yellow'><b>Der cnx</b></td>
</tr>
<?php
for($i=0;$i<count($data);$i++)
{
	$membre=$data[$i][3];
	if ($membre == "menuadmin") { $membre="Direction"; } 
	if ($membre == "menuscolaire") { $membre="Vie&nbsp;scolaire"; } 
	if ($membre == "menuprof") { $membre="Enseignant"; } 
	if ($membre == "menuparent") { $membre="Parent"; } 
	if ($membre == "menueleve") { $membre="Elève"; } 
	if ($membre == "menututeur") { $membre="Tuteur&nbsp;Stage"; } 
	if ($membre == "menupersonnel") { $membre="Personnel"; }

	?>
	<tr>
	<td>&nbsp;<?php print ucwords($data[$i][0])?></td>
	<td>&nbsp;<?php print ucwords($data[$i][1])?></td>
	<td>&nbsp;<?php print $membre ?></td>
	<td align=center><?php print dateForm($data[$i][2])?></td>
	<td>&nbsp;<?php print $data[$i][4]?></td>
	<td align=center><?php print dateForm($data[$i][5])?></td>
	</tr>
	<?php
}
?>
</table>
<br/> <br />
<ul><input type=button value="Autres statistiques " onclick="history.go(-1);"  class='bouton2'></ul>
</blockquote> </td></tr></table>
<SCRIPT language="JavaScript" src="./<?php print REPADMIN?>/librairie_js/menudepart2.js"></SCRIPT>
</body>
</html>
