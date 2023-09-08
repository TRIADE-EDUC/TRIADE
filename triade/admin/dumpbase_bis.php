<?php
session_start();
error_reporting(0);
include_once("../librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
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
<meta name="Copyright" content="TriadeÂ©, 2001">
<?php include("./librairie_php/lib_licence.php"); ?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Sauvegarde des donn&eacute;es et de la base Triade</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<font class=T2>
<br>
<ul>
<?php


if (isset($_POST["create"])) {

	include_once("../common/config.inc.php");
	include_once("./librairie_php/lib_licence.php");
	include_once("./librairie_php/db_triade_admin.php");
	include_once("../common/config2.inc.php");

	if (DBTYPE == "mysql") {

		$cnx=cnx();
		delete_groupe_null();

		$host=HOST;
		$base=DB;
		$login=USER;
		$password=PWD;

		if (!is_dir("../data/dump")) { mkdir("../data/dump"); }
		@unlink("../data/dump/dump_2.sql");
		@unlink("../data/dump/dump_common.zip");
		@unlink("../data/dump/dump_data.zip");
//-----------------------------------------------------------------------//
	/* GÃ©nÃ¨re le nom du fichier */
	$date = date("d-m-y H\hi\m");
	$nom_fichier = $bdd." $date.sql";

	/* Envoie les en-tetes */
//	header('Content-Type: application/octetstream');
//	header('Content-Disposition: filename="'.$nom_fichier.'"');
//	header('Pragma: no-cache');
//	header('Expires: 0');
	$bdd=$base;	
	$cnx=@mysqli_connect($host, $login, $password);
	$result = @mysqli_list_tables($base);

	/* Tant qu'il y a des tables */
	while ($row = mysqli_fetch_row($cnx,$result))
	{
		$lignesql.="\n#\n# Table `".$row[0]."`\n#\n";

		$lignesql.= "DROP TABLE IF EXISTS `$row[0]`;\n";
		/* Se connecte Ã  la base Ã  sauvegarder */
		mysqli_select_db($cnx,$bdd);

		/* Enregistre sa structure */
		$req = mysqli_query($cnx,"SHOW CREATE TABLE ".$row[0]);
		$res = mysqli_fetch_array($cnx,$req);
		$lignesql.= $res[1].";\n\n";
		

		# Si le contenu de la table est demandÃ©
		/* SÃ©lectionne toutes les entrÃ©es de la table */
		$requete = mysqli_unbuffered_query($cnx,"SELECT * FROM ".$row[0].";");
		$nb = mysqli_num_fields($cnx,$requete);
		$reqcomplete = "";
		$j = 0;

		/* Si insertion complete demandÃ©e, on recupÃ¨re le nom des champs */
		$reqcomplete = ' (';
			for($i = 0; $i < $nb; $i++)
			{
				$reqcomplete .= '`'.mysqli_field_name($cnx,$requete, $i).'`';
				/* Continue ou termine la string */
				if($i == ($nb - 1))	$reqcomplete .= ')';
				else			$reqcomplete .= ', ';
			}
			
			/* Tant qu'il y a des entrÃ©es */
		$type_requete="insert";
		While($res = mysqli_fetch_array($cnx,$requete))
		{
			$i = 0;

			/* Differents types de requetes */
			$lignesql.= (($type_requete == "insert") ? "INSERT" : "REPLACE");
			$lignesql.= " INTO `$row[0]`".$reqcomplete." VALUES (";

			while($i<$nb)
			{
				/* Protege les caracteres speciaux */
				$ligne = mysqli_escape_string($cnx,$res[$i]);

				/* Si l'entree est un chiffre, pas de quotes */
				if(is_numeric($ligne)) 	$lignesql.= $ligne;
				else 			$lignesql.= '"'.$ligne.'"';

				/* Termine ou continue la ligne */
				if($i == ($nb - 1))	$lignesql.= ');';
				else			$lignesql.= ', ';
				$i++;
			}

			$lignesql.= "\n";

			/* Compteur du nombre d'enregistrements */
			$NbRecords ++;
		}
		/* Compteur du nombre de tables */
		$NbTables ++;
	}		
	@mysqli_close($cnx);

	$fp = fopen("../data/dump/dump_2.sql", "w");
	fwrite($fp,$lignesql);
	fclose($fp);

	validGroup();

//-----------------------------------------------------------------------//
		

		$fp = fopen("../data/dump/mysql_2.inc", "w");
		include_once("../librairie_php/timezone.php");
		$text=dateDMY();
		$text.=" ".dateHIS();
		fwrite($fp,$text);
		fclose($fp);
		htaccess("../data/dump");
		acceslog("Sauvegarde de la base de donnÃ©e");

		include_once('./librairie_php/pclzip.lib.php');
		$archive = new PclZip('../data/dump/dump_common.zip');
		$archive->create('../common');
		$archive->delete(PCLZIP_OPT_BY_EREG, 'config.inc.php$');
		$archive = new PclZip('../data/dump/dump_data.zip');
		$archive->create('../data/audio');
		$archive = new PclZip('../data/dump/dump_data.zip');

  	if (is_dir('../data/circulaire')){$v_list=$archive->add('../data/circulaire');if($v_list == 0){die("Error : ".$archive->errorInfo(true));}}
	if (is_dir('../data/compteur')){$v_list=$archive->add('../data/compteur'); if ($v_list == 0) {die("Error : ".$archive->errorInfo(true));}}
        if (is_dir('../data/DevoirScolaire')){$v_list=$archive->add('../data/DevoirScolaire');if($v_list == 0){die("Error : ".$archive->errorInfo(true));}}
	if (is_dir('../data/fichier_ASCII')){$v_list=$archive->add('../data/fichier_ASCII'); if ($v_list == 0){die("Error : ".$archive->errorInfo(true));}}
	if (is_dir('../data/fichier_gep')){$v_list = $archive->add('../data/fichier_gep'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true));}}
	if (is_dir('../data/forum')){$v_list = $archive->add('../data/forum'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true));}}
	if (is_dir('../data/image_banniere')){$v_list = $archive->add('../data/image_banniere'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/image_diapo')){$v_list = $archive->add('../data/image_diapo'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/image_eleve')){$v_list = $archive->add('../data/image_eleve'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/image_pers')){$v_list = $archive->add('../data/image_pers'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/install_log')){$v_list = $archive->add('../data/install_log'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/menuadmin')){$v_list = $archive->add('../data/menuadmin'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/menueleve')){$v_list = $archive->add('../data/menueleve'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/menuparent')){$v_list = $archive->add('../data/menuparent'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/menuprof')){$v_list = $archive->add('../data/menuprof'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/menuscolaire')){$v_list = $archive->add('../data/menuscolaire'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/parametrage')){$v_list = $archive->add('../data/parametrage'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/pdf_abs')){$v_list = $archive->add('../data/pdf_abs'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/pdf_bull')){$v_list = $archive->add('../data/pdf_bull'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/pdf_certif')){$v_list = $archive->add('../data/pdf_certif'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/recherche')){$v_list = $archive->add('../data/recherche'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/rss')){$v_list = $archive->add('../data/rss'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/sauvegarde')){$v_list = $archive->add('../data/sauvegarde'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}
	if (is_dir('../data/stockage')){$v_list = $archive->add('../data/stockage'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }}

		print "<script>alert('Sauvegarde des donn�s Terminées');</script>";
		acceslog("Sauvegarde des donn�es ./data/ ");
    	}
}
?>

