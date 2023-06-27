<?php
error_reporting(0);
$patch_ftp=$_GET["patch_ftp"];
$fichier_orig="./patch_ftp/$patch_ftp";
if (!file_exists($fichier_orig)) {  exit; }
if (preg_match('/\.\./i',$patch_ftp)) { exit; }
if (!preg_match('/\.zip$/i',$patch_ftp)) { exit; }

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
 *************************************************************************/
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("../common/lib_admin.php");
include_once("../common/lib_ecole.php");
include_once("librairie_php/db_triade_admin.php");
@unlink("patch.zip");
if (defined("TYPETABLE")) {
	$typetable=TYPETABLE;
}else{
	$typetable="MYISAM";
}
$cnx=cnx();

if (file_exists($fichier_orig)) {
	$fichier=preg_replace('/\.zip/',"",$patch_ftp);
	$rep_patch=preg_replace('/\.zip/',"",$patch_ftp);
	rename($fichier_orig,"./patch.zip");
}
$md5fichier=md5_file("./patch.zip");

include_once('./librairie_php/pclzip.lib.php');
$archive = new PclZip('patch.zip');
	
if ($archive->extract(PCLZIP_OPT_PATH, '../data/patch') == 0) { 
	die( print "erreur"); }

$fichier_info="../data/patch/$fichier/LISEZMOI";
if (file_exists($fichier_info)) {
	$fic=fopen($fichier_info,"r");
	$donneeInfo=fread($fic,900000);
	$donneeInfo=nl2br($donneeInfo);
	fclose($fichier);
}else{
	exit;
}
		
$ok=1;
$fichier_info="../data/patch/$rep_patch/product/rep.triade";
if (file_exists($fichier_info)) {
	$fic=fopen($fichier_info,"r");
	$lines=file ("$fichier_info");
	foreach ($lines as $line_num => $line) {
		$motif=REPADMIN."/";
		$repecole=REPECOLE."/";
		$c1=$repecole.$motif;
		if (preg_match("/$c1/",$line)) { $line=trim(preg_replace('/admin\//',$motif,$line)); }
		if (!is_dir("../".trim($line))) {
			if ( ! mkdir ("../".trim($line),0755)) {
				$ok=0;
        		}
		}
			
	}
	fclose($fic);
}

$fichier_info="../data/patch/$rep_patch/product/info.triade";
if (file_exists($fichier_info)) {
	$fic=fopen($fichier_info,"r");
	$lines=file ("$fichier_info");
	foreach ($lines as $line_num => $line) {
		if(preg_match('/:/',$line)){
			list($tab0,$tab1)= preg_split ("/:/", $line, 2);
			$tab0=trim($tab0);
			$source="../data/patch/$rep_patch/product/".trim($tab0);
			$dest="../".$tab1;
			$motif=REPADMIN."/";
			if (!preg_match('/livreor\/admin\//',$dest)) {
				$dest=trim(preg_replace('/admin\//',$motif,$dest));
			}
			if ( !copy("$source","$dest") ) {
				$ok=0;
			}
		}
		fclose($fic);
	}
}

$fichier_info="../data/patch/$rep_patch/product/md5sum.log";
if (file_exists($fichier_info)) {
	if ($rep_patch == "000-MD5") {
		vider_checksum();
	}
	$fic=fopen($fichier_info,"r");
	$lines=file ("$fichier_info");
	foreach ($lines as $line_num => $line) {
		if(preg_match('/:/',$line)){
			list($md5,$fichier)= preg_split ("/:/", $line, 2);
			updateMd5($md5,$fichier);
		}
	}
	//$md5md5=md5_file("../data/patch/$rep_patch/");
	@unlink("../common/config-md5.php");
        $text2="<?php\n";
        $text2.="define(\"VERSIONMD5\",\"".$md5fichier."\");\n";
	$text2.="?>\n";
       	$fp=fopen("../common/config-md5.php","w");
 	fwrite($fp,"$text2");
        fclose($fp);
}

if (DBTYPE == "mysql") { $fichier_sql="../data/patch/$rep_patch/product/mysql.sql"; }
if (DBTYPE == "pgsql") { $fichier_sql="../data/patch/$rep_patch/product/pgsql.sql"; }
if (file_exists($fichier_sql)) {
	include_once("../librairie_php/lib_prefixe.php");
	$fic=fopen($fichier_sql,"r");
	$lines=file("$fichier_sql");
	$donnee=fread($fic,filesize("$fichier_sql"));
	$tab=explode(";",$donnee);
	foreach($tab as $value){
		if (trim($value) != "") {
			global $prefixe;
			$value=preg_replace('/PREFIXE/',$prefixe,$value);
			$value=preg_replace('/TYPETABLE/',$typetable,$value);
			$ret=updatesql(trim($value).";");
			if ($ret) {
				// rien
			}else{
				$ok=0;
			}
		}
	}
	fclose($fic);
}
 


if ($ok) {
	if (!preg_match('/^000-/',$rep_patch)) {
        	@unlink("../common/lib_patch.php");
        	$text2="<?php\n";
        	$text2.="define(\"VERSIONPATCH\",\"$rep_patch\");\n";
	        $text2.="?>\n";
       	 	$fp=fopen("../common/lib_patch.php","w");
 	      	fwrite($fp,"$text2");
        	fclose($fp);
	       	ajout_patch($rep_patch,addslashes($donneeInfo));
	}
        history_cmd("ADMIN","PATCH","Ajout : $rep_patch");
}else{
        supprimer_patch($rep_patch);
}
clearstatcache();

if ($ok) {
	$fichier_info="../data/patch/$rep_patch/product/alert.triade";
	if (file_exists($fichier_info)) {
		$fic=fopen($fichier_info,"r");
		$lines=file($fichier_info);
		foreach ($lines as $line_num => $line) {
			$info=trim($line);
			$info=preg_replace('/"/',"'",$info);
		}
		fclose($fic);
	}
	recursive_delete("../data/patch/$rep_patch");
}
@unlink("patch.zip");
clearstatcache();
Pgclose($cnx);
?>
