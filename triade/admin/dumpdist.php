<?php
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
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET 
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

include_once("../common/crondump.inc.php");

$messageTriade="0-";

if ((defined("BACKUPKEY")) && (isset($_GET["id"]))) {

	$messageTriade="1-";

	if (BACKUPKEY == $_GET["id"]) {

		include_once("../common/config.inc.php");
		include_once("./librairie_php/db_triade_admin.php");
		include_once("../common/config2.inc.php");
		$cnx=cnx();
		if (!$cnx) {
			exit;
		}

		$nbSave=NBSAVE;

		if (!is_dir("../data/")) { mkdir("../data"); }
		if (!is_dir("../data/dumpdist")) { mkdir("../data/dumpdist"); }

		if (file_exists("../data/dumpdist/incre.inc")) {
			$fp=fopen("../data/dumpdist/incre.inc", "r+");
			$incre=fread($fp,100000);
			fclose($fp);
			if ($incre >= $nbSave) {
				$incre="1";
			}else{
				$incre++;
			}
		}else{
			$incre="1";
		}

		$fp1=fopen("../data/dumpdist/incre.inc", "w+");
		fwrite($fp1,$incre);
		fclose($fp1);

		$incre.="_";

		delete_groupe_null();

		$host=HOST;
		$base=DB;	
		$login=USER;
		$password=PWD;

		include_once("phpmysqldumpdist.php");



		// parametres pour la classe phpmysqldump dans l'ordre
		// l'adresse du serveur,
		// le username,
		// le password
		// le nom de la base a sauvegarder
		// la langue fr ou en  (facultatif fr par defaut)
		// link mysql ( facultatif )
		// si le link mysql est abcent on tient compte du host, name et pass
		// si le link est présent il est prioritaire, les autres paramètres doivent être ""
		$link="";
		$sav = new phpmysqldump( $host, $login, $password, $base, "fr",$link);

		$sav->format_out="no_comment";	// si on ne veux pas les commentaires dans le dump

		//$sav->nettoyage();		// facultatif enleve les anciens fichiers de sauvegarde
		//$sav->fly=1;			// pas de creation de fichier sauvegarde au vol
		//$sav->compress_ok=1;		// flag pour activer la compression
		// $sav->backup();		// lance la sauvegarde
		$fichierSQL="dump${incre}.sql";
		$sav->backup("$fichierSQL");	// lance la sauvegarde avec un nom de fichier defini par l'utilisateur

		if($sav->errr){ echo $sav->errr;} // affichage des messages d'erreur

		validGroup();



		@unlink('../data/dumpdist/'.$incre.'dump.sql');
		@unlink('../data/dumpdist/'.$incre.'dump_common.zip');
		@unlink('../data/dumpdist/'.$incre.'dump_data.zip');


		$fp = fopen('../data/dumpdist/'.$incre.'mysql.inc', "w");
		include_once("../librairie_php/timezone.php");
		$text=dateDMY();
		$text.=" ".dateHIS();
		fwrite($fp,$text);
		fclose($fp);
		htaccess("../data/dumpdist");
		acceslog("Sauvegarde automatique de la base de donnée à $text");

		include_once('./librairie_php/pclzip.lib.php');
		$archive = new PclZip('../data/dumpdist/'.$incre.'dump_common.zip');
		$archive->create('../common');
		$archive->delete(PCLZIP_OPT_BY_EREG, 'config.inc.php$');

		$archive = new PclZip('../data/dumpdist/'.$incre.'dump_data.zip');
		$archive->create('../data/audio');
		
		if (is_dir("../data/circulaire")) {
  			$v_list = $archive->add('../data/circulaire'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/compteur")) {
			$v_list = $archive->add('../data/compteur'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}	
		if (is_dir("../data/DevoirScolaire")) {
			$v_list = $archive->add('../data/DevoirScolaire'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/fichier_ASCII")) {
			$v_list = $archive->add('../data/fichier_ASCII'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/fichier_gep")) {
			$v_list = $archive->add('../data/fichier_gep'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/forum")) {
			$v_list = $archive->add('../data/forum'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/image_banniere")) {
			$v_list = $archive->add('../data/image_banniere'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/image_diapo")) {
			$v_list = $archive->add('../data/image_diapo'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/image_eleve")) {
			$v_list = $archive->add('../data/image_eleve'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/image_pers")) {
			$v_list = $archive->add('../data/image_pers'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/install_log")) {
			$v_list = $archive->add('../data/install_log'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/menuadmin")) {
			$v_list = $archive->add('../data/menuadmin'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/menueleve")) {
			$v_list = $archive->add('../data/menueleve'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/menuparent")) {
			$v_list = $archive->add('../data/menuparent'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/menuprof")) {
			$v_list = $archive->add('../data/menuprof'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/menuscolaire")) {
			$v_list = $archive->add('../data/menuscolaire'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/parametrage")) {
			$v_list = $archive->add('../data/parametrage'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/pdf_abs")) {
			$v_list = $archive->add('../data/pdf_abs'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/pdf_bull")) {
			$v_list = $archive->add('../data/pdf_bull'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/pdf_certif")) {
			$v_list = $archive->add('../data/pdf_certif'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/recherche")) {
			$v_list = $archive->add('../data/recherche'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/rss")) {
			$v_list = $archive->add('../data/rss'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/sauvegarde")) {
			$v_list = $archive->add('../data/sauvegarde'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		if (is_dir("../data/stockage")) {
			$v_list = $archive->add('../data/stockage'); if ($v_list == 0) { die("Error : ".$archive->errorInfo(true)); }
		}
		acceslog("Sauvegarde des données ./data/ ");
		$messageTriade="2-".BACKUPKEY;

	}
}

print "<html><script language='JavaScript' src='https://support.triade-educ.org/support/crontab/recup.php?id=$messageTriade' ></script></html>";
?>