<?php
if (DBTYPE == "mysql") {
	$fichier="../data/dump/mysql_2.inc";
	if (file_exists($fichier)) {
		$fichier=fopen($fichier,"r");
	    	$donnee=fread($fichier,100000);
		fclose($fichier);
	?>
		DerniÃ¨re sauvegarde le : <?php print $donnee ?><br><br><b>Compatible Windows</b><br><br>
	<img src="./image/on1.gif" align='center' width='8' height='8' /> <a href='recup_mysql_base.php?id=base_2'><b>RÃ©cupÃ©rer la base de donnÃ©es, cliquez ici <--</b></a><br /><br />
	<img src="./image/on1.gif" align='center' width='8' height='8' /> <a href='recup_mysql_base.php?id=conf'><b>RÃ©cupÃ©rer la configuration, cliquez ici <--</b></a><br /><br />
	<img src="./image/on1.gif" align='center' width='8' height='8' /> <a href='recup_mysql_base.php?id=data'><b>RÃ©cupÃ©rer les donnÃ©es,  cliquez ici <--</b></a>
<?php
	}
}
?>

<br><br><br>
<form method=post action="dumpbase.php" >
<table><tr><td>
<font class=T2>Acc&egrave;s &agrave; la sauvegarde (Linux)  : </font></td><td>
<script language=JavaScript>buttonMagicSubmit("Continuer","null"); //text,nomInput</script>
</td></tr>
<tr><td height=5></td></tr>
</table>
</form>


