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

// Inclure la librairie d'initialisation du module
include("librairie_php/lib_init_module.inc.php");

// Verification autorisations acces au module
if(autorisation_module()) {
		//******************************Initialisation fichier prelevement******************************
	$sql = "SELECT * FROM ".FIN_TAB_CONFIG_ECOLE." ";
	$res = execSql($sql);
	$ligne = &$res->fetchRow();
	// Nom du fichier de prelevement
	$g_nom_fichier_prelevement = $ligne[0];
	//*************************************************************************************
	$suppression = lire_parametre('supp', '', 'GET');

	if($suppression != '')
	{
		$sql = "DELETE FROM ".FIN_TAB_REGLEMENT." ";
		$sql.= "WHERE numero = $suppression";
		$res=execSql($sql);
		
		header('Location: export.php') ;
	}
	
	$sql = "SELECT numero, date_enregistrement, date_reglement ";
	$sql.= "FROM ".FIN_TAB_REGLEMENT." ";
	$sql.= "GROUP BY numero ";
	$sql.= "ORDER BY 2 DESC ";
	$res=execSql($sql);
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

?>
<html>
	<head>
		<meta http-equiv="CacheControl" content = "no-cache">
		<meta http-equiv="pragma" content = "no-cache">
		<meta http-equiv="expires" content = -1>
		<meta name="Copyright" content="Triade©, 2001">
		<base href="<?php echo site_url_racine(FIN_REP_MODULE); ?>">
		<link title="style" type="text/CSS" rel="stylesheet" href="./librairie_css/css.css">
		<script language="javascript" src="./librairie_js/clickdroit2.js"></script>
		<script language="javascript" src="./librairie_js/function.js"></script>
		<script language="javascript" src="./librairie_js/lib_css.js"></script>
		<script language="javascript" src="./librairie_js/verif_creat.js"></script>
		<link title="style" type="text/CSS" rel="stylesheet" href="./<?php echo $g_chemin_relatif_module; ?>librairie_css/css.css">
		<?php
		// Inclure les scripts Javascript
		inclure_scripts_js_toutes_pages();
		?>
		<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
	</head>
	
	<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'.js'?>"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
<?php  $today= date ("j M, Y");  ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'>
<?php top_h(); ?>
<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'1.js'?>"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Exportation des données" ?>  </font></b></td>
</tr>
<tr id='cadreCentral0'>
<td valign=top>
<br />
<ul>
<font class=T2><?php print "Réédition des prélévement" ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
<input type="button" class="button" value="<?php echo LANG_FIN_GENE_003; ?>" onClick="onclick_annuler();" <?php echo $disabled_cadre; ?>>
											
<br />
<br/>
<?php
print "<table width='90%'>";

for($i=0;$i<$res->numRows();$i++) {
	$res1 = $res->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
	if($ligne[0] != 0)
	{
		if($i == 0)
		{
			//$nom_fichier = $g_nom_fichier_prelevement . '_' . substr($ligne[1], 8, 2). '-' . substr($ligne[1], 5, 2). '-' . substr($ligne[1], 0, 4).' à ' . substr($ligne[1], 11, 8);
			$nom_fichier = $g_nom_fichier_prelevement . '_' . substr($ligne[2], 8, 2). '-' . substr($ligne[2], 5, 2). '-' . substr($ligne[2], 0, 4);

			$numero = $ligne[0];
			$date = substr($ligne[1], 8, 2). '/' . substr($ligne[1], 5, 2). '/' . substr($ligne[1], 0, 4);
			print "
			<tr>
				<td>
					<img src='./image/commun/on1.gif' width='8' height='8'>
					<span id='disp$i'>$nom_fichier     (Généré le $date)</span>
					<a href='module_financier/export1.php?num=$numero' title=\"Générer fichier de prélèvement\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:blue;font-weight:bold;'\"  
					onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/texte.png' border='0' align='center'/></a>
					<a href='module_financier/export2.php?num=$numero' title=\"Exporter en Excel\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:green;font-weight:bold;'\"  
					onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/icone-excel.gif' border='0' align='center'/></a>
					<a href='module_financier/export.php?supp=$numero' title=\"Supprimer\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:red;font-weight:bold;'\"  
					onclick=\"return(confirm('Etes-vous sûr de vouloir supprimer ce fichier généré le $date ?'))\"
					onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/supprimer.png' border='0' align='center' width='24' height='24'/></a>
				</td>
			</tr>";
		}
		else
		{
			//$nom_fichier = $g_nom_fichier_prelevement . '_' . substr($ligne[1], 8, 2). '-' . substr($ligne[1], 5, 2). '-' . substr($ligne[1], 0, 4).' à ' . substr($ligne[1], 11, 8);
			$nom_fichier = $g_nom_fichier_prelevement . '_' . substr($ligne[2], 8, 2). '-' . substr($ligne[2], 5, 2). '-' . substr($ligne[2], 0, 4);
			$numero = $ligne[0];
			$date = substr($ligne[1], 8, 2). '/' . substr($ligne[1], 5, 2). '/' . substr($ligne[1], 0, 4);
			print "
			<tr>
				<td>
					<img src='./image/commun/on1.gif' width='8' height='8'>
					<span id='disp$i'>$nom_fichier     (Généré le $date)</span>
					<a href='module_financier/export1.php?num=$numero' title=\"Générer fichier de prélèvement\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:blue;font-weight:bold;'\"  
					onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/texte.png' border='0' align='center'/></a>
					
					<a href='module_financier/export2.php?num=$numero' title=\"Exporter en Excel\" onmouseover=\"document.getElementById('disp$i').style.cssText='color:green;font-weight:bold;'\"  
					onmouseout=\"document.getElementById('disp$i').style.cssText='color:black;' \" ><img src='module_financier/images/icone-excel.gif' border='0' align='center'/></a>
					
				</td>
			</tr>";
		}
	}
}

?>

</table>
<br><br>
<!-- // fin  -->
</td></tr>
</table>
<BR>
<script language="javascript">
function onclick_annuler() 
{
	document.getElementById('formulaire_annuler').submit();
}

</script>
<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>paiements.php" method="post">
					</form>












<SCRIPT language="JavaScript" src="<?php print './librairie_js/'.$_SESSION[membre].'2.js'?>"> </SCRIPT>
<SCRIPT language="JavaScript">InitBulle("#000000","#FFFFFF","red",1);</SCRIPT>

	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>