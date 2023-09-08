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
include_once("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Upgrade de Triade</font></b></td></tr>
<tr id='cadreCentral0' ><td >
<!-- // debut de la saisie -->
<?php
if (UPGRADE == 1) {
	print "<br><ul><font class=T2>Ce service est pris en compte par notre  équipe.";
	print "<br><br>Nous nous occupons de mettre à jour Triade automatiquement ";
	print "<br><br>L'Equipe Triade</font></ul>";

}else {
	if (file_exists("../installation/update/upgrade.log")) {
	
		$fcontents = file("../data/install_log/install.inc" ); 
		$tab=explode(":",$fcontents[1]);
    		$version_actuel = trim($tab[1]);
		$fichierinclude=$version_actuel."-".DBTYPE."-".VERSION;
		$fichierinclude=preg_replace('/\./','',$fichierinclude);
		if (file_exists("../installation/update/".$fichierinclude.".php")) {
			include_once("./librairie_php/db_triade_admin.php");
			include_once("../installation/update/".$fichierinclude.".php");
			$cnx=cnx();
                        if (defined("TYPETABLE")) {
                                $typetable=TYPETABLE;
                        }else{
                                $typetable="MYISAM";
                        }
                        update(PREFIXE,$typetable);
                        Pgclose();

                        $cnx=cnx();
                        supprimerTousLesPatchs();
                        Pgclose();
			
			$fp=fopen("../common/lib_patch.php","w");
			$text3 = '<?php'."\n";
			$text3.= 'define("VERSIONPATCH","000-00");'."\n";
			$text3.= '?>'."\n";
			fwrite($fp,"$text3");
			fclose($fp);
			unlink("../installation/update/upgrade.log");
			print "<ul><br />";
			print "<font class='T2'><strong>Mise à jour en terminé.</strong><br /><br />";
			print "Vous devez effectuer une vérification de la base, <br /><br />";
			print " et redéfinir la configuration de Triade <br />";
			print "</ul>";
		}else{
			print "<center>";
			print "<font class='T2'>Mise à jour effectuée</font>";
			print "</center>";
			unlink("../installation/update/upgrade.log");
		}
	}else{
		print "<center>";
		print "<font class='T2'>Mise à jour déjà effectuée.</font>";
		print "</center>";

	}
}	
?>
<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