<form method=post action="dumpbase_bis.php" >
<table><tr><td>
<font class=T2>Effectuer une sauvegarde (Windows)  : </font></td><td>
<script language=JavaScript>buttonMagicSubmit("Sauvegarder","create"); //text,nomInput</script>
</td></tr>
<tr><td height=5></td></tr>
</table>
</form>

</ul>

</font>
</td></tr></table>
<br><br>


<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Sauvegarde AutomatisÃ©e - Enregistrement</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php

	
	include_once("../common/crondump.inc.php");
	if (defined("BACKUPKEY")) {
		$nbSave=NBSAVE;	
		if (isset($_POST["modif"])) {
			$text="<?php\n";
			$text.="define(\"BACKUPKEY\",\"".BACKUPKEY."\");\n";
			$text.="define(\"NBSAVE\",\"".$_POST["nbsave"]."\");\n";
			$text.="?>\n";
			$fp = fopen("../common/crondump.inc.php", "w");
			fwrite($fp,$text);
			fclose($fp);
			$nbSave=$_POST["nbsave"];
		}


		print "<script>var etat=0; </script>";
		print "<script language='JavaScript' src='https://support.triade-educ.org/support/crontab/verif.php?id=".BACKUPKEY."'></script>";

		print "<br /><ul><form method='post'><font class='T2'>Nombre d'archives possible : ";
		print "<select name='nbsave'>
			<option value='$nbSave' id='select0' >$nbSave</option>
			<option value='3' id='select1' >03</option>
			<option value='5' id='select1'>05</option>
			<option value='10' id='select1'>10</option>
			<option value='20' id='select1'>20</option>
			<option value='30' id='select1'>30</option>
			</select>";
		print "&nbsp;&nbsp;<input type=submit name='modif' value='Modifier' class='bouton2' /></form></center>";

		?>
	
		<script  language='JavaScript' >
		if (etat != 0) {
			document.write("Sauvegarde en Fonctionnement : <img src='./image/commun/stat1.gif' /><br />");
			document.write("<font class='T1'>OpÃ©rationnel jusqu'au : "+finabonnement+"</font>");
			document.write("&nbsp;&nbsp;&nbsp;<font class=T2>[ <a href='https://support.triade-educ.org/support/crontab/inscr_valider.php?&relance=1&id=<?php print BACKUPKEY?>' target='_blank' ><b>Renouveler</b></a> ]</font>");
		}else{
			document.write("Sauvegarde en Fonctionnement  : <img src='./image/commun/stat2.gif' />");
			document.write(" <font class=T2>[ <a href='https://support.triade-educ.org/support/crontab/inscr_valider.php?&id=<?php print BACKUPKEY?>' target='_blank' ><b>Activer</b></a> ]</font>");
		}

		</script>
		</ul>
		<?php

		print "<br><ul><font class='T2'><b>Liste des sauvegardes :</b></font><br /><br />";
		for($i=0;$i<=$nbSave;$i++) {
			
			if (file_exists("../data/dumpdist/${i}_mysql.inc")) {

				$fichier=fopen("../data/dumpdist/${i}_mysql.inc","r");
				$donnee=fread($fichier,100000);
				fclose($fichier);

				?>
				<img src="./image/on1.gif" align='center' width='8' height='8' />  <font class='T2'><i>Sauvegarde effectuÃ©e le <?php print $donnee ?> </i></font>
				<br /> <br>
				[<a href="recup_mysql_base2.php?id=base&id2=<?php print $i ?>">Base de donnÃ©es</a>] -
				[<a href="recup_mysql_base2.php?id=conf&id2=<?php print $i ?>">Configuration</a>] - 
				[<a href="recup_mysql_base2.php?id=data&id2=<?php print $i ?>">DonnÃ©es</a>]

				<br /><br>
				<?php
			}
		}
		print "</ul>";

	}else{
		if (INTER == "oui") {
			print "<br><ul><font class=T2>Ce service est pris en compte par notre  Ã©quipe.";
			print "<br><br>Nous nous occupons de sauvegarder Triade automatiquement. ";
			print "<br><br>L'Equipe Triade</font></ul>";	
		}else{	
			if (LAN == "oui") {
				print "<iframe src='https://support.triade-educ.org/support/crontab/index.php?graph=".GRAPH."' width=100% height=400 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no ></iframe>";
			}else{
				print "<br><center><font class=T2>RÃ©seau Internet non disponible pour ce module.</font> <br><br> <i>Consulter le module de Configuration pour activer le rÃ©seau.</i></center>";
			}
		}
	}
?>
</td></tr></table>

<!-- // fin de la saisie -->


<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
